@extends('layouts.admin')

@section('title', 'Phòng Cách Ly Áp Lực Dương Ghép Tủy, An Toàn X-Ray/MRI Bọc Chì & Phòng Sạch ISO')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-lock-fill text-warning me-2"></i>Phòng Cách Ly Ghép Tủy PE, An Toàn X-Ray/MRI Bọc Chì & Phòng Sạch ISO
            </h4>
            <p class="text-muted small mb-0">
                Wave 9 • Phòng áp lực dương bảo vệ PE (Protective Environment), Quản lý che chắn chì phòng CT/MRI & Khóa liên động phát tia, Nhà đại thể thông minh RFID, Chu chuyển phòng nội soi AER & Phòng sạch pha chế TPN ISO Class 5
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill">
                <i class="bi bi-radioactive me-1"></i> Biosecurity & High-Risk Facility Command Active
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
                            <span class="text-muted small">Phòng Ghép Tủy PE Đạt Chuẩn</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['compliant_pe_rooms'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-shield-fill-check fs-5"></i>
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
                            <span class="text-muted small">Phòng X-Ray/CT Đang Quét</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_scanning_rad'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
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
                            <span class="text-muted small">Kho Lạnh Thi Thể Sử Dụng</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['occupied_cold_units'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-snow fs-5"></i>
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
                            <span class="text-muted small">Phòng Nội Soi Sẵn Sàng</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['ready_endo_suites'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-camera-video-fill fs-5"></i>
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
                            <span class="text-muted small">Lô Thuốc TPN Vô Trùng ISO</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['sterile_certified_batches'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-capsule fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold" id="tab-pe-tab" data-bs-toggle="pill" data-bs-target="#tab-pe" type="button" role="tab">
                <i class="bi bi-shield-plus me-1"></i> 1. Phòng Áp Lực Dương Ghép Tủy
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-rad-tab" data-bs-toggle="pill" data-bs-target="#tab-rad" type="button" role="tab">
                <i class="bi bi-lightning-charge me-1"></i> 2. An Toàn Chì X-Ray/MRI
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-mort-tab" data-bs-toggle="pill" data-bs-target="#tab-mort" type="button" role="tab">
                <i class="bi bi-tags me-1"></i> 3. Nhà Đại Thể & Bệnh Phẩm
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-endo-tab" data-bs-toggle="pill" data-bs-target="#tab-endo" type="button" role="tab">
                <i class="bi bi-arrow-repeat me-1"></i> 4. Chu Chuyển Nội Soi AER
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold" id="tab-cleanroom-tab" data-bs-toggle="pill" data-bs-target="#tab-cleanroom" type="button" role="tab">
                <i class="bi bi-droplet-half me-1"></i> 5. Phòng Sạch TPN ISO Class 5
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Protective Cleanroom PE -->
        <div class="tab-pane fade show active" id="tab-pe" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-success"></i>Thiết Lập Phòng Ghép Tủy Áp Dương PE</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.specialty-high-risk-room.pe-cleanroom') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Áp Dương</label>
                                    <input type="text" name="room_code" class="form-control form-control-sm" placeholder="VD: PE-BMT-01, PE-STEM-02" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bệnh Nhân Ghép Tủy</label>
                                    <select name="patient_id" class="form-select form-select-sm" required>
                                        <option value="">-- Chọn bệnh nhân ghép tủy --</option>
                                        @foreach($stats['patients'] as $pat)
                                            <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->patient_code ?? 'BN-'.$pat->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Họ Tên Bệnh Nhân</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="Phạm Văn F" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Áp Suất Dương (Pa)</label>
                                        <input type="number" step="0.5" name="differential_pressure_pa" class="form-control form-control-sm" value="12.5" required title="Chuẩn JCI/CDC: > +8 Pa">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Lần Đổi Khí (ACH)</label>
                                        <input type="number" step="0.5" name="air_changes_per_hour" class="form-control form-control-sm" value="15.0" required title="Chuẩn >= 12 ACH">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Màng Lọc HEPA H14</label>
                                        <select name="hepa_filter_status" class="form-select form-select-sm" required>
                                            <option value="normal_high_efficiency">Hoạt động bình thường</option>
                                            <option value="maintenance_due">Sắp đến hạn kiểm định</option>
                                            <option value="replacement_required">Cần thay thế ngay</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cảnh Báo Cửa Đệm Mở</label>
                                        <select name="door_airlock_alarm" class="form-select form-select-sm">
                                            <option value="0">Cửa đóng kín an toàn</option>
                                            <option value="1">Cảnh báo cửa mở tụt áp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Bào Tử Nấm Aspergillus (/m³ khí)</label>
                                    <input type="number" step="0.01" name="aspergillus_spore_count" class="form-control form-control-sm" value="0.00" required title="Phải đạt 0.00 để không nhiễm nấm phổi">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> Lưu Kiểm Định Buồng Áp Dương PE
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-shaded me-2 text-success"></i>Giám Sát Khí Động Học & Vi Sinh Phòng PE</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Áp Suất & ACH</th>
                                            <th>HEPA & Bào Tử Nấm</th>
                                            <th>Tuân Thủ JCI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_pe_cleanrooms'] as $pe)
                                            <tr>
                                                <td class="fw-bold text-success">{{ $pe->room_code }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $pe->patient_name }}</div>
                                                    <span class="text-muted small">{{ $pe->patient->patient_code ?? 'BN-'.$pe->patient_id }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary">+{{ $pe->differential_pressure_pa }} Pa</div>
                                                    <div class="text-muted small">{{ $pe->air_changes_per_hour }} ACH (Chuẩn &ge; 12)</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $pe->hepa_filter_status }}</span>
                                                    <div class="text-muted small mt-1">Aspergillus: {{ $pe->aspergillus_spore_count }}</div>
                                                </td>
                                                <td>
                                                    @if($pe->safety_compliance === 'pe_compliant')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-shield-fill-check me-1"></i> Đạt Chuẩn Ghép Tủy
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $pe->safety_compliance }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có phòng cách ly áp lực dương nào.</td>
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

        <!-- 2. Lead-Lined Radiology Safety -->
        <div class="tab-pane fade" id="tab-rad" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning me-2 text-danger"></i>Cấu Hình An Toàn Cửa Chì Phòng Chẩn Đoán Hình Ảnh</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.specialty-high-risk-room.radiology-lead') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Chẩn Đoán</label>
                                    <input type="text" name="room_code" class="form-control form-control-sm" placeholder="VD: RAD-CT01, RAD-MRI3T" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Thiết Bị</label>
                                        <select name="modality_type" class="form-select form-select-sm" required>
                                            <option value="ct_scanner_multislice">Máy Chụp CT Đa Dãy</option>
                                            <option value="mri_3_tesla">Máy Chụp Cộng Hưởng Từ MRI 3T</option>
                                            <option value="digital_xray">X-Quang Kỹ Thuật Số DR</option>
                                            <option value="angiography_cath_lab">Can Thiệp Mạch DSA CathLab</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Đèn Cảnh Báo Phát Tia</label>
                                        <select name="radiation_warning_light" class="form-select form-select-sm" required>
                                            <option value="light_off_safe">Tắt Đèn - An Toàn</option>
                                            <option value="amber_prep">Vàng - Chuẩn Bị Quét</option>
                                            <option value="red_xray_emitting">Đỏ - ĐANG PHÁT TIA X</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cửa Chì Đóng Kín</label>
                                        <select name="lead_door_sealed" class="form-select form-select-sm">
                                            <option value="1">Đã đóng chặt 100%</option>
                                            <option value="0">Cửa hé mở rò rỉ tia</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Độ Dày Chì (mm Pb)</label>
                                        <input type="number" step="0.1" name="lead_glass_thickness_mm" class="form-control form-control-sm" value="2.5" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Báo Động Kim Loại MRI</label>
                                        <select name="ferromagnetic_mri_alarm" class="form-select form-select-sm">
                                            <option value="0">Không phát hiện kim loại</option>
                                            <option value="1">Cảnh báo kim loại từ tính!</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Khóa Liên Động</label>
                                        <select name="interlock_lock_engaged" class="form-select form-select-sm">
                                            <option value="1">Khóa điện từ kích hoạt</option>
                                            <option value="0">Chưa khóa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Suất Liều Bức Xạ Môi Trường (μSv/h)</label>
                                    <input type="number" step="0.01" name="ambient_radiation_dose_rate_usvh" class="form-control form-control-sm" value="0.12" required title="Chuẩn môi trường an toàn < 0.25 uSv/h">
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-shield-shaded me-1"></i> Thiết Lập An Toàn Phòng Bọc Chì
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-radioactive me-2 text-danger"></i>Trạng Thái Cửa Chì & Khóa Liên Động Phát Tia</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng</th>
                                            <th>Thiết Bị</th>
                                            <th>Đèn Báo Tia</th>
                                            <th>Cửa Chì & Từ Tính</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_radiology'] as $rad)
                                            <tr>
                                                <td class="fw-bold text-danger">{{ $rad->room_code }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $rad->modality_type }}</span></td>
                                                <td>
                                                    @if($rad->radiation_warning_light === 'red_xray_emitting')
                                                        <span class="badge bg-danger text-white animate__animated animate__flash animate__infinite">
                                                            <i class="bi bi-radioactive me-1"></i> ĐANG PHÁT TIA
                                                        </span>
                                                    @elseif($rad->radiation_warning_light === 'amber_prep')
                                                        <span class="badge bg-warning text-dark">Chuẩn Bị</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">An Toàn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>Cửa chì: {{ $rad->lead_glass_thickness_mm }}mm Pb</div>
                                                    @if($rad->ferromagnetic_mri_alarm)
                                                        <span class="badge bg-danger text-white">Báo Động Kim Loại</span>
                                                    @else
                                                        <span class="badge bg-light text-muted">Từ trường an toàn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('reception.specialty-high-risk-room.beam-emission', $rad->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Kích hoạt phát tia quét bệnh nhân">
                                                            <i class="bi bi-play-circle"></i> Quét Tia
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu phòng bọc chì chẩn đoán hình ảnh.</td>
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

        <!-- 3. Pathology Mortuary Storage -->
        <div class="tab-pane fade" id="tab-mort" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tags me-2 text-info"></i>Lưu Trữ Nhà Đại Thể & Phòng Bệnh Phẩm</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.specialty-high-risk-room.mortuary') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Hộc Lạnh / Trạm Pha</label>
                                    <input type="text" name="unit_code" class="form-control form-control-sm" placeholder="VD: COLD-01, DEEP-02, GROSS-STATION-A" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Loại Lưu Trữ</label>
                                        <select name="storage_type" class="form-select form-select-sm" required>
                                            <option value="standard_cold_4c">Lạnh Tiêu Chuẩn (+2°C đến +4°C)</option>
                                            <option value="deep_freeze_minus15c">Âm Sâu (-15°C)</option>
                                            <option value="pathology_grossing_station">Bàn Cắt Lọc Bệnh Phẩm</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Hồ Sơ Thi Hài</label>
                                        <input type="text" name="deceased_record_id" class="form-control form-control-sm" placeholder="DEC-2026-045">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Chip Vòng Tay RFID</label>
                                    <input type="text" name="rfid_tag_identifier" class="form-control form-control-sm" placeholder="RFID-MORT-883920">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Nhiệt Độ (°C)</label>
                                        <input type="number" step="0.5" name="temperature_celsius" class="form-control form-control-sm" value="3.5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Hơi Formalin (ppm)</label>
                                        <input type="number" step="0.01" name="formalin_vapor_ppm" class="form-control form-control-sm" value="0.04" required title="Chuẩn an toàn lao động < 0.1 ppm">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Hút Khí Cưỡng Bức</label>
                                        <select name="exhaust_ventilation_status" class="form-select form-select-sm" required>
                                            <option value="running_nominal">Hoạt động định mức</option>
                                            <option value="high_speed_purge">Hút xả khẩn cấp tốc độ cao</option>
                                            <option value="fan_failure_alert">Báo động hỏng quạt thông gió</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Trạng Thái Hộc</label>
                                        <select name="storage_status" class="form-select form-select-sm" required>
                                            <option value="vacant_sanitized">Trống & Đã Vệ Sinh</option>
                                            <option value="occupied_secured">Đang Lưu Giữ Thi Hài</option>
                                            <option value="grossing_active">Đang Pha Bệnh Phẩm</option>
                                            <option value="dispatched_funeral">Đã Bàn Giao Gia Đình</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Đối Soát Nhận Diện 2 Nhân Viên</label>
                                    <select name="identity_verified_by_two_staff" class="form-select form-select-sm">
                                        <option value="1">Đã kiểm tra chéo 2 người khớp RFID 100%</option>
                                        <option value="0">Chưa đối soát</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-semibold">
                                    <i class="bi bi-save me-1"></i> Lưu Dữ Liệu Nhà Đại Thể & Bệnh Phẩm
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-snow me-2 text-info"></i>Giám Sát Nhiệt Độ Hộc Lạnh & Nồng Độ Khí Formalin</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Hộc</th>
                                            <th>Nhiệt Độ</th>
                                            <th>Hơi Formalin</th>
                                            <th>RFID & Đối Soát</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_mortuary'] as $mort)
                                            <tr>
                                                <td class="fw-bold text-info">{{ $mort->unit_code }}</td>
                                                <td><i class="bi bi-thermometer-snow text-primary me-1"></i> {{ $mort->temperature_celsius }}°C</td>
                                                <td>
                                                    <span class="fw-bold {{ $mort->formalin_vapor_ppm <= 0.10 ? 'text-success' : 'text-danger' }}">
                                                        {{ $mort->formalin_vapor_ppm }} ppm
                                                    </span>
                                                    <div class="text-muted small">Quạt: {{ $mort->exhaust_ventilation_status }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $mort->rfid_tag_identifier ?? 'Chưa gắn' }}</span>
                                                    @if($mort->identity_verified_by_two_staff)
                                                        <div class="text-success small mt-1"><i class="bi bi-check-all"></i> Đã đối soát 2 người</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mort->storage_status === 'occupied_secured')
                                                        <span class="badge bg-primary-subtle text-primary border border-primary">Có Thi Hài</span>
                                                    @else
                                                        <span class="badge bg-light text-muted">{{ $mort->storage_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bản ghi nào tại nhà đại thể.</td>
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

        <!-- 4. Endoscopy Suite Turnover -->
        <div class="tab-pane fade" id="tab-endo" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-repeat me-2 text-primary"></i>Quản Lý Chu Chuyển Phòng Nội Soi & Máy Rửa AER</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.specialty-high-risk-room.endoscopy-turnover') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Nội Soi</label>
                                    <input type="text" name="suite_code" class="form-control form-control-sm" placeholder="VD: ENDO-01, ENDO-02" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Kỹ Thuật Soi</label>
                                        <select name="procedure_type" class="form-select form-select-sm" required>
                                            <option value="gastroscopy">Nội Soi Dạ Dày Thực Quản</option>
                                            <option value="colonoscopy">Nội Soi Đại Trực Tràng</option>
                                            <option value="bronchoscopy">Nội Soi Phế Quản Khí Đạo</option>
                                            <option value="ercp_biliary">Nội Soi Mật Tụy Ngược Dòng ERCP</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Seri Dây Soi Mềm</label>
                                        <input type="text" name="endoscope_serial_code" class="form-control form-control-sm" placeholder="OLY-GIF-HQ190" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Thời Gian Chu Chuyển (Phút)</label>
                                        <input type="number" name="turnaround_minutes" class="form-control form-control-sm" value="20" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Mã Máy Rửa AER</label>
                                        <input type="text" name="aer_reprocessor_id" class="form-control form-control-sm" value="AER-MEDIVATORS-01" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Test Axit Peracetic</label>
                                        <select name="peracetic_acid_test_passed" class="form-select form-select-sm">
                                            <option value="1">Đạt nồng độ diệt khuẩn</option>
                                            <option value="0">Không đạt nồng độ</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Sấy Khô Kênh Sinh Thiết</label>
                                        <select name="biopsy_channel_dried" class="form-select form-select-sm">
                                            <option value="1">Đã sấy khô bằng khí nén 70% cồn</option>
                                            <option value="0">Còn ẩm</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Nghiệm Thu Vô Trùng Dây Soi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Trạng Thái Phòng Nội Soi & Tiệt Khuẩn Dây Soi</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Phòng Soi</th>
                                            <th>Thủ Thuật</th>
                                            <th>Dây Soi & Máy Rửa AER</th>
                                            <th>Thời Gian Chuyển</th>
                                            <th>Sẵn Sàng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_endoscopy'] as $endo)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $endo->suite_code }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $endo->procedure_type }}</span></td>
                                                <td>
                                                    <div class="fw-semibold">{{ $endo->endoscope_serial_code }}</div>
                                                    <span class="text-muted small">Máy: {{ $endo->aer_reprocessor_id }}</span>
                                                </td>
                                                <td>{{ $endo->turnaround_minutes }} phút</td>
                                                <td>
                                                    @if($endo->readiness_status === 'suite_ready')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-check-lg me-1"></i> Sẵn Sàng Soi
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">Đang Rửa Khử Trùng</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu chu chuyển phòng nội soi.</td>
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

        <!-- 5. Cleanroom Pharmacy Compounding ISO Class 5 -->
        <div class="tab-pane fade" id="tab-cleanroom" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-droplet-half me-2 text-warning"></i>Kiểm Tra Buồng Sạch Pha Chế Thuốc ISO Class 5</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.specialty-high-risk-room.cleanroom-pharmacy') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Mã Phòng Sạch Dược</label>
                                    <input type="text" name="cleanroom_code" class="form-control form-control-sm" placeholder="VD: CLEAN-TPN-01, CLEAN-OPHTH-02" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Cấp Độ Sạch ISO</label>
                                        <select name="iso_classification" class="form-select form-select-sm" required>
                                            <option value="iso_class_5">ISO Class 5 (Class 100 - Pha Tiêm Tĩnh Mạch)</option>
                                            <option value="iso_class_7">ISO Class 7 (Class 10,000 - Buồng Đệm)</option>
                                            <option value="iso_class_8">ISO Class 8 (Khu Vực Chuẩn Bị)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Áp Suất Bậc Thang (Pa)</label>
                                        <input type="number" step="0.5" name="cascade_pressure_pa" class="form-control form-control-sm" value="25.0" required title="Chuẩn ISO 5: >= +20 Pa">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Số Hạt Bụi 0.5μm (/m³)</label>
                                        <input type="number" name="air_particle_count_0_5um" class="form-control form-control-sm" value="280" required title="Giới hạn ISO 5 <= 3,520 hạt/m³">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Air-Shower Khóa Cửa</label>
                                        <select name="air_shower_interlock_active" class="form-select form-select-sm">
                                            <option value="1">Khóa liên động Air-Shower Bật</option>
                                            <option value="0">Tắt</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Dạng Thuốc Vô Trùng Pha Chế</label>
                                    <select name="compounding_type" class="form-select form-select-sm" required>
                                        <option value="neonatal_tpn_nutrition">Dinh Dưỡng Tĩnh Mạch Hoàn Toàn TPN Sơ Sinh</option>
                                        <option value="ophthalmic_eye_drops">Thuốc Nhỏ Mắt Tra Nhãn Khoa Kháng Sinh</option>
                                        <option value="intrathecal_antibiotic">Kháng Sinh Tiêm Khoang Tủy Sống</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Dược Sĩ Phụ Trách Pha Chế</label>
                                    <input type="text" name="pharmacist_lead_name" class="form-control form-control-sm" placeholder="DS. Hoàng Văn G" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1"></i> Chứng Nhận Độ Vô Trùng Lô Thuốc
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-check-all me-2 text-warning"></i>Nhật Ký Đếm Hạt Bụi & Độ Vô Trùng Dược Phẩm</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted">
                                            <th>Mã Phòng</th>
                                            <th>Cấp ISO & Áp Suất</th>
                                            <th>Đếm Hạt Bụi 0.5μm</th>
                                            <th>Dạng Thuốc & Dược Sĩ</th>
                                            <th>Chứng Nhận</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_cleanroom_pharmacy'] as $clean)
                                            <tr>
                                                <td class="fw-bold text-warning">{{ $clean->cleanroom_code }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $clean->iso_classification }}</span>
                                                    <div class="text-primary small mt-1">+{{ $clean->cascade_pressure_pa }} Pa</div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold {{ $clean->air_particle_count_0_5um <= 3520 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($clean->air_particle_count_0_5um) }} hạt/m³
                                                    </span>
                                                    <div class="text-muted small">(Giới hạn &le; 3,520)</div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $clean->compounding_type }}</div>
                                                    <span class="text-muted small">DS: {{ $clean->pharmacist_lead_name }}</span>
                                                </td>
                                                <td>
                                                    @if($clean->batch_sterility_status === 'certified_sterile_pass')
                                                        <span class="badge bg-success-subtle text-success border border-success">
                                                            <i class="bi bi-patch-check-fill me-1"></i> Vô Trùng Tuyệt Đối
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">{{ $clean->batch_sterility_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có bản ghi phòng sạch pha chế thuốc vô trùng.</td>
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
