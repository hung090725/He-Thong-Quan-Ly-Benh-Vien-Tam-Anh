<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Radiation Oncology 4D-CT & Linac DIBH
        if (!Schema::hasTable('radiation_oncology_plans')) {
            Schema::create('radiation_oncology_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('plan_code', 50)->comment('Mã kế hoạch xạ trị');
                $table->string('cancer_site', 100)->comment('Vị trí u: Phổi, Vú trái, Gan, Tiền liệt tuyến, Não');
                $table->enum('radiation_technique', ['imrt', 'vmat', 'sbrt', 'srs', '3d_crt'])->default('vmat');
                $table->decimal('total_prescribed_dose_gy', 6, 2)->comment('Tổng liều chỉ định Gray (Gy)');
                $table->integer('fraction_count')->default(30)->comment('Số phân liều (Fractions)');
                $table->decimal('dose_per_fraction_gy', 5, 2)->comment('Liều mỗi phân liều Gy');
                $table->boolean('dibh_breath_hold_used')->default(false)->comment('Kỹ thuật nín thở hít sâu DIBH bảo vệ tim');
                $table->boolean('four_d_ct_motion_tracked')->default(false)->comment('Theo dõi chuyển động hô hấp 4D-CT');
                $table->decimal('heart_mean_dose_gy', 5, 2)->nullable()->comment('Liều trung bình cơ quan lành OAR: Thân tim');
                $table->decimal('spinal_cord_max_dose_gy', 5, 2)->nullable()->comment('Liều tối đa OAR: Tủy sống');
                $table->boolean('oar_tolerance_verified')->default(true)->comment('Đạt chuẩn an toàn cơ quan nguy cấp OAR');
                $table->enum('treatment_status', ['planning', 'quality_assurance_passed', 'in_treatment', 'completed'])->default('planning');
                $table->timestamps();
                $table->index(['patient_id', 'treatment_status'], 'rad_pat_status_idx');
            });
        }

        // 2. CAR-T Cell Therapy & CRS Protocol
        if (!Schema::hasTable('car_t_cell_therapies')) {
            Schema::create('car_t_cell_therapies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('cart_product_name', 100)->comment('Sản phẩm CAR-T: Tisagenlecleucel, Axicabtagene ciloleucel');
                $table->string('target_antigen', 50)->default('CD19')->comment('Kháng nguyên đích: CD19, BCMA');
                $table->timestamp('infusion_date')->nullable()->comment('Ngày giờ truyền tế bào CAR-T');
                $table->decimal('peak_temperature_c', 4, 1)->default(37.0)->comment('Thân nhiệt đỉnh (°C)');
                $table->decimal('systolic_bp', 5, 1)->default(120)->comment('Huyết áp tâm thu mmHg');
                $table->decimal('spo2_pct', 4, 1)->default(98)->comment('SpO2 %');
                $table->enum('astct_crs_grade', ['grade_0_none', 'grade_1_fever_only', 'grade_2_hypotension_fluids', 'grade_3_vasopressors_high_o2', 'grade_4_mechanical_ventilation'])->default('grade_0_none');
                $table->enum('icans_neurotoxicity_grade', ['grade_0', 'grade_1', 'grade_2', 'grade_3', 'grade_4'])->default('grade_0');
                $table->boolean('tocilizumab_indicated')->default(false)->comment('Chỉ định thuốc kháng thụ thể IL-6 Tocilizumab');
                $table->integer('tocilizumab_doses_given')->default(0);
                $table->boolean('dexamethasone_given')->default(false);
                $table->enum('therapy_outcome', ['monitoring', 'complete_remission', 'partial_response', 'relapsed', 'fatal_toxicity'])->default('monitoring');
                $table->timestamps();
                $table->index(['patient_id', 'astct_crs_grade'], 'cart_pat_crs_idx');
            });
        }

        // 3. ECMO & Targeted Temperature Management (TTM)
        if (!Schema::hasTable('ecmo_ttm_sessions')) {
            Schema::create('ecmo_ttm_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('ecmo_modality', ['va_ecmo_venoarterial', 'vv_ecmo_venovenous', 'vav_hybrid'])->default('va_ecmo_venoarterial');
                $table->decimal('pump_flow_l_per_min', 4, 2)->default(3.5)->comment('Lưu lượng bơm máu L/phút');
                $table->integer('pump_speed_rpm')->default(3200)->comment('Tốc độ vòng quay RPM');
                $table->decimal('sweep_gas_flow_l_per_min', 4, 1)->default(4.0)->comment('Dòng khí quét L/phút');
                $table->decimal('fio2_sweep_pct', 4, 1)->default(100.0)->comment('Nồng độ oxy khí quét %');
                $table->decimal('delta_pressure_membrane_mmhg', 5, 1)->default(35.0)->comment('Chênh áp màng Delta P mmHg');
                $table->boolean('membrane_thrombosis_risk')->default(false)->comment('Cảnh báo nguy cơ đông màng oxy hóa');
                $table->boolean('ttm_targeted_temperature_active')->default(false)->comment('Kích hoạt hạ thân nhiệt chỉ huy TTM');
                $table->decimal('target_core_temp_c', 4, 1)->default(33.0)->comment('Thân nhiệt mục tiêu (°C)');
                $table->decimal('actual_core_temp_c', 4, 1)->default(33.2)->comment('Thân nhiệt thực tế đo đầu dò bàng quang/thực quản');
                $table->enum('session_status', ['running', 'weaning', 'decannulated_success', 'mortality'])->default('running');
                $table->timestamps();
                $table->index(['patient_id', 'session_status'], 'ecmo_pat_status_idx');
            });
        }

        // 4. Negative Pressure Isolation Room (AIIR) & Air Exchange (ACH)
        if (!Schema::hasTable('negative_pressure_aiirs')) {
            Schema::create('negative_pressure_aiirs', function (Blueprint $table) {
                $table->id();
                $table->string('room_code', 50)->unique()->comment('Mã phòng áp lực âm: AIIR-01');
                $table->foreignId('patient_id')->nullable()->constrained('patient_profiles')->nullOnDelete();
                $table->string('pathogen_isolation_type', 100)->comment('Tác nhân: Lao đa kháng MDR-TB, Covid-19, Cúm A/H5N1, Đậu mùa khỉ');
                $table->decimal('pressure_differential_pa', 6, 2)->default(-8.5)->comment('Chênh lệch áp suất so với hành lang Pa (chuẩn <= -2.5 Pa)');
                $table->decimal('air_changes_per_hour_ach', 5, 1)->default(14.5)->comment('Số lần đổi khí mỗi giờ ACH (chuẩn >= 12 ACH)');
                $table->boolean('hepa_filter_exhaust_passed')->default(true)->comment('Màng lọc HEPA H14 khí thải đạt chuẩn');
                $table->boolean('airlock_doors_interlocked')->default(true)->comment('Khóa liên động cửa buồng đệm Anteroom an toàn');
                $table->boolean('pressure_failure_alarm')->default(false)->comment('Cảnh báo mất áp lực âm');
                $table->boolean('uvc_disinfection_active')->default(false)->comment('Hệ thống khử khuẩn UV-C trong buồng');
                $table->timestamps();
                $table->index(['room_code', 'pressure_failure_alarm'], 'aiir_room_alarm_idx');
            });
        }

        // 5. Precision Antibiotic PK/PD & AUC/MIC Bayesian Modeling
        if (!Schema::hasTable('precision_antibiotic_pk_pds')) {
            Schema::create('precision_antibiotic_pk_pds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('antibiotic_name', 100)->comment('Tên kháng sinh: Vancomycin, Meropenem, Colistin');
                $table->string('pathogen_name', 150)->comment('Vi khuẩn phân lập: MRSA, K. pneumoniae KPC, Acinetobacter baumannii');
                $table->decimal('mic_mg_per_l', 6, 3)->default(1.0)->comment('Nồng độ ức chế tối thiểu MIC mg/L');
                $table->decimal('current_serum_trough_mg_per_l', 6, 2)->nullable()->comment('Nồng độ đáy đo được mg/L');
                $table->decimal('current_serum_peak_mg_per_l', 6, 2)->nullable()->comment('Nồng độ đỉnh mg/L');
                $table->decimal('estimated_auc24_mg_h_per_l', 7, 2)->nullable()->comment('Diện tích dưới đường cong AUC24');
                $table->decimal('auc24_over_mic_ratio', 7, 2)->nullable()->comment('Tỷ lệ AUC24/MIC');
                $table->boolean('auc_target_achieved')->default(false)->comment('Đạt mục tiêu AUC/MIC (400-600 cho Vancomycin)');
                $table->decimal('time_above_mic_pct', 5, 2)->nullable()->comment('% Thời gian vượt MIC cho nhóm Beta-lactam');
                $table->string('bayesian_recommended_dose', 200)->nullable()->comment('Khuyến nghị liều tối ưu của mô hình Bayesian');
                $table->boolean('nephrotoxicity_risk_alert')->default(false)->comment('Cảnh báo độc thận');
                $table->timestamps();
                $table->index(['patient_id', 'antibiotic_name'], 'pkpd_pat_drug_idx');
            });
        }

        // 6. Robotic Exoskeleton Neuro-Rehabilitation
        if (!Schema::hasTable('robotic_exoskeleton_rehabs')) {
            Schema::create('robotic_exoskeleton_rehabs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('therapist_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('exoskeleton_device_model', 100)->default('EksoNR Robotic Gait')->comment('Dòng máy Robot khung xương ngoài');
                $table->integer('session_duration_minutes')->default(45);
                $table->integer('total_steps_achieved')->default(0)->comment('Tổng số bước chân đạt được trong phiên');
                $table->decimal('distance_meters', 6, 1)->default(0)->comment('Quãng đường di chuyển (m)');
                $table->integer('assistance_level_pct')->default(60)->comment('Mức độ robot trợ lực (10-100%)');
                $table->decimal('hip_rom_degrees', 4, 1)->default(30.0)->comment('Biên độ góc khớp háng ROM (độ)');
                $table->decimal('knee_rom_degrees', 4, 1)->default(50.0)->comment('Biên độ góc khớp gối ROM (độ)');
                $table->decimal('gait_symmetry_index_pct', 5, 2)->default(85.0)->comment('Chỉ số đối xứng dáng đi % (tiêu chuẩn >= 90%)');
                $table->text('therapist_clinical_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'created_at'], 'exo_pat_date_idx');
            });
        }

        // 7. Molecular Tumor Board (MTB) & Virtual Oncology Consensus
        if (!Schema::hasTable('molecular_tumor_boards')) {
            Schema::create('molecular_tumor_boards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('chairperson_doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('board_session_code', 50)->comment('Mã phiên hội đồng MTB');
                $table->string('primary_cancer_diagnosis', 200)->comment('Chẩn đoán ung thư nguyên phát');
                $table->json('actionable_mutations_json')->nullable()->comment('Danh sách đột biến gen đích tìm thấy');
                $table->string('tumor_mutational_burden_tmb', 50)->nullable()->comment('Tải lượng đột biến u TMB (mut/Mb)');
                $table->enum('msi_status', ['msi_high', 'msi_stable', 'undetermined'])->default('msi_stable');
                $table->text('off_label_or_trial_therapies_proposed')->nullable()->comment('Thuốc ngoài chỉ định hoặc thử nghiệm lâm sàng đề xuất');
                $table->enum('mtb_consensus_decision', ['unanimous_approved', 'majority_approved', 'further_genomic_testing_needed', 'palliative_comfort_care'])->default('unanimous_approved');
                $table->text('consensus_rational_summary')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'mtb_consensus_decision'], 'mtb_pat_dec_idx');
            });
        }

        // 8. Awake Craniotomy & Intraoperative Brain Mapping
        if (!Schema::hasTable('awake_craniotomy_mappings')) {
            Schema::create('awake_craniotomy_mappings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('neurosurgeon_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('tumor_eloquent_location', 150)->comment('Vị trí u vùng não chức năng: Vùng Broca, Vùng Wernicke, Vỏ não vận động M1');
                $table->enum('anesthesia_technique', ['asleep_awake_asleep_aaa', 'monitored_anesthesia_care_mac'])->default('asleep_awake_asleep_aaa');
                $table->decimal('cortical_stimulation_current_ma', 4, 2)->default(2.5)->comment('Cường độ dòng điện kích thích vỏ não mA');
                $table->boolean('speech_arrest_detected')->default(false)->comment('Phát hiện hiện tượng ngừng ngôn ngữ khi kích thích');
                $table->boolean('motor_twitching_detected')->default(false)->comment('Phát hiện co giật cơ vận động');
                $table->decimal('safety_margin_to_eloquent_cortex_mm', 4, 1)->default(10.0)->comment('Khoảng cách an toàn đến vùng chức năng (mm)');
                $table->decimal('extent_of_resection_pct', 5, 2)->default(95.0)->comment('Tỷ lệ cắt bỏ khối u %');
                $table->boolean('intraoperative_seizure_occurred')->default(false)->comment('Có cơn động kinh trong mổ');
                $table->text('postop_neurological_status')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'speech_arrest_detected'], 'awake_pat_speech_idx');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('awake_craniotomy_mappings');
        Schema::dropIfExists('molecular_tumor_boards');
        Schema::dropIfExists('robotic_exoskeleton_rehabs');
        Schema::dropIfExists('precision_antibiotic_pk_pds');
        Schema::dropIfExists('negative_pressure_aiirs');
        Schema::dropIfExists('ecmo_ttm_sessions');
        Schema::dropIfExists('car_t_cell_therapies');
        Schema::dropIfExists('radiation_oncology_plans');
    }
};
