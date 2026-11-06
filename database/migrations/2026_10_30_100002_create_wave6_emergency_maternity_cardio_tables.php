<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 8. Tele-EMS 5G Mobile Pre-hospital Triage
        if (!Schema::hasTable('tele_ems_missions')) {
            Schema::create('tele_ems_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->nullable()->constrained('patient_profiles')->nullOnDelete();
                $table->string('ambulance_callsign', 50)->comment('Biển hiệu xe cấp cứu');
                $table->string('paramedic_in_charge', 100)->comment('Điều dưỡng/Cứu thương phụ trách');
                $table->string('location_gps', 100)->nullable()->comment('Tọa độ GPS hiện tại');
                $table->decimal('eta_hospital_minutes', 4, 1)->default(0)->comment('Thời gian dự kiến về viện (phút)');
                $table->integer('heart_rate')->nullable();
                $table->decimal('sbp', 5, 1)->nullable();
                $table->decimal('dbp', 5, 1)->nullable();
                $table->decimal('spo2', 5, 1)->nullable();
                $table->text('ecg_12lead_finding')->nullable()->comment('Kết luận ECG 12 chuyển đạo truyền về');
                $table->boolean('efast_ultrasound_positive')->default(false)->comment('Siêu âm eFAST phát hiện dịch tự do ổ bụng/màng phổi');
                $table->enum('pre_activation_target', ['none', 'cathlab_stemi', 'stroke_team', 'trauma_or', 'icu'])->default('none');
                $table->boolean('hospital_team_ready')->default(false)->comment('Kíp bệnh viện đã sẵn sàng đón');
                $table->enum('mission_status', ['en_route_scene', 'on_scene', 'transporting', 'arrived_hospital', 'cancelled'])->default('transporting');
                $table->timestamps();
                $table->index(['ambulance_callsign', 'mission_status']);
            });
        }

        // 9. Mass Casualty Incident (MCI) & START Triage
        if (!Schema::hasTable('mass_casualty_triages')) {
            Schema::create('mass_casualty_triages', function (Blueprint $table) {
                $table->id();
                $table->string('incident_name', 150)->comment('Tên thảm họa / biến cố: Cháy nổ, Tai nạn giao thông liên hoàn, etc.');
                $table->string('disaster_code', 50)->comment('Mã thảm họa');
                $table->string('victim_tag_number', 50)->unique()->comment('Số thẻ nhận diện nạn nhân tại hiện trường');
                $table->boolean('can_walk')->default(false)->comment('Có thể tự đi lại được');
                $table->integer('respiratory_rate')->nullable()->comment('Nhịp thở /phút');
                $table->boolean('radial_pulse_palpable')->default(true)->comment('Bắt được mạch quay');
                $table->enum('mental_status', ['follows_commands', 'does_not_follow'])->default('follows_commands');
                $table->enum('start_triage_color', ['red_immediate', 'yellow_delayed', 'green_minor', 'black_deceased'])->default('green_minor');
                $table->string('assigned_zone', 50)->comment('Khu vực phân bổ: Khu Hồi sức đỏ, Khu chờ vàng, Khu nhẹ xanh');
                $table->text('primary_injuries')->nullable();
                $table->string('triaged_by', 100)->comment('Bác sĩ/Điều dưỡng phân loại');
                $table->timestamps();
                $table->index(['disaster_code', 'start_triage_color']);
            });
        }

        // 10. Centralized Fetal Monitoring & AI Cardiotocography (CTG)
        if (!Schema::hasTable('fetal_monitoring_ctgs')) {
            Schema::create('fetal_monitoring_ctgs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('delivery_room_bed', 50)->comment('Phòng sinh / Giường');
                $table->integer('baseline_fhr_bpm')->default(140)->comment('Nhịp tim thai cơ bản bpm (110-160)');
                $table->enum('variability', ['absent', 'minimal', 'moderate_normal', 'marked'])->default('moderate_normal');
                $table->integer('accelerations_count')->default(0)->comment('Số cơn nhịp tăng (Accelerations)');
                $table->enum('decelerations_type', ['none', 'early_benign', 'variable_cord', 'late_uteroplacental_hypoxia', 'prolonged'])->default('none');
                $table->boolean('sinusoidal_pattern')->default(false)->comment('Nhịp hình sin thiếu máu nặng');
                $table->integer('uterine_contractions_per_10min')->default(3)->comment('Cơn co tử cung trong 10 phút');
                $table->enum('figo_classification', ['normal', 'suspicious', 'pathological'])->default('normal');
                $table->enum('acog_category', ['category_1_normal', 'category_2_indeterminate', 'category_3_abnormal_acidosis'])->default('category_1_normal');
                $table->boolean('emergency_csection_alert')->default(false)->comment('Cảnh báo mổ lấy thai tối khẩn cấp');
                $table->text('ai_trace_interpretation')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'acog_category']);
            });
        }

        // 11. IVF Embryo Scoring & Electronic Witnessing
        if (!Schema::hasTable('ivf_embryo_cycles')) {
            Schema::create('ivf_embryo_cycles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('cycle_code', 50)->comment('Mã chu kỳ IVF');
                $table->string('partner_name', 100)->nullable()->comment('Họ tên chồng/bạn đời');
                $table->string('partner_sample_code', 50)->nullable()->comment('Mã mẫu tinh trùng');
                $table->integer('oocytes_retrieved')->default(0)->comment('Số noãn chọc hút được');
                $table->integer('oocytes_fertilized')->default(0)->comment('Số noãn thụ tinh (2PN)');
                $table->string('embryo_id_tag', 50)->comment('Mã định danh phôi');
                $table->enum('development_stage', ['day1_zygote', 'day3_cleavage', 'day5_blastocyst', 'day6_blastocyst'])->default('day5_blastocyst');
                $table->string('gardner_grade', 20)->nullable()->comment('Phân loại Gardner phôi nang: VD 4AA, 4AB, 3BB');
                $table->decimal('ai_implantation_potential_score', 4, 1)->default(0)->comment('Điểm tiềm năng làm tổ AI (0-100)');
                $table->boolean('electronic_witnessing_verified')->default(true)->comment('Xác thực RFID chống nhầm mẫu kép');
                $table->string('nitrogen_tank_storage_position', 100)->nullable()->comment('Vị trí lưu trữ bình nitơ: Thùng/Rack/Box/Slot');
                $table->enum('embryo_status', ['in_culture', 'transferred', 'cryopreserved', 'discarded'])->default('cryopreserved');
                $table->timestamps();
                $table->index(['patient_id', 'cycle_code']);
            });
        }

        // 12. Pediatric Early Warning Score (PEWS)
        if (!Schema::hasTable('pediatric_early_warning_scores')) {
            Schema::create('pediatric_early_warning_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('age_group', ['neonate_under_3m', 'infant_3_12m', 'toddler_1_4y', 'child_5_11y', 'adolescent_12y_plus'])->default('toddler_1_4y');
                $table->integer('behavior_score')->default(0)->comment('Hành vi/Tri giác: 0=Chơi, 1=Ngủ/kích thích, 2=Li bì, 3=Bất tỉnh');
                $table->integer('cardiovascular_score')->default(0)->comment('Tuần hoàn: 0=Hồng/CRT<2s, 1=Tái, 2=Xám/CRT 3-4s, 3=Xám tro/CRT>5s');
                $table->integer('respiratory_score')->default(0)->comment('Hô hấp: 0=Bình thường, 1=Thở nhanh + co kéo nhẹ, 2=Co kéo vừa, 3=Thở rên/co kéo nặng');
                $table->boolean('nebulizer_used')->default(false)->comment('Có dùng khí dung mỗi 15 phút (+2 điểm)');
                $table->boolean('persistent_vomiting_postop')->default(false)->comment('Nôn liên tục sau phẫu thuật (+2 điểm)');
                $table->integer('total_pews_score')->default(0)->comment('Tổng điểm PEWS (0-9+)');
                $table->enum('risk_action_level', ['low_routine_monitor', 'medium_nurse_assessment', 'high_pediatric_met_activation'])->default('low_routine_monitor');
                $table->text('nursing_interventions')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'risk_action_level'], 'pews_pat_risk_idx');
            });
        }

        // 13. Coronary Physiology & Intravascular Imaging (FFR / IVUS / OCT)
        if (!Schema::hasTable('coronary_physiology_imagings')) {
            Schema::create('coronary_physiology_imagings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('target_artery', 50)->comment('Nhánh mạch vành: LAD, LCx, RCA, LM');
                $table->decimal('ffr_ratio', 4, 2)->nullable()->comment('Chỉ số dự trữ phân đoạn lưu lượng FFR (Thiếu máu khi <= 0.80)');
                $table->decimal('ifr_ratio', 4, 2)->nullable()->comment('Chỉ số iFR (Thiếu máu khi <= 0.89)');
                $table->enum('imaging_modality', ['ivus', 'oct', 'angiography_only'])->default('ivus');
                $table->decimal('minimal_lumen_area_mm2', 5, 2)->nullable()->comment('Diện tích lòng mạch tối thiểu MLA mm²');
                $table->decimal('plaque_burden_pct', 5, 1)->nullable()->comment('Tải lượng mảng xơ vữa %');
                $table->boolean('stent_malapposition_detected')->default(false)->comment('Phát hiện Stent không áp sát thành mạch');
                $table->boolean('stent_underexpansion_detected')->default(false)->comment('Phát hiện Stent nở không hoàn toàn');
                $table->enum('hemodynamic_significance', ['ischemic_revascularization_indicated', 'non_ischemic_medical_therapy'])->default('non_ischemic_medical_therapy');
                $table->text('operator_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'target_artery']);
            });
        }

        // 14. Extended Stroke Thrombectomy Core-Penumbra Mismatch
        if (!Schema::hasTable('extended_stroke_perfusions')) {
            Schema::create('extended_stroke_perfusions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->integer('onset_to_arrival_hours')->default(8)->comment('Thời gian từ khi khởi phát đến viện (giờ, 6-24h)');
                $table->decimal('ischemic_core_volume_ml', 6, 1)->default(0)->comment('Thể tích lõi nhồi máu (rCBF < 30%) mL');
                $table->decimal('hypoperfused_volume_ml', 6, 1)->default(0)->comment('Thể tích tưới máu kém (Tmax > 6s) mL');
                $table->decimal('mismatch_volume_ml', 6, 1)->default(0)->comment('Thể tích tranh tối tranh sáng Penumbra = Tmax - Core');
                $table->decimal('mismatch_ratio', 5, 2)->default(1.0)->comment('Tỷ lệ bất tương xứng Penumbra/Core');
                $table->boolean('dawn_criteria_met')->default(false)->comment('Thỏa mãn tiêu chuẩn thử nghiệm DAWN');
                $table->boolean('defuse3_criteria_met')->default(false)->comment('Thỏa mãn tiêu chuẩn thử nghiệm DEFUSE-3');
                $table->enum('extended_window_decision', ['eligible_for_mechanical_thrombectomy', 'ineligible_large_completed_infarct'])->default('eligible_for_mechanical_thrombectomy');
                $table->text('neurointervention_plan')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'extended_window_decision'], 'stroke_ext_dec_idx');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('extended_stroke_perfusions');
        Schema::dropIfExists('coronary_physiology_imagings');
        Schema::dropIfExists('pediatric_early_warning_scores');
        Schema::dropIfExists('ivf_embryo_cycles');
        Schema::dropIfExists('fetal_monitoring_ctgs');
        Schema::dropIfExists('mass_casualty_triages');
        Schema::dropIfExists('tele_ems_missions');
    }
};
