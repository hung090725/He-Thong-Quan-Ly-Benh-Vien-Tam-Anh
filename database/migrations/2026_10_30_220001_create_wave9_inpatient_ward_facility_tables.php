<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Phòng nội trú thông minh 5 sao & Bảng điều khiển cảm ứng IoT
        Schema::create('smart_inpatient_room_consoles', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 50);
            $table->foreignId('patient_id')->nullable()->constrained('patient_profiles')->nullOnDelete();
            $table->string('patient_name');
            $table->enum('room_class', ['vip_suite', 'presidential_suite', 'standard_single', 'double_isolation'])->default('vip_suite');
            $table->decimal('temperature_celsius', 4, 1)->default(24.0);
            $table->enum('circadian_lighting_mode', ['morning_energize', 'daylight_focus', 'evening_relax', 'night_sleep'])->default('daylight_focus');
            $table->enum('curtain_status', ['open', 'half_closed', 'fully_closed'])->default('open');
            $table->enum('nurse_call_type', ['none', 'water_assistance', 'pain_medication', 'bathroom_help', 'emergency_urgent'])->default('none');
            $table->enum('nurse_call_status', ['idle', 'pending', 'acknowledged', 'resolved'])->default('idle');
            $table->unsignedTinyInteger('patient_pain_score')->default(0);
            $table->string('dietary_order')->default('Cơm dưỡng bệnh tiêu chuẩn');
            $table->enum('iot_device_status', ['online', 'offline', 'maintenance'])->default('online');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'room_number'], 'siprc_pat_room_idx');
        });

        // 2. Điều phối động phòng khám ngoại trú & Biển tên điện tử E-Paper
        Schema::create('dynamic_opd_room_allocators', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 50);
            $table->string('department_name');
            $table->string('assigned_doctor_name');
            $table->string('doctor_specialty');
            $table->enum('shift_session', ['morning_shift', 'afternoon_shift', 'night_shift'])->default('morning_shift');
            $table->enum('epaper_display_status', ['active_consulting', 'paused_break', 'in_emergency_meeting', 'closed'])->default('active_consulting');
            $table->unsignedInteger('current_queue_number')->default(0);
            $table->unsignedInteger('next_queue_number')->default(0);
            $table->unsignedInteger('waiting_patient_count')->default(0);
            $table->unsignedInteger('average_consult_time_min')->default(15);
            $table->timestamp('last_epaper_synced_at')->nullable();
            $table->timestamps();

            $table->index(['room_code', 'shift_session'], 'opd_alloc_code_shift_idx');
        });

        // 3. Điều động vệ sinh buồng bệnh sau xuất viện & Robot khử trùng UV-C / VHP
        Schema::create('terminal_room_disinfections', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 50);
            $table->string('ward_department');
            $table->string('discharge_patient_name');
            $table->enum('cleaning_trigger_event', ['patient_discharge', 'post_operative', 'isolation_contact_precaution', 'scheduled_deep_clean'])->default('patient_discharge');
            $table->enum('disinfection_method', ['uvc_robot_autonomous', 'hydrogen_peroxide_vhp', 'manual_quaternary_wipe', 'hybrid_dual_cycle'])->default('uvc_robot_autonomous');
            $table->string('disinfection_robot_id', 50)->nullable();
            $table->unsignedInteger('run_duration_minutes')->default(30);
            $table->string('cleaning_staff_name');
            $table->timestamp('qr_verified_clean_at')->nullable();
            $table->unsignedInteger('atp_bioluminescence_rlu')->default(15);
            $table->enum('room_ready_status', ['dirty_pending', 'in_progress', 'quarantine_waiting', 'certified_clean_ready'])->default('dirty_pending');
            $table->timestamps();

            $table->index(['room_number', 'room_ready_status'], 'term_disinf_room_stat_idx');
        });

        // 4. Phòng sinh gia đình tích hợp trọn gói 4 trong 1 LDRP
        Schema::create('smart_delivery_suite_ldrps', function (Blueprint $table) {
            $table->id();
            $table->string('suite_code', 50);
            $table->foreignId('maternal_patient_id')->constrained('patient_profiles')->cascadeOnDelete();
            $table->string('mother_name');
            $table->decimal('gestational_weeks', 3, 1)->default(39.0);
            $table->enum('current_stage', ['stage1_labor', 'stage2_delivery', 'stage3_placenta', 'stage4_recovery_postpartum'])->default('stage1_labor');
            $table->enum('bed_transformation_mode', ['comfort_relax_bed', 'active_delivery_mode', 'emergency_c_section_prep'])->default('comfort_relax_bed');
            $table->boolean('water_birth_tub_active')->default(false);
            $table->boolean('infant_resuscitaire_warmed')->default(true);
            $table->string('partner_companion_tag_id', 50)->nullable();
            $table->enum('pain_management_type', ['epidural_analgesia', 'water_immersion', 'nitrous_oxide_gas', 'breathing_relaxation'])->default('epidural_analgesia');
            $table->unsignedInteger('fetal_heart_rate_bpm')->default(140);
            $table->unsignedTinyInteger('maternal_comfort_score')->default(8);
            $table->enum('status', ['active_in_labor', 'delivered_stable', 'postpartum_bonding', 'discharged'])->default('active_in_labor');
            $table->timestamps();

            $table->index(['maternal_patient_id', 'current_stage'], 'ldrp_pat_stage_idx');
        });

        // 5. Trạm điều dưỡng trung tâm & Bảng chỉ huy trực quan toàn khoa
        Schema::create('smart_nurse_station_hubs', function (Blueprint $table) {
            $table->id();
            $table->string('ward_code', 50);
            $table->string('ward_name');
            $table->unsignedInteger('total_active_beds')->default(30);
            $table->unsignedInteger('occupied_beds')->default(24);
            $table->unsignedInteger('critical_alert_count')->default(0);
            $table->unsignedInteger('pending_nurse_calls')->default(0);
            $table->unsignedInteger('infusion_near_empty_count')->default(0);
            $table->string('on_duty_head_nurse');
            $table->text('charge_nurse_list')->nullable();
            $table->enum('code_blue_standby_status', ['green_ready', 'yellow_standby', 'red_active_resuscitation'])->default('green_ready');
            $table->unsignedInteger('dashboard_refresh_rate_sec')->default(5);
            $table->timestamps();

            $table->index(['ward_code'], 'nurse_hub_ward_code_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_nurse_station_hubs');
        Schema::dropIfExists('smart_delivery_suite_ldrps');
        Schema::dropIfExists('terminal_room_disinfections');
        Schema::dropIfExists('dynamic_opd_room_allocators');
        Schema::dropIfExists('smart_inpatient_room_consoles');
    }
};
