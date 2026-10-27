<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\PatientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    /**
     * Show patient appointment history and medical results.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();

        $appointments = collect();

        if ($patientProfile) {
            $query = Appointment::with([
                'specialty',
                'doctor.user',
                'medicalRecord.prescriptions',
            ])
                ->where('patient_profile_id', $patientProfile->id)
                ->latest('appointment_date');

            if ($status = $request->query('status')) {
                $query->where('status', $status);
            }

            $appointments = $query->paginate(10)->withQueryString();
        }

        return view('patient.history', compact('patientProfile', 'appointments'));
    }

    /**
     * View detailed appointment and prescription.
     */
    public function showAppointment(Appointment $appointment): View
    {
        $user = Auth::user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();

        // Enforce ownership: patient can only view their own appointments
        if (!$patientProfile || $appointment->patient_profile_id !== $patientProfile->id) {
            abort(403, 'Bạn không có quyền xem thông tin lịch khám này.');
        }

        $appointment->load([
            'specialty',
            'doctor.user',
            'medicalRecord.prescriptions',
            'medicalRecord.attachments',
            'medicalRecord.sickLeaveCertificate',
        ]);

        return view('patient.appointment-detail', compact('appointment', 'patientProfile'));
    }

    /**
     * Patient prints their official prescription.
     */
    public function printPrescription(Appointment $appointment): View
    {
        $user = Auth::user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();

        if (!$patientProfile || $appointment->patient_profile_id !== $patientProfile->id) {
            abort(403, 'Bạn không có quyền in đơn thuốc này.');
        }

        if (!$appointment->medicalRecord) {
            abort(404, 'Lịch khám này chưa có đơn thuốc.');
        }

        $record = $appointment->medicalRecord->load(['appointment.specialty', 'patientProfile', 'doctor.user', 'prescriptions']);

        return view('doctor.records.print', compact('record'));
    }

    /**
     * Patient can cancel their pending appointment.
     */
    public function cancelAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = Auth::user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();

        if (!$patientProfile || $appointment->patient_profile_id !== $patientProfile->id) {
            abort(403, 'Bạn không có quyền thực hiện thao tác trên lịch hẹn này.');
        }

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Chỉ có thể hủy lịch hẹn đang ở trạng thái Chờ tiếp nhận.');
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ], [
            'cancellation_reason.required' => 'Vui lòng cung cấp lý do hủy hẹn.',
        ]);

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => 'Bệnh nhân tự hủy: ' . $request->input('cancellation_reason'),
        ]);

        return back()->with('success', "Đã hủy lịch hẹn {$appointment->appointment_code} thành công.");
    }
}
