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
        // 1. Code Blue AI & Hồi Sức Ngừng Tuần Hoàn Nâng Cao ACLS
        Schema::create('code_blue_acls_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('code_team_leader_id')->constrained('doctors')->onDelete('cascade');
            $table->string('event_location', 100); // Khoa Phòng, Giường bệnh
            $table->timestamp('arrest_witnessed_at')->nullable();
            $table->enum('initial_rhythm', ['ventricular_fibrillation_vf', 'pulseless_vt', 'asystole', 'pea_pulseless_electrical_activity']);
            $table->integer('cpr_total_duration_minutes')->default(10);
            $table->decimal('chest_compression_fraction_pct', 5, 2)->default(85.0); // AHA target >= 80%
            $table->integer('defibrillation_shocks_count')->default(0);
            $table->integer('epinephrine_doses_given')->default(0); // Mỗi 3-5 phút
            $table->integer('amiodarone_doses_given')->default(0);
            $table->boolean('advanced_airway_placed')->default(true);
            $table->decimal('etco2_during_cpr_mmhg', 5, 2)->default(22.5); // > 10 mmHg, > 20 mmHg báo hiệu ROSC
            $table->boolean('rosc_achieved')->default(false); // Return of Spontaneous Circulation
            $table->timestamp('rosc_timestamp')->nullable();
            $table->enum('final_outcome', ['admitted_to_icu', 'mortality_in_code', 'transferred_pci'])->default('admitted_to_icu');
            $table->text('resuscitation_timeline_notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'rosc_achieved'], 'cblue_pat_rosc_idx');
            $table->index(['event_location', 'initial_rhythm'], 'cblue_loc_rhythm_idx');
        });

        // 2. Hệ Thống Quản Lý Khí Y Tế Trung Tâm & Oxy Lỏng (MGPS & LOX)
        Schema::create('medical_gas_pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('zone_manifold_code', 50); // Zone A, Tầng 3 ICU, Khối Nhà B
            $table->string('building_floor', 50);
            $table->foreignId('biomedical_engineer_id')->constrained('doctors')->onDelete('cascade');
            $table->decimal('oxygen_line_pressure_bar', 5, 2)->default(4.2); // Chuẩn 4.0 - 4.5 bar
            $table->decimal('medical_air_pressure_bar', 5, 2)->default(4.0); // 4.0 bar
            $table->decimal('surgical_air_pressure_bar', 5, 2)->default(7.0); // 7.0 bar
            $table->decimal('vacuum_suction_pressure_bar', 5, 2)->default(-0.75); // -0.6 đến -0.8 bar
            $table->decimal('nitrous_oxide_pressure_bar', 5, 2)->default(4.0); // N2O gây mê
            $table->decimal('liquid_oxygen_tank_level_pct', 5, 2)->default(82.5); // % dung tích bồn LOX
            $table->decimal('liquid_oxygen_remaining_liters', 10, 2)->default(12500.0);
            $table->integer('active_ventilators_count')->default(25);
            $table->decimal('estimated_hours_until_depletion', 6, 2)->default(72.0); // Giờ cạn kiệt oxy
            $table->boolean('low_pressure_emergency_alarm')->default(false);
            $table->boolean('pipeline_leak_detected')->default(false);
            $table->text('manifold_status_summary')->nullable();
            $table->timestamps();

            $table->index(['zone_manifold_code', 'low_pressure_emergency_alarm'], 'mgps_code_alarm_idx');
        });

        // 3. Dược Động Học Lọc Máu Cấp Cứu Khi Quá Liều Độc Chất & Thuốc
        Schema::create('toxicology_hemoperfusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('toxicologist_id')->constrained('doctors')->onDelete('cascade');
            $table->string('toxin_or_drug_name', 100); // Paracetamol, Paraquat, Digoxin, Barbiturate...
            $table->decimal('estimated_ingestion_hours_ago', 5, 2)->default(4.0);
            $table->decimal('ingested_dose_mg_or_ml', 10, 2)->default(15000.0);
            $table->decimal('serum_toxin_concentration_mcg_ml', 8, 2)->default(220.0);
            $table->enum('rumack_matthew_toxicity_risk', ['probable_toxicity', 'possible_toxicity', 'low_risk', 'not_applicable'])->default('probable_toxicity');
            $table->string('specific_antidote_given', 100)->nullable(); // N-acetylcysteine (NAC)...
            $table->enum('extracorporeal_modality', ['hemoperfusion_charcoal', 'hemoperfusion_resin', 'intermittent_hemodialysis', 'crrt_cvvhdf'])->default('hemoperfusion_resin');
            $table->decimal('toxin_clearance_ml_min', 6, 2)->default(145.0);
            $table->decimal('recommended_duration_hours', 4, 2)->default(4.0);
            $table->boolean('hemodialysis_indicated')->default(true);
            $table->boolean('hepatotoxicity_or_nephrotoxicity_prevented')->default(true);
            $table->text('toxicology_clinical_assessment')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'toxin_or_drug_name'], 'tox_pat_drug_idx');
        });

        // 4. Biểu Đồ Thromboelastometry ROTEM / TEG Chỉ Huy Đông Máu
        Schema::create('rotem_viscoelastic_coagulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('anesthesiologist_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('clinical_scenario', ['cardiac_surgery_pump', 'liver_transplant', 'massive_postpartum_hemorrhage', 'severe_polytrauma'])->default('severe_polytrauma');
            $table->integer('extem_ct_seconds')->default(75); // Clotting Time chuẩn 40-80s
            $table->integer('extem_cft_seconds')->default(110); // Clot Formation Time 35-110s
            $table->decimal('extem_alpha_angle_degrees', 5, 2)->default(68.0); // 63-78 độ
            $table->integer('extem_mcf_mm')->default(58); // Maximum Clot Firmness 50-72mm
            $table->decimal('extem_ml_pct', 5, 2)->default(6.5); // Maximum Lysis < 15%
            $table->integer('fibtem_mcf_mm')->default(12); // Đánh giá Fibrinogen, < 10mm cần Cryoprecipitate
            $table->integer('aptem_mcf_mm')->default(58); // MCF sau ức chế aprotinin
            $table->boolean('hyperfibrinolysis_detected')->default(false); // ML > 15%
            $table->string('targeted_blood_product_recommendation', 255)->nullable();
            $table->boolean('tranexamic_acid_indicated')->default(false);
            $table->timestamps();

            $table->index(['patient_id', 'clinical_scenario'], 'rotem_pat_scene_idx');
        });

        // 5. Điều Phối Chuyển Viện Nặng & Trực Thăng Cấp Cứu Helipad
        Schema::create('critical_helipad_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('flight_physician_id')->constrained('doctors')->onDelete('cascade');
            $table->string('flight_mission_code', 50)->unique(); // HELI-EVAC-2026-XXXX
            $table->string('transfer_origin_location', 150); // Bệnh viện Tỉnh / Giàn khoan / Đảo
            $table->enum('aircraft_type', ['airbus_h225_super_puma', 'eurocopter_ec155', 'mi_171_medevac', 'bell_429'])->default('airbus_h225_super_puma');
            $table->timestamp('flight_departure_at')->nullable();
            $table->timestamp('estimated_helipad_touchdown_at')->nullable();
            $table->integer('transfer_risk_score_trips')->default(18); // TRIPS score (0-60)
            $table->boolean('inflight_ventilator_running')->default(true);
            $table->boolean('inflight_ecmo_running')->default(false);
            $table->boolean('helipad_landing_clearance_granted')->default(true);
            $table->enum('receiving_department', ['coronary_cathlab', 'trauma_hybrid_or', 'icu_extracorporeal', 'stroke_neuro_interv'])->default('trauma_hybrid_or');
            $table->text('flight_transfer_logistics')->nullable();
            $table->timestamps();

            $table->index(['flight_mission_code'], 'heli_mission_idx');
            $table->index(['patient_id', 'receiving_department'], 'heli_pat_dept_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('critical_helipad_transfers');
        Schema::dropIfExists('rotem_viscoelastic_coagulations');
        Schema::dropIfExists('toxicology_hemoperfusions');
        Schema::dropIfExists('medical_gas_pipelines');
        Schema::dropIfExists('code_blue_acls_trackers');
    }
};
