<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\SickLeaveCertificate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SickLeaveCertificateController extends Controller
{
    /**
     * List all issued sick leave certificates (Mẫu C65-HD).
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $date = $request->input('date');

        $query = SickLeaveCertificate::with(['patientProfile', 'doctor.user', 'issuedBy', 'medicalRecord']);

        if (!empty($date)) {
            $query->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_code', 'like', "%{$search}%")
                    ->orWhere('diagnosis', 'like', "%{$search}%")
                    ->orWhere('employer_name', 'like', "%{$search}%")
                    ->orWhereHas('patientProfile', function ($pq) use ($search) {
                        $pq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%")
                            ->orWhere('insurance_code', 'like', "%{$search}%")
                            ->orWhere('identity_card', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $query->latest()->paginate(15)->withQueryString();

        return view('receptionist.certificates.index', compact('certificates', 'search', 'date'));
    }

    /**
     * Show form to issue a new C65-HD certificate for a medical record.
     */
    public function create(MedicalRecord $record): View
    {
        $record->load(['patientProfile', 'doctor.user', 'appointment.specialty', 'sickLeaveCertificate']);

        $existing = $record->sickLeaveCertificate;
        if ($existing) {
            return view('receptionist.certificates.edit', compact('record', 'existing'));
        }

        return view('receptionist.certificates.create', compact('record'));
    }

    /**
     * Store or update sick leave certificate.
     */
    public function store(Request $request, MedicalRecord $record): RedirectResponse
    {
        $validated = $request->validate([
            'employer_name' => ['nullable', 'string', 'max:255'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'treatment_method' => ['required', 'string', 'max:150'],
            'leave_days' => ['required', 'integer', 'min:1', 'max:30'],
            'from_date' => ['required', 'date'],
            'father_mother_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'diagnosis.required' => 'Vui lòng nhập chẩn đoán tình trạng bệnh.',
            'treatment_method.required' => 'Vui lòng chọn hoặc nhập phương pháp điều trị.',
            'leave_days.required' => 'Vui lòng nhập số ngày nghỉ hưởng BHXH.',
            'leave_days.min' => 'Số ngày nghỉ tối thiểu là 1 ngày.',
            'leave_days.max' => 'Theo quy định Bộ Y Tế, mỗi lần khám ngoại trú cấp tối đa 30 ngày.',
            'from_date.required' => 'Vui lòng chọn ngày bắt đầu nghỉ việc.',
        ]);

        $fromDate = Carbon::parse($validated['from_date']);
        $toDate = (clone $fromDate)->addDays((int)$validated['leave_days'] - 1);

        $certificate = SickLeaveCertificate::updateOrCreate(
            ['medical_record_id' => $record->id],
            [
                'certificate_code' => $record->sickLeaveCertificate?->certificate_code ?? SickLeaveCertificate::generateCertificateCode(),
                'patient_profile_id' => $record->patient_profile_id,
                'doctor_id' => $record->doctor_id,
                'issued_by_user_id' => Auth::id(),
                'employer_name' => $validated['employer_name'] ?? null,
                'diagnosis' => $validated['diagnosis'],
                'treatment_method' => $validated['treatment_method'],
                'leave_days' => (int) $validated['leave_days'],
                'from_date' => $fromDate->toDateString(),
                'to_date' => $toDate->toDateString(),
                'father_mother_name' => $validated['father_mother_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()
            ->route('reception.certificates.print', $certificate)
            ->with('success', "Đã cấp Giấy chứng nhận nghỉ việc hưởng BHXH (Mã: {$certificate->certificate_code}) cho bệnh nhân {$record->patientProfile->full_name}.");
    }

    /**
     * Print official MOH standard C65-HD certificate.
     */
    public function print(SickLeaveCertificate $certificate): View
    {
        $user = Auth::user();
        if ($user && $user->isPatient()) {
            $profile = \App\Models\PatientProfile::where('user_id', $user->id)->first();
            if (!$profile || $certificate->patient_profile_id !== $profile->id) {
                abort(403, 'Bạn không có quyền xem hoặc in giấy chứng nhận này.');
            }
        }

        $certificate->load([
            'medicalRecord.appointment.specialty',
            'patientProfile',
            'doctor.user',
            'issuedBy',
        ]);

        return view('receptionist.certificates.print', compact('certificate'));
    }

    /**
     * Delete certificate.
     */
    public function destroy(SickLeaveCertificate $certificate): RedirectResponse
    {
        $certificate->delete();

        return back()->with('success', 'Đã hủy giấy chứng nhận nghỉ việc hưởng BHXH.');
    }
}
