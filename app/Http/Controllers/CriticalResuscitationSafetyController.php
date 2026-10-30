<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodeBlueAclsTracker;
use App\Models\MedicalGasPipeline;
use App\Models\ToxicologyHemoperfusion;
use App\Models\RotemViscoelasticCoagulation;
use App\Models\CriticalHelipadTransfer;

class CriticalResuscitationSafetyController extends Controller
{
    public function index()
    {
        $stats = [
            'total_code_blue_events' => CodeBlueAclsTracker::count(),
            'successful_rosc_cases' => CodeBlueAclsTracker::where('rosc_achieved', true)->count(),
            'monitored_gas_zones' => MedicalGasPipeline::count(),
            'low_pressure_alarms' => MedicalGasPipeline::where('low_pressure_emergency_alarm', true)->count(),
            'toxicology_cases' => ToxicologyHemoperfusion::count(),
            'hemoperfusion_active' => ToxicologyHemoperfusion::where('hemodialysis_indicated', true)->count(),
            'rotem_coagulation_profiles' => RotemViscoelasticCoagulation::count(),
            'hyperfibrinolysis_alerts' => RotemViscoelasticCoagulation::where('hyperfibrinolysis_detected', true)->count(),
            'helipad_missions' => CriticalHelipadTransfer::count(),
            'recent_code_blue' => CodeBlueAclsTracker::with('patient')->latest()->take(5)->get(),
            'recent_gas_pipelines' => MedicalGasPipeline::latest()->take(5)->get(),
            'recent_toxicology' => ToxicologyHemoperfusion::with('patient')->latest()->take(5)->get(),
            'recent_rotem' => RotemViscoelasticCoagulation::with('patient')->latest()->take(5)->get(),
            'recent_helipad' => CriticalHelipadTransfer::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.critical_resuscitation_safety.index', compact('stats'));
    }

    public function storeCodeBlue(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'code_team_leader_id' => 'required|exists:doctors,id',
            'event_location' => 'required|string|max:100',
            'arrest_witnessed_at' => 'nullable|date',
            'initial_rhythm' => 'required|in:ventricular_fibrillation_vf,pulseless_vt,asystole,pea_pulseless_electrical_activity',
            'cpr_total_duration_minutes' => 'required|integer|min:1',
            'chest_compression_fraction_pct' => 'required|numeric|between:0,100',
            'defibrillation_shocks_count' => 'required|integer|min:0',
            'epinephrine_doses_given' => 'required|integer|min:0',
            'amiodarone_doses_given' => 'nullable|integer|min:0',
            'advanced_airway_placed' => 'nullable|boolean',
            'etco2_during_cpr_mmhg' => 'required|numeric|min:0',
            'rosc_achieved' => 'nullable|boolean',
            'final_outcome' => 'required|in:admitted_to_icu,mortality_in_code,transferred_pci',
            'resuscitation_timeline_notes' => 'nullable|string|max:1000',
        ]);

        $rosc = (bool)($validated['rosc_achieved'] ?? false);
        $eval = CodeBlueAclsTracker::evaluateAclsResuscitationQuality(
            (float)$validated['chest_compression_fraction_pct'],
            (float)$validated['etco2_during_cpr_mmhg'],
            (int)$validated['defibrillation_shocks_count'],
            $rosc
        );

        $record = CodeBlueAclsTracker::create(array_merge($validated, [
            'advanced_airway_placed' => (bool)($validated['advanced_airway_placed'] ?? true),
            'rosc_achieved' => $rosc,
            'rosc_timestamp' => $rosc ? now() : null,
            'amiodarone_doses_given' => $validated['amiodarone_doses_given'] ?? 0,
            'resuscitation_timeline_notes' => trim(($validated['resuscitation_timeline_notes'] ?? '') . ' | ' . $eval['summary']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $eval]);
        }

        return redirect()->back()->with('success', 'Hồ sơ hồi sức ngừng tim Code Blue đã lưu: ' . $eval['summary']);
    }

    public function storeMedicalGas(Request $request)
    {
        $validated = $request->validate([
            'zone_manifold_code' => 'required|string|max:50',
            'building_floor' => 'required|string|max:50',
            'biomedical_engineer_id' => 'required|exists:doctors,id',
            'oxygen_line_pressure_bar' => 'required|numeric|min:0',
            'medical_air_pressure_bar' => 'required|numeric|min:0',
            'surgical_air_pressure_bar' => 'required|numeric|min:0',
            'vacuum_suction_pressure_bar' => 'required|numeric',
            'nitrous_oxide_pressure_bar' => 'required|numeric|min:0',
            'liquid_oxygen_tank_level_pct' => 'required|numeric|between:0,100',
            'liquid_oxygen_remaining_liters' => 'required|numeric|min:0',
            'active_ventilators_count' => 'required|integer|min:0',
            'pipeline_leak_detected' => 'nullable|boolean',
        ]);

        $eval = MedicalGasPipeline::evaluateMedicalGasSafety(
            (float)$validated['oxygen_line_pressure_bar'],
            (float)$validated['vacuum_suction_pressure_bar'],
            (float)$validated['liquid_oxygen_remaining_liters'],
            (int)$validated['active_ventilators_count']
        );

        $record = MedicalGasPipeline::updateOrCreate(
            ['zone_manifold_code' => $validated['zone_manifold_code']],
            array_merge($validated, [
                'estimated_hours_until_depletion' => $eval['hours_remaining'],
                'low_pressure_emergency_alarm' => $eval['emergency_alert'],
                'pipeline_leak_detected' => (bool)($validated['pipeline_leak_detected'] ?? false),
                'manifold_status_summary' => $eval['summary'],
            ])
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'safety' => $eval]);
        }

        return redirect()->back()->with('success', 'Trạng thái mạng lưới khí y tế trung tâm đã cập nhật: ' . $eval['summary']);
    }

    public function storeToxicology(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'toxicologist_id' => 'required|exists:doctors,id',
            'toxin_or_drug_name' => 'required|string|max:100',
            'estimated_ingestion_hours_ago' => 'required|numeric|min:0.1',
            'ingested_dose_mg_or_ml' => 'required|numeric|min:0',
            'serum_toxin_concentration_mcg_ml' => 'required|numeric|min:0',
            'rumack_matthew_toxicity_risk' => 'required|in:probable_toxicity,possible_toxicity,low_risk,not_applicable',
            'specific_antidote_given' => 'nullable|string|max:100',
            'extracorporeal_modality' => 'required|in:hemoperfusion_charcoal,hemoperfusion_resin,intermittent_hemodialysis,crrt_cvvhdf',
            'toxin_clearance_ml_min' => 'required|numeric|min:0',
            'recommended_duration_hours' => 'required|numeric|min:1',
        ]);

        $eval = ToxicologyHemoperfusion::evaluateToxicologyIntervention(
            $validated['toxin_or_drug_name'],
            (float)$validated['estimated_ingestion_hours_ago'],
            (float)$validated['serum_toxin_concentration_mcg_ml']
        );

        $record = ToxicologyHemoperfusion::create(array_merge($validated, [
            'hemodialysis_indicated' => $eval['extracorporeal_indicated'],
            'hepatotoxicity_or_nephrotoxicity_prevented' => true,
            'specific_antidote_given' => $validated['specific_antidote_given'] ?? $eval['antidote_recommended'],
            'toxicology_clinical_assessment' => $eval['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'toxicology' => $eval]);
        }

        return redirect()->back()->with('success', 'Hồ sơ ngộ độc cấp & lọc máu hấp phụ đã lưu: ' . $eval['summary']);
    }

    public function storeRotem(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'anesthesiologist_id' => 'required|exists:doctors,id',
            'clinical_scenario' => 'required|in:cardiac_surgery_pump,liver_transplant,massive_postpartum_hemorrhage,severe_polytrauma',
            'extem_ct_seconds' => 'required|integer|min:10',
            'extem_cft_seconds' => 'required|integer|min:10',
            'extem_alpha_angle_degrees' => 'required|numeric|between:20,90',
            'extem_mcf_mm' => 'required|integer|between:0,100',
            'extem_ml_pct' => 'required|numeric|between:0,100',
            'fibtem_mcf_mm' => 'required|integer|between:0,100',
            'aptem_mcf_mm' => 'nullable|integer|between:0,100',
        ]);

        $eval = RotemViscoelasticCoagulation::analyzeRotemParameters(
            (int)$validated['extem_ct_seconds'],
            (int)$validated['extem_mcf_mm'],
            (int)$validated['fibtem_mcf_mm'],
            (float)$validated['extem_ml_pct']
        );

        $record = RotemViscoelasticCoagulation::create(array_merge($validated, [
            'hyperfibrinolysis_detected' => $eval['hyperfibrinolysis'],
            'tranexamic_acid_indicated' => $eval['txa_indicated'],
            'targeted_blood_product_recommendation' => $eval['summary'],
            'aptem_mcf_mm' => $validated['aptem_mcf_mm'] ?? $validated['extem_mcf_mm'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'rotem' => $eval]);
        }

        return redirect()->back()->with('success', 'Phân tích đàn nhớt ROTEM hoàn tất: ' . $eval['summary']);
    }

    public function storeHelipad(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'flight_physician_id' => 'required|exists:doctors,id',
            'flight_mission_code' => 'required|string|max:50',
            'transfer_origin_location' => 'required|string|max:150',
            'aircraft_type' => 'required|in:airbus_h225_super_puma,eurocopter_ec155,mi_171_medevac,bell_429',
            'flight_departure_at' => 'nullable|date',
            'estimated_helipad_touchdown_at' => 'nullable|date',
            'transfer_risk_score_trips' => 'required|integer|between:0,60',
            'inflight_ventilator_running' => 'nullable|boolean',
            'inflight_ecmo_running' => 'nullable|boolean',
            'helipad_landing_clearance_granted' => 'nullable|boolean',
            'receiving_department' => 'required|in:coronary_cathlab,trauma_hybrid_or,icu_extracorporeal,stroke_neuro_interv',
        ]);

        $ecmo = (bool)($validated['inflight_ecmo_running'] ?? false);
        $vent = (bool)($validated['inflight_ventilator_running'] ?? true);
        $clearance = (bool)($validated['helipad_landing_clearance_granted'] ?? true);

        $eval = CriticalHelipadTransfer::evaluateHelipadTransferReadiness(
            (int)$validated['transfer_risk_score_trips'],
            $ecmo,
            $vent,
            $clearance
        );

        $record = CriticalHelipadTransfer::create(array_merge($validated, [
            'inflight_ecmo_running' => $ecmo,
            'inflight_ventilator_running' => $vent,
            'helipad_landing_clearance_granted' => $clearance,
            'flight_transfer_logistics' => $eval['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'readiness' => $eval]);
        }

        return redirect()->back()->with('success', 'Điều phối trực thăng cấp cứu Helipad đã lưu: ' . $eval['summary']);
    }
}
