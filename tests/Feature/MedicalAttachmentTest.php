<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Attachment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalAttachmentTest extends TestCase
{
    protected User $doctorUser;
    protected Doctor $doctor;
    protected Specialty $specialty;
    protected PatientProfile $patientProfile;
    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->doctorUser = User::where('role', User::ROLE_DOCTOR)->first()
            ?? User::factory()->create(['role' => User::ROLE_DOCTOR]);

        $this->doctor = Doctor::where('user_id', $this->doctorUser->id)->first()
            ?? Doctor::factory()->create(['user_id' => $this->doctorUser->id]);

        $this->specialty = Specialty::first() ?? Specialty::factory()->create();

        $this->patientProfile = PatientProfile::first() ?? PatientProfile::factory()->create();

        $this->appointment = Appointment::create([
            'appointment_code' => 'AP-TEST-ATT-' . rand(1000, 9999),
            'patient_profile_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'appointment_date' => Carbon::tomorrow()->addDays(8),
            'shift' => 'morning',
            'queue_number' => 99,
            'status' => Appointment::STATUS_CONFIRMED,
            'symptom_description' => 'Test đau ngực kèm khó thở',
        ]);
    }

    public function test_doctor_can_upload_attachments_during_consultation(): void
    {
        $fakeXray = UploadedFile::fake()->create('chest_xray.jpg', 500, 'image/jpeg');
        $fakePdf = UploadedFile::fake()->create('blood_test_results.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->doctorUser)->post(
            route('doctor.examine.store', $this->appointment),
            [
                'symptoms' => 'Bệnh nhân đau tức ngực khi gắng sức',
                'diagnosis' => 'Theo dõi cơn đau thắt ngực ổn định',
                'clinical_notes' => 'Nghe tim đều, phổi thông khí rõ',
                'treatment_plan' => 'Nghỉ ngơi, kiểm tra điện tâm đồ và men tim',
                'prescriptions' => [
                    [
                        'medication_name' => 'Amlodipin 5mg',
                        'dosage' => '1 viên/lần',
                        'frequency' => 'Ngày 1 lần vào buổi sáng',
                        'duration_days' => 14,
                        'instructions' => 'Uống lúc 8h sáng',
                    ],
                ],
                'attachments' => [$fakeXray, $fakePdf],
                'attachment_types' => ['x_ray', 'lab_result'],
            ]
        );

        $response->assertRedirect();

        $record = MedicalRecord::where('appointment_id', $this->appointment->id)->first();
        $this->assertNotNull($record);
        $this->assertCount(2, $record->attachments);

        $xrayAtt = $record->attachments->where('file_type', 'x_ray')->first();
        $this->assertNotNull($xrayAtt);
        $this->assertEquals('chest_xray.jpg', $xrayAtt->original_name);
        $this->assertTrue($xrayAtt->is_image);
        Storage::disk('public')->assertExists($xrayAtt->file_path);

        $pdfAtt = $record->attachments->where('file_type', 'lab_result')->first();
        $this->assertNotNull($pdfAtt);
        $this->assertEquals('blood_test_results.pdf', $pdfAtt->original_name);
        $this->assertTrue($pdfAtt->is_pdf);
        Storage::disk('public')->assertExists($pdfAtt->file_path);
    }

    public function test_doctor_can_upload_additional_attachment_from_record_view(): void
    {
        $record = MedicalRecord::create([
            'record_code' => MedicalRecord::generateRecordCode(),
            'appointment_id' => $this->appointment->id,
            'patient_profile_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctor->id,
            'symptoms' => 'Triệu chứng kiểm tra',
            'diagnosis' => 'Chẩn đoán kiểm tra',
        ]);

        $fakeUltrasound = UploadedFile::fake()->create('abdominal_ultrasound.png', 500, 'image/png');

        $response = $this->actingAs($this->doctorUser)->post(
            route('doctor.records.attachments.upload', $record),
            [
                'attachment' => $fakeUltrasound,
                'file_type' => 'x_ray',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attachments', [
            'medical_record_id' => $record->id,
            'original_name' => 'abdominal_ultrasound.png',
            'file_type' => 'x_ray',
        ]);
    }

    public function test_doctor_can_delete_an_attachment(): void
    {
        $record = MedicalRecord::create([
            'record_code' => MedicalRecord::generateRecordCode(),
            'appointment_id' => $this->appointment->id,
            'patient_profile_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctor->id,
            'symptoms' => 'Test delete att',
            'diagnosis' => 'Test diagnosis',
        ]);

        $fakeFile = UploadedFile::fake()->create('test_delete.pdf', 100);
        $path = $fakeFile->store('medical_attachments/test', 'public');

        $attachment = Attachment::create([
            'medical_record_id' => $record->id,
            'uploaded_by_user_id' => $this->doctorUser->id,
            'original_name' => 'test_delete.pdf',
            'file_path' => $path,
            'file_size' => 1024,
            'file_mime' => 'application/pdf',
            'file_type' => 'lab_result',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->doctorUser)->delete(
            route('doctor.records.attachments.destroy', [$record, $attachment])
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
