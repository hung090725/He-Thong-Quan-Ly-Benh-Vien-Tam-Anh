@extends('layouts.admin')

@section('title', 'Can Thiệp Chuyên Sâu, CIED Từ Xa, Robot Phục Hồi Chức Năng & Radar Kháng Sinh')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Can Thiệp Chuyên Sâu, CIED Tim Mạch, Robot PHCN & Radar Kháng Sinh
            </h4>
            <p class="text-muted small mb-0">
                Wave 7 • Nội soi cắt tách dưới niêm mạc ESD/POEM, Giám sát thiết bị tim mạch cấy ghép CIED từ xa, Bản đồ nhạy cảm kháng sinh Antibiogram CLSI M39 & Robot khung xương ngoài phục hồi vận động thần kinh
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-shield-check me-1"></i> Advanced Intervention & Patient Safety Active
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
                            <span class="text-muted small">Ca Nội Soi ESD / POEM</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total_esd_poem_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-camera-video fs-5"></i>
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
                            <span class="text-muted small">Cắt Trọn Khối R0 Triệt Căn</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['curative_r0_resections'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-patch-check-fill fs-5"></i>
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
                            <span class="text-muted small">Thiết Bị CIED Theo Dõi Từ Xa</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['monitored_cied_devices'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-broadcast fs-5"></i>
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
                            <span class="text-muted small">Báo Động Bão Điện Học VT/VF</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['electrical_storm_alerts'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
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
                            <span class="text-muted small">Phiên Robot Khung Xương PHCN</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['robotic_exoskeleton_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-person-walking fs-5"></i>
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
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="interventionTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="endo-tab" data-bs-toggle="tab" data-bs-target="#endo-pane" type="button" role="tab">
                <i class="bi bi-camera-video me-1"></i> 1. Nội Soi Can Thiệp ESD / POEM / ERCP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="cied-tab" data-bs-toggle="tab" data-bs-target="#cied-pane" type="button" role="tab">
                <i class="bi bi-broadcast me-1"></i> 2. Giám Sát Từ Xa CIED (ICD / Pacemaker)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="anti-tab" data-bs-toggle="tab" data-bs-target="#anti-pane" type="button" role="tab">
                <i class="bi bi-radar me-1"></i> 3. Bản Đồ Nhạy Cảm Antibiogram CLSI
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="exo-tab" data-bs-toggle="tab" data-bs-target="#exo-pane" type="button" role="tab">
                <i class="bi bi-robot me-1"></i> 4. Robot Khung Xương Ngoài PHCN
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="interventionTabContent">
        <!-- 1. Nội Soi Can Thiệp Nâng Cao -->
        <div class="tab-pane fade show active" id="endo-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-camera-video me-2 text-primary"></i>Đăng Ký Hồ Sơ Nội Soi Can Thiệp Tiêu Hóa</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.advanced-intervention.endoscopy') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bác Sĩ Nội Soi *</label>
                                        <input type="number" name="endoscopist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phân Loại Can Thiệp *</label>
                                        <select name="procedure_category" class="form-select form-select-sm" required>
                                            <option value="esd_endoscopic_submucosal_dissection">Cắt tách dưới niêm mạc ESD</option>
                                            <option value="poem_peroral_endoscopic_myotomy">Cắt cơ vòng thực quản qua miệng POEM</option>
                                            <option value="ercp_complex_biliary">Nội soi mật tụy ngược dòng ERCP phức tạp</option>
                                            <option value="eus_guided_drainage">Dẫn lưu nang tụy dưới siêu âm EUS</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Độ Khó Theo ASGE *</label>
                                        <select name="asge_difficulty_grade" class="form-select form-select-sm" required>
                                            <option value="grade_1_standard">Grade 1 - Tiêu Chuẩn</option>
                                            <option value="grade_2_advanced">Grade 2 - Nâng Cao</option>
                                            <option value="grade_3_expert" selected>Grade 3 - Chuyên Gia</option>
                                            <option value="grade_4_highly_complex">Grade 4 - Cực Kỳ Phức Tạp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Tổn Thương *</label>
                                        <input type="text" name="lesion_organ_location" class="form-control form-control-sm" required placeholder="Hang vị, Thân dạ dày, Đại tràng sigma...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kích Thước Tổn Thương (mm) *</label>
                                        <input type="number" step="0.1" name="lesion_size_mm" class="form-control form-control-sm" required value="28.5">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-shield-check me-1 text-success"></i>Chỉ Số Chất Lượng & Dự Phòng Biến Chứng</h6>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="en_bloc_resection_achieved" value="1" id="enBlocSwitch" checked>
                                        <label class="form-check-label small" for="enBlocSwitch">Cắt trọn khối En-bloc (1 mảnh nguyên vẹn)</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="r0_clear_margins_confirmed" value="1" id="r0Switch" checked>
                                        <label class="form-check-label small" for="r0Switch">R0 Diện cắt sạch mô bệnh học (Curative Resection)</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="prophylactic_pancreatic_stent_placed" value="1" id="stentSwitch">
                                        <label class="form-check-label small" for="stentSwitch">Đặt stent ống tụy dự phòng viêm tụy cấp sau ERCP</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="rectal_indomethacin_administered" value="1" id="indoSwitch" checked>
                                        <label class="form-check-label small" for="indoSwitch">Thuốc đặt hậu môn Indomethacin 100mg dự phòng PEP</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input text-danger" type="checkbox" name="perforation_or_bleeding_occurred" value="1" id="complicationSwitch">
                                        <label class="form-check-label small text-danger fw-bold" for="complicationSwitch">Có biến chứng thủng hoặc xuất huyết nội soi</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu Ca Can Thiệp Nội Soi & Đánh Giá Chất Lượng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Danh Sách Can Thiệp Nội Soi Tiêu Hóa Gần Đây</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân</th>
                                        <th>Thủ Thuật</th>
                                        <th>Vị Trí & Size</th>
                                        <th>Chỉ Số En-bloc / R0</th>
                                        <th>Độ Khó ASGE</th>
                                        <th>Biến Chứng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_endoscopy'] ?? [] as $endo)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $endo->patient->full_name ?? 'BN #' . $endo->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Mã: {{ $endo->patient->patient_code ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase">
                                                    {{ str_replace('_', ' ', explode('_', $endo->procedure_category)[0]) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $endo->lesion_organ_location }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $endo->lesion_size_mm }} mm</div>
                                            </td>
                                            <td>
                                                @if($endo->en_bloc_resection_achieved)
                                                    <span class="badge bg-success-subtle text-success me-1">En-bloc: Đạt</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning me-1">En-bloc: Mảnh vụn</span>
                                                @endif
                                                @if($endo->r0_clear_margins_confirmed)
                                                    <span class="badge bg-info-subtle text-info">R0 Sạch</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">R1 Còn U</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-dark-subtle text-dark text-capitalize">{{ str_replace('_', ' ', $endo->asge_difficulty_grade) }}</span>
                                            </td>
                                            <td>
                                                @if($endo->perforation_or_bleeding_occurred)
                                                    <span class="badge bg-danger text-white">Xuất Huyết / Thủng</span>
                                                @else
                                                    <span class="badge bg-success text-white">An Toàn</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có ca nội soi can thiệp nào được ghi nhận.
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

        <!-- 2. Giám Sát Từ Xa Thiết Bị CIED -->
        <div class="tab-pane fade" id="cied-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-broadcast me-2 text-danger"></i>Ghi Nhận Dữ Liệu Thiết Bị Tim Mạch CIED Từ Xa</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.advanced-intervention.cied') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Tim Mạch Can Thiệp *</label>
                                        <input type="number" name="cardiologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Loại Thiết Bị Cấy Ghép *</label>
                                        <select name="device_type" class="form-select form-select-sm" required>
                                            <option value="icd_implantable_defibrillator">Máy khử rung tim tự động ICD</option>
                                            <option value="crt_d_biventricular">Máy tái đồng bộ cơ tim CRT-D</option>
                                            <option value="pacemaker_single_dual">Máy tạo nhịp vĩnh viễn (1/2 buồng)</option>
                                            <option value="subcutaneous_icd">Máy khử rung dưới da S-ICD</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Serial Máy *</label>
                                        <input type="text" name="device_serial_number" class="form-control form-control-sm" required value="CIED-SN-{{ rand(10000, 99999) }}">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Lượng Pin Còn Lại (Năm) *</label>
                                        <input type="number" step="0.1" name="battery_longevity_remaining_years" class="form-control form-control-sm" required value="5.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trở Kháng Điện Cực (Ohms) *</label>
                                        <input type="number" name="lead_impedance_ohms" class="form-control form-control-sm" required value="480">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-activity me-1 text-danger"></i>Theo Dõi Loạn Nhịp Thất & Sốc Điện (30 Ngày Qua)</h6>
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <label class="form-label small">Số cơn VT/VF</label>
                                            <input type="number" name="ventricular_tachycardia_episodes_30d" class="form-control form-control-sm" value="0">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small">Sốc đúng chỉ định</label>
                                            <input type="number" name="shocks_delivered_appropriate" class="form-control form-control-sm" value="0">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small">Sốc không thích hợp</label>
                                            <input type="number" name="shocks_delivered_inappropriate" class="form-control form-control-sm" value="0">
                                        </div>
                                    </div>
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                                        Nếu có từ 3 cơn VT/VF trong 24-48h: Hệ thống tự động kích hoạt Cảnh báo Bão Điện Học (Electrical Storm).
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-broadcast-pin me-1"></i> Cập Nhật Dữ Liệu CIED & Kích Hoạt Phân Tích AI
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Hệ Thống Giám Sát Từ Xa CIED Trực Tuyến</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 thiết bị gần nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Máy</th>
                                        <th>Pin Còn Lại</th>
                                        <th>Trở Kháng Dây</th>
                                        <th>Cơn VT (30d)</th>
                                        <th>Bão Điện Học</th>
                                        <th>Cảnh Báo Dây</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_cied'] ?? [] as $cied)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $cied->patient->full_name ?? 'BN #' . $cied->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ strtoupper($cied->device_type) }} | SN: {{ $cied->device_serial_number }}</div>
                                            </td>
                                            <td>
                                                @if($cied->battery_longevity_remaining_years < 1.0)
                                                    <span class="badge bg-danger text-white"><i class="bi bi-battery-alert me-1"></i>{{ $cied->battery_longevity_remaining_years }} năm</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-battery-full me-1"></i>{{ $cied->battery_longevity_remaining_years }} năm</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $cied->lead_impedance_ohms }} Ω</span>
                                                @if($cied->lead_impedance_ohms < 200 || $cied->lead_impedance_ohms > 2000)
                                                    <div class="text-danger small"><i class="bi bi-exclamation-circle-fill"></i> Bất thường</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-dark-subtle text-dark">{{ $cied->ventricular_tachycardia_episodes_30d }} cơn</span>
                                            </td>
                                            <td>
                                                @if($cied->electrical_storm_alert)
                                                    <span class="badge bg-danger animate__animated animate__flash text-white">
                                                        <i class="bi bi-lightning-charge-fill me-1"></i>BÃỎ ĐIỆN HỌC
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Ổn định</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cied->lead_fracture_risk)
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-wrench me-1"></i>Nguy Cơ Gãy Dây</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Bình thường</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có thiết bị CIED nào gửi bản tin từ xa.
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

        <!-- 3. Radar Kháng Sinh Antibiogram CLSI M39 -->
        <div class="tab-pane fade" id="anti-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-radar me-2 text-info"></i>Cập Nhật Bản Đồ Vi Sinh & Kháng Thuốc (CLSI M39)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.advanced-intervention.antibiogram') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khoa Lâm Sàng *</label>
                                        <input type="text" name="department_name" class="form-control form-control-sm" required placeholder="ICU Hồi Sức Tích Cực, Ngoại Chấn Thương...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Nhiễm Khuẩn Bệnh Viện (HAI) *</label>
                                        <input type="text" name="hai_infection_site" class="form-control form-control-sm" required placeholder="HAP/VAP, Nhiễm trùng máu BSI, Vết mổ SSI...">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chủng Vi Sinh Vật *</label>
                                        <input type="text" name="pathogen_name" class="form-control form-control-sm" required placeholder="Klebsiella pneumoniae (KPC), Acinetobacter baumannii...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kháng Sinh Thử Nghiệm *</label>
                                        <input type="text" name="antibiotic_tested" class="form-control form-control-sm" required placeholder="Colistin, Meropenem, Vancomycin, Tigecycline...">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Lượng Mẫu Phân Lập (Isolates) *</label>
                                        <input type="number" name="total_isolates_tested" class="form-control form-control-sm" required value="45">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tỷ Lệ Nhạy Cảm (% S) *</label>
                                        <input type="number" step="0.1" name="susceptibility_pct" class="form-control form-control-sm" required value="82.5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mức Độ Đề Kháng *</label>
                                    <select name="resistance_classification" class="form-select form-select-sm" required>
                                        <option value="pan_susceptible">Pan-susceptible (Còn nhạy cảm tốt)</option>
                                        <option value="mdr_multidrug_resistant">MDR - Đa kháng thuốc (Multidrug-Resistant)</option>
                                        <option value="xdr_extensively_drug_resistant">XDR - Kháng thuốc diện rộng (Extensively Drug-Resistant)</option>
                                        <option value="pdr_pandrug_resistant">PDR - Toàn kháng thuốc (Pan-Drug Resistant / Siêu vi khuẩn)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-exclamation me-1"></i> Lưu Vào Bản Đồ Kháng Sinh & Giám Sát Nhiễm Khuẩn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-shaded me-2 text-info"></i>Dữ Liệu Radar Vi Sinh Bệnh Viện Đa Khoa Tâm Anh</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 mẫu mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Khoa & Vị Trí HAI</th>
                                        <th>Chủng Vi Sinh</th>
                                        <th>Kháng Sinh</th>
                                        <th>Mẫu & % Nhạy</th>
                                        <th>Phân Loại Kháng</th>
                                        <th>Báo Động Ổ Dịch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_antibiogram'] ?? [] as $anti)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $anti->department_name }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $anti->hai_infection_site }}</div>
                                            </td>
                                            <td>
                                                <span class="text-danger fw-bold fst-italic">{{ $anti->pathogen_name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $anti->antibiotic_tested }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $anti->susceptibility_pct }}%</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">(N = {{ $anti->total_isolates_tested }})</div>
                                            </td>
                                            <td>
                                                @if($anti->resistance_classification === 'pdr_pandrug_resistant')
                                                    <span class="badge bg-danger text-white">PDR Toàn Kháng</span>
                                                @elseif($anti->resistance_classification === 'xdr_extensively_drug_resistant')
                                                    <span class="badge bg-warning text-dark">XDR Kháng Diện Rộng</span>
                                                @elseif($anti->resistance_classification === 'mdr_multidrug_resistant')
                                                    <span class="badge bg-secondary text-white">MDR Đa Kháng</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Còn Nhạy Cảm</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($anti->outbreak_cluster_alert)
                                                    <span class="badge bg-danger text-white"><i class="bi bi-exclamation-diamond-fill me-1"></i>BÁO ĐỘNG Ổ DỊCH</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Kiểm soát an toàn</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có dữ liệu Antibiogram được phân lập.
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

        <!-- 4. Robot Khung Xương Ngoài Phục Hồi Chức Năng -->
        <div class="tab-pane fade" id="exo-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-robot me-2 text-warning"></i>Khởi Tạo Phiên Robot Khung Xương Ngoài (Exoskeleton)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.advanced-intervention.exoskeleton') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">KTV/BS PHCN *</label>
                                        <input type="number" name="therapist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thiết Bị Robot Exoskeleton *</label>
                                        <input type="text" name="exoskeleton_device_model" class="form-control form-control-sm" required value="EksoNR Robotic Gait Trainer">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Lượng Phiên Tập (Phút) *</label>
                                        <input type="number" name="session_duration_minutes" class="form-control form-control-sm" required value="45">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tổng Số Bước Đi *</label>
                                        <input type="number" name="total_steps_achieved" class="form-control form-control-sm" required value="850">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Quãng Đường Đi (Mét) *</label>
                                        <input type="number" step="0.1" name="distance_meters" class="form-control form-control-sm" required value="340.5">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mức Hỗ Trợ Trợ Lực (% Assistance) *</label>
                                        <input type="number" name="assistance_level_pct" class="form-control form-control-sm" required value="40">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chỉ Số Đối Xứng Dáng Đi (% GSI) *</label>
                                        <input type="number" step="0.1" name="gait_symmetry_index_pct" class="form-control form-control-sm" required value="88.5">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tầm Vận Động Hông (Hip ROM °)</label>
                                        <input type="number" step="0.1" name="hip_rom_degrees" class="form-control form-control-sm" value="32.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tầm Vận Động Gối (Knee ROM °)</label>
                                        <input type="number" step="0.1" name="knee_rom_degrees" class="form-control form-control-sm" value="55.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning text-dark btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu Phiên Tập Robot & Phân Tích Dáng Đi Thần Kinh
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-walking me-2 text-warning"></i>Nhật Ký Phục Hồi Vận Động Bằng Robot Exoskeleton</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 phiên gần nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Model Robot</th>
                                        <th>Thời Gian & Số Bước</th>
                                        <th>Quãng Đường</th>
                                        <th>Trợ Lực Robot</th>
                                        <th>Đối Xứng Dáng Đi</th>
                                        <th>Đánh Giá Lâm Sàng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_exoskeleton'] ?? [] as $exo)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $exo->patient->full_name ?? 'BN #' . $exo->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $exo->exoskeleton_device_model }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $exo->total_steps_achieved }} bước</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $exo->session_duration_minutes }} phút</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $exo->distance_meters }} m</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $exo->assistance_level_pct }}%"></div>
                                                </div>
                                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Hỗ trợ: {{ $exo->assistance_level_pct }}%</div>
                                            </td>
                                            <td>
                                                @if($exo->gait_symmetry_index_pct >= 85)
                                                    <span class="badge bg-success-subtle text-success">{{ $exo->gait_symmetry_index_pct }}% (Rất tốt)</span>
                                                @elseif($exo->gait_symmetry_index_pct >= 70)
                                                    <span class="badge bg-info-subtle text-info">{{ $exo->gait_symmetry_index_pct }}% (Khá)</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">{{ $exo->gait_symmetry_index_pct }}% (Lệch dáng đi)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 140px;" title="{{ $exo->therapist_clinical_notes }}">
                                                    {{ $exo->therapist_clinical_notes ?? 'Chưa ghi chú' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có phiên tập phục hồi chức năng Robot nào.
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
