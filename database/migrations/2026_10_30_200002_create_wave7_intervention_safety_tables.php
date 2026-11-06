<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 9. Advanced Endoscopy Procedures (ESD / POEM / ERCP)
        if (!Schema::hasTable('advanced_endoscopy_procedures')) {
            Schema::create('advanced_endoscopy_procedures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('endoscopist_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('procedure_category', ['esd_endoscopic_submucosal_dissection', 'poem_peroral_endoscopic_myotomy', 'ercp_complex_biliary', 'eus_guided_drainage'])->default('esd_endoscopic_submucosal_dissection');
                $table->string('lesion_organ_location', 100)->comment('Vị trí tổn thương: Dạ dày hang vị, Thực quản 1/3 dưới, Đại tràng sigma');
                $table->decimal('lesion_size_mm', 5, 1)->default(25.0)->comment('Kích thước tổn thương mm');
                $table->boolean('en_bloc_resection_achieved')->default(true)->comment('Cắt trọn khối (En-bloc resection)');
                $table->boolean('r0_clear_margins_confirmed')->default(true)->comment('Bờ diện cắt âm tính mô bệnh học R0');
                $table->enum('asge_difficulty_grade', ['grade_1_standard', 'grade_2_advanced', 'grade_3_expert', 'grade_4_highly_complex'])->default('grade_2_advanced');
                $table->boolean('prophylactic_pancreatic_stent_placed')->default(false)->comment('Đặt Stent tụy dự phòng viêm tụy sau ERCP');
                $table->boolean('rectal_indomethacin_administered')->default(true)->comment('Dùng Indomethacin đặt hậu môn dự phòng PEP');
                $table->boolean('perforation_or_bleeding_occurred')->default(false)->comment('Biến chứng thủng hoặc chảy máu can thiệp');
                $table->timestamps();
                $table->index(['patient_id', 'procedure_category'], 'endo_pat_proc_idx');
            });
        }

        // 10. CIED Remote Tele-Cardiology (Pacemaker, ICD, CRT)
        if (!Schema::hasTable('cied_remote_monitorings')) {
            Schema::create('cied_remote_monitorings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('cardiologist_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('device_type', ['pacemaker_single_dual', 'icd_implantable_defibrillator', 'crt_d_biventricular', 'subcutaneous_icd'])->default('icd_implantable_defibrillator');
                $table->string('device_serial_number', 50)->comment('Mã số thiết bị cấy ghép');
                $table->decimal('battery_longevity_remaining_years', 4, 1)->default(6.5)->comment('Tuổi thọ pin còn lại (năm)');
                $table->decimal('lead_impedance_ohms', 6, 1)->default(550.0)->comment('Trở kháng dây điện cực (Ohms)');
                $table->integer('ventricular_tachycardia_episodes_30d')->default(0)->comment('Số cơn nhanh thất VT trong 30 ngày');
                $table->integer('shocks_delivered_appropriate')->default(0)->comment('Số lần sốc phá rung thích hợp');
                $table->integer('shocks_delivered_inappropriate')->default(0)->comment('Số lần sốc phá rung không thích hợp (cảnh báo)');
                $table->boolean('electrical_storm_alert')->default(false)->comment('Cảnh báo cơn bão điện học VT/VF Storm');
                $table->boolean('lead_fracture_risk')->default(false)->comment('Nguy cơ nứt vỡ dây điện cực');
                $table->text('remote_transmission_summary')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'electrical_storm_alert'], 'cied_pat_storm_idx');
            });
        }

        // 11. Antibiogram Radar & MDRO Hospital-Acquired Infection (HAI)
        if (!Schema::hasTable('antibiogram_hai_surveillances')) {
            Schema::create('antibiogram_hai_surveillances', function (Blueprint $table) {
                $table->id();
                $table->string('department_name', 100)->comment('Khoa phòng: Hồi sức tích cực, Ngoại thần kinh, Bỏng, Sơ sinh');
                $table->string('pathogen_name', 150)->comment('Tên chủng vi khuẩn: MRSA, VRE, Klebsiella pneumoniae, Pseudomonas aeruginosa');
                $table->string('antibiotic_tested', 100)->comment('Kháng sinh thử nghiệm: Colistin, Meropenem, Vancomycin, Linezolid');
                $table->integer('total_isolates_tested')->default(50)->comment('Tổng số chủng vi khuẩn phân lập được (chuẩn >= 30 theo CLSI)');
                $table->decimal('susceptibility_pct', 5, 2)->default(75.0)->comment('Tỷ lệ nhạy cảm kháng sinh %');
                $table->enum('resistance_classification', ['pan_susceptible', 'mdr_multidrug_resistant', 'xdr_extensively_drug_resistant', 'pdr_pandrug_resistant'])->default('mdr_multidrug_resistant');
                $table->boolean('outbreak_cluster_alert')->default(false)->comment('Cảnh báo nguy cơ chùm ca nhiễm khuẩn bệnh viện (Outbreak)');
                $table->string('hai_infection_site', 50)->default('clabsi')->comment('Vị trí nhiễm khuẩn: CLABSI, CAUTI, VAP, SSI');
                $table->timestamps();
                $table->index(['department_name', 'outbreak_cluster_alert'], 'anti_dept_alert_idx');
            });
        }

        // 12. Spontaneous Breathing Trial (SBT) & Extubation Readiness
        if (!Schema::hasTable('extubation_readiness_sbts')) {
            Schema::create('extubation_readiness_sbts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->integer('sbt_duration_minutes')->default(30)->comment('Thời gian thử nghiệm thở tự nhiên (30 - 120 phút)');
                $table->decimal('respiratory_rate_f_bpm', 4, 1)->default(22.0)->comment('Tần số thở (nhịp/phút)');
                $table->decimal('tidal_volume_vt_liters', 4, 3)->default(0.420)->comment('Thể tích khí lưu thông L');
                $table->decimal('rsbi_score', 6, 2)->default(52.4)->comment('Chỉ số thở nhanh nông RSBI = f / VT (chuẩn < 105)');
                $table->decimal('maximal_inspiratory_pressure_mip', 5, 1)->default(-28.0)->comment('Áp lực hít vào tối đa MIP cmH2O (chuẩn < -20 cmH2O)');
                $table->boolean('cuff_leak_test_positive')->default(true)->comment('Nghiệm pháp rò bóng chèn đạt chuẩn (Loại trừ phù thanh môn)');
                $table->enum('sbt_outcome', ['passed_ready_for_extubation', 'failed_respiratory_muscle_fatigue', 'failed_hemodynamic_instability'])->default('passed_ready_for_extubation');
                $table->boolean('extubation_successful_no_reintubation_48h')->default(true)->comment('Rút nội khí quản thành công không cần đặt lại sau 48h');
                $table->timestamps();
                $table->index(['patient_id', 'sbt_outcome'], 'sbt_pat_outcome_idx');
            });
        }

        // 13. Trauma Resuscitation ISS & Trauma Team Level 1 Activation
        if (!Schema::hasTable('trauma_resuscitation_isses')) {
            Schema::create('trauma_resuscitation_isses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('trauma_leader_id')->constrained('doctors')->cascadeOnDelete();
                $table->integer('ais_head_neck')->default(0)->comment('Điểm tổn thương đầu cổ AIS (0-6)');
                $table->integer('ais_face')->default(0)->comment('Mặt AIS (0-6)');
                $table->integer('ais_chest')->default(0)->comment('Ngực AIS (0-6)');
                $table->integer('ais_abdomen_pelvis')->default(0)->comment('Bụng / Chậu AIS (0-6)');
                $table->integer('ais_extremities_pelvic_girdle')->default(0)->comment('Tứ chi AIS (0-6)');
                $table->integer('ais_external_burns')->default(0)->comment('Bề mặt / Bỏng AIS (0-6)');
                $table->integer('calculated_iss_score')->default(0)->comment('Thang điểm tổn thương ISS (Tổng bình phương 3 vùng nặng nhất, max 75)');
                $table->decimal('revised_trauma_score_rts', 5, 3)->default(7.841)->comment('Điểm sinh hiệu chấn thương RTS (0-7.841)');
                $table->boolean('trauma_level_1_activated')->default(false)->comment('Kích hoạt kíp chấn thương khẩn cấp cấp độ 1 (ISS >= 16)');
                $table->decimal('predicted_survival_triss_pct', 5, 2)->default(90.0)->comment('Xác suất sống sót TRISS %');
                $table->text('trauma_interventions_summary')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'trauma_level_1_activated'], 'trauma_pat_act_idx');
            });
        }

        // 14. CRRT Circuit Safety & Regional Citrate Anticoagulation (RCA)
        if (!Schema::hasTable('crrt_citrate_anticoagulations')) {
            Schema::create('crrt_citrate_anticoagulations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('crrt_modality', ['cvvhdf_hemodiafiltration', 'cvvh_hemofiltration', 'cvvhd_hemodialysis'])->default('cvvhdf_hemodiafiltration');
                $table->decimal('effluent_dose_ml_per_kg_per_h', 4, 1)->default(25.0)->comment('Liều dịch lọc thay thế mL/kg/h (chuẩn 20-25)');
                $table->decimal('transmembrane_pressure_tmp_mmhg', 5, 1)->default(120.0)->comment('Áp lực xuyên màng TMP mmHg');
                $table->decimal('filter_clotting_risk_pct', 4, 1)->default(15.0)->comment('Nguy cơ đông màng lọc %');
                $table->decimal('pre_filter_citrate_infusion_mmol_per_l', 4, 2)->default(3.0)->comment('Liều truyền Trisodium Citrate trước màng mmol/L');
                $table->decimal('post_filter_ionized_calcium_mmol_per_l', 4, 2)->default(0.30)->comment('Canxi ion hóa sau màng mmol/L (Mục tiêu 0.25 - 0.35)');
                $table->decimal('systemic_ionized_calcium_mmol_per_l', 4, 2)->default(1.15)->comment('Canxi ion hóa toàn thân người bệnh mmol/L (Mục tiêu 1.1 - 1.3)');
                $table->decimal('calcium_chloride_compensation_rate_ml_per_h', 5, 1)->default(12.0)->comment('Tốc độ bù Canxi Clorid 10% mL/h');
                $table->boolean('citrate_accumulation_acidosis_alert')->default(false)->comment('Cảnh báo tích lũy Citrate gây toan chuyển hóa');
                $table->timestamps();
                $table->index(['patient_id', 'crrt_modality'], 'crrt_pat_mod_idx');
            });
        }

        // 15. ICU ABCDEF Bundle & PICS Prevention
        if (!Schema::hasTable('icu_abcdef_bundle_pics')) {
            Schema::create('icu_abcdef_bundle_pics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->boolean('bundle_a_pain_assessed_controlled')->default(true)->comment('A: Đánh giá đau CPOT/BPS được kiểm soát');
                $table->boolean('bundle_b_both_sat_sbt_performed')->default(true)->comment('B: Thực hiện ngắt an thần SAT & thở tự nhiên SBT');
                $table->string('bundle_c_sedative_choice', 100)->default('Dexmedetomidine')->comment('C: Thuốc an thần lựa chọn (tránh Benzodiazepine)');
                $table->enum('bundle_d_cam_icu_delirium', ['negative_clear', 'positive_delirium_present'])->default('negative_clear')->comment('D: Sàng lọc mê sảng CAM-ICU');
                $table->enum('bundle_e_early_mobility_level', ['level_1_passive_bed', 'level_2_active_sitting_edge', 'level_3_standing_transfer', 'level_4_walking_gait'])->default('level_2_active_sitting_edge')->comment('E: Mức độ vận động sớm');
                $table->boolean('bundle_f_family_engagement_diary_active')->default(true)->comment('F: Nhật ký ICU Diary & Thăm nuôi thân nhân');
                $table->decimal('pics_post_intensive_care_risk_score', 4, 1)->default(20.0)->comment('Nguy cơ suy yếu sau hồi sức PICS %');
                $table->text('multidisciplinary_icu_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'bundle_d_cam_icu_delirium'], 'icu_pat_cam_idx');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('icu_abcdef_bundle_pics');
        Schema::dropIfExists('crrt_citrate_anticoagulations');
        Schema::dropIfExists('trauma_resuscitation_isses');
        Schema::dropIfExists('extubation_readiness_sbts');
        Schema::dropIfExists('antibiogram_hai_surveillances');
        Schema::dropIfExists('cied_remote_monitorings');
        Schema::dropIfExists('advanced_endoscopy_procedures');
    }
};
