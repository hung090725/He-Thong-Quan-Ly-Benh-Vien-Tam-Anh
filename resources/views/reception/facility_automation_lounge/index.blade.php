@extends('layouts.admin')

@section('title', 'Tự Động Hóa Tòa Nhà Bệnh Viện, An Ninh Cửa Liên Động & Ghế Truyền Hóa Chất VIP')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-cpu-fill text-success me-2"></i>Tự Động Hóa Tòa Nhà Bệnh Viện, An Ninh Cửa Liên Động & Ghế Truyền Ban Ngày
            </h4>
            <p class="text-muted small mb-0">
                Wave 9 • Quản trị phòng rác lây nhiễm & máy rửa bô vịt Biohazard Sluice, Kiểm soát ra vào FaceID/Airlock Interlock, Giám sát chất lượng không khí IAQ & Điều hòa HVAC, Kho lưu trữ bệnh án giấy FM-200 & Ghế truyền hóa chất ban ngày VIP
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                <i class="bi bi-fan me-1"></i> Smart Building Automation & Lounge Active
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
                            <span class="text-muted small">Phòng Xử Lý Rác Sluice Đạt Chuẩn</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['compliant_sluice_rooms'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-trash3-fill fs-5"></i>
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
                            <span class="text-muted small">Lượt Mở Cửa Hợp Lệ</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['granted_access_logs'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-person-badge fs-5"></i>
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
                            <span class="text-muted small">Cảm Biến Khí IAQ Xuất Sắc</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['excellent_iaq_zones'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-wind fs-5"></i>
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
                            <span class="text-muted small">Kho Bệnh Án Sẵn Sàng FM-200</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['armed_fire_systems'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-fire fs-5"></i>
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
                            <span class="text-muted small">Ghế Truyền Đang Hoạt Động</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['active_infusions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-sluice-tab" data-bs-toggle="pill" data-bs-target="#tab-sluice" type="button" role="tab">
                <i class="bi bi-trash me-1"></i> 1. Xử Lý Đồ Bẩn Sluice
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-interlock-tab" data-bs-toggle="pill" data-bs-target="#tab-interlock" type="button" role="tab">
                <i class="bi bi-door-closed me-1"></i> 2. Cửa Liên Động FaceID
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-iaq-tab" data-bs-toggle="pill" data-bs-target="#tab-iaq" type="button" role="tab">
                <i class="bi bi-wind me-1"></i> 3. Khí Sạch IAQ & HVAC
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-archive-tab" data-bs-toggle="pill" data-bs-target="#tab-archive" type="button" role="tab">
                <i class="bi bi-archive me-1"></i> 4. Bệnh Án Giấy FM-200
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-infusion-tab" data-bs-toggle="pill" data-bs-target="#tab-infusion" type="button" role="tab">
                <i class="bi bi-cup-hot me-1"></i> 5. Ghế Truyền Hóa Chất VIP
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Biohazard Dirty Utility Sluice -->
        <div class="tab-pane fade show active" id="tab-sluice" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-danger"></i>Ghi Nhận Phòng Xử Lý Đồ Bẩn & Rác Lây Nhiễm</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.facility-automation-lounge.biohazard-sluice') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Xử Lý Sluice</label>
                                    <input type="text" name="sluice_room_code" class="form-control form-control-sm" placeholder="VD: SLUICE-4F-SOUTH, SLUICE-ICU" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ Máy Rửa Bô (°C)</label>
                                        <input type="number" step="0.5" name="bedpan_washer_temp_celsius" class="form-control form-control-sm" value="88.5" required title="Chuẩn tiệt trùng nhiệt >= 85°C">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Chu Trình Máy Rửa</label>
                                        <select name="washer_disinfection_cycle" class="form-select form-select-sm" required>
                                            <option value="cycle_completed">Hoàn tất khử trùng nhiệt</option>
                                            <option value="thermal_disinfecting">Đang phun hơi nóng</option>
                                            <option value="door_locked_cooling">Khóa cửa chờ làm nguội</option>
                                            <option value="idle">Nghỉ chờ tải</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Khối Lượng Rác Vàng (Kg)</label>
                                        <input type="number" step="0.1" name="yellow_bag_infectious_kg" class="form-control form-control-sm" value="12.5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Hộp Vật Sắc Nhọn (%)</label>
                                        <input type="number" name="sharps_container_full_pct" class="form-control form-control-sm" value="45" required title="Không được đầy quá 75%">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Quạt Thông Gió Hút Mùi Cưỡng Bức</label>
                                    <select name="air_deodorizer_fan_status" class="form-select form-select-sm" required>
                                        <option value="exhaust_running_nominal">Hoạt động bình thường</option>
                                        <option value="booster_active">Tăng tốc hút mùi tức thì</option>
                                        <option value="filter_clogged">Màng lọc carbon bị nghẽn</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Lưu Kiểm Soát Nhiễm Khuẩn Sluice
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-danger"></i>Nhật Ký Kiểm Định Phòng Xử Lý Đồ Bẩn Sluice</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng Sluice</th>
                                            <th>Máy Rửa Bô</th>
                                            <th>Rác Vàng & Sắc Nhọn</th>
                                            <th>Thông Gió</th>
                                            <th>Đánh Giá An Toàn</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_sluice'] as $sl)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $sl->sluice_room_code }}</td>
                                                <td>
                                                    <span class="fw-bold {{ $sl->bedpan_washer_temp_celsius >= 85.0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $sl->bedpan_washer_temp_celsius }}°C
                                                    </span>
                                                    <div class="text-muted small">{{ $sl->washer_disinfection_cycle }}</div>
                                                </td>
                                                <td>
                                                    <div>Rác lây nhiễm: {{ $sl->yellow_bag_infectious_kg }} kg</div>
                                                    <span class="badge {{ $sl->sharps_container_full_pct <= 75 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                        Hộp kim tiêm: {{ $sl->sharps_container_full_pct }}%
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $sl->air_deodorizer_fan_status }}</span></td>
                                                <td>
                                                    @if($sl->compliance_audit_status === 'fully_compliant')
                                                        <span class="badge bg-success-subtle text-success border border-success">Chuẩn Kiểm Soát Nhiễm Khuẩn</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">{{ $sl->compliance_audit_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu kiểm định phòng rác sluice.</td>
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

        <!-- 2. Sterile Area Access Interlock FaceID -->
        <div class="tab-pane fade" id="tab-interlock" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-check me-2 text-primary"></i>Xác Thực FaceID & Khóa Cửa Liên Động</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.facility-automation-lounge.access-interlock') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tên Điểm Cửa Kiểm Soát</label>
                                    <input type="text" name="access_point_name" class="form-control form-control-sm" placeholder="VD: Cửa Buồng Đệm Phòng Mổ OR-01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Khu Vực An Ninh</label>
                                    <select name="zone_classification" class="form-select form-select-sm" required>
                                        <option value="operating_theater_sterile">Khu Phòng Mổ Vô Khuẩn Tuyệt Đối</option>
                                        <option value="narcotics_vault">Kho Thuốc Độc Gây Nghiện & Hướng Thần</option>
                                        <option value="blood_bank_core">Lõi Ngân Hàng Máu & Chế Phẩm</option>
                                        <option value="bsl3_virology_lab">Phòng Xét Nghiệm An Toàn Sinh Học Cấp III</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Thẻ RFID Nhân Viên</label>
                                        <input type="text" name="staff_badge_rfid" class="form-control form-control-sm" placeholder="RFID-DOC-9921" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Họ Tên Nhân Viên</label>
                                        <input type="text" name="staff_name" class="form-control form-control-sm" placeholder="BS. Vũ Văn H" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Chức Danh / Vị Trí Công Tác</label>
                                    <input type="text" name="staff_role" class="form-control form-control-sm" placeholder="Phẫu Thuật Viên Trưởng / Dược Sĩ Quản Kho" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Khớp Khuôn Mặt FaceID</label>
                                        <select name="biometric_face_matched" class="form-select form-select-sm">
                                            <option value="1">Khuôn mặt khớp 100%</option>
                                            <option value="0">Không khớp khuôn mặt</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cửa Ngoài Buồng Đệm</label>
                                        <select name="interlock_outer_door_closed" class="form-select form-select-sm">
                                            <option value="1">Cửa ngoài đã đóng chặt</option>
                                            <option value="0">Cửa ngoài đang mở hé!</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Phân Quyền Chức Danh</label>
                                    <select name="is_authorized_role" class="form-select form-select-sm">
                                        <option value="1">Được cấp quyền vào khu vực này</option>
                                        <option value="0">Không có quyền ra vào</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-door-open me-1"></i> Quét Thẻ & Mở Cửa Liên Động
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Nhật Ký Quẹt Thẻ & Khóa Liên Động Cửa 24/7</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Điểm Ra Vào</th>
                                            <th>Nhân Viên</th>
                                            <th>Khu Vực</th>
                                            <th>FaceID & Cửa Ngoài</th>
                                            <th>Quyết Định</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_access_interlocks'] as $access)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $access->access_point_name }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $access->staff_name }}</div>
                                                    <span class="text-muted small">{{ $access->staff_role }}</span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $access->zone_classification }}</span></td>
                                                <td>
                                                    @if($access->biometric_face_matched)
                                                        <span class="badge bg-success-subtle text-success">FaceID Khớp</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">Sai Mặt</span>
                                                    @endif
                                                    <div class="text-muted small mt-1">Cửa ngoài: {{ $access->interlock_outer_door_closed ? 'Đóng kín' : 'Mở' }}</div>
                                                </td>
                                                <td>
                                                    @if($access->access_decision === 'granted_normal')
                                                        <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i> Cho Phép Vào</span>
                                                    @elseif($access->access_decision === 'denied_airlock_breached')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Vi Phạm Liên Động</span>
                                                    @else
                                                        <span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i> Từ Chối</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có nhật ký quẹt thẻ kiểm soát cửa.</td>
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

        <!-- 3. Indoor Air Quality IAQ & HVAC -->
        <div class="tab-pane fade" id="tab-iaq" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wind me-2 text-info"></i>Đo Lường Chất Lượng Không Khí & Điều Hòa HVAC</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.facility-automation-lounge.iaq-hvac') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Cảm Biến / Vị Trí Phòng</label>
                                    <input type="text" name="room_sensor_code" class="form-control form-control-sm" placeholder="VD: IAQ-ICU-01, IAQ-OR-03" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Loại Phòng</label>
                                    <select name="room_type" class="form-select form-select-sm" required>
                                        <option value="intensive_care_unit">Khoa Hồi Sức Tích Cực ICU</option>
                                        <option value="operating_room">Phòng Mổ Phẫu Thuật</option>
                                        <option value="clean_pharmacy">Phòng Sạch Pha Chế Dược</option>
                                        <option value="general_inpatient_suite">Buồng Nội Trú Thông Thường</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Bụi PM2.5 (μg/m³)</label>
                                        <input type="number" step="0.1" name="pm2_5_ug_m3" class="form-control form-control-sm" value="6.5" required title="Chuẩn WHO: < 15 ug/m³">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Khí CO2 (ppm)</label>
                                        <input type="number" name="co2_ppm" class="form-control form-control-sm" value="520" required title="Chuẩn: < 800 ppm">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">TVOC (ppb)</label>
                                        <input type="number" name="tvoc_ppb" class="form-control form-control-sm" value="45" required title="Chuẩn: < 220 ppb">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ (°C)</label>
                                        <input type="number" step="0.5" name="temperature_celsius" class="form-control form-control-sm" value="22.5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Độ Ẩm (%)</label>
                                        <input type="number" step="0.5" name="relative_humidity_pct" class="form-control form-control-sm" value="52.0" required title="Chuẩn: 45 - 60%">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Độ Mở Van Gió Tươi HVAC (%)</label>
                                    <input type="number" name="hvac_damper_fresh_air_pct" class="form-control form-control-sm" value="35" required>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-fan me-1"></i> Cập Nhật Chỉ Số IAQ & Tự Động Hóa HVAC
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart me-2 text-info"></i>Giám Sát Môi Trường Không Khí IAQ Trong Bệnh Viện</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Vị Trí</th>
                                            <th>PM2.5 & CO2</th>
                                            <th>Nhiệt Ẩm Độ</th>
                                            <th>Van Gió Tươi</th>
                                            <th>Chất Lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_iaq_hvac'] as $iaq)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $iaq->room_sensor_code }}</td>
                                                <td>
                                                    <div>PM2.5: <span class="fw-bold text-success">{{ $iaq->pm2_5_ug_m3 }} μg/m³</span></div>
                                                    <span class="text-muted small">CO2: {{ $iaq->co2_ppm }} ppm • TVOC: {{ $iaq->tvoc_ppb }} ppb</span>
                                                </td>
                                                <td>
                                                    <div>{{ $iaq->temperature_celsius }}°C</div>
                                                    <span class="text-muted small">Độ ẩm: {{ $iaq->relative_humidity_pct }}%</span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $iaq->hvac_damper_fresh_air_pct }}% Gió tươi</span></td>
                                                <td>
                                                    @if($iaq->iaq_index_rating === 'excellent_green')
                                                        <span class="badge bg-success-subtle text-success border border-success">Rất Trong Lành</span>
                                                    @elseif($iaq->iaq_index_rating === 'moderate_yellow')
                                                        <span class="badge bg-warning-subtle text-warning">Trung Bình</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Ô Nhiễm / Cần Xả Gió</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu cảm biến không khí IAQ.</td>
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

        <!-- 4. Medical Records Physical Archive & FM-200 -->
        <div class="tab-pane fade" id="tab-archive" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-archive me-2 text-danger"></i>Kiểm Tra Kho Lưu Trữ Bệnh Án Giấy & Khí Sạch FM-200</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.facility-automation-lounge.medical-archive') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Khoang Kệ Compactor Di Động</label>
                                    <input type="text" name="compactor_bay_code" class="form-control form-control-sm" placeholder="VD: BAY-MOB-01, BAY-MOB-08" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Khu Vực Bệnh Án</label>
                                        <input type="text" name="section_code" class="form-control form-control-sm" placeholder="SEC-SURGERY-2020" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Lượng Bệnh Án Giấy</label>
                                        <input type="number" name="total_paper_charts_stored" class="form-control form-control-sm" value="3500" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ Kho (°C)</label>
                                        <input type="number" step="0.5" name="temperature_celsius" class="form-control form-control-sm" value="21.0" required title="Chuẩn bảo quản giấy 20-22°C">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Độ Ẩm Kho (%)</label>
                                        <input type="number" step="0.5" name="relative_humidity_pct" class="form-control form-control-sm" value="48.0" required title="Chuẩn chống ẩm mốc 45-55%">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Đầu Báo Khói Quang Học</label>
                                        <select name="smoke_detector_active" class="form-select form-select-sm">
                                            <option value="0">Bình thường (Không có khói)</option>
                                            <option value="1">Báo động phát hiện khói!</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Áp Suất Bình FM-200 (Bar)</label>
                                        <input type="number" step="0.5" name="fm200_cylinder_pressure_bar" class="form-control form-control-sm" value="25.0" required title="Chuẩn >= 24 bar">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Trạng Thái Định Vị RFID Hồ Sơ</label>
                                    <select name="rfid_chart_locator_status" class="form-select form-select-sm" required>
                                        <option value="indexed_online">Đã số hóa vị trí trên kệ</option>
                                        <option value="chart_checked_out">Đang mượn phục vụ nghiên cứu</option>
                                        <option value="inventory_audit">Đang kiểm kê định kỳ</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Lưu Kiểm Tra Kho Lưu Trữ FM-200
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-building me-2 text-danger"></i>Trạng Thái Kệ Compactor & Hệ Thống Chữa Cháy Khí FM-200</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Kệ</th>
                                            <th>Khu Vực & Số Lượng</th>
                                            <th>Môi Trường Bảo Quản</th>
                                            <th>Áp Suất FM-200</th>
                                            <th>Sẵn Sàng Dập Lửa</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_archives'] as $arc)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $arc->compactor_bay_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $arc->section_code }}</div>
                                                    <span class="badge bg-light text-dark border">{{ number_format($arc->total_paper_charts_stored) }} bệnh án</span>
                                                </td>
                                                <td>
                                                    <div>{{ $arc->temperature_celsius }}°C • Ẩm: {{ $arc->relative_humidity_pct }}%</div>
                                                    <span class="text-muted small">RFID: {{ $arc->rfid_chart_locator_status }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold {{ $arc->fm200_cylinder_pressure_bar >= 24.0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $arc->fm200_cylinder_pressure_bar }} bar
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($arc->fire_suppression_status === 'armed_standby')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-shield-check me-1"></i> FM-200 Sẵn Sàng
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger text-white">{{ $arc->fire_suppression_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu kho lưu trữ bệnh án giấy.</td>
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

        <!-- 5. Day-Care Infusion & Chemotherapy Lounge -->
        <div class="tab-pane fade" id="tab-infusion" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cup-hot me-2 text-warning"></i>Tiếp Nhận Bệnh Nhân Vào Ghế Truyền Hóa Chất VIP</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.facility-automation-lounge.daycare-infusion') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Ghế Ngả Lưng VIP</label>
                                    <input type="text" name="recliner_bay_code" class="form-control form-control-sm" placeholder="VD: BAY-VIP-01, BAY-VIP-05" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân</label>
                                    <select name="patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bệnh nhân truyền trong ngày --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Hoàng Thị K" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phác Đồ Truyền</label>
                                        <select name="infusion_regimen_type" class="form-select form-select-sm" required>
                                            <option value="chemotherapy_paclitaxel">Hóa Trị Paclitaxel Ung Thư</option>
                                            <option value="biologic_rituximab">Kháng Thể Đơn Dòng Rituximab</option>
                                            <option value="intravenous_iron_sucrose">Truyền Sắt Tĩnh Mạch Thiếu Máu</option>
                                            <option value="post_op_hydration_antibiotic">Bù Dịch & Kháng Sinh Hậu Phẫu</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Lượng (Giờ)</label>
                                        <input type="number" step="0.5" name="scheduled_duration_hours" class="form-control form-control-sm" value="3.0" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tốc Độ Máy Truyền (ml/giờ)</label>
                                    <input type="number" name="infusion_rate_ml_hr" class="form-control form-control-sm" value="125" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Massage & Sưởi Ấm</label>
                                        <select name="recliner_massage_heat_active" class="form-select form-select-sm">
                                            <option value="1">Bật sưởi ấm & massage nhẹ</option>
                                            <option value="0">Tắt</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tablet Giải Trí Cá Nhân</label>
                                        <select name="personal_tablet_connected" class="form-select form-select-sm">
                                            <option value="1">Kết nối Tablet gọi y tá</option>
                                            <option value="0">Tắt</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-check-circle me-1"></i> Bắt Đầu Buổi Truyền Ban Ngày
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-display me-2 text-warning"></i>Theo Dõi Bệnh Nhân Tại Ghế Truyền Hóa Chất VIP</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Ghế</th>
                                            <th>Bệnh Nhân & Phác Đồ</th>
                                            <th>Tốc Độ Truyền</th>
                                            <th>Trạng Thái & Tiện Nghi</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_daycare_infusions'] as $bay)
                                            <tr>
                                                <td class="fw-bold text-warning">{{ $bay->recliner_bay_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $bay->patient_name }}</div>
                                                    <span class="badge bg-light text-dark border">{{ $bay->infusion_regimen_type }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary">{{ $bay->infusion_rate_ml_hr }} ml/h</div>
                                                    <span class="text-muted small">Thời lượng: {{ $bay->scheduled_duration_hours }}h</span>
                                                </td>
                                                <td>
                                                    @if($bay->nurse_call_urgency === 'anaphylaxis_stat_call')
                                                        <span class="badge bg-danger text-white animate__animated animate__flash animate__infinite">
                                                            <i class="bi bi-exclamation-octagon-fill me-1"></i> BÁO ĐỘNG SỐC PHẢN VỆ
                                                        </span>
                                                    @elseif($bay->session_status === 'infusion_completed')
                                                        <span class="badge bg-success-subtle text-success border border-success">Đã Hoàn Tất</span>
                                                    @else
                                                        <span class="badge bg-info-subtle text-info border border-info">Đang Truyền Ổn Định</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('reception.facility-automation-lounge.infusion-emergency', $bay->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-1" title="Báo động sốc phản vệ khẩn cấp & ngắt bơm">
                                                                <i class="bi bi-exclamation-triangle"></i> Sốc
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('reception.facility-automation-lounge.infusion-complete', $bay->id) }}" method="POST" class="d-inline ms-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm py-0 px-1" title="Hoàn tất buổi truyền">
                                                                <i class="bi bi-check-lg"></i> Xong
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bệnh nhân nào trong phòng truyền ban ngày.</td>
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
