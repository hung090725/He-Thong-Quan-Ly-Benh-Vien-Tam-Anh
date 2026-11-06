<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 6. Định vị tài nguyên thiết bị y tế lưu động bằng BLE/UWB (Smart Asset Tracking)
        Schema::create('smart_asset_tracking_bles', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag_ble', 50)->unique();
            $table->string('asset_name', 100);
            $table->enum('equipment_category', ['mechanical_ventilator', 'defibrillator_aed', 'infusion_pump', 'portable_ultrasound', 'crash_cart'])->default('mechanical_ventilator');
            $table->string('current_location_zone', 100)->default('Tầng 3 - Buồng Mổ OR-02');
            $table->integer('rssi_signal_strength_dbm')->default(-65);
            $table->boolean('in_geofence_perimeter')->default(true);
            $table->enum('asset_status', ['available_ready', 'in_use_patient', 'cleaning_decon', 'out_of_bounds_theft_alarm'])->default('available_ready');
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->index(['asset_tag_ble', 'asset_status'], 'ble_asset_stat_idx');
        });

        // 7. Bảo trì thiết bị y tế & an toàn dòng rò điện y sinh IEC 62353 (Biomedical CMMS)
        Schema::create('biomedical_equipment_cmms', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_serial_no', 50);
            $table->string('device_name', 100);
            $table->enum('maintenance_type', ['preventive_calibration', 'corrective_repair', 'electrical_safety_iec62353', 'annual_overhaul'])->default('preventive_calibration');
            $table->decimal('leakage_current_microamp', 5, 1)->default(45.0);
            $table->decimal('ground_resistance_ohm', 4, 2)->default(0.12);
            $table->unsignedInteger('mtbf_hours_calculated')->default(2800);
            $table->date('calibration_expiry_date');
            $table->enum('certification_status', ['certified_safe_pass', 'quarantined_high_leakage', 'calibration_overdue'])->default('certified_safe_pass');
            $table->string('bio_engineer_name');
            $table->timestamps();

            $table->index(['equipment_serial_no', 'certification_status'], 'cmms_serial_cert_idx');
        });

        // 8. Bãi xe thông minh & nhận diện biển số xe cấp cứu mở barie (Smart Parking & Gate)
        Schema::create('smart_parking_ambulance_gates', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_license_plate', 30);
            $table->enum('vehicle_type', ['emergency_ambulance', 'patient_wheelchair_van', 'staff_physician', 'visitor_standard'])->default('emergency_ambulance');
            $table->decimal('alpr_camera_confidence_pct', 4, 1)->default(98.5);
            $table->enum('barrier_gate_status', ['auto_opened_fast_track', 'manual_security_override', 'gate_closed_denied'])->default('auto_opened_fast_track');
            $table->boolean('emergency_siren_detected')->default(true);
            $table->string('assigned_parking_bay', 50)->default('BAY-EMERGENCY-01');
            $table->timestamp('entry_timestamp')->useCurrent();
            $table->timestamps();

            $table->index(['vehicle_license_plate', 'entry_timestamp'], 'park_plate_time_idx');
        });

        // 9. Bếp ăn dinh dưỡng điều trị chuẩn an toàn thực phẩm HACCP / ISO 22000
        Schema::create('central_kitchen_haccps', function (Blueprint $table) {
            $table->id();
            $table->string('menu_batch_code', 50);
            $table->enum('diet_category', ['renal_low_sodium', 'diabetic_glycemic_control', 'post_op_soft_liquid', 'pediatric_allergen_free'])->default('renal_low_sodium');
            $table->decimal('cooking_core_temp_celsius', 4, 1)->default(82.5);
            $table->decimal('rethermalization_hot_temp', 4, 1)->default(68.0);
            $table->decimal('rethermalization_cold_temp', 4, 1)->default(3.5);
            $table->boolean('food_sample_refrigerated_24h')->default(true);
            $table->boolean('haccp_audit_passed')->default(true);
            $table->string('executive_chef_dietitian');
            $table->timestamps();

            $table->index(['menu_batch_code', 'haccp_audit_passed'], 'haccp_batch_audit_idx');
        });

        // 10. Quản lý đồ vải & săng mổ gắn chip RFID giặt chịu nhiệt (Linen RFID Tracking)
        Schema::create('linen_rfid_lifecycle_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('linen_rfid_epc', 50);
            $table->enum('linen_type', ['surgical_drape_pack', 'patient_gown_isolation', 'icu_bed_sheet_thermal', 'doctor_scrub_suit'])->default('surgical_drape_pack');
            $table->unsignedInteger('wash_cycle_count')->default(32);
            $table->unsignedInteger('max_wash_lifespan')->default(100);
            $table->decimal('last_wash_temp_celsius', 4, 1)->default(92.0);
            $table->enum('biological_spore_test', ['negative_sterile', 'positive_bioburden_wash_again'])->default('negative_sterile');
            $table->enum('linen_condition', ['in_circulation_sterile', 'quarantine_soil_contaminated', 'retired_condemned'])->default('in_circulation_sterile');
            $table->timestamps();

            $table->index(['linen_rfid_epc', 'linen_condition'], 'linen_epc_cond_idx');
        });

        // 11. Hệ thống chỉ huy ứng phó thảm họa toàn diện HICS (Hospital Incident Command System)
        Schema::create('hospital_incident_command_hics', function (Blueprint $table) {
            $table->id();
            $table->string('incident_event_code', 50);
            $table->enum('disaster_type', ['code_silver_active_shooter', 'code_black_bomb_threat', 'code_yellow_mass_casualty', 'code_orange_hazardous_spill'])->default('code_yellow_mass_casualty');
            $table->string('command_center_location', 100)->default('Phòng Hội Chẩn Trung Tâm Tầng 2');
            $table->string('incident_commander_name');
            $table->unsignedInteger('casualty_intake_expected')->default(50);
            $table->unsignedInteger('beds_mobilized_count')->default(45);
            $table->enum('drill_or_actual', ['scheduled_mock_drill', 'real_world_emergency'])->default('scheduled_mock_drill');
            $table->enum('activation_status', ['active_mobilizing', 'de_escalated_standby', 'terminated_post_incident'])->default('active_mobilizing');
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamps();

            $table->index(['incident_event_code', 'activation_status'], 'hics_code_stat_idx');
        });

        // 12. Két an toàn lưu giữ tư trang bệnh nhân hôn mê / vô danh bảo mật kép
        Schema::create('biometric_safekeeping_vaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('sealed_bag_barcode', 50);
            $table->text('item_inventory_manifest');
            $table->string('dual_sign_staff_1');
            $table->string('dual_sign_staff_2');
            $table->boolean('biometric_key_verified')->default(true);
            $table->string('vault_locker_number', 20)->default('LOCKER-A14');
            $table->enum('custody_status', ['secured_in_vault', 'released_to_guardian', 'audited_inventory'])->default('secured_in_vault');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'custody_status'], 'vault_pat_custody_idx');
        });

        // 13. Vận chuyển mẫu sinh thiết lạnh phòng mổ siêu tốc (Frozen Section Logistics)
        Schema::create('frozen_section_logistics', function (Blueprint $table) {
            $table->id();
            $table->string('specimen_label_code', 50);
            $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('operating_room', 50)->default('Phòng Mổ OR-03');
            $table->decimal('transit_time_minutes', 4, 1)->default(2.5);
            $table->decimal('frozen_biopsy_cold_box_temp', 4, 1)->default(-20.0);
            $table->string('pathology_consultant_name');
            $table->enum('histopathology_quick_result', ['benign_margins_clear', 'malignant_adenocarcinoma', 'inconclusive_await_permanent'])->default('benign_margins_clear');
            $table->unsignedInteger('turnaround_total_minutes')->default(16);
            $table->boolean('telepathology_screen_shared')->default(true);
            $table->timestamps();

            $table->index(['specimen_label_code', 'patient_id'], 'frozen_spec_pat_idx');
        });

        // 14. Giám sát quá tải & mệt mỏi ca trực chống kiệt sức y bác sĩ (The Joint Commission)
        Schema::create('clinician_fatigue_circadians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('doctor_name');
            $table->decimal('shift_duration_hours', 4, 1)->default(16.0);
            $table->decimal('consecutive_surgical_hours', 4, 1)->default(6.5);
            $table->decimal('sleep_deficit_hours', 4, 1)->default(4.0);
            $table->unsignedTinyInteger('fatigue_risk_index_score')->default(45);
            $table->boolean('mandatory_rest_alert')->default(false);
            $table->string('relief_physician_assigned')->nullable();
            $table->enum('fitness_for_duty_status', ['fit_for_duty', 'fatigue_warning_moderate', 'grounded_mandatory_sleep'])->default('fit_for_duty');
            $table->timestamps();

            $table->index(['doctor_id', 'fitness_for_duty_status'], 'fatigue_doc_fit_idx');
        });

        // 15. Khu chờ xuất viện tiện nghi & cấp phát thuốc tận giường Meds-to-Beds
        Schema::create('discharge_lounge_meds_to_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('departed_ward_room', 50)->default('702-B');
            $table->boolean('bed_freed_before_10am')->default(true);
            $table->string('discharge_lounge_bay', 50)->default('LOUNGE-RECLINER-03');
            $table->string('clinical_pharmacist_name');
            $table->boolean('meds_to_beds_delivered')->default(true);
            $table->boolean('contactless_payment_settled')->default(true);
            $table->boolean('discharge_ride_scheduled')->default(true);
            $table->enum('overall_discharge_status', ['waiting_in_lounge', 'counseled_meds_received', 'discharged_transport_home'])->default('waiting_in_lounge');
            $table->timestamps();

            $table->index(['patient_id', 'overall_discharge_status'], 'disc_pat_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discharge_lounge_meds_to_beds');
        Schema::dropIfExists('clinician_fatigue_circadians');
        Schema::dropIfExists('frozen_section_logistics');
        Schema::dropIfExists('biometric_safekeeping_vaults');
        Schema::dropIfExists('hospital_incident_command_hics');
        Schema::dropIfExists('linen_rfid_lifecycle_trackings');
        Schema::dropIfExists('central_kitchen_haccps');
        Schema::dropIfExists('smart_parking_ambulance_gates');
        Schema::dropIfExists('biomedical_equipment_cmms');
        Schema::dropIfExists('smart_asset_tracking_bles');
    }
};
