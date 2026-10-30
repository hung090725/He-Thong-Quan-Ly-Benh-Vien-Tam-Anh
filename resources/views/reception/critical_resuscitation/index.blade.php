@extends('layouts.admin')

@section('title', 'Hồi Sức Tích Cực, Chống Sốc Huyết Động & Quản Lý Máu PBM')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-shaded text-danger me-2"></i>Hồi Sức Tích Cực, Chống Sốc Huyết Động & Quản Lý Máu PBM
            </h4>
            <p class="text-muted small mb-0">
                Wave 6 • Bơm tiêm điện thông minh DERS Guardrails, Ma trận tương kỵ Y-Site, Phác đồ truyền máu tối cấp MTP (TEG/ROTEM) & Quản lý máu bệnh nhân PBM (Cell Saver)
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-broadcast me-1"></i> ICU Smart Guardrails Active
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Bơm Tiêm DERS Đang Chạy</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['active_ders_pumps'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-speedometer2 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Cảnh Báo Chặn Vượt Trần Hard Limit</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['ders_violations'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-slash-circle-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Ca MTP Truyền Máu Tối Cấp Đang Kích Hoạt</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['active_mtp_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-droplet-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">Ca PBM / Cell Saver Hồi Phục Máu</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['pbm_cell_saver_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-recycle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="criticalTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="ders-tab" data-bs-toggle="tab" data-bs-target="#ders-pane" type="button" role="tab">
                <i class="bi bi-cpu me-1"></i> 1. Smart Infusion DERS Guardrail
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="ysite-tab" data-bs-toggle="tab" data-bs-target="#ysite-pane" type="button" role="tab">
                <i class="bi bi-shuffle me-1"></i> 2. Ma Trận Tương Kỵ Y-Site
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="mtp-tab" data-bs-toggle="tab" data-bs-target="#mtp-pane" type="button" role="tab">
                <i class="bi bi-droplet-half me-1"></i> 3. MTP & TEG/ROTEM Hồi Sức Sốc
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="pbm-tab" data-bs-toggle="tab" data-bs-target="#pbm-pane" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i> 4. Quản Lý Máu PBM & Cell Saver
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="criticalTabContent">
        <!-- 1. DERS Tab -->
        <div class="tab-pane fade show active" id="ders-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Thiết Lập Liều & Giới Hạn Guardrail DERS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation.ders') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mã Bệnh Nhân ID <span class="text-danger">*</span></label>
                                    <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Nhập Patient ID (VD: 1)">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Thiết Bị Bơm</label>
                                        <input type="text" name="pump_device_code" class="form-control form-control-sm" required value="PUMP-ICU-08">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Profile Khoa Phòng</label>
                                        <select name="care_unit_profile" class="form-select form-select-sm">
                                            <option value="icu_adult">ICU Người Lớn</option>
                                            <option value="nicu_pediatric">NICU Nhi Sơ Sinh</option>
                                            <option value="operating_room">Phòng Mổ Gây Mê</option>
                                            <option value="cardiology_ward">Nội Tim Mạch</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="form-label small fw-bold">Thuốc Nguy Cơ Cao</label>
                                        <input type="text" name="medication_name" class="form-control form-control-sm" required value="Noradrenaline">
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label small fw-bold">Nồng Độ Dung Dịch</label>
                                        <input type="text" name="concentration_unit" class="form-control form-control-sm" required value="4mg/50mL">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Liều Y Lệnh</label>
                                        <input type="number" step="0.01" name="dose_rate" class="form-control form-control-sm" required value="0.25">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Đơn Vị Liều</label>
                                        <input type="text" name="dose_unit" class="form-control form-control-sm" required value="mcg/kg/min">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Cân Nặng (kg)</label>
                                        <input type="number" step="0.1" name="patient_weight_kg" class="form-control form-control-sm" value="65.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trần Mềm (Soft Limit)</label>
                                        <input type="number" step="0.01" name="upper_soft_limit" class="form-control form-control-sm" value="0.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-danger">Trần Cứng (Hard Limit) *</label>
                                        <input type="number" step="0.01" name="upper_hard_limit" class="form-control form-control-sm" required value="1.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-check me-1"></i> Kích Hoạt Thiết Lập Bơm Tiêm DERS
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-primary"></i>Danh Sách Bơm Tiêm Điện Thông Minh Giám Sát</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Thiết Bị</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Thuốc & Nồng Độ</th>
                                            <th>Liều / mL/h</th>
                                            <th>Guardrail DERS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ders'] as $d)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $d->pump_device_code }}</span></td>
                                            <td>{{ $d->patient->full_name ?? 'ID #' . $d->patient_id }}</td>
                                            <td><strong>{{ $d->medication_name }}</strong><br><small class="text-muted">{{ $d->concentration_unit }}</small></td>
                                            <td>{{ $d->dose_rate }} {{ $d->dose_unit }}<br><span class="badge bg-info-subtle text-info">{{ $d->infusion_rate_ml_hr }} mL/h</span></td>
                                            <td>
                                                @if($d->hard_limit_violation)
                                                    <span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i> Hard Limit Blocked</span>
                                                @elseif($d->soft_limit_exceeded)
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Soft Override</span>
                                                @else
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Normal Safe</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có thiết lập bơm tiêm DERS nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Y-Site Tab -->
        <div class="tab-pane fade" id="ysite-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-search me-2 text-info"></i>Kiểm Tra Tương Kỵ Hai Thuốc Y-Site</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation.ysite-check') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Thuốc Truyền Chính (Line 1) <span class="text-danger">*</span></label>
                                    <input type="text" name="primary_drug_name" class="form-control form-control-sm" required placeholder="VD: Furosemide hoặc Ciprofloxacin">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Thuốc Truyền Phụ Chạc Y (Line 2) <span class="text-danger">*</span></label>
                                    <input type="text" name="secondary_drug_name" class="form-control form-control-sm" required placeholder="VD: Midazolam hoặc Vancomycin">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Dung Dịch Mang Truyền</label>
                                    <input type="text" name="carrier_solution" class="form-control form-control-sm" value="NaCl 0.9%">
                                </div>
                                <button type="submit" class="btn btn-info btn-sm text-white w-100 fw-bold">
                                    <i class="bi bi-check-all me-1"></i> Quét Ma Trận Tương Kỵ Trissel's 2026
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-info"></i>Lịch Sử Tra Cứu Tương Kỵ Gần Nhất</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Cặp Thuốc</th>
                                            <th>Kết Luận Tương Kỵ</th>
                                            <th>Khuyến Cáo Dược Lâm Sàng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ysite'] as $y)
                                        <tr>
                                            <td><strong>{{ $y->primary_drug_name }}</strong> + <span class="text-primary">{{ $y->secondary_drug_name }}</span></td>
                                            <td>
                                                @if($y->incompatibility_type === 'compatible')
                                                    <span class="badge bg-success">Tương hợp (Compatible)</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $y->incompatibility_type }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $y->recommended_action }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Chưa có tra cứu tương kỵ nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. MTP Tab -->
        <div class="tab-pane fade" id="mtp-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-droplet-half me-2 text-danger"></i>Kích Hoạt Phác Đồ Truyền Máu Tối Cấp MTP</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation.mtp') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN <span class="text-danger">*</span></label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bác Sĩ Chỉ Huy <span class="text-danger">*</span></label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mã Gói Kích Hoạt MTP</label>
                                    <input type="text" name="protocol_code" class="form-control form-control-sm" required value="MTP-TRAUMA-PACK-1">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Hồng Cầu (RBC)</label>
                                        <input type="number" name="rbc_units_issued" class="form-control form-control-sm" required value="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Huyết Tương (FFP)</label>
                                        <input type="number" name="ffp_units_issued" class="form-control form-control-sm" required value="4">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Tiểu Cầu (PLT)</label>
                                        <input type="number" name="platelet_doses_issued" class="form-control form-control-sm" required value="1">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">TEG R-Time (phút)</label>
                                        <input type="number" step="0.1" name="teg_r_time_minutes" class="form-control form-control-sm" value="11.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">TEG MA (mm)</label>
                                        <input type="number" step="0.1" name="teg_maximum_amplitude_mm" class="form-control form-control-sm" value="48.0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Trạng Thái MTP</label>
                                    <select name="mtp_status" class="form-select form-select-sm">
                                        <option value="in_progress">Đang Truyền Hồi Sức Tối Cấp (In Progress)</option>
                                        <option value="completed_hemostasis">Đã Cầm Máu Ổn Định (Completed)</option>
                                        <option value="patient_deceased">Tử Vong Không Hồi Phục</option>
                                    </select>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="tranexamic_acid_administered" value="1" id="txaCheck" checked>
                                    <label class="form-check-label small" for="txaCheck">Đã dùng Axit Tranexamic (TXA 1g bolus) chống tiêu sợi huyết</label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-broadcast me-1"></i> Lưu Đợt Truyền Máu MTP
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Gói MTP Đang Kích Hoạt & Cầm Máu</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Mã Gói</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Tỷ Lệ Máu (RBC:FFP:PLT)</th>
                                            <th>Cân Bằng 1:1:1</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_mtp'] as $m)
                                        <tr>
                                            <td><strong>{{ $m->protocol_code }}</strong></td>
                                            <td>{{ $m->patient->full_name ?? 'ID #' . $m->patient_id }}</td>
                                            <td>{{ $m->rbc_units_issued }} : {{ $m->ffp_units_issued }} : {{ $m->platelet_doses_issued }}</td>
                                            <td>
                                                @if($m->balanced_ratio_achieved)
                                                    <span class="badge bg-success">Chuẩn 1:1:1</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Chưa Cân Bằng</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary">{{ $m->mtp_status }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có ca truyền máu tối cấp MTP nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. PBM Tab -->
        <div class="tab-pane fade" id="pbm-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-recycle me-2 text-success"></i>Quản Lý Máu PBM & Máy Thu Hồi Máu Cell Saver</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation.pbm') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN <span class="text-danger">*</span></label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bác Sĩ <span class="text-danger">*</span></label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Hb Tiền Phẫu (g/dL)</label>
                                        <input type="number" step="0.1" name="preop_hemoglobin_g_dl" class="form-control form-control-sm" required value="10.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Ferritin (ng/mL)</label>
                                        <input type="number" step="1" name="preop_ferritin_ng_ml" class="form-control form-control-sm" value="28">
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="cell_saver_used" value="1" id="csCheck" checked>
                                    <label class="form-check-label small" for="csCheck">Sử dụng máy thu hồi & truyền máu hoàn hồi Cell Saver trong mổ</label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Lượng Máu Tự Thân Thu Hồi Được (mL)</label>
                                    <input type="number" step="10" name="autologous_blood_salvaged_ml" class="form-control form-control-sm" value="450">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Đơn Vị Máu Đồng Loại Phải Truyền Thêm</label>
                                    <input type="number" name="allogeneic_transfusion_units" class="form-control form-control-sm" value="0">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Lưu Hồ Sơ Quản Lý Máu PBM
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-shaded me-2 text-success"></i>Bảo Tồn Hồng Cầu & Hạn Chế Truyền Máu Đồng Loại</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Hb Tiền Phẫu</th>
                                            <th>Cell Saver Salvaged</th>
                                            <th>Máu Đồng Loại</th>
                                            <th>Đánh Giá PBM</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_pbm'] as $p)
                                        <tr>
                                            <td>{{ $p->patient->full_name ?? 'ID #' . $p->patient_id }}</td>
                                            <td><strong>{{ $p->preop_hemoglobin_g_dl }} g/dL</strong></td>
                                            <td><span class="badge bg-success">{{ $p->autologous_blood_salvaged_ml }} mL</span></td>
                                            <td>{{ $p->allogeneic_transfusion_units }} đơn vị</td>
                                            <td>
                                                @if($p->allogeneic_transfusion_units == 0 && $p->cell_saver_used)
                                                    <span class="badge bg-primary">Không Cần Máu Ngân Hàng</span>
                                                @else
                                                    <span class="badge bg-secondary">Đạt Tiêu Chuẩn PBM</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu quản lý máu PBM.</td></tr>
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
