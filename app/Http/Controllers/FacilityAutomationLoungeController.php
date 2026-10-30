<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiohazardDirtyUtilitySluice;
use App\Models\SterileAreaAccessInterlock;
use App\Models\IndoorAirQualityHvac;
use App\Models\MedicalRecordsPhysicalArchive;
use App\Models\DayCareInfusionLounge;
use App\Models\PatientProfile;

class FacilityAutomationLoungeController extends Controller
{
    public function index()
    {
        $stats = [
            'sluice_rooms' => BiohazardDirtyUtilitySluice::count(),
            'compliant_sluice_rooms' => BiohazardDirtyUtilitySluice::where('compliance_audit_status', 'fully_compliant')->count(),
            'access_logs' => SterileAreaAccessInterlock::count(),
            'granted_access_logs' => SterileAreaAccessInterlock::where('access_decision', 'granted_normal')->count(),
            'monitored_iaq_sensors' => IndoorAirQualityHvac::count(),
            'excellent_iaq_zones' => IndoorAirQualityHvac::where('iaq_index_rating', 'excellent_green')->count(),
            'archive_bays' => MedicalRecordsPhysicalArchive::count(),
            'armed_fire_systems' => MedicalRecordsPhysicalArchive::where('fire_suppression_status', 'armed_standby')->count(),
            'daycare_infusion_bays' => DayCareInfusionLounge::count(),
            'active_infusions' => DayCareInfusionLounge::where('session_status', 'infusion_flowing_stable')->count(),
            'recent_sluice' => BiohazardDirtyUtilitySluice::latest()->take(5)->get(),
            'recent_access_interlocks' => SterileAreaAccessInterlock::latest()->take(5)->get(),
            'recent_iaq_hvac' => IndoorAirQualityHvac::latest()->take(5)->get(),
            'recent_archives' => MedicalRecordsPhysicalArchive::latest()->take(5)->get(),
            'recent_daycare_infusions' => DayCareInfusionLounge::with('patient')->latest()->take(5)->get(),
            'patients' => PatientProfile::latest()->take(20)->get(),
        ];

        return view('reception.facility_automation_lounge.index', compact('stats'));
    }

    public function storeBiohazardSluice(Request $request)
    {
        $validated = $request->validate([
            'sluice_room_code' => 'required|string|max:50',
            'bedpan_washer_temp_celsius' => 'required|numeric',
            'washer_disinfection_cycle' => 'required|in:idle,thermal_disinfecting,cycle_completed,door_locked_cooling',
            'yellow_bag_infectious_kg' => 'required|numeric|min:0',
            'sharps_container_full_pct' => 'required|integer|between:0,100',
            'air_deodorizer_fan_status' => 'required|in:exhaust_running_nominal,booster_active,filter_clogged',
        ]);

        $sluice = BiohazardDirtyUtilitySluice::create($validated);
        $sluice->auditSafety();

        return redirect()->back()->with('success', 'Ghi nhận kiểm soát phòng xử lý đồ bẩn & rác lây nhiễm [' . $sluice->sluice_room_code . '] thành công!');
    }

    public function storeSterileAreaInterlock(Request $request)
    {
        $validated = $request->validate([
            'access_point_name' => 'required|string|max:100',
            'zone_classification' => 'required|in:operating_theater_sterile,narcotics_vault,blood_bank_core,bsl3_virology_lab',
            'staff_badge_rfid' => 'required|string|max:50',
            'staff_name' => 'required|string|max:255',
            'staff_role' => 'required|string|max:255',
            'biometric_face_matched' => 'nullable|boolean',
            'interlock_outer_door_closed' => 'nullable|boolean',
            'interlock_inner_door_unlocked' => 'nullable|boolean',
            'is_authorized_role' => 'nullable|boolean',
        ]);

        $isAuthorized = $request->boolean('is_authorized_role', true);
        unset($validated['is_authorized_role']);

        $interlock = SterileAreaAccessInterlock::create($validated);
        $decision = $interlock->processAccessRequest($isAuthorized);

        return redirect()->back()->with('success', 'Xác thực kiểm soát ra vào cửa liên động: Kết quả [' . $decision . ']!');
    }

    public function storeIaqHvac(Request $request)
    {
        $validated = $request->validate([
            'room_sensor_code' => 'required|string|max:50',
            'room_type' => 'required|in:intensive_care_unit,operating_room,clean_pharmacy,general_inpatient_suite',
            'pm2_5_ug_m3' => 'required|numeric|min:0',
            'co2_ppm' => 'required|integer|min:0',
            'tvoc_ppb' => 'required|integer|min:0',
            'temperature_celsius' => 'required|numeric',
            'relative_humidity_pct' => 'required|numeric|between:0,100',
            'hvac_damper_fresh_air_pct' => 'required|integer|between:0,100',
        ]);

        $iaq = IndoorAirQualityHvac::create($validated);
        $iaq->computeIaqRating();

        return redirect()->back()->with('success', 'Cập nhật chỉ số chất lượng không khí IAQ & Điều hòa HVAC [' . $iaq->room_sensor_code . '] thành công!');
    }

    public function storeMedicalRecordsArchive(Request $request)
    {
        $validated = $request->validate([
            'compactor_bay_code' => 'required|string|max:50',
            'section_code' => 'required|string|max:50',
            'total_paper_charts_stored' => 'required|integer|min:0',
            'temperature_celsius' => 'required|numeric',
            'relative_humidity_pct' => 'required|numeric|between:0,100',
            'smoke_detector_active' => 'nullable|boolean',
            'fm200_cylinder_pressure_bar' => 'required|numeric|min:0',
            'rfid_chart_locator_status' => 'required|in:indexed_online,chart_checked_out,inventory_audit',
        ]);

        $archive = MedicalRecordsPhysicalArchive::create($validated);
        $archive->checkFireReadiness();

        return redirect()->back()->with('success', 'Kiểm tra kho lưu trữ hồ sơ bệnh án giấy & Khí sạch FM-200 [' . $archive->compactor_bay_code . '] hoàn tất!');
    }

    public function storeDayCareInfusion(Request $request)
    {
        $validated = $request->validate([
            'recliner_bay_code' => 'required|string|max:50',
            'patient_id' => 'required|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'infusion_regimen_type' => 'required|in:chemotherapy_paclitaxel,biologic_rituximab,intravenous_iron_sucrose,post_op_hydration_antibiotic',
            'scheduled_duration_hours' => 'required|numeric|min:0.5',
            'infusion_rate_ml_hr' => 'required|integer|min:1',
            'recliner_massage_heat_active' => 'nullable|boolean',
            'personal_tablet_connected' => 'nullable|boolean',
        ]);

        $bay = DayCareInfusionLounge::create($validated);

        return redirect()->back()->with('success', 'Tiếp nhận bệnh nhân vào ghế truyền dịch & hóa chất ban ngày [' . $bay->recliner_bay_code . '] thành công!');
    }

    public function triggerInfusionEmergency($id)
    {
        $bay = DayCareInfusionLounge::findOrFail($id);
        $bay->triggerAnaphylaxisEmergency();

        return redirect()->back()->with('error', 'CẢNH BÁO KHẨN CẤP: Đã kích hoạt báo động sốc phản vệ tại ghế [' . $bay->recliner_bay_code . '] & tự động ngắt truyền dịch!');
    }

    public function completeInfusion($id)
    {
        $bay = DayCareInfusionLounge::findOrFail($id);
        $bay->completeInfusion();

        return redirect()->back()->with('success', 'Xác nhận hoàn tất chu trình truyền dịch cho bệnh nhân ghế [' . $bay->recliner_bay_code . ']!');
    }
}
