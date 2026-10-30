<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmartInfusionPumpDers;
use App\Models\IvYsiteIncompatibility;
use App\Models\MassiveTransfusionProtocol;
use App\Models\PatientBloodManagement;

class CriticalCareResuscitationController extends Controller
{
    public function index()
    {
        $stats = [
            'active_ders_pumps' => SmartInfusionPumpDers::where('hard_limit_violation', false)->count(),
            'ders_violations' => SmartInfusionPumpDers::where('hard_limit_violation', true)->count(),
            'active_mtp_sessions' => MassiveTransfusionProtocol::where('mtp_status', 'in_progress')->count(),
            'pbm_cell_saver_cases' => PatientBloodManagement::where('cell_saver_used', true)->count(),
            'recent_ders' => SmartInfusionPumpDers::with('patient')->latest()->take(5)->get(),
            'recent_mtp' => MassiveTransfusionProtocol::with('patient')->latest()->take(5)->get(),
            'recent_pbm' => PatientBloodManagement::with('patient')->latest()->take(5)->get(),
            'recent_ysite' => IvYsiteIncompatibility::latest()->take(5)->get(),
        ];

        return view('reception.critical_resuscitation.index', compact('stats'));
    }

    public function storeDers(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'pump_device_code' => 'required|string|max:50',
            'care_unit_profile' => 'required|in:icu_adult,nicu_pediatric,operating_room,cardiology_ward',
            'medication_name' => 'required|string|max:100',
            'concentration_unit' => 'required|string|max:50',
            'dose_rate' => 'required|numeric|min:0',
            'dose_unit' => 'required|string|max:30',
            'patient_weight_kg' => 'nullable|numeric|min:0.5',
            'lower_soft_limit' => 'nullable|numeric',
            'upper_soft_limit' => 'nullable|numeric',
            'upper_hard_limit' => 'required|numeric',
            'infusion_rate_ml_hr' => 'nullable|numeric',
            'override_authorized_by' => 'nullable|string|max:100',
            'clinical_indication' => 'nullable|string|max:500',
        ]);

        $eval = SmartInfusionPumpDers::evaluateDersGuardrail(
            (float)$validated['dose_rate'],
            isset($validated['upper_soft_limit']) ? (float)$validated['upper_soft_limit'] : null,
            (float)$validated['upper_hard_limit']
        );

        $calculatedRate = $validated['infusion_rate_ml_hr'] ?? null;
        if (!$calculatedRate && !empty($validated['patient_weight_kg'])) {
            $calculatedRate = SmartInfusionPumpDers::calculateInfusionRateMlHr(
                (float)$validated['dose_rate'],
                (float)$validated['patient_weight_kg'],
                1.0 // nồng độ mg/mL chuẩn quy đổi
            );
        }

        $record = SmartInfusionPumpDers::create(array_merge($validated, [
            'soft_limit_exceeded' => $eval['soft_limit_exceeded'],
            'hard_limit_violation' => $eval['hard_limit_violation'],
            'infusion_rate_ml_hr' => $calculatedRate ?? 0,
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'alert' => $eval]);
        }

        return redirect()->back()->with('success', 'Đã cấu hình thiết lập bơm tiêm thông minh DERS thành công! Trạng thái: ' . $eval['message']);
    }

    public function checkYsite(Request $request)
    {
        $validated = $request->validate([
            'primary_drug_name' => 'required|string|max:100',
            'secondary_drug_name' => 'required|string|max:100',
            'carrier_solution' => 'nullable|string|max:100',
            'incompatibility_type' => 'nullable|in:compatible,precipitate_formation,turbidity_color_change,chemical_degradation,uncertain',
            'clinical_hazard_summary' => 'nullable|string|max:1000',
            'recommended_action' => 'nullable|string|max:500',
        ]);

        $incompat = IvYsiteIncompatibility::checkCompatibility(
            $validated['primary_drug_name'],
            $validated['secondary_drug_name']
        );

        $record = IvYsiteIncompatibility::create(array_merge($validated, [
            'carrier_solution' => $validated['carrier_solution'] ?? 'NaCl 0.9%',
            'incompatibility_type' => $incompat['type'],
            'clinical_hazard_summary' => $incompat['hazard'],
            'recommended_action' => $incompat['recommendation'],
            'evidence_source' => 'Trissel\'s 2 Multi-Drug Y-Site Compatibility Database 2026',
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $incompat]);
        }

        return redirect()->back()->with('success', 'Tra cứu tương kỵ Y-site hoàn tất: ' . $incompat['hazard']);
    }

    public function storeMtp(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'protocol_code' => 'required|string|max:50',
            'rbc_units_issued' => 'required|integer|min:0',
            'ffp_units_issued' => 'required|integer|min:0',
            'platelet_doses_issued' => 'required|integer|min:0',
            'cryoprecipitate_units' => 'nullable|integer|min:0',
            'tranexamic_acid_administered' => 'nullable|boolean',
            'teg_r_time_minutes' => 'nullable|numeric',
            'teg_k_time_minutes' => 'nullable|numeric',
            'teg_maximum_amplitude_mm' => 'nullable|numeric',
            'rotem_fibtem_a10_mm' => 'nullable|numeric',
            'mtp_status' => 'required|in:in_progress,completed_hemostasis,patient_deceased',
            'clinical_notes' => 'nullable|string|max:1000',
        ]);

        $evalRatio = MassiveTransfusionProtocol::evaluateTransfusionRatio(
            (int)$validated['rbc_units_issued'],
            (int)$validated['ffp_units_issued'],
            (int)$validated['platelet_doses_issued']
        );

        $tegAnalysis = MassiveTransfusionProtocol::analyzeTegRotem(
            isset($validated['teg_r_time_minutes']) ? (float)$validated['teg_r_time_minutes'] : null,
            isset($validated['teg_maximum_amplitude_mm']) ? (float)$validated['teg_maximum_amplitude_mm'] : null,
            isset($validated['rotem_fibtem_a10_mm']) ? (float)$validated['rotem_fibtem_a10_mm'] : null
        );

        $record = MassiveTransfusionProtocol::create(array_merge($validated, [
            'balanced_ratio_achieved' => $evalRatio['balanced'],
            'cryoprecipitate_units' => $validated['cryoprecipitate_units'] ?? 0,
            'tranexamic_acid_administered' => (bool)($validated['tranexamic_acid_administered'] ?? false),
            'target_hemostasis_reached' => $validated['mtp_status'] === 'completed_hemostasis',
            'clinical_notes' => trim(($validated['clinical_notes'] ?? '') . ' | TEG Note: ' . $tegAnalysis['recommendation']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'teg_analysis' => $tegAnalysis]);
        }

        return redirect()->back()->with('success', 'Đã ghi nhận đợt kích hoạt MTP truyền máu tối cấp.');
    }

    public function storePbm(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'preop_hemoglobin_g_dl' => 'required|numeric|min:1',
            'preop_ferritin_ng_ml' => 'nullable|numeric',
            'tranexamic_acid_used' => 'nullable|boolean',
            'cell_saver_used' => 'nullable|boolean',
            'autologous_blood_salvaged_ml' => 'nullable|numeric',
            'postop_hemoglobin_g_dl' => 'nullable|numeric',
            'allogeneic_transfusion_units' => 'nullable|integer|min:0',
            'clinical_outcome' => 'nullable|string|max:500',
        ]);

        $anemiaAssessed = PatientBloodManagement::detectPreopAnemia(
            (float)$validated['preop_hemoglobin_g_dl'],
            isset($validated['preop_ferritin_ng_ml']) ? (float)$validated['preop_ferritin_ng_ml'] : null
        );

        $record = PatientBloodManagement::create(array_merge($validated, [
            'preop_anemia_detected' => $anemiaAssessed['is_anemic'],
            'tranexamic_acid_used' => (bool)($validated['tranexamic_acid_used'] ?? false),
            'cell_saver_used' => (bool)($validated['cell_saver_used'] ?? false),
            'autologous_blood_salvaged_ml' => $validated['autologous_blood_salvaged_ml'] ?? 0,
            'allogeneic_transfusion_units' => $validated['allogeneic_transfusion_units'] ?? 0,
            'pbm_protocol_bundle_compliant' => true,
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'anemia' => $anemiaAssessed]);
        }

        return redirect()->back()->with('success', 'Hồ sơ Quản lý Máu Bệnh nhân (PBM) đã được lưu thành công.');
    }
}
