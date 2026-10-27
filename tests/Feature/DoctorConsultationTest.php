<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DoctorConsultationTest extends TestCase
{
    private function getDoctorUser(): User
    {
        return User::where('role', User::ROLE_DOCTOR)->first();
    }

    private function getReceptionist(): User
    {
        return User::where('role', User::ROLE_RECEPTIONIST)->first();
    }

    public function test_doctor_can_open_examination_room_for_confirmed_appointment(): void
    {
        $doctorUser = $this->getDoctorUser();
        $doctor = Doctor::where('user_id', $doctorUser->id)->first();
        $patient = PatientProfile::first();

        $appointment = Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $doctor->specialty_id,
            'appointment_date' => Carbon::today(),
            'shift' => 'morning',
            'appointment_time' => '08:30',
            'symptom_description' => '[Sinh hiệu: HA: 120/80 mmHg, Mạch: 75 l/p] Đau tức ngực trái',
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($doctorUser)->get(route('doctor.examine', $appointment));

        $response->assertStatus(200);
        $response->assertSee('Bàn Khám Bệnh Ngoại Trú');
        $response->assertSee($patient->full_name);
        $response->assertSee('HA: 120/80 mmHg');
    }

    public function test_doctor_can_store_medical_record_and_prescriptions(): void
    {
        $doctorUser = $this->getDoctorUser();
        $doctor = Doctor::where('user_id', $doctorUser->id)->first();
        $patient = PatientProfile::first();

        $appointment = Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $doctor->specialty_id,
            'appointment_date' => Carbon::today(),
            'shift' => 'morning',
            'appointment_time' => '09:00',
            'symptom_description' => 'Sốt nhẹ và ho có đờm',
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($doctorUser)->post(route('doctor.examine.store', $appointment), [
            'symptoms' => 'Họng đỏ xung huyết, amidan sưng nhẹ, không khó thở.',
            'diagnosis' => 'Viêm họng cấp do vi khuẩn',
            'clinical_notes' => 'Nghe phổi trong, không rales.',
            'treatment_plan' => 'Súc họng nước muối ấm, uống thuốc theo đơn 5 ngày.',
            'follow_up_date' => Carbon::tomorrow()->addDays(5)->toDateString(),
            'prescriptions' => [
                [
                    'medication_name' => 'Augmentin 1g',
                    'dosage' => '1 viên/lần',
                    'frequency' => 'Ngày 2 lần sau ăn',
                    'duration_days' => 5,
                    'instructions' => 'Uống đầu bữa ăn no',
                ],
                [
                    'medication_name' => 'Paracetamol 500mg',
                    'dosage' => '1 viên/lần',
                    'frequency' => 'Ngày 2-3 lần khi sốt > 38.5C',
                    'duration_days' => 3,
                    'instructions' => 'Cách nhau ít nhất 4-6 giờ',
                ],
            ],
        ]);

        // Appointment status updated to completed
        $appointment->refresh();
        $this->assertEquals(Appointment::STATUS_COMPLETED, $appointment->status);

        // Medical record created
        $record = MedicalRecord::where('appointment_id', $appointment->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals('Viêm họng cấp do vi khuẩn', $record->diagnosis);
        $this->assertStringStartsWith('MR-', $record->record_code);

        // Prescriptions created
        $this->assertCount(2, $record->prescriptions);

        $response->assertRedirect(route('doctor.records.show', $record));
    }

    public function test_doctor_can_print_official_prescription(): void
    {
        $doctorUser = $this->getDoctorUser();
        $doctor = Doctor::where('user_id', $doctorUser->id)->first();
        $patient = PatientProfile::first();

        $appointment = Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $doctor->specialty_id,
            'appointment_date' => Carbon::today(),
            'shift' => 'morning',
            'appointment_time' => '10:00',
            'symptom_description' => 'Khám tổng quát định kỳ',
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $record = MedicalRecord::create([
            'record_code' => MedicalRecord::generateRecordCode(),
            'appointment_id' => $appointment->id,
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'symptoms' => 'Bình thường',
            'diagnosis' => 'Theo dõi sức khỏe tổng quát',
        ]);

        $response = $this->actingAs($doctorUser)->get(route('doctor.records.print', $record));

        $response->assertStatus(200);
        $response->assertSee('ĐƠN THUỐC NGOẠI TRÚ');
        $response->assertSee($patient->full_name);
        $response->assertSee($record->record_code);
    }
}
