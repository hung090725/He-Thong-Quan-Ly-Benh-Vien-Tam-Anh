@extends('layouts.admin')

@section('title', 'Định Vị Thiết Bị BLE, Bảo Trì CMMS, Bãi Xe Cấp Cứu, Bếp HACCP & Đồ Vải RFID')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-geo-alt-fill text-warning me-2"></i>Định Vị Thiết Bị BLE, Bảo Trì CMMS, Bãi Xe Cấp Cứu, Bếp HACCP & Đồ Vải RFID
            </h4>
            <p class="text-muted small mb-0">
                Wave 10 • Giám sát tài sản y sinh lưu động BLE/UWB, Kỹ thuật y sinh CMMS & An toàn điện rò IEC 62353, Barie bãi xe nhận diện biển số cấp cứu ALPR, Bếp ăn bệnh viện dinh dưỡng HACCP/ISO 22000 & Đồ vải gắn chip giặt RFID
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill">
                <i class="bi bi-tools me-1"></i> Asset Engineering & Quality Assurance Active
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
                            <span class="text-muted small">Thiết Bị BLE Sẵn Sàng</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['ready_ble_assets'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-bluetooth fs-5"></i>
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
                            <span class="text-muted small">Máy Móc CMMS Đạt An Toàn</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['certified_cmms_devices'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-wrench-adjustable fs-5"></i>
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
                            <span class="text-muted small">Xe Cấp Cứu Mở Làn Ưu Tiên</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['fast_track_ambulances'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-hospital fs-5"></i>
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
                            <span class="text-muted small">Lô Cơm Bệnh Lý Chuẩn HACCP</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['haccp_compliant_batches'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-egg-fried fs-5"></i>
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
                            <span class="text-muted small">Đồ Vải Vô Trùng RFID</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['active_sterile_linens'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-tag-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-ble-tab" data-bs-toggle="pill" data-bs-target="#tab-ble" type="button" role="tab">
                <i class="bi bi-bluetooth me-1"></i> 1. Định Vị Thiết Bị BLE
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-cmms-tab" data-bs-toggle="pill" data-bs-target="#tab-cmms" type="button" role="tab">
                <i class="bi bi-wrench me-1"></i> 2. Kỹ Thuật Y Sinh CMMS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-gate-tab" data-bs-toggle="pill" data-bs-target="#tab-gate" type="button" role="tab">
                <i class="bi bi-car-front me-1"></i> 3. Bãi Xe Cấp Cứu ALPR
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-kitchen-tab" data-bs-toggle="pill" data-bs-target="#tab-kitchen" type="button" role="tab">
                <i class="bi bi-cup-straw me-1"></i> 4. Bếp Ăn Bệnh Viện HACCP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-linen-tab" data-bs-toggle="pill" data-bs-target="#tab-linen" type="button" role="tab">
                <i class="bi bi-tags me-1"></i> 5. Đồ Vải & Săng Mổ RFID
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Định Vị Thiết Bị BLE -->
        <div class="tab-pane fade show active" id="tab-ble" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Đăng Ký Định Vị Thiết Bị Y Tế BLE</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.asset-engineering-haccp-linen.asset-ble') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Thẻ Chip BLE Tag</label>
                                    <input type="text" name="asset_tag_ble" class="form-control form-control-sm" placeholder="VD: BLE-VENT-049, BLE-AED-012" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tên Thiết Bị Y Sinh</label>
                                    <input type="text" name="asset_name" class="form-control form-control-sm" placeholder="Máy Thở Cao Cấp Hamilton-C6" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phân Loại</label>
                                        <select name="equipment_category" class="form-select form-select-sm" required>
                                            <option value="mechanical_ventilator">Máy Thở Xâm Nhập ICU</option>
                                            <option value="defibrillator_aed">Máy Sốc Điện Phá Rung Tim</option>
                                            <option value="infusion_pump">Bơm Tiêm Điện Thông Minh</option>
                                            <option value="portable_ultrasound">Máy Siêu Âm Di Động</option>
                                            <option value="crash_cart">Xe Tiêm Cấp Cứu Crash Cart</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cường Độ Sóng (dBm)</label>
                                        <input type="number" name="rssi_signal_strength_dbm" class="form-control form-control-sm" value="-65" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Vị Trí Hiện Tại (Tầng / Phòng)</label>
                                    <input type="text" name="current_location_zone" class="form-control form-control-sm" value="Tầng 3 - Buồng Mổ OR-02" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Trạng Thái Thiết Bị</label>
                                    <select name="asset_status" class="form-select form-select-sm" required>
                                        <option value="available_ready">Sẵn Sàng Sử Dụng</option>
                                        <option value="in_use_patient">Đang Gắn Trên Bệnh Nhân</option>
                                        <option value="cleaning_decon">Đang Khử Khuẩn Làm Sạch</option>
                                        <option value="out_of_bounds_theft_alarm">Báo Động Ra Ngoài Khu Vực</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-broadcast me-1"></i> Lưu Định Vị Thiết Bị BLE
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-compass me-2 text-primary"></i>Bản Đồ Định Vị Tài Sản Y Tế Lưu Động</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã BLE</th>
                                            <th>Tên Thiết Bị</th>
                                            <th>Vị Trí Tầng / Phòng</th>
                                            <th>Sóng & Geofence</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ble_assets'] as $asset)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $asset->asset_tag_ble }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $asset->asset_name }}</div>
                                                    <span class="badge bg-light text-dark border">{{ $asset->equipment_category }}</span>
                                                </td>
                                                <td><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $asset->current_location_zone }}</td>
                                                <td>
                                                    <div>RSSI: {{ $asset->rssi_signal_strength_dbm }} dBm</div>
                                                    @if($asset->in_geofence_perimeter)
                                                        <span class="badge bg-success-subtle text-success">Trong Hàng Rào</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Vượt Rào!</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('reception.asset-engineering-haccp-linen.asset-alarm', $asset->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Kích hoạt báo động mất trộm">
                                                            <i class="bi bi-bell"></i> Báo Động
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có thiết bị nào gắn chip định vị BLE.</td>
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

        <!-- 2. Kỹ Thuật Y Sinh CMMS -->
        <div class="tab-pane fade" id="tab-cmms" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wrench me-2 text-primary"></i>Kiểm Định An Toàn Điện Rò IEC 62353 CMMS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.asset-engineering-haccp-linen.biomedical-cmms') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Số Seri Thiết Bị (SN)</label>
                                    <input type="text" name="equipment_serial_no" class="form-control form-control-sm" placeholder="VD: SN-ANESTH-GE-8849" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tên Máy Móc Thiết Bị</label>
                                    <input type="text" name="device_name" class="form-control form-control-sm" placeholder="Máy Gây Mê Kèm Thở GE Aisys CS2" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Bảo Trì</label>
                                        <select name="maintenance_type" class="form-select form-select-sm" required>
                                            <option value="preventive_calibration">Hiệu Chuẩn Dự Phòng Định Kỳ</option>
                                            <option value="electrical_safety_iec62353">Kiểm Tra Dòng Rò IEC 62353</option>
                                            <option value="corrective_repair">Sửa Chữa Khắc Phục Sự Cố</option>
                                            <option value="annual_overhaul">Đại Tu Thiết Bị Hàng Năm</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Hạn Hiệu Chuẩn</label>
                                        <input type="date" name="calibration_expiry_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Dòng Rò Điện (μA)</label>
                                        <input type="number" step="0.5" name="leakage_current_microamp" class="form-control form-control-sm" value="45.0" required title="Chuẩn IEC 62353: < 100 uA">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Điện Trở Đất (Ω)</label>
                                        <input type="number" step="0.01" name="ground_resistance_ohm" class="form-control form-control-sm" value="0.12" required title="Chuẩn < 0.2 Ohm">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Chỉ Số MTBF (Giờ)</label>
                                        <input type="number" name="mtbf_hours_calculated" class="form-control form-control-sm" value="2800" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Kỹ Sư Y Sinh</label>
                                        <input type="text" name="bio_engineer_name" class="form-control form-control-sm" placeholder="KS. Đặng Văn I" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Cấp Chứng Nhận An Toàn Điện IEC 62353
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-primary"></i>Hồ Sơ Quản Trị Vòng Đời Thiết Bị Y Sinh CMMS</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Số Seri</th>
                                            <th>Thiết Bị</th>
                                            <th>Dòng Rò & Tiếp Đất</th>
                                            <th>Hạn Hiệu Chuẩn</th>
                                            <th>Chứng Nhận</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_cmms'] as $cmms)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $cmms->equipment_serial_no }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $cmms->device_name }}</div>
                                                    <span class="text-muted small">KS: {{ $cmms->bio_engineer_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="{{ $cmms->leakage_current_microamp < 100.0 ? 'text-success' : 'text-danger fw-bold' }}">
                                                        Rò: {{ $cmms->leakage_current_microamp }} μA
                                                    </div>
                                                    <span class="text-muted small">Đất: {{ $cmms->ground_resistance_ohm }} Ω</span>
                                                </td>
                                                <td>{{ $cmms->calibration_expiry_date }}</td>
                                                <td>
                                                    @if($cmms->certification_status === 'certified_safe_pass')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-patch-check-fill me-1"></i> Đạt Chuẩn Điện Y Sinh
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Cách Ly Sửa Chữa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bản ghi bảo trì thiết bị y tế CMMS.</td>
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

        <!-- 3. Bãi Xe Cấp Cứu ALPR -->
        <div class="tab-pane fade" id="tab-gate" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-camera me-2 text-danger"></i>Nhận Diện Camera ALPR Mở Barie Ưu Tiên</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.asset-engineering-haccp-linen.parking-gate') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Biển Số Xe Vào Cổng</label>
                                    <input type="text" name="vehicle_license_plate" class="form-control form-control-sm" placeholder="VD: 29A-115.88, 51B-998.21" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Xe</label>
                                        <select name="vehicle_type" class="form-select form-select-sm" required>
                                            <option value="emergency_ambulance">Xe Cấp Cứu Khẩn Cấp (115)</option>
                                            <option value="patient_wheelchair_van">Xe Chở Bệnh Nhân Xe Lăn</option>
                                            <option value="staff_physician">Xe Bác Sĩ Trực Cấp Cứu</option>
                                            <option value="visitor_standard">Xe Thân Nhân Đến Khám</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Độ Chính Xác Camera (%)</label>
                                        <input type="number" step="0.1" name="alpr_camera_confidence_pct" class="form-control form-control-sm" value="98.5" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Vị Trí Đỗ Được Điều Hướng</label>
                                    <input type="text" name="assigned_parking_bay" class="form-control form-control-sm" value="BAY-EMERGENCY-01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Còi Hú Cấp Cứu Âm Thanh</label>
                                    <select name="emergency_siren_detected" class="form-select form-select-sm">
                                        <option value="1">Có còi hú khẩn cấp &rarr; Mở ngay luồng xanh</option>
                                        <option value="0">Không có còi hú</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Quét Biển Số & Điều Khiển Barie
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-car-front-fill me-2 text-danger"></i>Lịch Sử Luồng Xe Cấp Cứu & Bãi Đỗ Thông Minh</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Biển Số</th>
                                            <th>Loại Xe</th>
                                            <th>Độ Tin Cậy ALPR</th>
                                            <th>Vị Trí Đỗ</th>
                                            <th>Trạng Thái Barie</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_parking_gates'] as $gate)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $gate->vehicle_license_plate }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $gate->vehicle_type }}</span></td>
                                                <td><span class="fw-bold text-success">{{ $gate->alpr_camera_confidence_pct }}%</span></td>
                                                <td><i class="bi bi-p-square-fill text-primary me-1"></i> {{ $gate->assigned_parking_bay }}</td>
                                                <td>
                                                    @if($gate->barrier_gate_status === 'auto_opened_fast_track')
                                                        <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i> Luồng Xanh Mở Barie</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ $gate->barrier_gate_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có lượt xe nào qua cổng bãi xe.</td>
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

        <!-- 4. Bếp Ăn Bệnh Viện HACCP -->
        <div class="tab-pane fade" id="tab-kitchen" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cup-hot me-2 text-warning"></i>Kiểm Soát An Toàn Thực Phẩm Bếp Ăn HACCP</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.asset-engineering-haccp-linen.kitchen-haccp') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Lô Khay Cơm Bệnh Lý</label>
                                    <input type="text" name="menu_batch_code" class="form-control form-control-sm" placeholder="VD: DIET-RENAL-LUNCH-88" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Chế Độ Ăn Dinh Dưỡng</label>
                                    <select name="diet_category" class="form-select form-select-sm" required>
                                        <option value="renal_low_sodium">Bệnh Thận Ít Muối Giảm Đạm</option>
                                        <option value="diabetic_glycemic_control">Đái Tháo Đường Kiểm Soát Đường Huyết</option>
                                        <option value="post_op_soft_liquid">Sau Phẫu Thuật Cháo Súp Lỏng</option>
                                        <option value="pediatric_allergen_free">Nhi Khoa Không Chứa Dị Nguyên Dị Ứng</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Nấu Chín Tâm (°C)</label>
                                        <input type="number" step="0.5" name="cooking_core_temp_celsius" class="form-control form-control-sm" value="82.5" required title="Chuẩn >= 75°C">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Giữ Nóng Xe (°C)</label>
                                        <input type="number" step="0.5" name="rethermalization_hot_temp" class="form-control form-control-sm" value="68.0" required title="Chuẩn >= 65°C">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold">Giữ Lạnh Xe (°C)</label>
                                        <input type="number" step="0.5" name="rethermalization_cold_temp" class="form-control form-control-sm" value="3.5" required title="Chuẩn <= 4°C">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Lưu Mẫu Thức Ăn 24h</label>
                                        <select name="food_sample_refrigerated_24h" class="form-select form-select-sm">
                                            <option value="1">Đã niêm phong lưu mẫu tủ lạnh</option>
                                            <option value="0">Chưa lưu mẫu</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Bếp Trưởng / Dược Sĩ</label>
                                        <input type="text" name="executive_chef_dietitian" class="form-control form-control-sm" placeholder="BS. Dinh Dưỡng Mai Thị O" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1"></i> Nghiệm Thu Chuẩn An Toàn Thực Phẩm HACCP
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clipboard-check me-2 text-warning"></i>Nhật Ký Suất Ăn Bệnh Lý Chuẩn HACCP / ISO 22000</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Lô Khay</th>
                                            <th>Chế Độ Ăn</th>
                                            <th>Nhiệt Độ Nấu & Giữ Nóng</th>
                                            <th>Lưu Mẫu 24h</th>
                                            <th>Chuẩn HACCP</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_kitchen_batches'] as $kitchen)
                                            <tr>
                                                <td class="fw-bold text-warning">{{ $kitchen->menu_batch_code }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $kitchen->diet_category }}</span></td>
                                                <td>
                                                    <div>Nấu chín: <span class="fw-bold text-success">{{ $kitchen->cooking_core_temp_celsius }}°C</span></div>
                                                    <span class="text-muted small">Nóng: {{ $kitchen->rethermalization_hot_temp }}°C • Lạnh: {{ $kitchen->rethermalization_cold_temp }}°C</span>
                                                </td>
                                                <td>
                                                    @if($kitchen->food_sample_refrigerated_24h)
                                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-all"></i> Đã Lưu Mẫu</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Chưa Lưu</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($kitchen->haccp_audit_passed)
                                                        <span class="badge bg-success-subtle text-success border border-success">Đạt Chuẩn HACCP</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Không Đạt</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có lô suất ăn dinh dưỡng nào được nghiệm thu.</td>
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

        <!-- 5. Đồ Vải & Săng Mổ RFID -->
        <div class="tab-pane fade" id="tab-linen" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tags me-2 text-info"></i>Quản Lý Đồ Vải & Săng Mổ Gắn Chip RFID</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.asset-engineering-haccp-linen.linen-rfid') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Chip RFID EPC Chịu Nhiệt</label>
                                    <input type="text" name="linen_rfid_epc" class="form-control form-control-sm" placeholder="VD: RFID-LINEN-OR-TRAY-881" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Đồ Vải</label>
                                        <select name="linen_type" class="form-select form-select-sm" required>
                                            <option value="surgical_drape_pack">Gói Săng Mổ Vô Khuẩn Phẫu Thuật</option>
                                            <option value="patient_gown_isolation">Áo Choàng Bệnh Nhân Cách Ly</option>
                                            <option value="icu_bed_sheet_thermal">Ga Trải Giường Giữ Nhiệt ICU</option>
                                            <option value="doctor_scrub_suit">Bộ Quần Áo Scrub Bác Sĩ Mổ</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Chu Kỳ Đã Giặt</label>
                                        <input type="number" name="wash_cycle_count" class="form-control form-control-sm" value="32" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tuổi Thọ Tối Đa (Lần)</label>
                                        <input type="number" name="max_wash_lifespan" class="form-control form-control-sm" value="100" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Giặt Khử Trùng (°C)</label>
                                        <input type="number" step="0.5" name="last_wash_temp_celsius" class="form-control form-control-sm" value="92.0" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Kiểm Định Vi Sinh Bào Tử</label>
                                    <select name="biological_spore_test" class="form-select form-select-sm" required>
                                        <option value="negative_sterile">Âm Tính - Vô Khuẩn Tuyệt Đối</option>
                                        <option value="positive_bioburden_wash_again">Dương Tính - Phải Giặt Khử Lại</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-tag me-1"></i> Lưu Đồ Vải Gắn Chip RFID
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-repeat me-2 text-info"></i>Vòng Đời Giặt Tiệt Trùng & Quản Lý Đồ Vải RFID</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Chip EPC</th>
                                            <th>Loại Đồ Vải</th>
                                            <th>Chu Kỳ Giặt</th>
                                            <th>Nhiệt Độ & Vi Sinh</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_linens'] as $linen)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $linen->linen_rfid_epc }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $linen->linen_type }}</span></td>
                                                <td>
                                                    <span class="fw-bold">{{ $linen->wash_cycle_count }} / {{ $linen->max_wash_lifespan }}</span> lần
                                                    <div class="progress" style="height: 4px;">
                                                        <div class="progress-bar bg-info" style="width: {{ ($linen->wash_cycle_count / $linen->max_wash_lifespan) * 100 }}%;"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>Nhiệt giặt: {{ $linen->last_wash_temp_celsius }}°C</div>
                                                    <span class="badge bg-success-subtle text-success">{{ $linen->biological_spore_test }}</span>
                                                </td>
                                                <td>
                                                    <form action="{{ route('reception.asset-engineering-haccp-linen.linen-wash', $linen->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info btn-sm py-0 px-2" title="Ghi nhận thêm 1 lần giặt">
                                                            <i class="bi bi-plus"></i> Giặt
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có món đồ vải nào được gắn chip RFID.</td>
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
