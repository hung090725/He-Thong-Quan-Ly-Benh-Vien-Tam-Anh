@extends('layouts.admin')

@section('title', 'Chỉ Huy Thảm Họa HICS, Két Bảo Mật, Sinh Thiết Lạnh & Chống Kiệt Sức Ca Trực')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-shaded text-danger me-2"></i>Chỉ Huy Thảm Họa HICS, Két Bảo Mật, Sinh Thiết Lạnh & Chống Kiệt Sức Ca Trực
            </h4>
            <p class="text-muted small mb-0">
                Wave 10 • Trung tâm chỉ huy thảm họa & kích hoạt kịch bản khẩn cấp HICS, Két sắt lưu giữ tư trang bệnh nhân bảo mật kép, Vận chuyển sinh thiết lạnh phòng mổ Frozen Section $\le 3$ phút, Giám sát mệt mỏi ca trực Fatigue Index & Khu chờ xuất viện Meds-to-Beds
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-hospital me-1"></i> Disaster Safety & Clinician Wellness Active
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
                            <span class="text-muted small">Kịch Bản Thảm Họa Kích Hoạt</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_hics_mobilizing'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
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
                            <span class="text-muted small">Túi Tư Trang Niêm Phong Két</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['secured_vault_items'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-safe fs-5"></i>
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
                            <span class="text-muted small">Sinh Thiết Lạnh Diện Cắt Sạch</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['clear_margins_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-snow2 fs-5"></i>
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
                            <span class="text-muted small">Cảnh Báo Kiệt Sức Ca Trực</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['grounded_sleep_alerts'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-moon-stars-fill fs-5"></i>
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
                            <span class="text-muted small">Giải Phóng Giường Trước 10h</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['bed_freed_early_count'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-door-open-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-hics-tab" data-bs-toggle="pill" data-bs-target="#tab-hics" type="button" role="tab">
                <i class="bi bi-shield-exclamation me-1"></i> 1. Chỉ Huy Thảm Họa HICS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-vault-tab" data-bs-toggle="pill" data-bs-target="#tab-vault" type="button" role="tab">
                <i class="bi bi-safe2 me-1"></i> 2. Két Bảo Mật Kép Tư Trang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-frozen-tab" data-bs-toggle="pill" data-bs-target="#tab-frozen" type="button" role="tab">
                <i class="bi bi-snow me-1"></i> 3. Sinh Thiết Lạnh Frozen Section
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-fatigue-tab" data-bs-toggle="pill" data-bs-target="#tab-fatigue" type="button" role="tab">
                <i class="bi bi-moon me-1"></i> 4. Chống Mệt Mỏi Fatigue Index
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-discharge-tab" data-bs-toggle="pill" data-bs-target="#tab-discharge" type="button" role="tab">
                <i class="bi bi-box-arrow-right me-1"></i> 5. Chờ Xuất Viện Meds-to-Beds
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Chỉ Huy Thảm Họa HICS -->
        <div class="tab-pane fade show active" id="tab-hics" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-fill-exclamation me-2 text-danger"></i>Kích Hoạt Kịch Bản Thảm Họa HICS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.disaster-safety-fatigue-discharge.hics') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Sự Kiện Thảm Họa</label>
                                    <input type="text" name="incident_event_code" class="form-control form-control-sm" placeholder="VD: HICS-2026-FIRE-WEST" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Kịch Bản Khẩn Cấp Bệnh Viện</label>
                                    <select name="disaster_type" class="form-select form-select-sm" required>
                                        <option value="code_yellow_mass_casualty">Code Yellow - Thảm Họa Tai Nạn Hàng Loạt</option>
                                        <option value="code_silver_active_shooter">Code Silver - Bạo Lực Vũ Trang / Bắt Giữ Con Tiềm</option>
                                        <option value="code_black_bomb_threat">Code Black - Đe Dọa Đặt Bom Khủng Bố</option>
                                        <option value="code_orange_hazardous_spill">Code Orange - Rò Rỉ Hóa Chất Nguy Hại Độc Xạ</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Địa Điểm Đặt Sở Chỉ Huy</label>
                                    <input type="text" name="command_center_location" class="form-control form-control-sm" value="Phòng Hội Chẩn Trung Tâm Tầng 2" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Chỉ Huy Trưởng Ban Ứng Phó (Commander)</label>
                                    <input type="text" name="incident_commander_name" class="form-control form-control-sm" placeholder="Giám Đốc Bệnh Viện / Trưởng Ban Chỉ Đạo" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Dự Kiến Nạn Nhân Tiếp Nhận</label>
                                        <input type="number" name="casualty_intake_expected" class="form-control form-control-sm" value="50" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Giường Giải Phóng Cấp Tốc</label>
                                        <input type="number" name="beds_mobilized_count" class="form-control form-control-sm" value="45" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Loại Hoạt Động</label>
                                    <select name="drill_or_actual" class="form-select form-select-sm" required>
                                        <option value="scheduled_mock_drill">Diễn Tập Thực Chiến Theo Kế Hoạch</option>
                                        <option value="real_world_emergency">Tình Huống Khẩn Cấp Thực Tế Ngoài Đời Thật</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-megaphone me-1"></i> Phát Lệnh Kích Hoạt Chỉ Huy Thảm Họa HICS
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-danger"></i>Nhật Ký Các Đợt Ứng Phó Thảm Họa Bệnh Viện</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Sự Kiện</th>
                                            <th>Kịch Bản Thảm Họa</th>
                                            <th>Chỉ Huy Trưởng</th>
                                            <th>Quy Mô Giường</th>
                                            <th>Trạng Thái & Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_hics'] as $hics)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $hics->incident_event_code }}</td>
                                                <td>
                                                    <span class="badge bg-danger text-white">{{ $hics->disaster_type }}</span>
                                                    <div class="text-muted small mt-1">{{ $hics->drill_or_actual }}</div>
                                                </td>
                                                <td>{{ $hics->incident_commander_name }}</td>
                                                <td>
                                                    <div>Tiếp nhận: {{ $hics->casualty_intake_expected }} ca</div>
                                                    <span class="text-success small">Huy động: {{ $hics->beds_mobilized_count }} giường</span>
                                                </td>
                                                <td>
                                                    @if($hics->activation_status === 'active_mobilizing')
                                                        <span class="badge bg-danger text-white animate__animated animate__pulse animate__infinite mb-1">Đang Kích Hoạt</span>
                                                        <form action="{{ route('reception.disaster-safety-fatigue-discharge.hics-deescalate', $hics->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning btn-sm py-0 px-2" title="Hạ nhiệt về chế độ Standby">
                                                                Dự Phòng
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $hics->activation_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có sự kiện thảm họa nào được kích hoạt.</td>
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

        <!-- 2. Két Bảo Mật Kép Tư Trang -->
        <div class="tab-pane fade" id="tab-vault" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-safe me-2 text-primary"></i>Niêm Phong Két Bảo Mật Kép Tư Trang Bệnh Nhân</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.disaster-safety-fatigue-discharge.biometric-vault') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân Hôn Mê / Vô Danh / Cấp Cứu</label>
                                    <select name="patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bệnh nhân --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân Nhập Nhanh</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Vô Danh Nam 35 Tuổi" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Túi Niêm Phong Barcode</label>
                                        <input type="text" name="sealed_bag_barcode" class="form-control form-control-sm" placeholder="VAULT-BAG-2026-009" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Hộc Két Sắt</label>
                                        <input type="text" name="vault_locker_number" class="form-control form-control-sm" value="LOCKER-B08" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bản Kê Danh Mục Tài Sản Chi Tiết</label>
                                    <textarea name="item_inventory_manifest" class="form-control form-control-sm" rows="2" placeholder="1 Ví da đen có 4.500.000 VNĐ, 1 điện thoại iPhone 14, 1 nhẫn kim loại màu vàng..." required></textarea>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Người Ký 1 (ĐD Trưởng)</label>
                                        <input type="text" name="dual_sign_staff_1" class="form-control form-control-sm" placeholder="ĐD. Nguyễn Thị P" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Người Ký 2 (Bảo Vệ)</label>
                                        <input type="text" name="dual_sign_staff_2" class="form-control form-control-sm" placeholder="BV. Trần Văn Q" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-lock me-1"></i> Khóa Két Sắt & Niêm Phong Tư Trang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock me-2 text-primary"></i>Danh Sách Tài Sản Đang Được Lưu Giữ Trong Két</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Túi</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Tài Sản Kê Khai</th>
                                            <th>Người Ký Chéo</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_vaults'] as $vault)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $vault->sealed_bag_barcode }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $vault->patient_name }}</div>
                                                    <span class="badge bg-light text-dark border">{{ $vault->vault_locker_number }}</span>
                                                </td>
                                                <td><span class="text-truncate d-inline-block" style="max-width: 180px;">{{ $vault->item_inventory_manifest }}</span></td>
                                                <td>
                                                    <div class="small">{{ $vault->dual_sign_staff_1 }}</div>
                                                    <span class="text-muted small">+ {{ $vault->dual_sign_staff_2 }}</span>
                                                </td>
                                                <td>
                                                    @if($vault->custody_status === 'secured_in_vault')
                                                        <form action="{{ route('reception.disaster-safety-fatigue-discharge.vault-release', $vault->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2" title="Bàn giao lại cho người nhà">
                                                                <i class="bi bi-box-arrow-up"></i> Bàn Giao
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">Đã Bàn Giao</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có tư trang nào trong két bảo mật kép.</td>
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

        <!-- 3. Sinh Thiết Lạnh Frozen Section -->
        <div class="tab-pane fade" id="tab-frozen" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-snow me-2 text-success"></i>Báo Cáo Sinh Thiết Lạnh Tức Thì Frozen Section</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.disaster-safety-fatigue-discharge.frozen-section') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Nhãn Mẫu Bệnh Phẩm</label>
                                    <input type="text" name="specimen_label_code" class="form-control form-control-sm" placeholder="VD: FS-OR03-BRAIN-TUMOR" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân Đang Mổ</label>
                                    <select name="patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bệnh nhân --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Ngô Văn R" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phòng Mổ Gửi Mẫu</label>
                                        <input type="text" name="operating_room" class="form-control form-control-sm" value="Phòng Mổ OR-03" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Gian Chuyển Mẫu (Phút)</label>
                                        <input type="number" step="0.1" name="transit_time_minutes" class="form-control form-control-sm" value="2.5" required title="Chuẩn <= 3 phút">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Hộp Lạnh (°C)</label>
                                        <input type="number" step="0.5" name="frozen_biopsy_cold_box_temp" class="form-control form-control-sm" value="-20.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Tổng Thời Gian Trả KQ (Phút)</label>
                                        <input type="number" name="turnaround_total_minutes" class="form-control form-control-sm" value="16" required title="Chuẩn <= 20 phút">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bác Sĩ Giải Phẫu Bệnh Đọc Mẫu</label>
                                    <input type="text" name="pathology_consultant_name" class="form-control form-control-sm" placeholder="PGS. TS. Lê Văn S" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Kết Luận Nhanh Cho Phẫu Thuật Viên</label>
                                    <select name="histopathology_quick_result" class="form-select form-select-sm" required>
                                        <option value="benign_margins_clear">Lành Tính - Diện Cắt Sạch 100%</option>
                                        <option value="malignant_adenocarcinoma">Ác Tính - Còn Tế Bào Ung Thư Ở Diện Cắt</option>
                                        <option value="inconclusive_await_permanent">Chưa Rõ - Chờ Nhuộm Thường Quy Paraffin</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                                    <i class="bi bi-display me-1"></i> Báo Cáo KQ Lên Màn Hình Phòng Mổ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-eye me-2 text-success"></i>Theo Dõi Ca Sinh Thiết Lạnh Trong Khi Phẫu Thuật</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Mẫu</th>
                                            <th>Bệnh Nhân & Phòng Mổ</th>
                                            <th>Thời Gian Trả KQ</th>
                                            <th>Bác Sĩ Đọc Mẫu</th>
                                            <th>Kết Quả Diện Cắt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_frozen'] as $fs)
                                            <tr>
                                                <td class="fw-bold text-success">{{ $fs->specimen_label_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $fs->patient_name }}</div>
                                                    <span class="text-muted small">{{ $fs->operating_room }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold {{ $fs->turnaround_total_minutes <= 20 ? 'text-success' : 'text-danger' }}">
                                                        {{ $fs->turnaround_total_minutes }} phút
                                                    </span>
                                                    <div class="text-muted small">Chuyển mẫu: {{ $fs->transit_time_minutes }}p</div>
                                                </td>
                                                <td>{{ $fs->pathology_consultant_name }}</td>
                                                <td>
                                                    @if($fs->histopathology_quick_result === 'benign_margins_clear')
                                                        <span class="badge bg-success-subtle text-success border border-success">Diện Cắt Sạch</span>
                                                    @elseif($fs->histopathology_quick_result === 'malignant_adenocarcinoma')
                                                        <span class="badge bg-danger text-white">Diện Cắt Còn U</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Chờ Paraffin</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có ca sinh thiết lạnh nào được thực hiện.</td>
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

        <!-- 4. Chống Mệt Mỏi Fatigue Index -->
        <div class="tab-pane fade" id="tab-fatigue" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-moon-stars me-2 text-warning"></i>Đánh Giá Mệt Mỏi Ca Trực Fatigue Index</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.disaster-safety-fatigue-discharge.clinician-fatigue') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bác Sĩ / Phẫu Thuật Viên Trực</label>
                                    <select name="doctor_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bác sĩ --</option>
                                        @foreach($stats['doctors'] as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->user->name ?? 'BS. '.$doc->id }} ({{ $doc->specialty->name ?? 'Khoa' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bác Sĩ Nhập Nhanh</label>
                                    <input type="text" name="doctor_name" class="form-control form-control-sm" placeholder="BS. Nguyễn Văn Hưng" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Gian Trực (Giờ)</label>
                                        <input type="number" step="0.5" name="shift_duration_hours" class="form-control form-control-sm" value="16.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Giờ Mổ Liên Tục (Giờ)</label>
                                        <input type="number" step="0.5" name="consecutive_surgical_hours" class="form-control form-control-sm" value="6.5" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thiếu Ngủ Tích Lũy (Giờ)</label>
                                        <input type="number" step="0.5" name="sleep_deficit_hours" class="form-control form-control-sm" value="4.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Điểm Mệt Mỏi (0-100)</label>
                                        <input type="number" name="fatigue_risk_index_score" class="form-control form-control-sm" value="75" required title=">= 70 bắt buộc phải nghỉ ngủ bù">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bác Sĩ Dự Phòng Thay Ca (Nêu có)</label>
                                    <input type="text" name="relief_physician_assigned" class="form-control form-control-sm" placeholder="BS. Hoàng Văn T">
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-heart-pulse me-1"></i> Tính Chỉ Số Mệt Mỏi & Điều Chuyển Ca
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-fill-exclamation me-2 text-warning"></i>Cảnh Báo Kiệt Sức Ca Trực Cho Đội Ngũ Y Bác Sĩ</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Bác Sĩ</th>
                                            <th>Thời Gian Trực / Mổ</th>
                                            <th>Điểm Fatigue Index</th>
                                            <th>Nghỉ Ngủ Bù</th>
                                            <th>Tình Trạng Sức Khỏe</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_fatigue'] as $fatigue)
                                            <tr>
                                                <td class="fw-bold text-dark">{{ $fatigue->doctor_name }}</td>
                                                <td>
                                                    <div>Trực: {{ $fatigue->shift_duration_hours }}h</div>
                                                    <span class="text-danger small">Mổ liên tục: {{ $fatigue->consecutive_surgical_hours }}h</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold {{ $fatigue->fatigue_risk_index_score >= 70 ? 'text-danger' : 'text-primary' }}">
                                                        {{ $fatigue->fatigue_risk_index_score }}/100
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($fatigue->mandatory_rest_alert)
                                                        <span class="badge bg-danger text-white animate__animated animate__flash animate__infinite">BẮT BUỘC NGHỈ</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">Đủ Sức Khỏe</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($fatigue->fitness_for_duty_status === 'grounded_mandatory_sleep')
                                                        <span class="badge bg-danger text-white">Cách Ly Khỏi Bàn Mổ</span>
                                                    @else
                                                        <span class="badge bg-light text-dark border">{{ $fatigue->fitness_for_duty_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có đánh giá mệt mỏi ca trực nào.</td>
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

        <!-- 5. Chờ Xuất Viện Meds-to-Beds -->
        <div class="tab-pane fade" id="tab-discharge" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-arrow-right me-2 text-info"></i>Tiếp Nhận Vào Discharge Lounge & Meds-to-Beds</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.disaster-safety-fatigue-discharge.discharge-lounge') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân Xuất Viện</label>
                                    <select name="patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bệnh nhân --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân Nhập Nhanh</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Phạm Thị U" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Phòng Cũ Vừa Trả</label>
                                        <input type="text" name="departed_ward_room" class="form-control form-control-sm" value="702-B" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Vị Trí Ghế Sofa Lounge</label>
                                        <input type="text" name="discharge_lounge_bay" class="form-control form-control-sm" value="LOUNGE-RECLINER-03" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Dược Sĩ Lâm Sàng Phụ Trách</label>
                                        <input type="text" name="clinical_pharmacist_name" class="form-control form-control-sm" placeholder="DS. Vũ Thị V" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Giải Phóng Giường Trước 10h</label>
                                        <select name="bed_freed_before_10am" class="form-select form-select-sm">
                                            <option value="1">Đã trả buồng trước 10h sáng</option>
                                            <option value="0">Sau 10h</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-door-open me-1"></i> Chuyển Bệnh Nhân Xuống Lounge Chờ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-check-all me-2 text-info"></i>Tiến Độ Cấp Phát Thuốc Tận Giường & Rời Viện</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Bệnh Nhân</th>
                                            <th>Buồng Cũ & Ghế Chờ</th>
                                            <th>Dược Sĩ Lâm Sàng</th>
                                            <th>Meds-to-Beds</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_discharge'] as $disc)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $disc->patient_name }}</td>
                                                <td>
                                                    <div>Phòng cũ: {{ $disc->departed_ward_room }}</div>
                                                    <span class="badge bg-light text-dark border">{{ $disc->discharge_lounge_bay }}</span>
                                                </td>
                                                <td>{{ $disc->clinical_pharmacist_name }}</td>
                                                <td>
                                                    @if($disc->meds_to_beds_delivered)
                                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle"></i> Đã Giao Thuốc</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">Đang Soạn Thuốc</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('reception.disaster-safety-fatigue-discharge.meds-deliver', $disc->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-sm py-0 px-2" title="Xác nhận giao thuốc tận giường">
                                                                Thuốc
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('reception.disaster-safety-fatigue-discharge.discharge-home', $disc->id) }}" method="POST" class="d-inline ms-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2" title="Xác nhận rời viện về nhà">
                                                                Về Nhà
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bệnh nhân nào trong khu chờ xuất viện.</td>
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
