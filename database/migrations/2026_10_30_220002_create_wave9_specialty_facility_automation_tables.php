<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 6. Phòng cách ly áp lực dương ghép tủy & vô trùng PE (Protective Environment)
        Schema::create('protective_cleanroom_pes', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 50);
            $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('patient_name');
            $table->decimal('differential_pressure_pa', 4, 1)->default(12.5);
            $table->enum('hepa_filter_status', ['normal_high_efficiency', 'maintenance_due', 'replacement_required'])->default('normal_high_efficiency');
            $table->decimal('air_changes_per_hour', 4, 1)->default(15.0);
            $table->boolean('door_airlock_alarm')->default(false);
            $table->decimal('aspergillus_spore_count', 4, 2)->default(0.00);
            $table->enum('safety_compliance', ['pe_compliant', 'pressure_drop_alert', 'compromised_evacuate'])->default('pe_compliant');
            $table->timestamps();

            $table->index(['room_code', 'safety_compliance'], 'pe_room_comp_idx');
        });

        // 7. Quản lý an toàn phòng X-quang, CT, MRI bọc chì & Khóa liên động
        Schema::create('lead_lined_radiology_safeties', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 50);
            $table->enum('modality_type', ['ct_scanner_multislice', 'mri_3_tesla', 'digital_xray', 'angiography_cath_lab'])->default('ct_scanner_multislice');
            $table->enum('radiation_warning_light', ['light_off_safe', 'amber_prep', 'red_xray_emitting'])->default('light_off_safe');
            $table->boolean('lead_door_sealed')->default(true);
            $table->decimal('lead_glass_thickness_mm', 3, 1)->default(2.5);
            $table->boolean('ferromagnetic_mri_alarm')->default(false);
            $table->boolean('interlock_lock_engaged')->default(true);
            $table->decimal('ambient_radiation_dose_rate_usvh', 5, 2)->default(0.12);
            $table->enum('safety_status', ['beam_safe_locked', 'scanning_in_progress', 'door_ajar_interlock_tripped'])->default('beam_safe_locked');
            $table->timestamps();

            $table->index(['room_code', 'safety_status'], 'rad_room_stat_idx');
        });

        // 8. Phòng pha bệnh phẩm giải phẫu bệnh & Quản lý nhà đại thể thông minh
        Schema::create('pathology_mortuary_storages', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 50);
            $table->enum('storage_type', ['standard_cold_4c', 'deep_freeze_minus15c', 'pathology_grossing_station'])->default('standard_cold_4c');
            $table->string('deceased_record_id', 50)->nullable();
            $table->string('rfid_tag_identifier', 50)->nullable();
            $table->decimal('temperature_celsius', 4, 1)->default(3.5);
            $table->decimal('formalin_vapor_ppm', 4, 2)->default(0.05);
            $table->enum('exhaust_ventilation_status', ['running_nominal', 'high_speed_purge', 'fan_failure_alert'])->default('running_nominal');
            $table->boolean('identity_verified_by_two_staff')->default(true);
            $table->enum('storage_status', ['vacant_sanitized', 'occupied_secured', 'grossing_active', 'dispatched_funeral'])->default('vacant_sanitized');
            $table->timestamps();

            $table->index(['unit_code', 'storage_status'], 'path_mort_unit_stat_idx');
        });

        // 9. Quản lý chu chuyển phòng nội soi & máy rửa dây soi AER
        Schema::create('endoscopy_suite_turnovers', function (Blueprint $table) {
            $table->id();
            $table->string('suite_code', 50);
            $table->enum('procedure_type', ['gastroscopy', 'colonoscopy', 'bronchoscopy', 'ercp_biliary'])->default('gastroscopy');
            $table->string('endoscope_serial_code', 50);
            $table->unsignedInteger('turnaround_minutes')->default(20);
            $table->string('aer_reprocessor_id', 50)->default('AER-MEDIVATORS-01');
            $table->boolean('peracetic_acid_test_passed')->default(true);
            $table->boolean('biopsy_channel_dried')->default(true);
            $table->enum('readiness_status', ['procedure_running', 'scope_in_reprocessor', 'terminal_room_wipe', 'suite_ready'])->default('suite_ready');
            $table->timestamps();

            $table->index(['suite_code', 'readiness_status'], 'endo_suite_stat_idx');
        });

        // 10. Phòng sạch pha chế TPN & Nhãn khoa ISO Class 5/7
        Schema::create('cleanroom_pharmacy_compoundings', function (Blueprint $table) {
            $table->id();
            $table->string('cleanroom_code', 50);
            $table->enum('iso_classification', ['iso_class_5', 'iso_class_7', 'iso_class_8'])->default('iso_class_5');
            $table->decimal('cascade_pressure_pa', 4, 1)->default(25.0);
            $table->unsignedInteger('air_particle_count_0_5um')->default(280);
            $table->boolean('air_shower_interlock_active')->default(true);
            $table->enum('compounding_type', ['neonatal_tpn_nutrition', 'ophthalmic_eye_drops', 'intrathecal_antibiotic'])->default('neonatal_tpn_nutrition');
            $table->string('pharmacist_lead_name');
            $table->enum('batch_sterility_status', ['certified_sterile_pass', 'airlock_compromised', 'recalibration_quarantine'])->default('certified_sterile_pass');
            $table->timestamps();

            $table->index(['cleanroom_code', 'batch_sterility_status'], 'clean_pharm_stat_idx');
        });

        // 11. Phòng xử lý đồ bẩn & Quản trị rác thải lây nhiễm Biohazard
        Schema::create('biohazard_dirty_utility_sluices', function (Blueprint $table) {
            $table->id();
            $table->string('sluice_room_code', 50);
            $table->decimal('bedpan_washer_temp_celsius', 4, 1)->default(88.5);
            $table->enum('washer_disinfection_cycle', ['idle', 'thermal_disinfecting', 'cycle_completed', 'door_locked_cooling'])->default('cycle_completed');
            $table->decimal('yellow_bag_infectious_kg', 5, 2)->default(12.40);
            $table->unsignedTinyInteger('sharps_container_full_pct')->default(45);
            $table->enum('air_deodorizer_fan_status', ['exhaust_running_nominal', 'booster_active', 'filter_clogged'])->default('exhaust_running_nominal');
            $table->enum('compliance_audit_status', ['fully_compliant', 'sharps_overfill_warning', 'washer_temp_insufficient'])->default('fully_compliant');
            $table->timestamps();

            $table->index(['sluice_room_code', 'compliance_audit_status'], 'bio_sluice_stat_idx');
        });

        // 12. Phân quyền ra vào FaceID/Thẻ từ & Khóa liên động phòng rủi ro cao
        Schema::create('sterile_area_access_interlocks', function (Blueprint $table) {
            $table->id();
            $table->string('access_point_name', 100);
            $table->enum('zone_classification', ['operating_theater_sterile', 'narcotics_vault', 'blood_bank_core', 'bsl3_virology_lab'])->default('operating_theater_sterile');
            $table->string('staff_badge_rfid', 50);
            $table->string('staff_name');
            $table->string('staff_role');
            $table->boolean('biometric_face_matched')->default(true);
            $table->boolean('interlock_outer_door_closed')->default(true);
            $table->boolean('interlock_inner_door_unlocked')->default(true);
            $table->enum('access_decision', ['granted_normal', 'denied_unauthorized_role', 'denied_airlock_breached', 'emergency_override_breakglass'])->default('granted_normal');
            $table->timestamp('access_timestamp')->useCurrent();
            $table->timestamps();

            $table->index(['access_point_name', 'access_decision'], 'interlock_point_dec_idx');
        });

        // 13. Giám sát chất lượng không khí IAQ & Điều hòa thông minh HVAC
        Schema::create('indoor_air_quality_hvacs', function (Blueprint $table) {
            $table->id();
            $table->string('room_sensor_code', 50);
            $table->enum('room_type', ['intensive_care_unit', 'operating_room', 'clean_pharmacy', 'general_inpatient_suite'])->default('intensive_care_unit');
            $table->decimal('pm2_5_ug_m3', 4, 1)->default(6.2);
            $table->unsignedInteger('co2_ppm')->default(520);
            $table->unsignedInteger('tvoc_ppb')->default(45);
            $table->decimal('temperature_celsius', 4, 1)->default(22.5);
            $table->decimal('relative_humidity_pct', 4, 1)->default(52.0);
            $table->unsignedTinyInteger('hvac_damper_fresh_air_pct')->default(35);
            $table->enum('iaq_index_rating', ['excellent_green', 'moderate_yellow', 'poor_unhealthy_red'])->default('excellent_green');
            $table->timestamps();

            $table->index(['room_sensor_code', 'iaq_index_rating'], 'iaq_sensor_rate_idx');
        });

        // 14. Kho lưu trữ hồ sơ bệnh án giấy & Hệ thống chữa cháy khí sạch FM-200
        Schema::create('medical_records_physical_archives', function (Blueprint $table) {
            $table->id();
            $table->string('compactor_bay_code', 50);
            $table->string('section_code', 50);
            $table->unsignedInteger('total_paper_charts_stored')->default(2500);
            $table->decimal('temperature_celsius', 4, 1)->default(21.0);
            $table->decimal('relative_humidity_pct', 4, 1)->default(48.0);
            $table->boolean('smoke_detector_active')->default(false);
            $table->decimal('fm200_cylinder_pressure_bar', 4, 1)->default(25.0);
            $table->enum('fire_suppression_status', ['armed_standby', 'gas_discharge_countdown', 'discharged_lockdown', 'service_inspection'])->default('armed_standby');
            $table->enum('rfid_chart_locator_status', ['indexed_online', 'chart_checked_out', 'inventory_audit'])->default('indexed_online');
            $table->timestamps();

            $table->index(['compactor_bay_code', 'fire_suppression_status'], 'archive_bay_fire_idx');
        });

        // 15. Phòng điều trị hóa chất & Truyền dịch ban ngày cao cấp
        Schema::create('day_care_infusion_lounges', function (Blueprint $table) {
            $table->id();
            $table->string('recliner_bay_code', 50);
            $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('patient_name');
            $table->enum('infusion_regimen_type', ['chemotherapy_paclitaxel', 'biologic_rituximab', 'intravenous_iron_sucrose', 'post_op_hydration_antibiotic'])->default('chemotherapy_paclitaxel');
            $table->decimal('scheduled_duration_hours', 3, 1)->default(3.0);
            $table->unsignedInteger('infusion_rate_ml_hr')->default(125);
            $table->boolean('recliner_massage_heat_active')->default(true);
            $table->boolean('personal_tablet_connected')->default(true);
            $table->enum('nurse_call_urgency', ['normal', 'rigor_chills_alert', 'anaphylaxis_stat_call'])->default('normal');
            $table->enum('session_status', ['pre_medication_prep', 'infusion_flowing_stable', 'infusion_completed', 'discharged_home'])->default('infusion_flowing_stable');
            $table->timestamps();

            $table->index(['patient_id', 'session_status'], 'infusion_pat_stat_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_care_infusion_lounges');
        Schema::dropIfExists('medical_records_physical_archives');
        Schema::dropIfExists('indoor_air_quality_hvacs');
        Schema::dropIfExists('sterile_area_access_interlocks');
        Schema::dropIfExists('biohazard_dirty_utility_sluices');
        Schema::dropIfExists('cleanroom_pharmacy_compoundings');
        Schema::dropIfExists('endoscopy_suite_turnovers');
        Schema::dropIfExists('pathology_mortuary_storages');
        Schema::dropIfExists('lead_lined_radiology_safeties');
        Schema::dropIfExists('protective_cleanroom_pes');
    }
};
