<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\CodeBlueAclsTracker;
use App\Models\MedicalGasPipeline;
use App\Models\ToxicologyHemoperfusion;
use App\Models\RotemViscoelasticCoagulation;
use App\Models\CriticalHelipadTransfer;
use App\Models\DigitalTwinCssdSterile;
use App\Models\PatientControlledAnalgesia;
use App\Models\GravimetricChemoCompounding;
use App\Models\IntraoperativeNeuromonitoring;
use App\Models\LvadMechanicalCirculatory;
use App\Models\InfantSecurityRtls;
use App\Models\BradenSmartBedPressure;
use App\Models\DonorHumanMilkBank;
use App\Models\NuclearMedicineRadiation;
use App\Models\ClinicalTrialManagement;
use Tests\TestCase;

class Wave8HospitalInnovationTest extends TestCase
{
    private function createReceptionistUser(): User
    {
        return User::factory()->create([
            'email' => 'rec_w8_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    private function createPatient(): PatientProfile
    {
        return PatientProfile::create([
            'patient_code' => 'BN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)) . rand(100, 999),
            'full_name' => 'Bệnh Nhân Đột Phá W8 ' . rand(100, 999),
            'phone' => '0966' . rand(100000, 999999),
            'dob' => '1990-08-20',
            'gender' => 'female',
            'identity_card' => '001090' . rand(100000, 999999),
            'address' => 'Hà Nội',
        ]);
    }

    private function createDoctor(): Doctor
    {
        $specialty = Specialty::firstOrCreate(
            ['slug' => 'chuyen-khoa-sau-w8'],
            ['name' => 'Khoa Mũi Nhọn Công Nghệ Y Khoa W8', 'description' => 'Khoa Hồi Sức Sống Còn & Thiết Bị Y Tế Cao Cấp']
        );

        $user = User::factory()->create([
            'email' => 'doc_w8_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'doctor',
            'is_active' => true,
        ]);

        return Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
            'qualification' => 'TS.BS Chuyên Gia Đầu Ngành Wave 8',
            'consultation_fee' => 700000,
            'room_number' => 'W8.VIP',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Test Code Blue & Ép Tim ACLS, Tái Lập Tuần Hoàn ROSC
     */
    public function test_01_code_blue_acls_cpr_quality_and_rosc(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'code_team_leader_id' => $doctor->id,
            'event_location' => 'Khoa Cấp Cứu A1',
            'initial_rhythm' => 'ventricular_fibrillation_vf',
            'cpr_total_duration_minutes' => 14,
            'chest_compression_fraction_pct' => 88.0,
            'defibrillation_shocks_count' => 2,
            'epinephrine_doses_given' => 3,
            'amiodarone_doses_given' => 1,
            'advanced_airway_placed' => 1,
            'etco2_during_cpr_mmhg' => 26.5,
            'rosc_achieved' => 1,
            'final_outcome' => 'admitted_to_icu',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation-safety.code-blue'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('evaluation.high_quality_cpr', true);
        $response->assertJsonPath('evaluation.rosc_achieved', true);

        $this->assertDatabaseHas('code_blue_acls_trackers', [
            'patient_id' => $patient->id,
            'rosc_achieved' => true,
            'initial_rhythm' => 'ventricular_fibrillation_vf',
        ]);
    }

    /**
     * 2. Test Mạng Lưới Khí Y Tế MGPS & Dự Báo Cạn Bồn Oxy Lỏng LOX
     */
    public function test_02_medical_gas_pipeline_o2_pressure_and_lox_depletion(): void
    {
        $user = $this->createReceptionistUser();
        $doctor = $this->createDoctor();

        $payload = [
            'zone_manifold_code' => 'MGPS-ZONE-W8-' . rand(100, 999),
            'building_floor' => 'Tòa A - Tầng 3 ICU',
            'biomedical_engineer_id' => $doctor->id,
            'oxygen_line_pressure_bar' => 4.2,
            'medical_air_pressure_bar' => 4.0,
            'surgical_air_pressure_bar' => 7.0,
            'vacuum_suction_pressure_bar' => -0.72,
            'nitrous_oxide_pressure_bar' => 4.0,
            'liquid_oxygen_tank_level_pct' => 85.0,
            'liquid_oxygen_remaining_liters' => 15000.0,
            'active_ventilators_count' => 30,
            'pipeline_leak_detected' => 0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation-safety.medical-gas'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('safety.o2_pressure_safe', true);
        $response->assertJsonPath('safety.emergency_alert', false);

        $this->assertDatabaseHas('medical_gas_pipelines', [
            'zone_manifold_code' => $payload['zone_manifold_code'],
            'oxygen_line_pressure_bar' => 4.2,
        ]);
    }

    /**
     * 3. Test Lọc Máu Hấp Phụ Ngộ Độc Cấp Theo Đồ Thị Rumack-Matthew
     */
    public function test_03_toxicology_hemoperfusion_rumack_matthew_and_clearance(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'toxicologist_id' => $doctor->id,
            'toxin_or_drug_name' => 'Paracetamol (Acetaminophen)',
            'estimated_ingestion_hours_ago' => 4.0,
            'ingested_dose_mg_or_ml' => 20000.0,
            'serum_toxin_concentration_mcg_ml' => 320.0, // > 300 mcg/mL tại 4h => Lọc máu khẩn
            'rumack_matthew_toxicity_risk' => 'probable_toxicity',
            'extracorporeal_modality' => 'hemoperfusion_resin',
            'toxin_clearance_ml_min' => 160.0,
            'recommended_duration_hours' => 4.0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation-safety.toxicology'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('toxicology.extracorporeal_indicated', true);

        $this->assertDatabaseHas('toxicology_hemoperfusions', [
            'patient_id' => $patient->id,
            'hemodialysis_indicated' => true,
        ]);
    }

    /**
     * 4. Test Phân Tích Đàn Nhớt ROTEM & Chỉ Định Bù Máu / Acid Tranexamic
     */
    public function test_04_rotem_viscoelastic_coagulation_and_txa_indication(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'anesthesiologist_id' => $doctor->id,
            'clinical_scenario' => 'severe_polytrauma',
            'extem_ct_seconds' => 95, // > 80s => Thiếu yếu tố đông máu
            'extem_cft_seconds' => 140,
            'extem_alpha_angle_degrees' => 58.0,
            'extem_mcf_mm' => 42,
            'extem_ml_pct' => 22.5, // > 15% => Tăng tiêu sợi huyết (Cần TXA)
            'fibtem_mcf_mm' => 8, // < 10mm => Thiếu Fibrinogen (Cần Cryoprecipitate)
            'aptem_mcf_mm' => 55,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation-safety.rotem'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('rotem.hyperfibrinolysis', true);
        $response->assertJsonPath('rotem.txa_indicated', true);

        $this->assertDatabaseHas('rotem_viscoelastic_coagulations', [
            'patient_id' => $patient->id,
            'hyperfibrinolysis_detected' => true,
            'tranexamic_acid_indicated' => true,
        ]);
    }

    /**
     * 5. Test Điều Phối Chuyển Viện Nặng & Trực Thăng Cấp Cứu Helipad
     */
    public function test_05_critical_helipad_transfer_trips_risk_and_clearance(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'flight_physician_id' => $doctor->id,
            'flight_mission_code' => 'HELI-TEST-W8-' . rand(1000, 9999),
            'transfer_origin_location' => 'Bệnh viện Quân y Côn Đảo',
            'aircraft_type' => 'airbus_h225_super_puma',
            'transfer_risk_score_trips' => 22,
            'inflight_ventilator_running' => 1,
            'inflight_ecmo_running' => 1,
            'helipad_landing_clearance_granted' => 1,
            'receiving_department' => 'trauma_hybrid_or',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation-safety.helipad'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('readiness.high_risk', true);
        $response->assertJsonPath('readiness.helipad_cleared', true);

        $this->assertDatabaseHas('critical_helipad_transfers', [
            'flight_mission_code' => $payload['flight_mission_code'],
            'helipad_landing_clearance_granted' => true,
        ]);
    }

    /**
     * 6. Test Digital Twin Tiệt Trùng Dụng Cụ Mổ CSSD Bằng Mã Vạch 2D & Bào Tử
     */
    public function test_06_digital_twin_cssd_sterile_release_verification(): void
    {
        $user = $this->createReceptionistUser();
        $doctor = $this->createDoctor();

        $payload = [
            'tray_barcode_2d' => 'CSSD-TRAY-TEST-' . rand(1000, 9999),
            'tray_name' => 'Bộ Đại Phẫu Khớp Gối Nhân Tạo Stryker',
            'cssd_supervisor_id' => $doctor->id,
            'sterilization_method' => 'autoclave_steam_134c',
            'sterilizer_chamber_number' => 2,
            'chemical_integrator_passed' => 1,
            'biological_spore_test_passed' => 1,
            'sterile_expiration_date' => date('Y-m-d', strtotime('+30 days')),
            'tray_status' => 'sterile_storage',
            'assigned_operating_room' => 'OR 05 - Hybrid',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.surgical-chemo-device.cssd'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('verification.released_for_surgery', true);

        $this->assertDatabaseHas('digital_twin_cssd_steriles', [
            'tray_barcode_2d' => $payload['tray_barcode_2d'],
            'chemical_integrator_passed' => true,
            'biological_spore_test_passed' => true,
        ]);
    }

    /**
     * 7. Test Bơm Tiêm Giảm Đau Tự Kiểm Soát PCA Khóa An Toàn Khi Tụt Hô Hấp
     */
    public function test_07_patient_controlled_analgesia_respiratory_lockout(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'anesthesiologist_id' => $doctor->id,
            'opioid_agent' => 'Morphine Sulfate 1mg/mL',
            'basal_infusion_rate_mg_or_mcg_per_h' => 1.0,
            'pca_bolus_dose_mg_or_mcg' => 1.0,
            'lockout_interval_minutes' => 8,
            'four_hour_dose_limit' => 20.0,
            'total_demands_count' => 18,
            'delivered_bolus_count' => 12,
            'etco2_respiratory_monitoring_mmhg' => 52.5, // > 50 mmHg => Cảnh báo ức chế hô hấp
            'spo2_monitoring_pct' => 90.5, // < 92%
            'pain_score_vas_10' => 'vas_3_5_moderate',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.surgical-chemo-device.pca'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('safety.respiratory_depression_alert', true);
        $response->assertJsonPath('safety.lock_pump', true);

        $this->assertDatabaseHas('patient_controlled_analgesias', [
            'patient_id' => $patient->id,
            'respiratory_depression_lockout_active' => true,
        ]);
    }

    /**
     * 8. Test Pha Chế Thuốc Hóa Trị Ung Thư Bằng Cân Trọng Khối Gravimetric
     */
    public function test_08_gravimetric_chemo_compounding_accuracy_check(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'pharmacist_id' => $doctor->id,
            'preparation_barcode' => 'CHEMO-BARCODE-' . rand(10000, 99999),
            'chemo_drug_name' => 'Paclitaxel 175mg/m2',
            'prescribed_dose_mg' => 280.0,
            'carrier_diluent' => 'NaCl 0.9% 500mL',
            'expected_total_weight_grams' => 540.0,
            'measured_actual_weight_grams' => 542.5, // Sai lệch ~0.46% (đạt <= 5%)
            'biosafety_cabinet_hepa_certified' => 1,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.surgical-chemo-device.chemo'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('gravimetric.passed', true);

        $this->assertDatabaseHas('gravimetric_chemo_compoundings', [
            'preparation_barcode' => $payload['preparation_barcode'],
            'gravimetric_check_passed' => true,
        ]);
    }

    /**
     * 9. Test Giám Sát Tổn Thương Thần Kinh Trong Mổ (IONM) Tụt MEP Cảnh Báo
     */
    public function test_09_intraoperative_neuromonitoring_mep_ssep_alert(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'neurophysiologist_id' => $doctor->id,
            'surgical_type' => 'scoliosis_spinal_deformity',
            'mep_amplitude_uv' => 120.0,
            'mep_amplitude_drop_pct' => 58.0, // >= 50% => Kích hoạt báo động tổn thương bó tháp
            'ssep_latency_ms' => 24.5,
            'ssep_latency_increase_pct' => 14.0, // >= 10%
            'emg_spontaneous_train_activity' => 1,
            'surgical_corrective_action_taken' => 'Nới lỏng ốc vít T7-T8 và nâng MAP > 85 mmHg',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.surgical-chemo-device.ionm'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('neuromonitoring.alert_triggered', true);

        $this->assertDatabaseHas('intraoperative_neuromonitorings', [
            'patient_id' => $patient->id,
            'neuromonitoring_alert_triggered' => true,
        ]);
    }

    /**
     * 10. Test Giám Sát Vận Hành Thiết Bị Hỗ Trợ Thất Trái LVAD (HeartMate 3)
     */
    public function test_10_lvad_telemetry_pump_thrombosis_and_suction_detection(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'cardiologist_id' => $doctor->id,
            'lvad_serial_number' => 'HM3-W8-' . rand(10000, 99999),
            'pump_speed_rpm' => 5400,
            'pump_flow_l_per_min' => 7.2, // Tăng vọt cùng Power >= 8W => Nghi tắc huyết khối
            'pump_power_watts' => 8.8,
            'pulsatility_index_pi' => 4.5,
            'inr_anticoagulation_target' => 2.5,
            'driveline_infection_alert' => 0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.surgical-chemo-device.lvad'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('lvad.thrombosis_risk', true);
        $response->assertJsonPath('lvad.status', 'pump_thrombosis_alert');

        $this->assertDatabaseHas('lvad_mechanical_circulatories', [
            'patient_id' => $patient->id,
            'pump_thrombosis_suspected' => true,
        ]);
    }

    /**
     * 11. Test An Ninh Sơ Sinh RTLS, Chống Nhầm & Cảnh Báo Khóa Thang Máy
     */
    public function test_11_infant_security_rtls_tamper_and_geofence_lockdown(): void
    {
        $user = $this->createReceptionistUser();
        $infant = $this->createPatient();
        $mother = $this->createPatient();

        $payload = [
            'infant_patient_id' => $infant->id,
            'mother_patient_id' => $mother->id,
            'infant_rtls_tag_id' => 'TAG-INFANT-' . rand(1000, 9999),
            'mother_rtls_tag_id' => 'TAG-MOM-' . rand(1000, 9999),
            'skin_tamper_sensor_intact' => 1,
            'mother_infant_dual_pairing_verified' => 0, // Không có mẹ đi cùng
            'current_floor_room' => 'Hành Lang Thang Máy Tầng 4',
            'distance_to_nearest_exit_meters' => 3.5, // <= 5m => Geofence Breach
        ];

        $response = $this->actingAs($user)->postJson(route('reception.patient-safety-pediatric.rtls'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('security.geofence_breach', true);
        $response->assertJsonPath('security.lockdown_elevator', true);

        $this->assertDatabaseHas('infant_security_rtls', [
            'infant_rtls_tag_id' => $payload['infant_rtls_tag_id'],
            'elevator_emergency_lockdown_triggered' => true,
        ]);
    }

    /**
     * 12. Test Đệm Thông Minh Braden Ngừa Loét Tì Đè & Nhắc Xoay Trở
     */
    public function test_12_braden_smart_bed_risk_score_and_turn_reminder(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'head_nurse_id' => $doctor->id,
            'braden_sensory_perception' => 1,
            'braden_moisture' => 2,
            'braden_activity' => 1,
            'braden_mobility' => 1,
            'braden_nutrition' => 2,
            'braden_friction_shear' => 1,
            // Tổng Braden = 1+2+1+1+2+1 = 8 (<= 9 => Very High Risk)
            'sacrum_peak_pressure_mmhg' => 38.0,
            'heels_peak_pressure_mmhg' => 28.0,
            'bed_head_elevation_degrees' => 35.0, // > 30 độ cảnh báo trượt
            'last_repositioning_elapsed_minutes' => 140, // >= 120p => Nhắc xoay trở
        ];

        $response = $this->actingAs($user)->postJson(route('reception.patient-safety-pediatric.braden'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('braden.total_score', 8);
        $response->assertJsonPath('braden.risk_level', 'very_high_risk');
        $response->assertJsonPath('braden.turn_due', true);

        $this->assertDatabaseHas('braden_smart_bed_pressures', [
            'patient_id' => $patient->id,
            'total_braden_score' => 8,
            'turn_reminder_due_alert' => true,
        ]);
    }

    /**
     * 13. Test Ngân Hàng Sữa Mẹ Tiệt Trùng Holder DHMB Cho Trẻ Sinh Non
     */
    public function test_13_donor_human_milk_bank_holder_pasteurization_safety(): void
    {
        $user = $this->createReceptionistUser();
        $doctor = $this->createDoctor();

        $payload = [
            'milk_batch_code' => 'DHMB-BATCH-' . rand(1000, 9999),
            'anonymous_donor_code' => 'DONOR-H78',
            'dhmb_technician_id' => $doctor->id,
            'batch_volume_ml' => 1800.0,
            'pre_pasteurization_culture_passed' => 1,
            'post_pasteurization_culture_sterile' => 1,
            'energy_kcal_per_100ml' => 69.5,
            'protein_grams_per_100ml' => 1.35,
            'fat_grams_per_100ml' => 4.1,
            'milk_batch_status' => 'pasteurized_approved',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.patient-safety-pediatric.milk-bank'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('safety.approved_for_nicu', true);

        $this->assertDatabaseHas('donor_human_milk_banks', [
            'milk_batch_code' => $payload['milk_batch_code'],
            'post_pasteurization_culture_sterile' => true,
        ]);
    }

    /**
     * 14. Test An Toàn Bức Xạ Y Học Hạt Nhân PET/CT & Tiêu Chuẩn Xuất Viện
     */
    public function test_14_nuclear_medicine_decay_and_safe_discharge(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'radiation_safety_officer_id' => $doctor->id,
            'radiopharmaceutical' => 'f18_fludeoxyglucose',
            'injected_activity_mbq' => 260.0,
            'isotope_half_life_minutes' => 109.8,
            'elapsed_minutes' => 180.0,
            'staff_personal_dosimeter_rate_usv_h' => 2.8,
            'patient_surface_radiation_rate_usv_h' => 14.5, // < 20 uSv/h => Đủ chuẩn xuất viện
        ];

        $response = $this->actingAs($user)->postJson(route('reception.patient-safety-pediatric.nuclear'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('nuclear.safe_for_discharge', true);

        $this->assertDatabaseHas('nuclear_medicine_radiations', [
            'patient_id' => $patient->id,
            'safe_for_discharge_home' => true,
        ]);
    }

    /**
     * 15. Test Quản Lý Thử Nghiệm Lâm Sàng CTMS & Báo Cáo SAE 24h Chuẩn GCP
     */
    public function test_15_clinical_trial_management_gcp_and_sae_reporting(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'protocol_code' => 'TA-CTMS-PHASE3-' . rand(100, 999),
            'patient_id' => $patient->id,
            'principal_investigator_id' => $doctor->id,
            'trial_title' => 'Thử nghiệm lâm sàng đa trung tâm thuốc phân tử đích',
            'study_phase' => 'phase_3',
            'randomization_arm' => 'investigational_drug_arm',
            'electronic_informed_consent_signed' => 1,
            'study_visit_number' => 2,
            'protocol_adherence_rate_pct' => 96.0,
            'serious_adverse_event_sae_occurred' => 1,
            'sae_reported_to_irb_within_24h_at' => now()->toDateTimeString(),
            'subject_status' => 'enrolled_active',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.patient-safety-pediatric.trial'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('audit.gcp_compliant', true);
        $response->assertJsonPath('audit.sae_reported_on_time', true);

        $this->assertDatabaseHas('clinical_trial_managements', [
            'protocol_code' => $payload['protocol_code'],
            'serious_adverse_event_sae_occurred' => true,
        ]);
    }

    /**
     * 16. Test Render Giao Diện Critical Resuscitation Safety Dashboard
     */
    public function test_16_critical_resuscitation_safety_dashboard_view_renders(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.critical-resuscitation-safety.index'));

        $response->assertStatus(200);
        $response->assertSee('Hồi Sức Code Blue', false);
        $response->assertSee('Khí Y Tế MGPS', false);
        $response->assertSee('Trực Thăng Helipad', false);
    }

    /**
     * 17. Test Render Giao Diện Surgical Chemo Device Dashboard
     */
    public function test_17_surgical_chemo_device_dashboard_view_renders(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.surgical-chemo-device.index'));

        $response->assertStatus(200);
        $response->assertSee('Tiệt Trùng CSSD', false);
        $response->assertSee('Bơm Giảm Đau PCA', false);
        $response->assertSee('Hóa Trị Gravimetric', false);
    }

    /**
     * 18. Test Render Giao Diện Patient Safety Pediatric Dashboard
     */
    public function test_18_patient_safety_pediatric_dashboard_view_renders(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.patient-safety-pediatric.index'));

        $response->assertStatus(200);
        $response->assertSee('An Ninh Sơ Sinh RTLS', false);
        $response->assertSee('Đệm Thông Minh Braden', false);
        $response->assertSee('Ngân Hàng Sữa Mẹ DHMB', false);
    }

    /**
     * 19. Test Business Logic Model Unit Wave 8: Các Hàm Xử Lý Thuật Toán Y Khoa
     */
    public function test_19_wave8_model_business_logics_and_calculations(): void
    {
        // 1. Code Blue ACLS
        $acls = CodeBlueAclsTracker::evaluateAclsResuscitationQuality(85.0, 22.0, 2, true);
        $this->assertTrue($acls['high_quality_cpr']);
        $this->assertTrue($acls['perfusion_adequate']);

        // 2. MGPS Gas Pipeline
        $gas = MedicalGasPipeline::evaluateMedicalGasSafety(4.2, -0.7, 12000.0, 25);
        $this->assertTrue($gas['o2_pressure_safe']);
        $this->assertFalse($gas['emergency_alert']);

        // 3. Toxicology Rumack-Matthew
        $tox = ToxicologyHemoperfusion::evaluateToxicologyIntervention('Paraquat', 3.0, 80.0);
        $this->assertTrue($tox['extracorporeal_indicated']);

        // 4. ROTEM Fibrinolysis
        $rotem = RotemViscoelasticCoagulation::analyzeRotemParameters(90, 40, 8, 20.0);
        $this->assertTrue($rotem['hyperfibrinolysis']);
        $this->assertTrue($rotem['txa_indicated']);

        // 5. Helipad Readiness
        $heli = CriticalHelipadTransfer::evaluateHelipadTransferReadiness(25, true, true, true);
        $this->assertTrue($heli['high_risk']);
        $this->assertTrue($heli['helipad_cleared']);

        // 6. CSSD Sterile Release
        $cssd = DigitalTwinCssdSterile::verifySterileRelease(true, true, date('Y-m-d', strtotime('+10 days')));
        $this->assertTrue($cssd['released_for_surgery']);

        // 7. PCA Smart Pump Safety
        $pca = PatientControlledAnalgesia::evaluatePcaSafety(54.0, 89.0, 15, 10);
        $this->assertTrue($pca['respiratory_depression_alert']);
        $this->assertTrue($pca['lock_pump']);

        // 8. Gravimetric Chemo Accuracy
        $chemo = GravimetricChemoCompounding::verifyGravimetricAccuracy(500.0, 510.0); // 2% lệch
        $this->assertTrue($chemo['passed']);
        $this->assertEquals(2.0, $chemo['deviation_pct']);

        // 9. IONM Neuromonitoring
        $ionm = IntraoperativeNeuromonitoring::evaluateNeuromonitoringSignal(55.0, 6.0, false);
        $this->assertTrue($ionm['alert_triggered']);

        // 10. LVAD Telemetry
        $lvad = LvadMechanicalCirculatory::analyzeLvadTelemetry(7.5, 9.2, 4.0);
        $this->assertTrue($lvad['thrombosis_risk']);
        $this->assertEquals('pump_thrombosis_alert', $lvad['status']);

        // 11. RTLS Infant Security
        $rtls = InfantSecurityRtls::evaluateInfantSecurityStatus(true, false, 4.0);
        $this->assertTrue($rtls['geofence_breach']);
        $this->assertTrue($rtls['lockdown_elevator']);

        // 12. Braden Smart Bed
        $braden = BradenSmartBedPressure::calculateBradenRisk([1, 1, 1, 1, 2, 1], 150, 40.0);
        $this->assertEquals(7, $braden['total_score']);
        $this->assertEquals('very_high_risk', $braden['risk_level']);
        $this->assertTrue($braden['turn_due']);
        $this->assertTrue($braden['shear_warning']);

        // 13. Donor Milk Bank
        $milk = DonorHumanMilkBank::assessMilkBatchSafety(true, true, 68.0);
        $this->assertTrue($milk['approved_for_nicu']);

        // 14. Nuclear Medicine Decay
        $nuc = NuclearMedicineRadiation::calculateRadiationDecayAndDischarge(250.0, 110.0, 110.0, 12.0);
        $this->assertEquals(125.0, $nuc['residual_activity_mbq']);
        $this->assertTrue($nuc['safe_for_discharge']);

        // 15. Clinical Trial Audit
        $gcp = ClinicalTrialManagement::auditTrialCompliance(true, 95.0, false, null);
        $this->assertTrue($gcp['gcp_compliant']);
    }
}
