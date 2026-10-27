<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\PatientVital;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class EhrVitalsTrackerTest extends TestCase
{
    private User $receptionist;
    private User $patientUser;
    private Specialty $specialty;
    private Doctor $doctor;
    private PatientProfile $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receptionist = User::where('role', User::ROLE_RECEPTIONIST)->first()
            ?? User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->patientUser = User::where('role', User::ROLE_PATIENT)->first()
            ?? User::factory()->create(['role' => User::ROLE_PATIENT]);

        $this->specialty = Specialty::first()
            ?? Specialty::create([
                'name' => 'Tim Mạch Tâm Anh',
                'slug' => 'tim-mach-tam-anh',
                'is_active' => true,
            ]);

        $this->doctor = Doctor::first()
            ?? Doctor::create([
                'user_id' => User::factory()->create(['role' => User::ROLE_DOCTOR])->id,
                'specialty_id' => $this->specialty->id,
                'qualification' => 'Thạc sĩ Bác sĩ CKII',
                'consultation_fee' => 300000,
                'room_number' => 'P.301',
                'is_available' => true,
            ]);

        $this->patient = PatientProfile::create([
            'patient_code' => 'BN-EHR-' . rand(10000, 99999),
            'full_name' => 'Hoàng Minh Tuấn ' . rand(100, 999),
            'dob' => '1985-08-20',
            'gender' => 'male',
            'phone' => '091' . rand(1000000, 9999999),
            'blood_group' => 'O+',
        ]);
    }

    public function test_receptionist_can_view_patient_ehr_vitals_tab(): void
    {
        PatientVital::create([
            'patient_profile_id' => $this->patient->id,
            'recorded_by_user_id' => $this->receptionist->id,
            'recorded_at' => Carbon::now()->subDays(2),
            'systolic_bp' => 125,
            'diastolic_bp' => 82,
            'heart_rate' => 74,
            'temperature' => 36.6,
            'spo2' => 98,
            'notes' => 'Triage lúc tiếp nhận tại quầy',
        ]);

        $response = $this->actingAs($this->receptionist)
            ->get(route('reception.patients.show', $this->patient));

        $response->assertOk();
        $response->assertSee('Diễn Tiến Sinh Hiệu');
        $response->assertSee('125/82');
        $response->assertSee('vitalsChart');
    }

    public function test_auto_extracts_vitals_from_unstructured_medical_record(): void
    {
        $appointment = Appointment::create([
            'appointment_code' => 'AP-EHR-EXTRACT-' . rand(1000, 9999),
            'patient_profile_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'appointment_date' => Carbon::today()->subDays(5),
            'shift' => 'morning',
            'symptom_description' => 'Đau ngực',
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        MedicalRecord::create([
            'record_code' => 'BA-EHR-EXTRACT-' . rand(1000, 9999),
            'appointment_id' => $appointment->id,
            'patient_profile_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'symptoms' => 'Huyết áp 140/90 mmHg, nhịp tim 88 ck/phút. Thân nhiệt 37.2°C, SpO2 99%.',
            'diagnosis' => 'Tăng huyết áp độ 1',
        ]);

        $this->assertEquals(0, $this->patient->vitals()->count());

        // Accessing show should trigger automatic smart extraction
        $response = $this->actingAs($this->receptionist)
            ->get(route('reception.patients.show', $this->patient));

        $response->assertOk();

        $this->assertDatabaseHas('patient_vitals', [
            'patient_profile_id' => $this->patient->id,
            'systolic_bp' => 140,
            'diastolic_bp' => 90,
            'heart_rate' => 88,
            'temperature' => 37.2,
            'spo2' => 99,
        ]);
    }

    public function test_can_record_new_vitals_measurement(): void
    {
        $payload = [
            'recorded_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'systolic_bp' => 135,
            'diastolic_bp' => 85,
            'heart_rate' => 80,
            'temperature' => 36.8,
            'spo2' => 98,
            'weight' => 70.5,
            'height' => 172.0,
            'notes' => 'Bệnh nhân nghỉ ngơi 10 phút trước khi đo',
        ];

        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.patients.vitals.store', $this->patient), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_vitals', [
            'patient_profile_id' => $this->patient->id,
            'systolic_bp' => 135,
            'diastolic_bp' => 85,
            'heart_rate' => 80,
            'weight' => 70.5,
            'height' => 172.0,
        ]);
    }

    public function test_clinical_evaluations_and_classifications(): void
    {
        // 1. Stage 1 Hypertension
        $vital1 = PatientVital::create([
            'patient_profile_id' => $this->patient->id,
            'recorded_at' => Carbon::now(),
            'systolic_bp' => 145,
            'diastolic_bp' => 92,
            'heart_rate' => 105, // Tachycardia
            'temperature' => 38.2, // Fever
            'spo2' => 93, // Hypoxia
            'weight' => 75.0,
            'height' => 170.0,
        ]);

        $bpClass = $vital1->bp_classification;
        $this->assertEquals('Tăng Huyết Áp Độ 1', $bpClass['label']);
        $this->assertTrue($vital1->isTachycardia());
        $this->assertTrue($vital1->isFever());
        $this->assertTrue($vital1->isHypoxia());

        // BMI: 75 / (1.7^2) = 25.95 -> Béo phì Độ 1 (Asian criteria)
        $this->assertEquals(26.0, $vital1->bmi);
        $this->assertEquals('Béo phì Độ 1', $vital1->bmi_classification);

        // 2. Optimal Blood Pressure
        $vital2 = PatientVital::create([
            'patient_profile_id' => $this->patient->id,
            'recorded_at' => Carbon::now(),
            'systolic_bp' => 118,
            'diastolic_bp' => 76,
            'heart_rate' => 72,
            'temperature' => 36.5,
            'spo2' => 99,
        ]);

        $this->assertEquals('Huyết Áp Tối Ưu', $vital2->bp_classification['label']);
        $this->assertFalse($vital2->isFever());
        $this->assertFalse($vital2->isHypoxia());
    }

    public function test_can_delete_vital_entry(): void
    {
        $vital = PatientVital::create([
            'patient_profile_id' => $this->patient->id,
            'recorded_at' => Carbon::now(),
            'systolic_bp' => 120,
            'diastolic_bp' => 80,
            'notes' => 'Ghi nhầm cần xóa',
        ]);

        $response = $this->actingAs($this->receptionist)
            ->delete(route('reception.vitals.destroy', $vital));

        $response->assertRedirect();
        $this->assertDatabaseMissing('patient_vitals', [
            'id' => $vital->id,
        ]);
    }
}
