<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DoctorManagementController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Doctor\ClinicalConsultationController;
use App\Http\Controllers\Doctor\DoctorConsultationController;
use App\Http\Controllers\Doctor\DoctorQueueController;
use App\Http\Controllers\Patient\PatientPortalController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\DeliveryTrackingController;
use App\Http\Controllers\Public\DoctorPublicController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KioskController;
use App\Http\Controllers\Public\PatientJourneyController;
use App\Http\Controllers\Public\PatientSurveyController;
use App\Http\Controllers\Receptionist\BloodTransfusionController;
use App\Http\Controllers\Receptionist\ClinicalNutritionController;
use App\Http\Controllers\Receptionist\CodeRedEmergencyController;
use App\Http\Controllers\Receptionist\DayBedController;
use App\Http\Controllers\Receptionist\InformedConsentController;
use App\Http\Controllers\Receptionist\LabOrderController;
use App\Http\Controllers\Receptionist\MedicationDeliveryController;
use App\Http\Controllers\Receptionist\MedicationScheduleController;
use App\Http\Controllers\Receptionist\MobilityEquipmentController;
use App\Http\Controllers\Receptionist\PatientController;
use App\Http\Controllers\Receptionist\PatientInvoiceController;
use App\Http\Controllers\Receptionist\PatientVitalController;
use App\Http\Controllers\Receptionist\PharmacyController;
use App\Http\Controllers\Receptionist\PharmacyInventoryController;
use App\Http\Controllers\Receptionist\ReceptionAnalyticsController;
use App\Http\Controllers\Receptionist\ReceptionAppointmentController;
use App\Http\Controllers\Receptionist\ReceptionNotificationController;
use App\Http\Controllers\Receptionist\ReceptionSurveyController;
use App\Http\Controllers\Receptionist\SickLeaveCertificateController;
use App\Http\Controllers\Receptionist\VaccinationController;
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

// 1.3. Smart Kiosk Tự Phục Vụ Tại Cửa Sảnh (Self-Service Check-in & Queue Ticket)
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/checkin', [KioskController::class, 'checkinAppointment'])->name('checkin');
    Route::post('/lookup', [KioskController::class, 'lookupPatient'])->name('lookup');
    Route::post('/register-returning', [KioskController::class, 'registerReturningPatient'])->name('register-returning');
    Route::post('/register-new', [KioskController::class, 'registerNewPatient'])->name('register-new');
    Route::get('/ticket/{appointment}', [KioskController::class, 'printTicket'])->name('ticket');
});

// Cổng Tra Cứu Viện Phí Điện Tử & Thanh Toán VietQR Tự Động (Public Smart Billing Lookup)
Route::get('/billing/lookup', [PatientInvoiceController::class, 'publicLookup'])->name('public.billing.lookup');

// Cổng Theo Dõi Hành Trình Khám Trực Tuyến Thời Gian Thực (Smart Patient Journey Live Tracker)
Route::get('/journey/{code}', [PatientJourneyController::class, 'show'])->name('public.journey.show');

// Cổng Tra Cứu Giao Thuốc Tận Nhà Trực Tuyến (Public Medication Delivery Tracking)
Route::get('/delivery/{tracking_code}', [DeliveryTrackingController::class, 'show'])->name('public.delivery.tracking');

// Khảo Sát Đánh Giá Trải Nghiệm & Hài Lòng Người Bệnh Chuẩn Bộ Y Tế (MOH 83-Criteria CSAT & NPS)
Route::get('/survey/{appointment_code}', [PatientSurveyController::class, 'create'])->name('public.survey.create');
Route::post('/survey', [PatientSurveyController::class, 'store'])->name('public.survey.store');
Route::get('/survey-thankyou', [PatientSurveyController::class, 'thankyou'])->name('public.survey.thankyou');

// 1.3. Quản lý Tài khoản Người dùng (Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

// 1.4. Patient Portal (Lịch sử khám & Kết quả của Bệnh nhân)
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/history', [PatientPortalController::class, 'history'])->name('history');
    Route::get('/appointments/{appointment}', [PatientPortalController::class, 'showAppointment'])->name('appointments.show');
    Route::get('/appointments/{appointment}/prescription/print', [PatientPortalController::class, 'printPrescription'])->name('appointments.prescription.print');
    Route::get('/certificates/{certificate}/print', [SickLeaveCertificateController::class, 'print'])->name('certificates.print');
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
    Route::post('/appointments/{appointment}/call-audio', [ReceptionAppointmentController::class, 'callAudio'])->name('appointments.call-audio');

    // 3.2. Quản lý Hồ sơ Bệnh nhân (CRUD)
    Route::get('/patients/{patient}/print-card', [PatientController::class, 'printCard'])->name('patients.print-card');
    Route::get('/patients/{patient}/print-wristband', [PatientController::class, 'printWristband'])->name('patients.print-wristband');
    Route::post('/patients/{patient}/vitals', [PatientVitalController::class, 'store'])->name('patients.vitals.store');
    Route::delete('/vitals/{vital}', [PatientVitalController::class, 'destroy'])->name('vitals.destroy');
    Route::resource('patients', PatientController::class);

    // 3.3. Quầy Dược Ngoại Trú & Cấp Phát Thuốc (Outpatient Pharmacy Dispensing)
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
    Route::post('/pharmacy/{record}/dispense', [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
    Route::post('/pharmacy/{record}/undo', [PharmacyController::class, 'undoDispense'])->name('pharmacy.undo');
    Route::post('/pharmacy/{record}/call-audio', [PharmacyController::class, 'callAudio'])->name('pharmacy.call-audio');
    Route::get('/pharmacy/{record}/label', [PharmacyController::class, 'printLabel'])->name('pharmacy.label');
    Route::get('/pharmacy/{record}/bottle-labels', [PharmacyController::class, 'printBottleLabels'])->name('pharmacy.bottle-labels');
    Route::get('/pharmacy/prescriptions/{prescription}/bottle-label', [PharmacyController::class, 'printSingleBottleLabel'])->name('pharmacy.single-bottle-label');
    Route::get('/pharmacy/{record}/schedule', [MedicationScheduleController::class, 'show'])->name('pharmacy.schedule');
    Route::get('/pharmacy/{record}/schedule/ical', [MedicationScheduleController::class, 'downloadIcal'])->name('pharmacy.schedule.ical');

    // 3.3b. Quản lý Kho Dược & Danh Mục Thuốc Ngoại Trú (Pharmacy Formulary & Inventory)
    Route::get('/pharmacy/inventory', [PharmacyInventoryController::class, 'index'])->name('pharmacy.inventory.index');
    Route::post('/pharmacy/inventory', [PharmacyInventoryController::class, 'store'])->name('pharmacy.inventory.store');
    Route::put('/pharmacy/inventory/{medication}', [PharmacyInventoryController::class, 'update'])->name('pharmacy.inventory.update');
    Route::post('/pharmacy/inventory/{medication}/restock', [PharmacyInventoryController::class, 'restock'])->name('pharmacy.inventory.restock');
    Route::delete('/pharmacy/inventory/{medication}', [PharmacyInventoryController::class, 'destroy'])->name('pharmacy.inventory.destroy');

    // 3.4. Giấy Chứng Nhận Nghỉ Việc Hưởng BHXH (Mẫu C65-HD Bộ Y Tế)
    Route::get('/certificates', [SickLeaveCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/records/{record}/certificate', [SickLeaveCertificateController::class, 'create'])->name('certificates.create');
    Route::post('/records/{record}/certificate', [SickLeaveCertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}/print', [SickLeaveCertificateController::class, 'print'])->name('certificates.print');
    Route::delete('/certificates/{certificate}', [SickLeaveCertificateController::class, 'destroy'])->name('certificates.destroy');

    // 3.5. Bản Cam Đoan Phẫu Thuật / Thủ Thuật Y Khoa Ký Chữ Ký Số Điện Tử (E-Informed Consent)
    Route::get('/consents', [InformedConsentController::class, 'index'])->name('consents.index');
    Route::get('/patients/{patient}/consents/create', [InformedConsentController::class, 'create'])->name('consents.create');
    Route::post('/consents', [InformedConsentController::class, 'store'])->name('consents.store');
    Route::get('/consents/{consent}/print', [InformedConsentController::class, 'print'])->name('consents.print');
    Route::delete('/consents/{consent}', [InformedConsentController::class, 'destroy'])->name('consents.destroy');

    // 3.6. Quản Lý Hóa Đơn Viện Phí & Thanh Toán VietQR Tự Động (Smart Billing)
    Route::get('/invoices', [PatientInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [PatientInvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/appointments/{appointment}/create-invoice', [PatientInvoiceController::class, 'createFromAppointment'])->name('invoices.create-from-appointment');
    Route::post('/invoices/{invoice}/confirm-payment', [PatientInvoiceController::class, 'confirmPayment'])->name('invoices.confirm-payment');
    Route::get('/invoices/{invoice}/print', [PatientInvoiceController::class, 'print'])->name('invoices.print');

    // 3.7. Trung Tâm Giám Sát Hiệu Suất Tiếp Nhận & An Toàn Dược (BI Analytics & Operations)
    Route::get('/analytics', [ReceptionAnalyticsController::class, 'index'])->name('analytics.index');

    // 3.8. Sơ Đồ Giường Ngoại Trú & Khu Lưu Bệnh Ban Ngày (Day-Bed Management)
    Route::get('/day-beds', [DayBedController::class, 'index'])->name('day-beds.index');
    Route::post('/day-beds/admit', [DayBedController::class, 'storeAdmission'])->name('day-beds.admit');
    Route::get('/day-beds/admissions/{admission}', [DayBedController::class, 'showAdmission'])->name('day-beds.show');
    Route::post('/day-beds/admissions/{admission}/infusion', [DayBedController::class, 'storeInfusion'])->name('day-beds.infusion.store');
    Route::post('/day-beds/infusions/{infusion}/complete', [DayBedController::class, 'completeInfusion'])->name('day-beds.infusion.complete');
    Route::post('/day-beds/admissions/{admission}/discharge', [DayBedController::class, 'discharge'])->name('day-beds.discharge');
    Route::post('/day-beds/{bed}/cleaning-done', [DayBedController::class, 'finishCleaning'])->name('day-beds.cleaning-done');

    // 3.9. Trung Tâm Thông Báo Zalo ZNS / SMS Brandname & Cảnh Báo Sắp Đến Lượt
    Route::get('/notifications', [ReceptionNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/appointments/{appointment}/alert', [ReceptionNotificationController::class, 'dispatchAlert'])->name('notifications.alert');
    Route::post('/appointments/{appointment}/prescription-alert', [ReceptionNotificationController::class, 'dispatchPrescriptionAlert'])->name('notifications.prescription-alert');

    // 3.10. Trung Tâm Tiêm Chủng Ngoại Trú (Vaccination Management)
    Route::get('/vaccinations', [VaccinationController::class, 'index'])->name('vaccinations.index');
    Route::get('/vaccinations/create', [VaccinationController::class, 'create'])->name('vaccinations.create');
    Route::post('/vaccinations', [VaccinationController::class, 'store'])->name('vaccinations.store');
    Route::get('/vaccinations/{record}', [VaccinationController::class, 'show'])->name('vaccinations.show');
    Route::post('/vaccinations/{record}/clear', [VaccinationController::class, 'clearObservation'])->name('vaccinations.clear');
    Route::get('/vaccinations/{record}/certificate', [VaccinationController::class, 'printCertificate'])->name('vaccinations.certificate');

    // 3.11. Tiếp Nhận Cấp Cứu Trước Viện & Báo Động Đỏ Toàn Viện (Code Red Emergency)
    Route::get('/code-red', [CodeRedEmergencyController::class, 'index'])->name('code-red.index');
    Route::post('/code-red', [CodeRedEmergencyController::class, 'store'])->name('code-red.store');
    Route::post('/code-red/{codeRed}/mark-arrived', [CodeRedEmergencyController::class, 'markArrived'])->name('code-red.mark-arrived');
    Route::post('/code-red/{codeRed}/mark-intervention', [CodeRedEmergencyController::class, 'markIntervention'])->name('code-red.mark-intervention');
    Route::post('/code-red/{codeRed}/resolve', [CodeRedEmergencyController::class, 'resolve'])->name('code-red.resolve');

    // 3.12. Giao Thuốc Ngoại Trú Tận Nhà & Cấp Thuốc Bệnh Mãn Tính (Medication Delivery)
    Route::get('/deliveries', [MedicationDeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('/deliveries', [MedicationDeliveryController::class, 'store'])->name('deliveries.store');
    Route::post('/deliveries/{delivery}/status', [MedicationDeliveryController::class, 'updateStatus'])->name('deliveries.status');
    Route::get('/deliveries/{delivery}/waybill', [MedicationDeliveryController::class, 'printWaybill'])->name('deliveries.waybill');

    // 3.13. Khảo Sát Đánh Giá Trải Nghiệm & Hài Lòng Bộ Y Tế (Patient CSAT & NPS)
    Route::get('/surveys', [ReceptionSurveyController::class, 'index'])->name('surveys.index');
    Route::post('/surveys/{survey}/resolve', [ReceptionSurveyController::class, 'resolveFollowup'])->name('surveys.resolve');

    // 3.14. Trung Tâm Cận Lâm Sàng & Xét Nghiệm Y Khoa (Lab & Imaging Orders)
    Route::get('/lab-orders', [LabOrderController::class, 'index'])->name('lab-orders.index');
    Route::get('/lab-orders/create', [LabOrderController::class, 'create'])->name('lab-orders.create');
    Route::post('/lab-orders', [LabOrderController::class, 'store'])->name('lab-orders.store');
    Route::get('/lab-orders/{labOrder}', [LabOrderController::class, 'show'])->name('lab-orders.show');
    Route::post('/lab-orders/items/{item}/update', [LabOrderController::class, 'updateItemResult'])->name('lab-orders.items.update');
    Route::get('/lab-orders/{labOrder}/report', [LabOrderController::class, 'printReport'])->name('lab-orders.report');

    // 3.15. Kiểm Soát An Toàn Truyền Máu Lâm Sàng (Blood Transfusion Safety)
    Route::get('/blood-transfusions', [BloodTransfusionController::class, 'index'])->name('blood-transfusions.index');
    Route::get('/blood-transfusions/create', [BloodTransfusionController::class, 'create'])->name('blood-transfusions.create');
    Route::post('/blood-transfusions', [BloodTransfusionController::class, 'store'])->name('blood-transfusions.store');
    Route::get('/blood-transfusions/{transfusion}', [BloodTransfusionController::class, 'show'])->name('blood-transfusions.show');
    Route::post('/blood-transfusions/{transfusion}/start', [BloodTransfusionController::class, 'start'])->name('blood-transfusions.start');
    Route::post('/blood-transfusions/{transfusion}/vitals', [BloodTransfusionController::class, 'recordVitals'])->name('blood-transfusions.vitals');
    Route::post('/blood-transfusions/{transfusion}/complete', [BloodTransfusionController::class, 'complete'])->name('blood-transfusions.complete');
    Route::post('/blood-transfusions/{transfusion}/reaction', [BloodTransfusionController::class, 'reaction'])->name('blood-transfusions.reaction');

    // 3.16. Quản Lý Mượn Trả Xe Lăn & Khử Khuẩn Thiết Bị Di Chuyển (Mobility Equipment)
    Route::get('/mobility', [MobilityEquipmentController::class, 'index'])->name('mobility.index');
    Route::post('/mobility/borrow', [MobilityEquipmentController::class, 'borrow'])->name('mobility.borrow');
    Route::post('/mobility/return/{loan}', [MobilityEquipmentController::class, 'return'])->name('mobility.return');
    Route::post('/mobility/disinfect/{equipment}', [MobilityEquipmentController::class, 'completeDisinfection'])->name('mobility.disinfect');

    // 3.17. Khoa Dinh Dưỡng Lâm Sàng & Thực Đơn Điều Trị (Clinical Nutrition)
    Route::get('/nutrition', [ClinicalNutritionController::class, 'index'])->name('nutrition.index');
    Route::get('/nutrition/create', [ClinicalNutritionController::class, 'create'])->name('nutrition.create');
    Route::post('/nutrition', [ClinicalNutritionController::class, 'store'])->name('nutrition.store');
    Route::get('/nutrition/{assessment}', [ClinicalNutritionController::class, 'show'])->name('nutrition.show');
    Route::get('/nutrition/{assessment}/print-plan', [ClinicalNutritionController::class, 'printPlan'])->name('nutrition.print-plan');
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
    Route::get('/records/{record}/certificate', [SickLeaveCertificateController::class, 'create'])->name('records.certificate.create');
    Route::post('/records/{record}/certificate', [SickLeaveCertificateController::class, 'store'])->name('records.certificate.store');
    Route::post('/records/{record}/attachments', [DoctorConsultationController::class, 'uploadAttachment'])->name('records.attachments.upload');
    Route::delete('/records/{record}/attachments/{attachment}', [DoctorConsultationController::class, 'destroyAttachment'])->name('records.attachments.destroy');
    Route::get('/records/{record}/print', [DoctorConsultationController::class, 'printPrescription'])->name('records.print');
});

// =========================================================================
// ====== MODULE 5: IN-HOSPITAL CLINICAL CONSULTATION =======================
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::post('/consultations/{consultation}/respond', [ClinicalConsultationController::class, 'respond'])->name('consultations.respond');
    Route::get('/consultations/{consultation}/print', [ClinicalConsultationController::class, 'print'])->name('consultations.print');
    Route::resource('consultations', ClinicalConsultationController::class);
});
