<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EcmoTtmSession;
use App\Models\NegativePressureAiir;
use App\Models\ExtubationReadinessSbt;
use App\Models\TraumaResuscitationIss;
use App\Models\CrrtCitrateAnticoagulation;
use App\Models\IcuAbcdefBundlePics;

class CriticalExtracorporealLifeController extends Controller
{
    public function index()
    {
        $stats = [
            'active_ecmo_sessions' => EcmoTtmSession::where('session_status', 'running')->count(),
            'ecmo_membrane_alerts' => EcmoTtmSession::where('membrane_thrombosis_risk', true)->count(),
            'occupied_aiir_rooms' => NegativePressureAiir::whereNotNull('patient_id')->count(),
            'aiir_pressure_alarms' => NegativePressureAiir::where('pressure_failure_alarm', true)->count(),
            'ready_for_extubation_cases' => ExtubationReadinessSbt::where('sbt_outcome', 'passed_ready_for_extubation')->count(),
            'major_trauma_level_1' => TraumaResuscitationIss::where('trauma_level_1_activated', true)->count(),
            'active_crrt_citrate' => CrrtCitrateAnticoagulation::count(),
            'recent_ecmo' => EcmoTtmSession::with('patient')->latest()->take(5)->get(),
            'recent_aiir' => NegativePressureAiir::with('patient')->latest()->take(5)->get(),
            'recent_sbt' => ExtubationReadinessSbt::with('patient')->latest()->take(5)->get(),
            'recent_trauma' => TraumaResuscitationIss::with('patient')->latest()->take(5)->get(),
            'recent_crrt' => CrrtCitrateAnticoagulation::with('patient')->latest()->take(5)->get(),
            'recent_bundle' => IcuAbcdefBundlePics::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.critical_extracorporeal.index', compact('stats'));
    }

    public function storeEcmo(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'ecmo_modality' => 'required|in:va_ecmo_venoarterial,vv_ecmo_venovenous,vav_hybrid',
            'pump_flow_l_per_min' => 'required|numeric|min:0.5',
            'pump_speed_rpm' => 'required|integer|min:500',
            'sweep_gas_flow_l_per_min' => 'required|numeric|min:0.5',
            'fio2_sweep_pct' => 'required|numeric|between:21,100',
            'delta_pressure_membrane_mmhg' => 'required|numeric|min:5',
            'ttm_targeted_temperature_active' => 'nullable|boolean',
            'target_core_temp_c' => 'nullable|numeric|between:32,38',
            'actual_core_temp_c' => 'nullable|numeric|between:30,42',
            'session_status' => 'required|in:running,weaning,decannulated_success,mortality',
        ]);

        $evalMembrane = EcmoTtmSession::evaluateMembraneSafety((float)$validated['delta_pressure_membrane_mmhg']);

        $record = EcmoTtmSession::create(array_merge($validated, [
            'membrane_thrombosis_risk' => $evalMembrane['thrombosis_risk'],
            'ttm_targeted_temperature_active' => (bool)($validated['ttm_targeted_temperature_active'] ?? false),
            'target_core_temp_c' => $validated['target_core_temp_c'] ?? 33.0,
            'actual_core_temp_c' => $validated['actual_core_temp_c'] ?? 33.0,
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'membrane' => $evalMembrane]);
        }

        return redirect()->back()->with('success', 'Thông số ECMO & TTM đã được cập nhật: ' . $evalMembrane['status']);
    }

    public function storeAiir(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:50',
            'patient_id' => 'nullable|exists:patient_profiles,id',
            'pathogen_isolation_type' => 'required|string|max:100',
            'pressure_differential_pa' => 'required|numeric',
            'air_changes_per_hour_ach' => 'required|numeric|min:1',
            'hepa_filter_exhaust_passed' => 'nullable|boolean',
            'airlock_doors_interlocked' => 'nullable|boolean',
            'uvc_disinfection_active' => 'nullable|boolean',
        ]);

        $eval = NegativePressureAiir::evaluateAiirCompliance(
            (float)$validated['pressure_differential_pa'],
            (float)$validated['air_changes_per_hour_ach']
        );

        $record = NegativePressureAiir::updateOrCreate(
            ['room_code' => $validated['room_code']],
            array_merge($validated, [
                'hepa_filter_exhaust_passed' => (bool)($validated['hepa_filter_exhaust_passed'] ?? true),
                'airlock_doors_interlocked' => (bool)($validated['airlock_doors_interlocked'] ?? true),
                'pressure_failure_alarm' => $eval['alert'],
                'uvc_disinfection_active' => (bool)($validated['uvc_disinfection_active'] ?? false),
            ])
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'compliance' => $eval]);
        }

        return redirect()->back()->with('success', 'Giám sát buồng cách ly áp lực âm AIIR hoàn tất: ' . $eval['summary']);
    }

    public function storeSbt(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'sbt_duration_minutes' => 'required|integer|min:15',
            'respiratory_rate_f_bpm' => 'required|numeric|min:5',
            'tidal_volume_vt_liters' => 'required|numeric|min:0.05',
            'maximal_inspiratory_pressure_mip' => 'required|numeric',
            'cuff_leak_test_positive' => 'nullable|boolean',
        ]);

        $cuff = (bool)($validated['cuff_leak_test_positive'] ?? true);
        $readiness = ExtubationReadinessSbt::evaluateExtubationReadiness(
            (float)$validated['respiratory_rate_f_bpm'],
            (float)$validated['tidal_volume_vt_liters'],
            (float)$validated['maximal_inspiratory_pressure_mip'],
            $cuff
        );

        $record = ExtubationReadinessSbt::create(array_merge($validated, [
            'rsbi_score' => $readiness['rsbi'],
            'cuff_leak_test_positive' => $cuff,
            'sbt_outcome' => $readiness['outcome'],
            'extubation_successful_no_reintubation_48h' => $readiness['ready'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'readiness' => $readiness]);
        }

        return redirect()->back()->with('success', 'Thử nghiệm thở tự nhiên SBT & RSBI hoàn tất: ' . $readiness['recommendation']);
    }

    public function storeTraumaIss(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'trauma_leader_id' => 'required|exists:doctors,id',
            'ais_head_neck' => 'required|integer|between:0,6',
            'ais_face' => 'required|integer|between:0,6',
            'ais_chest' => 'required|integer|between:0,6',
            'ais_abdomen_pelvis' => 'required|integer|between:0,6',
            'ais_extremities_pelvic_girdle' => 'required|integer|between:0,6',
            'ais_external_burns' => 'required|integer|between:0,6',
            'revised_trauma_score_rts' => 'nullable|numeric|between:0,7.841',
        ]);

        $aisScores = [
            (int)$validated['ais_head_neck'],
            (int)$validated['ais_face'],
            (int)$validated['ais_chest'],
            (int)$validated['ais_abdomen_pelvis'],
            (int)$validated['ais_extremities_pelvic_girdle'],
            (int)$validated['ais_external_burns'],
        ];

        $issResult = TraumaResuscitationIss::calculateIss($aisScores);

        $record = TraumaResuscitationIss::create(array_merge($validated, [
            'calculated_iss_score' => $issResult['iss'],
            'trauma_level_1_activated' => $issResult['level_1'],
            'revised_trauma_score_rts' => $validated['revised_trauma_score_rts'] ?? 7.841,
            'trauma_interventions_summary' => $issResult['severity'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'iss' => $issResult]);
        }

        return redirect()->back()->with('success', 'Đánh giá thang điểm chấn thương ISS hoàn tất: ' . $issResult['severity']);
    }

    public function storeCrrt(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'crrt_modality' => 'required|in:cvvhdf_hemodiafiltration,cvvh_hemofiltration,cvvhd_hemodialysis',
            'effluent_dose_ml_per_kg_per_h' => 'required|numeric|min:10',
            'transmembrane_pressure_tmp_mmhg' => 'required|numeric|min:10',
            'pre_filter_citrate_infusion_mmol_per_l' => 'required|numeric|min:1',
            'post_filter_ionized_calcium_mmol_per_l' => 'required|numeric|min:0.1',
            'systemic_ionized_calcium_mmol_per_l' => 'required|numeric|min:0.5',
            'calcium_chloride_compensation_rate_ml_per_h' => 'required|numeric|min:0',
        ]);

        $safety = CrrtCitrateAnticoagulation::evaluateCitrateSafety(
            (float)$validated['post_filter_ionized_calcium_mmol_per_l'],
            (float)$validated['systemic_ionized_calcium_mmol_per_l'],
            (float)$validated['transmembrane_pressure_tmp_mmhg']
        );

        $record = CrrtCitrateAnticoagulation::create(array_merge($validated, [
            'filter_clotting_risk_pct' => $safety['clotting_risk_pct'],
            'citrate_accumulation_acidosis_alert' => !$safety['safe'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'safety' => $safety]);
        }

        return redirect()->back()->with('success', 'Lọc máu liên tục CRRT & Citrate RCA đã ghi nhận: ' . $safety['advice']);
    }

    public function storeBundle(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'bundle_a_pain_assessed_controlled' => 'nullable|boolean',
            'bundle_b_both_sat_sbt_performed' => 'nullable|boolean',
            'bundle_c_sedative_choice' => 'required|string|max:100',
            'bundle_d_cam_icu_delirium' => 'required|in:negative_clear,positive_delirium_present',
            'bundle_e_early_mobility_level' => 'required|in:level_1_passive_bed,level_2_active_sitting_edge,level_3_standing_transfer,level_4_walking_gait',
            'bundle_f_family_engagement_diary_active' => 'nullable|boolean',
        ]);

        $bundleA = (bool)($validated['bundle_a_pain_assessed_controlled'] ?? true);
        $bundleB = (bool)($validated['bundle_b_both_sat_sbt_performed'] ?? true);
        $bundleF = (bool)($validated['bundle_f_family_engagement_diary_active'] ?? true);

        $eval = IcuAbcdefBundlePics::evaluateBundleCompliance(
            $bundleA,
            $bundleB,
            $validated['bundle_c_sedative_choice'],
            $validated['bundle_d_cam_icu_delirium'],
            $validated['bundle_e_early_mobility_level'],
            $bundleF
        );

        $record = IcuAbcdefBundlePics::create(array_merge($validated, [
            'bundle_a_pain_assessed_controlled' => $bundleA,
            'bundle_b_both_sat_sbt_performed' => $bundleB,
            'bundle_f_family_engagement_diary_active' => $bundleF,
            'pics_post_intensive_care_risk_score' => $eval['pics_risk_pct'],
            'multidisciplinary_icu_notes' => $eval['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $eval]);
        }

        return redirect()->back()->with('success', 'Đánh giá gói ICU ABCDEF Bundle hoàn tất: ' . $eval['summary']);
    }
}
