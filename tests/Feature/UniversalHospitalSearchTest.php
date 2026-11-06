<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\HospitalBed;
use App\Models\HospitalRoom;
use App\Models\LabOrder;
use App\Models\Medication;
use App\Models\PatientInvoice;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use Tests\TestCase;

class UniversalHospitalSearchTest extends TestCase
{
    protected User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receptionist = User::where('role', User::ROLE_RECEPTIONIST)->first()
            ?? User::factory()->create([
                'email' => 'rec_search_' . uniqid() . '@tamanh.vn',
                'role' => User::ROLE_RECEPTIONIST,
            ]);
    }

    /**
     * Test guest cannot access universal search endpoint.
     */
    public function test_guest_is_redirected_from_universal_search(): void
    {
        $response = $this->get(route('universal.search'));
        $response->assertRedirect('/login');
    }

    /**
     * Test empty query returns default navigation suggestions.
     */
    public function test_empty_query_returns_default_suggestions(): void
    {
        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => '']));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'query',
            'total',
            'results' => [
                'navigation',
                'patients',
                'doctors',
                'medications',
                'invoices',
                'beds',
                'labs',
                'appointments',
                'specialties',
            ],
        ]);
        $this->assertNotEmpty($response->json('results.navigation'));
    }

    /**
     * Test searching for patient by unique name returns matching patient.
     */
    public function test_search_finds_patient_by_name(): void
    {
        $patientUser = User::factory()->create([
            'email' => 'patient_search_' . uniqid() . '@gmail.com',
            'role' => User::ROLE_PATIENT,
        ]);

        $uniqueName = 'Nguyễn Đăng Khoa ' . rand(1000, 9999);
        $patient = PatientProfile::create([
            'user_id' => $patientUser->id,
            'patient_code' => 'BN-' . uniqid() . '-' . rand(100, 999),
            'full_name' => $uniqueName,
            'dob' => '1985-06-20',
            'gender' => 'male',
            'phone' => '0918' . rand(100000, 999999),
            'allergies' => 'Penicillin',
        ]);

        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => $uniqueName]));
        $response->assertStatus(200);

        $patients = $response->json('results.patients');
        $this->assertNotEmpty($patients);
        $this->assertStringContainsString($uniqueName, $patients[0]['title']);
    }

    /**
     * Test searching for medication returns matching drug.
     */
    public function test_search_finds_medication(): void
    {
        $uniqueMedName = 'Thuốc Kháng Sinh Thử Nghiệm ' . rand(100, 999);
        $med = Medication::create([
            'medication_code' => 'TH-' . uniqid() . '-' . rand(10, 99),
            'name' => $uniqueMedName,
            'active_ingredient' => 'Cefuroxime',
            'dosage_form' => 'Viên nang',
            'unit' => 'Viên',
            'stock_quantity' => 500,
            'min_stock_alert' => 20,
            'unit_price' => 12000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => $uniqueMedName]));
        $response->assertStatus(200);

        $meds = $response->json('results.medications');
        $this->assertNotEmpty($meds);
        $this->assertStringContainsString($uniqueMedName, $meds[0]['title']);
    }

    /**
     * Test searching for hospital bed / room finds bed.
     */
    public function test_search_finds_hospital_bed(): void
    {
        $room = HospitalRoom::firstOrCreate(
            ['room_number' => 'P999'],
            ['department' => 'Hồi sức tích cực ICU', 'room_type' => 'vip', 'floor' => 9]
        );

        $uniqueBedCode = 'BED-' . rand(10000, 99999);
        $bed = HospitalBed::create([
            'room_id' => $room->id,
            'bed_code' => $uniqueBedCode,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => $uniqueBedCode]));
        $response->assertStatus(200);

        $beds = $response->json('results.beds');
        $this->assertNotEmpty($beds);
        $this->assertStringContainsString($uniqueBedCode, $beds[0]['title']);
    }

    /**
     * Test searching for lab order by code.
     */
    public function test_search_finds_lab_order(): void
    {
        $patientUser = User::factory()->create([
            'email' => 'pat_lab_' . uniqid() . '@tamanh.vn',
            'role' => User::ROLE_PATIENT,
        ]);

        $patient = PatientProfile::create([
            'user_id' => $patientUser->id,
            'patient_code' => 'BN-' . uniqid() . '-' . rand(100, 999),
            'full_name' => 'Bệnh nhân Xét nghiệm ' . rand(100, 999),
            'gender' => 'male',
            'dob' => '1992-05-15',
            'phone' => '0912' . rand(100000, 999999),
        ]);

        $uniqueOrderCode = 'ORD-' . strtoupper(uniqid());
        $lab = LabOrder::create([
            'order_code' => $uniqueOrderCode,
            'patient_profile_id' => $patient->id,
            'clinical_diagnosis' => 'Theo dõi nhiễm khuẩn huyết',
            'status' => LabOrder::STATUS_PENDING,
            'total_amount' => 350000,
            'ordered_at' => now(),
        ]);

        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => $uniqueOrderCode]));
        $response->assertStatus(200);

        $labs = $response->json('results.labs');
        $this->assertNotEmpty($labs);
        $this->assertStringContainsString($uniqueOrderCode, $labs[0]['title']);
    }

    /**
     * Test searching for appointment by code.
     */
    public function test_search_finds_appointment(): void
    {
        $patientUser = User::factory()->create([
            'email' => 'pat_apt_' . uniqid() . '@tamanh.vn',
            'role' => User::ROLE_PATIENT,
        ]);

        $patient = PatientProfile::create([
            'user_id' => $patientUser->id,
            'patient_code' => 'BN-' . uniqid() . '-' . rand(100, 999),
            'full_name' => 'Bệnh nhân Khám bệnh ' . rand(100, 999),
            'gender' => 'female',
            'dob' => '1995-10-20',
            'phone' => '0913' . rand(100000, 999999),
        ]);

        $doctor = \App\Models\Doctor::first() ?? \App\Models\Doctor::factory()->create();
        $specialty = Specialty::first() ?? Specialty::create(['name' => 'Nội tổng quát ' . rand(100, 999), 'is_active' => true]);

        $uniqueAptCode = 'APT-' . strtoupper(uniqid());
        $apt = Appointment::create([
            'appointment_code' => $uniqueAptCode,
            'patient_profile_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'symptom_description' => 'Đau tức ngực trái kéo dài',
            'status' => Appointment::STATUS_PENDING,
            'appointment_date' => now()->format('Y-m-d'),
            'shift' => Appointment::SHIFT_MORNING,
        ]);

        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => $uniqueAptCode]));
        $response->assertStatus(200);

        $apts = $response->json('results.appointments');
        $this->assertNotEmpty($apts);
        $this->assertStringContainsString($uniqueAptCode, $apts[0]['title']);
    }

    /**
     * Test searching with unaccented Vietnamese matches system features.
     */
    public function test_search_matches_unaccented_vietnamese_feature(): void
    {
        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => 'tiep nhan']));
        $response->assertStatus(200);

        $nav = $response->json('results.navigation');
        $this->assertNotEmpty($nav);
    }

    /**
     * Test searching with special characters does not cause SQL error or 500.
     */
    public function test_search_handles_special_characters_gracefully(): void
    {
        $response = $this->actingAs($this->receptionist)->getJson(route('universal.search', ['q' => "'; DROP TABLE users; -- <script>"]));
        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('total'));
    }
}
