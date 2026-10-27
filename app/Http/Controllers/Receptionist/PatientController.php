<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of patient profiles with advanced search and filters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $gender = $request->query('gender');
        $insurance = $request->query('insurance');
        $sort = $request->query('sort', 'latest');

        $query = PatientProfile::with('user')
            ->withCount(['appointments', 'medicalRecords']);

        // Gender filter
        if ($gender) {
            $query->where('gender', $gender);
        }

        // Insurance filter
        if ($insurance === 'yes') {
            $query->whereNotNull('insurance_code')->where('insurance_code', '!=', '');
        } elseif ($insurance === 'no') {
            $query->where(function ($q) {
                $q->whereNull('insurance_code')->orWhere('insurance_code', '');
            });
        }

        // Full-text search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_code', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('identity_card', 'LIKE', "%{$search}%")
                  ->orWhere('insurance_code', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        match ($sort) {
            'name_asc' => $query->orderBy('full_name', 'asc'),
            'name_desc' => $query->orderBy('full_name', 'desc'),
            'dob_asc' => $query->orderBy('dob', 'asc'),
            'dob_desc' => $query->orderBy('dob', 'desc'),
            'visits_desc' => $query->orderBy('appointments_count', 'desc'),
            default => $query->latest('id'),
        };

        $patients = $query->paginate(15)->withQueryString();

        // Summary statistics
        $stats = [
            'total' => PatientProfile::count(),
            'male' => PatientProfile::where('gender', 'male')->count(),
            'female' => PatientProfile::where('gender', 'female')->count(),
            'insured' => PatientProfile::whereNotNull('insurance_code')->where('insurance_code', '!=', '')->count(),
            'new_this_month' => PatientProfile::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];

        return view('receptionist.patients.index', compact('patients', 'search', 'gender', 'insurance', 'sort', 'stats'));
    }

    /**
     * Show the form for creating a new patient profile.
     */
    public function create(): View
    {
        return view('receptionist.patients.create');
    }

    /**
     * Store a newly created patient profile in storage.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Check potential duplicate phone number or identity card for awareness
        if (!empty($data['identity_card'])) {
            $existingCccd = PatientProfile::where('identity_card', $data['identity_card'])->first();
            if ($existingCccd) {
                return back()->withInput()->with('error', "Số CCCD/CMND {$data['identity_card']} đã tồn tại trong hệ thống (thuộc bệnh nhân {$existingCccd->full_name} - Mã: {$existingCccd->patient_code}). Vui lòng kiểm tra lại hồ sơ.");
            }
        }

        $data['patient_code'] = PatientProfile::generatePatientCode();

        $patient = PatientProfile::create($data);

        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', "Hồ sơ bệnh nhân {$patient->full_name} đã được tạo thành công với mã định danh: {$patient->patient_code}");
    }

    /**
     * Display the specified patient profile with comprehensive medical history.
     */
    public function show(PatientProfile $patient): View
    {
        $patient->load([
            'user',
            'appointments' => function ($q) {
                $q->with(['doctor.user', 'specialty'])->latest('appointment_date');
            },
            'medicalRecords' => function ($q) {
                $q->with(['doctor.user', 'prescriptions', 'attachments'])->latest('created_at');
            },
        ]);

        $completedVisits = $patient->appointments->where('status', 'completed')->count();
        $latestAppointment = $patient->appointments->first();

        return view('receptionist.patients.show', compact('patient', 'completedVisits', 'latestAppointment'));
    }

    /**
     * Show the form for editing the specified patient profile.
     */
    public function edit(PatientProfile $patient): View
    {
        return view('receptionist.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient profile in storage.
     */
    public function update(UpdatePatientRequest $request, PatientProfile $patient): RedirectResponse
    {
        $data = $request->validated();

        // Check if CCCD belongs to another patient
        if (!empty($data['identity_card'])) {
            $duplicateCccd = PatientProfile::where('identity_card', $data['identity_card'])
                ->where('id', '!=', $patient->id)
                ->first();
            if ($duplicateCccd) {
                return back()->withInput()->with('error', "Số CCCD/CMND {$data['identity_card']} đã thuộc về bệnh nhân {$duplicateCccd->full_name} ({$duplicateCccd->patient_code}). Không thể cập nhật trùng.");
            }
        }

        $patient->update($data);

        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', "Cập nhật thông tin bệnh nhân {$patient->full_name} thành công.");
    }

    /**
     * Remove the specified patient profile from storage with relational safety.
     */
    public function destroy(PatientProfile $patient): RedirectResponse
    {
        // Strict database integrity constraint: Cannot delete if any clinical or appointment records exist
        if ($patient->appointments()->exists() || $patient->medicalRecords()->exists()) {
            return back()->with('error', "Ràng buộc toàn vẹn dữ liệu: Không thể xóa hồ sơ bệnh nhân {$patient->full_name} ({$patient->patient_code}) vì đã phát sinh lịch sử khám bệnh hoặc bệnh án trong hệ thống y tế.");
        }

        DB::transaction(function () use ($patient) {
            $patient->delete();
        });

        return redirect()
            ->route('reception.patients.index')
            ->with('success', "Đã xóa hồ sơ bệnh nhân {$patient->full_name} ({$patient->patient_code}) thành công.");
    }

    /**
     * Print patient barcode identity card.
     */
    public function printCard(PatientProfile $patient): View
    {
        return view('receptionist.patients.print_card', compact('patient'));
    }
}
