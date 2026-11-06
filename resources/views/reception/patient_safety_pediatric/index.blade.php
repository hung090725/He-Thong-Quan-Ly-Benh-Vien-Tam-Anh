@extends('layouts.admin')

@section('title', 'An Ninh Sơ Sinh RTLS, Nệm Braden, Ngân Hàng Sữa Mẹ, Hạt Nhân & Thử Nghiệm Lâm Sàng')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-lock-fill text-danger me-2"></i>An Ninh Sơ Sinh RTLS, Nệm Braden, Sữa Mẹ, Hạt Nhân & Thử Nghiệm Lâm Sàng
            </h4>
            <p class="text-muted small mb-0">
                Wave 8 • Vòng tay định vị thời gian thực RTLS (chống bắt cóc & nhầm lẫn sơ sinh), Giường nệm thông minh Braden ngừa loét tì đè, Ngân hàng sữa mẹ thanh trùng DHMB, An toàn bức xạ hạt nhân PET/CT theo IAEA & Quản trị thử nghiệm lâm sàng GCP
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-shield-check me-1"></i> Patient Safety, Pediatrics & Clinical Research Active
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
                            <span class="text-muted small">Bé Giám Sát Vòng Tay RTLS</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['monitored_infants_rtls'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-person-badge-fill fs-5"></i>
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
                            <span class="text-muted small">Giường Nệm Ngừa Loét Braden</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['braden_smart_beds'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
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
                            <span class="text-muted small">Mẻ Sữa Mẹ Tiệt Trùng DHMB</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['dhmb_approved_batches'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-cup-hot-fill fs-5"></i>
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
                            <span class="text-muted small">Điều Trị Y Học Hạt Nhân</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['nuclear_treatments'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-radioactive fs-5"></i>
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
                            <span class="text-muted small">Đề Tài Thử Nghiệm Lâm Sàng</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['active_clinical_trials'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-clipboard2-pulse fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Vui lòng kiểm tra lại thông tin biểu mẫu!
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="safetyPedTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="rtls-tab" data-bs-toggle="tab" data-bs-target="#rtls-pane" type="button" role="tab">
                <i class="bi bi-shield-lock me-1"></i> 1. An Ninh Sơ Sinh RTLS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="braden-tab" data-bs-toggle="tab" data-bs-target="#braden-pane" type="button" role="tab">
                <i class="bi bi-hospital me-1"></i> 2. Đệm Thông Minh Braden
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="milk-tab" data-bs-toggle="tab" data-bs-target="#milk-pane" type="button" role="tab">
                <i class="bi bi-cup-hot me-1"></i> 3. Ngân Hàng Sữa Mẹ DHMB
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="nuc-tab" data-bs-toggle="tab" data-bs-target="#nuc-pane" type="button" role="tab">
                <i class="bi bi-radioactive me-1"></i> 4. An Toàn Bức Xạ Hạt Nhân
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="trial-tab" data-bs-toggle="tab" data-bs-target="#trial-pane" type="button" role="tab">
                <i class="bi bi-clipboard2-pulse me-1"></i> 5. Thử Nghiệm Lâm Sàng CTMS
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="safetyPedTabContent">
        <!-- 1. An Ninh Sơ Sinh Định Vị RTLS -->
        <div class="tab-pane fade show active" id="rtls-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock me-2 text-danger"></i>Kích Hoạt Vòng Tay An Ninh Sơ Sinh RTLS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.patient-safety-pediatric.rtls') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Bé Sơ Sinh ID *</label>
                                        <input type="number" name="infant_patient_id" class="form-control form-control-sm" required placeholder="Infant ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Người Mẹ ID *</label>
                                        <input type="number" name="mother_patient_id" class="form-control form-control-sm" required placeholder="Mother ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Vòng Tay Bé (Tag ID) *</label>
                                        <input type="text" name="infant_rtls_tag_id" class="form-control form-control-sm" required value="RTLS-INFANT-{{ rand(1000, 9999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Vòng Tay Mẹ (Tag ID) *</label>
                                        <input type="text" name="mother_rtls_tag_id" class="form-control form-control-sm" required value="RTLS-MOM-{{ rand(1000, 9999) }}">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Hiện Tại *</label>
                                        <input type="text" name="current_floor_room" class="form-control form-control-sm" required value="Khoa Sản Tầng 4 - Phòng 402">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khoảng Cách Đến Lối Ra (m) *</label>
                                        <input type="number" step="0.1" name="distance_to_nearest_exit_meters" class="form-control form-control-sm" required value="24.5">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-broadcast text-danger me-1"></i>Cảm Biến Tiếp Xúc & Ghép Đôi Kép</h6>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="skin_tamper_sensor_intact" value="1" id="tamperSwitch" checked>
                                        <label class="form-check-label small" for="tamperSwitch">Cảm biến áp suất da tiếp xúc nguyên vẹn (Không bị cắt tháo)</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mother_infant_dual_pairing_verified" value="1" id="pairingSwitch" checked>
                                        <label class="form-check-label small" for="pairingSwitch">Ghép đôi mẹ - con xác thực âm thanh hai chiều (Dual Pairing)</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Giám Sát Vị Trí Tránh Bắt Cóc / Nhầm Lẫn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-broadcast me-2 text-danger"></i>Định Vị Thời Gian Thực Bé Sơ Sinh Tại Khoa Sản</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 bé mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bé Sơ Sinh & Mã Vòng</th>
                                        <th>Người Mẹ</th>
                                        <th>Vị Trí & Khoảng Cách Cửa</th>
                                        <th>Cảm Biến Da</th>
                                        <th>Trạng Thái An Ninh</th>
                                        <th>Khóa Cửa Khẩn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_rtls'] ?? [] as $rt)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $rt->infant->full_name ?? 'Bé #' . $rt->infant_patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Tag: {{ $rt->infant_rtls_tag_id }}</div>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $rt->mother->full_name ?? 'Mẹ #' . $rt->mother_patient_id }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $rt->current_floor_room }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Cách cửa: {{ $rt->distance_to_nearest_exit_meters }} m</div>
                                            </td>
                                            <td>
                                                @if($rt->skin_tamper_sensor_intact)
                                                    <span class="badge bg-success-subtle text-success">Tiếp Xúc Tốt</span>
                                                @else
                                                    <span class="badge bg-danger text-white">BỊ CẮT VÒNG</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rt->infant_safety_status === 'safe_in_room')
                                                    <span class="badge bg-success text-white">An Toàn Tại Phòng</span>
                                                @elseif($rt->infant_safety_status === 'geofence_alert')
                                                    <span class="badge bg-danger text-white">BÁO ĐỘNG RỜI KHOA</span>
                                                @else
                                                    <span class="badge bg-warning text-dark text-capitalize">{{ str_replace('_', ' ', $rt->infant_safety_status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rt->elevator_emergency_lockdown_triggered)
                                                    <span class="badge bg-danger text-white"><i class="bi bi-lock-fill me-1"></i>KHÓA THANG MÁY</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Bình Thường</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có bé sơ sinh nào đeo vòng định vị RTLS.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Đệm Thông Minh Braden Ngừa Loét Tì Đè -->
        <div class="tab-pane fade" id="braden-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-hospital me-2 text-warning"></i>Đánh Giá Thang Điểm Braden & Áp Lực Đệm</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.patient-safety-pediatric.braden') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Điều Dưỡng Trưởng *</label>
                                        <input type="number" name="head_nurse_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Cảm Giác (1-4)</label>
                                        <input type="number" name="braden_sensory_perception" class="form-control form-control-sm" required value="2" min="1" max="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Độ Ẩm (1-4)</label>
                                        <input type="number" name="braden_moisture" class="form-control form-control-sm" required value="3" min="1" max="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Hoạt Động (1-4)</label>
                                        <input type="number" name="braden_activity" class="form-control form-control-sm" required value="2" min="1" max="4">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Vận Động (1-4)</label>
                                        <input type="number" name="braden_mobility" class="form-control form-control-sm" required value="2" min="1" max="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Dinh Dưỡng (1-4)</label>
                                        <input type="number" name="braden_nutrition" class="form-control form-control-sm" required value="2" min="1" max="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Ma Sát Trượt (1-3)</label>
                                        <input type="number" name="braden_friction_shear" class="form-control form-control-sm" required value="2" min="1" max="3">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Áp Lực Xương Cùng (mmHg) *</label>
                                        <input type="number" step="0.1" name="sacrum_peak_pressure_mmhg" class="form-control form-control-sm" required value="28.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Áp Lực Gót Chân (mmHg) *</label>
                                        <input type="number" step="0.1" name="heels_peak_pressure_mmhg" class="form-control form-control-sm" required value="22.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Góc Nâng Đầu Giường (°) *</label>
                                        <input type="number" step="0.1" name="bed_head_elevation_degrees" class="form-control form-control-sm" required value="28.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phút Từ Lần Xoay Trước *</label>
                                        <input type="number" name="last_repositioning_elapsed_minutes" class="form-control form-control-sm" required value="85">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning text-dark btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Phân Tích Nguy Cơ Loét Tì Đè Tự Động
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-hospital me-2 text-warning"></i>Theo Dõi Giường Bệnh Ngừa Loét Tì Đè</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 giường gần nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân</th>
                                        <th>Điểm Braden</th>
                                        <th>Nguy Cơ Loét</th>
                                        <th>Áp Lực Cùng / Gót</th>
                                        <th>Đầu Giường (°)</th>
                                        <th>Nhắc Xoay Trở</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_braden'] ?? [] as $bd)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $bd->patient->full_name ?? 'BN #' . $bd->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Mã: {{ $bd->patient->patient_code ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold fs-6">{{ $bd->total_braden_score }}</span> / 23
                                            </td>
                                            <td>
                                                @if($bd->decubitus_ulcer_risk_level === 'very_high_risk' || $bd->decubitus_ulcer_risk_level === 'high_risk')
                                                    <span class="badge bg-danger text-white">Nguy Cơ Cao</span>
                                                @elseif($bd->decubitus_ulcer_risk_level === 'moderate_risk')
                                                    <span class="badge bg-warning text-dark">Nguy Cơ Trung Bình</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Nguy Cơ Thấp</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $bd->sacrum_peak_pressure_mmhg }}</span> / {{ $bd->heels_peak_pressure_mmhg }} mmHg
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $bd->bed_head_elevation_degrees }}°</span>
                                            </td>
                                            <td>
                                                @if($bd->turn_reminder_due_alert)
                                                    <span class="badge bg-danger text-white animate__animated animate__flash"><i class="bi bi-alarm-fill me-1"></i>ĐẾN GIỜ XOAY</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">{{ $bd->last_repositioning_elapsed_minutes }}p trước</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có giường bệnh nào được đánh giá Braden.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Ngân Hàng Sữa Mẹ Tiệt Trùng DHMB -->
        <div class="tab-pane fade" id="milk-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cup-hot me-2 text-success"></i>Kiểm Định Mẻ Sữa Mẹ Tiệt Trùng (DHMB)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.patient-safety-pediatric.milk-bank') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Mẻ Sữa (Batch Code) *</label>
                                        <input type="text" name="milk_batch_code" class="form-control form-control-sm" required value="DHMB-BATCH-{{ rand(1000, 9999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Người Hiến Ẩn Danh *</label>
                                        <input type="text" name="anonymous_donor_code" class="form-control form-control-sm" required value="DONOR-H{{ rand(100, 999) }}">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">KTV Ngân Hàng Sữa *</label>
                                        <input type="number" name="dhmb_technician_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thể Tích Mẻ Sữa (mL) *</label>
                                        <input type="number" step="0.1" name="batch_volume_ml" class="form-control form-control-sm" required value="1600.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Năng Lượng (kcal/100mL) *</label>
                                        <input type="number" step="0.1" name="energy_kcal_per_100ml" class="form-control form-control-sm" required value="68.5">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Đạm (g/100mL) *</label>
                                        <input type="number" step="0.01" name="protein_grams_per_100ml" class="form-control form-control-sm" required value="1.28">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Chất Béo (g/100mL) *</label>
                                        <input type="number" step="0.1" name="fat_grams_per_100ml" class="form-control form-control-sm" required value="3.9">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Trạng Thái Mẻ Sữa *</label>
                                    <select name="milk_batch_status" class="form-select form-select-sm" required>
                                        <option value="pasteurized_approved" selected>Đã thanh trùng Holder & Đạt vô khuẩn (Sẵn sàng)</option>
                                        <option value="dispensed_to_nicu">Đã cấp phát cho trẻ sinh non NICU</option>
                                        <option value="quarantine_testing">Đang cách ly chờ kết quả cấy vi sinh</option>
                                        <option value="discarded">Hủy bỏ do cấy vi sinh dương tính</option>
                                    </select>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-thermometer-high text-success me-1"></i>Thanh Trùng Pasteur 62.5°C Trong 30 Phút</h6>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="pre_pasteurization_culture_passed" value="1" id="preCultureSwitch" checked>
                                        <label class="form-check-label small" for="preCultureSwitch">Cấy khuẩn trước thanh trùng đạt ngưỡng vi sinh cho phép</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="post_pasteurization_culture_sterile" value="1" id="postCultureSwitch" checked>
                                        <label class="form-check-label small text-success fw-bold" for="postCultureSwitch">Cấy khuẩn sau thanh trùng 100% vô khuẩn (Sterile)</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Cấp Phép Mẻ Sữa Mẹ Cho Khoa NICU
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cup-hot me-2 text-success"></i>Mẻ Sữa Mẹ Tiệt Trùng Trong Kho Trữ Đông -20°C</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 mẻ mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Mẻ & Thể Tích</th>
                                        <th>Người Hiến</th>
                                        <th>Năng Lượng & Đạm</th>
                                        <th>Vi Sinh Sau Hấp</th>
                                        <th>Hạn Dùng Trữ Đông</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_milk_bank'] ?? [] as $mb)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $mb->milk_batch_code }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $mb->batch_volume_ml }} mL</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $mb->anonymous_donor_code }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ $mb->energy_kcal_per_100ml }} kcal</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Đạm: {{ $mb->protein_grams_per_100ml }} g%</div>
                                            </td>
                                            <td>
                                                @if($mb->post_pasteurization_culture_sterile)
                                                    <span class="badge bg-success text-white">100% Vô Khuẩn</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Nhiễm Khuẩn</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $mb->expiration_frozen_date ? date('d/m/Y', strtotime($mb->expiration_frozen_date)) : 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info text-capitalize">{{ str_replace('_', ' ', $mb->milk_batch_status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có mẻ sữa mẹ nào trong kho lưu trữ.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. An Toàn Bức Xạ Y Học Hạt Nhân & PET/CT -->
        <div class="tab-pane fade" id="nuc-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-radioactive me-2 text-primary"></i>Kiểm Soát Bức Xạ Đồng Vị Phóng Xạ Y Học Hạt Nhân</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.patient-safety-pediatric.nuclear') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Cán Bộ An Toàn Bức Xạ *</label>
                                        <input type="number" name="radiation_safety_officer_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Đồng Vị Phóng Xạ *</label>
                                        <select name="radiopharmaceutical" class="form-select form-select-sm" required>
                                            <option value="f18_fludeoxyglucose" selected>18F-FDG PET/CT (Ung thư)</option>
                                            <option value="tc99m_sestamibi">99mTc-Sestamibi SPECT (Tưới máu cơ tim)</option>
                                            <option value="i131_sodium_iodide">131I Sodium Iodide (K Tuyến giáp)</option>
                                            <option value="ga68_dotatate">68Ga-DOTATATE (U thần kinh nội tiết)</option>
                                            <option value="lu177_psma">177Lu-PSMA (K Tiền liệt tuyến)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Hoạt Độ Tiêm Ban Đầu (MBq) *</label>
                                        <input type="number" step="0.1" name="injected_activity_mbq" class="form-control form-control-sm" required value="260.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chu Kỳ Bán Rã (Phút) *</label>
                                        <input type="number" step="0.1" name="isotope_half_life_minutes" class="form-control form-control-sm" required value="109.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Đã Trôi Qua (Phút)</label>
                                        <input type="number" step="0.1" name="elapsed_minutes" class="form-control form-control-sm" value="120.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Kế Nhân Viên (µSv/h) *</label>
                                        <input type="number" step="0.1" name="staff_personal_dosimeter_rate_usv_h" class="form-control form-control-sm" required value="3.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Suất Liều Bề Mặt BN (µSv/h) *</label>
                                        <input type="number" step="0.1" name="patient_surface_radiation_rate_usv_h" class="form-control form-control-sm" required value="16.5">
                                    </div>
                                </div>
                                <div class="form-text text-muted mb-3" style="font-size: 0.75rem;">Theo tiêu chuẩn an toàn IAEA: Suất liều đo tại 1 mét < 20 µSv/h mới đủ điều kiện xuất viện về cộng đồng.</div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Tính Phân Rã Bức Xạ & Đánh Giá Tiêu Chuẩn Xuất Viện
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-radioactive me-2 text-primary"></i>Hồ Sơ An Toàn Phóng Xạ Bệnh Nhân Y Học Hạt Nhân</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Thuốc</th>
                                        <th>Hoạt Độ Tiêm</th>
                                        <th>Hoạt Độ Tồn Dư</th>
                                        <th>Liều Đo Bề Mặt</th>
                                        <th>Liều Kế NV</th>
                                        <th>Xuất Viện Về Nhà</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_nuclear'] ?? [] as $nc)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $nc->patient->full_name ?? 'BN #' . $nc->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $nc->radiopharmaceutical) }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $nc->injected_activity_mbq }} MBq</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-info">{{ $nc->decayed_residual_activity_mbq }} MBq</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning-subtle text-dark">{{ $nc->patient_surface_radiation_rate_usv_h }} µSv/h</span>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $nc->staff_personal_dosimeter_rate_usv_h }} µSv/h</span>
                                            </td>
                                            <td>
                                                @if($nc->safe_for_discharge_home)
                                                    <span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Đủ Điều Kiện</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Lưu Cách Ly</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có ca y học hạt nhân nào được ghi nhận.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Thử Nghiệm Lâm Sàng CTMS & IRB -->
        <div class="tab-pane fade" id="trial-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clipboard2-pulse me-2 text-info"></i>Quản Lý Thử Nghiệm Lâm Sàng & Đạo Đức Y Sinh (GCP)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.patient-safety-pediatric.trial') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Giao Thức (Protocol) *</label>
                                        <input type="text" name="protocol_code" class="form-control form-control-sm" required value="TA-CTMS-2026-ONCO-{{ rand(10, 99) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN Thử Nghiệm *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nghiên Cứu Viên Chính (PI) *</label>
                                        <input type="number" name="principal_investigator_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giai Đoạn (Phase) *</label>
                                        <select name="study_phase" class="form-select form-select-sm" required>
                                            <option value="phase_3" selected>Pha 3 - Thử nghiệm diện rộng</option>
                                            <option value="phase_1">Pha 1 - Độc tính liều đầu</option>
                                            <option value="phase_2">Pha 2 - Hiệu quả lâm sàng</option>
                                            <option value="phase_4_post_market">Pha 4 - Giám sát hậu mại</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Đề Tài Nghiên Cứu *</label>
                                    <input type="text" name="trial_title" class="form-control form-control-sm" required value="Thử nghiệm ngẫu nhiên mù đôi thuốc đích phân tử ức chế Tyrosine Kinase thế hệ mới">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nhánh Ngẫu Nhiên Mù Đôi *</label>
                                        <select name="randomization_arm" class="form-select form-select-sm" required>
                                            <option value="investigational_drug_arm" selected>Nhánh thuốc nghiên cứu mới</option>
                                            <option value="active_comparator_arm">Nhánh thuốc đối chứng chuẩn</option>
                                            <option value="placebo_blinded_arm">Nhánh giả dược làm mù</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lần Tái Khám (Visit Số) *</label>
                                        <input type="number" name="study_visit_number" class="form-control form-control-sm" required value="3">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tỷ Lệ Tuân Thủ Phác Đồ (%) *</label>
                                        <input type="number" step="0.1" name="protocol_adherence_rate_pct" class="form-control form-control-sm" required value="98.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trạng Thái Đối Tượng *</label>
                                        <select name="subject_status" class="form-select form-select-sm" required>
                                            <option value="enrolled_active" selected>Đang tham gia thử nghiệm</option>
                                            <option value="completed_protocol">Hoàn tất nghiên cứu</option>
                                            <option value="withdrawn_consent">Rút khỏi nghiên cứu</option>
                                            <option value="discontinued_adverse_event">Ngừng do biến cố bất lợi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="electronic_informed_consent_signed" value="1" id="eConsentSwitch" checked>
                                        <label class="form-check-label small" for="eConsentSwitch">Đã ký chấp thuận có thông tin điện tử (e-Consent) hợp lệ</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input text-danger" type="checkbox" name="serious_adverse_event_sae_occurred" value="1" id="saeSwitch">
                                        <label class="form-check-label small text-danger fw-bold" for="saeSwitch">Xuất hiện biến cố bất lợi nghiêm trọng (SAE cần báo IRB 24h)</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu Hồ Sơ CTMS & Đánh Giá Chuẩn Thực Hành Tốt GCP
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clipboard2-check me-2 text-info"></i>Đối Tượng Tham Gia Thử Nghiệm Lâm Sàng</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Giao Thức & Bệnh Nhân</th>
                                        <th>Pha & Nhánh Mù</th>
                                        <th>Lần Khám (Visit)</th>
                                        <th>Tuân Thủ %</th>
                                        <th>e-Consent</th>
                                        <th>Biến Cố SAE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_trials'] ?? [] as $tr)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $tr->protocol_code }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $tr->patient->full_name ?? 'BN #' . $tr->patient_id }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary text-uppercase">{{ str_replace('_', ' ', $tr->study_phase) }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ str_replace('_', ' ', explode('_', $tr->randomization_arm)[0]) }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">Visit #{{ $tr->study_visit_number }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success">{{ $tr->protocol_adherence_rate_pct }}%</span>
                                            </td>
                                            <td>
                                                @if($tr->electronic_informed_consent_signed)
                                                    <span class="badge bg-success text-white">Đã Ký e-Consent</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Chưa Ký</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tr->serious_adverse_event_sae_occurred)
                                                    <span class="badge bg-danger text-white animate__animated animate__flash">BÁO CÁO SAE</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Không Có SAE</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có đối tượng thử nghiệm lâm sàng nào được ghi nhận.
                                            </td>
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
@endsection
