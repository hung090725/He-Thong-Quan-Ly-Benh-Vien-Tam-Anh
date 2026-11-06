@extends('layouts.admin')

@section('title', 'Robot AGV Tự Hành, Ống Khí Nén PTS, Microgrid ESG & Nước Chạy Thận RO')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-robot text-primary me-2"></i>Robot AGV Tự Hành, Ống Khí Nén PTS, Microgrid ESG & Nước Chạy Thận RO
            </h4>
            <p class="text-muted small mb-0">
                Wave 10 • Đội Robot tự hành AGV/AMR vận chuyển nội viện, Đường ống khí nén PTS siêu tốc chống vỡ hồng cầu, Lưới điện vi mô bệnh viện Microgrid & ATS, Nghiền hấp vi sóng rác y tế tại chỗ & Nước lọc thận siêu tinh khiết AAMI/ISO 23500
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-cpu me-1"></i> Autonomous Logistics & Green ESG Active
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
                            <span class="text-muted small">Robot AGV Đang Giao Hàng</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['active_delivering_robots'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-robot fs-5"></i>
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
                            <span class="text-muted small">Thoi Khí Nén Đến An Toàn</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['pts_safe_arrivals'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-send-check fs-5"></i>
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
                            <span class="text-muted small">Vùng Lưới Điện Tier-4</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['optimal_tier4_grids'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-lightning-charge-fill fs-5"></i>
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
                            <span class="text-muted small">Lô Rác Khử Trùng Vi Sóng</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['decontaminated_batches'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-recycle fs-5"></i>
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
                            <span class="text-muted small">Trạm Nước RO Siêu Sạch ISO</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['ultra_pure_ro_plants'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-droplet-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-agv-tab" data-bs-toggle="pill" data-bs-target="#tab-agv" type="button" role="tab">
                <i class="bi bi-robot me-1"></i> 1. Robot AGV Tự Hành
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-pts-tab" data-bs-toggle="pill" data-bs-target="#tab-pts" type="button" role="tab">
                <i class="bi bi-send me-1"></i> 2. Ống Khí Nén PTS Siêu Tốc
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-microgrid-tab" data-bs-toggle="pill" data-bs-target="#tab-microgrid" type="button" role="tab">
                <i class="bi bi-lightning-charge me-1"></i> 3. Lưới Điện Microgrid ESG
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-shredder-tab" data-bs-toggle="pill" data-bs-target="#tab-shredder" type="button" role="tab">
                <i class="bi bi-recycle me-1"></i> 4. Nghiền Hấp Vi Sóng Rác Y Tế
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-ro-tab" data-bs-toggle="pill" data-bs-target="#tab-ro" type="button" role="tab">
                <i class="bi bi-water me-1"></i> 5. Nước Chạy Thận RO ISO 23500
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Robot AGV Tự Hành -->
        <div class="tab-pane fade show active" id="tab-agv" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Điều Phối Xe Tự Hành Robot AGV/AMR</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.autonomous-logistics-esg.agv-robot') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Robot AGV</label>
                                    <input type="text" name="robot_code" class="form-control form-control-sm" placeholder="VD: AGV-PHARM-01, AGV-LINEN-02" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Model Xe Robot</label>
                                    <input type="text" name="robot_model" class="form-control form-control-sm" value="MiR250 Autonomous Mobile Robot" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Tải Vận Chuyển</label>
                                        <select name="payload_category" class="form-select form-select-sm" required>
                                            <option value="sterile_supplies">Dụng Cụ Mổ Vô Khuẩn CSSD</option>
                                            <option value="cytotoxic_chemotherapy">Thuốc Hóa Trị Độc Tế Bào</option>
                                            <option value="linen_laundry">Xe Đồ Vải Ga Giường</option>
                                            <option value="therapeutic_dietary">Khay Cơm Dinh Dưỡng Bệnh Lý</option>
                                            <option value="biohazard_waste">Thùng Rác Lây Nhiễm Niêm Phong</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tầng Hiện Tại</label>
                                        <input type="text" name="current_floor" class="form-control form-control-sm" value="Tầng 1" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Điểm Đến Giao Hàng</label>
                                    <input type="text" name="destination_location" class="form-control form-control-sm" placeholder="Khu Phẫu Thuật Tầng 3 - Phòng Mổ OR-04" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mức Pin (%)</label>
                                        <input type="number" name="battery_percentage" class="form-control form-control-sm" value="95" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạng Thái Điều Hướng</label>
                                        <select name="navigation_status" class="form-select form-select-sm" required>
                                            <option value="idle_docked">Đang chờ tại Dock</option>
                                            <option value="navigating_corridor">Đang di chuyển hành lang</option>
                                            <option value="riding_elevator">Đang đi thang máy số</option>
                                            <option value="delivering_payload">Đang giao hàng tận nơi</option>
                                            <option value="charging_return">Tự quay về trạm sạc</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Giám Sát Viên Điều Phối</label>
                                    <input type="text" name="dispatch_supervisor_name" class="form-control form-control-sm" placeholder="Kỹ sư Nguyễn Văn H" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-robot me-1"></i> Khởi Tạo & Điều Động Xe Robot AGV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-broadcast me-2 text-primary"></i>Hạm Đội Xe Tự Hành AGV Hoạt Động Thời Gian Thực</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Robot</th>
                                            <th>Loại Tải</th>
                                            <th>Vị Trí & Điểm Đến</th>
                                            <th>Pin</th>
                                            <th>Trạng Thái & Lệnh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_agv'] as $agv)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $agv->robot_code }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $agv->payload_category }}</span></td>
                                                <td>
                                                    <div><i class="bi bi-geo-alt-fill text-danger"></i> {{ $agv->current_floor }}</div>
                                                    <span class="text-muted small">&rarr; {{ $agv->destination_location }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-success">{{ $agv->battery_percentage }}%</div>
                                                    <div class="progress" style="height: 4px;">
                                                        <div class="progress-bar bg-success" style="width: {{ $agv->battery_percentage }}%;"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info border border-info mb-1">{{ $agv->navigation_status }}</span>
                                                    <form action="{{ route('reception.autonomous-logistics-esg.agv-dispatch', $agv->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm py-0 px-2" title="Giao hàng ngay">
                                                            <i class="bi bi-send"></i> Chạy
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có xe robot tự hành AGV nào trong hệ thống.</td>
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

        <!-- 2. Ống Khí Nén PTS Siêu Tốc -->
        <div class="tab-pane fade" id="tab-pts" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-send me-2 text-success"></i>Bắn Thoi Khí Nén Chuyển Mẫu Máu PTS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.autonomous-logistics-esg.pneumatic-tube') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Thoi Khí Nén Barcode</label>
                                    <input type="text" name="capsule_barcode" class="form-control form-control-sm" placeholder="VD: PTS-CAP-8839" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạm Gửi (Nguồn)</label>
                                        <input type="text" name="station_code" class="form-control form-control-sm" value="STATION-ICU-3F" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạm Nhận (Đích)</label>
                                        <input type="text" name="destination_station" class="form-control form-control-sm" value="CENTRAL-LAB-1F" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Loại Mẫu Xét Nghiệm Cấp Cứu</label>
                                    <select name="specimen_category" class="form-select form-select-sm" required>
                                        <option value="stat_troponin_blood">Mẫu Máu STAT Troponin Đột Quỵ / Nhồi Máu</option>
                                        <option value="abg_arterial_blood">Khí Máu Động Mạch ABG</option>
                                        <option value="csf_spinal_fluid">Dịch Não Tủy CSF</option>
                                        <option value="crossmatch_blood_sample">Mẫu Máu Phản Ứng Chéo Truyền Máu Tối Cấp</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Vận Tốc Khí (m/s)</label>
                                        <input type="number" step="0.1" name="velocity_meters_per_sec" class="form-control form-control-sm" value="6.5" required title="Chuẩn 6.0 - 8.0 m/s">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Đỉnh Gia Tốc G-Force</label>
                                        <input type="number" step="0.05" name="g_force_shock_peak" class="form-control form-control-sm" value="1.20" required title="Chuẩn < 2.5G để chống vỡ hồng cầu">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                                    <i class="bi bi-send-fill me-1"></i> Bắn Thoi Khí Nén Siêu Tốc
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-success"></i>Đường Bay Ống Khí Nén & Kiểm Soát Vỡ Hồng Cầu</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Thoi</th>
                                            <th>Tuyến Đường</th>
                                            <th>Vận Tốc & G-Force</th>
                                            <th>Chống Vỡ Máu</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_pts'] as $pts)
                                            <tr>
                                                <td class="fw-bold text-success">{{ $pts->capsule_barcode }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $pts->station_code }} &rarr; {{ $pts->destination_station }}</div>
                                                    <span class="text-muted small">{{ $pts->specimen_category }}</span>
                                                </td>
                                                <td>
                                                    <div>Vận tốc: {{ $pts->velocity_meters_per_sec }} m/s</div>
                                                    <span class="fw-bold text-primary">{{ $pts->g_force_shock_peak }} G</span>
                                                </td>
                                                <td>
                                                    @if($pts->hemolysis_risk_alert)
                                                        <span class="badge bg-danger text-white">Nguy Cơ Vỡ Hồng Cầu!</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">An Toàn Tuyệt Đối</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pts->delivery_status === 'arrived_safe')
                                                        <span class="badge bg-success text-white"><i class="bi bi-check-lg"></i> Đã Đến Lab</span>
                                                    @else
                                                        <span class="badge bg-info text-white">Đang Bay</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có thoi chuyển mẫu nào được bắn.</td>
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

        <!-- 3. Lưới Điện Microgrid ESG -->
        <div class="tab-pane fade" id="tab-microgrid" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge me-2 text-info"></i>Giám Sát Nguồn Điện ATS & Microgrid ESG</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.autonomous-logistics-esg.microgrid') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phân Vùng Điện</label>
                                    <input type="text" name="power_zone_code" class="form-control form-control-sm" placeholder="VD: GRID-OR-CORE, GRID-ICU-WEST" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạng Thái Lưới Điện</label>
                                        <select name="grid_power_status" class="form-select form-select-sm" required>
                                            <option value="national_grid_nominal">Lưới điện quốc gia bình thường</option>
                                            <option value="grid_blackout_detected">Mất điện lưới quốc gia!</option>
                                            <option value="microgrid_islanding">Chế độ đảo điện Microgrid độc lập</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Gian ATS (Giây)</label>
                                        <input type="number" step="0.1" name="ats_transfer_time_sec" class="form-control form-control-sm" value="6.8" required title="Chuẩn <= 10 giây">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Máy Phát Diesel Dự Phòng</label>
                                        <select name="backup_generator_running" class="form-select form-select-sm">
                                            <option value="0">Tắt - Chế độ chờ</option>
                                            <option value="1">Đang nổ máy cấp điện</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Dự Trữ UPS (Phút)</label>
                                        <input type="number" name="ups_reserve_minutes" class="form-control form-control-sm" value="45" required title="Chuẩn >= 30 phút">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Điện Mặt Trời Mái Nhà (kW)</label>
                                        <input type="number" step="0.1" name="rooftop_solar_generation_kw" class="form-control form-control-sm" value="125.5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Giảm Phát Thải CO2 (kg)</label>
                                        <input type="number" step="0.1" name="carbon_emissions_avoided_kg" class="form-control form-control-sm" value="850.0" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Xếp Hạng An Toàn Điện Lưới</label>
                                    <select name="grid_safety_rating" class="form-select form-select-sm" required>
                                        <option value="optimal_tier4">Tối Ưu Tier-4 (Độ tin cậy 99.99%)</option>
                                        <option value="alert_generator_fuel_low">Cảnh báo nhiên liệu máy phát thấp</option>
                                        <option value="ups_battery_degraded">Pin bình ắc quy UPS suy giảm</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Lưu Kiểm Định Nguồn Điện Khẩn Cấp
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill me-2 text-info"></i>Hệ Thống Điện Sống Còn & Năng Lượng Xanh ESG</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phân Vùng</th>
                                            <th>Trạng Thái Lưới</th>
                                            <th>ATS & UPS</th>
                                            <th>Điện Mặt Trời & CO2</th>
                                            <th>Xếp Hạng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_microgrid'] as $grid)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $grid->power_zone_code }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $grid->grid_power_status }}</span></td>
                                                <td>
                                                    <div>ATS: <span class="fw-bold text-primary">{{ $grid->ats_transfer_time_sec }}s</span></div>
                                                    <span class="text-muted small">UPS: {{ $grid->ups_reserve_minutes }} phút</span>
                                                </td>
                                                <td>
                                                    <div>Solar: {{ $grid->rooftop_solar_generation_kw }} kW</div>
                                                    <span class="text-success small">-{{ $grid->carbon_emissions_avoided_kg }} kg CO2</span>
                                                </td>
                                                <td>
                                                    @if($grid->grid_safety_rating === 'optimal_tier4')
                                                        <span class="badge bg-success-subtle text-success border border-success">Tier-4 Chuẩn JCI</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">{{ $grid->grid_safety_rating }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có phân vùng điện Microgrid nào được ghi nhận.</td>
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

        <!-- 4. Nghiền Hấp Vi Sóng Rác Y Tế -->
        <div class="tab-pane fade" id="tab-shredder" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-recycle me-2 text-warning"></i>Nghiền Hấp Khử Khuẩn Vi Sóng Rác Lây Nhiễm</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.autonomous-logistics-esg.microwave-shredder') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Lò Nghiền Hấp Vi Sóng</label>
                                    <input type="text" name="unit_code" class="form-control form-control-sm" value="MW-SHRED-B2-01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Lô Rác Barcode</label>
                                    <input type="text" name="batch_barcode" class="form-control form-control-sm" placeholder="VD: BATCH-WASTE-2026-112" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Khối Lượng Rác (kg)</label>
                                        <input type="number" step="0.1" name="waste_input_weight_kg" class="form-control form-control-sm" value="85.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Vi Sóng (°C)</label>
                                        <input type="number" step="0.5" name="microwave_temp_celsius" class="form-control form-control-sm" value="105.0" required title="Chuẩn >= 100°C">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Gian Chiếu Xạ (Phút)</label>
                                        <input type="number" name="exposure_time_minutes" class="form-control form-control-sm" value="30" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Giảm Thể Tích (%)</label>
                                        <input type="number" step="0.1" name="volume_reduction_pct" class="form-control form-control-sm" value="82.5" required title="Chuẩn >= 80%">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Diệt Bào Tử Bacillus Atrophaeus</label>
                                    <select name="spore_bacillus_atrophaeus_killed" class="form-select form-select-sm">
                                        <option value="1">Đã diệt 100% bào tử vi sinh</option>
                                        <option value="0">Chưa đạt vô trùng sinh học</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-recycle me-1"></i> Nghiền Hấp Khử Trùng Biến Rác An Toàn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-trash-fill me-2 text-warning"></i>Nhật Ký Xử Lý Chất Thải Y Tế Tại Chỗ Bằng Vi Sóng</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Lô</th>
                                            <th>Khối Lượng</th>
                                            <th>Nhiệt Vi Sóng</th>
                                            <th>Giảm Thể Tích</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_shredders'] as $mw)
                                            <tr>
                                                <td class="fw-bold text-warning">{{ $mw->batch_barcode }}</td>
                                                <td>{{ $mw->waste_input_weight_kg }} kg</td>
                                                <td>
                                                    <span class="fw-bold {{ $mw->microwave_temp_celsius >= 100.0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $mw->microwave_temp_celsius }}°C
                                                    </span>
                                                    <div class="text-muted small">Thời gian: {{ $mw->exposure_time_minutes }}p</div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ $mw->volume_reduction_pct }}%</span>
                                                    <div class="text-muted small">4 trục nghiền vụn</div>
                                                </td>
                                                <td>
                                                    @if($mw->processing_status === 'decontaminated_inert')
                                                        <span class="badge bg-success-subtle text-success border border-success">Rác Trơ An Toàn</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">{{ $mw->processing_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có mẻ nghiền hấp vi sóng nào được thực hiện.</td>
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

        <!-- 5. Nước Chạy Thận RO ISO 23500 -->
        <div class="tab-pane fade" id="tab-ro" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-droplet-half me-2 text-danger"></i>Kiểm Tra Nước Lọc Thận Siêu Tinh Khiết RO</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.autonomous-logistics-esg.ro-water') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Trạm Nước RO Trung Tâm</label>
                                    <input type="text" name="ro_plant_code" class="form-control form-control-sm" value="RO-HEMO-CENTER-01" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Độ Dẫn Điện (μS/cm)</label>
                                        <input type="number" step="0.05" name="conductivity_us_cm" class="form-control form-control-sm" value="4.20" required title="Chuẩn < 10 uS/cm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Vi Khuẩn (CFU/ml)</label>
                                        <input type="number" name="bacteria_cfu_per_ml" class="form-control form-control-sm" value="12" required title="Chuẩn < 100 CFU/ml">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nội Độc Tố (EU/ml)</label>
                                        <input type="number" step="0.005" name="endotoxin_eu_per_ml" class="form-control form-control-sm" value="0.040" required title="Chuẩn < 0.25 EU/ml">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ Nước (°C)</label>
                                        <input type="number" step="0.5" name="water_temperature_celsius" class="form-control form-control-sm" value="24.5" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Lưu Lượng Thẩm Thấu Permeate (L/phút)</label>
                                    <input type="number" step="0.5" name="permeate_flow_rate_lpm" class="form-control form-control-sm" value="45.0" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Chứng Nhận Nước Lọc Thận ISO 23500
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clipboard-pulse me-2 text-danger"></i>Chất Lượng Nước Lọc Thận Theo Tiêu Chuẩn AAMI/ISO 23500</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Trạm RO</th>
                                            <th>Độ Dẫn Điện</th>
                                            <th>Vi Sinh & Endotoxin</th>
                                            <th>Lưu Lượng</th>
                                            <th>Chuẩn AAMI/ISO</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ro_waters'] as $ro)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $ro->ro_plant_code }}</td>
                                                <td>
                                                    <span class="fw-bold {{ $ro->conductivity_us_cm < 10.0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $ro->conductivity_us_cm }} μS/cm
                                                    </span>
                                                    <div class="text-muted small">(Chuẩn &lt; 10)</div>
                                                </td>
                                                <td>
                                                    <div>Vi khuẩn: {{ $ro->bacteria_cfu_per_ml }} CFU/ml</div>
                                                    <span class="text-primary small">Endotoxin: {{ $ro->endotoxin_eu_per_ml }} EU/ml</span>
                                                </td>
                                                <td>{{ $ro->permeate_flow_rate_lpm }} L/phút</td>
                                                <td>
                                                    @if($ro->aami_iso23500_compliance === 'iso_ultra_pure_pass')
                                                        <span class="badge bg-success-subtle text-success border border-success">Đạt Chuẩn Siêu Sạch</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Khẩn Cấp Ngắt Nước</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bản ghi xét nghiệm nước RO lọc thận.</td>
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
