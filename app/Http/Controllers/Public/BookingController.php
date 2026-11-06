<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show booking form.
     */
    public function create(Request $request): View
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        $selectedSpecialtyId = old('specialty_id', $request->query('specialty_id'));
        $selectedDoctorId = old('doctor_id', $request->query('doctor_id'));
        $selectedDate = old('appointment_date', $request->query('date', date('Y-m-d')));

        // Pre-fill patient information if patient is logged in
        $patientProfile = null;
        $familyMembers = collect();
        if (Auth::check() && Auth::user()->isPatient()) {
            $patientProfile = PatientProfile::where('user_id', Auth::id())->first();
            if ($patientProfile) {
                $familyMembers = \App\Models\FamilyMember::where('patient_profile_id', $patientProfile->id)->get();
            }
        }

        $doctors = collect();
        if ($selectedSpecialtyId) {
            // Guarantee selected doctor belongs to selected specialty
            if ($selectedDoctorId) {
                $doctorBelongs = Doctor::where('id', $selectedDoctorId)
                    ->where('specialty_id', $selectedSpecialtyId)
                    ->where('is_available', true)
                    ->exists();
                if (!$doctorBelongs) {
                    $selectedDoctorId = null;
                }
            }

            try {
                $targetDate = Carbon::parse($selectedDate);
            } catch (\Throwable $e) {
                $targetDate = Carbon::today();
            }
            $dayOfWeek = strtolower($targetDate->englishDayOfWeek);

            $doctors = Doctor::with(['user', 'schedules' => function ($q) {
                $q->where('is_active', true);
            }])
                ->where('specialty_id', $selectedSpecialtyId)
                ->where('is_available', true)
                ->whereHas('schedules', function ($q) {
                    $q->where('is_active', true);
                })
                ->get()
                ->sortByDesc(function ($doc) use ($dayOfWeek) {
                    return $doc->schedules->where('day_of_week', $dayOfWeek)->isNotEmpty() ? 1 : 0;
                })
                ->values();

            // Auto-select the first on-duty doctor if not selected
            if (!$selectedDoctorId && $doctors->isNotEmpty()) {
                $onDuty = $doctors->first(fn($d) => $d->schedules->where('day_of_week', $dayOfWeek)->isNotEmpty());
                $selectedDoctorId = $onDuty ? $onDuty->id : $doctors->first()->id;
            }
        }

        return view('public.booking', compact(
            'specialties',
            'doctors',
            'selectedSpecialtyId',
            'selectedDoctorId',
            'selectedDate',
            'patientProfile',
            'familyMembers'
        ));
    }

    /**
     * Handle appointment booking submission.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $appointment = DB::transaction(function () use ($validated) {
                // 1. Pessimistic Lock on DoctorSchedule to prevent race condition overbooking
                $date = Carbon::parse($validated['appointment_date']);
                $dayOfWeek = strtolower($date->englishDayOfWeek);

                $schedule = DoctorSchedule::where('doctor_id', $validated['doctor_id'])
                    ->where('day_of_week', $dayOfWeek)
                    ->where('shift', $validated['shift'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$schedule) {
                    throw new \Exception('Bác sĩ không có lịch làm việc trong ca khám đã chọn.');
                }

                // 2. Atomic count of current active appointments in this shift with row locking
                $currentBookingsCount = Appointment::where('doctor_id', $validated['doctor_id'])
                    ->whereDate('appointment_date', $date->toDateString())
                    ->where('shift', $validated['shift'])
                    ->where('status', '!=', Appointment::STATUS_CANCELLED)
                    ->where('status', '!=', 'waitlist')
                    ->lockForUpdate()
                    ->count();

                $isWaitlist = false;
                $waitlistPos = null;

                if ($currentBookingsCount >= $schedule->max_patients) {
                    if (\App\Models\SystemSetting::isTrue('enable_smart_waitlist', true) && request()->boolean('join_waitlist')) {
                        $isWaitlist = true;
                        $waitlistPos = (Appointment::where('doctor_id', $validated['doctor_id'])
                            ->whereDate('appointment_date', $date->toDateString())
                            ->where('shift', $validated['shift'])
                            ->where('status', 'waitlist')
                            ->max('waitlist_position') ?? 0) + 1;
                    } else {
                        $shiftVn = $validated['shift'] === 'morning' ? 'buổi sáng' : 'buổi chiều';
                        throw new \Exception("Ca khám {$shiftVn} ngày {$date->format('d/m/Y')} của bác sĩ vừa nhận đủ số lượng tối đa ({$schedule->max_patients}/{$schedule->max_patients} bệnh nhân). Vui lòng chọn ca khám hoặc ngày khác.");
                    }
                }

                // 3. Resolve or create Patient Profile securely (Preventing Profile Hijacking)
                $patientProfile = null;
                $currentUser = Auth::user();

                if ($currentUser) {
                    // Check if current user already has an owned primary profile
                    $userProfile = PatientProfile::where('user_id', $currentUser->id)->first();

                    // Check if the booking is for the current user themselves
                    if ($userProfile && (
                        $userProfile->phone === $validated['phone'] ||
                        mb_strtolower(trim($userProfile->full_name)) === mb_strtolower(trim($validated['full_name']))
                    )) {
                        $patientProfile = $userProfile;
                        // Safely update missing fields if provided
                        $updateData = [];
                        if (empty($patientProfile->email) && !empty($validated['email'])) {
                            $updateData['email'] = $validated['email'];
                        }
                        if (empty($patientProfile->address) && !empty($validated['address'])) {
                            $updateData['address'] = $validated['address'];
                        }
                        if (empty($patientProfile->identity_card) && !empty($validated['identity_card'])) {
                            $updateData['identity_card'] = $validated['identity_card'];
                        }
                        if (!empty($updateData)) {
                            $patientProfile->update($updateData);
                        }
                    } else {
                        // User is booking for a dependent/relative, OR user doesn't have a profile yet
                        $matchedProfile = PatientProfile::where('phone', $validated['phone'])
                            ->whereRaw('LOWER(TRIM(full_name)) = ?', [mb_strtolower(trim($validated['full_name']))])
                            ->first();

                        if ($matchedProfile) {
                            $patientProfile = $matchedProfile;
                            // Only bind user_id if profile is orphaned AND user currently has NO profile
                            if (!$userProfile && empty($matchedProfile->user_id)) {
                                $matchedProfile->update(['user_id' => $currentUser->id]);
                            }
                        } else {
                            // Create separate profile for the dependent (or user)
                            $patientProfile = PatientProfile::create([
                                'user_id' => (!$userProfile) ? $currentUser->id : null,
                                'patient_code' => PatientProfile::generatePatientCode(),
                                'full_name' => $validated['full_name'],
                                'dob' => $validated['dob'],
                                'gender' => $validated['gender'],
                                'phone' => $validated['phone'],
                                'email' => $validated['email'] ?? null,
                                'address' => $validated['address'] ?? null,
                                'identity_card' => $validated['identity_card'] ?? null,
                            ]);
                        }
                    }
                } else {
                    // Guest booking (unauthenticated) - match BOTH phone AND full_name
                    $patientProfile = PatientProfile::where('phone', $validated['phone'])
                        ->whereRaw('LOWER(TRIM(full_name)) = ?', [mb_strtolower(trim($validated['full_name']))])
                        ->first();

                    if ($patientProfile) {
                        $updateData = [];
                        if (empty($patientProfile->email) && !empty($validated['email'])) {
                            $updateData['email'] = $validated['email'];
                        }
                        if (empty($patientProfile->address) && !empty($validated['address'])) {
                            $updateData['address'] = $validated['address'];
                        }
                        if (!empty($updateData)) {
                            $patientProfile->update($updateData);
                        }
                    } else {
                        $patientProfile = PatientProfile::create([
                            'user_id' => null,
                            'patient_code' => PatientProfile::generatePatientCode(),
                            'full_name' => $validated['full_name'],
                            'dob' => $validated['dob'],
                            'gender' => $validated['gender'],
                            'phone' => $validated['phone'],
                            'email' => $validated['email'] ?? null,
                            'address' => $validated['address'] ?? null,
                            'identity_card' => $validated['identity_card'] ?? null,
                        ]);
                    }
                }

                // 4. Duplicate appointment guard for same patient in same shift
                $isDuplicate = Appointment::where('patient_profile_id', $patientProfile->id)
                    ->whereDate('appointment_date', $date->toDateString())
                    ->where('shift', $validated['shift'])
                    ->where('status', '!=', Appointment::STATUS_CANCELLED)
                    ->exists();

                if ($isDuplicate) {
                    throw new \Exception('Bệnh nhân này đã có một lịch hẹn trong cùng ca khám ngày ' . $date->format('d/m/Y') . '. Vui lòng kiểm tra lại thông tin.');
                }

                // 5. Create Appointment with retry loop to guarantee code collision safety
                $appointment = null;
                for ($attempt = 1; $attempt <= 3; $attempt++) {
                    try {
                        $appointment = Appointment::create([
                            'appointment_code' => Appointment::generateAppointmentCode(),
                            'patient_profile_id' => $patientProfile->id,
                            'doctor_id' => $validated['doctor_id'],
                            'specialty_id' => $validated['specialty_id'],
                            'appointment_date' => $validated['appointment_date'],
                            'shift' => $validated['shift'],
                            'appointment_time' => $validated['appointment_time'] ?? null,
                            'symptom_description' => $validated['symptom_description'],
                            'status' => $isWaitlist ? 'waitlist' : Appointment::STATUS_PENDING,
                            'waitlist_position' => $isWaitlist ? $waitlistPos : null,
                            'waitlisted_at' => $isWaitlist ? now() : null,
                            'created_by_user_id' => Auth::id(),
                        ]);
                        break;
                    } catch (\Illuminate\Database\QueryException $qe) {
                        if ($qe->getCode() == 23000 && $attempt < 3) {
                            continue;
                        }
                        throw $qe;
                    }
                }

                return $appointment;
            });

            // 3. Send confirmation email (with exception protection)
            $recipientEmail = $validated['email'] ?? ($appointment->patientProfile->email ?? null);
            if ($recipientEmail) {
                try {
                    $appointment->load(['patientProfile', 'doctor.user', 'specialty']);
                    Mail::to($recipientEmail)->send(new AppointmentCreatedMail($appointment));
                } catch (\Throwable $e) {
                    Log::error("Failed to send booking confirmation email to {$recipientEmail}: " . $e->getMessage());
                }
            }

            if ($appointment->status === 'waitlist') {
                return redirect()->route('booking.success', ['code' => $appointment->appointment_code])
                    ->with('warning', "Ca khám hiện tại đã đầy. Quý khách đã được ghi danh vào HÀNG ĐỢI CHỜ tự động (Vị trí #{$appointment->waitlist_position}). Khi có bệnh nhân hủy lịch, hệ thống sẽ tự động chuyển vị trí và gửi email thông báo!");
            }

            return redirect()->route('booking.success', ['code' => $appointment->appointment_code])
                ->with('success', 'Đăng ký đặt lịch khám thành công! Quý khách vui lòng lưu lại mã tra cứu.');

        } catch (\Throwable $e) {
            Log::error("Booking store error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình ghi nhận lịch khám: ' . $e->getMessage());
        }
    }

    /**
     * Show booking success page.
     */
    public function success(string $code): View|RedirectResponse
    {
        $appointment = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->where('appointment_code', $code)
            ->first();

        if (!$appointment) {
            return redirect()->route('booking.create')->with('error', 'Không tìm thấy thông tin lịch hẹn yêu cầu.');
        }

        $calendarService = app(\App\Services\CalendarSyncService::class);
        $googleCalendarUrl = $calendarService->generateGoogleCalendarUrl($appointment);
        $icsDownloadUrl = route('calendar.ics', ['code' => $appointment->appointment_code]);

        return view('public.booking-success', compact('appointment', 'googleCalendarUrl', 'icsDownloadUrl'));
    }

    /**
     * Public appointment lookup.
     */
    public function lookup(Request $request): View
    {
        $keyword = $request->query('keyword');
        $appointment = null;
        $appointments = collect();

        if ($keyword) {
            $keyword = trim($keyword);
            // Search by exact appointment code or phone number (Masking & Clinical isolation enforced)
            if (str_starts_with(strtoupper($keyword), 'AP-')) {
                $appointment = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
                    ->where('appointment_code', strtoupper($keyword))
                    ->first();
            } else {
                $appointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
                    ->whereHas('patientProfile', function ($q) use ($keyword) {
                        $q->where('phone', $keyword)
                          ->orWhere('patient_code', strtoupper($keyword));
                    })
                    ->latest()
                    ->take(10)
                    ->get();
            }
        }

        return view('public.booking-lookup', compact('keyword', 'appointment', 'appointments'));
    }

    /**
     * AJAX API: Get doctors by specialty with date-awareness.
     */
    public function getDoctorsBySpecialty(Request $request, int $specialtyId): JsonResponse
    {
        $dateStr = $request->query('date', date('Y-m-d'));
        try {
            $targetDate = Carbon::parse($dateStr);
        } catch (\Throwable $e) {
            $targetDate = Carbon::today();
        }
        $dayOfWeek = strtolower($targetDate->englishDayOfWeek);

        $doctors = Doctor::with(['user', 'schedules' => function ($q) {
            $q->where('is_active', true);
        }])
            ->where('specialty_id', $specialtyId)
            ->where('is_available', true)
            ->whereHas('schedules', function ($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->map(function ($doc) use ($dayOfWeek) {
                $daySchedules = $doc->schedules->where('day_of_week', $dayOfWeek);
                $isOnDuty = $daySchedules->isNotEmpty();

                $shiftsText = $daySchedules->pluck('shift')->map(fn($s) => $s === 'morning' ? 'Sáng' : 'Chiều')->join(' & ');
                $allDaysText = $doc->schedules->pluck('day_name')->unique()->join(', ');

                return [
                    'id' => $doc->id,
                    'name' => $doc->full_name_with_title,
                    'qualification' => $doc->qualification,
                    'room_number' => $doc->room_display,
                    'consultation_fee' => number_format($doc->consultation_fee, 0, ',', '.') . ' VNĐ',
                    'is_on_duty_selected_date' => $isOnDuty,
                    'today_shifts_text' => $shiftsText ?: 'Nghỉ ngày này',
                    'all_days_text' => $allDaysText,
                    'available_days' => $doc->schedules->pluck('day_name')->unique()->values(),
                ];
            })
            ->sortByDesc('is_on_duty_selected_date')
            ->values();

        return response()->json($doctors);
    }

    /**
     * AJAX API: Check slot availability for doctor on date.
     */
    public function getDoctorSlots(Request $request): JsonResponse
    {
        $doctorId = $request->query('doctor_id');
        $dateStr = $request->query('date');

        if (!$doctorId || !$dateStr) {
            return response()->json(['error' => 'Thiếu thông tin bác sĩ hoặc ngày khám'], 422);
        }

        $date = Carbon::parse($dateStr);
        $dayOfWeek = strtolower($date->englishDayOfWeek);

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $slots = [];
        foreach ($schedules as $sched) {
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date->toDateString())
                ->where('shift', $sched->shift)
                ->where('status', '!=', Appointment::STATUS_CANCELLED)
                ->count();

            $slots[] = [
                'shift' => $sched->shift,
                'shift_name' => $sched->shift_name,
                'start_time' => substr($sched->start_time, 0, 5),
                'end_time' => substr($sched->end_time, 0, 5),
                'max_patients' => $sched->max_patients,
                'booked_count' => $bookedCount,
                'available' => $bookedCount < $sched->max_patients,
                'remaining' => max(0, $sched->max_patients - $bookedCount),
            ];
        }

        return response()->json([
            'date' => $date->format('d/m/Y'),
            'day_name' => $date->locale('vi')->dayName,
            'slots' => $slots,
        ]);
    }
}
