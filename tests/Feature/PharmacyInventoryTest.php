<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\PatientProfile;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class PharmacyInventoryTest extends TestCase
{
    private User $receptionist;
    private User $patientUser;
    private Specialty $specialty;
    private Doctor $doctor;

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
                'room_number' => 'P.302',
                'is_available' => true,
            ]);
    }

    public function test_receptionist_can_view_pharmacy_inventory_dashboard(): void
    {
        $code = 'MED-VIEW-' . rand(1000, 9999);
        $name = 'Thuốc Test Dashboard ' . rand(100, 999);

        Medication::create([
            'medication_code' => $code,
            'name' => $name,
            'active_ingredient' => 'Amoxicillin + Acid Clavulanic',
            'dosage_form' => 'Viên nén bao phim',
            'unit' => 'Viên',
            'stock_quantity' => 250,
            'min_stock_alert' => 50,
            'unit_price' => 18500,
        ]);

        $response = $this->actingAs($this->receptionist)
            ->get(route('reception.pharmacy.inventory.index', ['q' => $code]));

        $response->assertOk();
        $response->assertSee('Kho Dược Ngoại Trú & Quản Lý Tồn Kho');
        $response->assertSee($name);
        $response->assertSee($code);
    }

    public function test_unauthorized_user_cannot_view_inventory(): void
    {
        $response = $this->actingAs($this->patientUser)
            ->get(route('reception.pharmacy.inventory.index'));

        $response->assertForbidden();
    }

    public function test_can_create_new_medication_in_inventory(): void
    {
        $name = 'Panadol Extra ' . rand(1000, 9999);
        $payload = [
            'name' => $name,
            'active_ingredient' => 'Paracetamol, Caffeine',
            'dosage_form' => 'Viên nén bao phim',
            'unit' => 'Viên',
            'stock_quantity' => 1000,
            'min_stock_alert' => 100,
            'unit_price' => 2500,
            'batch_number' => 'PAN-BATCH-' . rand(100, 999),
            'expiry_date' => Carbon::today()->addYears(2)->toDateString(),
            'dosage_default' => '1 viên/lần',
            'frequency_default' => 'Uống khi sốt hoặc đau, cách 4-6 giờ',
            'duration_default' => 3,
            'instructions_default' => 'Không dùng quá 8 viên/ngày',
        ];

        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.pharmacy.inventory.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('medications', [
            'name' => $name,
            'stock_quantity' => 1000,
        ]);
    }

    public function test_can_restock_medication_and_increase_stock(): void
    {
        $medication = Medication::create([
            'medication_code' => 'MED-RESTOCK-' . rand(1000, 9999),
            'name' => 'Amlodipine Test ' . rand(100, 999),
            'dosage_form' => 'Viên nén',
            'unit' => 'Viên',
            'stock_quantity' => 100,
            'min_stock_alert' => 30,
            'unit_price' => 3200,
        ]);

        $batch = 'AML-BATCH-' . rand(100, 999);
        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.pharmacy.inventory.restock', $medication), [
                'quantity' => 500,
                'batch_number' => $batch,
                'expiry_date' => Carbon::today()->addYears(3)->toDateString(),
            ]);

        $response->assertRedirect();
        $medication->refresh();
        $this->assertEquals(600, $medication->stock_quantity);
        $this->assertEquals($batch, $medication->batch_number);
    }

    public function test_can_update_medication_details(): void
    {
        $medication = Medication::create([
            'medication_code' => 'MED-UPD-' . rand(1000, 9999),
            'name' => 'Nexium 40mg ' . rand(100, 999),
            'dosage_form' => 'Viên nén',
            'unit' => 'Viên',
            'stock_quantity' => 200,
            'min_stock_alert' => 40,
            'unit_price' => 24000,
        ]);

        $updatedName = 'Nexium Mups ' . rand(1000, 9999);
        $response = $this->actingAs($this->receptionist)
            ->put(route('reception.pharmacy.inventory.update', $medication), [
                'name' => $updatedName,
                'active_ingredient' => 'Esomeprazole magnesium',
                'dosage_form' => 'Viên nén bao phim kháng acid',
                'unit' => 'Viên',
                'min_stock_alert' => 50,
                'unit_price' => 26000,
            ]);

        $response->assertRedirect();
        $medication->refresh();
        $this->assertEquals($updatedName, $medication->name);
        $this->assertEquals(26000, $medication->unit_price);
    }

    public function test_dispensing_prescription_auto_deducts_inventory_stock(): void
    {
        $uniqueBrand = 'Amoxiklav' . rand(1000, 9999);
        $medName = $uniqueBrand . ' 1g';
        $medication = Medication::create([
            'medication_code' => 'MED-DISP-' . rand(1000, 9999),
            'name' => $medName,
            'dosage_form' => 'Viên nén',
            'unit' => 'Viên',
            'stock_quantity' => 100,
            'min_stock_alert' => 20,
            'unit_price' => 18500,
        ]);

        $patient = PatientProfile::create([
            'patient_code' => 'BN-TEST-' . rand(10000, 99999),
            'full_name' => 'Lê Thị Thu Test',
            'dob' => '1990-05-15',
            'gender' => 'female',
            'phone' => '090' . rand(1000000, 9999999),
        ]);

        $appointment = Appointment::create([
            'appointment_code' => 'AP-TEST-DISP-' . rand(1000, 9999),
            'patient_profile_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'appointment_date' => Carbon::today(),
            'shift' => 'morning',
            'symptom_description' => 'Viêm phế quản cấp',
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $record = MedicalRecord::create([
            'record_code' => 'BA-TEST-DISP-' . rand(1000, 9999),
            'appointment_id' => $appointment->id,
            'patient_profile_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
            'symptoms' => 'Ho đờm, sốt nhẹ',
            'diagnosis' => 'Viêm phế quản cấp (J20)',
            'treatment_plan' => 'Ngoại trú 7 ngày',
        ]);

        // Prescription: 7 days * 2 = 14 tablets
        Prescription::create([
            'medical_record_id' => $record->id,
            'medication_name' => $medName,
            'dosage' => '1 viên',
            'frequency' => '2 lần/ngày',
            'duration_days' => 7,
            'instructions' => 'Sau ăn',
        ]);

        // Dispense medication
        $response = $this->actingAs($this->receptionist)
            ->post(route('reception.pharmacy.dispense', $record), [
                'dispensed_notes' => 'Đã kiểm tra đúng bệnh nhân và dặn dò kỹ lưỡng',
            ]);

        $response->assertRedirect();
        $record->refresh();
        $medication->refresh();

        $this->assertTrue($record->isDispensed());
        // Initial 100 - (7 * 2 = 14) = 86
        $this->assertEquals(86, $medication->stock_quantity);

        // Undo dispense
        $undoResponse = $this->actingAs($this->receptionist)
            ->post(route('reception.pharmacy.undo', $record));

        $undoResponse->assertRedirect();
        $record->refresh();
        $medication->refresh();

        $this->assertFalse($record->isDispensed());
        // Stock restored back to 100
        $this->assertEquals(100, $medication->stock_quantity);
    }

    public function test_can_delete_medication(): void
    {
        $medication = Medication::create([
            'medication_code' => 'MED-DEL-' . rand(1000, 9999),
            'name' => 'Thuốc Hết Lưu Hành ' . rand(100, 999),
            'dosage_form' => 'Viên',
            'unit' => 'Viên',
            'stock_quantity' => 0,
            'min_stock_alert' => 10,
            'unit_price' => 5000,
        ]);

        $response = $this->actingAs($this->receptionist)
            ->delete(route('reception.pharmacy.inventory.destroy', $medication));

        $response->assertRedirect();
        $this->assertDatabaseMissing('medications', [
            'id' => $medication->id,
        ]);
    }
}
