<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\SmartInpatientRoomConsole;
use App\Models\DynamicOpdRoomAllocator;
use App\Models\TerminalRoomDisinfection;
use App\Models\SmartDeliverySuiteLdrp;
use App\Models\SmartNurseStationHub;
use App\Models\ProtectiveCleanroomPe;
use App\Models\LeadLinedRadiologySafety;
use App\Models\PathologyMortuaryStorage;
use App\Models\EndoscopySuiteTurnover;
use App\Models\CleanroomPharmacyCompounding;
use App\Models\BiohazardDirtyUtilitySluice;
use App\Models\SterileAreaAccessInterlock;
use App\Models\IndoorAirQualityHvac;
use App\Models\MedicalRecordsPhysicalArchive;
use App\Models\DayCareInfusionLounge;
use Tests\TestCase;

class Wave9SmartFacilityRoomTest extends TestCase
{
    private function createReceptionistUser(): User
    {
        return User::factory()->create([
            'email' => 'rec_w9_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    private function createPatient(): PatientProfile
    {
        return PatientProfile::create([
            'patient_code' => 'BN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)) . rand(100, 999),
            'full_name' => 'Bệnh Nhân Wave 9 ' . rand(100, 999),
            'phone' => '0977' . rand(100000, 999999),
            'dob' => '1992-05-15',
            'gender' => 'female',
            'identity_card' => '001092' . rand(100000, 999999),
            'address' => 'Hà Nội',
        ]);
    }

    // 1. Kiểm tra trang Index Smart Inpatient Ward Facility
    public function test_smart_inpatient_ward_facility_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.smart-inpatient-ward-facility.index'));

        $response->assertStatus(200);
        $response->assertSee('Quản Trị Khoa Phòng, Buồng Bệnh Nội Trú Thông Minh & Trạm Điều Dưỡng');
        $response->assertSee('Buồng Bệnh Thông Minh IoT');
    }

    // 2. Buồng bệnh thông minh IoT & chuông gọi điều dưỡng
    public function test_store_inpatient_console_and_nurse_call_workflow(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $response = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.console'), [
            'room_number' => 'VIP-901',
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'room_class' => 'vip_suite',
            'temperature_celsius' => 23.5,
            'circadian_lighting_mode' => 'evening_relax',
            'curtain_status' => 'open',
            'nurse_call_type' => 'none',
            'patient_pain_score' => 0,
            'dietary_order' => 'Cơm dưỡng bệnh sau mổ',
            'iot_device_status' => 'online',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('smart_inpatient_room_consoles', [
            'room_number' => 'VIP-901',
            'room_class' => 'vip_suite',
        ]);

        $console = SmartInpatientRoomConsole::where('room_number', 'VIP-901')->first();
        $this->assertNotNull($console);

        // Kích hoạt chuông gọi y tá khẩn cấp
        $callResponse = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.nurse-call', $console->id), [
            'nurse_call_type' => 'pain_medication',
            'patient_pain_score' => 7,
        ]);

        $callResponse->assertRedirect();
        $console->refresh();
        $this->assertEquals('pending', $console->nurse_call_status);
        $this->assertEquals('pain_medication', $console->nurse_call_type);
        $this->assertEquals(7, $console->patient_pain_score);

        // Hoàn tất giải quyết cuộc gọi
        $console->resolveNurseCall();
        $this->assertEquals('resolved', $console->nurse_call_status);
        $this->assertEquals('none', $console->nurse_call_type);
    }

    // 3. Phân bổ phòng khám ngoại trú & đồng bộ E-Paper
    public function test_store_opd_room_allocator_and_advance_queue(): void
    {
        $user = $this->createReceptionistUser();
        $roomCode = 'OPD-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.opd-allocator'), [
            'room_code' => $roomCode,
            'department_name' => 'Khoa Hô Hấp',
            'assigned_doctor_name' => 'BS. Trần Văn K',
            'doctor_specialty' => 'Chuyên gia Phổi & Dị Ứng',
            'shift_session' => 'morning_shift',
            'epaper_display_status' => 'active_consulting',
            'current_queue_number' => 10,
            'next_queue_number' => 11,
            'waiting_patient_count' => 6,
            'average_consult_time_min' => 15,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dynamic_opd_room_allocators', [
            'room_code' => $roomCode,
            'current_queue_number' => 10,
        ]);

        $allocator = DynamicOpdRoomAllocator::where('room_code', $roomCode)->first();
        $this->assertNotNull($allocator);

        // Gọi bệnh nhân kế tiếp
        $advanceResponse = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.opd-advance', $allocator->id));
        $advanceResponse->assertRedirect();

        $allocator->refresh();
        $this->assertEquals(11, $allocator->current_queue_number);
        $this->assertEquals(12, $allocator->next_queue_number);
        $this->assertEquals(5, $allocator->waiting_patient_count);
        $this->assertNotNull($allocator->last_epaper_synced_at);
    }

    // 4. Khử trùng phòng sau xuất viện UV-C & kiểm định ATP
    public function test_store_terminal_room_disinfection_and_atp_clean_certification(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.disinfection'), [
            'room_number' => 'ROOM-612',
            'ward_department' => 'Khoa Nội Tổng Hợp',
            'discharge_patient_name' => 'Nguyễn Thị L',
            'cleaning_trigger_event' => 'patient_discharge',
            'disinfection_method' => 'uvc_robot_autonomous',
            'disinfection_robot_id' => 'UVC-BOT-05',
            'run_duration_minutes' => 30,
            'cleaning_staff_name' => 'Lê Văn M',
            'atp_bioluminescence_rlu' => 18,
        ]);

        $response->assertRedirect();
        $disinfection = TerminalRoomDisinfection::where('room_number', 'ROOM-612')->first();
        $this->assertNotNull($disinfection);
        $this->assertEquals('certified_clean_ready', $disinfection->room_ready_status);
        $this->assertNotNull($disinfection->qr_verified_clean_at);
    }

    // 5. Buồng sinh gia đình LDRP 4-trong-1
    public function test_store_smart_delivery_suite_ldrp_and_stage_transition(): void
    {
        $user = $this->createReceptionistUser();
        $mother = $this->createPatient();

        $response = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.ldrp'), [
            'suite_code' => 'LDRP-03',
            'maternal_patient_id' => $mother->id,
            'mother_name' => $mother->full_name,
            'gestational_weeks' => 39.5,
            'current_stage' => 'stage1_labor',
            'water_birth_tub_active' => true,
            'infant_resuscitaire_warmed' => true,
            'pain_management_type' => 'water_immersion',
            'fetal_heart_rate_bpm' => 144,
            'maternal_comfort_score' => 9,
        ]);

        $response->assertRedirect();
        $suite = SmartDeliverySuiteLdrp::where('suite_code', 'LDRP-03')->first();
        $this->assertNotNull($suite);

        // Chuyển sang giai đoạn 2 rặn sinh
        $suite->advanceStage('stage2_delivery');
        $this->assertEquals('stage2_delivery', $suite->current_stage);
        $this->assertEquals('active_delivery_mode', $suite->bed_transformation_mode);
    }

    // 6. Trạm điều dưỡng trung tâm & tỷ lệ lấp đầy
    public function test_store_smart_nurse_station_hub_and_occupancy_rate(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.smart-inpatient-ward-facility.nurse-hub'), [
            'ward_code' => 'WARD-SURG-7F',
            'ward_name' => 'Khoa Ngoại Tiêu Hóa',
            'total_active_beds' => 40,
            'occupied_beds' => 32,
            'critical_alert_count' => 1,
            'pending_nurse_calls' => 2,
            'infusion_near_empty_count' => 3,
            'on_duty_head_nurse' => 'ĐD. Phạm Thị N',
            'code_blue_standby_status' => 'green_ready',
        ]);

        $response->assertRedirect();
        $hub = SmartNurseStationHub::where('ward_code', 'WARD-SURG-7F')->first();
        $this->assertNotNull($hub);
        $this->assertEquals(80.0, $hub->occupancy_rate);
    }

    // 7. Kiểm tra trang Index Specialty High Risk Room
    public function test_specialty_high_risk_room_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.specialty-high-risk-room.index'));

        $response->assertStatus(200);
        $response->assertSee('Phòng Cách Ly Ghép Tủy PE, An Toàn X-Ray/MRI Bọc Chì & Phòng Sạch ISO', false);
        $response->assertSee('Phòng Áp Lực Dương Ghép Tủy');
    }

    // 8. Phòng cách ly áp lực dương ghép tủy PE & chuẩn JCI
    public function test_store_protective_cleanroom_pe_and_jci_compliance_evaluation(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $response = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.pe-cleanroom'), [
            'room_code' => 'PE-BMT-02',
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'differential_pressure_pa' => 14.0,
            'hepa_filter_status' => 'normal_high_efficiency',
            'air_changes_per_hour' => 16.5,
            'door_airlock_alarm' => false,
            'aspergillus_spore_count' => 0.00,
        ]);

        $response->assertRedirect();
        $pe = ProtectiveCleanroomPe::where('room_code', 'PE-BMT-02')->first();
        $this->assertNotNull($pe);
        $this->assertEquals('pe_compliant', $pe->safety_compliance);

        // Thử trường hợp áp suất tụt
        $pe->differential_pressure_pa = 5.0;
        $status = $pe->evaluateCompliance();
        $this->assertEquals('pressure_drop_alert', $status);
    }

    // 9. Phòng bọc chì chẩn đoán hình ảnh & khóa liên động phát tia
    public function test_store_lead_lined_radiology_and_beam_interlock_trigger(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.radiology-lead'), [
            'room_code' => 'RAD-CT-03',
            'modality_type' => 'ct_scanner_multislice',
            'radiation_warning_light' => 'light_off_safe',
            'lead_door_sealed' => true,
            'lead_glass_thickness_mm' => 3.0,
            'ferromagnetic_mri_alarm' => false,
            'interlock_lock_engaged' => true,
            'ambient_radiation_dose_rate_usvh' => 0.10,
        ]);

        $response->assertRedirect();
        $rad = LeadLinedRadiologySafety::where('room_code', 'RAD-CT-03')->first();
        $this->assertNotNull($rad);

        // Kích hoạt phát tia quét an toàn
        $triggerResponse = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.beam-emission', $rad->id));
        $triggerResponse->assertRedirect();

        $rad->refresh();
        $this->assertEquals('red_xray_emitting', $rad->radiation_warning_light);
        $this->assertEquals('scanning_in_progress', $rad->safety_status);
    }

    // 10. Nhà đại thể thông minh & giám sát hơi Formalin
    public function test_store_pathology_mortuary_storage_and_formalin_exhaust_safety(): void
    {
        $user = $this->createReceptionistUser();
        $unitCode = 'COLD-BAY-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.mortuary'), [
            'unit_code' => $unitCode,
            'storage_type' => 'standard_cold_4c',
            'deceased_record_id' => 'DEC-2026-099',
            'rfid_tag_identifier' => 'RFID-TAG-0099',
            'temperature_celsius' => 3.2,
            'formalin_vapor_ppm' => 0.04,
            'exhaust_ventilation_status' => 'running_nominal',
            'identity_verified_by_two_staff' => true,
            'storage_status' => 'occupied_secured',
        ]);

        $response->assertRedirect();
        $mort = PathologyMortuaryStorage::where('unit_code', $unitCode)->first();
        $this->assertNotNull($mort);
        $this->assertTrue($mort->checkEnvironmentalSafety());

        // Mô phỏng rò rỉ Formalin
        $mort->formalin_vapor_ppm = 0.18;
        $safe = $mort->checkEnvironmentalSafety();
        $this->assertFalse($safe);
        $this->assertEquals('high_speed_purge', $mort->exhaust_ventilation_status);
    }

    // 11. Chu chuyển phòng nội soi & máy rửa AER
    public function test_store_endoscopy_suite_turnover_and_aer_reprocessor_ready(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.endoscopy-turnover'), [
            'suite_code' => 'ENDO-SUITE-4',
            'procedure_type' => 'colonoscopy',
            'endoscope_serial_code' => 'OLY-CF-HQ190L',
            'turnaround_minutes' => 18,
            'aer_reprocessor_id' => 'AER-MED-03',
            'peracetic_acid_test_passed' => true,
            'biopsy_channel_dried' => true,
        ]);

        $response->assertRedirect();
        $endo = EndoscopySuiteTurnover::where('suite_code', 'ENDO-SUITE-4')->first();
        $this->assertNotNull($endo);
        $this->assertEquals('suite_ready', $endo->readiness_status);
    }

    // 12. Phòng sạch pha chế thuốc TPN ISO Class 5
    public function test_store_cleanroom_pharmacy_compounding_and_iso_class5_compliance(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.specialty-high-risk-room.cleanroom-pharmacy'), [
            'cleanroom_code' => 'CLEAN-ISO5-TPN',
            'iso_classification' => 'iso_class_5',
            'cascade_pressure_pa' => 28.0,
            'air_particle_count_0_5um' => 310,
            'air_shower_interlock_active' => true,
            'compounding_type' => 'neonatal_tpn_nutrition',
            'pharmacist_lead_name' => 'DS. Nguyễn Hoàng P',
        ]);

        $response->assertRedirect();
        $clean = CleanroomPharmacyCompounding::where('cleanroom_code', 'CLEAN-ISO5-TPN')->first();
        $this->assertNotNull($clean);
        $this->assertEquals('certified_sterile_pass', $clean->batch_sterility_status);
    }

    // 13. Kiểm tra trang Index Facility Automation Lounge
    public function test_facility_automation_lounge_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.facility-automation-lounge.index'));

        $response->assertStatus(200);
        $response->assertSee('Tự Động Hóa Tòa Nhà Bệnh Viện, An Ninh Cửa Liên Động & Ghế Truyền Ban Ngày', false);
        $response->assertSee('Xử Lý Đồ Bẩn Sluice');
    }

    // 14. Phòng xử lý đồ bẩn Sluice & máy rửa bô vịt
    public function test_store_biohazard_dirty_utility_sluice_and_bedpan_washer_audit(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.facility-automation-lounge.biohazard-sluice'), [
            'sluice_room_code' => 'SLUICE-5F-NORTH',
            'bedpan_washer_temp_celsius' => 90.0,
            'washer_disinfection_cycle' => 'cycle_completed',
            'yellow_bag_infectious_kg' => 14.2,
            'sharps_container_full_pct' => 50,
            'air_deodorizer_fan_status' => 'exhaust_running_nominal',
        ]);

        $response->assertRedirect();
        $sluice = BiohazardDirtyUtilitySluice::where('sluice_room_code', 'SLUICE-5F-NORTH')->first();
        $this->assertNotNull($sluice);
        $this->assertEquals('fully_compliant', $sluice->compliance_audit_status);

        // Hộp sắc nhọn đầy quá 75%
        $sluice->sharps_container_full_pct = 85;
        $audit = $sluice->auditSafety();
        $this->assertEquals('sharps_overfill_warning', $audit);
    }

    // 15. Kiểm soát ra vào FaceID & cửa liên động
    public function test_store_sterile_area_access_interlock_and_biometric_faceid_decision(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.facility-automation-lounge.access-interlock'), [
            'access_point_name' => 'Cửa Khóa Phòng Mổ OR-05',
            'zone_classification' => 'operating_theater_sterile',
            'staff_badge_rfid' => 'RFID-SURG-771',
            'staff_name' => 'BS. Đỗ Quang Q',
            'staff_role' => 'Phẫu Thuật Viên Trưởng',
            'biometric_face_matched' => true,
            'interlock_outer_door_closed' => true,
            'is_authorized_role' => true,
        ]);

        $response->assertRedirect();
        $interlock = SterileAreaAccessInterlock::where('access_point_name', 'Cửa Khóa Phòng Mổ OR-05')->first();
        $this->assertNotNull($interlock);
        $this->assertEquals('granted_normal', $interlock->access_decision);
    }

    // 16. Giám sát chất lượng không khí IAQ & ASHRAE
    public function test_store_indoor_air_quality_hvac_and_ashrae_rating_computation(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.facility-automation-lounge.iaq-hvac'), [
            'room_sensor_code' => 'IAQ-ICU-BED05',
            'room_type' => 'intensive_care_unit',
            'pm2_5_ug_m3' => 8.0,
            'co2_ppm' => 540,
            'tvoc_ppb' => 50,
            'temperature_celsius' => 22.0,
            'relative_humidity_pct' => 50.0,
            'hvac_damper_fresh_air_pct' => 30,
        ]);

        $response->assertRedirect();
        $iaq = IndoorAirQualityHvac::where('room_sensor_code', 'IAQ-ICU-BED05')->first();
        $this->assertNotNull($iaq);
        $this->assertEquals('excellent_green', $iaq->iaq_index_rating);

        // Mô phỏng CO2 tăng cao
        $iaq->co2_ppm = 1200;
        $rating = $iaq->computeIaqRating();
        $this->assertEquals('poor_unhealthy_red', $rating);
        $this->assertEquals(100, $iaq->hvac_damper_fresh_air_pct);
    }

    // 17. Kho lưu trữ hồ sơ bệnh án giấy & FM-200
    public function test_store_medical_records_archive_and_fm200_fire_suppression_readiness(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->post(route('reception.facility-automation-lounge.medical-archive'), [
            'compactor_bay_code' => 'BAY-MOB-12',
            'section_code' => 'SEC-PEDIATRICS-2022',
            'total_paper_charts_stored' => 4200,
            'temperature_celsius' => 21.2,
            'relative_humidity_pct' => 49.0,
            'smoke_detector_active' => false,
            'fm200_cylinder_pressure_bar' => 25.0,
            'rfid_chart_locator_status' => 'indexed_online',
        ]);

        $response->assertRedirect();
        $archive = MedicalRecordsPhysicalArchive::where('compactor_bay_code', 'BAY-MOB-12')->first();
        $this->assertNotNull($archive);
        $this->assertTrue($archive->checkFireReadiness());
        $this->assertEquals('armed_standby', $archive->fire_suppression_status);
    }

    // 18. Ghế truyền hóa chất ban ngày VIP & nút báo sốc phản vệ
    public function test_store_daycare_infusion_lounge_and_anaphylaxis_emergency_trigger(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $response = $this->actingAs($user)->post(route('reception.facility-automation-lounge.daycare-infusion'), [
            'recliner_bay_code' => 'RECLINER-VIP-08',
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'infusion_regimen_type' => 'chemotherapy_paclitaxel',
            'scheduled_duration_hours' => 3.5,
            'infusion_rate_ml_hr' => 150,
            'recliner_massage_heat_active' => true,
            'personal_tablet_connected' => true,
        ]);

        $response->assertRedirect();
        $bay = DayCareInfusionLounge::where('recliner_bay_code', 'RECLINER-VIP-08')->first();
        $this->assertNotNull($bay);

        // Kích hoạt báo động sốc phản vệ khẩn cấp
        $emergencyResponse = $this->actingAs($user)->post(route('reception.facility-automation-lounge.infusion-emergency', $bay->id));
        $emergencyResponse->assertRedirect();

        $bay->refresh();
        $this->assertEquals('anaphylaxis_stat_call', $bay->nurse_call_urgency);
        $this->assertEquals(0, $bay->infusion_rate_ml_hr); // Tự động ngắt dịch truyền
    }

    // 19. Hoàn tất buổi truyền ban ngày
    public function test_complete_daycare_infusion_session(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $bay = DayCareInfusionLounge::create([
            'recliner_bay_code' => 'RECLINER-VIP-09',
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'infusion_regimen_type' => 'intravenous_iron_sucrose',
            'scheduled_duration_hours' => 2.0,
            'infusion_rate_ml_hr' => 100,
            'session_status' => 'infusion_flowing_stable',
        ]);

        $completeResponse = $this->actingAs($user)->post(route('reception.facility-automation-lounge.infusion-complete', $bay->id));
        $completeResponse->assertRedirect();

        $bay->refresh();
        $this->assertEquals('infusion_completed', $bay->session_status);
        $this->assertEquals('normal', $bay->nurse_call_urgency);
    }
}
