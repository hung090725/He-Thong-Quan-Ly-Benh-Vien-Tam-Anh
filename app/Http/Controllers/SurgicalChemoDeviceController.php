<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DigitalTwinCssdSterile;
use App\Models\PatientControlledAnalgesia;
use App\Models\GravimetricChemoCompounding;
use App\Models\IntraoperativeNeuromonitoring;
use App\Models\LvadMechanicalCirculatory;

class SurgicalChemoDeviceController extends Controller
{
    public function index()
    {
        $stats = [
            'total_cssd_trays' => DigitalTwinCssdSterile::count(),
            'sterile_ready_trays' => DigitalTwinCssdSterile::where('tray_status', 'sterile_storage')->count(),
            'active_pca_pumps' => PatientControlledAnalgesia::count(),
            'respiratory_lockouts' => PatientControlledAnalgesia::where('respiratory_depression_lockout_active', true)->count(),
            'chemo_preparations' => GravimetricChemoCompounding::count(),
            'gravimetric_passed' => GravimetricChemoCompounding::where('gravimetric_check_passed', true)->count(),
            'ionm_monitored_surgeries' => IntraoperativeNeuromonitoring::count(),
            'ionm_critical_alerts' => IntraoperativeNeuromonitoring::where('neuromonitoring_alert_triggered', true)->count(),
            'monitored_lvad_patients' => LvadMechanicalCirculatory::count(),
            'recent_cssd' => DigitalTwinCssdSterile::latest()->take(5)->get(),
            'recent_pca' => PatientControlledAnalgesia::with('patient')->latest()->take(5)->get(),
            'recent_chemo' => GravimetricChemoCompounding::with('patient')->latest()->take(5)->get(),
            'recent_ionm' => IntraoperativeNeuromonitoring::with('patient')->latest()->take(5)->get(),
            'recent_lvad' => LvadMechanicalCirculatory::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.surgical_chemo_device.index', compact('stats'));
    }

    public function storeCssd(Request $request)
    {
        $validated = $request->validate([
            'tray_barcode_2d' => 'required|string|max:60',
            'tray_name' => 'required|string|max:120',
            'cssd_supervisor_id' => 'required|exists:doctors,id',
            'sterilization_method' => 'required|in:autoclave_steam_134c,hydrogen_peroxide_plasma,ethylene_oxide_eto',
            'sterilizer_chamber_number' => 'required|integer|min:1',
            'chemical_integrator_passed' => 'nullable|boolean',
            'biological_spore_test_passed' => 'nullable|boolean',
            'sterile_expiration_date' => 'nullable|date',
            'tray_status' => 'required|in:decontamination_washing,packaging,in_sterilizer,sterile_storage,dispatched_to_or,used_in_surgery',
            'assigned_operating_room' => 'nullable|string|max:50',
        ]);

        $chem = (bool)($validated['chemical_integrator_passed'] ?? true);
        $bio = (bool)($validated['biological_spore_test_passed'] ?? true);
        $exp = $validated['sterile_expiration_date'] ?? date('Y-m-d', strtotime('+30 days'));

        $verify = DigitalTwinCssdSterile::verifySterileRelease($chem, $bio, $exp);

        $record = DigitalTwinCssdSterile::updateOrCreate(
            ['tray_barcode_2d' => $validated['tray_barcode_2d']],
            array_merge($validated, [
                'chemical_integrator_passed' => $chem,
                'biological_spore_test_passed' => $bio,
                'sterile_expiration_date' => $exp,
                'sterilization_cycle_completed_at' => now(),
            ])
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'verification' => $verify]);
        }

        return redirect()->back()->with('success', 'Bản sao số khay tiệt trùng CSSD đã ghi nhận: ' . $verify['summary']);
    }

    public function storePca(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'anesthesiologist_id' => 'required|exists:doctors,id',
            'opioid_agent' => 'required|string|max:80',
            'basal_infusion_rate_mg_or_mcg_per_h' => 'required|numeric|min:0',
            'pca_bolus_dose_mg_or_mcg' => 'required|numeric|min:0',
            'lockout_interval_minutes' => 'required|integer|min:5',
            'four_hour_dose_limit' => 'required|numeric|min:1',
            'total_demands_count' => 'required|integer|min:0',
            'delivered_bolus_count' => 'required|integer|min:0',
            'etco2_respiratory_monitoring_mmhg' => 'required|numeric|min:10',
            'spo2_monitoring_pct' => 'required|numeric|between:50,100',
            'pain_score_vas_10' => 'required|in:vas_0_2_mild,vas_3_5_moderate,vas_6_8_severe,vas_9_10_worst',
        ]);

        $eval = PatientControlledAnalgesia::evaluatePcaSafety(
            (float)$validated['etco2_respiratory_monitoring_mmhg'],
            (float)$validated['spo2_monitoring_pct'],
            (int)$validated['total_demands_count'],
            (int)$validated['delivered_bolus_count']
        );

        $record = PatientControlledAnalgesia::create(array_merge($validated, [
            'respiratory_depression_lockout_active' => $eval['lock_pump'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'safety' => $eval]);
        }

        return redirect()->back()->with('success', 'Bơm giảm đau tự kiểm soát PCA đã ghi nhận: ' . $eval['summary']);
    }

    public function storeChemo(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'pharmacist_id' => 'required|exists:doctors,id',
            'preparation_barcode' => 'required|string|max:60',
            'chemo_drug_name' => 'required|string|max:100',
            'prescribed_dose_mg' => 'required|numeric|min:1',
            'carrier_diluent' => 'required|string|max:80',
            'expected_total_weight_grams' => 'required|numeric|min:10',
            'measured_actual_weight_grams' => 'required|numeric|min:10',
            'biosafety_cabinet_hepa_certified' => 'nullable|boolean',
        ]);

        $verify = GravimetricChemoCompounding::verifyGravimetricAccuracy(
            (float)$validated['expected_total_weight_grams'],
            (float)$validated['measured_actual_weight_grams']
        );

        $record = GravimetricChemoCompounding::create(array_merge($validated, [
            'weight_deviation_pct' => $verify['deviation_pct'],
            'gravimetric_check_passed' => $verify['passed'],
            'biosafety_cabinet_hepa_certified' => (bool)($validated['biosafety_cabinet_hepa_certified'] ?? true),
            'compounded_at' => now(),
            'beyond_use_date_time' => now()->addHours(24),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'gravimetric' => $verify]);
        }

        return redirect()->back()->with('success', 'Pha chế thuốc hóa trị Gravimetric hoàn tất: ' . $verify['summary']);
    }

    public function storeIonm(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'neurophysiologist_id' => 'required|exists:doctors,id',
            'surgical_type' => 'required|in:scoliosis_spinal_deformity,spinal_cord_tumor,thyroid_recurrent_nerve,brainstem_craniotomy',
            'mep_amplitude_uv' => 'required|numeric|min:1',
            'mep_amplitude_drop_pct' => 'required|numeric|between:0,100',
            'ssep_latency_ms' => 'required|numeric|min:1',
            'ssep_latency_increase_pct' => 'required|numeric|between:0,100',
            'emg_spontaneous_train_activity' => 'nullable|boolean',
            'surgical_corrective_action_taken' => 'nullable|string|max:1000',
            'postop_neurological_deficit' => 'nullable|boolean',
        ]);

        $emg = (bool)($validated['emg_spontaneous_train_activity'] ?? false);
        $eval = IntraoperativeNeuromonitoring::evaluateNeuromonitoringSignal(
            (float)$validated['mep_amplitude_drop_pct'],
            (float)$validated['ssep_latency_increase_pct'],
            $emg
        );

        $record = IntraoperativeNeuromonitoring::create(array_merge($validated, [
            'emg_spontaneous_train_activity' => $emg,
            'neuromonitoring_alert_triggered' => $eval['alert_triggered'],
            'postop_neurological_deficit' => (bool)($validated['postop_neurological_deficit'] ?? false),
            'surgical_corrective_action_taken' => $validated['surgical_corrective_action_taken'] ?? $eval['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'neuromonitoring' => $eval]);
        }

        return redirect()->back()->with('success', 'Giám sát thần kinh trong mổ IONM đã ghi nhận: ' . $eval['summary']);
    }

    public function storeLvad(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'cardiologist_id' => 'required|exists:doctors,id',
            'lvad_serial_number' => 'required|string|max:50',
            'pump_speed_rpm' => 'required|integer|min:3000',
            'pump_flow_l_per_min' => 'required|numeric|min:0.5',
            'pump_power_watts' => 'required|numeric|min:1',
            'pulsatility_index_pi' => 'required|numeric|min:0.1',
            'driveline_infection_alert' => 'nullable|boolean',
            'inr_anticoagulation_target' => 'required|numeric|between:1,5',
        ]);

        $eval = LvadMechanicalCirculatory::analyzeLvadTelemetry(
            (float)$validated['pump_flow_l_per_min'],
            (float)$validated['pump_power_watts'],
            (float)$validated['pulsatility_index_pi']
        );

        $record = LvadMechanicalCirculatory::create(array_merge($validated, [
            'pump_thrombosis_suspected' => $eval['thrombosis_risk'],
            'suction_event_detected' => $eval['suction_event'],
            'driveline_infection_alert' => (bool)($validated['driveline_infection_alert'] ?? false),
            'lvad_diagnostic_summary' => $eval['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'lvad' => $eval]);
        }

        return redirect()->back()->with('success', 'Thông số thiết bị hỗ trợ thất trái LVAD đã lưu: ' . $eval['summary']);
    }
}
