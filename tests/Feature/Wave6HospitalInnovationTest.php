<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\SmartInfusionPumpDers;
use App\Models\IvYsiteIncompatibility;
use App\Models\MassiveTransfusionProtocol;
use App\Models\PatientBloodManagement;
use App\Models\OrganTransplantCoordination;
use App\Models\RoboticSurgerySession;
use App\Models\HybridOrNavigationPlan;
use App\Models\TeleEmsMission;
use App\Models\MassCasualtyTriage;
use App\Models\FetalMonitoringCtg;
use App\Models\IvfEmbryoCycle;
use App\Models\PediatricEarlyWarningScore;
use App\Models\CoronaryPhysiologyImaging;
use App\Models\ExtendedStrokePerfusion;
use App\Models\OncogeneticsNiptRecord;
use App\Models\BiobankStemCellUnit;
use App\Models\BedCapacityCommand;
use App\Models\HospitalAccreditationKpi;
use App\Models\OrphanAntidoteRegistry;
use App\Models\VirtualWardPatient;
use Tests\TestCase;

class Wave6HospitalInnovationTest extends TestCase
{
    private function createReceptionistUser(): User
    {
        return User::factory()->create([
            'email' => 'rec_w6_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    private function createPatient(): PatientProfile
    {
        return PatientProfile::create([
            'patient_code' => 'BN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)) . rand(100, 999),
            'full_name' => 'Bệnh Nhân Test Wave 6 ' . rand(100, 999),
            'phone' => '0988' . rand(100000, 999999),
            'dob' => '1988-06-15',
            'gender' => 'female',
            'identity_card' => '001088' . rand(100000, 999999),
            'address' => 'Hà Nội',
        ]);
    }

    private function createDoctor(): Doctor
    {
        $specialty = Specialty::firstOrCreate(
            ['slug' => 'hoi-suc-wave-6'],
            ['name' => 'Hồi Sức Cấp Cứu Wave 6', 'description' => 'Khoa Hồi sức tích cực và chống độc BV Đa Khoa Tâm Anh']
        );

        $user = User::factory()->create([
            'email' => 'doc_w6_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'doctor',
            'is_active' => true,
        ]);

        return Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
            'qualification' => 'ThS.BS Chuyên Khoa Sâu Wave 6',
            'consultation_fee' => 500000,
            'room_number' => 'ICU.W6',
            'is_active' => true,
        ]);
    }

    // 1. Smart Infusion Pump DERS Guardrails
    public function test_01_smart_infusion_pump_ders_guardrail_evaluation_and_limits(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        // Test static logic
        $evalNormal = SmartInfusionPumpDers::evaluateDersGuardrail(0.2, 0.5, 1.0);
        $this->assertFalse($evalNormal['soft_limit_exceeded']);
        $this->assertFalse($evalNormal['hard_limit_violation']);

        $evalHardViolation = SmartInfusionPumpDers::evaluateDersGuardrail(1.5, 0.5, 1.0);
        $this->assertTrue($evalHardViolation['hard_limit_violation']);

        $rate = SmartInfusionPumpDers::calculateInfusionRateMlHr(0.2, 70.0, 1.0);
        $this->assertEquals(0.84, $rate);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation.ders'), [
            'patient_id' => $patient->id,
            'pump_device_code' => 'PUMP-ICU-01',
            'care_unit_profile' => 'icu_adult',
            'medication_name' => 'Norepinephrine',
            'concentration_unit' => '4mg/50mL',
            'dose_rate' => 0.25,
            'dose_unit' => 'mcg/kg/min',
            'patient_weight_kg' => 65.0,
            'lower_soft_limit' => 0.05,
            'upper_soft_limit' => 0.5,
            'upper_hard_limit' => 1.0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('smart_infusion_pump_ders', [
            'patient_id' => $patient->id,
            'medication_name' => 'Norepinephrine',
            'hard_limit_violation' => false,
        ]);
    }

    // 2. IV Y-Site Incompatibility Engine
    public function test_02_iv_ysite_incompatibility_matrix_detection(): void
    {
        $user = $this->createReceptionistUser();

        // Test static logic
        $checkPrecipitate = IvYsiteIncompatibility::checkCompatibility('Furosemide', 'Midazolam');
        $this->assertEquals('precipitate_formation', $checkPrecipitate['type']);

        $checkSafe = IvYsiteIncompatibility::checkCompatibility('Morphine', 'Ondansetron');
        $this->assertEquals('compatible', $checkSafe['type']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation.ysite-check'), [
            'primary_drug_name' => 'Ciprofloxacin',
            'secondary_drug_name' => 'Heparin',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('iv_ysite_incompatibilities', [
            'primary_drug_name' => 'Ciprofloxacin',
            'secondary_drug_name' => 'Heparin',
            'incompatibility_type' => 'precipitate_formation',
        ]);
    }

    // 3. Massive Transfusion Protocol (MTP) & TEG/ROTEM
    public function test_03_massive_transfusion_protocol_and_teg_analysis(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $balanced = MassiveTransfusionProtocol::evaluateTransfusionRatio(4, 4, 1);
        $this->assertTrue($balanced['balanced']);

        $teg = MassiveTransfusionProtocol::analyzeTegRotem(12.0, 45.0, 8.0);
        $this->assertStringContainsString('Thiếu hụt yếu tố đông máu', $teg['recommendation']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation.mtp'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'protocol_code' => 'MTP-TRAUMA-W6-01',
            'rbc_units_issued' => 6,
            'ffp_units_issued' => 6,
            'platelet_doses_issued' => 1,
            'cryoprecipitate_units' => 10,
            'tranexamic_acid_administered' => true,
            'teg_r_time_minutes' => 11.5,
            'teg_maximum_amplitude_mm' => 52.0,
            'mtp_status' => 'completed_hemostasis',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('massive_transfusion_protocols', [
            'patient_id' => $patient->id,
            'protocol_code' => 'MTP-TRAUMA-W6-01',
            'balanced_ratio_achieved' => true,
            'target_hemostasis_reached' => true,
        ]);
    }

    // 4. Patient Blood Management (PBM) & Cell Saver
    public function test_04_patient_blood_management_and_cell_saver_salvage(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $anemia = PatientBloodManagement::detectPreopAnemia(10.5, 25.0);
        $this->assertTrue($anemia['is_anemic']);
        $this->assertEquals('Thiếu máu thiếu sắt tiền phẫu', $anemia['anemia_type']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.critical-resuscitation.pbm'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'preop_hemoglobin_g_dl' => 10.5,
            'preop_ferritin_ng_ml' => 25.0,
            'tranexamic_acid_used' => true,
            'cell_saver_used' => true,
            'autologous_blood_salvaged_ml' => 500.0,
            'postop_hemoglobin_g_dl' => 11.2,
            'allogeneic_transfusion_units' => 0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('patient_blood_managements', [
            'patient_id' => $patient->id,
            'preop_anemia_detected' => true,
            'cell_saver_used' => true,
            'allogeneic_transfusion_units' => 0,
        ]);
    }

    // 5. Organ Transplant Coordination & HLA Matching
    public function test_05_organ_transplant_hla_matching_and_cold_ischemia(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $hla = OrganTransplantCoordination::evaluateHlaCompatibility(1, 'negative');
        $this->assertEquals('Khớp kháng nguyên HLA rất tốt', $hla['compatibility_tier']);

        $coldKidney = OrganTransplantCoordination::evaluateColdIschemiaLimit('kidney', 18.0);
        $this->assertTrue($coldKidney['safe']);

        $coldHeart = OrganTransplantCoordination::evaluateColdIschemiaLimit('heart', 5.0);
        $this->assertFalse($coldHeart['safe']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.transplant-robotics.transplant'), [
            'patient_id' => $patient->id,
            'lead_surgeon_id' => $doctor->id,
            'organ_type' => 'kidney',
            'donor_type' => 'brain_dead_deceased',
            'donor_code' => 'DONOR-BD-W6-01',
            'hla_mismatch_count' => 1,
            'crossmatch_result' => 'negative',
            'cold_ischemia_time_hours' => 14.5,
            'graft_status_day30' => 'functional_good',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('organ_transplant_coordinations', [
            'patient_id' => $patient->id,
            'organ_type' => 'kidney',
            'graft_status_day30' => 'functional_good',
        ]);
    }

    // 6. Robotic Surgery & EndoWrist Lifecycle
    public function test_06_robotic_surgery_session_and_dowrist_instrument_lifespan(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $checkLife = RoboticSurgerySession::checkInstrumentSafety(1);
        $this->assertTrue($checkLife['alert']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.transplant-robotics.robotic'), [
            'patient_id' => $patient->id,
            'surgeon_id' => $doctor->id,
            'robot_system_serial' => 'DAVINCI-XI-W6',
            'surgical_procedure_name' => 'Cắt bán phần thận có hỗ trợ Robot Da Vinci',
            'console_time_minutes' => 95,
            'instrument_lifespan_remaining' => 1,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('robotic_surgery_sessions', [
            'patient_id' => $patient->id,
            'robot_system_serial' => 'DAVINCI-XI-W6',
            'instrument_retirement_alert' => true,
        ]);
    }

    // 7. Hybrid OR & Surgical Navigation Planning
    public function test_07_hybrid_or_navigation_tre_accuracy_and_cin_risk(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $tre = HybridOrNavigationPlan::assessTreAccuracy(0.8);
        $this->assertTrue($tre['accurate']);

        $cin = HybridOrNavigationPlan::calculateCinRisk(120.0, 40.0);
        $this->assertEquals('high_cin_risk', $cin['risk_level']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.transplant-robotics.hybrid-or'), [
            'patient_id' => $patient->id,
            'primary_operator_id' => $doctor->id,
            'procedure_type' => 'tavi_tavr',
            'navigation_modality' => 'stealth_station_optical',
            'target_registration_error_mm' => 0.8,
            'contrast_media_volume_ml' => 60.0,
            'patient_baseline_egfr' => 65.0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('hybrid_or_navigation_plans', [
            'patient_id' => $patient->id,
            'procedure_type' => 'tavi_tavr',
            'contrast_induced_nephropathy_risk' => 'low_cin_risk',
        ]);
    }

    // 8. Tele-EMS 5G Mobile Pre-hospital Triage
    public function test_08_tele_ems_5g_pre_hospital_triage_and_cathlab_activation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        // Test static logic
        $preAct = TeleEmsMission::evaluatePreActivation('cathlab_stemi', 12.0);
        $this->assertTrue($preAct['critical_urgency']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.tele-ems'), [
            'patient_id' => $patient->id,
            'ambulance_callsign' => 'AMB-5G-W6-99',
            'paramedic_in_charge' => 'BS. Nguyễn Văn Hưng',
            'eta_hospital_minutes' => 10.0,
            'heart_rate' => 88,
            'sbp' => 135,
            'dbp' => 85,
            'spo2' => 97,
            'ecg_12lead_finding' => 'STEMI thành trước rộng',
            'pre_activation_target' => 'cathlab_stemi',
            'mission_status' => 'transporting',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('tele_ems_missions', [
            'ambulance_callsign' => 'AMB-5G-W6-99',
            'pre_activation_target' => 'cathlab_stemi',
        ]);
    }

    // 9. Mass Casualty Incident (MCI) & START Triage
    public function test_09_mass_casualty_start_triage_algorithm_zones(): void
    {
        $user = $this->createReceptionistUser();

        // Test static logic
        $triageGreen = MassCasualtyTriage::calculateStartTriage(true, 18, true, 'follows_commands');
        $this->assertEquals('green_minor', $triageGreen['color']);

        $triageRed = MassCasualtyTriage::calculateStartTriage(false, 34, true, 'follows_commands');
        $this->assertEquals('red_immediate', $triageRed['color']);

        $triageBlack = MassCasualtyTriage::calculateStartTriage(false, 0, false, 'does_not_follow');
        $this->assertEquals('black_deceased', $triageBlack['color']);

        // Test HTTP Request
        $tagCode = 'VIC-W6-' . uniqid();
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.mci-triage'), [
            'incident_name' => 'Vụ Cháy Chung Cư Cao Tầng',
            'disaster_code' => 'MCI-W6-FIRE',
            'victim_tag_number' => $tagCode,
            'can_walk' => false,
            'respiratory_rate' => 32,
            'radial_pulse_palpable' => true,
            'mental_status' => 'follows_commands',
            'triaged_by' => 'Kíp Cứu Nạn BV Tâm Anh',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('mass_casualty_triages', [
            'victim_tag_number' => $tagCode,
            'start_triage_color' => 'red_immediate',
        ]);
    }

    // 10. Centralized Fetal Monitoring & AI Cardiotocography (CTG)
    public function test_10_centralized_fetal_monitoring_ctg_figo_and_acog_classification(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $ctgNormal = FetalMonitoringCtg::classifyCtgTraces(140, 'moderate_normal', 'none', false, 3);
        $this->assertEquals('normal', $ctgNormal['figo']);
        $this->assertEquals('category_1_normal', $ctgNormal['acog']);

        $ctgPatho = FetalMonitoringCtg::classifyCtgTraces(90, 'absent', 'late_uteroplacental_hypoxia', false, 5);
        $this->assertEquals('pathological', $ctgPatho['figo']);
        $this->assertTrue($ctgPatho['emergency_alert']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.ctg'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'delivery_room_bed' => 'Phòng Sinh VIP 01',
            'baseline_fhr_bpm' => 142,
            'variability' => 'moderate_normal',
            'decelerations_type' => 'none',
            'uterine_contractions_per_10min' => 3,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('fetal_monitoring_ctgs', [
            'patient_id' => $patient->id,
            'figo_classification' => 'normal',
        ]);
    }

    // 11. IVF Embryo Scoring & Electronic Witnessing
    public function test_11_ivf_embryo_gardner_score_and_electronic_witnessing(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $score4AA = IvfEmbryoCycle::calculateGardnerAiScore('4AA');
        $this->assertEquals(95.5, $score4AA);

        $witnessOk = IvfEmbryoCycle::verifyElectronicWitnessing('DISH-IVF-001', 'SPERM-IVF-001', 'DISH-IVF');
        $this->assertTrue($witnessOk);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.ivf'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'cycle_code' => 'IVF-W6-CYC-01',
            'partner_name' => 'Chồng Test Wave 6',
            'oocytes_retrieved' => 12,
            'oocytes_fertilized' => 10,
            'embryo_id_tag' => 'EMB-W6-4AA-01',
            'development_stage' => 'day5_blastocyst',
            'gardner_grade' => '4AA',
            'embryo_status' => 'cryopreserved',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('ivf_embryo_cycles', [
            'patient_id' => $patient->id,
            'gardner_grade' => '4AA',
            'ai_implantation_potential_score' => 95.5,
        ]);
    }

    // 12. Pediatric Early Warning Score (PEWS)
    public function test_12_pediatric_early_warning_score_pews_and_met_activation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $pewsLow = PediatricEarlyWarningScore::calculatePews(0, 0, 0);
        $this->assertEquals(0, $pewsLow['total_score']);
        $this->assertEquals('low_routine_monitor', $pewsLow['action_level']);

        $pewsHigh = PediatricEarlyWarningScore::calculatePews(2, 2, 2);
        $this->assertEquals(6, $pewsHigh['total_score']);
        $this->assertEquals('high_pediatric_met_activation', $pewsHigh['action_level']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.pews'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'age_group' => 'toddler_1_4y',
            'behavior_score' => 2,
            'cardiovascular_score' => 2,
            'respiratory_score' => 2,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('pediatric_early_warning_scores', [
            'patient_id' => $patient->id,
            'total_pews_score' => 6,
            'risk_action_level' => 'high_pediatric_met_activation',
        ]);
    }

    // 13. Coronary Physiology & Intravascular Imaging (FFR / IVUS / OCT)
    public function test_13_coronary_physiology_ffr_ifr_ivus_ischemia_decision(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $ischemic = CoronaryPhysiologyImaging::assessIschemiaSignificance('LAD', 0.76, 0.85, 3.2);
        $this->assertEquals('ischemic_revascularization_indicated', $ischemic['hemodynamic']);

        $nonIschemic = CoronaryPhysiologyImaging::assessIschemiaSignificance('LAD', 0.88, 0.94, 4.5);
        $this->assertEquals('non_ischemic_medical_therapy', $nonIschemic['hemodynamic']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.coronary'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'target_artery' => 'LAD',
            'ffr_ratio' => 0.74,
            'imaging_modality' => 'ivus',
            'minimal_lumen_area_mm2' => 3.0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('coronary_physiology_imagings', [
            'patient_id' => $patient->id,
            'target_artery' => 'LAD',
            'hemodynamic_significance' => 'ischemic_revascularization_indicated',
        ]);
    }

    // 14. Extended Stroke Thrombectomy Core-Penumbra Mismatch
    public function test_14_extended_stroke_thrombectomy_core_penumbra_mismatch(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $strokeEligible = ExtendedStrokePerfusion::evaluateExtendedWindow(10, 25.0, 95.0);
        $this->assertEquals('eligible_for_mechanical_thrombectomy', $strokeEligible['decision']);
        $this->assertTrue($strokeEligible['dawn_met']);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.emergency-maternity-cardio.stroke-extended'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'onset_to_arrival_hours' => 12,
            'ischemic_core_volume_ml' => 20.0,
            'hypoperfused_volume_ml' => 85.0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('extended_stroke_perfusions', [
            'patient_id' => $patient->id,
            'extended_window_decision' => 'eligible_for_mechanical_thrombectomy',
        ]);
    }

    // 15. Oncogenetics & Prenatal Genomics NIPT
    public function test_15_oncogenetics_targeted_therapy_matching_and_nipt_validation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $therapies = OncogeneticsNiptRecord::matchTargetedTherapies(['EGFR_L858R', 'BRCA1']);
        $this->assertCount(2, $therapies);

        $niptValid = OncogeneticsNiptRecord::validateNiptFetalFraction(6.5);
        $this->assertTrue($niptValid);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.genomics'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'test_category' => 'somatic_cancer_ngs',
            'sample_type' => 'Mô sinh thiết FFPE',
            'identified_variants' => ['EGFR_L858R'],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('oncogenetics_nipt_records', [
            'patient_id' => $patient->id,
            'test_category' => 'somatic_cancer_ngs',
        ]);
    }

    // 16. Biobank Registry & Stem Cell Unit
    public function test_16_biobank_stem_cell_viability_and_sterility_assessment(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        // Test static logic
        $evalBio = BiobankStemCellUnit::assessViabilityAndSterility(95.0, true);
        $this->assertTrue($evalBio['qualified_for_transplant']);
        $this->assertEquals('stored_cryo', $evalBio['status']);

        // Test HTTP Request
        $specimenCode = 'STEM-W6-' . uniqid();
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.biobank'), [
            'patient_id' => $patient->id,
            'specimen_code' => $specimenCode,
            'stem_cell_source' => 'umbilical_cord_blood',
            'total_nucleated_cells_10e8' => 15.0,
            'cd34_positive_count_10e6' => 6.2,
            'cell_viability_pct' => 96.0,
            'sterility_clearance_passed' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('biobank_stem_cell_units', [
            'specimen_code' => $specimenCode,
            'storage_status' => 'stored_cryo',
        ]);
    }

    // 17. Hospital Capacity Command Center & Bed Flow Predictor
    public function test_17_hospital_capacity_command_center_and_gridlock_prediction(): void
    {
        $user = $this->createReceptionistUser();

        // Test static logic
        $metrics = BedCapacityCommand::calculateCapacityMetrics(50, 48, 5, 2);
        $this->assertEquals(96.0, $metrics['occupancy_rate']);
        $this->assertEquals('gridlock_code_purple', $metrics['alert_status']);

        // Test HTTP Request
        $deptName = 'Khoa Hồi Sức Tích Cực Test ' . rand(100, 999);
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.bed-command'), [
            'department_name' => $deptName,
            'total_licensed_beds' => 50,
            'occupied_beds' => 48,
            'pending_admissions_er' => 5,
            'confirmed_discharges_today' => 2,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('bed_capacity_commands', [
            'department_name' => $deptName,
            'capacity_alert_status' => 'gridlock_code_purple',
        ]);
    }

    // 18. Automated Hospital Accreditation (83 Tiêu chí BYT 2.0 & JCI)
    public function test_18_automated_hospital_accreditation_kpi_evaluation_byt_jci(): void
    {
        $user = $this->createReceptionistUser();

        // Test static logic
        $kpi = HospitalAccreditationKpi::evaluateKpiCompliance(90.0, 95.0, true);
        $this->assertTrue($kpi['achieved']);
        $this->assertEquals('level_5_excellent', $kpi['level']);

        // Test HTTP Request
        $kpiCode = 'JCI-W6-' . uniqid();
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.kpi'), [
            'kpi_code' => $kpiCode,
            'kpi_title' => 'Tuân thủ danh sách kiểm an toàn phẫu thuật WHO',
            'domain_category' => 'An Toàn Phẫu Thuật',
            'benchmark_target' => 95.0,
            'actual_value' => 98.0,
            'metric_unit' => '%',
            'higher_is_better' => true,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('hospital_accreditation_kpis', [
            'kpi_code' => $kpiCode,
            'target_achieved' => true,
            'compliance_level' => 'level_4_good',
        ]);
    }

    // 19. Medical Toxicology & Orphan Antidote Registry
    public function test_19_medical_toxicology_orphan_antidote_registry_and_replenishment(): void
    {
        $user = $this->createReceptionistUser();

        // Test static logic
        $isLow = OrphanAntidoteRegistry::checkAntidoteStockStatus(2, 5);
        $this->assertTrue($isLow);

        $protocol = OrphanAntidoteRegistry::getFirstDoseProtocol('Intralipid 20%');
        $this->assertStringContainsString('Bolus 1.5 mL/kg', $protocol);

        // Test HTTP Request
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.antidote'), [
            'antidote_name' => 'Intralipid 20%',
            'indication_poison' => 'Ngộ độc thuốc tê toàn thân LAST',
            'stock_vials_available' => 2,
            'minimum_safety_threshold' => 5,
            'storage_location_cabinet' => 'Tủ Trực Cấp Cứu Wave 6',
            'earliest_expiry_date' => '2027-12-31',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('orphan_antidote_registries', [
            'antidote_name' => 'Intralipid 20%',
            'immediate_procurement_alert' => true,
        ]);
    }

    // 20. Hospital-at-Home (HAH) & Virtual Ward Monitoring
    public function test_20_hospital_at_home_virtual_ward_decompensation_detection(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $doctor = $this->createDoctor();

        // Test static logic
        $decomp = VirtualWardPatient::detectDecompensationRisk(2.5, 91.0, 160.0);
        $this->assertTrue($decomp['red_flag']);
        $this->assertStringContainsString('BÁO ĐỘNG ĐỎ', $decomp['summary']);

        // Test HTTP Request
        $bedCode = 'HAH-W6-BED-' . uniqid();
        $response = $this->actingAs($user)->postJson(route('reception.genomics-hospital-at-home.virtual-ward'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'virtual_bed_identifier' => $bedCode,
            'admission_diagnosis' => 'chf_decompensation_post_discharge',
            'virtual_admission_date' => '2026-10-30',
            'current_weight_kg' => 68.0,
            'weight_change_3days_kg' => 2.5,
            'latest_spo2_pct' => 91.0,
            'latest_sbp' => 140.0,
            'latest_dbp' => 85.0,
            'virtual_ward_status' => 'monitoring_active',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('virtual_ward_patients', [
            'virtual_bed_identifier' => $bedCode,
            'red_flag_alert_triggered' => true,
        ]);
    }

    // 21. Critical Resuscitation Dashboard View
    public function test_21_critical_resuscitation_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();
        $response = $this->actingAs($user)->get(route('reception.critical-resuscitation.index'));
        $response->assertStatus(200);
        $response->assertSee('Bơm Tiêm DERS Đang Chạy');
    }

    // 22. Transplant & Robotics Dashboard View
    public function test_22_transplant_robotics_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();
        $response = $this->actingAs($user)->get(route('reception.transplant-robotics.index'));
        $response->assertStatus(200);
        $response->assertSee('Ca Ghép Tạng Thực Hiện');
    }

    // 23. Emergency, Maternity & Cardio Dashboard View
    public function test_23_emergency_maternity_cardio_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();
        $response = $this->actingAs($user)->get(route('reception.emergency-maternity-cardio.index'));
        $response->assertStatus(200);
        $response->assertSee('Tele-EMS 5G Đang Di Chuyển');
    }

    // 24. Genomics & Hospital-at-Home Dashboard View
    public function test_24_genomics_hospital_at_home_dashboard_view_renders_successfully(): void
    {
        $user = $this->createReceptionistUser();
        $response = $this->actingAs($user)->get(route('reception.genomics-hospital-at-home.index'));
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo Di Truyền / NIPT');
    }
}
