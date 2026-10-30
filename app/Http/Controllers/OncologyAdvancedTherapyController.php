<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RadiationOncologyPlan;
use App\Models\CarTCellTherapy;
use App\Models\MolecularTumorBoard;
use App\Models\AwakeCraniotomyMapping;
use App\Models\PrecisionAntibioticPkPd;

class OncologyAdvancedTherapyController extends Controller
{
    public function index()
    {
        $stats = [
            'active_radiation_plans' => RadiationOncologyPlan::whereIn('treatment_status', ['planning', 'in_treatment'])->count(),
            'cart_sessions' => CarTCellTherapy::count(),
            'severe_crs_alerts' => CarTCellTherapy::whereIn('astct_crs_grade', ['grade_3_vasopressors_high_o2', 'grade_4_mechanical_ventilation'])->count(),
            'mtb_approved_sessions' => MolecularTumorBoard::where('mtb_consensus_decision', 'unanimous_approved')->count(),
            'awake_craniotomies' => AwakeCraniotomyMapping::count(),
            'pkpd_target_achieved' => PrecisionAntibioticPkPd::where('auc_target_achieved', true)->count(),
            'recent_radiation' => RadiationOncologyPlan::with('patient')->latest()->take(5)->get(),
            'recent_cart' => CarTCellTherapy::with('patient')->latest()->take(5)->get(),
            'recent_mtb' => MolecularTumorBoard::with('patient')->latest()->take(5)->get(),
            'recent_awake' => AwakeCraniotomyMapping::with('patient')->latest()->take(5)->get(),
            'recent_pkpd' => PrecisionAntibioticPkPd::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.oncology_advanced.index', compact('stats'));
    }

    public function storeRadiationPlan(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'plan_code' => 'required|string|max:50',
            'cancer_site' => 'required|string|max:100',
            'radiation_technique' => 'required|in:imrt,vmat,sbrt,srs,3d_crt',
            'total_prescribed_dose_gy' => 'required|numeric|min:1',
            'fraction_count' => 'required|integer|min:1',
            'dose_per_fraction_gy' => 'required|numeric|min:0.5',
            'dibh_breath_hold_used' => 'nullable|boolean',
            'four_d_ct_motion_tracked' => 'nullable|boolean',
            'heart_mean_dose_gy' => 'nullable|numeric|min:0',
            'spinal_cord_max_dose_gy' => 'nullable|numeric|min:0',
            'treatment_status' => 'required|in:planning,quality_assurance_passed,in_treatment,completed',
        ]);

        $heartDose = isset($validated['heart_mean_dose_gy']) ? (float)$validated['heart_mean_dose_gy'] : null;
        $cordDose = isset($validated['spinal_cord_max_dose_gy']) ? (float)$validated['spinal_cord_max_dose_gy'] : null;

        $oar = RadiationOncologyPlan::verifyOarTolerances($heartDose, $cordDose);

        $record = RadiationOncologyPlan::create(array_merge($validated, [
            'dibh_breath_hold_used' => (bool)($validated['dibh_breath_hold_used'] ?? false),
            'four_d_ct_motion_tracked' => (bool)($validated['four_d_ct_motion_tracked'] ?? false),
            'oar_tolerance_verified' => $oar['verified'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'oar' => $oar]);
        }

        return redirect()->back()->with('success', 'Kế hoạch xạ trị ung thư chính xác 4D đã được lưu: ' . $oar['summary']);
    }

    public function storeCarT(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'cart_product_name' => 'required|string|max:100',
            'target_antigen' => 'required|string|max:50',
            'infusion_date' => 'nullable|date',
            'peak_temperature_c' => 'required|numeric|between:35,43',
            'systolic_bp' => 'required|numeric|between:40,250',
            'spo2_pct' => 'required|numeric|between:50,100',
            'needs_vasopressor' => 'nullable|boolean',
            'needs_ventilator' => 'nullable|boolean',
            'icans_neurotoxicity_grade' => 'required|in:grade_0,grade_1,grade_2,grade_3,grade_4',
            'tocilizumab_doses_given' => 'nullable|integer|min:0',
            'dexamethasone_given' => 'nullable|boolean',
            'therapy_outcome' => 'required|in:monitoring,complete_remission,partial_response,relapsed,fatal_toxicity',
        ]);

        $crs = CarTCellTherapy::gradeCrs(
            (float)$validated['peak_temperature_c'],
            (float)$validated['systolic_bp'],
            (float)$validated['spo2_pct'],
            (bool)($validated['needs_vasopressor'] ?? false),
            (bool)($validated['needs_ventilator'] ?? false)
        );

        $record = CarTCellTherapy::create(array_merge($validated, [
            'astct_crs_grade' => $crs['grade'],
            'tocilizumab_indicated' => $crs['tocilizumab'],
            'tocilizumab_doses_given' => $validated['tocilizumab_doses_given'] ?? 0,
            'dexamethasone_given' => (bool)($validated['dexamethasone_given'] ?? false),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'crs' => $crs]);
        }

        return redirect()->back()->with('success', 'Đánh giá bão Cytokine CAR-T hoàn tất: ' . $crs['action']);
    }

    public function storeMtb(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'chairperson_doctor_id' => 'required|exists:doctors,id',
            'board_session_code' => 'required|string|max:50',
            'primary_cancer_diagnosis' => 'required|string|max:200',
            'actionable_mutations_json' => 'nullable|array',
            'tumor_mutational_burden_tmb' => 'nullable|string|max:50',
            'msi_status' => 'required|in:msi_high,msi_stable,undetermined',
            'off_label_or_trial_therapies_proposed' => 'nullable|string|max:1000',
        ]);

        $mutations = $validated['actionable_mutations_json'] ?? [];
        $rec = MolecularTumorBoard::synthesizeMtbRecommendation(
            $mutations,
            $validated['msi_status'],
            $validated['tumor_mutational_burden_tmb'] ?? null
        );

        $record = MolecularTumorBoard::create(array_merge($validated, [
            'mtb_consensus_decision' => $rec['decision'],
            'consensus_rational_summary' => $rec['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'recommendation' => $rec]);
        }

        return redirect()->back()->with('success', 'Hội đồng Ung bướu Phân tử (MTB) đã phê duyệt phác đồ: ' . $rec['summary']);
    }

    public function storeAwakeCraniotomy(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'neurosurgeon_id' => 'required|exists:doctors,id',
            'tumor_eloquent_location' => 'required|string|max:150',
            'anesthesia_technique' => 'required|in:asleep_awake_asleep_aaa,monitored_anesthesia_care_mac',
            'cortical_stimulation_current_ma' => 'required|numeric|between:0.5,10',
            'speech_arrest_detected' => 'nullable|boolean',
            'motor_twitching_detected' => 'nullable|boolean',
            'safety_margin_to_eloquent_cortex_mm' => 'required|numeric|min:0',
            'extent_of_resection_pct' => 'required|numeric|between:0,100',
            'intraoperative_seizure_occurred' => 'nullable|boolean',
            'postop_neurological_status' => 'nullable|string|max:1000',
        ]);

        $speechArrest = (bool)($validated['speech_arrest_detected'] ?? false);
        $motorTwitch = (bool)($validated['motor_twitching_detected'] ?? false);

        $boundary = AwakeCraniotomyMapping::evaluateSurgicalBoundary(
            $speechArrest,
            $motorTwitch,
            (float)$validated['safety_margin_to_eloquent_cortex_mm']
        );

        $record = AwakeCraniotomyMapping::create(array_merge($validated, [
            'speech_arrest_detected' => $speechArrest,
            'motor_twitching_detected' => $motorTwitch,
            'intraoperative_seizure_occurred' => (bool)($validated['intraoperative_seizure_occurred'] ?? false),
            'postop_neurological_status' => trim(($validated['postop_neurological_status'] ?? '') . ' | Chỉ dẫn: ' . $boundary['instruction']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'boundary' => $boundary]);
        }

        return redirect()->back()->with('success', 'Ghi nhận kích thích vỏ não mổ não tỉnh thức hoàn tất: ' . $boundary['instruction']);
    }

    public function storePkPd(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'antibiotic_name' => 'required|string|max:100',
            'pathogen_name' => 'required|string|max:150',
            'mic_mg_per_l' => 'required|numeric|min:0.01',
            'current_serum_trough_mg_per_l' => 'nullable|numeric|min:0',
            'current_serum_peak_mg_per_l' => 'nullable|numeric|min:0',
            'estimated_auc24_mg_h_per_l' => 'nullable|numeric|min:0',
        ]);

        $trough = isset($validated['current_serum_trough_mg_per_l']) ? (float)$validated['current_serum_trough_mg_per_l'] : null;
        $aucEst = isset($validated['estimated_auc24_mg_h_per_l']) ? (float)$validated['estimated_auc24_mg_h_per_l'] : null;

        $target = PrecisionAntibioticPkPd::calculateBayesianTarget(
            $validated['antibiotic_name'],
            (float)$validated['mic_mg_per_l'],
            $trough,
            $aucEst
        );

        $record = PrecisionAntibioticPkPd::create(array_merge($validated, [
            'estimated_auc24_mg_h_per_l' => $target['auc24'],
            'auc24_over_mic_ratio' => $target['ratio'],
            'auc_target_achieved' => $target['achieved'],
            'bayesian_recommended_dose' => $target['recommendation'],
            'nephrotoxicity_risk_alert' => $target['nephrotoxic_alert'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'pkpd' => $target]);
        }

        return redirect()->back()->with('success', 'Mô hình Bayesian Dược động học PK/PD đã tính toán thành công: ' . $target['recommendation']);
    }
}
