<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmartAssetTrackingBle;
use App\Models\BiomedicalEquipmentCmms;
use App\Models\SmartParkingAmbulanceGate;
use App\Models\CentralKitchenHaccp;
use App\Models\LinenRfidLifecycleTracking;

class AssetEngineeringHaccpLinenController extends Controller
{
    public function index()
    {
        $stats = [
            'total_ble_assets' => SmartAssetTrackingBle::count(),
            'ready_ble_assets' => SmartAssetTrackingBle::where('asset_status', 'available_ready')->count(),
            'total_cmms_devices' => BiomedicalEquipmentCmms::count(),
            'certified_cmms_devices' => BiomedicalEquipmentCmms::where('certification_status', 'certified_safe_pass')->count(),
            'parking_gate_events' => SmartParkingAmbulanceGate::count(),
            'fast_track_ambulances' => SmartParkingAmbulanceGate::where('barrier_gate_status', 'auto_opened_fast_track')->count(),
            'kitchen_dietary_batches' => CentralKitchenHaccp::count(),
            'haccp_compliant_batches' => CentralKitchenHaccp::where('haccp_audit_passed', true)->count(),
            'linen_rfid_items' => LinenRfidLifecycleTracking::count(),
            'active_sterile_linens' => LinenRfidLifecycleTracking::where('linen_condition', 'in_circulation_sterile')->count(),
            'recent_ble_assets' => SmartAssetTrackingBle::latest()->take(5)->get(),
            'recent_cmms' => BiomedicalEquipmentCmms::latest()->take(5)->get(),
            'recent_parking_gates' => SmartParkingAmbulanceGate::latest()->take(5)->get(),
            'recent_kitchen_batches' => CentralKitchenHaccp::latest()->take(5)->get(),
            'recent_linens' => LinenRfidLifecycleTracking::latest()->take(5)->get(),
        ];

        return view('reception.asset_engineering_haccp_linen.index', compact('stats'));
    }

    public function storeAssetBle(Request $request)
    {
        $validated = $request->validate([
            'asset_tag_ble' => 'required|string|max:50|unique:smart_asset_tracking_bles,asset_tag_ble',
            'asset_name' => 'required|string|max:100',
            'equipment_category' => 'required|in:mechanical_ventilator,defibrillator_aed,infusion_pump,portable_ultrasound,crash_cart',
            'current_location_zone' => 'required|string|max:100',
            'rssi_signal_strength_dbm' => 'required|integer',
            'asset_status' => 'required|in:available_ready,in_use_patient,cleaning_decon,out_of_bounds_theft_alarm',
        ]);

        $asset = SmartAssetTrackingBle::create($validated);

        return redirect()->back()->with('success', 'Đăng ký định vị tài sản y tế BLE/UWB [' . $asset->asset_name . '] thành công!');
    }

    public function triggerTheftAlarm($id)
    {
        $asset = SmartAssetTrackingBle::findOrFail($id);
        $asset->triggerTheftAlarm();

        return redirect()->back()->with('error', 'CẢNH BÁO BẢO MẬT: Thiết bị [' . $asset->asset_name . '] đã di chuyển ra ngoài hàng rào Geofence!');
    }

    public function storeBiomedicalCmms(Request $request)
    {
        $validated = $request->validate([
            'equipment_serial_no' => 'required|string|max:50',
            'device_name' => 'required|string|max:100',
            'maintenance_type' => 'required|in:preventive_calibration,corrective_repair,electrical_safety_iec62353,annual_overhaul',
            'leakage_current_microamp' => 'required|numeric|min:0',
            'ground_resistance_ohm' => 'required|numeric|min:0',
            'mtbf_hours_calculated' => 'required|integer|min:0',
            'calibration_expiry_date' => 'required|date',
            'bio_engineer_name' => 'required|string|max:255',
        ]);

        $cmms = BiomedicalEquipmentCmms::create($validated);
        $cmms->verifyElectricalSafetyIec62353();

        return redirect()->back()->with('success', 'Ghi nhận kiểm định bảo trì thiết bị y tế CMMS [' . $cmms->device_name . '] thành công!');
    }

    public function storeParkingGate(Request $request)
    {
        $validated = $request->validate([
            'vehicle_license_plate' => 'required|string|max:30',
            'vehicle_type' => 'required|in:emergency_ambulance,patient_wheelchair_van,staff_physician,visitor_standard',
            'alpr_camera_confidence_pct' => 'required|numeric|between:0,100',
            'assigned_parking_bay' => 'required|string|max:50',
            'emergency_siren_detected' => 'nullable|boolean',
        ]);

        $gate = SmartParkingAmbulanceGate::create($validated);
        if ($gate->vehicle_type === 'emergency_ambulance') {
            $gate->fastTrackEmergencyAccess();
        }

        return redirect()->back()->with('success', 'Nhận diện camera ALPR xe [' . $gate->vehicle_license_plate . '] - Mở barie bãi xe thành công!');
    }

    public function fastTrackAmbulance($id)
    {
        $gate = SmartParkingAmbulanceGate::findOrFail($id);
        $gate->fastTrackEmergencyAccess();

        return redirect()->back()->with('success', 'Mở barie làn ưu tiên khẩn cấp cho xe cấp cứu [' . $gate->vehicle_license_plate . ']!');
    }

    public function storeKitchenHaccp(Request $request)
    {
        $validated = $request->validate([
            'menu_batch_code' => 'required|string|max:50',
            'diet_category' => 'required|in:renal_low_sodium,diabetic_glycemic_control,post_op_soft_liquid,pediatric_allergen_free',
            'cooking_core_temp_celsius' => 'required|numeric',
            'rethermalization_hot_temp' => 'required|numeric',
            'rethermalization_cold_temp' => 'required|numeric',
            'food_sample_refrigerated_24h' => 'nullable|boolean',
            'executive_chef_dietitian' => 'required|string|max:255',
        ]);

        $kitchen = CentralKitchenHaccp::create($validated);
        $kitchen->auditHaccpFoodSafety();

        return redirect()->back()->with('success', 'Kiểm soát an toàn thực phẩm bếp ăn bệnh viện HACCP lô [' . $kitchen->menu_batch_code . '] thành công!');
    }

    public function storeLinenRfid(Request $request)
    {
        $validated = $request->validate([
            'linen_rfid_epc' => 'required|string|max:50',
            'linen_type' => 'required|in:surgical_drape_pack,patient_gown_isolation,icu_bed_sheet_thermal,doctor_scrub_suit',
            'wash_cycle_count' => 'required|integer|min:0',
            'max_wash_lifespan' => 'required|integer|min:1',
            'last_wash_temp_celsius' => 'required|numeric|min:0',
            'biological_spore_test' => 'required|in:negative_sterile,positive_bioburden_wash_again',
        ]);

        $linen = LinenRfidLifecycleTracking::create($validated);

        return redirect()->back()->with('success', 'Quản lý đồ vải & săng mổ gắn chip RFID [' . $linen->linen_rfid_epc . '] thành công!');
    }

    public function recordWashCycle($id)
    {
        $linen = LinenRfidLifecycleTracking::findOrFail($id);
        $linen->recordWashCycle();

        return redirect()->back()->with('success', 'Cập nhật chu kỳ giặt đồ vải RFID [' . $linen->linen_rfid_epc . '] lên lần thứ ' . $linen->wash_cycle_count . '!');
    }
}
