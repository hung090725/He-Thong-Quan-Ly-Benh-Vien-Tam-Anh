<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Specialty;
use Carbon\Carbon;
use Tests\TestCase;

class BookingTest extends TestCase
{
    public function test_booking_page_renders_successfully(): void
    {
        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('ĐẶT LỊCH KHÁM BỆNH TRỰC TUYẾN');
    }

    public function test_can_book_an_appointment_successfully(): void
    {
        $doctor = Doctor::first();
        $targetDate = Carbon::tomorrow()->addDays(20);
        $date = $targetDate->toDateString();
        $dayOfWeek = strtolower($targetDate->englishDayOfWeek);

        // Ensure schedule exists for target morning and reset slot
        Appointment::where('doctor_id', $doctor->id)->where('appointment_date', $date)->where('shift', 'morning')->delete();

        DoctorSchedule::updateOrCreate([
            'doctor_id' => $doctor->id,
            'day_of_week' => $dayOfWeek,
            'shift' => 'morning',
        ], [
            'start_time' => '07:30:00',
            'end_time' => '11:30:00',
            'max_patients' => 15,
            'is_active' => true,
        ]);

        $uniquePhone = '0981' . rand(100000, 999999);

        $response = $this->post('/booking', [
            'full_name' => 'Trần Văn Kiểm Thử',
            'phone' => $uniquePhone,
            'dob' => '1992-08-15',
            'gender' => 'male',
            'email' => 'kiemthu@gmail.com',
            'specialty_id' => $doctor->specialty_id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'shift' => 'morning',
            'appointment_time' => '08:00',
            'symptom_description' => 'Sốt nhẹ và ho có đờm từ hôm qua',
        ]);

        $response->assertSessionHas('success');
        $appointment = Appointment::where('symptom_description', 'Sốt nhẹ và ho có đờm từ hôm qua')->first();
        $this->assertNotNull($appointment);
        $this->assertStringStartsWith('AP-', $appointment->appointment_code);
        $this->assertEquals(Appointment::STATUS_PENDING, $appointment->status);

        $patient = PatientProfile::where('phone', $uniquePhone)->first();
        $this->assertNotNull($patient);
        $this->assertEquals('Trần Văn Kiểm Thử', $patient->full_name);
    }

    public function test_cannot_overbook_when_max_patients_reached(): void
    {
        $doctor = Doctor::first();
        $date = Carbon::tomorrow()->addDays(5)->toDateString();
        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);

        // Set max_patients to 1 for testing
        DoctorSchedule::updateOrCreate([
            'doctor_id' => $doctor->id,
            'day_of_week' => $dayOfWeek,
            'shift' => 'morning',
        ], [
            'start_time' => '07:30:00',
            'end_time' => '11:30:00',
            'max_patients' => 1,
            'is_active' => true,
        ]);

        $patient = PatientProfile::first();

        // Create 1 existing appointment to fill the capacity
        Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $doctor->specialty_id,
            'appointment_date' => $date,
            'shift' => 'morning',
            'symptom_description' => 'Bệnh nhân thứ nhất chiếm chỗ',
            'status' => Appointment::STATUS_PENDING,
        ]);

        // Attempting to book a second patient should trigger overbooking validation error
        $response = $this->from('/booking')->post('/booking', [
            'full_name' => 'Bệnh Nhân Thứ Hai',
            'phone' => '0982345678',
            'dob' => '1990-01-01',
            'gender' => 'female',
            'specialty_id' => $doctor->specialty_id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'shift' => 'morning',
            'symptom_description' => 'Cố tình đặt thêm khi đã đầy slot',
        ]);

        $response->assertSessionHasErrors('shift');
    }

    public function test_lookup_appointment_by_code(): void
    {
        $appointment = Appointment::first();

        $response = $this->get('/booking/lookup?keyword=' . $appointment->appointment_code);

        $response->assertStatus(200);
        $response->assertSee($appointment->appointment_code);
        $response->assertSee($appointment->patientProfile->full_name);
    }
}
