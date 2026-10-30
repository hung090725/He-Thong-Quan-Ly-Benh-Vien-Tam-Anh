<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HospitalAgvRobotFleet;
use App\Models\PneumaticTubeRouting;
use App\Models\SmartHospitalMicrogridEsg;
use App\Models\BiohazardMicrowaveShredder;
use App\Models\HemodialysisCentralRoWater;

class AutonomousLogisticsEsgController extends Controller
{
    public function index()
    {
        $stats = [
            'total_agv_robots' => HospitalAgvRobotFleet::count(),
            'active_delivering_robots' => HospitalAgvRobotFleet::where('navigation_status', 'delivering_payload')->count(),
            'pts_dispatches' => PneumaticTubeRouting::count(),
            'pts_safe_arrivals' => PneumaticTubeRouting::where('delivery_status', 'arrived_safe')->count(),
            'microgrid_zones' => SmartHospitalMicrogridEsg::count(),
            'optimal_tier4_grids' => SmartHospitalMicrogridEsg::where('grid_safety_rating', 'optimal_tier4')->count(),
            'shredder_batches' => BiohazardMicrowaveShredder::count(),
            'decontaminated_batches' => BiohazardMicrowaveShredder::where('processing_status', 'decontaminated_inert')->count(),
            'ro_water_plants' => HemodialysisCentralRoWater::count(),
            'ultra_pure_ro_plants' => HemodialysisCentralRoWater::where('aami_iso23500_compliance', 'iso_ultra_pure_pass')->count(),
            'recent_agv' => HospitalAgvRobotFleet::latest()->take(5)->get(),
            'recent_pts' => PneumaticTubeRouting::latest()->take(5)->get(),
            'recent_microgrid' => SmartHospitalMicrogridEsg::latest()->take(5)->get(),
            'recent_shredders' => BiohazardMicrowaveShredder::latest()->take(5)->get(),
            'recent_ro_waters' => HemodialysisCentralRoWater::latest()->take(5)->get(),
        ];

        return view('reception.autonomous_logistics_esg.index', compact('stats'));
    }

    public function storeAgvRobot(Request $request)
    {
        $validated = $request->validate([
            'robot_code' => 'required|string|max:50|unique:hospital_agv_robot_fleets,robot_code',
            'robot_model' => 'required|string|max:100',
            'payload_category' => 'required|in:sterile_supplies,cytotoxic_chemotherapy,linen_laundry,therapeutic_dietary,biohazard_waste',
            'current_floor' => 'required|string|max:20',
            'destination_location' => 'required|string|max:100',
            'battery_percentage' => 'required|integer|between:0,100',
            'navigation_status' => 'required|in:idle_docked,navigating_corridor,riding_elevator,delivering_payload,charging_return',
            'obstacle_detected' => 'nullable|boolean',
            'dispatch_supervisor_name' => 'required|string|max:255',
        ]);

        $robot = HospitalAgvRobotFleet::create($validated);
        $robot->checkBatteryAndReturn();

        return redirect()->back()->with('success', 'Điều phối đội Robot tự hành AGV/AMR [' . $robot->robot_code . '] thành công!');
    }

    public function dispatchAgvDelivery($id)
    {
        $robot = HospitalAgvRobotFleet::findOrFail($id);
        $robot->deliverPayload();

        return redirect()->back()->with('success', 'Robot AGV [' . $robot->robot_code . '] đã bắt đầu hành trình chuyển hàng tới ' . $robot->destination_location . '!');
    }

    public function storePneumaticTube(Request $request)
    {
        $validated = $request->validate([
            'station_code' => 'required|string|max:50',
            'destination_station' => 'required|string|max:50',
            'capsule_barcode' => 'required|string|max:50',
            'specimen_category' => 'required|in:stat_troponin_blood,abg_arterial_blood,csf_spinal_fluid,crossmatch_blood_sample',
            'velocity_meters_per_sec' => 'required|numeric|min:1',
            'g_force_shock_peak' => 'required|numeric|min:0',
        ]);

        $pts = PneumaticTubeRouting::create($validated);
        $pts->assessHemolysisRisk();

        return redirect()->back()->with('success', 'Bắn thoi khí nén chuyển mẫu xét nghiệm [' . $pts->capsule_barcode . '] thành công!');
    }

    public function storeMicrogridEsg(Request $request)
    {
        $validated = $request->validate([
            'power_zone_code' => 'required|string|max:50',
            'grid_power_status' => 'required|in:national_grid_nominal,grid_blackout_detected,microgrid_islanding',
            'ats_transfer_time_sec' => 'required|numeric|min:0',
            'backup_generator_running' => 'nullable|boolean',
            'ups_reserve_minutes' => 'required|integer|min:0',
            'rooftop_solar_generation_kw' => 'required|numeric|min:0',
            'carbon_emissions_avoided_kg' => 'required|numeric|min:0',
            'grid_safety_rating' => 'required|in:optimal_tier4,alert_generator_fuel_low,ups_battery_degraded',
        ]);

        $grid = SmartHospitalMicrogridEsg::create($validated);

        return redirect()->back()->with('success', 'Giám sát lưới điện vi mô Microgrid & ESG [' . $grid->power_zone_code . '] hoàn tất!');
    }

    public function storeMicrowaveShredder(Request $request)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:50',
            'batch_barcode' => 'required|string|max:50',
            'waste_input_weight_kg' => 'required|numeric|min:1',
            'microwave_temp_celsius' => 'required|numeric|min:0',
            'exposure_time_minutes' => 'required|integer|min:1',
            'volume_reduction_pct' => 'required|numeric|between:0,100',
            'spore_bacillus_atrophaeus_killed' => 'nullable|boolean',
        ]);

        $mw = BiohazardMicrowaveShredder::create($validated);
        $mw->completeDecontaminationCycle();

        return redirect()->back()->with('success', 'Nghiền hấp vi sóng rác thải y tế lây nhiễm lô [' . $mw->batch_barcode . '] thành công!');
    }

    public function storeRoWater(Request $request)
    {
        $validated = $request->validate([
            'ro_plant_code' => 'required|string|max:50',
            'conductivity_us_cm' => 'required|numeric|min:0',
            'bacteria_cfu_per_ml' => 'required|integer|min:0',
            'endotoxin_eu_per_ml' => 'required|numeric|min:0',
            'water_temperature_celsius' => 'required|numeric',
            'permeate_flow_rate_lpm' => 'required|numeric|min:0',
        ]);

        $ro = HemodialysisCentralRoWater::create($validated);
        $ro->auditIsoCompliance();

        return redirect()->back()->with('success', 'Kiểm tra chất lượng nước lọc thận siêu sạch AAMI/ISO 23500 [' . $ro->ro_plant_code . '] thành công!');
    }
}
