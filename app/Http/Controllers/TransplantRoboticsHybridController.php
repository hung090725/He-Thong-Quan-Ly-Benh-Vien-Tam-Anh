<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganTransplantCoordination;
use App\Models\RoboticSurgerySession;
use App\Models\HybridOrNavigationPlan;

class TransplantRoboticsHybridController extends Controller
{
    public function index()
    {
        $stats = [
            'total_transplants' => OrganTransplantCoordination::count(),
            'successful_grafts' => OrganTransplantCoordination::where('graft_status_day30', 'functional_good')->count(),
            'robotic_sessions' => RoboticSurgerySession::count(),
            'active_instruments_warning' => RoboticSurgerySession::where('instrument_lifespan_remaining', '<=', 2)->count(),
            'hybrid_or_plans' => HybridOrNavigationPlan::count(),
            'recent_transplants' => OrganTransplantCoordination::with('patient')->latest()->take(5)->get(),
            'recent_robotics' => RoboticSurgerySession::with('patient')->latest()->take(5)->get(),
            'recent_hybrid' => HybridOrNavigationPlan::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.transplant_robotics.index', compact('stats'));
    }

    public function storeTransplant(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'lead_surgeon_id' => 'required|exists:doctors,id',
            'organ_type' => 'required|in:kidney,liver,heart,lung,cornea',
            'donor_type' => 'required|in:living_related,living_unrelated,brain_dead_deceased',
            'donor_code' => 'required|string|max:50',
            'hla_mismatch_count' => 'required|integer|between:0,6',
            'crossmatch_result' => 'required|in:negative,positive,borderline',
            'cold_ischemia_time_hours' => 'required|numeric|min:0',
            'warm_ischemia_time_minutes' => 'nullable|numeric|min:0',
            'immunosuppression_protocol' => 'nullable|string|max:200',
            'tacrolimus_trough_level_ng_ml' => 'nullable|numeric',
            'graft_status_day30' => 'required|in:functional_good,delayed_graft_function,acute_rejection,primary_non_function',
            'clinical_notes' => 'nullable|string|max:1000',
        ]);

        $evalHla = OrganTransplantCoordination::evaluateHlaCompatibility(
            (int)$validated['hla_mismatch_count'],
            $validated['crossmatch_result']
        );

        $evalCold = OrganTransplantCoordination::evaluateColdIschemiaLimit(
            $validated['organ_type'],
            (float)$validated['cold_ischemia_time_hours']
        );

        $record = OrganTransplantCoordination::create(array_merge($validated, [
            'warm_ischemia_time_minutes' => $validated['warm_ischemia_time_minutes'] ?? 0,
            'clinical_notes' => trim(($validated['clinical_notes'] ?? '') . ' | Đánh giá HLA: ' . $evalHla['recommendation'] . ' | Thiếu máu lạnh: ' . $evalCold['recommendation']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'hla_eval' => $evalHla, 'cold_eval' => $evalCold]);
        }

        return redirect()->back()->with('success', 'Hồ sơ Điều phối Ghép tạng đã được lưu thành công.');
    }

    public function storeRobotic(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'surgeon_id' => 'required|exists:doctors,id',
            'robot_system_serial' => 'required|string|max:50',
            'surgical_procedure_name' => 'required|string|max:150',
            'console_time_minutes' => 'required|integer|min:1',
            'docking_time_minutes' => 'nullable|integer|min:1',
            'instruments_used_json' => 'nullable|array',
            'instrument_lifespan_remaining' => 'required|integer|min:0|max:10',
            'conversion_to_open_surgery' => 'nullable|boolean',
            'estimated_blood_loss_ml' => 'nullable|numeric|min:0',
            'postop_hospital_stay_days' => 'nullable|numeric|min:0',
            'clinical_summary' => 'nullable|string|max:1000',
        ]);

        $safeCheck = RoboticSurgerySession::checkInstrumentSafety((int)$validated['instrument_lifespan_remaining']);

        $record = RoboticSurgerySession::create(array_merge($validated, [
            'docking_time_minutes' => $validated['docking_time_minutes'] ?? 15,
            'instruments_used_json' => $validated['instruments_used_json'] ?? ['Monopolar Curved Scissors', 'Maryland Bipolar Forceps', 'ProGrasp Forceps'],
            'instrument_retirement_alert' => $safeCheck['alert'],
            'conversion_to_open_surgery' => (bool)($validated['conversion_to_open_surgery'] ?? false),
            'estimated_blood_loss_ml' => $validated['estimated_blood_loss_ml'] ?? 50,
            'postop_hospital_stay_days' => $validated['postop_hospital_stay_days'] ?? 2.0,
            'clinical_summary' => trim(($validated['clinical_summary'] ?? '') . ' | Tay dao: ' . $safeCheck['message']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'instrument_status' => $safeCheck]);
        }

        return redirect()->back()->with('success', 'Phiên phẫu thuật Robot Da Vinci đã ghi nhận thành công.');
    }

    public function storeHybridOr(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'primary_operator_id' => 'required|exists:doctors,id',
            'procedure_type' => 'required|in:tavi_tavr,tevar_evar,cerebral_aneurysm_coiling,spine_navigation_fusion,complex_congenital_heart',
            'navigation_modality' => 'required|in:artisan_angio_ct,stealth_station_optical,electromagnetic_em,fusion_roadmapping',
            'target_registration_error_mm' => 'required|numeric|min:0',
            'contrast_media_volume_ml' => 'required|numeric|min:0',
            'patient_baseline_egfr' => 'nullable|numeric|min:1',
            'fluoroscopy_time_minutes' => 'nullable|numeric|min:0',
            'radiation_dose_dap_mgycm2' => 'nullable|numeric|min:0',
            'procedural_success' => 'nullable|boolean',
            'complications_encountered' => 'nullable|string|max:500',
        ]);

        $treEval = HybridOrNavigationPlan::assessTreAccuracy((float)$validated['target_registration_error_mm']);
        $cinRisk = HybridOrNavigationPlan::calculateCinRisk(
            (float)$validated['contrast_media_volume_ml'],
            isset($validated['patient_baseline_egfr']) ? (float)$validated['patient_baseline_egfr'] : null
        );

        $record = HybridOrNavigationPlan::create(array_merge($validated, [
            'contrast_induced_nephropathy_risk' => $cinRisk['risk_level'],
            'fluoroscopy_time_minutes' => $validated['fluoroscopy_time_minutes'] ?? 10.0,
            'radiation_dose_dap_mgycm2' => $validated['radiation_dose_dap_mgycm2'] ?? 2500.0,
            'procedural_success' => (bool)($validated['procedural_success'] ?? true),
            'complications_encountered' => trim(($validated['complications_encountered'] ?? '') . ' | TRE: ' . $treEval['interpretation'] . ' | CIN: ' . $cinRisk['warning']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'tre' => $treEval, 'cin' => $cinRisk]);
        }

        return redirect()->back()->with('success', 'Kế hoạch can thiệp Phòng mổ Hybrid đã được lưu trữ an toàn.');
    }
}
