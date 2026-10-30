<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\RadiationOncologyPlan;
use App\Models\CarTCellTherapy;
use App\Models\EcmoTtmSession;
use App\Models\NegativePressureAiir;
use App\Models\PrecisionAntibioticPkPd;
use App\Models\RoboticExoskeletonRehab;
use App\Models\MolecularTumorBoard;
use App\Models\AwakeCraniotomyMapping;
use App\Models\AdvancedEndoscopyProcedure;
use App\Models\CiedRemoteMonitoring;
use App\Models\AntibiogramHaiSurveillance;
use App\Models\ExtubationReadinessSbt;
use App\Models\TraumaResuscitationIss;
use App\Models\CrrtCitrateAnticoagulation;
use App\Models\IcuAbcdefBundlePics;
use Tests\TestCase;

class Wave7HospitalInnovationTest extends TestCase
{
    private function createReceptionistUser(): User
    {
        return User::factory()->create([
            'email' => 'rec_w7_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    private function createPatient(): PatientProfile
    {
        return PatientProfile::create([
            'patient_code' => 'BN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)) . rand(100, 999),
            'full_name' => 'Bệnh Nhân Ung Thư Hồi Sức W7 ' . rand(100, 999),
            'phone' => '0977' . rand(100000, 999999),
            'dob' => '1982-04-12',
            'gender' => 'male',
            'identity_card' => '001082' . rand(100000, 999999),
            'address' => 'Hà Nội',
        ]);
    }

    private function createDoctor(): Doctor
    {
        $specialty = Specialty::firstOrCreate(
            ['slug' => 'ung-thu-hoi-suc-w7'],
            ['name' => 'Khoa Ung Thư & Hồi Sức Tích Cực Wave 7', 'description' => 'Khoa Đột phá Công nghệ Y học BV Đa Khoa Tâm Anh']
        );

        $user = User::factory()->create([
            'email' => 'doc_w7_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'doctor',
            'is_active' => true,
        ]);

        return Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
            'qualification' => 'TS.BS Chuyên Gia Sâu Wave 7',
            'consultation_fee' => 600000,
            'room_number' => 'W7.SPECIALTY',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Test Xạ Trị 4D-CT Linac & Nín Thở DIBH Bảo Vệ Tim OAR
     */
    public function test_01_radiation_oncology_4d_linac_dibh_and_oar_compliance(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'plan_code' => 'RAD-4D-TEST-' . rand(100, 999),
            'cancer_site' => 'Ung thư vú trái giai đoạn IIA',
            'radiation_technique' => 'vmat',
            'total_prescribed_dose_gy' => 50.0,
            'fraction_count' => 25,
            'dose_per_fraction_gy' => 2.0,
            'dibh_breath_hold_used' => 1,
            'four_d_ct_motion_tracked' => 1,
            'heart_mean_dose_gy' => 2.1,
            'spinal_cord_max_dose_gy' => 18.0,
            'treatment_status' => 'quality_assurance_passed',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.oncology-advanced.radiation-plan'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('radiation_oncology_plans', [
            'plan_code' => $payload['plan_code'],
            'radiation_technique' => 'vmat',
            'dibh_breath_hold_used' => true,
            'oar_tolerance_verified' => true,
        ]);
    }

    /**
     * 2. Test Liệu Pháp Tế Bào CAR-T, Phân Độ Bão Cytokine CRS theo ASTCT & Chỉ Định Tocilizumab
     */
    public function test_02_car_t_cell_therapy_crs_grading_and_tocilizumab(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'cart_product_name' => 'Tisagenlecleucel (Kymriah)',
            'target_antigen' => 'CD19',
            'infusion_date' => now()->toDateString(),
            'peak_temperature_c' => 39.8,
            'systolic_bp' => 85.0,
            'spo2_pct' => 91.0,
            'needs_vasopressor' => 1,
            'needs_ventilator' => 0,
            'icans_neurotoxicity_grade' => 'grade_2',
            'tocilizumab_doses_given' => 1,
            'dexamethasone_given' => 0,
            'therapy_outcome' => 'monitoring',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.oncology-advanced.cart'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('crs.grade', 'grade_3_vasopressors_high_o2');
        $response->assertJsonPath('crs.tocilizumab', true);

        $this->assertDatabaseHas('car_t_cell_therapies', [
            'patient_id' => $patient->id,
            'target_antigen' => 'CD19',
            'astct_crs_grade' => 'grade_3_vasopressors_high_o2',
            'tocilizumab_indicated' => true,
        ]);
    }

    /**
     * 3. Test Hội Đồng Ung Thư Phân Tử MTB Đột Biến Gen NGS & Thuốc Trúng Đích
     */
    public function test_03_molecular_tumor_board_actionable_mutations_consensus(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'chairperson_doctor_id' => $doctor->id,
            'board_session_code' => 'MTB-W7-' . rand(100, 999),
            'primary_cancer_diagnosis' => 'Ung thư phổi không tế bào nhỏ NSCLC',
            'actionable_mutations_json' => ['EGFR Exon 19 Deletion', 'ALK Rearrangement negative'],
            'tumor_mutational_burden_tmb' => '14.5 mut/Mb (High)',
            'msi_status' => 'msi_high',
            'off_label_or_trial_therapies_proposed' => 'Osimertinib kết hợp Pembrolizumab theo kết quả NGS',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.oncology-advanced.mtb'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('recommendation.decision', 'unanimous_approved');

        $this->assertDatabaseHas('molecular_tumor_boards', [
            'patient_id' => $patient->id,
            'msi_status' => 'msi_high',
            'mtb_consensus_decision' => 'unanimous_approved',
        ]);
    }

    /**
     * 4. Test Phẫu Thuật Sọ Não Khi Tỉnh Thức (Awake Craniotomy) Kích Thích Điện Vỏ Não
     */
    public function test_04_awake_craniotomy_direct_cortical_stimulation_and_speech(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'neurosurgeon_id' => $doctor->id,
            'tumor_eloquent_location' => 'Vùng Broca bán cầu ưu thế trái (Thùy trán)',
            'anesthesia_technique' => 'asleep_awake_asleep_aaa',
            'cortical_stimulation_current_ma' => 3.5,
            'speech_arrest_detected' => 1,
            'motor_twitching_detected' => 0,
            'safety_margin_to_eloquent_cortex_mm' => 6.0,
            'extent_of_resection_pct' => 92.5,
            'intraoperative_seizure_occurred' => 0,
            'postop_neurological_status' => 'Ngôn ngữ thông suốt, không yếu liệt vận động',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.oncology-advanced.awake-craniotomy'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('boundary.stop_resection_alert', true);

        $this->assertDatabaseHas('awake_craniotomy_mappings', [
            'patient_id' => $patient->id,
            'speech_arrest_detected' => true,
            'anesthesia_technique' => 'asleep_awake_asleep_aaa',
        ]);
    }

    /**
     * 5. Test Dược Động Học Cá Thể Hóa Bayesian PK/PD Kháng Sinh
     */
    public function test_05_precision_antibiotic_bayesian_pkpd_auc_mic_target(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'antibiotic_name' => 'vancomycin',
            'pathogen_name' => 'Staphylococcus aureus kháng Methicillin (MRSA)',
            'mic_mg_per_l' => 1.0,
            'current_serum_trough_mg_per_l' => 17.5,
            'current_serum_peak_mg_per_l' => 34.0,
            'estimated_auc24_mg_h_per_l' => 480.0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.oncology-advanced.pkpd'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('pkpd.achieved', true);

        $this->assertDatabaseHas('precision_antibiotic_pk_pds', [
            'patient_id' => $patient->id,
            'antibiotic_name' => 'vancomycin',
            'auc_target_achieved' => true,
        ]);
    }

    /**
     * 6. Test Tuần Hoàn Ngoài Cơ Thể ECMO, Chênh Áp Màng Delta P & Hạ Thân Nhiệt TTM
     */
    public function test_06_ecmo_membrane_pressure_safety_and_ttm_target(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'ecmo_modality' => 'va_ecmo_venoarterial',
            'pump_flow_l_per_min' => 4.2,
            'pump_speed_rpm' => 3200,
            'sweep_gas_flow_l_per_min' => 3.5,
            'fio2_sweep_pct' => 100.0,
            'delta_pressure_membrane_mmhg' => 38.0,
            'ttm_targeted_temperature_active' => 1,
            'target_core_temp_c' => 33.5,
            'actual_core_temp_c' => 33.6,
            'session_status' => 'running',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.ecmo'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('membrane.thrombosis_risk', false);

        $this->assertDatabaseHas('ecmo_ttm_sessions', [
            'patient_id' => $patient->id,
            'ecmo_modality' => 'va_ecmo_venoarterial',
            'membrane_thrombosis_risk' => false,
            'ttm_targeted_temperature_active' => true,
        ]);
    }

    /**
     * 7. Test Buồng Cách Ly Áp Lực Âm AIIR Chuẩn CDC / WHO
     */
    public function test_07_negative_pressure_aiir_cdc_pressure_and_ach_compliance(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $payload = [
            'room_code' => 'AIIR-ICU-W7-' . rand(100, 999),
            'patient_id' => $patient->id,
            'pathogen_isolation_type' => 'Lao đa kháng thuốc (MDR-TB) & SARS-CoV-2',
            'pressure_differential_pa' => -8.5,
            'air_changes_per_hour_ach' => 14.0,
            'hepa_filter_exhaust_passed' => 1,
            'airlock_doors_interlocked' => 1,
            'uvc_disinfection_active' => 1,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.aiir'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('compliance.compliant', true);

        $this->assertDatabaseHas('negative_pressure_aiirs', [
            'room_code' => $payload['room_code'],
            'pressure_failure_alarm' => false,
        ]);
    }

    /**
     * 8. Test Thử Nghiệm Thở Tự Nhiên SBT, Chỉ Số RSBI < 105 & Đánh Giá Rút Ống Nội Khí Quản
     */
    public function test_08_spontaneous_breathing_trial_rsbi_and_extubation_readiness(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'sbt_duration_minutes' => 45,
            'respiratory_rate_f_bpm' => 20.0,
            'tidal_volume_vt_liters' => 0.42,
            'maximal_inspiratory_pressure_mip' => -28.0,
            'cuff_leak_test_positive' => 1,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.sbt'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        // RSBI = 20 / 0.42 = 47.62 (< 105) => Khả năng rút ống thành công cao
        $response->assertJsonPath('readiness.ready', true);

        $this->assertDatabaseHas('extubation_readiness_sbts', [
            'patient_id' => $patient->id,
            'extubation_successful_no_reintubation_48h' => true,
        ]);
    }

    /**
     * 9. Test Phân Loại Đa Chấn Thương Nặng Theo ISS & Kích Hoạt Báo Động Trauma Team Level 1
     */
    public function test_09_trauma_resuscitation_iss_and_level_1_activation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'trauma_leader_id' => $doctor->id,
            'ais_head_neck' => 3,
            'ais_face' => 1,
            'ais_chest' => 4,
            'ais_abdomen_pelvis' => 3,
            'ais_extremities_pelvic_girdle' => 2,
            'ais_external_burns' => 1,
            'revised_trauma_score_rts' => 6.8,
        ];

        // 3 vùng cao nhất: Chest (4^2=16), Head/Neck (3^2=9), Abdomen (3^2=9) => ISS = 16 + 9 + 9 = 34
        // ISS >= 16 => Kích hoạt Trauma Level 1
        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.trauma-iss'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('iss.iss', 34);
        $response->assertJsonPath('iss.level_1', true);

        $this->assertDatabaseHas('trauma_resuscitation_isses', [
            'patient_id' => $patient->id,
            'calculated_iss_score' => 34,
            'trauma_level_1_activated' => true,
        ]);
    }

    /**
     * 10. Test Lọc Máu Liên Tục CRRT Chống Đông Citrate Cục Bộ RCA & Duy Trì Canxi Sau Màng
     */
    public function test_10_crrt_citrate_anticoagulation_post_filter_calcium_safety(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'crrt_modality' => 'cvvhdf_hemodiafiltration',
            'effluent_dose_ml_per_kg_per_h' => 28.5,
            'transmembrane_pressure_tmp_mmhg' => 110.0,
            'pre_filter_citrate_infusion_mmol_per_l' => 3.0,
            'post_filter_ionized_calcium_mmol_per_l' => 0.31,
            'systemic_ionized_calcium_mmol_per_l' => 1.18,
            'calcium_chloride_compensation_rate_ml_per_h' => 45.0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.crrt'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('safety.safe', true);

        $this->assertDatabaseHas('crrt_citrate_anticoagulations', [
            'patient_id' => $patient->id,
            'post_filter_ionized_calcium_mmol_per_l' => 0.31,
            'citrate_accumulation_acidosis_alert' => false,
        ]);
    }

    /**
     * 11. Test Gói Can Thiệp ICU ABCDEF Bundle & Phòng Ngừa Suy Yếu PICS
     */
    public function test_11_icu_abcdef_bundle_compliance_and_pics_prevention(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'bundle_a_pain_assessed_controlled' => 1,
            'bundle_b_both_sat_sbt_performed' => 1,
            'bundle_c_sedative_choice' => 'Dexmedetomidine đích RASS -1 đến 0 (Không dùng Benzodiazepine)',
            'bundle_d_cam_icu_delirium' => 'negative_clear',
            'bundle_e_early_mobility_level' => 'level_3_standing_transfer',
            'bundle_f_family_engagement_diary_active' => 1,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.critical-extracorporeal.bundle'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('evaluation.compliant_elements', '6/6');

        $this->assertDatabaseHas('icu_abcdef_bundle_pics', [
            'patient_id' => $patient->id,
            'bundle_d_cam_icu_delirium' => 'negative_clear',
        ]);
    }

    /**
     * 12. Test Nội Soi Can Thiệp Tiêu Hóa Nâng Cao ESD, Cắt Trọn Khối En-Bloc R0
     */
    public function test_12_advanced_endoscopy_esd_en_bloc_r0_curative_resection(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'endoscopist_id' => $doctor->id,
            'procedure_category' => 'esd_endoscopic_submucosal_dissection',
            'lesion_organ_location' => 'Góc bờ cong nhỏ dạ dày',
            'lesion_size_mm' => 32.0,
            'en_bloc_resection_achieved' => 1,
            'r0_clear_margins_confirmed' => 1,
            'asge_difficulty_grade' => 'grade_3_expert',
            'prophylactic_pancreatic_stent_placed' => 0,
            'rectal_indomethacin_administered' => 1,
            'perforation_or_bleeding_occurred' => 0,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.advanced-intervention.endoscopy'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('quality.success', true);

        $this->assertDatabaseHas('advanced_endoscopy_procedures', [
            'patient_id' => $patient->id,
            'en_bloc_resection_achieved' => true,
            'r0_clear_margins_confirmed' => true,
        ]);
    }

    /**
     * 13. Test Giám Sát Thiết Bị Tim Mạch Cấy Ghép CIED Từ Xa Phát Hiện Bão Điện Học VT/VF
     */
    public function test_13_cied_remote_monitoring_electrical_storm_and_fracture(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'cardiologist_id' => $doctor->id,
            'device_type' => 'icd_implantable_defibrillator',
            'device_serial_number' => 'CIED-W7-' . rand(10000, 99999),
            'battery_longevity_remaining_years' => 6.2,
            'lead_impedance_ohms' => 2450.0, // > 2000 => Lead fracture alert
            'ventricular_tachycardia_episodes_30d' => 4, // >= 3 => Electrical storm
            'shocks_delivered_appropriate' => 3,
            'shocks_delivered_inappropriate' => 1,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.advanced-intervention.cied'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('analysis.electrical_storm', true);
        $response->assertJsonPath('analysis.lead_fracture', true);

        $this->assertDatabaseHas('cied_remote_monitorings', [
            'patient_id' => $patient->id,
            'electrical_storm_alert' => true,
            'lead_fracture_risk' => true,
        ]);
    }

    /**
     * 14. Test Bản Đồ Radar Nhạy Cảm Kháng Sinh Antibiogram CLSI M39 Phát Hiện Ổ Dịch
     */
    public function test_14_antibiogram_hai_surveillance_clsi_outbreak_cluster(): void
    {
        $user = $this->createReceptionistUser();

        $payload = [
            'department_name' => 'ICU Hồi Sức Tích Cực',
            'pathogen_name' => 'Acinetobacter baumannii kháng Carbapenem (CRAB)',
            'antibiotic_tested' => 'Colistin',
            'total_isolates_tested' => 35,
            'susceptibility_pct' => 18.5, // < 20% + XDR => Outbreak Cluster Alert
            'resistance_classification' => 'xdr_extensively_drug_resistant',
            'hai_infection_site' => 'Viêm phổi thở máy VAP',
        ];

        $response = $this->actingAs($user)->postJson(route('reception.advanced-intervention.antibiogram'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('risk.outbreak_alert', true);

        $this->assertDatabaseHas('antibiogram_hai_surveillances', [
            'pathogen_name' => $payload['pathogen_name'],
            'outbreak_cluster_alert' => true,
        ]);
    }

    /**
     * 15. Test Robot Khung Xương Ngoài Phục Hồi Chức Năng Dáng Đi Thần Kinh
     */
    public function test_15_robotic_exoskeleton_rehab_gait_symmetry_evaluation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        $payload = [
            'patient_id' => $patient->id,
            'therapist_id' => $doctor->id,
            'exoskeleton_device_model' => 'EksoNR Robotic Exoskeleton 2026',
            'session_duration_minutes' => 50,
            'total_steps_achieved' => 1120,
            'distance_meters' => 480.0,
            'assistance_level_pct' => 35,
            'hip_rom_degrees' => 34.0,
            'knee_rom_degrees' => 58.0,
            'gait_symmetry_index_pct' => 91.5,
        ];

        $response = $this->actingAs($user)->postJson(route('reception.advanced-intervention.exoskeleton'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('gait.symmetry_acceptable', true);

        $this->assertDatabaseHas('robotic_exoskeleton_rehabs', [
            'patient_id' => $patient->id,
            'total_steps_achieved' => 1120,
            'gait_symmetry_index_pct' => 91.5,
        ]);
    }

    /**
     * 16. Test Render Giao Diện View Oncology Advanced Dashboard
     */
    public function test_16_oncology_advanced_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.oncology-advanced.index'));

        $response->assertStatus(200);
        $response->assertSee('Ung Thư Chính Xác', false);
        $response->assertSee('Xạ Trị 4D', false);
        $response->assertSee('CAR-T', false);
    }

    /**
     * 17. Test Render Giao Diện View Critical Extracorporeal Dashboard
     */
    public function test_17_critical_extracorporeal_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.critical-extracorporeal.index'));

        $response->assertStatus(200);
        $response->assertSee('Hồi Sức Tuần Hoàn Sống Còn', false);
        $response->assertSee('ECMO', false);
        $response->assertSee('Buồng Áp Lực Âm AIIR', false);
    }

    /**
     * 18. Test Render Giao Diện View Advanced Intervention Dashboard
     */
    public function test_18_advanced_intervention_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.advanced-intervention.index'));

        $response->assertStatus(200);
        $response->assertSee('Can Thiệp Chuyên Sâu', false);
        $response->assertSee('Nội Soi Can Thiệp ESD', false);
        $response->assertSee('Giám Sát Từ Xa CIED', false);
    }

    /**
     * 19. Test Business Logic Model Unit: Tự Động Phân Tích & Phân Độ Y Khoa
     */
    public function test_19_model_business_logics_and_calculations(): void
    {
        // 1. CAR-T ASTCT
        $crs = CarTCellTherapy::gradeCrs(39.5, 80, 92, true, false);
        $this->assertEquals('grade_3_vasopressors_high_o2', $crs['grade']);
        $this->assertTrue($crs['tocilizumab']);

        // 2. ECMO Delta P
        $ecmoRisk = EcmoTtmSession::evaluateMembraneSafety(125.0);
        $this->assertTrue($ecmoRisk['thrombosis_risk']);

        // 3. AIIR CDC Compliance
        $aiirStatus = NegativePressureAiir::evaluateAiirCompliance(-5.0, 14.0);
        $this->assertTrue($aiirStatus['compliant']);

        // 4. SBT RSBI
        $sbtEval = ExtubationReadinessSbt::evaluateExtubationReadiness(22.0, 0.44, -25.0, true);
        $this->assertEquals(50.0, $sbtEval['rsbi']);
        $this->assertTrue($sbtEval['ready']);

        // 5. Trauma ISS
        $issEval = TraumaResuscitationIss::calculateIss([3, 1, 4, 3, 2, 1]);
        $this->assertEquals(34, $issEval['iss']);
        $this->assertTrue($issEval['level_1']);

        // 6. CRRT Citrate Safety
        $crrtSafety = CrrtCitrateAnticoagulation::evaluateCitrateSafety(0.30, 1.15, 120.0);
        $this->assertTrue($crrtSafety['safe']);

        // 7. ICU ABCDEF Bundle Compliance
        $bundleEval = IcuAbcdefBundlePics::evaluateBundleCompliance(true, true, 'Dexmedetomidine', 'negative_clear', 'level_3_standing_transfer', true);
        $this->assertEquals('6/6', $bundleEval['compliant_elements']);
        $this->assertEquals(10.0, $bundleEval['pics_risk_pct']);

        // 8. CIED Analysis
        $ciedEval = CiedRemoteMonitoring::analyzeCiedTransmission(4, 0, 480, 5.0);
        $this->assertTrue($ciedEval['electrical_storm']);
        $this->assertFalse($ciedEval['lead_fracture']);

        // 9. Antibiogram Risk
        $antiEval = AntibiogramHaiSurveillance::evaluateResistanceRisk('xdr_extensively_drug_resistant', 15.0, 30);
        $this->assertTrue($antiEval['outbreak_alert']);
    }
}
