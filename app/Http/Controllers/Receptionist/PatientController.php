<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\PatientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of patient profiles with search and filter.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $gender = $request->query('gender');

        $query = PatientProfile::with('user')
            ->withCount(['appointments', 'medicalRecords'])
            ->latest('id');

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_code', 'LIKE', "%{$search}%")
                  ->orWhere('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('identity_card', 'LIKE', "%{$search}%")
                  ->orWhere('insurance_code', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return view('receptionist.patients.index', compact('patients', 'search', 'gender'));
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
        $data['patient_code'] = PatientProfile::generatePatientCode();

        $patient = PatientProfile::create($data);

        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', "Hồ sơ bệnh nhân {$patient->full_name} đã được tạo thành công với mã: {$patient->patient_code}");
    }

    /**
     * Display the specified patient profile with examination history.
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

        return view('receptionist.patients.show', compact('patient'));
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
        $patient->update($request->validated());

        return redirect()
            ->route('reception.patients.show', $patient)
            ->with('success', "Cập nhật thông tin bệnh nhân {$patient->full_name} thành công.");
    }

    /**
     * Remove the specified patient profile from storage safely.
     */
    public function destroy(PatientProfile $patient): RedirectResponse
    {
        if ($patient->appointments()->exists() || $patient->medicalRecords()->exists()) {
            return back()->with('error', "Không thể xóa hồ sơ bệnh nhân {$patient->full_name} ({$patient->patient_code}) vì đã phát sinh lịch sử khám bệnh trong hệ thống.");
        }

        $fullName = $patient->full_name;
        $code = $patient->patient_code;
        $patient->delete();

        return redirect()
            ->route('reception.patients.index')
            ->with('success', "Đã xóa hồ sơ bệnh nhân {$fullName} ({$code}) thành công.");
    }
}
