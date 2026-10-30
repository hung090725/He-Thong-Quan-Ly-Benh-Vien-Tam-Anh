<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeleEmsMission;
use App\Models\MassCasualtyTriage;
use App\Models\FetalMonitoringCtg;
use App\Models\IvfEmbryoCycle;
use App\Models\PediatricEarlyWarningScore;
use App\Models\CoronaryPhysiologyImaging;
use App\Models\ExtendedStrokePerfusion;

class EmergencyMaternityCardioController extends Controller
{
    public function index()
    {
        $stats = [
            'active_tele_ems' => TeleEmsMission::where('mission_status', 'transporting')->count(),
            'mci_red_tags' => MassCasualtyTriage::where('start_triage_color', 'red_immediate')->count(),
            'ctg_pathological' => FetalMonitoringCtg::where('figo_classification', 'pathological')->count(),
            'high_score_embryos' => IvfEmbryoCycle::where('ai_implantation_potential_score', '>=', 85)->count(),
            'high_risk_pews' => PediatricEarlyWarningScore::where('risk_action_level', 'high_pediatric_met_activation')->count(),
            'ischemic_coronary_cases' => CoronaryPhysiologyImaging::where('hemodynamic_significance', 'ischemic_revascularization_indicated')->count(),
            'eligible_stroke_thrombectomy' => ExtendedStrokePerfusion::where('extended_window_decision', 'eligible_for_mechanical_thrombectomy')->count(),
            'recent_tele_ems' => TeleEmsMission::latest()->take(5)->get(),
            'recent_mci' => MassCasualtyTriage::latest()->take(5)->get(),
            'recent_ctg' => FetalMonitoringCtg::with('patient')->latest()->take(5)->get(),
            'recent_pews' => PediatricEarlyWarningScore::with('patient')->latest()->take(5)->get(),
            'recent_stroke' => ExtendedStrokePerfusion::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.emergency_maternity_cardio.index', compact('stats'));
    }

    public function storeTeleEms(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patient_profiles,id',
            'ambulance_callsign' => 'required|string|max:50',
            'paramedic_in_charge' => 'required|string|max:100',
            'location_gps' => 'nullable|string|max:100',
            'eta_hospital_minutes' => 'required|numeric|min:0',
            'heart_rate' => 'nullable|integer',
            'sbp' => 'nullable|numeric',
            'dbp' => 'nullable|numeric',
            'spo2' => 'nullable|numeric',
            'ecg_12lead_finding' => 'nullable|string|max:500',
            'efast_ultrasound_positive' => 'nullable|boolean',
            'pre_activation_target' => 'required|in:none,cathlab_stemi,stroke_team,trauma_or,icu',
            'hospital_team_ready' => 'nullable|boolean',
            'mission_status' => 'required|in:en_route_scene,on_scene,transporting,arrived_hospital,cancelled',
        ]);

        $teleAlert = TeleEmsMission::evaluatePreActivation(
            $validated['pre_activation_target'],
            (float)$validated['eta_hospital_minutes']
        );

        $record = TeleEmsMission::create(array_merge($validated, [
            'efast_ultrasound_positive' => (bool)($validated['efast_ultrasound_positive'] ?? false),
            'hospital_team_ready' => (bool)($validated['hospital_team_ready'] ?? false),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'alert' => $teleAlert]);
        }

        return redirect()->back()->with('success', 'Nhiệm vụ Cấp cứu Ngoại viện Tele-EMS 5G đã được cập nhật.');
    }

    public function storeMciTriage(Request $request)
    {
        $validated = $request->validate([
            'incident_name' => 'required|string|max:150',
            'disaster_code' => 'required|string|max:50',
            'victim_tag_number' => 'required|string|max:50|unique:mass_casualty_triages,victim_tag_number',
            'can_walk' => 'nullable|boolean',
            'respiratory_rate' => 'nullable|integer|min:0',
            'radial_pulse_palpable' => 'nullable|boolean',
            'mental_status' => 'required|in:follows_commands,does_not_follow',
            'primary_injuries' => 'nullable|string|max:500',
            'triaged_by' => 'required|string|max:100',
        ]);

        $canWalk = (bool)($validated['can_walk'] ?? false);
        $radial = (bool)($validated['radial_pulse_palpable'] ?? true);
        $rr = isset($validated['respiratory_rate']) ? (int)$validated['respiratory_rate'] : null;

        $triage = MassCasualtyTriage::calculateStartTriage($canWalk, $rr, $radial, $validated['mental_status']);

        $record = MassCasualtyTriage::create(array_merge($validated, [
            'can_walk' => $canWalk,
            'radial_pulse_palpable' => $radial,
            'start_triage_color' => $triage['color'],
            'assigned_zone' => $triage['zone'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'triage' => $triage]);
        }

        return redirect()->back()->with('success', 'Nạn nhân đã được phân loại START thành công: ' . $triage['zone']);
    }

    public function storeCtg(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'delivery_room_bed' => 'required|string|max:50',
            'baseline_fhr_bpm' => 'required|integer|between:50,220',
            'variability' => 'required|in:absent,minimal,moderate_normal,marked',
            'accelerations_count' => 'nullable|integer|min:0',
            'decelerations_type' => 'required|in:none,early_benign,variable_cord,late_uteroplacental_hypoxia,prolonged',
            'sinusoidal_pattern' => 'nullable|boolean',
            'uterine_contractions_per_10min' => 'required|integer|between:0,12',
            'ai_trace_interpretation' => 'nullable|string|max:1000',
        ]);

        $sinusoidal = (bool)($validated['sinusoidal_pattern'] ?? false);
        $ctgEval = FetalMonitoringCtg::classifyCtgTraces(
            (int)$validated['baseline_fhr_bpm'],
            $validated['variability'],
            $validated['decelerations_type'],
            $sinusoidal,
            (int)$validated['uterine_contractions_per_10min']
        );

        $record = FetalMonitoringCtg::create(array_merge($validated, [
            'sinusoidal_pattern' => $sinusoidal,
            'accelerations_count' => $validated['accelerations_count'] ?? 0,
            'figo_classification' => $ctgEval['figo'],
            'acog_category' => $ctgEval['acog'],
            'emergency_csection_alert' => $ctgEval['emergency_alert'],
            'ai_trace_interpretation' => $ctgEval['interpretation'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $ctgEval]);
        }

        return redirect()->back()->with('success', 'Đã phân tích bản ghi CTG Tim thai AI: ' . $ctgEval['interpretation']);
    }

    public function storeIvf(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'cycle_code' => 'required|string|max:50',
            'partner_name' => 'nullable|string|max:100',
            'partner_sample_code' => 'nullable|string|max:50',
            'oocytes_retrieved' => 'required|integer|min:0',
            'oocytes_fertilized' => 'required|integer|min:0',
            'embryo_id_tag' => 'required|string|max:50',
            'development_stage' => 'required|in:day1_zygote,day3_cleavage,day5_blastocyst,day6_blastocyst',
            'gardner_grade' => 'required|string|max:20',
            'electronic_witnessing_verified' => 'nullable|boolean',
            'nitrogen_tank_storage_position' => 'nullable|string|max:100',
            'embryo_status' => 'required|in:in_culture,transferred,cryopreserved,discarded',
        ]);

        $aiScore = IvfEmbryoCycle::calculateGardnerAiScore($validated['gardner_grade']);

        $record = IvfEmbryoCycle::create(array_merge($validated, [
            'ai_implantation_potential_score' => $aiScore,
            'electronic_witnessing_verified' => (bool)($validated['electronic_witnessing_verified'] ?? true),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'ai_score' => $aiScore]);
        }

        return redirect()->back()->with('success', 'Hồ sơ phôi IVF đã được lưu trữ an toàn (Điểm tiềm năng AI: ' . $aiScore . '%).');
    }

    public function storePews(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'age_group' => 'required|in:neonate_under_3m,infant_3_12m,toddler_1_4y,child_5_11y,adolescent_12y_plus',
            'behavior_score' => 'required|integer|between:0,3',
            'cardiovascular_score' => 'required|integer|between:0,3',
            'respiratory_score' => 'required|integer|between:0,3',
            'nebulizer_used' => 'nullable|boolean',
            'persistent_vomiting_postop' => 'nullable|boolean',
        ]);

        $nebulizer = (bool)($validated['nebulizer_used'] ?? false);
        $vomiting = (bool)($validated['persistent_vomiting_postop'] ?? false);

        $pews = PediatricEarlyWarningScore::calculatePews(
            (int)$validated['behavior_score'],
            (int)$validated['cardiovascular_score'],
            (int)$validated['respiratory_score'],
            $nebulizer,
            $vomiting
        );

        $record = PediatricEarlyWarningScore::create(array_merge($validated, [
            'nebulizer_used' => $nebulizer,
            'persistent_vomiting_postop' => $vomiting,
            'total_pews_score' => $pews['total_score'],
            'risk_action_level' => $pews['action_level'],
            'nursing_interventions' => $pews['recommendation'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'pews' => $pews]);
        }

        return redirect()->back()->with('success', 'Đánh giá PEWS hoàn tất: Tổng điểm ' . $pews['total_score']);
    }

    public function storeCoronary(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'target_artery' => 'required|string|max:50',
            'ffr_ratio' => 'nullable|numeric|between:0,1.5',
            'ifr_ratio' => 'nullable|numeric|between:0,1.5',
            'imaging_modality' => 'required|in:ivus,oct,angiography_only',
            'minimal_lumen_area_mm2' => 'nullable|numeric|min:0',
            'plaque_burden_pct' => 'nullable|numeric|between:0,100',
            'stent_malapposition_detected' => 'nullable|boolean',
            'stent_underexpansion_detected' => 'nullable|boolean',
            'operator_notes' => 'nullable|string|max:1000',
        ]);

        $ffr = isset($validated['ffr_ratio']) ? (float)$validated['ffr_ratio'] : null;
        $ifr = isset($validated['ifr_ratio']) ? (float)$validated['ifr_ratio'] : null;
        $mla = isset($validated['minimal_lumen_area_mm2']) ? (float)$validated['minimal_lumen_area_mm2'] : null;

        $ischemia = CoronaryPhysiologyImaging::assessIschemiaSignificance(
            $validated['target_artery'],
            $ffr,
            $ifr,
            $mla
        );

        $record = CoronaryPhysiologyImaging::create(array_merge($validated, [
            'stent_malapposition_detected' => (bool)($validated['stent_malapposition_detected'] ?? false),
            'stent_underexpansion_detected' => (bool)($validated['stent_underexpansion_detected'] ?? false),
            'hemodynamic_significance' => $ischemia['hemodynamic'],
            'operator_notes' => trim(($validated['operator_notes'] ?? '') . ' | Đánh giá: ' . $ischemia['decision']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'ischemia' => $ischemia]);
        }

        return redirect()->back()->with('success', 'Ghi nhận phân tích sinh lý & hình ảnh học mạch vành hoàn tất.');
    }

    public function storeStrokeExtended(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'onset_to_arrival_hours' => 'required|integer|between:1,48',
            'ischemic_core_volume_ml' => 'required|numeric|min:0',
            'hypoperfused_volume_ml' => 'required|numeric|min:0',
            'neurointervention_plan' => 'nullable|string|max:1000',
        ]);

        $evaluation = ExtendedStrokePerfusion::evaluateExtendedWindow(
            (int)$validated['onset_to_arrival_hours'],
            (float)$validated['ischemic_core_volume_ml'],
            (float)$validated['hypoperfused_volume_ml']
        );

        $record = ExtendedStrokePerfusion::create(array_merge($validated, [
            'mismatch_volume_ml' => $evaluation['mismatch_volume'],
            'mismatch_ratio' => $evaluation['mismatch_ratio'],
            'dawn_criteria_met' => $evaluation['dawn_met'],
            'defuse3_criteria_met' => $evaluation['defuse3_met'],
            'extended_window_decision' => $evaluation['decision'],
            'neurointervention_plan' => trim(($validated['neurointervention_plan'] ?? '') . ' | Đánh giá: ' . $evaluation['plan']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $evaluation]);
        }

        return redirect()->back()->with('success', 'Đánh giá đột quỵ cửa sổ mở rộng hoàn tất: ' . $evaluation['plan']);
    }
}
