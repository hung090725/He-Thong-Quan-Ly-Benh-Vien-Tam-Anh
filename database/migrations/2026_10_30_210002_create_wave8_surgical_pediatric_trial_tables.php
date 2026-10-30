<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 6. Digital Twin Khử Khuẩn Tiệt Trùng Dụng Cụ Mổ CSSD
        Schema::create('digital_twin_cssd_steriles', function (Blueprint $table) {
            $table->id();
            $table->string('tray_barcode_2d', 60)->unique(); // CSSD-TRAY-OR-XXXX
            $table->string('tray_name', 120); // Bộ đại phẫu lồng ngực, Bộ thay khớp gối Stryker...
            $table->foreignId('cssd_supervisor_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('sterilization_method', ['autoclave_steam_134c', 'hydrogen_peroxide_plasma', 'ethylene_oxide_eto'])->default('autoclave_steam_134c');
            $table->integer('sterilizer_chamber_number')->default(2);
            $table->timestamp('sterilization_cycle_completed_at')->nullable();
            $table->boolean('chemical_integrator_passed')->default(true); // Class 5 chemical indicator
            $table->boolean('biological_spore_test_passed')->default(true); // Geobacillus stearothermophilus
            $table->date('sterile_expiration_date')->nullable();
            $table->enum('tray_status', ['decontamination_washing', 'packaging', 'in_sterilizer', 'sterile_storage', 'dispatched_to_or', 'used_in_surgery'])->default('sterile_storage');
            $table->string('assigned_operating_room', 50)->nullable(); // OR 01, OR 05 Hybrid...
            $table->timestamps();

            $table->index(['tray_barcode_2d'], 'cssd_barcode_idx');
            $table->index(['tray_status', 'assigned_operating_room'], 'cssd_stat_or_idx');
        });

        // 7. Bơm Tiêm Giảm Đau Tự Kiểm Soát (PCA Smart Pump)
        Schema::create('patient_controlled_analgesias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('anesthesiologist_id')->constrained('doctors')->onDelete('cascade');
            $table->string('opioid_agent', 80); // Morphine, Fentanyl, Hydromorphone, Sufentanil
            $table->decimal('basal_infusion_rate_mg_or_mcg_per_h', 6, 2)->default(1.0);
            $table->decimal('pca_bolus_dose_mg_or_mcg', 6, 2)->default(1.0);
            $table->integer('lockout_interval_minutes')->default(8); // Khóa 6-15 phút
            $table->decimal('four_hour_dose_limit', 6, 2)->default(20.0);
            $table->integer('total_demands_count')->default(15); // Số lần bấm nút
            $table->integer('delivered_bolus_count')->default(10); // Số lần bơm thật sự
            $table->decimal('etco2_respiratory_monitoring_mmhg', 5, 2)->default(38.0); // > 50 nguy cơ suy hô hấp
            $table->decimal('spo2_monitoring_pct', 5, 2)->default(98.0);
            $table->boolean('respiratory_depression_lockout_active')->default(false);
            $table->enum('pain_score_vas_10', ['vas_0_2_mild', 'vas_3_5_moderate', 'vas_6_8_severe', 'vas_9_10_worst'])->default('vas_3_5_moderate');
            $table->timestamps();

            $table->index(['patient_id', 'opioid_agent'], 'pca_pat_opioid_idx');
        });

        // 8. Pha Chế Thuốc Hóa Trị Ung Thư Bằng Cân Trọng Khối (Gravimetric BSC)
        Schema::create('gravimetric_chemo_compoundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('pharmacist_id')->constrained('doctors')->onDelete('cascade');
            $table->string('preparation_barcode', 60)->unique();
            $table->string('chemo_drug_name', 100); // Cisplatin, Paclitaxel, Doxorubicin, Gemcitabine...
            $table->decimal('prescribed_dose_mg', 8, 2)->default(120.0);
            $table->string('carrier_diluent', 80)->default('NaCl 0.9% 500mL');
            $table->decimal('expected_total_weight_grams', 8, 2)->default(535.0);
            $table->decimal('measured_actual_weight_grams', 8, 2)->default(536.2);
            $table->decimal('weight_deviation_pct', 5, 2)->default(0.22); // Target <= +-5%
            $table->boolean('gravimetric_check_passed')->default(true);
            $table->boolean('biosafety_cabinet_hepa_certified')->default(true);
            $table->timestamp('compounded_at')->nullable();
            $table->timestamp('beyond_use_date_time')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'chemo_drug_name'], 'chemo_pat_drug_idx');
        });

        // 9. Giám Sát Tổn Thương Thần Kinh Trong Mổ (IONM)
        Schema::create('intraoperative_neuromonitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('neurophysiologist_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('surgical_type', ['scoliosis_spinal_deformity', 'spinal_cord_tumor', 'thyroid_recurrent_nerve', 'brainstem_craniotomy'])->default('scoliosis_spinal_deformity');
            $table->decimal('mep_amplitude_uv', 8, 2)->default(350.0); // Motor Evoked Potential
            $table->decimal('mep_amplitude_drop_pct', 5, 2)->default(12.0); // > 50% = Cảnh báo tổn thương vận động
            $table->decimal('ssep_latency_ms', 6, 2)->default(22.5); // Somatosensory Latency
            $table->decimal('ssep_latency_increase_pct', 5, 2)->default(4.0); // > 10% = Cảnh báo cảm giác
            $table->boolean('emg_spontaneous_train_activity')->default(false); // Co giật cơ do căng dây thần kinh
            $table->boolean('neuromonitoring_alert_triggered')->default(false);
            $table->text('surgical_corrective_action_taken')->nullable(); // Nới ốc vít, nâng huyết áp MAP...
            $table->boolean('postop_neurological_deficit')->default(false);
            $table->timestamps();

            $table->index(['patient_id', 'surgical_type'], 'ionm_pat_surg_idx');
        });

        // 10. Giám Sát Thiết Bị Hỗ Trợ Thất Trái Cơ Học (LVAD HeartMate 3)
        Schema::create('lvad_mechanical_circulatories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('cardiologist_id')->constrained('doctors')->onDelete('cascade');
            $table->string('lvad_serial_number', 50);
            $table->integer('pump_speed_rpm')->default(5400); // 5000-6000 RPM
            $table->decimal('pump_flow_l_per_min', 5, 2)->default(4.8); // 3.5 - 6.0 L/min
            $table->decimal('pump_power_watts', 5, 2)->default(4.5); // 3.5 - 6.0 W
            $table->decimal('pulsatility_index_pi', 5, 2)->default(4.2); // 2.0 - 6.0
            $table->boolean('pump_thrombosis_suspected')->default(false); // Lưu lượng và công suất tăng vọt
            $table->boolean('suction_event_detected')->default(false); // Hút dính vách tim làm PI tụt dốc
            $table->boolean('driveline_infection_alert')->default(false);
            $table->decimal('inr_anticoagulation_target', 4, 2)->default(2.5); // 2.0 - 3.0 với Warfarin
            $table->text('lvad_diagnostic_summary')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'pump_thrombosis_suspected'], 'lvad_pat_thromb_idx');
        });

        // 11. An Ninh Sơ Sinh Định Vị RTLS & Chống Nhầm / Bắt Cóc Trẻ Em
        Schema::create('infant_security_rtls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infant_patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('mother_patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('infant_rtls_tag_id', 50)->unique();
            $table->string('mother_rtls_tag_id', 50);
            $table->boolean('skin_tamper_sensor_intact')->default(true); // Cắt vòng tay -> Alarm ngay
            $table->boolean('mother_infant_dual_pairing_verified')->default(true);
            $table->string('current_floor_room', 100)->default('Khoa Sản Tầng 4 - Phòng 402');
            $table->decimal('distance_to_nearest_exit_meters', 6, 2)->default(25.0);
            $table->boolean('geofence_perimeter_breach_alarm')->default(false);
            $table->boolean('elevator_emergency_lockdown_triggered')->default(false);
            $table->enum('infant_safety_status', ['safe_in_room', 'transport_with_mother', 'geofence_alert', 'tamper_cut_alarm'])->default('safe_in_room');
            $table->timestamps();

            $table->index(['infant_rtls_tag_id'], 'rtls_infant_tag_idx');
            $table->index(['mother_patient_id', 'infant_safety_status'], 'rtls_mom_status_idx');
        });

        // 12. Bản Đồ Áp Lực Giường Nệm & Ngừa Loét Tì Đè (Braden Smart Bed)
        Schema::create('braden_smart_bed_pressures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('head_nurse_id')->constrained('doctors')->onDelete('cascade');
            $table->integer('braden_sensory_perception')->default(3); // 1-4
            $table->integer('braden_moisture')->default(3); // 1-4
            $table->integer('braden_activity')->default(2); // 1-4
            $table->integer('braden_mobility')->default(2); // 1-4
            $table->integer('braden_nutrition')->default(2); // 1-4
            $table->integer('braden_friction_shear')->default(2); // 1-3
            $table->integer('total_braden_score')->default(14); // 6 - 23, <= 12 là nguy cơ cao
            $table->decimal('sacrum_peak_pressure_mmhg', 6, 2)->default(28.0); // Áp lực xương cùng
            $table->decimal('heels_peak_pressure_mmhg', 6, 2)->default(22.0);
            $table->decimal('bed_head_elevation_degrees', 4, 1)->default(25.0); // <= 30 độ để giảm trượt xé
            $table->integer('last_repositioning_elapsed_minutes')->default(45); // Tối đa 120 phút
            $table->boolean('turn_reminder_due_alert')->default(false);
            $table->enum('decubitus_ulcer_risk_level', ['low_risk', 'moderate_risk', 'high_risk', 'very_high_risk'])->default('moderate_risk');
            $table->timestamps();

            $table->index(['patient_id', 'decubitus_ulcer_risk_level'], 'bed_pat_risk_idx');
        });

        // 13. Ngân Hàng Sữa Mẹ Tiệt Trùng & Dinh Dưỡng Trẻ Sinh Non (DHMB)
        Schema::create('donor_human_milk_banks', function (Blueprint $table) {
            $table->id();
            $table->string('milk_batch_code', 60)->unique(); // DHMB-BATCH-2026-XXXX
            $table->string('anonymous_donor_code', 50); // DONOR-XXXX
            $table->foreignId('dhmb_technician_id')->constrained('doctors')->onDelete('cascade');
            $table->decimal('batch_volume_ml', 8, 2)->default(1500.0);
            $table->timestamp('pasteurization_holder_completed_at')->nullable(); // 62.5C trong 30p
            $table->boolean('pre_pasteurization_culture_passed')->default(true);
            $table->boolean('post_pasteurization_culture_sterile')->default(true); // 100% âm tính
            $table->decimal('energy_kcal_per_100ml', 5, 2)->default(67.5);
            $table->decimal('protein_grams_per_100ml', 4, 2)->default(1.25);
            $table->decimal('fat_grams_per_100ml', 4, 2)->default(3.8);
            $table->date('expiration_frozen_date')->nullable(); // -20C hạn 3-6 tháng
            $table->foreignId('assigned_recipient_infant_id')->nullable()->constrained('patient_profiles')->onDelete('set null');
            $table->enum('milk_batch_status', ['quarantine_testing', 'pasteurized_approved', 'dispensed_to_nicu', 'discarded'])->default('pasteurized_approved');
            $table->timestamps();

            $table->index(['milk_batch_code'], 'dhmb_batch_idx');
            $table->index(['milk_batch_status', 'assigned_recipient_infant_id'], 'dhmb_stat_nicu_idx');
        });

        // 14. An Toàn Bức Xạ Y Học Hạt Nhân & PET/CT (IAEA Radiation)
        Schema::create('nuclear_medicine_radiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('radiation_safety_officer_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('radiopharmaceutical', ['f18_fludeoxyglucose', 'tc99m_sestamibi', 'i131_sodium_iodide', 'ga68_dotatate', 'lu177_psma'])->default('f18_fludeoxyglucose');
            $table->decimal('injected_activity_mbq', 8, 2)->default(250.0); // Megabecquerel
            $table->timestamp('injection_timestamp')->nullable();
            $table->decimal('isotope_half_life_minutes', 8, 2)->default(109.8); // F-18 = 109.8p
            $table->decimal('decayed_residual_activity_mbq', 8, 2)->default(62.5); // Tự tính theo thời gian
            $table->decimal('staff_personal_dosimeter_rate_usv_h', 6, 2)->default(4.2); // MicroSievert/h
            $table->decimal('patient_surface_radiation_rate_usv_h', 6, 2)->default(18.5);
            $table->boolean('safe_for_discharge_home')->default(false); // Chuẩn < 20 uSv/h ở 1 mét
            $table->text('radioprotection_instructions')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'radiopharmaceutical'], 'nuc_pat_radio_idx');
        });

        // 15. Quản Lý Thử Nghiệm Lâm Sàng & Hội Đồng Đạo Đức Y Sinh (CTMS & IRB)
        Schema::create('clinical_trial_managements', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code', 60); // TA-CTMS-2026-ONCO-01
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('principal_investigator_id')->constrained('doctors')->onDelete('cascade');
            $table->string('trial_title', 200);
            $table->enum('study_phase', ['phase_1', 'phase_2', 'phase_3', 'phase_4_post_market'])->default('phase_3');
            $table->enum('randomization_arm', ['investigational_drug_arm', 'active_comparator_arm', 'placebo_blinded_arm'])->default('investigational_drug_arm');
            $table->boolean('electronic_informed_consent_signed')->default(true);
            $table->integer('study_visit_number')->default(1);
            $table->decimal('protocol_adherence_rate_pct', 5, 2)->default(98.5);
            $table->boolean('serious_adverse_event_sae_occurred')->default(false);
            $table->timestamp('sae_reported_to_irb_within_24h_at')->nullable();
            $table->enum('subject_status', ['enrolled_active', 'completed_protocol', 'withdrawn_consent', 'discontinued_adverse_event'])->default('enrolled_active');
            $table->text('gcp_compliance_audit_notes')->nullable();
            $table->timestamps();

            $table->index(['protocol_code', 'patient_id'], 'ctms_proto_pat_idx');
            $table->index(['serious_adverse_event_sae_occurred'], 'ctms_sae_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_trial_managements');
        Schema::dropIfExists('nuclear_medicine_radiations');
        Schema::dropIfExists('donor_human_milk_banks');
        Schema::dropIfExists('braden_smart_bed_pressures');
        Schema::dropIfExists('infant_security_rtls');
        Schema::dropIfExists('lvad_mechanical_circulatories');
        Schema::dropIfExists('intraoperative_neuromonitorings');
        Schema::dropIfExists('gravimetric_chemo_compoundings');
        Schema::dropIfExists('patient_controlled_analgesias');
        Schema::dropIfExists('digital_twin_cssd_steriles');
    }
};
