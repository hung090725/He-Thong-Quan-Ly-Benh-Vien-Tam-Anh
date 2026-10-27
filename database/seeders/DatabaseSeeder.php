<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $this->call(UserSeeder::class);

        // 2. Seed Specialties
        $specialtiesData = [
            [
                'name' => 'Tim mạch học',
                'slug' => 'tim-mach-hoc',
                'description' => 'Chẩn đoán và điều trị toàn diện các bệnh lý tim mạch, cao huyết áp, rối loạn nhịp tim với trang thiết bị siêu âm tim Doppler 4D hiện đại.',
                'icon' => 'bi bi-heart-pulse',
                'is_active' => true,
            ],
            [
                'name' => 'Nhi khoa',
                'slug' => 'nhi-khoa',
                'description' => 'Khám và tư vấn phát triển thể chất, tiêm chủng, điều trị bệnh lý hô hấp, tiêu hóa cho trẻ sơ sinh và trẻ nhỏ.',
                'icon' => 'bi bi-emoji-smile',
                'is_active' => true,
            ],
            [
                'name' => 'Chấn thương chỉnh hình',
                'slug' => 'chan-thuong-chinh-hinh',
                'description' => 'Điều trị chấn thương xương khớp, phẫu thuật nội soi khớp, thoái hóa khớp gối và cột sống.',
                'icon' => 'bi bi-activity',
                'is_active' => true,
            ],
            [
                'name' => 'Tai Mũi Họng',
                'slug' => 'tai-mui-hong',
                'description' => 'Nội soi tầm soát ung thư vòm họng, điều trị viêm xoang, viêm amidan, viêm tai giữa bằng công nghệ Plasma.',
                'icon' => 'bi bi-headset',
                'is_active' => true,
            ],
            [
                'name' => 'Tiêu hóa - Gan mật',
                'slug' => 'tieu-hoa-gan-mat',
                'description' => 'Nội soi dạ dày - đại tràng không đau bằng dải tần hẹp NBI, điều trị viêm loét, trào ngược và bệnh lý gan mật tụy.',
                'icon' => 'bi bi-capsule',
                'is_active' => true,
            ],
            [
                'name' => 'Mắt (Nhãn khoa)',
                'slug' => 'mat-nhan-khoa',
                'description' => 'Đo khúc xạ, khám và điều trị tật khúc xạ, đục thủy tinh thể, glôcôm với hệ thống máy sinh hiển vi kỹ thuật số.',
                'icon' => 'bi bi-eye',
                'is_active' => true,
            ],
        ];

        $specialties = [];
        foreach ($specialtiesData as $item) {
            $specialties[$item['slug']] = Specialty::updateOrCreate(['slug' => $item['slug']], $item);
        }

        // 3. Seed Doctors
        $doctorUsers = [
            'doctor@tamanh.vn' => [
                'specialty_id' => $specialties['tim-mach-hoc']->id,
                'qualification' => 'TS.BS',
                'consultation_fee' => 450000.00,
                'bio' => 'Hơn 20 năm kinh nghiệm trong lĩnh vực Tim mạch can thiệp. Từng tu nghiệp chuyên sâu tại Pháp.',
                'room_number' => 'Phòng 201 - Tòa A',
                'is_available' => true,
            ],
            'doctor.hung@tamanh.vn' => [
                'specialty_id' => $specialties['nhi-khoa']->id,
                'qualification' => 'ThS.BS',
                'consultation_fee' => 350000.00,
                'bio' => 'Bác sĩ chuyên khoa Nhi giàu kinh nghiệm và tận tâm, chuyên điều trị hô hấp và dinh dưỡng trẻ em.',
                'room_number' => 'Phòng 105 - Tòa B',
                'is_available' => true,
            ],
            'doctor.lan@tamanh.vn' => [
                'specialty_id' => $specialties['tai-mui-hong']->id,
                'qualification' => 'Bác sĩ CKI',
                'consultation_fee' => 300000.00,
                'bio' => 'Chuyên gia nội soi Tai Mũi Họng kỹ thuật cao, nhiều năm công tác tại bệnh viện tuyến trung ương.',
                'room_number' => 'Phòng 304 - Tòa A',
                'is_available' => true,
            ],
            'doctor.dat@tamanh.vn' => [
                'specialty_id' => $specialties['chan-thuong-chinh-hinh']->id,
                'qualification' => 'PGS.TS.BS',
                'consultation_fee' => 500000.00,
                'bio' => 'Chuyên gia đầu ngành về cơ xương khớp, thay khớp nhân tạo và phục hồi chức năng thể thao.',
                'room_number' => 'Phòng 402 - Tòa A',
                'is_available' => true,
            ],
        ];

        $doctors = [];
        foreach ($doctorUsers as $email => $docInfo) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $doctors[$email] = Doctor::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge($docInfo, ['user_id' => $user->id])
                );
            }
        }

        // 4. Seed Doctor Schedules (Monday to Sunday, morning & afternoon shifts)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($doctors as $doc) {
            foreach ($days as $day) {
                // Morning shift
                DoctorSchedule::updateOrCreate([
                    'doctor_id' => $doc->id,
                    'day_of_week' => $day,
                    'shift' => 'morning',
                ], [
                    'start_time' => '07:30:00',
                    'end_time' => '11:30:00',
                    'max_patients' => 15,
                    'is_active' => true,
                ]);

                // Afternoon shift (skip Sunday afternoon)
                if ($day !== 'sunday') {
                    DoctorSchedule::updateOrCreate([
                        'doctor_id' => $doc->id,
                        'day_of_week' => $day,
                        'shift' => 'afternoon',
                    ], [
                        'start_time' => '13:30:00',
                        'end_time' => '17:00:00',
                        'max_patients' => 15,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 5. Seed Patient Profiles
        $patientUser = User::where('email', 'patient@gmail.com')->first();
        $patientProfile = PatientProfile::updateOrCreate([
            'phone' => '0987654321',
        ], [
            'user_id' => $patientUser ? $patientUser->id : null,
            'patient_code' => 'BN-20261017-0001',
            'full_name' => 'Nguyễn Văn Bệnh Nhân',
            'dob' => '1990-05-15',
            'gender' => 'male',
            'email' => 'patient@gmail.com',
            'address' => 'Số 45 Tràng Tiền, Hoàn Kiếm, Hà Nội',
            'identity_card' => '001090012345',
            'insurance_code' => 'DN4010123456789',
        ]);

        $patientUserMai = User::where('email', 'patient.mai@gmail.com')->first();
        $patientProfileMai = PatientProfile::updateOrCreate([
            'phone' => '0978123456',
        ], [
            'user_id' => $patientUserMai ? $patientUserMai->id : null,
            'patient_code' => 'BN-20261017-0002',
            'full_name' => 'Vũ Thị Tuyết Mai',
            'dob' => '1995-10-20',
            'gender' => 'female',
            'email' => 'patient.mai@gmail.com',
            'address' => '12 Kim Mã, Ba Đình, Hà Nội',
            'identity_card' => '001195098765',
            'insurance_code' => 'DN4010987654321',
        ]);

        // Walk-in patients without account
        $patientWalkIn = PatientProfile::updateOrCreate([
            'phone' => '0912334455',
        ], [
            'user_id' => null,
            'patient_code' => 'BN-20261017-0003',
            'full_name' => 'Lê Hoàng Nam',
            'dob' => '1985-02-10',
            'gender' => 'male',
            'email' => 'nam.le@company.com',
            'address' => 'P502 Tòa Landmark, Cầu Giấy, Hà Nội',
            'identity_card' => '001085002233',
        ]);

        // 6. Seed Appointments
        $primaryDoctor = $doctors['doctor@tamanh.vn'] ?? Doctor::first();
        $pediatricDoctor = $doctors['doctor.hung@tamanh.vn'] ?? Doctor::first();
        $entDoctor = $doctors['doctor.lan@tamanh.vn'] ?? Doctor::first();

        $today = Carbon::today();

        // 6.1. Completed appointment for Patient with full Medical Record & Prescription
        $appCompleted = Appointment::updateOrCreate([
            'appointment_code' => 'AP-20261016-0001',
        ], [
            'patient_profile_id' => $patientProfile->id,
            'doctor_id' => $primaryDoctor->id,
            'specialty_id' => $primaryDoctor->specialty_id,
            'appointment_date' => $today->copy()->subDays(2),
            'shift' => 'morning',
            'appointment_time' => '08:30:00',
            'symptom_description' => 'Đau tức ngực trái khi gắng sức, hồi hộp đánh trống ngực.',
            'status' => Appointment::STATUS_COMPLETED,
            'created_by_user_id' => $patientUser ? $patientUser->id : null,
        ]);

        $medRecord = MedicalRecord::updateOrCreate([
            'appointment_id' => $appCompleted->id,
        ], [
            'record_code' => 'MR-20261016-0001',
            'patient_profile_id' => $patientProfile->id,
            'doctor_id' => $primaryDoctor->id,
            'symptoms' => 'Huyết áp 140/90 mmHg, nhịp tim 88 ck/phút. Tim T1, T2 rõ, không nghe tiếng thổi bệnh lý. Đau tức ngực trái vùng trước tim.',
            'diagnosis' => 'Tăng huyết áp độ 1 - Cơn đau thắt ngực ổn định (I20).',
            'clinical_notes' => 'Điện tâm đồ (ECG) ghi nhận nhịp xoang đều, ST-T không chênh. Siêu âm tim Doppler chức năng tâm thất trái EF 62%.',
            'treatment_plan' => 'Dùng thuốc kiểm soát huyết áp đều đặn mỗi sáng. Chế độ ăn giảm muối, hạn chế dầu mỡ và thức uống chứa cồn. Tái khám sau 14 ngày.',
            'follow_up_date' => $today->copy()->addDays(12),
        ]);

        Prescription::updateOrCreate([
            'medical_record_id' => $medRecord->id,
            'medication_name' => 'Amlodipine 5mg',
        ], [
            'dosage' => '1 viên/lần',
            'frequency' => 'Uống 1 lần vào buổi sáng sau ăn',
            'duration_days' => 14,
            'instructions' => 'Theo dõi huyết áp hàng ngày tại nhà.',
        ]);

        Prescription::updateOrCreate([
            'medical_record_id' => $medRecord->id,
            'medication_name' => 'Aspirin 81mg',
        ], [
            'dosage' => '1 viên/lần',
            'frequency' => 'Uống 1 lần vào buổi trưa sau ăn no',
            'duration_days' => 14,
            'instructions' => 'Không uống khi bụng đói.',
        ]);

        // 6.2. Confirmed appointment for today (waiting for doctor examination)
        Appointment::updateOrCreate([
            'appointment_code' => 'AP-20261017-0001',
        ], [
            'patient_profile_id' => $patientProfile->id,
            'doctor_id' => $primaryDoctor->id,
            'specialty_id' => $primaryDoctor->specialty_id,
            'appointment_date' => $today,
            'shift' => 'morning',
            'appointment_time' => '09:00:00',
            'symptom_description' => 'Tái khám định kỳ chỉ số huyết áp, đo lại điện tâm đồ.',
            'status' => Appointment::STATUS_CONFIRMED,
            'created_by_user_id' => $patientUser ? $patientUser->id : null,
        ]);

        // 6.3. Pending appointment for walk-in patient (waiting for receptionist check-in)
        Appointment::updateOrCreate([
            'appointment_code' => 'AP-20261017-0002',
        ], [
            'patient_profile_id' => $patientWalkIn->id,
            'doctor_id' => $entDoctor->id,
            'specialty_id' => $entDoctor->specialty_id,
            'appointment_date' => $today,
            'shift' => 'morning',
            'appointment_time' => '10:00:00',
            'symptom_description' => 'Đau rát họng, khàn tiếng kéo dài, nghẹt mũi bên phải.',
            'status' => Appointment::STATUS_PENDING,
            'created_by_user_id' => null,
        ]);

        // 6.4. Future appointment
        Appointment::updateOrCreate([
            'appointment_code' => 'AP-20261017-0003',
        ], [
            'patient_profile_id' => $patientProfileMai->id,
            'doctor_id' => $pediatricDoctor->id,
            'specialty_id' => $pediatricDoctor->specialty_id,
            'appointment_date' => $today->copy()->addDays(2),
            'shift' => 'afternoon',
            'appointment_time' => '14:30:00',
            'symptom_description' => 'Khám tổng quát và tư vấn dinh dưỡng cho bé 2 tuổi.',
            'status' => Appointment::STATUS_PENDING,
            'created_by_user_id' => $patientUserMai ? $patientUserMai->id : null,
        ]);
        // 7. Seed Outpatient Pharmacy Formulary & Inventory
        $this->call(MedicationSeeder::class);
    }
}
