<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HospitalBed;
use App\Models\LabOrder;
use App\Models\Medication;
use App\Models\PatientInvoice;
use App\Models\PatientProfile;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversalSearchController extends Controller
{
    /**
     * Universal Spotlight Search across the entire hospital system.
     * Searches patients, doctors, medications, invoices, hospital beds/rooms, lab orders, appointments, specialties, and system functions.
     */
    public function search(Request $request): JsonResponse
    {
        $rawQuery = trim($request->input('q', ''));

        if (mb_strlen($rawQuery) < 2) {
            return response()->json([
                'query' => $rawQuery,
                'total' => 0,
                'results' => [
                    'navigation' => $this->getDefaultNavigationSuggestions(),
                    'patients' => [],
                    'doctors' => [],
                    'medications' => [],
                    'invoices' => [],
                    'beds' => [],
                    'labs' => [],
                    'appointments' => [],
                    'specialties' => [],
                ],
            ]);
        }

        $query = '%' . $rawQuery . '%';

        // 1. Tìm kiếm Bệnh nhân (Patients)
        $patients = PatientProfile::query()
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'like', $query)
                    ->orWhere('patient_code', 'like', $query)
                    ->orWhere('phone', 'like', $query)
                    ->orWhere('identity_card', 'like', $query)
                    ->orWhere('insurance_code', 'like', $query);
            })
            ->limit(5)
            ->get()
            ->map(function (PatientProfile $p) {
                return [
                    'title' => $p->full_name . ' (' . $p->patient_code . ')',
                    'subtitle' => 'SĐT: ' . ($p->phone ?? 'N/A') . ' • Ngày sinh: ' . ($p->formatted_dob ?? 'N/A') . ($p->allergies ? ' • Dị ứng: ' . $p->allergies : ''),
                    'badge' => $p->blood_group ? 'Nhóm ' . $p->blood_group : 'Bệnh nhân',
                    'badge_class' => $p->allergies ? 'bg-danger' : 'bg-primary',
                    'icon' => 'bi-person-vcard',
                    'url' => route('reception.patients.show', $p->id),
                ];
            });

        // 2. Tìm kiếm Bác sĩ (Doctors)
        $doctors = Doctor::query()
            ->with(['user', 'specialty'])
            ->where(function ($mainQ) use ($query) {
                $mainQ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', $query)
                        ->orWhere('phone', 'like', $query);
                })
                ->orWhere('qualification', 'like', $query)
                ->orWhere('room_number', 'like', $query)
                ->orWhereHas('specialty', function ($q) use ($query) {
                    $q->where('name', 'like', $query);
                });
            })
            ->limit(5)
            ->get()
            ->map(function (Doctor $d) {
                $name = $d->user->name ?? 'Bác sĩ';
                $spec = $d->specialty->name ?? 'Đa khoa';
                return [
                    'title' => 'BS. ' . $name,
                    'subtitle' => 'Chuyên khoa: ' . $spec . ($d->room_number ? ' • Phòng: ' . $d->room_number : '') . ($d->qualification ? ' • ' . $d->qualification : ''),
                    'badge' => $d->is_available ? 'Đang trực' : 'Nghỉ ca',
                    'badge_class' => $d->is_available ? 'bg-success' : 'bg-secondary',
                    'icon' => 'bi-person-badge',
                    'url' => route('admin.doctors.index'),
                ];
            });

        // 3. Tìm kiếm Thuốc & Dược phẩm (Medications)
        $medications = Medication::query()
            ->where('name', 'like', $query)
            ->orWhere('medication_code', 'like', $query)
            ->orWhere('active_ingredient', 'like', $query)
            ->limit(5)
            ->get()
            ->map(function (Medication $m) {
                return [
                    'title' => $m->name,
                    'subtitle' => 'Hoạt chất: ' . ($m->active_ingredient ?? 'N/A') . ' • Mã: ' . $m->medication_code . ' • Tồn: ' . number_format($m->stock_quantity) . ' ' . $m->unit,
                    'badge' => $m->isLowStock() ? 'Sắp hết kho' : 'Còn hàng',
                    'badge_class' => $m->isLowStock() ? 'bg-danger' : 'bg-info',
                    'icon' => 'bi-capsule-pill',
                    'url' => route('reception.pharmacy.inventory.index'),
                ];
            });

        // 4. Tìm kiếm Hóa đơn & Viện phí (Invoices)
        $invoices = PatientInvoice::query()
            ->with('patientProfile')
            ->where('invoice_code', 'like', $query)
            ->orWhere('vietqr_ref', 'like', $query)
            ->orWhereHas('patientProfile', function ($q) use ($query) {
                $q->where('full_name', 'like', $query);
            })
            ->limit(5)
            ->get()
            ->map(function (PatientInvoice $inv) {
                $statusText = match ($inv->payment_status) {
                    PatientInvoice::STATUS_PAID => 'Đã thanh toán',
                    PatientInvoice::STATUS_REFUNDED => 'Đã hoàn tiền',
                    default => 'Chưa thanh toán',
                };
                $badgeClass = match ($inv->payment_status) {
                    PatientInvoice::STATUS_PAID => 'bg-success',
                    PatientInvoice::STATUS_REFUNDED => 'bg-secondary',
                    default => 'bg-warning text-dark',
                };

                return [
                    'title' => 'Hóa đơn: ' . $inv->invoice_code,
                    'subtitle' => 'BN: ' . ($inv->patientProfile->full_name ?? 'N/A') . ' • Tổng tiền: ' . number_format($inv->total_amount) . ' VNĐ',
                    'badge' => $statusText,
                    'badge_class' => $badgeClass,
                    'icon' => 'bi-receipt',
                    'url' => route('reception.invoices.index', ['code' => $inv->invoice_code]),
                ];
            });

        // 5. Tìm kiếm Buồng bệnh & Giường nội trú (Hospital Beds & Rooms)
        $beds = HospitalBed::query()
            ->with(['room', 'currentPatient'])
            ->where(function ($bQ) use ($query) {
                $bQ->where('bed_code', 'like', $query)
                    ->orWhereHas('room', function ($rQ) use ($query) {
                        $rQ->where('room_number', 'like', $query)
                            ->orWhere('department', 'like', $query)
                            ->orWhere('room_type', 'like', $query);
                    })
                    ->orWhereHas('currentPatient', function ($pQ) use ($query) {
                        $pQ->where('full_name', 'like', $query);
                    });
            })
            ->limit(5)
            ->get()
            ->map(function (HospitalBed $b) {
                $roomNum = $b->room->room_number ?? 'Chưa xếp phòng';
                $dept = $b->room->department ?? 'Nội trú';
                $patientName = $b->currentPatient->full_name ?? null;

                $statusMap = [
                    'available' => ['text' => 'Giường trống', 'class' => 'bg-success'],
                    'occupied' => ['text' => 'Đang nằm', 'class' => 'bg-primary'],
                    'cleaning' => ['text' => 'Đang khử khuẩn', 'class' => 'bg-warning text-dark'],
                    'maintenance' => ['text' => 'Bảo trì', 'class' => 'bg-danger'],
                ];
                $bedStatus = $statusMap[$b->status] ?? ['text' => ucfirst($b->status), 'class' => 'bg-secondary'];

                return [
                    'title' => 'Giường ' . $b->bed_code . ' - Phòng ' . $roomNum,
                    'subtitle' => 'Khoa: ' . $dept . ($patientName ? ' • Đang nằm: ' . $patientName : ' • Sẵn sàng đón người bệnh'),
                    'badge' => $bedStatus['text'],
                    'badge_class' => $bedStatus['class'],
                    'icon' => 'bi-hospital',
                    'url' => route('reception.bed-board.index'),
                ];
            });

        // 6. Tìm kiếm Phiếu Cận Lâm Sàng & Xét nghiệm (Lab Orders)
        $labs = LabOrder::query()
            ->with('patientProfile')
            ->where('order_code', 'like', $query)
            ->orWhere('clinical_diagnosis', 'like', $query)
            ->orWhereHas('patientProfile', function ($pQ) use ($query) {
                $pQ->where('full_name', 'like', $query);
            })
            ->limit(5)
            ->get()
            ->map(function (LabOrder $lo) {
                $statusMap = [
                    LabOrder::STATUS_PENDING => ['text' => 'Chờ lấy mẫu', 'class' => 'bg-warning text-dark'],
                    LabOrder::STATUS_SAMPLING => ['text' => 'Đang lấy mẫu', 'class' => 'bg-info'],
                    LabOrder::STATUS_PROCESSING => ['text' => 'Đang phân tích', 'class' => 'bg-primary'],
                    LabOrder::STATUS_COMPLETED => ['text' => 'Đã có kết quả', 'class' => 'bg-success'],
                    LabOrder::STATUS_CANCELLED => ['text' => 'Đã hủy', 'class' => 'bg-secondary'],
                ];
                $st = $statusMap[$lo->status] ?? ['text' => $lo->status, 'class' => 'bg-secondary'];

                return [
                    'title' => 'Xét nghiệm: ' . $lo->order_code,
                    'subtitle' => 'BN: ' . ($lo->patientProfile->full_name ?? 'N/A') . ' • CĐ: ' . ($lo->clinical_diagnosis ?? 'Chưa ghi nhận'),
                    'badge' => $st['text'],
                    'badge_class' => $st['class'],
                    'icon' => 'bi-moisture',
                    'url' => route('reception.lab-orders.index'),
                ];
            });

        // 7. Tìm kiếm Lịch Hẹn & Tiếp Nhận (Appointments)
        $appointments = Appointment::query()
            ->with(['patientProfile', 'specialty'])
            ->where('appointment_code', 'like', $query)
            ->orWhere('symptom_description', 'like', $query)
            ->orWhereHas('patientProfile', function ($pQ) use ($query) {
                $pQ->where('full_name', 'like', $query);
            })
            ->limit(5)
            ->get()
            ->map(function (Appointment $apt) {
                $spec = $apt->specialty->name ?? 'Đa khoa';
                $statusText = match ($apt->status) {
                    Appointment::STATUS_CONFIRMED => 'Đã xác nhận',
                    Appointment::STATUS_COMPLETED => 'Đã khám xong',
                    Appointment::STATUS_CANCELLED => 'Đã hủy',
                    default => 'Chờ khám',
                };
                $badgeClass = match ($apt->status) {
                    Appointment::STATUS_COMPLETED => 'bg-success',
                    Appointment::STATUS_CONFIRMED => 'bg-primary',
                    Appointment::STATUS_CANCELLED => 'bg-secondary',
                    default => 'bg-warning text-dark',
                };

                return [
                    'title' => 'Tiếp nhận: ' . $apt->appointment_code,
                    'subtitle' => 'BN: ' . ($apt->patientProfile->full_name ?? 'N/A') . ' • Khoa: ' . $spec . ($apt->symptom_description ? ' • TC: ' . $apt->symptom_description : ''),
                    'badge' => $statusText,
                    'badge_class' => $badgeClass,
                    'icon' => 'bi-calendar-check',
                    'url' => route('reception.appointments.index'),
                ];
            });

        // 8. Tìm kiếm Chuyên khoa (Specialties)
        $specialties = Specialty::query()
            ->where('name', 'like', $query)
            ->orWhere('description', 'like', $query)
            ->limit(5)
            ->get()
            ->map(function (Specialty $s) {
                return [
                    'title' => 'Chuyên khoa: ' . $s->name,
                    'subtitle' => $s->description ?? 'Chuyên khoa khám chữa bệnh chất lượng cao',
                    'badge' => $s->is_active ? 'Đang mở' : 'Tạm dừng',
                    'badge_class' => $s->is_active ? 'bg-success' : 'bg-secondary',
                    'icon' => 'bi-diagram-3',
                    'url' => route('admin.specialties.index'),
                ];
            });

        // 9. Tìm kiếm Chức năng hệ thống (System Navigation)
        $navigation = $this->searchSystemNavigation($rawQuery);

        $totalResults = count($patients) + count($doctors) + count($medications) + count($invoices) 
            + count($beds) + count($labs) + count($appointments) + count($specialties) + count($navigation);

        return response()->json([
            'query' => $rawQuery,
            'total' => $totalResults,
            'results' => [
                'navigation' => $navigation,
                'patients' => $patients,
                'doctors' => $doctors,
                'medications' => $medications,
                'invoices' => $invoices,
                'beds' => $beds,
                'labs' => $labs,
                'appointments' => $appointments,
                'specialties' => $specialties,
            ],
        ]);
    }

    /**
     * Search available navigation features by keywords.
     */
    protected function searchSystemNavigation(string $q): array
    {
        $features = [
            ['title' => 'Tiếp nhận & Check-in Bệnh nhân', 'subtitle' => 'Quản lý lịch hẹn, tiếp đón vãng lai, in phiếu khám', 'url' => route('reception.appointments.index'), 'icon' => 'bi-calendar-check', 'badge' => 'Tiếp đón'],
            ['title' => 'Quản lý Hồ sơ Người bệnh', 'subtitle' => 'Tạo mới, in vòng đeo tay y tế mã vạch, tiền sử dị ứng', 'url' => route('reception.patients.index'), 'icon' => 'bi-person-vcard', 'badge' => 'Bệnh nhân'],
            ['title' => 'Kiosk Tự Phục Vụ Khám Bệnh', 'subtitle' => 'Sảnh đón tiếp tự phục vụ, quét CCCD/BHYT, lấy số nhiệt 80mm', 'url' => url('/kiosk'), 'icon' => 'bi-display', 'badge' => 'Kiosk'],
            ['title' => 'Hàng Đợi TV & Gọi Loa Thông Minh', 'subtitle' => 'Màn hình sảnh chờ, Text-to-Speech gọi loa vào phòng khám', 'url' => route('reception.appointments.queue-display'), 'icon' => 'bi-megaphone-fill', 'badge' => 'Sảnh chờ'],
            ['title' => 'Hóa Đơn & Thanh Toán VietQR', 'subtitle' => 'Tách BHYT, sinh mã VietQR Napas247, in hóa đơn viện phí', 'url' => route('reception.invoices.index'), 'icon' => 'bi-qr-code-scan', 'badge' => 'Viện phí'],
            ['title' => 'Ví Ký Quỹ & Tạm Ứng Viện Phí', 'subtitle' => 'Nạp ví tạm ứng một chạm, hoàn ứng minh bạch', 'url' => route('reception.wallets.index'), 'icon' => 'bi-wallet2', 'badge' => 'Tài chính'],
            ['title' => 'Sổ Giấy Nghỉ Việc Hưởng BHXH (C65)', 'subtitle' => 'Cấp giấy chứng nhận nghỉ ốm Thông tư 56/2017/TT-BYT', 'url' => route('reception.certificates.index'), 'icon' => 'bi-journal-medical', 'badge' => 'BHXH'],
            ['title' => 'Quầy Dược Phát Thuốc Ngoại Trú', 'subtitle' => 'Duyệt đơn thuốc, cảnh báo dị ứng chéo, cấp phát', 'url' => route('reception.pharmacy.index'), 'icon' => 'bi-capsule-pill', 'badge' => 'Dược phẩm'],
            ['title' => 'Kho Dược & Tồn Kho (Hạn dùng FEFO)', 'subtitle' => 'Theo dõi lô, xuất kho theo hạn dùng, cảnh báo hết hạn', 'url' => route('reception.pharmacy.inventory.index'), 'icon' => 'bi-box-seam', 'badge' => 'Kho dược'],
            ['title' => 'Cận Lâm Sàng & Xét Nghiệm', 'subtitle' => 'Chỉ định CLS, cảnh báo giá trị nguy kịch Panic Value', 'url' => route('reception.lab-orders.index'), 'icon' => 'bi-moisture', 'badge' => 'Xét nghiệm'],
            ['title' => 'An Toàn Truyền Máu Tại Giường', 'subtitle' => 'Đối chiếu chéo ABO/Rh, chặn bất đồng nhóm máu, ký duyệt kép', 'url' => route('reception.blood-transfusions.index'), 'icon' => 'bi-droplet-half', 'badge' => 'Truyền máu'],
            ['title' => 'Báo Động Đỏ Cấp Cứu (Code Red / Code Blue)', 'subtitle' => 'Kích hoạt báo động khẩn cấp, khóa buồng cấp cứu', 'url' => route('reception.code-red.index'), 'icon' => 'bi-bell-fill', 'badge' => 'Cấp cứu'],
            ['title' => 'Cảnh Báo Sớm NEWS2 & Đội Phản Ứng Nhanh RRT', 'subtitle' => 'Thang điểm suy sụp lâm sàng, thông báo can thiệp 3 phút', 'url' => route('reception.news2-rrt.index'), 'icon' => 'bi-activity', 'badge' => 'Cảnh báo sớm'],
            ['title' => 'Quản Lý Buồng Bệnh & Giường Nội Trú', 'subtitle' => 'Sơ đồ giường trực quan, trạng thái khử khuẩn Cloramin B', 'url' => route('reception.bed-board.index'), 'icon' => 'bi-door-open-fill', 'badge' => 'Nội trú'],
            ['title' => 'Giường Ngoại Trú & Lưu Ngày (Day-Bed)', 'subtitle' => 'Theo dõi bệnh nhân lưu ngày sau tiểu phẫu/truyền dịch', 'url' => route('reception.day-beds.index'), 'icon' => 'bi-hospital', 'badge' => 'Lưu ngày'],
            ['title' => 'Lọc Máu Thận Nhân Tạo Chu Kỳ (Hemodialysis)', 'subtitle' => 'Giám sát ca lọc máu, số lần tái sử dụng quả lọc, rút dịch UF', 'url' => route('reception.hemodialysis.index'), 'icon' => 'bi-droplet-fill', 'badge' => 'Thận nhân tạo'],
            ['title' => 'Tiệt Khuẩn Dụng Cụ Y Tế CSSD', 'subtitle' => 'Theo dõi chu trình lò hấp Autoclave, mã vạch khay mổ', 'url' => route('reception.cssd.index'), 'icon' => 'bi-tools', 'badge' => 'Tiệt khuẩn'],
            ['title' => 'Chuỗi Lạnh IoT Vắc-Xin', 'subtitle' => 'Giám sát nhiệt độ 2-8°C, tự khóa an toàn khi vi phạm', 'url' => route('reception.cold-chain.index'), 'icon' => 'bi-snow', 'badge' => 'Chuỗi lạnh'],
            ['title' => 'Phòng Mổ Thông Minh & Hồi Tỉnh PACU', 'subtitle' => 'Bảng kiểm an toàn WHO 3 bước, kiểm đếm gạc RFID', 'url' => route('reception.operating-room.index'), 'icon' => 'bi-hospital', 'badge' => 'Phòng mổ'],
            ['title' => 'Đội Xe Robot Tự Hành AGV / AMR', 'subtitle' => 'Điều phối vận chuyển bệnh viện, tự quay về dock sạc pin', 'url' => route('reception.autonomous-logistics-esg.index'), 'icon' => 'bi-robot', 'badge' => 'Logistics'],
            ['title' => 'Đường Ống Khí Nén PTS Chuyển Mẫu', 'subtitle' => 'Giám sát thoi khí nén gia tốc G < 2.5G chống vỡ hồng cầu', 'url' => route('reception.autonomous-logistics-esg.index'), 'icon' => 'bi-send-fill', 'badge' => 'Logistics'],
            ['title' => 'Giám Sát Hiệu Suất Tiếp Đón BI Analytics', 'subtitle' => 'Biểu đồ thời gian chờ, tỷ lệ BHYT vs Viện phí', 'url' => route('reception.analytics.index'), 'icon' => 'bi-graph-up-arrow', 'badge' => 'Báo cáo BI'],
            ['title' => 'Hội Chẩn Đa Chuyên Khoa (MDT Consultation)', 'subtitle' => 'Hội đồng bác sĩ chẩn đoán các ca bệnh phức tạp', 'url' => route('consultations.index'), 'icon' => 'bi-people-fill', 'badge' => 'Hội chẩn'],
        ];

        $needle = mb_strtolower($q);
        $normNeedle = $this->removeVietnameseTones($needle);

        $matched = [];
        foreach ($features as $f) {
            $haystack = mb_strtolower($f['title'] . ' ' . $f['subtitle'] . ' ' . $f['badge']);
            $normHaystack = $this->removeVietnameseTones($haystack);

            if (str_contains($haystack, $needle) || str_contains($normHaystack, $normNeedle)) {
                $matched[] = $f;
            }
        }

        return array_slice($matched, 0, 5);
    }

    /**
     * Default quick action navigation suggestions when search query is empty.
     */
    protected function getDefaultNavigationSuggestions(): array
    {
        return [
            ['title' => 'Tiếp nhận & Check-in', 'subtitle' => 'Mở sảnh đón tiếp bệnh nhân', 'url' => route('reception.appointments.index'), 'icon' => 'bi-calendar-check', 'badge' => 'Phổ biến'],
            ['title' => 'Quản lý Bệnh nhân', 'subtitle' => 'Tra cứu hồ sơ hoặc tạo bệnh nhân mới', 'url' => route('reception.patients.index'), 'icon' => 'bi-person-vcard', 'badge' => 'Hồ sơ'],
            ['title' => 'Hóa Đơn & Thu Phí VietQR', 'subtitle' => 'Thanh toán viện phí không tiền mặt', 'url' => route('reception.invoices.index'), 'icon' => 'bi-qr-code-scan', 'badge' => 'Viện phí'],
            ['title' => 'Kho Dược & Tồn Kho (FEFO)', 'subtitle' => 'Tra cứu danh mục thuốc và tồn kho', 'url' => route('reception.pharmacy.inventory.index'), 'icon' => 'bi-box-seam', 'badge' => 'Dược phẩm'],
            ['title' => 'Báo Động Đỏ Cấp Cứu', 'subtitle' => 'Kích hoạt Code Red khẩn cấp toàn viện', 'url' => route('reception.code-red.index'), 'icon' => 'bi-bell-fill', 'badge' => 'Cấp cứu'],
        ];
    }

    /**
     * Remove Vietnamese accents helper for server-side matching.
     */
    protected function removeVietnameseTones(string $str): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ộ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];

        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/($accent)/i", $nonAccent, $str);
        }

        return mb_strtolower($str);
    }
}
