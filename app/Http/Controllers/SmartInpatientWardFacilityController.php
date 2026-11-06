<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmartInpatientRoomConsole;
use App\Models\DynamicOpdRoomAllocator;
use App\Models\TerminalRoomDisinfection;
use App\Models\SmartDeliverySuiteLdrp;
use App\Models\SmartNurseStationHub;
use App\Models\PatientProfile;

class SmartInpatientWardFacilityController extends Controller
{
    public function index()
    {
        $stats = [
            'total_inpatient_consoles' => SmartInpatientRoomConsole::count(),
            'active_nurse_calls' => SmartInpatientRoomConsole::whereIn('nurse_call_status', ['pending', 'acknowledged'])->count(),
            'total_opd_rooms' => DynamicOpdRoomAllocator::count(),
            'active_consulting_rooms' => DynamicOpdRoomAllocator::where('epaper_display_status', 'active_consulting')->count(),
            'disinfection_records' => TerminalRoomDisinfection::count(),
            'certified_clean_ready' => TerminalRoomDisinfection::where('room_ready_status', 'certified_clean_ready')->count(),
            'ldrp_suites' => SmartDeliverySuiteLdrp::count(),
            'active_in_labor' => SmartDeliverySuiteLdrp::where('status', 'active_in_labor')->count(),
            'nurse_stations' => SmartNurseStationHub::count(),
            'recent_consoles' => SmartInpatientRoomConsole::with('patient')->latest()->take(5)->get(),
            'recent_opd_allocators' => DynamicOpdRoomAllocator::latest()->take(5)->get(),
            'recent_disinfections' => TerminalRoomDisinfection::latest()->take(5)->get(),
            'recent_ldrp_suites' => SmartDeliverySuiteLdrp::with('maternalPatient')->latest()->take(5)->get(),
            'recent_nurse_stations' => SmartNurseStationHub::latest()->take(5)->get(),
            'patients' => PatientProfile::latest()->take(20)->get(),
        ];

        return view('reception.smart_inpatient_ward_facility.index', compact('stats'));
    }

    public function storeInpatientConsole(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'patient_id' => 'nullable|exists:patient_profiles,id',
            'patient_name' => 'required|string|max:255',
            'room_class' => 'required|in:vip_suite,presidential_suite,standard_single,double_isolation',
            'temperature_celsius' => 'required|numeric|between:18,30',
            'circadian_lighting_mode' => 'required|in:morning_energize,daylight_focus,evening_relax,night_sleep',
            'curtain_status' => 'required|in:open,half_closed,fully_closed',
            'nurse_call_type' => 'nullable|in:none,water_assistance,pain_medication,bathroom_help,emergency_urgent',
            'patient_pain_score' => 'nullable|integer|between:0,10',
            'dietary_order' => 'required|string|max:255',
            'iot_device_status' => 'required|in:online,offline,maintenance',
            'notes' => 'nullable|string',
        ]);

        $console = SmartInpatientRoomConsole::create($validated);

        return redirect()->back()->with('success', 'Thiết lập buồng bệnh nội trú thông minh [' . $console->room_number . '] thành công!');
    }

    public function triggerNurseCall(Request $request, $id)
    {
        $console = SmartInpatientRoomConsole::findOrFail($id);
        $validated = $request->validate([
            'nurse_call_type' => 'required|in:water_assistance,pain_medication,bathroom_help,emergency_urgent',
            'patient_pain_score' => 'nullable|integer|between:0,10',
        ]);

        $console->triggerNurseCall($validated['nurse_call_type'], $validated['patient_pain_score'] ?? 0);

        return redirect()->back()->with('success', 'Đã kích hoạt chuông gọi điều dưỡng cho phòng ' . $console->room_number . ' thành công!');
    }

    public function storeOpdAllocator(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:50',
            'department_name' => 'required|string|max:255',
            'assigned_doctor_name' => 'required|string|max:255',
            'doctor_specialty' => 'required|string|max:255',
            'shift_session' => 'required|in:morning_shift,afternoon_shift,night_shift',
            'epaper_display_status' => 'required|in:active_consulting,paused_break,in_emergency_meeting,closed',
            'current_queue_number' => 'required|integer|min:0',
            'next_queue_number' => 'required|integer|min:0',
            'waiting_patient_count' => 'required|integer|min:0',
            'average_consult_time_min' => 'required|integer|min:5',
        ]);

        $allocator = DynamicOpdRoomAllocator::create($validated);

        return redirect()->back()->with('success', 'Phân bổ phòng khám ngoại trú & đồng bộ E-Paper [' . $allocator->room_code . '] thành công!');
    }

    public function advanceOpdQueue($id)
    {
        $allocator = DynamicOpdRoomAllocator::findOrFail($id);
        $allocator->advanceQueue();

        return redirect()->back()->with('success', 'Đã gọi bệnh nhân số ' . $allocator->current_queue_number . ' vào phòng khám ' . $allocator->room_code . '!');
    }

    public function storeTerminalDisinfection(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'ward_department' => 'required|string|max:255',
            'discharge_patient_name' => 'required|string|max:255',
            'cleaning_trigger_event' => 'required|in:patient_discharge,post_operative,isolation_contact_precaution,scheduled_deep_clean',
            'disinfection_method' => 'required|in:uvc_robot_autonomous,hydrogen_peroxide_vhp,manual_quaternary_wipe,hybrid_dual_cycle',
            'disinfection_robot_id' => 'nullable|string|max:50',
            'run_duration_minutes' => 'required|integer|min:10',
            'cleaning_staff_name' => 'required|string|max:255',
            'atp_bioluminescence_rlu' => 'required|integer|min:0',
        ]);

        $disinfection = TerminalRoomDisinfection::create($validated);
        $disinfection->certifyClean();

        return redirect()->back()->with('success', 'Ghi nhận chu trình khử trùng phòng [' . $disinfection->room_number . '] hoàn tất!');
    }

    public function storeDeliverySuiteLdrp(Request $request)
    {
        $validated = $request->validate([
            'suite_code' => 'required|string|max:50',
            'maternal_patient_id' => 'required|exists:patient_profiles,id',
            'mother_name' => 'required|string|max:255',
            'gestational_weeks' => 'required|numeric|between:20,44',
            'current_stage' => 'required|in:stage1_labor,stage2_delivery,stage3_placenta,stage4_recovery_postpartum',
            'water_birth_tub_active' => 'nullable|boolean',
            'infant_resuscitaire_warmed' => 'nullable|boolean',
            'partner_companion_tag_id' => 'nullable|string|max:50',
            'pain_management_type' => 'required|in:epidural_analgesia,water_immersion,nitrous_oxide_gas,breathing_relaxation',
            'fetal_heart_rate_bpm' => 'required|integer|between:60,220',
            'maternal_comfort_score' => 'required|integer|between:0,10',
        ]);

        $validated['bed_transformation_mode'] = $request->input('bed_transformation_mode', 'comfort_relax_bed');
        $suite = SmartDeliverySuiteLdrp::create($validated);
        $suite->advanceStage($validated['current_stage']);

        return redirect()->back()->with('success', 'Thiết lập buồng sinh gia đình 4-trong-1 LDRP [' . $suite->suite_code . '] thành công!');
    }

    public function storeNurseStationHub(Request $request)
    {
        $validated = $request->validate([
            'ward_code' => 'required|string|max:50',
            'ward_name' => 'required|string|max:255',
            'total_active_beds' => 'required|integer|min:1',
            'occupied_beds' => 'required|integer|min:0',
            'critical_alert_count' => 'required|integer|min:0',
            'pending_nurse_calls' => 'required|integer|min:0',
            'infusion_near_empty_count' => 'required|integer|min:0',
            'on_duty_head_nurse' => 'required|string|max:255',
            'charge_nurse_list' => 'nullable|string',
            'code_blue_standby_status' => 'required|in:green_ready,yellow_standby,red_active_resuscitation',
        ]);

        $hub = SmartNurseStationHub::create($validated);

        return redirect()->back()->with('success', 'Cập nhật bảng chỉ huy Trạm Điều Dưỡng [' . $hub->ward_name . '] thành công!');
    }
}
