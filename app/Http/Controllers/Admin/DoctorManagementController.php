<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\DoctorAuditLog;
use App\Models\DoctorLeave;
use App\Models\DoctorReview;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoctorManagementController extends Controller
{
    /**
     * Display a listing of the doctors.
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = Doctor::query()->with(['user', 'specialty', 'schedules', 'leaves.substituteDoctor.user']);

        if ($search = trim($request->input('search', ''))) {
            $numOnly = preg_replace('/[^0-9]/', '', $search);
            $slugTerm = \Illuminate\Support\Str::slug($search);
            $withD = str_replace(['d', 'D'], ['đ', 'Đ'], $search);
            $withoutD = str_replace(['đ', 'Đ'], ['d', 'D'], $search);
            $terms = array_unique(array_filter([$search, $withD, $withoutD, $slugTerm]));

            $query->where(function ($q) use ($terms, $numOnly) {
                foreach ($terms as $term) {
                    $q->orWhere('qualification', 'LIKE', "%{$term}%")
                      ->orWhere('room_number', 'LIKE', "%{$term}%")
                      ->orWhere('building', 'LIKE', "%{$term}%")
                      ->orWhere('floor', 'LIKE', "%{$term}%")
                      ->orWhereHas('specialty', function ($sq) use ($term) {
                          $sq->where('name', 'LIKE', "%{$term}%")
                             ->orWhere('slug', 'LIKE', "%{$term}%")
                             ->orWhere('description', 'LIKE', "%{$term}%");
                      })
                      ->orWhereHas('user', function ($uq) use ($term) {
                          $uq->where('name', 'LIKE', "%{$term}%")
                             ->orWhere('email', 'LIKE', "%{$term}%")
                             ->orWhere('phone', 'LIKE', "%{$term}%");
                      });
                }

                if (!empty($numOnly)) {
                    $q->orWhere('room_number', 'LIKE', "%{$numOnly}%");
                }
            });
        }

        if ($specialtyId = $request->input('specialty_id')) {
            $query->where('specialty_id', $specialtyId);
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->input('is_available') === '1');
        }

        $doctors = $query->latest()->paginate(10)->withQueryString();

        if ($request->wantsJson() || $request->input('format') === 'json') {
            return response()->json([
                'data' => $doctors->map(function ($doc) {
                    $currentLeave = $doc->current_leave;
                    return [
                        'id' => $doc->id,
                        'name' => $doc->full_name_with_title,
                        'email' => $doc->user ? $doc->user->email : '',
                        'specialty' => $doc->specialty ? $doc->specialty->name : 'Chưa phân khoa',
                        'room' => $doc->room_number,
                        'fee' => number_format($doc->consultation_fee, 0, ',', '.') . ' đ',
                        'avatar' => $doc->avatar_url,
                        'is_available' => (bool)$doc->is_available,
                        'is_on_leave' => (bool)$currentLeave,
                        'leave_reason' => $currentLeave ? $currentLeave->reason : null,
                        'substitute_name' => $currentLeave && $currentLeave->substituteDoctor ? $currentLeave->substituteDoctor->clean_name : null,
                        'url' => route('admin.doctors.show', $doc),
                    ];
                }),
                'total' => $doctors->total(),
            ]);
        }
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => Doctor::count(),
            'available' => Doctor::where('is_available', true)->count(),
            'unavailable' => Doctor::where('is_available', false)->count(),
            'rooms_count' => Doctor::distinct('room_number')->count('room_number'),
        ];

        return view('admin.doctors.index', compact('doctors', 'specialties', 'stats'));
    }

    /**
     * Show the form for creating a new doctor.
     */
    public function create(): View
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        // Get doctor users who don't have a doctor profile yet
        $availableUsers = User::where('role', User::ROLE_DOCTOR)
            ->whereDoesntHave('doctor')
            ->orderBy('name')
            ->get();

        return view('admin.doctors.create', compact('specialties', 'availableUsers'));
    }

    /**
     * Store a newly created doctor in storage.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, &$doctor) {
            if ($request->filled('user_id')) {
                $userId = $data['user_id'];
            } else {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role' => User::ROLE_DOCTOR,
                ]);
                $userId = $user->id;
            }

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('doctors', 'public');
            }

            $doctor = Doctor::create([
                'user_id' => $userId,
                'specialty_id' => $data['specialty_id'],
                'qualification' => $data['qualification'],
                'consultation_fee' => $data['consultation_fee'],
                'room_number' => $data['room_number'],
                'avatar' => $avatarPath,
                'bio' => $data['bio'] ?? null,
                'is_available' => $request->has('is_available'),
                'cchn_number' => $data['cchn_number'] ?? null,
                'cchn_issuer' => $data['cchn_issuer'] ?? 'Bộ Y Tế',
                'cchn_issued_date' => $data['cchn_issued_date'] ?? null,
                'cchn_expiry_date' => $data['cchn_expiry_date'] ?? null,
                'cme_hours' => $data['cme_hours'] ?? 0,
            ]);

            // Automatically initialize weekly schedules for the new doctor (Monday - Sunday, 2 shifts)
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'shift' => 'morning',
                    'start_time' => '07:30:00',
                    'end_time' => '11:30:00',
                    'max_patients' => 15,
                    'is_active' => true,
                ]);

                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'shift' => 'afternoon',
                    'start_time' => '13:30:00',
                    'end_time' => '17:00:00',
                    'max_patients' => 15,
                    'is_active' => in_array($day, ['saturday', 'sunday']) ? false : true,
                ]);
            }
        });

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Thêm bác sĩ mới và khởi tạo lịch trực tuần thành công!');
    }

    /**
     * Display the specified doctor.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules', 'appointments' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor.
     */
    public function edit(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty']);
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    /**
     * Update the specified doctor in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $doctor) {
            // Update associated User details
            if ($doctor->user) {
                $doctor->user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                ]);
            }

            $updateFields = [
                'specialty_id' => $data['specialty_id'],
                'qualification' => $data['qualification'],
                'consultation_fee' => $data['consultation_fee'],
                'room_number' => $data['room_number'],
                'bio' => $data['bio'] ?? null,
                'is_available' => $request->has('is_available'),
                'cchn_number' => $data['cchn_number'] ?? null,
                'cchn_issuer' => $data['cchn_issuer'] ?? ($doctor->cchn_issuer ?? 'Bộ Y Tế'),
                'cchn_issued_date' => $data['cchn_issued_date'] ?? null,
                'cchn_expiry_date' => $data['cchn_expiry_date'] ?? null,
                'cme_hours' => $data['cme_hours'] ?? ($doctor->cme_hours ?? 0),
            ];

            if ($request->hasFile('avatar')) {
                if ($doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
                    Storage::disk('public')->delete($doctor->avatar);
                }
                $updateFields['avatar'] = $request->file('avatar')->store('doctors', 'public');
            }

            // Update Doctor profile
            $doctor->update($updateFields);
        });

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Cập nhật thông tin bác sĩ thành công!');
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->appointments()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return redirect()->route('admin.doctors.index')
                ->with('error', 'Không thể xóa bác sĩ này vì đang có lịch hẹn khám sắp tới hoặc đang chờ xử lý.');
        }

        if ($doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
            Storage::disk('public')->delete($doctor->avatar);
        }

        $doctor->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Đã xóa bác sĩ khỏi hệ thống thành công!');
    }

    /**
     * View weekly schedule configuration for the doctor.
     */
    public function schedule(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules']);

        $schedules = $doctor->schedules->keyBy(function ($item) {
            return $item->day_of_week . '_' . $item->shift;
        });

        $days = [
            'monday' => 'Thứ Hai',
            'tuesday' => 'Thứ Ba',
            'wednesday' => 'Thứ Tư',
            'thursday' => 'Thứ Năm',
            'friday' => 'Thứ Sáu',
            'saturday' => 'Thứ Bảy',
            'sunday' => 'Chủ Nhật',
        ];

        return view('admin.doctors.schedule', compact('doctor', 'schedules', 'days'));
    }

    /**
     * Update weekly schedule configuration.
     */
    public function updateSchedule(Request $request, Doctor $doctor): RedirectResponse
    {
        $schedulesInput = $request->input('schedules', []);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $shifts = ['morning', 'afternoon'];

        DB::transaction(function () use ($doctor, $schedulesInput, $days, $shifts) {
            foreach ($days as $day) {
                foreach ($shifts as $shift) {
                    $key = "{$day}_{$shift}";
                    $data = $schedulesInput[$key] ?? [];

                    $isActive = isset($data['is_active']);
                    $startTime = !empty($data['start_time']) ? $data['start_time'] : ($shift === 'morning' ? '07:30:00' : '13:30:00');
                    $endTime = !empty($data['end_time']) ? $data['end_time'] : ($shift === 'morning' ? '11:30:00' : '17:00:00');
                    $maxPatients = !empty($data['max_patients']) ? intval($data['max_patients']) : 15;

                    DoctorSchedule::updateOrCreate([
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'shift' => $shift,
                    ], [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'max_patients' => $maxPatients,
                        'is_active' => $isActive,
                    ]);
                }
            }
        });

        return redirect()->route('admin.doctors.schedule', $doctor)
            ->with('success', 'Cập nhật lịch trực tuần của bác sĩ thành công!');
    }

    /**
     * Toggle available status for the doctor.
     */
    public function toggleAvailability(Doctor $doctor): RedirectResponse
    {
        $doctor->is_available = !$doctor->is_available;
        $doctor->save();

        $statusText = $doctor->is_available ? 'sẵn sàng khám' : 'tạm dừng nhận khám';
        return back()->with('success', "Đã chuyển trạng thái bác sĩ sang: {$statusText}!");
    }

    /**
     * View and manage leave / vacation requests for the doctor.
     */
    public function leaves(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'leaves.substituteDoctor.user']);

        // Doctors in same specialty excluding current doctor
        $compatibleDoctors = Doctor::with('user')
            ->where('specialty_id', $doctor->specialty_id)
            ->where('id', '!=', $doctor->id)
            ->where('is_available', true)
            ->get();

        $leaves = $doctor->leaves()->with('substituteDoctor.user')->latest('start_date')->get();

        return view('admin.doctors.leaves', compact('doctor', 'leaves', 'compatibleDoctors'));
    }

    /**
     * Store a new doctor leave request with optional substitute doctor.
     */
    public function storeLeave(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'substitute_doctor_id' => 'nullable|exists:doctors,id|different:doctor_id',
            'status' => 'required|in:approved,pending,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $doctor->leaves()->create($validated);

        return redirect()->route('admin.doctors.leaves', $doctor)
            ->with('success', 'Đã ghi nhận lịch nghỉ phép / công tác cho bác sĩ thành công!');
    }

    /**
     * Delete or cancel a doctor leave request.
     */
    public function destroyLeave(DoctorLeave $leave): RedirectResponse
    {
        $doctorId = $leave->doctor_id;
        $leave->delete();

        return redirect()->route('admin.doctors.leaves', $doctorId)
            ->with('success', 'Đã hủy lịch nghỉ phép thành công!');
    }

    /**
     * ADVANCED 1: Bảng đánh giá KPI & Năng suất Khám Bác sĩ.
     */
    public function kpi(): View
    {
        $doctors = Doctor::with(['user', 'specialty', 'appointments', 'schedules'])->get()->map(function ($doc) {
            $completedApps = $doc->appointments->where('status', 'completed')->count();
            $totalApps = $doc->appointments->count();
            $revenue = $completedApps * (float)$doc->consultation_fee;
            $totalShiftSlots = $doc->schedules->sum('max_patients');
            $occupancyRate = $totalShiftSlots > 0 ? round(($totalApps / $totalShiftSlots) * 100, 1) : 0;
            
            $tier = match (true) {
                $completedApps >= 5 || (float)$doc->rating_avg >= 4.9 => 'Hạng A+ (Chuyên gia Xuất sắc)',
                $completedApps >= 2 || (float)$doc->rating_avg >= 4.8 => 'Hạng A (Bác sĩ Ưu tú)',
                default => 'Hạng B (Bác sĩ Tiêu chuẩn)',
            };

            return (object)[
                'id' => $doc->id,
                'name' => $doc->full_name_with_title,
                'specialty' => $doc->specialty?->name ?? '—',
                'fee' => (float)$doc->consultation_fee,
                'completed_count' => $completedApps,
                'revenue' => $revenue,
                'rating_avg' => (float)$doc->rating_avg,
                'rating_count' => (int)$doc->rating_count,
                'occupancy_rate' => $occupancyRate,
                'tier' => $tier,
            ];
        })->sortByDesc('revenue');

        $totalRevenue = $doctors->sum('revenue');
        $totalCompleted = $doctors->sum('completed_count');
        $avgRating = round($doctors->avg('rating_avg'), 2);

        return view('admin.doctors.kpi', compact('doctors', 'totalRevenue', 'totalCompleted', 'avgRating'));
    }

    /**
     * ADVANCED 2: Xuất Danh bạ Bác sĩ ra file Excel (.xls) có định dạng bảng chuẩn, màu sắc và viền ô.
     */
    public function exportExcel(): \Illuminate\Http\Response
    {
        $doctors = Doctor::with(['user', 'specialty'])->get();

        $html = view('admin.doctors.exports.excel_table', compact('doctors'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="danh_ba_bac_si_tam_anh_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * ADVANCED 2: Xuất Danh bạ Bác sĩ ra file CSV chuẩn UTF-8 BOM & sep=,.
     */
    public function exportCsv(): StreamedResponse
    {
        $doctors = Doctor::with(['user', 'specialty'])->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="danh_ba_bac_si_tam_anh_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($doctors) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM and sep=, so Excel automatically splits columns without region issues
            fputs($handle, "\xEF\xBB\xBF");
            fputs($handle, "sep=,\r\n");

            // CSV Header
            fputcsv($handle, [
                'Mã Bác Sĩ',
                'Họ và Tên',
                'Học Vị',
                'Email Công Vụ',
                'Số Điện Thoại',
                'Số CCHN',
                'Ngày Cấp CCHN',
                'Hạn CCHN',
                'Chuyên Khoa',
                'Phòng Khám',
                'Tòa Nhà',
                'Tầng',
                'Giá Khám (VNĐ)',
                'Trạng Thái Khám',
                'Điểm Đánh Giá Sao',
            ]);

            foreach ($doctors as $d) {
                fputcsv($handle, [
                    $d->id,
                    $d->user?->name,
                    $d->qualification,
                    $d->user?->email,
                    $d->user?->phone,
                    $d->cchn_number ?: 'Chưa cập nhật',
                    $d->cchn_issued_date ? $d->cchn_issued_date->format('d/m/Y') : '',
                    $d->cchn_expiry_date ? $d->cchn_expiry_date->format('d/m/Y') : '',
                    $d->specialty?->name,
                    $d->room_number,
                    $d->building,
                    $d->floor,
                    $d->consultation_fee,
                    $d->is_available ? 'Sẵn sàng nhận khám' : 'Tạm dừng',
                    $d->rating_avg,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * ADVANCED 2 (Template): Tải file mẫu CSV để import (kèm sep=,).
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_nhap_lieu_bac_si.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputs($handle, "sep=,\r\n");
            fputcsv($handle, ['Họ và Tên', 'Email', 'Mật Khẩu', 'Mã Chuyên Khoa', 'Học Vị', 'Phòng Khám', 'Giá Khám']);
            fputcsv($handle, ['Bác sĩ Mẫu A', 'bs.maua@tamanh.vn', '123456', '1', 'TS.BS', 'Phòng 101 - Tòa A', '400000']);
            fputcsv($handle, ['Bác sĩ Mẫu B', 'bs.maub@tamanh.vn', '123456', '2', 'ThS.BS', 'Phòng 202 - Tòa B', '350000']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * ADVANCED 2 (Template Excel): Tải file mẫu Excel (.xls) có bảng kẻ ô.
     */
    public function downloadTemplateExcel(): \Illuminate\Http\Response
    {
        $html = view('admin.doctors.exports.template_table')->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_nhap_lieu_bac_si.xls"',
        ]);
    }

    /**
     * ADVANCED 2 (Import): Nhập danh bạ bác sĩ hàng loạt từ file CSV hoặc Excel (.xls).
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ]);

        $file = $request->file('csv_file');
        $content = file_get_contents($file->getRealPath());
        
        $importedCount = 0;
        $rows = [];

        // Check if file is HTML/Excel table (.xls)
        if (str_contains($content, '<table') || str_contains($content, '<tr')) {
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $content, $trMatches);
            foreach ($trMatches[1] as $trIndex => $trHtml) {
                if (str_contains($trHtml, '<th') || $trIndex < 2) continue;
                preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $trHtml, $tdMatches);
                $cols = array_map(function ($val) {
                    return trim(strip_tags(html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                }, $tdMatches[1]);

                if (count($cols) >= 7 && !empty($cols[1])) {
                    $rows[] = $cols;
                }
            }
        } else {
            // Standard CSV or TXT
            if (str_starts_with($content, "\xEF\xBB\xBF")) {
                $content = substr($content, 3);
            }
            if (preg_match('/^sep=[,;\t]\r?\n/', $content, $sepMatch)) {
                $content = substr($content, strlen($sepMatch[0]));
            }

            $lines = explode("\n", str_replace("\r", "", $content));
            if (empty($lines)) {
                return back()->with('error', 'File dữ liệu rỗng!');
            }

            $firstLine = $lines[0];
            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }

            $isFirst = true;
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $cols = str_getcsv($line, $delimiter);
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }
                if (count($cols) >= 7 && !empty(trim($cols[1]))) {
                    $rows[] = $cols;
                }
            }
        }

        foreach ($rows as $row) {
            $name = trim($row[0]);
            $email = trim($row[1]);
            $password = trim($row[2]) ?: '123456';
            $specialtyId = (int)trim($row[3]);
            $qualification = trim($row[4]) ?: 'BS';
            $roomNumber = trim($row[5]) ?: 'Phòng Khám';
            $fee = (float)str_replace(['.', ',', ' '], '', trim($row[6])) ?: 300000;

            if (!Specialty::where('id', $specialtyId)->exists()) continue;

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => User::ROLE_DOCTOR,
                ]
            );

            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $specialtyId,
                    'qualification' => $qualification,
                    'consultation_fee' => $fee,
                    'room_number' => $roomNumber,
                    'is_available' => true,
                ]
            );

            $importedCount++;
        }

        DoctorAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'import_doctors_csv',
            'description' => "Đã import thành công {$importedCount} bác sĩ từ file dữ liệu",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('success', "Đã import thành công {$importedCount} hồ sơ bác sĩ vào hệ thống!");
    }

    /**
     * ADVANCED 3: Quản lý Chứng Chỉ Hành Nghề (CCHN) & Cảnh báo hết hạn.
     */
    public function licenses(): View
    {
        $doctors = Doctor::with(['user', 'specialty'])->get();

        $expiringSoon = $doctors->filter(fn($d) => $d->is_cchn_expiring_soon);
        $expired = $doctors->filter(fn($d) => $d->is_cchn_expired);
        $validCount = $doctors->count() - $expiringSoon->count() - $expired->count();

        return view('admin.doctors.licenses', compact('doctors', 'expiringSoon', 'expired', 'validCount'));
    }

    /**
     * ADVANCED 3 (Action): Cập nhật & Gia hạn Giấy phép CCHN / Tín chỉ CME.
     */
    public function updateLicense(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'cchn_number' => 'required|string|max:50',
            'cchn_issuer' => 'required|string|max:100',
            'cchn_issued_date' => 'required|date',
            'cchn_expiry_date' => 'required|date|after_or_equal:cchn_issued_date',
            'cme_hours' => 'nullable|integer|min:0|max:500',
        ]);

        $oldValues = [
            'cchn_number' => $doctor->cchn_number,
            'cchn_issuer' => $doctor->cchn_issuer,
            'cchn_expiry_date' => $doctor->cchn_expiry_date ? $doctor->cchn_expiry_date->format('Y-m-d') : null,
        ];

        $doctor->update([
            'cchn_number' => $validated['cchn_number'],
            'cchn_issuer' => $validated['cchn_issuer'],
            'cchn_issued_date' => $validated['cchn_issued_date'],
            'cchn_expiry_date' => $validated['cchn_expiry_date'],
        ]);

        DoctorAuditLog::create([
            'user_id' => auth()->id(),
            'doctor_id' => $doctor->id,
            'action' => 'renew_cchn_license',
            'description' => "Gia hạn / Cập nhật chứng chỉ hành nghề CCHN cho Bác sĩ {$doctor->full_name_with_title}",
            'old_values' => $oldValues,
            'new_values' => [
                'cchn_number' => $validated['cchn_number'],
                'cchn_issuer' => $validated['cchn_issuer'],
                'cchn_expiry_date' => $validated['cchn_expiry_date'],
                'cme_hours' => $request->input('cme_hours', 48),
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.doctors.licenses')
            ->with('success', "Đã gia hạn chứng chỉ CCHN cho Bác sĩ {$doctor->full_name_with_title} thành công đến ngày " . date('d/m/Y', strtotime($validated['cchn_expiry_date'])) . "!");
    }

    /**
     * ADVANCED 4: Ma trận Bản đồ Nhiệt Tải khám & Cân bằng Tải Bác sĩ.
     */
    public function workloadBalancer(): View
    {
        $specialties = Specialty::with(['doctors.user', 'doctors.appointments', 'doctors.schedules'])
            ->where('is_active', true)
            ->get();

        return view('admin.doctors.workload_balancer', compact('specialties'));
    }

    /**
     * ADVANCED 5: Quản lý Đánh giá & Bình luận Bác sĩ.
     */
    public function reviews(): View
    {
        $reviews = DoctorReview::with(['doctor.user', 'doctor.specialty'])->latest()->paginate(15);
        $avgScore = round(DoctorReview::where('is_approved', true)->avg('rating'), 2);
        $totalReviews = DoctorReview::count();

        return view('admin.doctors.reviews', compact('reviews', 'avgScore', 'totalReviews'));
    }

    /**
     * ADVANCED 5 (Action): Bật / Tắt duyệt hiển thị đánh giá.
     */
    public function toggleReviewApproval(DoctorReview $review): RedirectResponse
    {
        $review->is_approved = !$review->is_approved;
        $review->save();

        // Recalculate doctor rating_avg
        $doc = $review->doctor;
        $approvedReviews = $doc->reviews()->where('is_approved', true);
        if ($approvedReviews->count() > 0) {
            $doc->rating_avg = round($approvedReviews->avg('rating'), 2);
            $doc->rating_count = $approvedReviews->count();
        } else {
            $doc->rating_avg = 5.00;
            $doc->rating_count = 0;
        }
        $doc->save();

        $state = $review->is_approved ? 'hiển thị' : 'ẩn';
        return back()->with('success', "Đã {$state} đánh giá của bệnh nhân {$review->patient_name} thành công!");
    }

    /**
     * ADVANCED 9: Nhật ký Kiểm toán Thay đổi Lịch trực & Hồ sơ Bác sĩ.
     */
    public function auditLogs(): View
    {
        $logs = DoctorAuditLog::with(['user', 'doctor.user'])->latest()->paginate(20);
        return view('admin.doctors.audit_logs', compact('logs'));
    }
}
