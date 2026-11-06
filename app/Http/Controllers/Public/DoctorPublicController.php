<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorReview;
use App\Models\Specialty;
use App\Services\DoctorVcardService;
use App\Services\SymptomMatcherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorPublicController extends Controller
{
    /**
     * Display a public directory of doctors.
     */
    public function index(Request $request): View
    {
        $query = Doctor::query()
            ->with(['user', 'specialty', 'schedules'])
            ->where('is_available', true)
            ->whereHas('specialty', function ($q) {
                $q->where('is_active', true);
            });

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('qualification', 'LIKE', "%{$search}%")
                  ->orWhere('room_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($specialtyId = $request->input('specialty_id')) {
            $query->where('specialty_id', $specialtyId);
        }

        if ($request->boolean('on_duty_today')) {
            $today = strtolower(now()->format('l'));
            $query->whereHas('schedules', function ($sq) use ($today) {
                $sq->where('day_of_week', $today)->where('is_active', true);
            });
        }

        $doctors = $query->paginate(9)->withQueryString();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        if ($request->wantsJson() || $request->input('format') === 'json') {
            return response()->json([
                'data' => $doctors->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'name' => $doc->full_name_with_title,
                        'specialty' => $doc->specialty ? $doc->specialty->name : '',
                        'room' => $doc->room_number,
                        'avatar' => $doc->avatar_url,
                        'url' => route('public.doctors.show', $doc),
                    ];
                }),
                'total' => $doctors->total(),
            ]);
        }

        if ($request->ajax()) {
            return view('public.doctors.partials.doctor_grid', compact('doctors'));
        }

        return view('public.doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Display the specified doctor profile and weekly schedule.
     */
    public function show(Request $request, Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules' => function ($q) {
            $q->where('is_active', true);
        }, 'reviews' => function ($rq) {
            $rq->where('is_approved', true)->latest();
        }]);

        $lang = $request->query('lang', 'vi');

        $days = ($lang === 'en') ? [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ] : [
            'monday' => 'Thứ Hai',
            'tuesday' => 'Thứ Ba',
            'wednesday' => 'Thứ Tư',
            'thursday' => 'Thứ Năm',
            'friday' => 'Thứ Sáu',
            'saturday' => 'Thứ Bảy',
            'sunday' => 'Chủ Nhật',
        ];

        $qrCodeUrl = DoctorVcardService::getQrCodeUrl($doctor, 200);

        return view('public.doctors.show', compact('doctor', 'days', 'qrCodeUrl', 'lang'));
    }

    /**
     * ADVANCED 6: Generate dynamic doctor QR smart-badge page.
     */
    public function qrCode(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules']);
        $qrCodeUrl = DoctorVcardService::getQrCodeUrl($doctor, 300);

        return view('public.doctors.qr_code', compact('doctor', 'qrCodeUrl'));
    }

    /**
     * ADVANCED 7: Clinic floor wayfinding and navigation guide for patients.
     */
    public function wayfinding(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty']);
        return view('public.doctors.wayfinding', compact('doctor'));
    }

    /**
     * ADVANCED 5: Public patient stores review for doctor.
     */
    public function storeReview(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:100',
            'patient_phone' => 'nullable|string|max:20',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $doctor->reviews()->create($validated);

        // Recalculate doctor rating
        $approvedReviews = $doctor->reviews()->where('is_approved', true);
        $doctor->rating_avg = round($approvedReviews->avg('rating'), 2);
        $doctor->rating_count = $approvedReviews->count();
        $doctor->save();

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Ý kiến của bạn đã được ghi nhận vào hệ thống.');
    }

    /**
     * Download doctor's digital business card in standard .vcf vCard format.
     */
    public function downloadVcard(Doctor $doctor): Response
    {
        $doctor->load(['user', 'specialty']);
        $vcard = DoctorVcardService::generateVcard($doctor);
        $filename = 'bac_si_' . Str::slug($doctor->user->name ?? 'bac_si') . '.vcf';

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Display printable table tent card for examination room desk.
     */
    public function deskCard(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty']);
        $qrCodeUrl = DoctorVcardService::getQrCodeUrl($doctor, 280);

        return view('public.doctors.desk_card', compact('doctor', 'qrCodeUrl'));
    }

    /**
     * Display formal printable medical profile / CV for the doctor.
     */
    public function printProfile(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules' => function ($q) {
            $q->where('is_active', true);
        }]);

        $days = [
            'monday' => 'Thứ Hai',
            'tuesday' => 'Thứ Ba',
            'wednesday' => 'Thứ Tư',
            'thursday' => 'Thứ Năm',
            'friday' => 'Thứ Sáu',
            'saturday' => 'Thứ Bảy',
            'sunday' => 'Chủ Nhật',
        ];

        $qrCodeUrl = DoctorVcardService::getQrCodeUrl($doctor, 180);

        return view('public.doctors.print_profile', compact('doctor', 'days', 'qrCodeUrl'));
    }

    /**
     * Show the smart symptom checker assistant page.
     */
    public function symptomChecker(): View
    {
        $specialties = Specialty::where('is_active', true)->withCount('doctors')->get();
        return view('public.symptom_checker', compact('specialties'));
    }

    /**
     * API endpoint to match symptoms to specialty and doctors.
     */
    public function matchSymptoms(Request $request, SymptomMatcherService $matcher): JsonResponse
    {
        $symptoms = $request->input('symptoms', '');
        $result = $matcher->match($symptoms);

        return response()->json($result);
    }
}
