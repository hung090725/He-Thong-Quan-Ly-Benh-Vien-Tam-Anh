<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OncogeneticsNiptRecord;
use App\Models\BiobankStemCellUnit;
use App\Models\BedCapacityCommand;
use App\Models\HospitalAccreditationKpi;
use App\Models\OrphanAntidoteRegistry;
use App\Models\VirtualWardPatient;

class GenomicsHospitalAtHomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_genomics_reports' => OncogeneticsNiptRecord::count(),
            'biobank_units_stored' => BiobankStemCellUnit::where('storage_status', 'stored_cryo')->count(),
            'capacity_commands' => BedCapacityCommand::all(),
            'accreditation_kpis_achieved' => HospitalAccreditationKpi::where('target_achieved', true)->count(),
            'antidote_low_stock_alerts' => OrphanAntidoteRegistry::where('immediate_procurement_alert', true)->count(),
            'virtual_ward_active_patients' => VirtualWardPatient::where('virtual_ward_status', 'monitoring_active')->count(),
            'virtual_ward_red_flags' => VirtualWardPatient::where('red_flag_alert_triggered', true)->count(),
            'recent_genomics' => OncogeneticsNiptRecord::with('patient')->latest()->take(5)->get(),
            'recent_biobank' => BiobankStemCellUnit::with('patient')->latest()->take(5)->get(),
            'recent_antidotes' => OrphanAntidoteRegistry::latest()->take(5)->get(),
            'recent_virtual_ward' => VirtualWardPatient::with('patient')->latest()->take(5)->get(),
        ];

        return view('reception.genomics_hospital_at_home.index', compact('stats'));
    }

    public function storeGenomics(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'test_category' => 'required|in:somatic_cancer_ngs,hereditary_cancer_risk,prenatal_nipt_cfdna',
            'sample_type' => 'required|string|max:50',
            'identified_variants' => 'nullable|array',
            'nipt_fetal_fraction_pct' => 'nullable|numeric|between:0,100',
            'nipt_aneuploidy_risk' => 'nullable|in:low_risk,high_risk_trisomy_21,high_risk_trisomy_18,high_risk_trisomy_13,monosomy_x,na',
            'genetic_counselor_summary' => 'nullable|string|max:1000',
        ]);

        $variants = $validated['identified_variants'] ?? [];
        $targetedTherapies = OncogeneticsNiptRecord::matchTargetedTherapies($variants);

        $fractionOk = null;
        if (isset($validated['nipt_fetal_fraction_pct'])) {
            $fractionOk = OncogeneticsNiptRecord::validateNiptFetalFraction((float)$validated['nipt_fetal_fraction_pct']);
        }

        $record = OncogeneticsNiptRecord::create(array_merge($validated, [
            'fda_approved_targeted_therapies' => implode('; ', $targetedTherapies),
            'nipt_aneuploidy_risk' => $validated['nipt_aneuploidy_risk'] ?? 'na',
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'therapies' => $targetedTherapies, 'fraction_ok' => $fractionOk]);
        }

        return redirect()->back()->with('success', 'Kết quả Di truyền & Sinh học phân tử đã được ghi nhận.');
    }

    public function storeBiobank(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'specimen_code' => 'required|string|max:50|unique:biobank_stem_cell_units,specimen_code',
            'stem_cell_source' => 'required|in:bone_marrow,umbilical_cord_blood,adipose_tissue,peripheral_blood_mobilized',
            'nitrogen_tank_id' => 'nullable|string|max:30',
            'canister_rack_id' => 'nullable|string|max:30',
            'box_slot_coordinate' => 'nullable|string|max:30',
            'total_nucleated_cells_10e8' => 'required|numeric|min:0',
            'cd34_positive_count_10e6' => 'required|numeric|min:0',
            'cell_viability_pct' => 'required|numeric|between:0,100',
            'sterility_clearance_passed' => 'nullable|boolean',
        ]);

        $sterility = (bool)($validated['sterility_clearance_passed'] ?? true);
        $assessment = BiobankStemCellUnit::assessViabilityAndSterility(
            (float)$validated['cell_viability_pct'],
            $sterility
        );

        $record = BiobankStemCellUnit::create(array_merge($validated, [
            'nitrogen_tank_id' => $validated['nitrogen_tank_id'] ?? 'TANK-01',
            'canister_rack_id' => $validated['canister_rack_id'] ?? 'RACK-A',
            'box_slot_coordinate' => $validated['box_slot_coordinate'] ?? 'B02-C05',
            'sterility_clearance_passed' => $sterility,
            'storage_status' => $assessment['status'],
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'assessment' => $assessment]);
        }

        return redirect()->back()->with('success', 'Mẫu tế bào gốc đã được lưu vào Biobank: ' . $assessment['assessment']);
    }

    public function storeBedCommand(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:100',
            'total_licensed_beds' => 'required|integer|min:1',
            'occupied_beds' => 'required|integer|min:0',
            'pending_admissions_er' => 'required|integer|min:0',
            'confirmed_discharges_today' => 'required|integer|min:0',
            'beds_in_sanitization' => 'nullable|integer|min:0',
            'discharge_lounge_active' => 'nullable|boolean',
        ]);

        $metrics = BedCapacityCommand::calculateCapacityMetrics(
            (int)$validated['total_licensed_beds'],
            (int)$validated['occupied_beds'],
            (int)$validated['pending_admissions_er'],
            (int)$validated['confirmed_discharges_today']
        );

        $record = BedCapacityCommand::create(array_merge($validated, [
            'bed_occupancy_rate_pct' => $metrics['occupancy_rate'],
            'predicted_available_beds_afternoon' => $metrics['predicted_empty'],
            'capacity_alert_status' => $metrics['alert_status'],
            'beds_in_sanitization' => $validated['beds_in_sanitization'] ?? 0,
            'discharge_lounge_active' => (bool)($validated['discharge_lounge_active'] ?? true),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'metrics' => $metrics]);
        }

        return redirect()->back()->with('success', 'Dữ liệu điều phối giường đã được cập nhật: ' . $metrics['alert_status']);
    }

    public function storeKpi(Request $request)
    {
        $validated = $request->validate([
            'kpi_code' => 'required|string|max:50|unique:hospital_accreditation_kpis,kpi_code',
            'kpi_title' => 'required|string|max:200',
            'domain_category' => 'required|string|max:100',
            'benchmark_target' => 'required|numeric',
            'actual_value' => 'required|numeric',
            'metric_unit' => 'required|string|max:30',
            'higher_is_better' => 'nullable|boolean',
            'action_improvement_plan' => 'nullable|string|max:1000',
        ]);

        $higherIsBetter = (bool)($validated['higher_is_better'] ?? true);
        $eval = HospitalAccreditationKpi::evaluateKpiCompliance(
            (float)$validated['benchmark_target'],
            (float)$validated['actual_value'],
            $higherIsBetter
        );

        $record = HospitalAccreditationKpi::create([
            'kpi_code' => $validated['kpi_code'],
            'kpi_title' => $validated['kpi_title'],
            'domain_category' => $validated['domain_category'],
            'benchmark_target' => $validated['benchmark_target'],
            'actual_value' => $validated['actual_value'],
            'metric_unit' => $validated['metric_unit'],
            'target_achieved' => $eval['achieved'],
            'compliance_level' => $eval['level'],
            'action_improvement_plan' => $validated['action_improvement_plan'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'evaluation' => $eval]);
        }

        return redirect()->back()->with('success', 'Chỉ số chất lượng bệnh viện (83 Tiêu chí / JCI) đã được đánh giá: Mức ' . $eval['level']);
    }

    public function storeAntidote(Request $request)
    {
        $validated = $request->validate([
            'antidote_name' => 'required|string|max:100',
            'indication_poison' => 'required|string|max:150',
            'stock_vials_available' => 'required|integer|min:0',
            'minimum_safety_threshold' => 'required|integer|min:1',
            'storage_location_cabinet' => 'required|string|max:100',
            'earliest_expiry_date' => 'required|date',
            'first_dose_administration_protocol' => 'nullable|string|max:1000',
        ]);

        $alert = OrphanAntidoteRegistry::checkAntidoteStockStatus(
            (int)$validated['stock_vials_available'],
            (int)$validated['minimum_safety_threshold']
        );

        $protocol = $validated['first_dose_administration_protocol'] ?? OrphanAntidoteRegistry::getFirstDoseProtocol($validated['antidote_name']);

        $record = OrphanAntidoteRegistry::create(array_merge($validated, [
            'immediate_procurement_alert' => $alert,
            'first_dose_administration_protocol' => $protocol,
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'procurement_alert' => $alert]);
        }

        return redirect()->back()->with('success', 'Thông tin thuốc giải độc đã được cập nhật.');
    }

    public function storeVirtualWard(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'doctor_id' => 'required|exists:doctors,id',
            'virtual_bed_identifier' => 'required|string|max:50',
            'admission_diagnosis' => 'required|in:chf_decompensation_post_discharge,copd_mild_exacerbation,cellulitis_iv_antibiotics,covid19_recovery',
            'virtual_admission_date' => 'required|date',
            'current_weight_kg' => 'required|numeric|min:20',
            'weight_change_3days_kg' => 'nullable|numeric',
            'latest_spo2_pct' => 'required|numeric|between:50,100',
            'latest_sbp' => 'required|numeric|between:50,300',
            'latest_dbp' => 'required|numeric|between:30,200',
            'virtual_ward_status' => 'required|in:monitoring_active,escalated_to_hospital_admission,graduated_discharged',
            'care_coordinator_notes' => 'nullable|string|max:1000',
        ]);

        $wChange = isset($validated['weight_change_3days_kg']) ? (float)$validated['weight_change_3days_kg'] : 0.0;
        $decompensation = VirtualWardPatient::detectDecompensationRisk(
            $wChange,
            (float)$validated['latest_spo2_pct'],
            (float)$validated['latest_sbp']
        );

        $record = VirtualWardPatient::create(array_merge($validated, [
            'weight_change_3days_kg' => $wChange,
            'red_flag_alert_triggered' => $decompensation['red_flag'],
            'care_coordinator_notes' => trim(($validated['care_coordinator_notes'] ?? '') . ' | Cảnh báo: ' . $decompensation['summary']),
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $record, 'decompensation' => $decompensation]);
        }

        return redirect()->back()->with('success', 'Bệnh nhân Bệnh viện tại nhà (HAH) đã được ghi nhận: ' . $decompensation['summary']);
    }
}
