<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DoctorManagementController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Doctor\DoctorConsultationController;
use App\Http\Controllers\Doctor\DoctorQueueController;
use App\Http\Controllers\Patient\PatientPortalController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\DoctorPublicController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Receptionist\PatientController;
use App\Http\Controllers\Receptionist\ReceptionAppointmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Hệ Thống Quản Lý Bệnh Viện Tâm Anh
| Phân chia theo quy tắc Git Flow & Tránh xung đột tại Mục 11.2 Kế hoạch
|--------------------------------------------------------------------------
*/

// Trang chủ công khai (Landing Page)
Route::get('/', [HomeController::class, 'index'])->name('home');

// =========================================================================
// ====== MODULE 1: AUTH & BOOKING (Phụ trách: NGUYỄN THÀNH QUÝ) ===========
// =========================================================================

// 1.1. Xác thực người dùng (Auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// 1.2. Booking Engine (Đặt lịch khám bệnh trực tuyến)
Route::prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/success/{code}', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/lookup', [BookingController::class, 'lookup'])->name('booking.lookup');

    // AJAX API routes for dynamic dropdown and overbooking slot checking
    Route::get('/doctors-by-specialty/{specialtyId}', [BookingController::class, 'getDoctorsBySpecialty'])->name('booking.doctors_by_specialty');
    Route::get('/doctor-slots', [BookingController::class, 'getDoctorSlots'])->name('booking.doctor_slots');
});

// 1.3. Quản lý Tài khoản Người dùng (Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

// 1.4. Patient Portal (Lịch sử khám & Kết quả của Bệnh nhân)
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/history', [PatientPortalController::class, 'history'])->name('history');
    Route::get('/appointments/{appointment}', [PatientPortalController::class, 'showAppointment'])->name('appointments.show');
    Route::put('/appointments/{appointment}/cancel', [PatientPortalController::class, 'cancelAppointment'])->name('appointments.cancel');
});


// =========================================================================
// ====== MODULE 2: SPECIALTIES & DOCTORS (Phụ trách: NGUYỄN VĂN ĐẠT) ======
// =========================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Quản lý Chuyên khoa (CRUD & toggle status)
    Route::patch('/specialties/{specialty}/toggle-status', [SpecialtyController::class, 'toggleStatus'])->name('specialties.toggle-status');
    Route::resource('specialties', SpecialtyController::class);

    // Quản lý Bác sĩ & Lịch trực tuần
    Route::get('/doctors/{doctor}/schedule', [DoctorManagementController::class, 'schedule'])->name('doctors.schedule');
    Route::put('/doctors/{doctor}/schedule', [DoctorManagementController::class, 'updateSchedule'])->name('doctors.schedule.update');
    Route::patch('/doctors/{doctor}/toggle-availability', [DoctorManagementController::class, 'toggleAvailability'])->name('doctors.toggle-availability');
    Route::resource('doctors', DoctorManagementController::class);
});

// Cổng thông tin công chúng: Danh bạ Bác sĩ
Route::prefix('doctors')->name('public.doctors.')->group(function () {
    Route::get('/', [DoctorPublicController::class, 'index'])->name('index');
    Route::get('/{doctor}', [DoctorPublicController::class, 'show'])->name('show');
});


// =========================================================================
// ====== MODULE 3: RECEPTION & PATIENTS (Phụ trách: NGUYỄN VĂN HƯNG) ======
// =========================================================================
Route::middleware(['auth', 'role:receptionist,admin'])->prefix('reception')->name('reception.')->group(function () {
    // 3.1. Bàn tiếp nhận, Check-in & Tiếp nhận vãng lai tại quầy
    Route::get('/appointments', [ReceptionAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/export', [ReceptionAppointmentController::class, 'export'])->name('appointments.export');
    Route::get('/appointments/queue-display', [ReceptionAppointmentController::class, 'queueDisplay'])->name('appointments.queue-display');
    Route::get('/appointments/walk-in', [ReceptionAppointmentController::class, 'createWalkin'])->name('appointments.walkin');
    Route::post('/appointments/walk-in', [ReceptionAppointmentController::class, 'storeWalkin'])->name('appointments.store-walkin');
    Route::get('/appointments/{appointment}/print', [ReceptionAppointmentController::class, 'printTicket'])->name('appointments.print');
    Route::get('/appointments/{appointment}/receipt', [ReceptionAppointmentController::class, 'printReceipt'])->name('appointments.receipt');
    Route::put('/appointments/{appointment}/checkin', [ReceptionAppointmentController::class, 'checkin'])->name('appointments.checkin');
    Route::put('/appointments/{appointment}/cancel', [ReceptionAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::put('/appointments/{appointment}/reassign', [ReceptionAppointmentController::class, 'reassign'])->name('appointments.reassign');

    // 3.2. Quản lý Hồ sơ Bệnh nhân (CRUD)
    Route::get('/patients/{patient}/print-card', [PatientController::class, 'printCard'])->name('patients.print-card');
    Route::resource('patients', PatientController::class);
});


// =========================================================================
// ====== MODULE 4: EXAMINATION & RECORDS (Phụ trách: VÕ LÊ GIA BẢO) =======
// =========================================================================
// Hàng đợi phòng khám của Bác sĩ & Khám bệnh
Route::middleware(['auth', 'role:doctor,admin'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/queue', [DoctorQueueController::class, 'index'])->name('queue');
    Route::get('/examine/{appointment}', [DoctorConsultationController::class, 'examine'])->name('examine');
    Route::post('/examine/{appointment}', [DoctorConsultationController::class, 'store'])->name('examine.store');
    Route::get('/records/{record}', [DoctorConsultationController::class, 'showRecord'])->name('records.show');
    Route::get('/records/{record}/print', [DoctorConsultationController::class, 'printPrescription'])->name('records.print');
});
