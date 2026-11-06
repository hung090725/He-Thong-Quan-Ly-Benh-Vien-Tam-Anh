@extends('layouts.admin')

@section('title', 'Quản Trị Khoa Phòng, Buồng Bệnh Nội Trú Thông Minh & Trạm Điều Dưỡng')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-hospital-fill text-primary me-2"></i>Quản Trị Khoa Phòng, Buồng Bệnh Nội Trú Thông Minh & Trạm Điều Dưỡng
            </h4>
            <p class="text-muted small mb-0">
                Wave 9 • Buồng bệnh nội trú thông minh IoT 5 sao, Phân bổ động phòng khám ngoại trú & Biển tên E-Paper, Robot khử trùng buồng sau xuất viện UV-C, Phòng sinh gia đình LDRP 4-trong-1 & Trạm điều dưỡng trung tâm
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-building-check me-1"></i> Smart Hospital Ward & Spatial Facilities Active
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Buồng Bệnh Thông Minh</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total_inpatient_consoles'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-tablet-landscape fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Chuông Gọi Điều Dưỡng</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['active_nurse_calls'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-bell-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Phòng Khám Đang Khám</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['active_consulting_rooms'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-door-open-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Buồng Đã Khử Trùng Sạch</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['certified_clean_ready'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Buồng Sinh LDRP Hoạt Động</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_in_labor'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-heart-pulse fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-inpatient-tab" data-bs-toggle="pill" data-bs-target="#tab-inpatient" type="button" role="tab">
                <i class="bi bi-tablet me-1"></i> 1. Buồng Bệnh Thông Minh IoT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-opd-tab" data-bs-toggle="pill" data-bs-target="#tab-opd" type="button" role="tab">
                <i class="bi bi-display me-1"></i> 2. Phòng Khám & E-Paper
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-disinfection-tab" data-bs-toggle="pill" data-bs-target="#tab-disinfection" type="button" role="tab">
                <i class="bi bi-robot me-1"></i> 3. Khử Trùng Buồng UV-C
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-ldrp-tab" data-bs-toggle="pill" data-bs-target="#tab-ldrp" type="button" role="tab">
                <i class="bi bi-gender-female me-1"></i> 4. Buồng Sinh LDRP 4-in-1
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-nursehub-tab" data-bs-toggle="pill" data-bs-target="#tab-nursehub" type="button" role="tab">
                <i class="bi bi-grid-3x3-gap me-1"></i> 5. Trạm Điều Dưỡng Trung Tâm
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Smart Inpatient Room Console -->
        <div class="tab-pane fade show active" id="tab-inpatient" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Thiết Lập Môi Trường Buồng Bệnh IoT</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.smart-inpatient-ward-facility.console') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Số Phòng / Buồng</label>
                                    <input type="text" name="room_number" class="form-control form-control-sm" placeholder="VD: VIP-801, PRES-1002" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân</label>
                                    <select name="patient_id" class="form-select form-select-sm">
                                        <option value="">-- Chọn bệnh nhân lưu trú --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân Nhập Nhanh</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Nguyễn Văn A" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Hạng Phòng</label>
                                        <select name="room_class" class="form-select form-select-sm" required>
                                            <option value="vip_suite">VIP Suite</option>
                                            <option value="presidential_suite">Tổng Thống Presidential</option>
                                            <option value="standard_single">Đơn Tiêu Chuẩn</option>
                                            <option value="double_isolation">Cách Ly Kép</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ (°C)</label>
                                        <input type="number" step="0.5" name="temperature_celsius" class="form-control form-control-sm" value="24.0" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Ánh Sáng Sinh Học</label>
                                        <select name="circadian_lighting_mode" class="form-select form-select-sm" required>
                                            <option value="daylight_focus">Ánh sáng ngày tập trung</option>
                                            <option value="morning_energize">Bình minh sảng khoái</option>
                                            <option value="evening_relax">Hoàng hôn thư giãn</option>
                                            <option value="night_sleep">Chế độ ngủ đêm</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạng Thái Rèm</label>
                                        <select name="curtain_status" class="form-select form-select-sm" required>
                                            <option value="open">Mở 100%</option>
                                            <option value="half_closed">Khép 50%</option>
                                            <option value="fully_closed">Đóng kín</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Chế Độ Dinh Dưỡng Bệnh Lý</label>
                                    <input type="text" name="dietary_order" class="form-control form-control-sm" value="Chế độ tim mạch, giảm muối <3g/ngày" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Trạng Thái IoT Hub Đầu Giường</label>
                                    <select name="iot_device_status" class="form-select form-select-sm" required>
                                        <option value="online">Online - Kết nối ổn định</option>
                                        <option value="offline">Offline - Mất tín hiệu</option>
                                        <option value="maintenance">Đang bảo trì</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Lưu Cấu Hình Buồng Bệnh IoT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-display me-2 text-primary"></i>Giám Sát Buồng Bệnh & Chuông Gọi Điều Dưỡng</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Số Phòng</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Môi Trường IoT</th>
                                            <th>Chuông Y Tá</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_consoles'] as $console)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $console->room_number }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $console->patient_name }}</div>
                                                    <span class="badge bg-light text-dark border">{{ $console->room_class }}</span>
                                                </td>
                                                <td>
                                                    <div><i class="bi bi-thermometer-half text-danger"></i> {{ $console->temperature_celsius }}°C</div>
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $console->circadian_lighting_mode }}</span>
                                                </td>
                                                <td>
                                                    @if($console->nurse_call_status === 'pending')
                                                        <span class="badge bg-danger text-white animate__animated animate__flash animate__infinite">
                                                            <i class="bi bi-bell-fill me-1"></i> {{ $console->nurse_call_type }}
                                                        </span>
                                                        <div class="text-danger small mt-1">Đau: {{ $console->patient_pain_score }}/10</div>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">Không có cuộc gọi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('reception.smart-inpatient-ward-facility.nurse-call', $console->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="nurse_call_type" value="emergency_urgent">
                                                        <input type="hidden" name="patient_pain_score" value="8">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Test gọi y tá khẩn cấp">
                                                            <i class="bi bi-bell"></i> Gọi Y Tá
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu buồng bệnh nội trú thông minh.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Dynamic OPD Room Allocator -->
        <div class="tab-pane fade" id="tab-opd" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-info"></i>Phân Bổ Phòng Khám & Đồng Bộ Biển E-Paper</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.smart-inpatient-ward-facility.opd-allocator') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Khám</label>
                                    <input type="text" name="room_code" class="form-control form-control-sm" placeholder="VD: P-102, P-205" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Chuyên Khoa</label>
                                    <input type="text" name="department_name" class="form-control form-control-sm" placeholder="Khoa Tim Mạch, Khoa Nhi..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bác Sĩ Trực Khám</label>
                                    <input type="text" name="assigned_doctor_name" class="form-control form-control-sm" placeholder="BS. CKI Nguyễn Văn A" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Học Hàm / Chuyên Môn Bác Sĩ</label>
                                    <input type="text" name="doctor_specialty" class="form-control form-control-sm" placeholder="Chuyên gia Can thiệp Tim Mạch" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Ca Khám</label>
                                        <select name="shift_session" class="form-select form-select-sm" required>
                                            <option value="morning_shift">Ca Sáng (07h00 - 11h30)</option>
                                            <option value="afternoon_shift">Ca Chiều (13h00 - 16h30)</option>
                                            <option value="night_shift">Ca Ngoài Giờ / Đêm</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạng Thái E-Paper</label>
                                        <select name="epaper_display_status" class="form-select form-select-sm" required>
                                            <option value="active_consulting">Đang Khám</option>
                                            <option value="paused_break">Nghỉ Giải Lao</option>
                                            <option value="in_emergency_meeting">Hội Chẩn Đột Xuất</option>
                                            <option value="closed">Đóng Cửa Phòng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Số Hiện Tại</label>
                                        <input type="number" name="current_queue_number" class="form-control form-control-sm" value="12" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Số Kế Tiếp</label>
                                        <input type="number" name="next_queue_number" class="form-control form-control-sm" value="13" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Đang Chờ</label>
                                        <input type="number" name="waiting_patient_count" class="form-control form-control-sm" value="5" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Thời Gian Khám TB (Phút)</label>
                                    <input type="number" name="average_consult_time_min" class="form-control form-control-sm" value="15" required>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-broadcast me-1"></i> Phân Bổ Phòng & Đồng Bộ E-Paper
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tv me-2 text-info"></i>Trạng Thái Biển Tên Ngoài Cửa Phòng Khám</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng</th>
                                            <th>Khoa & Bác Sĩ</th>
                                            <th>Hàng Đợi</th>
                                            <th>Biển E-Paper</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_opd_allocators'] as $alloc)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $alloc->room_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $alloc->assigned_doctor_name }}</div>
                                                    <span class="text-muted small">{{ $alloc->department_name }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary text-white">Đang khám: #{{ $alloc->current_queue_number }}</span>
                                                    <div class="text-muted small mt-1">Kế tiếp: #{{ $alloc->next_queue_number }} (Còn {{ $alloc->waiting_patient_count }} người)</div>
                                                </td>
                                                <td>
                                                    @if($alloc->epaper_display_status === 'active_consulting')
                                                        <span class="badge bg-success-subtle text-success border border-success">Đang Khám</span>
                                                    @elseif($alloc->epaper_display_status === 'in_emergency_meeting')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger">Hội Chẩn Gấp</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">{{ $alloc->epaper_display_status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('reception.smart-inpatient-ward-facility.opd-advance', $alloc->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info btn-sm py-0 px-2" title="Gọi bệnh nhân kế tiếp">
                                                            <i class="bi bi-chevron-double-right"></i> Gọi Tiếp
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có phòng khám nào được phân bổ.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Terminal Room Disinfection UV-C -->
        <div class="tab-pane fade" id="tab-disinfection" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-2 text-success"></i>Lệnh Khử Trùng Buồng Sau Xuất Viện</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.smart-inpatient-ward-facility.disinfection') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Số Phòng Cần Làm Sạch</label>
                                    <input type="text" name="room_number" class="form-control form-control-sm" placeholder="VD: 504, 712, OR-03" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Khoa / Đơn Vị</label>
                                    <input type="text" name="ward_department" class="form-control form-control-sm" placeholder="Khoa Ngoại Thần Kinh" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân Vừa Ra Viện</label>
                                    <input type="text" name="discharge_patient_name" class="form-control form-control-sm" placeholder="Trần Thị B" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Sự Kiện Kích Hoạt</label>
                                        <select name="cleaning_trigger_event" class="form-select form-select-sm" required>
                                            <option value="patient_discharge">Xuất Viện Trả Phòng</option>
                                            <option value="post_operative">Hậu Phẫu Hoàn Tất</option>
                                            <option value="isolation_contact_precaution">Cách Ly Phòng Lây Nhiễm</option>
                                            <option value="scheduled_deep_clean">Làm Sạch Định Kỳ Chuyên Sâu</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phương Pháp</label>
                                        <select name="disinfection_method" class="form-select form-select-sm" required>
                                            <option value="uvc_robot_autonomous">Robot UV-C Tự Hành</option>
                                            <option value="hydrogen_peroxide_vhp">Khí Dung H2O2 VHP</option>
                                            <option value="manual_quaternary_wipe">Lau Cồn & Quaternary</option>
                                            <option value="hybrid_dual_cycle">Hỗn Hợp Kép (Lau + UV-C)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Robot UV-C</label>
                                        <input type="text" name="disinfection_robot_id" class="form-control form-control-sm" value="UVC-BOT-02">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Lượng (Phút)</label>
                                        <input type="number" name="run_duration_minutes" class="form-control form-control-sm" value="30" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhân Viên Vệ Sinh</label>
                                        <input type="text" name="cleaning_staff_name" class="form-control form-control-sm" placeholder="Lê Thị C" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Chỉ Số ATP (RLU)</label>
                                        <input type="number" name="atp_bioluminescence_rlu" class="form-control form-control-sm" value="14" required title="RLU <= 30 là vô khuẩn tuyệt đối">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Báo Cáo Khử Trùng & Quét Mã Sạch
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-success"></i>Nhật Ký Làm Sạch Buồng & Kiểm Định ATP Vi Sinh</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng</th>
                                            <th>Khoa</th>
                                            <th>Phương Pháp</th>
                                            <th>Chỉ Số ATP</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_disinfections'] as $dis)
                                            <tr>
                                                <td class="fw-bold text-success">{{ $dis->room_number }}</td>
                                                <td>{{ $dis->ward_department }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $dis->disinfection_method }}</span>
                                                    <div class="text-muted small mt-1">Robot: {{ $dis->disinfection_robot_id ?? 'N/A' }}</div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold {{ $dis->atp_bioluminescence_rlu <= 30 ? 'text-success' : 'text-danger' }}">
                                                        {{ $dis->atp_bioluminescence_rlu }} RLU
                                                    </span>
                                                    <div class="text-muted small">(Chuẩn &le; 30)</div>
                                                </td>
                                                <td>
                                                    @if($dis->room_ready_status === 'certified_clean_ready')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-check-circle me-1"></i> Sẵn Sàng Đón Khách
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">Đang Cách Ly Chờ Khử</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bản ghi khử trùng buồng sau xuất viện.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Smart Delivery Suite LDRP -->
        <div class="tab-pane fade" id="tab-ldrp" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart me-2 text-danger"></i>Thiết Lập Buồng Sinh Gia Đình LDRP 4-in-1</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.smart-inpatient-ward-facility.ldrp') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Sinh LDRP</label>
                                    <input type="text" name="suite_code" class="form-control form-control-sm" placeholder="VD: LDRP-01, LDRP-02" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Sản Phụ</label>
                                    <select name="maternal_patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn sản phụ --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Sản Phụ</label>
                                    <input type="text" name="mother_name" class="form-control form-control-sm" placeholder="Nguyễn Thị D" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tuổi Thai (Tuần)</label>
                                        <input type="number" step="0.1" name="gestational_weeks" class="form-control form-control-sm" value="39.2" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Giai Đoạn Sinh</label>
                                        <select name="current_stage" class="form-select form-select-sm" required>
                                            <option value="stage1_labor">Giai đoạn 1: Chuyển dạ</option>
                                            <option value="stage2_delivery">Giai đoạn 2: Rặn sinh</option>
                                            <option value="stage3_placenta">Giai đoạn 3: Sổ nhau</option>
                                            <option value="stage4_recovery_postpartum">Giai đoạn 4: Hậu sản</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Bồn Sinh Nước</label>
                                        <select name="water_birth_tub_active" class="form-select form-select-sm">
                                            <option value="0">Không kích hoạt</option>
                                            <option value="1">Có bồn ngâm giảm đau</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Sưởi Ấm Sơ Sinh</label>
                                        <select name="infant_resuscitaire_warmed" class="form-select form-select-sm">
                                            <option value="1">Bật sưởi ấm sẵn sàng</option>
                                            <option value="0">Tắt</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phương Pháp Giảm Đau</label>
                                        <select name="pain_management_type" class="form-select form-select-sm" required>
                                            <option value="epidural_analgesia">Gây tê ngoài màng cứng</option>
                                            <option value="water_immersion">Ngâm bồn nước ấm</option>
                                            <option value="nitrous_oxide_gas">Khí cười Nitrous Oxide</option>
                                            <option value="breathing_relaxation">Thư giãn nhịp thở Lamaze</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tim Thai (BPM)</label>
                                        <input type="number" name="fetal_heart_rate_bpm" class="form-control form-control-sm" value="142" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Điểm Tiện Nghi Sản Phụ (0-10)</label>
                                    <input type="number" name="maternal_comfort_score" class="form-control form-control-sm" value="9" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-heart-pulse me-1"></i> Khởi Tạo Buồng Sinh Gia Đình LDRP
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-danger"></i>Theo Dõi Sản Phụ Tại Buồng LDRP</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Phòng</th>
                                            <th>Sản Phụ</th>
                                            <th>Giai Đoạn</th>
                                            <th>Tim Thai & Giảm Đau</th>
                                            <th>Tiện Nghi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ldrp_suites'] as $ldrp)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $ldrp->suite_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $ldrp->mother_name }}</div>
                                                    <span class="text-muted small">Thai {{ $ldrp->gestational_weeks }} tuần</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                                        {{ $ldrp->current_stage }}
                                                    </span>
                                                    <div class="text-muted small mt-1">Giường: {{ $ldrp->bed_transformation_mode }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><i class="bi bi-heart-fill text-danger me-1"></i> {{ $ldrp->fetal_heart_rate_bpm }} bpm</div>
                                                    <span class="badge bg-light text-dark border">{{ $ldrp->pain_management_type }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success-subtle text-success">{{ $ldrp->maternal_comfort_score }}/10</span>
                                                    @if($ldrp->water_birth_tub_active)
                                                        <span class="badge bg-info-subtle text-info"><i class="bi bi-water"></i> Bồn Nước</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có sản phụ nào trong buồng sinh LDRP.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Smart Nurse Station Hub -->
        <div class="tab-pane fade" id="tab-nursehub" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-fill me-2 text-primary"></i>Cập Nhật Bảng Chỉ Huy Trạm Điều Dưỡng</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.smart-inpatient-ward-facility.nurse-hub') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Trạm / Khoa</label>
                                    <input type="text" name="ward_code" class="form-control form-control-sm" placeholder="VD: WARD-ICU-3F, WARD-CARDIO-6F" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tên Khoa Nội Trú</label>
                                    <input type="text" name="ward_name" class="form-control form-control-sm" placeholder="Khoa Hồi Sức Tích Cực Nội" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tổng Số Giường</label>
                                        <input type="number" name="total_active_beds" class="form-control form-control-sm" value="30" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Giường Đang Nằm</label>
                                        <input type="number" name="occupied_beds" class="form-control form-control-sm" value="25" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Cảnh Báo Nặng</label>
                                        <input type="number" name="critical_alert_count" class="form-control form-control-sm" value="1" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Chuông Chờ</label>
                                        <input type="number" name="pending_nurse_calls" class="form-control form-control-sm" value="2" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Dịch Sắp Hết</label>
                                        <input type="number" name="infusion_near_empty_count" class="form-control form-control-sm" value="3" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Điều Dưỡng Trưởng Trực Ca</label>
                                    <input type="text" name="on_duty_head_nurse" class="form-control form-control-sm" placeholder="ĐD. Đỗ Thị E" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Trạng Thái Sẵn Sàng Code Blue</label>
                                    <select name="code_blue_standby_status" class="form-select form-select-sm" required>
                                        <option value="green_ready">Xanh - Sẵn sàng 100%</option>
                                        <option value="yellow_standby">Vàng - Đang theo dõi sát</option>
                                        <option value="red_active_resuscitation">Đỏ - Đang hồi sinh tim phổi</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-save me-1"></i> Lưu Trạm Điều Dưỡng Trung Tâm
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-hdd-network me-2 text-primary"></i>Bảng Tổng Chỉ Huy Khoa Nội Trú</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Khoa</th>
                                            <th>Tên Khoa & Điều Dưỡng Trưởng</th>
                                            <th>Tỷ Lệ Giường</th>
                                            <th>Báo Động Khoa</th>
                                            <th>Code Blue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_nurse_stations'] as $hub)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $hub->ward_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $hub->ward_name }}</div>
                                                    <span class="text-muted small">Trưởng ca: {{ $hub->on_duty_head_nurse }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary text-white">{{ $hub->occupied_beds }} / {{ $hub->total_active_beds }} giường</span>
                                                    <div class="text-muted small mt-1">Lấp đầy: {{ $hub->occupancy_rate }}%</div>
                                                </td>
                                                <td>
                                                    <div><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> {{ $hub->critical_alert_count }} ca nặng</div>
                                                    <div class="text-muted small"><i class="bi bi-bell text-warning"></i> {{ $hub->pending_nurse_calls }} chuông • <i class="bi bi-droplet-fill text-info"></i> {{ $hub->infusion_near_empty_count }} dịch</div>
                                                </td>
                                                <td>
                                                    @if($hub->code_blue_standby_status === 'green_ready')
                                                        <span class="badge bg-success-subtle text-success border border-success">Sẵn Sàng</span>
                                                    @else
                                                        <span class="badge bg-danger text-white animate__animated animate__pulse animate__infinite">Báo Động</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có trạm điều dưỡng nào được cấu hình.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
