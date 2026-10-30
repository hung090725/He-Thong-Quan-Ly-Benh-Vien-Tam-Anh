<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 15. Oncogenetics & Prenatal Genomics NIPT
        if (!Schema::hasTable('oncogenetics_nipt_records')) {
            Schema::create('oncogenetics_nipt_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('test_category', ['somatic_cancer_ngs', 'hereditary_cancer_risk', 'prenatal_nipt_cfdna'])->default('somatic_cancer_ngs');
                $table->string('sample_type', 50)->comment('Mẫu: Mô sinh thiết FFPE, Máu ngoại vi ctDNA, Máu mẹ cfDNA');
                $table->json('identified_variants')->nullable()->comment('Các biến dị đột biến phát hiện JSON');
                $table->string('fda_approved_targeted_therapies', 250)->nullable()->comment('Thuốc điều trị trúng đích phù hợp: Osimertinib, Trastuzumab, Olaparib, etc.');
                $table->decimal('nipt_fetal_fraction_pct', 4, 1)->nullable()->comment('Tỷ lệ DNA tự do thai nhi cfDNA % (yêu cầu >= 4%)');
                $table->enum('nipt_aneuploidy_risk', ['low_risk', 'high_risk_trisomy_21', 'high_risk_trisomy_18', 'high_risk_trisomy_13', 'monosomy_x', 'na'])->default('na');
                $table->text('genetic_counselor_summary')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'test_category']);
            });
        }

        // 16. Biobank Registry & Stem Cell Unit
        if (!Schema::hasTable('biobank_stem_cell_units')) {
            Schema::create('biobank_stem_cell_units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->string('specimen_code', 50)->unique()->comment('Mã mẫu sinh học / mã tế bào gốc');
                $table->enum('stem_cell_source', ['bone_marrow', 'umbilical_cord_blood', 'adipose_tissue', 'peripheral_blood_mobilized'])->default('umbilical_cord_blood');
                $table->string('nitrogen_tank_id', 30)->default('TANK-01')->comment('Thùng chứa nitơ lỏng -196 độ C');
                $table->string('canister_rack_id', 30)->default('RACK-A');
                $table->string('box_slot_coordinate', 30)->default('B02-C05');
                $table->decimal('total_nucleated_cells_10e8', 6, 2)->default(0)->comment('Tổng số tế bào có nhân TNC');
                $table->decimal('cd34_positive_count_10e6', 6, 2)->default(0)->comment('Số lượng tế bào gốc CD34+');
                $table->decimal('cell_viability_pct', 5, 2)->default(95.0)->comment('Độ sống tế bào % (yêu cầu >= 90%)');
                $table->boolean('sterility_clearance_passed')->default(true)->comment('Chứng nhận vô trùng vi khuẩn & nấm');
                $table->enum('storage_status', ['stored_cryo', 'thawed_transplanted', 'quarantined', 'discarded'])->default('stored_cryo');
                $table->timestamps();
                $table->index(['patient_id', 'storage_status']);
            });
        }

        // 17. Hospital Capacity Command Center & Bed Flow Predictor
        if (!Schema::hasTable('bed_capacity_commands')) {
            Schema::create('bed_capacity_commands', function (Blueprint $table) {
                $table->id();
                $table->string('department_name', 100)->comment('Tên khoa lâm sàng');
                $table->integer('total_licensed_beds')->default(50);
                $table->integer('occupied_beds')->default(0);
                $table->integer('pending_admissions_er')->default(0)->comment('Bệnh nhân đang chờ giường từ Cấp cứu');
                $table->integer('confirmed_discharges_today')->default(0)->comment('Bệnh nhân đã có y lệnh xuất viện');
                $table->integer('beds_in_sanitization')->default(0)->comment('Giường đang khử khuẩn vệ sinh');
                $table->decimal('bed_occupancy_rate_pct', 5, 2)->default(0)->comment('Tỷ lệ lấp đầy giường %');
                $table->integer('predicted_available_beds_afternoon')->default(0)->comment('Dự báo số giường trống buổi chiều');
                $table->enum('capacity_alert_status', ['normal_green', 'busy_yellow', 'gridlock_code_purple'])->default('normal_green');
                $table->boolean('discharge_lounge_active')->default(true)->comment('Có kích hoạt phòng chờ xuất viện thư giãn');
                $table->timestamps();
                $table->index(['department_name', 'capacity_alert_status'], 'bed_dept_status_idx');
            });
        }

        // 18. Automated Hospital Accreditation (83 Tiêu chí BYT 2.0 & JCI)
        if (!Schema::hasTable('hospital_accreditation_kpis')) {
            Schema::create('hospital_accreditation_kpis', function (Blueprint $table) {
                $table->id();
                $table->string('kpi_code', 50)->unique()->comment('Mã tiêu chí: JCI-IPSG, BYT-A4.2, BYT-C6.1');
                $table->string('kpi_title', 200)->comment('Tên chỉ số chất lượng lâm sàng');
                $table->string('domain_category', 100)->comment('Lĩnh vực: An toàn người bệnh, Kiểm soát nhiễm khuẩn, Phẫu thuật, Dược');
                $table->decimal('benchmark_target', 8, 2)->comment('Ngưỡng chuẩn mục tiêu');
                $table->decimal('actual_value', 8, 2)->comment('Giá trị thực tế bệnh viện đạt được');
                $table->string('metric_unit', 30)->comment('Đơn vị tính: %, ca/1000 ngày nằm, phút, etc.');
                $table->boolean('target_achieved')->default(true)->comment('Đạt chuẩn');
                $table->enum('compliance_level', ['level_5_excellent', 'level_4_good', 'level_3_fair', 'level_2_poor'])->default('level_5_excellent');
                $table->text('action_improvement_plan')->nullable();
                $table->timestamps();
                $table->index(['domain_category', 'compliance_level'], 'kpi_domain_comp_idx');
            });
        }

        // 19. Medical Toxicology & Orphan Antidote Registry
        if (!Schema::hasTable('orphan_antidote_registries')) {
            Schema::create('orphan_antidote_registries', function (Blueprint $table) {
                $table->id();
                $table->string('antidote_name', 100)->comment('Tên thuốc giải độc: Intralipid 20%, Kháng độc tố Botulinum (BAT), Huyết thanh kháng nọc rắn, Fomepizole');
                $table->string('indication_poison', 150)->comment('Chỉ định ngộ độc: Ngộ độc thuốc tê LAST, Botulinum, Rắn lục, Paracetamol, etc.');
                $table->integer('stock_vials_available')->default(0)->comment('Số lượng lọ tồn kho sẵn sàng');
                $table->integer('minimum_safety_threshold')->default(5)->comment('Ngưỡng dự trữ tối thiểu khẩn cấp');
                $table->string('storage_location_cabinet', 100)->comment('Vị trí tủ thuốc cấp cứu / kho bảo quản');
                $table->date('earliest_expiry_date')->comment('Hạn sử dụng lô gần nhất');
                $table->boolean('immediate_procurement_alert')->default(false)->comment('Báo động cần mua bù khẩn cấp');
                $table->text('first_dose_administration_protocol')->nullable()->comment('Phác đồ tiêm liều đầu cấp cứu');
                $table->timestamps();
                $table->index(['antidote_name', 'immediate_procurement_alert'], 'antidote_proc_idx');
            });
        }

        // 20. Hospital-at-Home (HAH) & Virtual Ward Monitoring
        if (!Schema::hasTable('virtual_ward_patients')) {
            Schema::create('virtual_ward_patients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('virtual_bed_identifier', 50)->comment('Mã giường ảo: HAH-WARD-01');
                $table->enum('admission_diagnosis', ['chf_decompensation_post_discharge', 'copd_mild_exacerbation', 'cellulitis_iv_antibiotics', 'covid19_recovery'])->default('chf_decompensation_post_discharge');
                $table->date('virtual_admission_date');
                $table->decimal('current_weight_kg', 5, 2)->default(65.0)->comment('Cân nặng người bệnh');
                $table->decimal('weight_change_3days_kg', 4, 2)->default(0)->comment('Thay đổi cân nặng 3 ngày gần nhất (Báo động phù khi > 2kg)');
                $table->decimal('latest_spo2_pct', 4, 1)->default(98.0)->comment('Chỉ số SpO2 thiết bị đo tại nhà gửi về');
                $table->decimal('latest_sbp', 5, 1)->default(120)->comment('Huyết áp tâm thu tại nhà');
                $table->decimal('latest_dbp', 5, 1)->default(80)->comment('Huyết áp tâm trương tại nhà');
                $table->boolean('red_flag_alert_triggered')->default(false)->comment('Kích hoạt còi báo động cần can thiệp khẩn');
                $table->enum('virtual_ward_status', ['monitoring_active', 'escalated_to_hospital_admission', 'graduated_discharged'])->default('monitoring_active');
                $table->text('care_coordinator_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'virtual_ward_status'], 'vward_pat_status_idx');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('virtual_ward_patients');
        Schema::dropIfExists('orphan_antidote_registries');
        Schema::dropIfExists('hospital_accreditation_kpis');
        Schema::dropIfExists('bed_capacity_commands');
        Schema::dropIfExists('biobank_stem_cell_units');
        Schema::dropIfExists('oncogenetics_nipt_records');
    }
};
