@php
    $footerHospitalName = 'BỆNH VIỆN TÂM ANH';
    $footerHotline = '1800 6858';
    $footerAddress = '108 Hoàng Như Tiếp, P. Bồ Đề, Q. Long Biên, TP. Hà Nội';
    try {
        if (class_exists(\App\Models\SystemSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            $footerHospitalName = \App\Models\SystemSetting::get('hospital_name', $footerHospitalName) ?: $footerHospitalName;
            $footerHotline = \App\Models\SystemSetting::get('hospital_hotline', $footerHotline) ?: $footerHotline;
            $footerAddress = \App\Models\SystemSetting::get('hospital_address', $footerAddress) ?: $footerAddress;
        }
    } catch (\Throwable $e) {
    }
@endphp
<footer class="bg-dark text-white pt-5 pb-4 mt-auto" style="background-color: #0f172a !important;">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="bi bi-hospital fs-5"></i>
                    </div>
                    <span class="fw-bold fs-5 text-white text-uppercase">{{ $footerHospitalName }}</span>
                </div>
                <p class="text-secondary small">
                    Hệ thống Bệnh viện Đa khoa Tâm Anh quy tụ đội ngũ chuyên gia đầu ngành, trang thiết bị tối tân, mang đến dịch vụ chăm sóc sức khỏe toàn diện và chuẩn mực quốc tế.
                </p>
                <div class="d-flex gap-2">
                    <span class="badge bg-secondary">Hà Nội</span>
                    <span class="badge bg-secondary">TP. Hồ Chí Minh</span>
                    <span class="badge bg-success">Cấp cứu 24/7</span>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3 text-light border-start border-3 border-primary ps-2">Thông tin liên hệ</h5>
                <ul class="list-unstyled text-secondary small mb-0 d-flex flex-column gap-2">
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-danger mt-1"></i>
                        <span>{{ $footerAddress }}</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-success"></i>
                        <span>Tổng đài đặt lịch: <strong>{{ $footerHotline }}</strong> (Miễn phí)</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-info"></i>
                        <span>cskh@tamanhhospital.vn</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-fill text-warning"></i>
                        <span>Khám ngoại trú: 07:30 - 17:00 (Từ Thứ Hai đến Chủ Nhật)</span>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h5 class="fw-bold mb-3 text-light border-start border-3 border-teal ps-2" style="border-color: #0d9488 !important;">Đồ án Môn học</h5>
                <p class="text-secondary small mb-2">
                    <strong>Đề tài:</strong> Hệ thống Quản lý Bệnh viện Tâm Anh<br>
                    <strong>Nền tảng:</strong> PHP Laravel Framework & MySQL<br>
                    <strong>Nhóm phát triển:</strong> 04 thành viên (Quý - Đạt - Hưng - Bảo)
                </p>
                <div class="card bg-secondary bg-opacity-25 border-0 p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-light small">Tra cứu nhanh lịch khám:</span>
                        <a href="{{ route('booking.lookup') }}" class="btn btn-sm btn-outline-info rounded-pill">Tra cứu ngay</a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-3 opacity-25">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 text-secondary small">
            <div>
                &copy; {{ date('Y') }} <strong>Bệnh viện Đa khoa Tâm Anh</strong>. Bản quyền thuộc về Nhóm đồ án Phát triển ứng dụng Web.
            </div>
            <div>
                <span>Tech Lead: Nguyễn Thành Quý</span>
            </div>
        </div>
    </div>
</footer>
