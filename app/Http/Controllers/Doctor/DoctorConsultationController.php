<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Attachment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DoctorConsultationController extends Controller
{
    /**
     * Common medications catalogue for fast autocomplete.
     */
    public static array $commonMedications = [
        ['name' => 'Paracetamol 500mg', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 2 - 3 lần', 'duration' => 5, 'instructions' => 'Uống sau ăn khi sốt trên 38.5°C hoặc đau'],
        ['name' => 'Augmentin 1g (Amoxicillin + Clavulanic)', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 2 lần (cách 12h)', 'duration' => 7, 'instructions' => 'Uống ngay đầu bữa ăn, uống đủ liều 7 ngày'],
        ['name' => 'Panadol Extra Đỏ', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 2 lần', 'duration' => 3, 'instructions' => 'Uống sau ăn khi đau đầu nhiều'],
        ['name' => 'Omeprazol 20mg', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 1 lần vào buổi sáng', 'duration' => 14, 'instructions' => 'Uống trước bữa ăn sáng 30 phút'],
        ['name' => 'Amlodipin 5mg', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 1 lần cố định', 'duration' => 30, 'instructions' => 'Uống cố định vào 8h sáng hàng ngày'],
        ['name' => 'Cetirizin 10mg', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 1 lần vào buổi tối', 'duration' => 5, 'instructions' => 'Uống trước khi đi ngủ, có thể gây buồn ngủ nhẹ'],
        ['name' => 'Alpha Choay (Chymotrypsin)', 'dosage' => '2 viên/lần', 'frequency' => 'Ngày 3 lần ngậm dưới lưỡi', 'duration' => 5, 'instructions' => 'Ngậm dưới lưỡi cho tan từ từ để giảm phù nề'],
        ['name' => 'Oresol 245', 'dosage' => '1 gói/lần', 'frequency' => 'Uống rải rác trong ngày', 'duration' => 3, 'instructions' => 'Pha đúng 200ml nước đun sôi để nguội, uống thay nước'],
        ['name' => 'Vitamin C 500mg', 'dosage' => '1 viên/lần', 'frequency' => 'Ngày 1 lần sau ăn sáng', 'duration' => 10, 'instructions' => 'Uống sau bữa ăn sáng với nhiều nước'],
    ];

    /**
     * Open consultation examination room for an appointment.
     */
    public function examine(Appointment $appointment): View|RedirectResponse
    {
        if ($appointment->status === Appointment::STATUS_CANCELLED) {
            return redirect()->route('doctor.queue')->with('error', 'Lịch khám này đã bị hủy.');
        }

        $user = Auth::user();
        $doctor = Doctor::with('specialty')->where('user_id', $user->id)->first();

        $appointment->load(['patientProfile', 'doctor.user', 'specialty', 'medicalRecord.prescriptions', 'medicalRecord.attachments']);

        // Historical records of this patient
        $pastRecords = MedicalRecord::with(['doctor.user', 'prescriptions', 'attachments'])
            ->where('patient_profile_id', $appointment->patient_profile_id)
            ->where('id', '!=', $appointment->medicalRecord?->id ?? 0)
            ->latest()
            ->limit(5)
            ->get();

        $commonMeds = self::$commonMedications;

        return view('doctor.examine', compact('appointment', 'doctor', 'pastRecords', 'commonMeds'));
    }

    /**
     * Save medical record, prescriptions, attachments, and complete appointment.
     */
    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'symptoms' => ['required', 'string'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'clinical_notes' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'follow_up_date' => ['nullable', 'date'],
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.medication_name' => ['required_with:prescriptions', 'string', 'max:150'],
            'prescriptions.*.dosage' => ['required_with:prescriptions', 'string', 'max:100'],
            'prescriptions.*.frequency' => ['required_with:prescriptions', 'string', 'max:100'],
            'prescriptions.*.duration_days' => ['required_with:prescriptions', 'integer', 'min:1', 'max:90'],
            'prescriptions.*.instructions' => ['nullable', 'string', 'max:255'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:10240'],
            'attachment_types' => ['nullable', 'array'],
        ], [
            'symptoms.required' => 'Vui lòng ghi nhận triệu chứng lâm sàng.',
            'diagnosis.required' => 'Vui lòng nhập chẩn đoán kết luận của bác sĩ.',
            'prescriptions.*.medication_name.required_with' => 'Vui lòng nhập tên thuốc.',
            'prescriptions.*.dosage.required_with' => 'Vui lòng nhập liều dùng.',
            'prescriptions.*.frequency.required_with' => 'Vui lòng nhập tần suất uống.',
            'prescriptions.*.duration_days.required_with' => 'Vui lòng nhập số ngày dùng thuốc.',
            'attachments.*.mimes' => 'Tệp đính kèm phải là định dạng JPG, PNG, WEBP hoặc PDF.',
            'attachments.*.max' => 'Dung lượng mỗi tệp tối đa 10MB.',
        ]);

        $record = DB::transaction(function () use ($validated, $request, $appointment) {
            $user = Auth::user();
            $doctor = Doctor::where('user_id', $user->id)->first();
            $doctorId = $doctor ? $doctor->id : $appointment->doctor_id;

            // 1. Create or update medical record
            $record = MedicalRecord::where('appointment_id', $appointment->id)->first();

            if (!$record) {
                $record = MedicalRecord::create([
                    'record_code' => MedicalRecord::generateRecordCode(),
                    'appointment_id' => $appointment->id,
                    'patient_profile_id' => $appointment->patient_profile_id,
                    'doctor_id' => $doctorId,
                    'symptoms' => $validated['symptoms'],
                    'diagnosis' => $validated['diagnosis'],
                    'clinical_notes' => $validated['clinical_notes'] ?? null,
                    'treatment_plan' => $validated['treatment_plan'] ?? null,
                    'follow_up_date' => $validated['follow_up_date'] ?? null,
                ]);
            } else {
                $record->update([
                    'symptoms' => $validated['symptoms'],
                    'diagnosis' => $validated['diagnosis'],
                    'clinical_notes' => $validated['clinical_notes'] ?? null,
                    'treatment_plan' => $validated['treatment_plan'] ?? null,
                    'follow_up_date' => $validated['follow_up_date'] ?? null,
                ]);
            }

            // 2. Clear old prescriptions and insert new ones
            $record->prescriptions()->delete();

            if (!empty($validated['prescriptions'])) {
                foreach ($validated['prescriptions'] as $med) {
                    if (!empty(trim($med['medication_name'] ?? ''))) {
                        Prescription::create([
                            'medical_record_id' => $record->id,
                            'medication_name' => trim($med['medication_name']),
                            'dosage' => trim($med['dosage']),
                            'frequency' => trim($med['frequency']),
                            'duration_days' => (int) $med['duration_days'],
                            'instructions' => !empty($med['instructions']) ? trim($med['instructions']) : null,
                        ]);
                    }
                }
            }

            // 3. Handle Medical Attachments (X-Quang, MRI, Siêu âm, Kết quả XN, v.v.)
            if ($request->hasFile('attachments')) {
                $types = $request->input('attachment_types', []);
                $validTypes = ['x_ray', 'lab_result', 'prescription_scan', 'avatar'];

                foreach ($request->file('attachments') as $idx => $file) {
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $mime = $file->getClientMimeType();
                        $size = $file->getSize();

                        $assignedType = $types[$idx] ?? null;
                        if (!in_array($assignedType, $validTypes, true)) {
                            $assignedType = str_starts_with($mime, 'image/') ? 'x_ray' : 'lab_result';
                        }

                        $storedPath = $file->store('medical_attachments/' . date('Y/m'), 'public');

                        Attachment::create([
                            'medical_record_id' => $record->id,
                            'uploaded_by_user_id' => $user->id,
                            'original_name' => $originalName,
                            'file_path' => $storedPath,
                            'file_size' => $size,
                            'file_mime' => $mime,
                            'file_type' => $assignedType,
                        ]);
                    }
                }
            }

            // 4. Mark appointment as COMPLETED
            $appointment->update([
                'status' => Appointment::STATUS_COMPLETED,
            ]);

            return $record;
        });

        return redirect()
            ->route('doctor.records.show', $record)
            ->with('success', "Đã hoàn tất ca khám cho bệnh nhân {$appointment->patientProfile->full_name}. Bệnh án mã {$record->record_code} đã được lưu thành công.");
    }

    /**
     * Show detailed medical record and prescriptions.
     */
    public function showRecord(MedicalRecord $record): View
    {
        $record->load(['appointment.specialty', 'patientProfile', 'doctor.user', 'prescriptions', 'attachments.uploader']);

        return view('doctor.records.show', compact('record'));
    }

    /**
     * Upload additional medical attachment (X-Quang, lab result) to an existing record.
     */
    public function uploadAttachment(Request $request, MedicalRecord $record): RedirectResponse
    {
        $request->validate([
            'attachment' => ['required', 'file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:10240'],
            'file_type' => ['nullable', 'in:x_ray,lab_result,prescription_scan'],
        ], [
            'attachment.required' => 'Vui lòng chọn tệp cận lâm sàng cần tải lên.',
            'attachment.mimes' => 'Định dạng hỗ trợ: JPG, PNG, WEBP, PDF.',
            'attachment.max' => 'Dung lượng tệp tối đa 10MB.',
        ]);

        $file = $request->file('attachment');
        $mime = $file->getClientMimeType();
        $type = $request->input('file_type') ?: (str_starts_with($mime, 'image/') ? 'x_ray' : 'lab_result');
        $storedPath = $file->store('medical_attachments/' . date('Y/m'), 'public');

        Attachment::create([
            'medical_record_id' => $record->id,
            'uploaded_by_user_id' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_size' => $file->getSize(),
            'file_mime' => $mime,
            'file_type' => $type,
        ]);

        return back()->with('success', 'Đã tải lên tệp đính kèm kết quả cận lâm sàng thành công.');
    }

    /**
     * Delete an attachment.
     */
    public function destroyAttachment(MedicalRecord $record, Attachment $attachment): RedirectResponse
    {
        if ($attachment->medical_record_id !== $record->id) {
            abort(404, 'Tệp đính kèm không thuộc hồ sơ bệnh án này.');
        }

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Đã xóa tệp đính kèm cận lâm sàng.');
    }

    /**
     * Print official hospital prescription (A4/A5).
     */
    public function printPrescription(MedicalRecord $record): View
    {
        $record->load(['appointment.specialty', 'patientProfile', 'doctor.user', 'prescriptions']);

        return view('doctor.records.print', compact('record'));
    }
}
