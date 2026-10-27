<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\User;
use Tests\TestCase;

class PatientPortalTest extends TestCase
{
    public function test_patient_can_view_appointment_history(): void
    {
        $patientUser = User::where('email', 'patient@gmail.com')->first();

        $response = $this->actingAs($patientUser)->get('/patient/history');

        $response->assertStatus(200);
        $response->assertSee('Lịch Sử Khám Bệnh');
        $response->assertSee('Nguyễn Văn Bệnh Nhân');
    }

    public function test_patient_cannot_view_another_patients_appointment(): void
    {
        $patientUserMai = User::where('email', 'patient.mai@gmail.com')->first();

        // Appointment 1 belongs to patient@gmail.com
        $appPatientAn = Appointment::whereHas('patientProfile', function ($q) {
            $q->where('phone', '0987654321');
        })->first();

        $response = $this->actingAs($patientUserMai)->get('/patient/appointments/' . $appPatientAn->id);

        $response->assertStatus(403);
    }

    public function test_patient_can_cancel_pending_appointment(): void
    {
        $patientUser = User::where('email', 'patient@gmail.com')->first();
        $profile = PatientProfile::where('user_id', $patientUser->id)->first();

        $pendingApp = Appointment::where('patient_profile_id', $profile->id)
            ->where('status', Appointment::STATUS_PENDING)
            ->first();

        if (!$pendingApp) {
            $pendingApp = Appointment::create([
                'appointment_code' => Appointment::generateAppointmentCode(),
                'patient_profile_id' => $profile->id,
                'doctor_id' => 1,
                'specialty_id' => 1,
                'appointment_date' => now()->addDays(3)->toDateString(),
                'shift' => 'morning',
                'symptom_description' => 'Test pending appointment',
                'status' => Appointment::STATUS_PENDING,
                'created_by_user_id' => $patientUser->id,
            ]);
        }

        $response = $this->actingAs($patientUser)->put('/patient/appointments/' . $pendingApp->id . '/cancel', [
            'cancellation_reason' => 'Có việc đột xuất không đến được',
        ]);

        $response->assertSessionHas('success');
        $pendingApp->refresh();
        $this->assertEquals(Appointment::STATUS_CANCELLED, $pendingApp->status);
    }

    public function test_patient_can_print_their_own_prescription(): void
    {
        $patientUser = User::where('email', 'patient@gmail.com')->first();
        $profile = PatientProfile::where('user_id', $patientUser->id)->first();

        $completedApp = Appointment::where('patient_profile_id', $profile->id)
            ->whereHas('medicalRecord')
            ->first();

        if ($completedApp) {
            $response = $this->actingAs($patientUser)->get('/patient/appointments/' . $completedApp->id . '/prescription/print');

            $response->assertStatus(200);
            $response->assertSee('ĐƠN THUỐC NGOẠI TRÚ');
            $response->assertSee('BỆNH VIỆN ĐA KHOA TÂM ANH');
        }
    }

    public function test_patient_cannot_print_another_patients_prescription(): void
    {
        $patientUserMai = User::where('email', 'patient.mai@gmail.com')->first();

        // Appointment belongs to patient@gmail.com
        $appPatientAn = Appointment::whereHas('patientProfile', function ($q) {
            $q->where('phone', '0987654321');
        })->whereHas('medicalRecord')->first();

        if ($appPatientAn) {
            $response = $this->actingAs($patientUserMai)->get('/patient/appointments/' . $appPatientAn->id . '/prescription/print');
            $response->assertStatus(403);
        }
    }
}
