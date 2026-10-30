<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProtectiveCleanroomPe;
use App\Models\LeadLinedRadiologySafety;
use App\Models\PathologyMortuaryStorage;
use App\Models\EndoscopySuiteTurnover;
use App\Models\CleanroomPharmacyCompounding;
use App\Models\PatientProfile;

class SpecialtyHighRiskRoomController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pe_rooms' => ProtectiveCleanroomPe::count(),
            'compliant_pe_rooms' => ProtectiveCleanroomPe::where('safety_compliance', 'pe_compliant')->count(),
            'radiology_suites' => LeadLinedRadiologySafety::count(),
            'active_scanning_rad' => LeadLinedRadiologySafety::where('safety_status', 'scanning_in_progress')->count(),
            'mortuary_units' => PathologyMortuaryStorage::count(),
            'occupied_cold_units' => PathologyMortuaryStorage::where('storage_status', 'occupied_secured')->count(),
            'endoscopy_suites' => EndoscopySuiteTurnover::count(),
            'ready_endo_suites' => EndoscopySuiteTurnover::where('readiness_status', 'suite_ready')->count(),
            'cleanroom_pharmacy_suites' => CleanroomPharmacyCompounding::count(),
            'sterile_certified_batches' => CleanroomPharmacyCompounding::where('batch_sterility_status', 'certified_sterile_pass')->count(),
            'recent_pe_cleanrooms' => ProtectiveCleanroomPe::with('patient')->latest()->take(5)->get(),
            'recent_radiology' => LeadLinedRadiologySafety::latest()->take(5)->get(),
            'recent_mortuary' => PathologyMortuaryStorage::latest()->take(5)->get(),
            'recent_endoscopy' => EndoscopySuiteTurnover::latest()->take(5)->get(),
            'recent_cleanroom_pharmacy' => CleanroomPharmacyCompounding::latest()->take(5)->get(),
            'patients' => PatientProfile::latest()->take(20)->get(),
        ];

        return view('reception.specialty_high_risk_room.index', compact('stats'));
    }

    public function storeProtectiveCleanroomPe(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:50',
            'patient_id' => 'required|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'differential_pressure_pa' => 'required|numeric',
            'hepa_filter_status' => 'required|in:normal_high_efficiency,maintenance_due,replacement_required',
            'air_changes_per_hour' => 'required|numeric|min:0',
            'door_airlock_alarm' => 'nullable|boolean',
            'aspergillus_spore_count' => 'required|numeric|min:0',
        ]);

        $pe = ProtectiveCleanroomPe::create($validated);
        $pe->evaluateCompliance();

        return redirect()->back()->with('success', 'Ghi nhận giám sát phòng cách ly áp lực dương ghép tủy [' . $pe->room_code . '] thành công!');
    }

    public function storeLeadLinedRadiology(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:50',
            'modality_type' => 'required|in:ct_scanner_multislice,mri_3_tesla,digital_xray,angiography_cath_lab',
            'radiation_warning_light' => 'required|in:light_off_safe,amber_prep,red_xray_emitting',
            'lead_door_sealed' => 'nullable|boolean',
            'lead_glass_thickness_mm' => 'required|numeric|min:1.0',
            'ferromagnetic_mri_alarm' => 'nullable|boolean',
            'interlock_lock_engaged' => 'nullable|boolean',
            'ambient_radiation_dose_rate_usvh' => 'required|numeric|min:0',
        ]);

        $rad = LeadLinedRadiologySafety::create($validated);

        return redirect()->back()->with('success', 'Thiết lập an toàn bức xạ phòng bọc chì [' . $rad->room_code . '] thành công!');
    }

    public function triggerBeamEmission($id)
    {
        $rad = LeadLinedRadiologySafety::findOrFail($id);
        $result = $rad->triggerBeamEmission();

        if ($result) {
            return redirect()->back()->with('success', 'Khóa liên động xác nhận cửa đóng kín - Máy phát tia phòng [' . $rad->room_code . '] đã bắt đầu quét an toàn!');
        } else {
            return redirect()->back()->with('error', 'CẢNH BÁO: Cửa chì chưa đóng hoặc có rủi ro từ trường - Khóa liên động tự động ngắt phát tia!');
        }
    }

    public function storePathologyMortuary(Request $request)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:50',
            'storage_type' => 'required|in:standard_cold_4c,deep_freeze_minus15c,pathology_grossing_station',
            'deceased_record_id' => 'nullable|string|max:50',
            'rfid_tag_identifier' => 'nullable|string|max:50',
            'temperature_celsius' => 'required|numeric',
            'formalin_vapor_ppm' => 'required|numeric|min:0',
            'exhaust_ventilation_status' => 'required|in:running_nominal,high_speed_purge,fan_failure_alert',
            'identity_verified_by_two_staff' => 'nullable|boolean',
            'storage_status' => 'required|in:vacant_sanitized,occupied_secured,grossing_active,dispatched_funeral',
        ]);

        $mort = PathologyMortuaryStorage::create($validated);
        $mort->checkEnvironmentalSafety();

        return redirect()->back()->with('success', 'Ghi nhận quản lý nhà đại thể & phòng bệnh phẩm [' . $mort->unit_code . '] thành công!');
    }

    public function storeEndoscopyTurnover(Request $request)
    {
        $validated = $request->validate([
            'suite_code' => 'required|string|max:50',
            'procedure_type' => 'required|in:gastroscopy,colonoscopy,bronchoscopy,ercp_biliary',
            'endoscope_serial_code' => 'required|string|max:50',
            'turnaround_minutes' => 'required|integer|min:5',
            'aer_reprocessor_id' => 'required|string|max:50',
            'peracetic_acid_test_passed' => 'nullable|boolean',
            'biopsy_channel_dried' => 'nullable|boolean',
        ]);

        $endo = EndoscopySuiteTurnover::create($validated);
        $endo->certifySuiteReady();

        return redirect()->back()->with('success', 'Quản trị chu chuyển & tiệt khuẩn ống soi buồng [' . $endo->suite_code . '] hoàn tất!');
    }

    public function storeCleanroomPharmacy(Request $request)
    {
        $validated = $request->validate([
            'cleanroom_code' => 'required|string|max:50',
            'iso_classification' => 'required|in:iso_class_5,iso_class_7,iso_class_8',
            'cascade_pressure_pa' => 'required|numeric',
            'air_particle_count_0_5um' => 'required|integer|min:0',
            'air_shower_interlock_active' => 'nullable|boolean',
            'compounding_type' => 'required|in:neonatal_tpn_nutrition,ophthalmic_eye_drops,intrathecal_antibiotic',
            'pharmacist_lead_name' => 'required|string|max:255',
        ]);

        $clean = CleanroomPharmacyCompounding::create($validated);
        $clean->verifyIsoCompliance();

        return redirect()->back()->with('success', 'Kiểm tra độ sạch ISO buồng pha chế thuốc vô trùng [' . $clean->cleanroom_code . '] thành công!');
    }
}
