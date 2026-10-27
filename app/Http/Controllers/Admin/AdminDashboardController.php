<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Central Dashboard with real database stats.
     */
    public function index(): View
    {
        $today = Carbon::today();

        $stats = [
            'total_users' => User::count(),
            'total_doctors' => Doctor::count(),
            'total_specialties' => Specialty::count(),
            'total_patients' => PatientProfile::count(),
            'appointments_today' => Appointment::whereDate('appointment_date', $today)->count(),
            'appointments_pending' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'appointments_confirmed' => Appointment::where('status', Appointment::STATUS_CONFIRMED)->count(),
            'appointments_completed' => Appointment::where('status', Appointment::STATUS_COMPLETED)->count(),
        ];

        // Financial & Efficiency KPIs
        $todayRevenue = (float) Appointment::whereDate('appointment_date', $today)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_COMPLETED])
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->sum('doctors.consultation_fee');

        $monthRevenue = (float) Appointment::whereYear('appointment_date', $today->year)
            ->whereMonth('appointment_date', $today->month)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_COMPLETED])
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->sum('doctors.consultation_fee');

        $totalToday = $stats['appointments_today'];
        $completionRate = $totalToday > 0 ? (int) round(($stats['appointments_completed'] / $totalToday) * 100) : 0;

        // 7-day appointment trend data for Chart.js
        $trendLabels = [];
        $trendMorning = [];
        $trendAfternoon = [];
        $trendTotal = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $trendLabels[] = $d->format('d/m');
            $mCount = Appointment::whereDate('appointment_date', $d)->where('shift', 'morning')->count();
            $aCount = Appointment::whereDate('appointment_date', $d)->where('shift', 'afternoon')->count();
            $trendMorning[] = $mCount;
            $trendAfternoon[] = $aCount;
            $trendTotal[] = $mCount + $aCount;
        }

        // Specialty distribution data for Doughnut Chart
        $specialtyDistribution = Specialty::withCount('appointments')
            ->orderByDesc('appointments_count')
            ->take(6)
            ->get();
        $specialtyLabels = $specialtyDistribution->pluck('name')->toArray();
        $specialtyCounts = $specialtyDistribution->pluck('appointments_count')->toArray();

        $recentAppointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'todayRevenue',
            'monthRevenue',
            'completionRate',
            'trendLabels',
            'trendMorning',
            'trendAfternoon',
            'trendTotal',
            'specialtyLabels',
            'specialtyCounts',
            'recentAppointments'
        ));
    }
}
