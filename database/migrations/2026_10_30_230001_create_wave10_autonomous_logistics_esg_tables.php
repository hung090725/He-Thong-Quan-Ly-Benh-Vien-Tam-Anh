<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Đội xe tự hành robot AGV/AMR vận chuyển nội viện
        Schema::create('hospital_agv_robot_fleets', function (Blueprint $table) {
            $table->id();
            $table->string('robot_code', 50)->unique();
            $table->string('robot_model', 100);
            $table->enum('payload_category', ['sterile_supplies', 'cytotoxic_chemotherapy', 'linen_laundry', 'therapeutic_dietary', 'biohazard_waste'])->default('sterile_supplies');
            $table->string('current_floor', 20)->default('1F');
            $table->string('destination_location', 100);
            $table->unsignedTinyInteger('battery_percentage')->default(100);
            $table->enum('navigation_status', ['idle_docked', 'navigating_corridor', 'riding_elevator', 'delivering_payload', 'charging_return'])->default('idle_docked');
            $table->boolean('obstacle_detected')->default(false);
            $table->string('dispatch_supervisor_name');
            $table->timestamps();

            $table->index(['robot_code', 'navigation_status'], 'agv_robot_code_stat_idx');
        });

        // 2. Đường ống khí nén chuyển mẫu xét nghiệm siêu tốc PTS (Pneumatic Tube System)
        Schema::create('pneumatic_tube_routings', function (Blueprint $table) {
            $table->id();
            $table->string('station_code', 50);
            $table->string('destination_station', 50);
            $table->string('capsule_barcode', 50);
            $table->enum('specimen_category', ['stat_troponin_blood', 'abg_arterial_blood', 'csf_spinal_fluid', 'crossmatch_blood_sample'])->default('stat_troponin_blood');
            $table->decimal('velocity_meters_per_sec', 4, 1)->default(6.5);
            $table->decimal('g_force_shock_peak', 4, 2)->default(1.20);
            $table->boolean('hemolysis_risk_alert')->default(false);
            $table->timestamp('dispatch_timestamp')->useCurrent();
            $table->enum('delivery_status', ['in_flight', 'arrived_safe', 'carrier_jam_alert', 'diverted_maintenance'])->default('in_flight');
            $table->timestamps();

            $table->index(['capsule_barcode', 'destination_station'], 'pts_capsule_dest_idx');
        });

        // 3. Quản trị năng lượng xanh & máy phát điện ATS khẩn cấp (Smart Hospital Microgrid & ESG)
        Schema::create('smart_hospital_microgrid_esgs', function (Blueprint $table) {
            $table->id();
            $table->string('power_zone_code', 50);
            $table->enum('grid_power_status', ['national_grid_nominal', 'grid_blackout_detected', 'microgrid_islanding'])->default('national_grid_nominal');
            $table->decimal('ats_transfer_time_sec', 4, 2)->default(6.80);
            $table->boolean('backup_generator_running')->default(false);
            $table->unsignedInteger('ups_reserve_minutes')->default(45);
            $table->decimal('rooftop_solar_generation_kw', 6, 2)->default(125.50);
            $table->decimal('carbon_emissions_avoided_kg', 8, 2)->default(850.00);
            $table->enum('grid_safety_rating', ['optimal_tier4', 'alert_generator_fuel_low', 'ups_battery_degraded'])->default('optimal_tier4');
            $table->timestamps();

            $table->index(['power_zone_code', 'grid_safety_rating'], 'grid_zone_stat_idx');
        });

        // 4. Xử lý & nghiền hấp chất thải y tế vi sóng tại chỗ (Biohazard Microwave Shredder)
        Schema::create('biohazard_microwave_shredders', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 50);
            $table->string('batch_barcode', 50);
            $table->decimal('waste_input_weight_kg', 6, 2)->default(85.00);
            $table->decimal('microwave_temp_celsius', 5, 1)->default(105.0);
            $table->unsignedInteger('exposure_time_minutes')->default(30);
            $table->unsignedTinyInteger('shredder_blade_count')->default(4);
            $table->decimal('volume_reduction_pct', 4, 1)->default(82.5);
            $table->boolean('spore_bacillus_atrophaeus_killed')->default(true);
            $table->enum('processing_status', ['shredding_cycle', 'microwave_disinfecting', 'decontaminated_inert', 'maintenance_blade_dull'])->default('shredding_cycle');
            $table->timestamps();

            $table->index(['batch_barcode', 'processing_status'], 'mw_batch_stat_idx');
        });

        // 5. Trạm xử lý nước tinh khiết chạy thận nhân tạo RO (AAMI/ISO 23500)
        Schema::create('hemodialysis_central_ro_waters', function (Blueprint $table) {
            $table->id();
            $table->string('ro_plant_code', 50);
            $table->decimal('conductivity_us_cm', 5, 2)->default(4.20);
            $table->unsignedInteger('bacteria_cfu_per_ml')->default(12);
            $table->decimal('endotoxin_eu_per_ml', 4, 3)->default(0.040);
            $table->decimal('water_temperature_celsius', 4, 1)->default(24.5);
            $table->boolean('thermal_disinfection_active')->default(false);
            $table->decimal('permeate_flow_rate_lpm', 5, 1)->default(45.0);
            $table->enum('aami_iso23500_compliance', ['iso_ultra_pure_pass', 'chemical_alarm_divert', 'endotoxin_breach_shutoff'])->default('iso_ultra_pure_pass');
            $table->timestamps();

            $table->index(['ro_plant_code', 'aami_iso23500_compliance'], 'ro_plant_iso_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hemodialysis_central_ro_waters');
        Schema::dropIfExists('biohazard_microwave_shredders');
        Schema::dropIfExists('smart_hospital_microgrid_esgs');
        Schema::dropIfExists('pneumatic_tube_routings');
        Schema::dropIfExists('hospital_agv_robot_fleets');
    }
};
