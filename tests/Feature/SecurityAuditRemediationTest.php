<?php

namespace Tests\Feature;

use App\Models\PatientInvoice;
use App\Models\PatientProfile;
use App\Models\User;
use Tests\TestCase;

class SecurityAuditRemediationTest extends TestCase
{
    /**
     * Test SEC-P0-01: Laravel storage serve routes are disabled for private storage.
     * PUT/GET storage/{path} must not be publicly exposed without auth/policy.
     */
    public function test_private_storage_serve_routes_are_not_exposed(): void
    {
        // Attempting to upload to storage/test_leak.txt should not succeed with 200/201
        $responsePut = $this->put('/storage/test_leak.txt', ['content' => 'malicious_content']);
        $this->assertTrue(in_array($responsePut->status(), [404, 405]));

        // Attempting to read storage/test_leak.txt directly via serve route should return 404
        $responseGet = $this->get('/storage/test_leak.txt');
        $this->assertTrue(in_array($responseGet->status(), [404, 405]));
    }

    /**
     * Test SEC-P1-01: RBAC enforcement on clinical consultation routes.
     * Patients must be denied (403 Forbidden). Doctors/Admins must have access.
     */
    public function test_patient_cannot_access_clinical_consultations(): void
    {
        $patientUser = User::factory()->create([
            'role' => User::ROLE_PATIENT,
            'email' => 'audit_patient_' . uniqid() . '@tamanh.vn',
        ]);

        $response = $this->actingAs($patientUser)->get(route('consultations.index'));
        $response->assertStatus(403);
    }

    public function test_doctor_can_access_clinical_consultations(): void
    {
        $doctorUser = User::where('role', User::ROLE_DOCTOR)->first()
            ?? User::factory()->create(['role' => User::ROLE_DOCTOR]);

        $response = $this->actingAs($doctorUser)->get(route('consultations.index'));
        $response->assertStatus(200);
    }

    /**
     * Test PRIV-P2-01: Patient name & code masking in public billing lookup.
     * Patient's full name must be masked (e.g. 'Nguyễn V** H***') to prevent PII exposure.
     */
    public function test_public_billing_lookup_masks_patient_pii(): void
    {
        $patientUser = User::factory()->create([
            'role' => User::ROLE_PATIENT,
            'email' => 'audit_pii_' . uniqid() . '@tamanh.vn',
        ]);

        $patient = PatientProfile::create([
            'user_id' => $patientUser->id,
            'patient_code' => 'BN-' . rand(100000, 999999),
            'full_name' => 'Nguyễn Văn Hưng',
            'dob' => '1990-05-15',
            'gender' => 'male',
            'phone' => '0912345678',
        ]);

        $invoice = PatientInvoice::create([
            'invoice_code' => 'HD-AUDIT-' . uniqid(),
            'patient_profile_id' => $patient->id,
            'consultation_fee' => 500000,
            'total_amount' => 500000,
            'insurance_covered_amount' => 0,
            'patient_co_pay_amount' => 500000,
            'payment_status' => PatientInvoice::STATUS_UNPAID,
        ]);

        $response = $this->get(route('public.billing.lookup', ['code' => $invoice->invoice_code]));
        $response->assertStatus(200);

        // Check that masked name is displayed, and full raw name is not shown
        $response->assertSee('Nguyễn V* H***');
        $response->assertDontSee('>Nguyễn Văn Hưng<');
    }
}
