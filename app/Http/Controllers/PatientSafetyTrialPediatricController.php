<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfantSecurityRtls;
use App\Models\BradenSmartBedPressure;
use App\Models\DonorHumanMilkBank;
use App\Models\NuclearMedicineRadiation;
use App\Models\ClinicalTrialManagement;

class PatientSafetyTrialPediatricController extends Controller
{
    public function index()
    {
        $stats = [
            'monitored_infants_rtls' => InfantSecurityRtls::count(),
            'rtls_geofence_alerts' => InfantSecurityRtls::where('geofence_perimeter_breach_alarm', true)->count(),
            'braden_smart_beds' => BradenSmartBedPressure::count(),
            'high_risk_decubitus_beds' => BradenSmartBedPressure::whereIn('decubitus_ulcer_risk_level', ['high_risk', 'very_high_risk'])->count(),
            'dhmb_milk_batches' => DonorHumanMilkBank::count(),
            'dhmb_approved_batches' => DonorHumanMilkBank::where('milk_batch_status', 'pasteurized_approved')->count(),
            'nuclear_treatments' => NuclearMedicineRadiation::count(),
            'safe_for_discharge_nuclear' => NuclearMedicineRadiation::where('safe_for_discharge_home', true)->count(),
            'active_clinical_trials' => ClinicalTrialManagement::count(),
            'sae_adverse_events' => ClinicalTrialManagement::where('serious_adverse_event_sae_occurred', true)->count(),
            'recent_rtls' => InfantSecurityRtls::with(['infant', 'mother'])->latest()->take(5)->get(),
            'recent_braden' => BradenSmartBedPressure::with('patient')->latest()->take(5)->get(),
            'recent_milk_bank' => DonorHumanMilkBank::latest()->take(5)->get(),
            'recent_nuclear' => NuclearMedicineRadiation::with('patient')->latest()->take(5)->get(),
            'recent_trials' => ClinicalTrialManagement::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.patient_safety_pediatric.index', compact('stats'));
    }

    public function storeRtls(Request $request)
    {
        $validated = $request->validate([
            'infant_patient_id' => 'required|exists:patient_profiles,id',
            'mother_patient_id' => 'required|exists:patient_profiles,id',
            'infant_rtls_tag_id' => 'required|string|max:50',
            'mother_rtls_tag_id' => 'required|string|max:50',
            'skin_tamper_sensor_intact' => 'nullable|boolean',
            'mother_infant_dual_pairing_verified' => 'nullable|boolean',
            'current_floor_room' => 'required|string|max:100',
            'distance_to_nearest_exit_meters' => 'required|numeric|min:0',
        ]);

        $tamper = (bool)($validated['skin_tamper_sensor_intact'] ?? true);
        $paired = (bool)($validated['mother_infant_dual_pairing_verified'] ?? true);

        $eval = InfantSecurityRtls::evaluateInfantSecurityStatus(
            $tamper,
            $paired,
            (float)$validated['distance_to_nearest_exit_meters']
        );

        $record = InfantSecurityRtls::updateOrCreate(
            ['infant_rtls_tag_id' => $validated['infant_rtls_tag_id']],
            array_merge($validated, [
                'skin_tamper_sensor_intact' => $tamper,
                'mother_infant_dual_pairing_verified' => $paired,
                'geofence_perimeter_breach_alarm' => $eval['geofence_breach'],
                'elevator_emergency_lockdown_triggered' => $eval['lockdown_elevator'],
                'infant_safety_status' => $eval['status'],
            ])
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'security' => $eval]);
        }

        return redirect()->back()->with('success', 'Định vị an ninh sơ sinh RTLS đã cập nhật: ' . $eval['summary']);
    }

    public function storeBraden(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'head_nurse_id' => 'required|exists:doctors,id',
            'braden_sensory_perception' => 'required|integer|between:1,4',
            'braden_moisture' => 'required|integer|between:1,4',
            'braden_activity' => 'required|integer|between:1,4',
            'braden_mobility' => 'required|integer|between:1,4',
            'braden_nutrition' => 'required|integer|between:1,4',
            'braden_friction_shear' => 'required|integer|between:1,3',
            'sacrum_peak_pressure_mmhg' => 'required|numeric|min:0',
            'heels_peak_pressure_mmhg' => 'required|numeric|min:0',
            'bed_head_elevation_degrees' => 'required|numeric|between:0,90',
            'last_repositioning_elapsed_minutes' => 'required|integer|min:0',
        ]);

        $scores = [
            (int)$validated['braden_sensory_perception'],
            (int)$validated['braden_moisture'],
            (int)$validated['braden_activity'],
            (int)$validated['braden_mobility'],
            (int)$validated['braden_nutrition'],
            (int)$validated['braden_friction_shear'],
        ];

        $eval = BradenSmartBedPressure::calculateBradenRisk(
            $scores,
            (int)$validated['last_repositioning_elapsed_minutes'],
            (float)$validated['bed_head_elevation_degrees']
        );

        $record = BradenSmartBedPressure::create(array_merge($validated, [
            'total_braden_score' => $eval['total_score'],
            'decubitus_ulcer_risk_level' => $eval['risk_level'],
            'turn_reminder_due_alert' => $eval['turn_due'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'braden' => $eval]);
        }

        return redirect()->back()->with('success', 'Đánh giá nguy cơ loét tì đè & đệm thông minh đã lưu: ' . $eval['summary']);
    }

    public function storeMilkBank(Request $request)
    {
        $validated = $request->validate([
            'milk_batch_code' => 'required|string|max:60',
            'anonymous_donor_code' => 'required|string|max:50',
            'dhmb_technician_id' => 'required|exists:doctors,id',
            'batch_volume_ml' => 'required|numeric|min:10',
            'pre_pasteurization_culture_passed' => 'nullable|boolean',
            'post_pasteurization_culture_sterile' => 'nullable|boolean',
            'energy_kcal_per_100ml' => 'required|numeric|min:30',
            'protein_grams_per_100ml' => 'required|numeric|min:0.5',
            'fat_grams_per_100ml' => 'required|numeric|min:0.5',
            'expiration_frozen_date' => 'nullable|date',
            'assigned_recipient_infant_id' => 'nullable|exists:patient_profiles,id',
            'milk_batch_status' => 'required|in:quarantine_testing,pasteurized_approved,dispensed_to_nicu,discarded',
        ]);

        $pre = (bool)($validated['pre_pasteurization_culture_passed'] ?? true);
        $post = (bool)($validated['post_pasteurization_culture_sterile'] ?? true);

        $eval = DonorHumanMilkBank::assessMilkBatchSafety(
            $pre,
            $post,
            (float)$validated['energy_kcal_per_100ml']
        );

        $record = DonorHumanMilkBank::updateOrCreate(
            ['milk_batch_code' => $validated['milk_batch_code']],
            array_merge($validated, [
                'pre_pasteurization_culture_passed' => $pre,
                'post_pasteurization_culture_sterile' => $post,
                'pasteurization_holder_completed_at' => now(),
                'expiration_frozen_date' => $validated['expiration_frozen_date'] ?? date('Y-m-d', strtotime('+90 days')),
            ])
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'safety' => $eval]);
        }

        return redirect()->back()->with('success', 'Hồ sơ ngân hàng sữa mẹ tiệt trùng DHMB đã lưu: ' . $eval['summary']);
    }

    public function storeNuclear(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'radiation_safety_officer_id' => 'required|exists:doctors,id',
            'radiopharmaceutical' => 'required|in:f18_fludeoxyglucose,tc99m_sestamibi,i131_sodium_iodide,ga68_dotatate,lu177_psma',
            'injected_activity_mbq' => 'required|numeric|min:1',
            'isotope_half_life_minutes' => 'required|numeric|min:1',
            'elapsed_minutes' => 'nullable|numeric|min:0',
            'staff_personal_dosimeter_rate_usv_h' => 'required|numeric|min:0',
            'patient_surface_radiation_rate_usv_h' => 'required|numeric|min:0',
        ]);

        $elapsed = (float)($request->input('elapsed_minutes', 120.0));

        $calc = NuclearMedicineRadiation::calculateRadiationDecayAndDischarge(
            (float)$validated['injected_activity_mbq'],
            (float)$validated['isotope_half_life_minutes'],
            $elapsed,
            (float)$validated['patient_surface_radiation_rate_usv_h']
        );

        $record = NuclearMedicineRadiation::create(array_merge($validated, [
            'decayed_residual_activity_mbq' => $calc['residual_activity_mbq'],
            'safe_for_discharge_home' => $calc['safe_for_discharge'],
            'injection_timestamp' => now()->subMinutes((int)$elapsed),
            'radioprotection_instructions' => $calc['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'nuclear' => $calc]);
        }

        return redirect()->back()->with('success', 'Giám sát an toàn bức xạ hạt nhân đã ghi nhận: ' . $calc['summary']);
    }

    public function storeTrial(Request $request)
    {
        $validated = $request->validate([
            'protocol_code' => 'required|string|max:60',
            'patient_id' => 'required|exists:patient_profiles,id',
            'principal_investigator_id' => 'required|exists:doctors,id',
            'trial_title' => 'required|string|max:200',
            'study_phase' => 'required|in:phase_1,phase_2,phase_3,phase_4_post_market',
            'randomization_arm' => 'required|in:investigational_drug_arm,active_comparator_arm,placebo_blinded_arm',
            'electronic_informed_consent_signed' => 'nullable|boolean',
            'study_visit_number' => 'required|integer|min:1',
            'protocol_adherence_rate_pct' => 'required|numeric|between:0,100',
            'serious_adverse_event_sae_occurred' => 'nullable|boolean',
            'sae_reported_to_irb_within_24h_at' => 'nullable|date',
            'subject_status' => 'required|in:enrolled_active,completed_protocol,withdrawn_consent,discontinued_adverse_event',
        ]);

        $consent = (bool)($validated['electronic_informed_consent_signed'] ?? true);
        $sae = (bool)($validated['serious_adverse_event_sae_occurred'] ?? false);
        $saeTime = $validated['sae_reported_to_irb_within_24h_at'] ?? null;

        $audit = ClinicalTrialManagement::auditTrialCompliance(
            $consent,
            (float)$validated['protocol_adherence_rate_pct'],
            $sae,
            $saeTime
        );

        $record = ClinicalTrialManagement::create(array_merge($validated, [
            'electronic_informed_consent_signed' => $consent,
            'serious_adverse_event_sae_occurred' => $sae,
            'gcp_compliance_audit_notes' => $audit['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'audit' => $audit]);
        }

        return redirect()->back()->with('success', 'Hồ sơ thử nghiệm lâm sàng GCP đã lưu: ' . $audit['summary']);
    }
}
