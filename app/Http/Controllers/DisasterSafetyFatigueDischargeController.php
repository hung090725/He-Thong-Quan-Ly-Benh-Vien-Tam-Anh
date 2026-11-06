<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HospitalIncidentCommandHics;
use App\Models\BiometricSafekeepingVault;
use App\Models\FrozenSectionLogistics;
use App\Models\ClinicianFatigueCircadian;
use App\Models\DischargeLoungeMedsToBeds;
use App\Models\PatientProfile;
use App\Models\Doctor;

class DisasterSafetyFatigueDischargeController extends Controller
{
    public function index()
    {
        $stats = [
            'hics_incidents' => HospitalIncidentCommandHics::count(),
            'active_hics_mobilizing' => HospitalIncidentCommandHics::where('activation_status', 'active_mobilizing')->count(),
            'vault_items' => BiometricSafekeepingVault::count(),
            'secured_vault_items' => BiometricSafekeepingVault::where('custody_status', 'secured_in_vault')->count(),
            'frozen_section_cases' => FrozenSectionLogistics::count(),
            'clear_margins_cases' => FrozenSectionLogistics::where('histopathology_quick_result', 'benign_margins_clear')->count(),
            'fatigue_assessments' => ClinicianFatigueCircadian::count(),
            'grounded_sleep_alerts' => ClinicianFatigueCircadian::where('fitness_for_duty_status', 'grounded_mandatory_sleep')->count(),
            'discharge_lounge_patients' => DischargeLoungeMedsToBeds::count(),
            'bed_freed_early_count' => DischargeLoungeMedsToBeds::where('bed_freed_before_10am', true)->count(),
            'recent_hics' => HospitalIncidentCommandHics::latest()->take(5)->get(),
            'recent_vaults' => BiometricSafekeepingVault::with('patient')->latest()->take(5)->get(),
            'recent_frozen' => FrozenSectionLogistics::with('patient')->latest()->take(5)->get(),
            'recent_fatigue' => ClinicianFatigueCircadian::with('doctor')->latest()->take(5)->get(),
            'recent_discharge' => DischargeLoungeMedsToBeds::with('patient')->latest()->take(5)->get(),
            'patients' => PatientProfile::latest()->take(20)->get(),
            'doctors' => Doctor::with('specialty')->latest()->take(20)->get(),
        ];

        return view('reception.disaster_safety_fatigue_discharge.index', compact('stats'));
    }

    public function storeHicsDisaster(Request $request)
    {
        $validated = $request->validate([
            'incident_event_code' => 'required|string|max:50',
            'disaster_type' => 'required|in:code_silver_active_shooter,code_black_bomb_threat,code_yellow_mass_casualty,code_orange_hazardous_spill',
            'command_center_location' => 'required|string|max:100',
            'incident_commander_name' => 'required|string|max:255',
            'casualty_intake_expected' => 'required|integer|min:1',
            'beds_mobilized_count' => 'required|integer|min:1',
            'drill_or_actual' => 'required|in:scheduled_mock_drill,real_world_emergency',
        ]);

        $hics = HospitalIncidentCommandHics::create($validated);

        return redirect()->back()->with('success', 'Kích hoạt Trung Tâm Chỉ Huy Ứng Phó Thảm Họa HICS [' . $hics->incident_event_code . '] thành công!');
    }

    public function deEscalateHics($id)
    {
        $hics = HospitalIncidentCommandHics::findOrFail($id);
        $hics->deEscalateToStandby();

        return redirect()->back()->with('success', 'Đã chuyển trạng thái thảm họa [' . $hics->incident_event_code . '] về chế độ Dự phòng (Standby)!');
    }

    public function storeBiometricVault(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'sealed_bag_barcode' => 'required|string|max:50',
            'item_inventory_manifest' => 'required|string',
            'dual_sign_staff_1' => 'required|string|max:255',
            'dual_sign_staff_2' => 'required|string|max:255',
            'vault_locker_number' => 'required|string|max:20',
        ]);

        $vault = BiometricSafekeepingVault::create($validated);

        return redirect()->back()->with('success', 'Niêm phong két bảo mật kép tư trang bệnh nhân [' . $vault->patient_name . '] thành công!');
    }

    public function releaseVaultItems($id)
    {
        $vault = BiometricSafekeepingVault::findOrFail($id);
        $vault->releaseToGuardian();

        return redirect()->back()->with('success', 'Bàn giao tư trang trong két [' . $vault->vault_locker_number . '] cho thân nhân bệnh nhân ' . $vault->patient_name . ' hoàn tất!');
    }

    public function storeFrozenSection(Request $request)
    {
        $validated = $request->validate([
            'specimen_label_code' => 'required|string|max:50',
            'patient_id' => 'required|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'operating_room' => 'required|string|max:50',
            'transit_time_minutes' => 'required|numeric|min:0',
            'frozen_biopsy_cold_box_temp' => 'required|numeric',
            'pathology_consultant_name' => 'required|string|max:255',
            'histopathology_quick_result' => 'required|in:benign_margins_clear,malignant_adenocarcinoma,inconclusive_await_permanent',
            'turnaround_total_minutes' => 'required|integer|min:1',
        ]);

        $fs = FrozenSectionLogistics::create($validated);

        return redirect()->back()->with('success', 'Báo cáo kết quả sinh thiết lạnh tức thì phòng mổ [' . $fs->specimen_label_code . '] thành công!');
    }

    public function storeClinicianFatigue(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'doctor_name' => 'required|string|max:255',
            'shift_duration_hours' => 'required|numeric|min:0',
            'consecutive_surgical_hours' => 'required|numeric|min:0',
            'sleep_deficit_hours' => 'required|numeric|min:0',
            'fatigue_risk_index_score' => 'required|integer|between:0,100',
            'relief_physician_assigned' => 'nullable|string|max:255',
        ]);

        $fatigue = ClinicianFatigueCircadian::create($validated);
        $fatigue->evaluateFatigueRisk();

        return redirect()->back()->with('success', 'Đánh giá chỉ số mệt mỏi ca trực Fatigue Index BS [' . $fatigue->doctor_name . '] thành công!');
    }

    public function storeDischargeLounge(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'departed_ward_room' => 'required|string|max:50',
            'discharge_lounge_bay' => 'required|string|max:50',
            'clinical_pharmacist_name' => 'required|string|max:255',
            'bed_freed_before_10am' => 'nullable|boolean',
        ]);

        $discharge = DischargeLoungeMedsToBeds::create($validated);

        return redirect()->back()->with('success', 'Tiếp nhận bệnh nhân [' . $discharge->patient_name . '] vào Lounge chờ xuất viện thành công!');
    }

    public function completeMedsToBeds($id)
    {
        $discharge = DischargeLoungeMedsToBeds::findOrFail($id);
        $discharge->completeMedsDelivery();

        return redirect()->back()->with('success', 'Dược sĩ lâm sàng đã hoàn tất phát thuốc tận giường (Meds-to-Beds) cho bệnh nhân ' . $discharge->patient_name . '!');
    }

    public function dispatchPatientHome($id)
    {
        $discharge = DischargeLoungeMedsToBeds::findOrFail($id);
        $discharge->dispatchHome();

        return redirect()->back()->with('success', 'Bệnh nhân ' . $discharge->patient_name . ' đã xuất viện an toàn trở về nhà!');
    }
}
