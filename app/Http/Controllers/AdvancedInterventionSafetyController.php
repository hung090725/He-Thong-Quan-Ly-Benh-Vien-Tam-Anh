<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdvancedEndoscopyProcedure;
use App\Models\CiedRemoteMonitoring;
use App\Models\AntibiogramHaiSurveillance;
use App\Models\RoboticExoskeletonRehab;

class AdvancedInterventionSafetyController extends Controller
{
    public function index()
    {
        $stats = [
            'total_esd_poem_cases' => AdvancedEndoscopyProcedure::count(),
            'curative_r0_resections' => AdvancedEndoscopyProcedure::where('r0_clear_margins_confirmed', true)->count(),
            'monitored_cied_devices' => CiedRemoteMonitoring::count(),
            'electrical_storm_alerts' => CiedRemoteMonitoring::where('electrical_storm_alert', true)->count(),
            'antibiogram_records' => AntibiogramHaiSurveillance::count(),
            'outbreak_cluster_alerts' => AntibiogramHaiSurveillance::where('outbreak_cluster_alert', true)->count(),
            'robotic_exoskeleton_sessions' => RoboticExoskeletonRehab::count(),
            'recent_endoscopy' => AdvancedEndoscopyProcedure::with('patient')->latest()->take(5)->get(),
            'recent_cied' => CiedRemoteMonitoring::with('patient')->latest()->take(5)->get(),
            'recent_antibiogram' => AntibiogramHaiSurveillance::latest()->take(5)->get(),
            'recent_exoskeleton' => RoboticExoskeletonRehab::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.advanced_intervention.index', compact('stats'));
    }

    public function storeEndoscopy(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'endoscopist_id' => 'required|exists:doctors,id',
            'procedure_category' => 'required|in:esd_endoscopic_submucosal_dissection,poem_peroral_endoscopic_myotomy,ercp_complex_biliary,eus_guided_drainage',
            'lesion_organ_location' => 'required|string|max:100',
            'lesion_size_mm' => 'required|numeric|min:1',
            'en_bloc_resection_achieved' => 'nullable|boolean',
            'r0_clear_margins_confirmed' => 'nullable|boolean',
            'asge_difficulty_grade' => 'required|in:grade_1_standard,grade_2_advanced,grade_3_expert,grade_4_highly_complex',
            'prophylactic_pancreatic_stent_placed' => 'nullable|boolean',
            'rectal_indomethacin_administered' => 'nullable|boolean',
            'perforation_or_bleeding_occurred' => 'nullable|boolean',
        ]);

        $enBloc = (bool)($validated['en_bloc_resection_achieved'] ?? true);
        $r0 = (bool)($validated['r0_clear_margins_confirmed'] ?? true);
        $stent = (bool)($validated['prophylactic_pancreatic_stent_placed'] ?? false);
        $indo = (bool)($validated['rectal_indomethacin_administered'] ?? true);

        $quality = AdvancedEndoscopyProcedure::assessEndoscopicQuality(
            $validated['procedure_category'],
            $enBloc,
            $r0,
            $indo,
            $stent
        );

        $record = AdvancedEndoscopyProcedure::create(array_merge($validated, [
            'en_bloc_resection_achieved' => $enBloc,
            'r0_clear_margins_confirmed' => $r0,
            'prophylactic_pancreatic_stent_placed' => $stent,
            'rectal_indomethacin_administered' => $indo,
            'perforation_or_bleeding_occurred' => (bool)($validated['perforation_or_bleeding_occurred'] ?? false),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'quality' => $quality]);
        }

        return redirect()->back()->with('success', 'Hồ sơ Nội soi Can thiệp Nâng cao đã ghi nhận: ' . $quality['summary']);
    }

    public function storeCied(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'cardiologist_id' => 'required|exists:doctors,id',
            'device_type' => 'required|in:pacemaker_single_dual,icd_implantable_defibrillator,crt_d_biventricular,subcutaneous_icd',
            'device_serial_number' => 'required|string|max:50',
            'battery_longevity_remaining_years' => 'required|numeric|min:0.1',
            'lead_impedance_ohms' => 'required|numeric|min:10',
            'ventricular_tachycardia_episodes_30d' => 'nullable|integer|min:0',
            'shocks_delivered_appropriate' => 'nullable|integer|min:0',
            'shocks_delivered_inappropriate' => 'nullable|integer|min:0',
        ]);

        $vt = (int)($validated['ventricular_tachycardia_episodes_30d'] ?? 0);
        $inapp = (int)($validated['shocks_delivered_inappropriate'] ?? 0);

        $analysis = CiedRemoteMonitoring::analyzeCiedTransmission(
            $vt,
            $inapp,
            (float)$validated['lead_impedance_ohms'],
            (float)$validated['battery_longevity_remaining_years']
        );

        $record = CiedRemoteMonitoring::create(array_merge($validated, [
            'ventricular_tachycardia_episodes_30d' => $vt,
            'shocks_delivered_appropriate' => $validated['shocks_delivered_appropriate'] ?? 0,
            'shocks_delivered_inappropriate' => $inapp,
            'electrical_storm_alert' => $analysis['electrical_storm'],
            'lead_fracture_risk' => $analysis['lead_fracture'],
            'remote_transmission_summary' => $analysis['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'analysis' => $analysis]);
        }

        return redirect()->back()->with('success', 'Dữ liệu theo dõi thiết bị tim mạch cấy ghép CIED từ xa đã ghi nhận: ' . $analysis['summary']);
    }

    public function storeAntibiogram(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:100',
            'pathogen_name' => 'required|string|max:150',
            'antibiotic_tested' => 'required|string|max:100',
            'total_isolates_tested' => 'required|integer|min:1',
            'susceptibility_pct' => 'required|numeric|between:0,100',
            'resistance_classification' => 'required|in:pan_susceptible,mdr_multidrug_resistant,xdr_extensively_drug_resistant,pdr_pandrug_resistant',
            'hai_infection_site' => 'required|string|max:50',
        ]);

        $risk = AntibiogramHaiSurveillance::evaluateResistanceRisk(
            $validated['resistance_classification'],
            (float)$validated['susceptibility_pct'],
            (int)$validated['total_isolates_tested']
        );

        $record = AntibiogramHaiSurveillance::create(array_merge($validated, [
            'outbreak_cluster_alert' => $risk['outbreak_alert'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'risk' => $risk]);
        }

        return redirect()->back()->with('success', 'Bản đồ nhạy cảm kháng sinh Antibiogram đã lưu: ' . $risk['summary']);
    }

    public function storeExoskeleton(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'therapist_id' => 'required|exists:doctors,id',
            'exoskeleton_device_model' => 'required|string|max:100',
            'session_duration_minutes' => 'required|integer|min:10',
            'total_steps_achieved' => 'required|integer|min:0',
            'distance_meters' => 'required|numeric|min:0',
            'assistance_level_pct' => 'required|integer|between:0,100',
            'hip_rom_degrees' => 'nullable|numeric',
            'knee_rom_degrees' => 'nullable|numeric',
            'gait_symmetry_index_pct' => 'required|numeric|between:0,100',
        ]);

        $evalGait = RoboticExoskeletonRehab::evaluateGaitQuality(
            (float)$validated['gait_symmetry_index_pct'],
            (int)$validated['assistance_level_pct'],
            (int)$validated['total_steps_achieved']
        );

        $record = RoboticExoskeletonRehab::create(array_merge($validated, [
            'hip_rom_degrees' => $validated['hip_rom_degrees'] ?? 30.0,
            'knee_rom_degrees' => $validated['knee_rom_degrees'] ?? 50.0,
            'therapist_clinical_notes' => $evalGait['summary'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'gait' => $evalGait]);
        }

        return redirect()->back()->with('success', 'Phiên phục hồi chức năng Robot khung xương ngoài đã ghi nhận: ' . $evalGait['summary']);
    }
}
