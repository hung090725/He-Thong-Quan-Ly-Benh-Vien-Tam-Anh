<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\HospitalAgvRobotFleet;
use App\Models\PneumaticTubeRouting;
use App\Models\SmartHospitalMicrogridEsg;
use App\Models\BiohazardMicrowaveShredder;
use App\Models\HemodialysisCentralRoWater;
use App\Models\SmartAssetTrackingBle;
use App\Models\BiomedicalEquipmentCmms;
use App\Models\SmartParkingAmbulanceGate;
use App\Models\CentralKitchenHaccp;
use App\Models\LinenRfidLifecycleTracking;
use App\Models\HospitalIncidentCommandHics;
use App\Models\BiometricSafekeepingVault;
use App\Models\FrozenSectionLogistics;
use App\Models\ClinicianFatigueCircadian;
use App\Models\DischargeLoungeMedsToBeds;
use Tests\TestCase;

class Wave10SmartLogisticsEsgTest extends TestCase
{
    private function createReceptionistUser(): User
    {
        return User::factory()->create([
            'email' => 'rec_w10_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    private function createPatient(): PatientProfile
    {
        return PatientProfile::create([
            'patient_code' => 'BN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)) . rand(100, 999),
            'full_name' => 'Bệnh Nhân Wave 10 ' . rand(100, 999),
            'phone' => '0988' . rand(100000, 999999),
            'dob' => '1995-10-10',
            'gender' => 'male',
            'identity_card' => '001095' . rand(100000, 999999),
            'address' => 'Hà Nội',
        ]);
    }

    private function createDoctor(): Doctor
    {
        $specialty = Specialty::firstOrCreate(
            ['slug' => 'chuyen-khoa-w10'],
            ['name' => 'Khoa Vận Hành & An Ninh Bệnh Viện W10', 'description' => 'Khoa Vận Hành Tòa Nhà Thông Minh']
        );

        $user = User::factory()->create([
            'email' => 'doc_w10_' . uniqid() . '_' . rand(1000, 9999) . '@tamanh.vn',
            'role' => 'doctor',
            'is_active' => true,
        ]);

        return Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'CCHN-W10-' . rand(10000, 99999),
            'room_number' => 'P-W10-' . rand(100, 999),
            'qualification' => 'Bác Sĩ CKI',
            'experience_years' => 12,
            'consultation_fee' => 450000,
        ]);
    }

    // 1. Kiểm tra trang Index Autonomous Logistics ESG
    public function test_autonomous_logistics_esg_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.autonomous-logistics-esg.index'));

        $response->assertStatus(200);
        $response->assertSee('Robot AGV Tự Hành, Ống Khí Nén PTS, Microgrid ESG & Nước Chạy Thận RO', false);
        $response->assertSee('Robot AGV Tự Hành');
    }

    // 2. Robot AGV tự hành & lệnh điều phối giao hàng
    public function test_store_agv_robot_and_dispatch_delivery(): void
    {
        $user = $this->createReceptionistUser();
        $code = 'AGV-TEST-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.agv-robot'), [
            'robot_code' => $code,
            'robot_model' => 'MiR250 AMR Autonomous Mobile Robot',
            'payload_category' => 'sterile_supplies',
            'current_floor' => '1F',
            'destination_location' => 'Phòng Mổ OR-04 Tầng 3',
            'battery_percentage' => 88,
            'navigation_status' => 'idle_docked',
            'dispatch_supervisor_name' => 'Kỹ sư Phạm Văn H',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospital_agv_robot_fleets', [
            'robot_code' => $code,
            'payload_category' => 'sterile_supplies',
        ]);

        $robot = HospitalAgvRobotFleet::where('robot_code', $code)->first();
        $this->assertNotNull($robot);

        // Kích hoạt giao hàng
        $dispatchResponse = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.agv-dispatch', $robot->id));
        $dispatchResponse->assertRedirect();

        $robot->refresh();
        $this->assertEquals('delivering_payload', $robot->navigation_status);
    }

    // 3. Đường ống khí nén PTS & kiểm soát vỡ hồng cầu
    public function test_store_pneumatic_tube_and_hemolysis_risk_evaluation(): void
    {
        $user = $this->createReceptionistUser();
        $barcode = 'PTS-CAP-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.pneumatic-tube'), [
            'station_code' => 'STATION-ICU-3F',
            'destination_station' => 'CENTRAL-LAB-1F',
            'capsule_barcode' => $barcode,
            'specimen_category' => 'stat_troponin_blood',
            'velocity_meters_per_sec' => 6.8,
            'g_force_shock_peak' => 1.45,
        ]);

        $response->assertRedirect();
        $pts = PneumaticTubeRouting::where('capsule_barcode', $barcode)->first();
        $this->assertNotNull($pts);
        $this->assertEquals('arrived_safe', $pts->delivery_status);
        $this->assertFalse($pts->hemolysis_risk_alert);

        // Trường hợp sốc lực cao
        $pts->g_force_shock_peak = 3.2;
        $risk = $pts->assessHemolysisRisk();
        $this->assertTrue($risk);
        $this->assertEquals('diverted_maintenance', $pts->delivery_status);
    }

    // 4. Lưới điện vi mô Microgrid & chuyển mạch ATS
    public function test_store_smart_hospital_microgrid_and_blackout_trigger(): void
    {
        $user = $this->createReceptionistUser();
        $code = 'GRID-ZONE-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.microgrid'), [
            'power_zone_code' => $code,
            'grid_power_status' => 'national_grid_nominal',
            'ats_transfer_time_sec' => 6.50,
            'backup_generator_running' => false,
            'ups_reserve_minutes' => 45,
            'rooftop_solar_generation_kw' => 135.0,
            'carbon_emissions_avoided_kg' => 920.0,
            'grid_safety_rating' => 'optimal_tier4',
        ]);

        $response->assertRedirect();
        $grid = SmartHospitalMicrogridEsg::where('power_zone_code', $code)->first();
        $this->assertNotNull($grid);

        // Kích hoạt tình huống mất điện lưới quốc gia
        $grid->triggerGridBlackoutEmergency();
        $this->assertEquals('grid_blackout_detected', $grid->grid_power_status);
        $this->assertTrue($grid->backup_generator_running);
    }

    // 5. Nghiền hấp vi sóng rác thải y tế tại chỗ
    public function test_store_biohazard_microwave_shredder_cycle(): void
    {
        $user = $this->createReceptionistUser();
        $barcode = 'MW-BATCH-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.microwave-shredder'), [
            'unit_code' => 'MW-SHRED-B2-01',
            'batch_barcode' => $barcode,
            'waste_input_weight_kg' => 90.5,
            'microwave_temp_celsius' => 106.5,
            'exposure_time_minutes' => 30,
            'volume_reduction_pct' => 84.0,
            'spore_bacillus_atrophaeus_killed' => true,
        ]);

        $response->assertRedirect();
        $mw = BiohazardMicrowaveShredder::where('batch_barcode', $barcode)->first();
        $this->assertNotNull($mw);
        $this->assertEquals('decontaminated_inert', $mw->processing_status);
    }

    // 6. Nước tinh khiết chạy thận nhân tạo RO chuẩn ISO 23500
    public function test_store_hemodialysis_ro_water_and_iso23500_audit(): void
    {
        $user = $this->createReceptionistUser();
        $code = 'RO-PLANT-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.autonomous-logistics-esg.ro-water'), [
            'ro_plant_code' => $code,
            'conductivity_us_cm' => 3.80,
            'bacteria_cfu_per_ml' => 8,
            'endotoxin_eu_per_ml' => 0.035,
            'water_temperature_celsius' => 24.0,
            'permeate_flow_rate_lpm' => 48.0,
        ]);

        $response->assertRedirect();
        $ro = HemodialysisCentralRoWater::where('ro_plant_code', $code)->first();
        $this->assertNotNull($ro);
        $this->assertEquals('iso_ultra_pure_pass', $ro->aami_iso23500_compliance);

        // Thử trường hợp nồng độ endotoxin vượt ngưỡng
        $ro->endotoxin_eu_per_ml = 0.35;
        $compliance = $ro->auditIsoCompliance();
        $this->assertEquals('endotoxin_breach_shutoff', $compliance);
    }

    // 7. Kiểm tra trang Index Asset Engineering HACCP Linen
    public function test_asset_engineering_haccp_linen_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.asset-engineering-haccp-linen.index'));

        $response->assertStatus(200);
        $response->assertSee('Định Vị Thiết Bị BLE, Bảo Trì CMMS, Bãi Xe Cấp Cứu, Bếp HACCP & Đồ Vải RFID', false);
        $response->assertSee('Định Vị Thiết Bị BLE');
    }

    // 8. Định vị thiết bị y tế BLE & cảnh báo thất thoát Geofence
    public function test_store_asset_ble_and_theft_alarm(): void
    {
        $user = $this->createReceptionistUser();
        $tag = 'BLE-TAG-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.asset-ble'), [
            'asset_tag_ble' => $tag,
            'asset_name' => 'Máy Sốc Điện Phá Rung Zoll R-Series',
            'equipment_category' => 'defibrillator_aed',
            'current_location_zone' => 'Phòng Cấp Cứu Tầng 1',
            'rssi_signal_strength_dbm' => -58,
            'asset_status' => 'available_ready',
        ]);

        $response->assertRedirect();
        $asset = SmartAssetTrackingBle::where('asset_tag_ble', $tag)->first();
        $this->assertNotNull($asset);

        // Kích hoạt báo động mất trộm vượt rào Geofence
        $alarmResponse = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.asset-alarm', $asset->id));
        $alarmResponse->assertRedirect();

        $asset->refresh();
        $this->assertEquals('out_of_bounds_theft_alarm', $asset->asset_status);
        $this->assertFalse($asset->in_geofence_perimeter);
    }

    // 9. Kỹ thuật y sinh CMMS & an toàn dòng rò điện IEC 62353
    public function test_store_biomedical_cmms_and_electrical_safety(): void
    {
        $user = $this->createReceptionistUser();
        $serial = 'SN-VENT-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.biomedical-cmms'), [
            'equipment_serial_no' => $serial,
            'device_name' => 'Máy Thở Hamilton-C6',
            'maintenance_type' => 'electrical_safety_iec62353',
            'leakage_current_microamp' => 38.0,
            'ground_resistance_ohm' => 0.10,
            'mtbf_hours_calculated' => 3200,
            'calibration_expiry_date' => date('Y-m-d', strtotime('+1 year')),
            'bio_engineer_name' => 'Kỹ sư Đặng Văn I',
        ]);

        $response->assertRedirect();
        $cmms = BiomedicalEquipmentCmms::where('equipment_serial_no', $serial)->first();
        $this->assertNotNull($cmms);
        $this->assertEquals('certified_safe_pass', $cmms->certification_status);
    }

    // 10. Bãi xe thông minh & barie luồng xanh cấp cứu ALPR
    public function test_store_parking_gate_and_ambulance_fast_track(): void
    {
        $user = $this->createReceptionistUser();
        $plate = '29A-115.' . rand(10, 99);

        $response = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.parking-gate'), [
            'vehicle_license_plate' => $plate,
            'vehicle_type' => 'emergency_ambulance',
            'alpr_camera_confidence_pct' => 99.2,
            'assigned_parking_bay' => 'BAY-EMERGENCY-01',
            'emergency_siren_detected' => true,
        ]);

        $response->assertRedirect();
        $gate = SmartParkingAmbulanceGate::where('vehicle_license_plate', $plate)->first();
        $this->assertNotNull($gate);
        $this->assertEquals('auto_opened_fast_track', $gate->barrier_gate_status);
    }

    // 11. Bếp ăn dinh dưỡng điều trị chuẩn HACCP / ISO 22000
    public function test_store_central_kitchen_haccp_audit(): void
    {
        $user = $this->createReceptionistUser();
        $batch = 'DIET-RENAL-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.kitchen-haccp'), [
            'menu_batch_code' => $batch,
            'diet_category' => 'renal_low_sodium',
            'cooking_core_temp_celsius' => 84.0,
            'rethermalization_hot_temp' => 69.5,
            'rethermalization_cold_temp' => 3.2,
            'food_sample_refrigerated_24h' => true,
            'executive_chef_dietitian' => 'BS. Mai Thị O',
        ]);

        $response->assertRedirect();
        $kitchen = CentralKitchenHaccp::where('menu_batch_code', $batch)->first();
        $this->assertNotNull($kitchen);
        $this->assertTrue($kitchen->haccp_audit_passed);
    }

    // 12. Quản lý đồ vải & săng mổ gắn chip RFID chịu nhiệt
    public function test_store_linen_rfid_and_wash_cycle_advance(): void
    {
        $user = $this->createReceptionistUser();
        $epc = 'RFID-LINEN-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.linen-rfid'), [
            'linen_rfid_epc' => $epc,
            'linen_type' => 'surgical_drape_pack',
            'wash_cycle_count' => 35,
            'max_wash_lifespan' => 100,
            'last_wash_temp_celsius' => 92.5,
            'biological_spore_test' => 'negative_sterile',
        ]);

        $response->assertRedirect();
        $linen = LinenRfidLifecycleTracking::where('linen_rfid_epc', $epc)->first();
        $this->assertNotNull($linen);

        // Tăng chu kỳ giặt
        $washResponse = $this->actingAs($user)->post(route('reception.asset-engineering-haccp-linen.linen-wash', $linen->id));
        $washResponse->assertRedirect();

        $linen->refresh();
        $this->assertEquals(36, $linen->wash_cycle_count);
        $this->assertEquals('in_circulation_sterile', $linen->linen_condition);
    }

    // 13. Kiểm tra trang Index Disaster Safety Fatigue Discharge
    public function test_disaster_safety_fatigue_discharge_index_accessible(): void
    {
        $user = $this->createReceptionistUser();

        $response = $this->actingAs($user)->get(route('reception.disaster-safety-fatigue-discharge.index'));

        $response->assertStatus(200);
        $response->assertSee('Chỉ Huy Thảm Họa HICS, Két Bảo Mật, Sinh Thiết Lạnh & Chống Kiệt Sức Ca Trực', false);
        $response->assertSee('Chỉ Huy Thảm Họa HICS');
    }

    // 14. Chỉ huy thảm họa HICS & kích hoạt kịch bản
    public function test_store_hics_disaster_and_deescalate(): void
    {
        $user = $this->createReceptionistUser();
        $code = 'HICS-EVENT-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.hics'), [
            'incident_event_code' => $code,
            'disaster_type' => 'code_yellow_mass_casualty',
            'command_center_location' => 'Phòng Hội Chẩn Trung Tâm Tầng 2',
            'incident_commander_name' => 'Giám Đốc Bệnh Viện',
            'casualty_intake_expected' => 60,
            'beds_mobilized_count' => 50,
            'drill_or_actual' => 'real_world_emergency',
        ]);

        $response->assertRedirect();
        $hics = HospitalIncidentCommandHics::where('incident_event_code', $code)->first();
        $this->assertNotNull($hics);
        $this->assertEquals('active_mobilizing', $hics->activation_status);

        // Hạ nhiệt về chế độ Standby
        $deEscalateResponse = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.hics-deescalate', $hics->id));
        $deEscalateResponse->assertRedirect();

        $hics->refresh();
        $this->assertEquals('de_escalated_standby', $hics->activation_status);
    }

    // 15. Két bảo mật kép lưu giữ tư trang bệnh nhân
    public function test_store_biometric_vault_and_release_to_guardian(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $barcode = 'VAULT-BAG-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.biometric-vault'), [
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'sealed_bag_barcode' => $barcode,
            'item_inventory_manifest' => 'Ví da có tiền mặt 5.000.000 VNĐ, điện thoại iPhone 14 Pro Max',
            'dual_sign_staff_1' => 'ĐD. Lê Thị K',
            'dual_sign_staff_2' => 'BV. Vũ Văn L',
            'vault_locker_number' => 'LOCKER-C12',
        ]);

        $response->assertRedirect();
        $vault = BiometricSafekeepingVault::where('sealed_bag_barcode', $barcode)->first();
        $this->assertNotNull($vault);
        $this->assertEquals('secured_in_vault', $vault->custody_status);

        // Bàn giao cho thân nhân
        $releaseResponse = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.vault-release', $vault->id));
        $releaseResponse->assertRedirect();

        $vault->refresh();
        $this->assertEquals('released_to_guardian', $vault->custody_status);
        $this->assertNotNull($vault->released_at);
    }

    // 16. Sinh thiết lạnh tức thì phòng mổ Frozen Section
    public function test_store_frozen_section_and_turnaround_compliance(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();
        $code = 'FS-SPEC-' . rand(1000, 9999);

        $response = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.frozen-section'), [
            'specimen_label_code' => $code,
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'operating_room' => 'Phòng Mổ OR-03',
            'transit_time_minutes' => 2.2,
            'frozen_biopsy_cold_box_temp' => -20.5,
            'pathology_consultant_name' => 'PGS. TS. Lê Văn S',
            'histopathology_quick_result' => 'benign_margins_clear',
            'turnaround_total_minutes' => 15,
        ]);

        $response->assertRedirect();
        $fs = FrozenSectionLogistics::where('specimen_label_code', $code)->first();
        $this->assertNotNull($fs);
        $this->assertTrue($fs->verifyTurnaroundCompliance());
        $this->assertEquals('benign_margins_clear', $fs->histopathology_quick_result);
    }

    // 17. Giám sát mệt mỏi ca trực Clinician Fatigue Index
    public function test_store_clinician_fatigue_and_duty_fitness_evaluation(): void
    {
        $user = $this->createReceptionistUser();
        $doctor = $this->createDoctor();

        $response = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.clinician-fatigue'), [
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->user->name,
            'shift_duration_hours' => 18.0,
            'consecutive_surgical_hours' => 7.0,
            'sleep_deficit_hours' => 5.0,
            'fatigue_risk_index_score' => 78,
            'relief_physician_assigned' => 'BS. Hoàng Văn M',
        ]);

        $response->assertRedirect();
        $fatigue = ClinicianFatigueCircadian::where('doctor_id', $doctor->id)->first();
        $this->assertNotNull($fatigue);
        $this->assertEquals('grounded_mandatory_sleep', $fatigue->fitness_for_duty_status);
        $this->assertTrue($fatigue->mandatory_rest_alert);
    }

    // 18. Tiếp nhận vào Discharge Lounge & Meds-to-Beds
    public function test_store_discharge_lounge_and_meds_to_beds_delivery(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $response = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.discharge-lounge'), [
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'departed_ward_room' => '705-A',
            'discharge_lounge_bay' => 'LOUNGE-BAY-02',
            'clinical_pharmacist_name' => 'DS. Nguyễn Thị V',
            'bed_freed_before_10am' => true,
        ]);

        $response->assertRedirect();
        $discharge = DischargeLoungeMedsToBeds::where('patient_id', $patient->id)->first();
        $this->assertNotNull($discharge);
        $this->assertEquals('waiting_in_lounge', $discharge->overall_discharge_status);

        // Hoàn tất cấp phát thuốc tại giường
        $medsResponse = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.meds-deliver', $discharge->id));
        $medsResponse->assertRedirect();

        $discharge->refresh();
        $this->assertEquals('counseled_meds_received', $discharge->overall_discharge_status);
        $this->assertTrue($discharge->meds_to_beds_delivered);
    }

    // 19. Bệnh nhân rời viện về nhà an toàn
    public function test_discharge_patient_home_safely(): void
    {
        $user = $this->createReceptionistUser();
        $patient = $this->createPatient();

        $discharge = DischargeLoungeMedsToBeds::create([
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
            'departed_ward_room' => '601-B',
            'discharge_lounge_bay' => 'LOUNGE-BAY-05',
            'clinical_pharmacist_name' => 'DS. Phạm Văn N',
            'overall_discharge_status' => 'counseled_meds_received',
        ]);

        $homeResponse = $this->actingAs($user)->post(route('reception.disaster-safety-fatigue-discharge.discharge-home', $discharge->id));
        $homeResponse->assertRedirect();

        $discharge->refresh();
        $this->assertEquals('discharged_transport_home', $discharge->overall_discharge_status);
    }
}
