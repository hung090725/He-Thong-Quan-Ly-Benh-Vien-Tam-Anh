<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Smart Infusion Pump DERS
        if (!Schema::hasTable('smart_infusion_pump_ders')) {
            Schema::create('smart_infusion_pump_ders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->string('pump_device_code', 50)->comment('Mã thiết bị bơm tiêm điện');
                $table->enum('care_unit_profile', ['icu_adult', 'nicu_pediatric', 'operating_room', 'cardiology_ward'])->default('icu_adult');
                $table->string('medication_name', 100)->comment('Tên thuốc');
                $table->string('concentration_unit', 50)->comment('Nồng độ');
                $table->decimal('dose_rate', 8, 3)->comment('Liều chỉ định');
                $table->string('dose_unit', 30)->comment('Đơn vị liều');
                $table->decimal('patient_weight_kg', 5, 2)->nullable()->comment('Cân nặng kg');
                $table->decimal('lower_soft_limit', 8, 3)->nullable()->comment('Ngưỡng mềm tối thiểu');
                $table->decimal('upper_soft_limit', 8, 3)->nullable()->comment('Ngưỡng mềm tối đa');
                $table->decimal('upper_hard_limit', 8, 3)->comment('Ngưỡng cứng chặn tuyệt đối');
                $table->decimal('infusion_rate_ml_hr', 8, 2)->default(0)->comment('Tốc độ mL/h');
                $table->boolean('soft_limit_exceeded')->default(false);
                $table->boolean('hard_limit_violation')->default(false);
                $table->string('override_authorized_by', 100)->nullable();
                $table->text('clinical_indication')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'medication_name'], 'ders_pat_med_idx');
            });
        }

        // 2. IV Y-Site Incompatibility Engine
        if (!Schema::hasTable('iv_ysite_incompatibilities')) {
            Schema::create('iv_ysite_incompatibilities', function (Blueprint $table) {
                $table->id();
                $table->string('primary_drug_name', 100)->comment('Thuốc truyền chính');
                $table->string('secondary_drug_name', 100)->comment('Thuốc truyền phụ');
                $table->string('carrier_solution', 100)->default('NaCl 0.9%');
                $table->enum('incompatibility_type', ['compatible', 'precipitate_formation', 'turbidity_color_change', 'chemical_degradation', 'uncertain'])->default('compatible');
                $table->text('clinical_hazard_summary')->nullable();
                $table->text('recommended_action')->nullable();
                $table->string('evidence_source', 150)->nullable();
                $table->timestamps();
                $table->index(['primary_drug_name', 'secondary_drug_name'], 'ysite_drugs_idx');
            });
        }

        // 3. Massive Transfusion Protocol (MTP) & TEG/ROTEM
        if (!Schema::hasTable('massive_transfusion_protocols')) {
            Schema::create('massive_transfusion_protocols', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('protocol_code', 50)->comment('Mã kích hoạt MTP');
                $table->integer('rbc_units_issued')->default(0)->comment('Hồng cầu lắng');
                $table->integer('ffp_units_issued')->default(0)->comment('Huyết tương tươi đông lạnh');
                $table->integer('platelet_doses_issued')->default(0)->comment('Tiểu cầu đậm đặc');
                $table->integer('cryoprecipitate_units')->default(0)->comment('Tủa lạnh');
                $table->boolean('tranexamic_acid_administered')->default(false);
                $table->decimal('teg_r_time_minutes', 5, 2)->nullable();
                $table->decimal('teg_k_time_minutes', 5, 2)->nullable();
                $table->decimal('teg_maximum_amplitude_mm', 5, 2)->nullable();
                $table->decimal('rotem_fibtem_a10_mm', 5, 2)->nullable();
                $table->boolean('balanced_ratio_achieved')->default(false);
                $table->boolean('target_hemostasis_reached')->default(false);
                $table->enum('mtp_status', ['in_progress', 'completed_hemostasis', 'patient_deceased'])->default('in_progress');
                $table->text('clinical_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'mtp_status'], 'mtp_pat_status_idx');
            });
        }

        // 4. Patient Blood Management (PBM) & Cell Saver
        if (!Schema::hasTable('patient_blood_managements')) {
            Schema::create('patient_blood_managements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->decimal('preop_hemoglobin_g_dl', 4, 1)->default(0);
                $table->decimal('preop_ferritin_ng_ml', 6, 1)->nullable();
                $table->boolean('preop_anemia_detected')->default(false);
                $table->boolean('tranexamic_acid_used')->default(false);
                $table->boolean('cell_saver_used')->default(false);
                $table->decimal('autologous_blood_salvaged_ml', 8, 1)->default(0);
                $table->decimal('postop_hemoglobin_g_dl', 4, 1)->nullable();
                $table->integer('allogeneic_transfusion_units')->default(0);
                $table->boolean('pbm_protocol_bundle_compliant')->default(true);
                $table->text('clinical_outcome')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'cell_saver_used'], 'pbm_pat_cs_idx');
            });
        }

        // 5. Organ Transplant Coordination & HLA Matching
        if (!Schema::hasTable('organ_transplant_coordinations')) {
            Schema::create('organ_transplant_coordinations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('lead_surgeon_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('organ_type', ['kidney', 'liver', 'heart', 'lung', 'cornea'])->default('kidney');
                $table->enum('donor_type', ['living_related', 'living_unrelated', 'brain_dead_deceased'])->default('living_related');
                $table->string('donor_code', 50);
                $table->integer('hla_mismatch_count')->default(0)->comment('Số lượng bất tương thích HLA (0-6)');
                $table->enum('crossmatch_result', ['negative', 'positive', 'borderline'])->default('negative');
                $table->decimal('cold_ischemia_time_hours', 5, 2)->default(0);
                $table->decimal('warm_ischemia_time_minutes', 5, 2)->default(0);
                $table->string('immunosuppression_protocol', 200)->nullable();
                $table->decimal('tacrolimus_trough_level_ng_ml', 5, 2)->nullable();
                $table->enum('graft_status_day30', ['functional_good', 'delayed_graft_function', 'acute_rejection', 'primary_non_function'])->default('functional_good');
                $table->text('clinical_notes')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'organ_type'], 'transplant_pat_organ_idx');
            });
        }

        // 6. Robotic Surgery & EndoWrist Lifecycle
        if (!Schema::hasTable('robotic_surgery_sessions')) {
            Schema::create('robotic_surgery_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('surgeon_id')->constrained('doctors')->cascadeOnDelete();
                $table->string('robot_system_serial', 50)->comment('Serial máy Da Vinci');
                $table->string('surgical_procedure_name', 150)->comment('Tên phẫu thuật');
                $table->integer('console_time_minutes')->default(0);
                $table->integer('docking_time_minutes')->default(0);
                $table->json('instruments_used_json')->nullable();
                $table->integer('instrument_lifespan_remaining')->default(10)->comment('Lần dùng còn lại');
                $table->boolean('instrument_retirement_alert')->default(false);
                $table->boolean('conversion_to_open_surgery')->default(false);
                $table->decimal('estimated_blood_loss_ml', 7, 1)->default(0);
                $table->decimal('postop_hospital_stay_days', 4, 1)->default(0);
                $table->text('clinical_summary')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'surgical_procedure_name'], 'robot_pat_proc_idx');
            });
        }

        // 7. Hybrid OR & Surgical Navigation Planning
        if (!Schema::hasTable('hybrid_or_navigation_plans')) {
            Schema::create('hybrid_or_navigation_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patient_profiles')->cascadeOnDelete();
                $table->foreignId('primary_operator_id')->constrained('doctors')->cascadeOnDelete();
                $table->enum('procedure_type', ['tavi_tavr', 'tevar_evar', 'cerebral_aneurysm_coiling', 'spine_navigation_fusion', 'complex_congenital_heart'])->default('tavi_tavr');
                $table->enum('navigation_modality', ['artisan_angio_ct', 'stealth_station_optical', 'electromagnetic_em', 'fusion_roadmapping'])->default('stealth_station_optical');
                $table->decimal('target_registration_error_mm', 4, 2)->default(0.5);
                $table->decimal('contrast_media_volume_ml', 6, 1)->default(0);
                $table->decimal('patient_baseline_egfr', 5, 2)->nullable();
                $table->enum('contrast_induced_nephropathy_risk', ['low_cin_risk', 'moderate_cin_risk', 'high_cin_risk'])->default('low_cin_risk');
                $table->decimal('fluoroscopy_time_minutes', 6, 2)->default(0);
                $table->decimal('radiation_dose_dap_mgycm2', 8, 2)->default(0);
                $table->boolean('procedural_success')->default(true);
                $table->text('complications_encountered')->nullable();
                $table->timestamps();
                $table->index(['patient_id', 'procedure_type'], 'hybrid_pat_proc_idx');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('hybrid_or_navigation_plans');
        Schema::dropIfExists('robotic_surgery_sessions');
        Schema::dropIfExists('organ_transplant_coordinations');
        Schema::dropIfExists('patient_blood_managements');
        Schema::dropIfExists('massive_transfusion_protocols');
        Schema::dropIfExists('iv_ysite_incompatibilities');
        Schema::dropIfExists('smart_infusion_pump_ders');
    }
};
