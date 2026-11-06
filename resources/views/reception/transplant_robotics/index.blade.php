@extends('layouts.admin')

@section('title', 'Điều Phối Ghép Tạng, Phẫu Thuật Robot Da Vinci & Phòng Mổ Hybrid')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-diagram-3 text-primary me-2"></i>Điều Phối Ghép Tạng, Phẫu Thuật Robot Da Vinci & Phòng Mổ Hybrid
            </h4>
            <p class="text-muted small mb-0">
                Wave 6 • Ghép tạng (Khớp kháng nguyên HLA, Cold Ischemia Time), Phẫu thuật Robot Da Vinci Xi (Vòng đời tay dao EndoWrist) & Phòng mổ Hybrid Đa năng (StealthStation TRE Navigation & CIN Risk)
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-robot me-1"></i> Surgical Tech 4.0 Active
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
                            <span class="text-muted small">Ca Ghép Tạng Thực Hiện</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['total_transplants'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
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
                            <span class="text-muted small">Mảnh Ghép Hoạt Động Tốt D30</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['successful_grafts'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-patch-check-fill fs-5"></i>
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
                            <span class="text-muted small">Phiên Phẫu Thuật Robot Da Vinci</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['robotic_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-controller fs-5"></i>
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
                            <span class="text-muted small">Tay Dao Robot Sắp Hết Hạn (&le;2)</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_instruments_warning'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="transplantTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="transplant-tab" data-bs-toggle="tab" data-bs-target="#transplant-pane" type="button" role="tab">
                <i class="bi bi-heart me-1"></i> 1. Điều Phối Ghép Tạng & Kháng Nguyên HLA
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="robot-tab" data-bs-toggle="tab" data-bs-target="#robot-pane" type="button" role="tab">
                <i class="bi bi-robot me-1"></i> 2. Phẫu Thuật Robot Da Vinci Xi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="hybrid-tab" data-bs-toggle="tab" data-bs-target="#hybrid-pane" type="button" role="tab">
                <i class="bi bi-aspect-ratio me-1"></i> 3. Phòng Mổ Hybrid & Navigation 3D
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="transplantTabContent">
        <!-- 1. Ghép Tạng Tab -->
        <div class="tab-pane fade show active" id="transplant-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-success"></i>Đăng Ký & Điều Phối Ca Ghép Tạng</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.transplant-robotics.transplant') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Người Nhận ID <span class="text-danger">*</span></label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phẫu Thuật Viên Trưởng <span class="text-danger">*</span></label>
                                        <input type="number" name="lead_surgeon_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tạng Ghép</label>
                                        <select name="organ_type" class="form-select form-select-sm">
                                            <option value="kidney">Thận (Kidney)</option>
                                            <option value="liver">Gan (Liver)</option>
                                            <option value="heart">Tim (Heart)</option>
                                            <option value="lung">Phổi (Lung)</option>
                                            <option value="cornea">Giác Mạc (Cornea)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nguồn Tạng</label>
                                        <select name="donor_type" class="form-select form-select-sm">
                                            <option value="living_related">Người Cho Sống Cùng Huyết Thống</option>
                                            <option value="living_unrelated">Người Cho Sống Không Cùng Huyết Thống</option>
                                            <option value="brain_dead_deceased">Người Cho Chết Não Hiến Tạng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Người Hiến</label>
                                        <input type="text" name="donor_code" class="form-control form-control-sm" required value="DONOR-BD-2026-08">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Lệch Kháng Nguyên HLA (0-6)</label>
                                        <input type="number" name="hla_mismatch_count" class="form-control form-control-sm" required value="1" min="0" max="6">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Crossmatch (Phản ứng chéo)</label>
                                        <select name="crossmatch_result" class="form-select form-select-sm">
                                            <option value="negative">Âm Tính (Negative - An toàn)</option>
                                            <option value="positive">Dương Tính (Positive - Chống chỉ định)</option>
                                            <option value="borderline">Nghi Ngờ (Borderline)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Thiếu Máu Lạnh (h)</label>
                                        <input type="number" step="0.5" name="cold_ischemia_time_hours" class="form-control form-control-sm" required value="6.5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Chức Năng Mảnh Ghép Sau 30 Ngày</label>
                                    <select name="graft_status_day30" class="form-select form-select-sm">
                                        <option value="functional_good">Hoạt Động Tốt (Functional Good)</option>
                                        <option value="delayed_graft_function">Chức Năng Chậm (Delayed Graft Function)</option>
                                        <option value="acute_rejection">Thải Ghép Cấp (Acute Rejection)</option>
                                        <option value="primary_non_function">Không Hoạt Động (Primary Non-function)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Lưu Hồ Sơ Điều Phối Ghép Tạng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-ul me-2 text-success"></i>Các Ca Ghép Tạng Mới Nhất</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Tạng Ghép</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Khớp HLA / Crossmatch</th>
                                            <th>Cold Ischemia</th>
                                            <th>Mảnh Ghép D30</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_transplants'] as $t)
                                        <tr>
                                            <td><strong>{{ strtoupper($t->organ_type) }}</strong><br><small class="text-muted">{{ $t->donor_code }}</small></td>
                                            <td>{{ $t->patient->full_name ?? 'ID #' . $t->patient_id }}</td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">Lệch {{ $t->hla_mismatch_count }}/6 HLA</span>
                                                <br>
                                                @if($t->crossmatch_result === 'negative')
                                                    <span class="badge bg-success">Âm Tính</span>
                                                @else
                                                    <span class="badge bg-danger">Dương Tính</span>
                                                @endif
                                            </td>
                                            <td>{{ $t->cold_ischemia_time_hours }} giờ</td>
                                            <td><span class="badge bg-primary">{{ $t->graft_status_day30 }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có hồ sơ ghép tạng nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Robot Da Vinci Tab -->
        <div class="tab-pane fade" id="robot-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-robot me-2 text-info"></i>Ghi Nhận Phẫu Thuật Robot & Giám Sát Tay Dao</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.transplant-robotics.robotic') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN <span class="text-danger">*</span></label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bác Sĩ Mổ Chính <span class="text-danger">*</span></label>
                                        <input type="number" name="surgeon_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Phẫu Thuật Robot</label>
                                    <input type="text" name="surgical_procedure_name" class="form-control form-control-sm" required value="Phẫu thuật cắt tuyến tiền liệt tận gốc bằng Robot Da Vinci">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Serial Hệ Thống Robot</label>
                                        <input type="text" name="robot_system_serial" class="form-control form-control-sm" required value="DAVINCI-XI-SN9928">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Điều Khiển Console (phút)</label>
                                        <input type="number" name="console_time_minutes" class="form-control form-control-sm" required value="110">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-danger">Số Lần Sử Dụng Còn Lại Của Tay Dao EndoWrist (0-10)</label>
                                    <input type="number" name="instrument_lifespan_remaining" class="form-control form-control-sm" required value="2" min="0" max="10">
                                    <small class="text-muted">Tay dao da Vinci giới hạn 10 ca mổ theo quy chuẩn FDA để đảm bảo an toàn tuyệt đối.</small>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm text-white w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu Hồ Sơ Phẫu Thuật Robot Da Vinci
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu me-2 text-info"></i>Các Phiên Mổ Robot Thực Hiện</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Phẫu Thuật</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Console Time</th>
                                            <th>Tay Dao EndoWrist</th>
                                            <th>Cảnh Báo Thu Hồi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_robotics'] as $r)
                                        <tr>
                                            <td><strong>{{ $r->surgical_procedure_name }}</strong><br><small class="text-muted">{{ $r->robot_system_serial }}</small></td>
                                            <td>{{ $r->patient->full_name ?? 'ID #' . $r->patient_id }}</td>
                                            <td>{{ $r->console_time_minutes }} phút</td>
                                            <td>Còn {{ $r->instrument_lifespan_remaining }} / 10 lượt</td>
                                            <td>
                                                @if($r->instrument_retirement_alert)
                                                    <span class="badge bg-danger">Khuyến Cáo Thay Mới</span>
                                                @else
                                                    <span class="badge bg-success">An Toàn</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có phiên mổ Robot nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Hybrid OR Tab -->
        <div class="tab-pane fade" id="hybrid-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-aspect-ratio me-2 text-primary"></i>Lập Kế Hoạch Can Thiệp Phòng Mổ Hybrid</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.transplant-robotics.hybrid-or') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN <span class="text-danger">*</span></label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Can Thiệp <span class="text-danger">*</span></label>
                                        <input type="number" name="primary_operator_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Loại Can Thiệp Hybrid</label>
                                        <select name="procedure_type" class="form-select form-select-sm">
                                            <option value="tavi_tavr">Thay Van Động Mạch Chủ TAVI</option>
                                            <option value="tevar_evar">Đặt Stent Grapht Động Mạch Chủ EVAR/TEVAR</option>
                                            <option value="cerebral_aneurysm_coiling">Thuyên Tắc Coil Phình Mạch Não</option>
                                            <option value="spine_navigation_fusion">Cố Định Cột Sống Bằng Định Vị 3D</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Công Nghệ Điều Hướng</label>
                                        <select name="navigation_modality" class="form-select form-select-sm">
                                            <option value="stealth_station_optical">StealthStation Quang Học (Optical)</option>
                                            <option value="artisan_angio_ct">Artisan Robotic Angio-CT</option>
                                            <option value="fusion_roadmapping">Hợp Nhất Hình Ảnh 3D Roadmapping</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Sai Số Định Vị TRE (mm) *</label>
                                        <input type="number" step="0.1" name="target_registration_error_mm" class="form-control form-control-sm" required value="0.9">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lượng Thuốc Cản Quang (mL) *</label>
                                        <input type="number" step="1" name="contrast_media_volume_ml" class="form-control form-control-sm" required value="65">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">eGFR Nền Của Bệnh Nhân (mL/min/1.73m²)</label>
                                    <input type="number" step="0.1" name="patient_baseline_egfr" class="form-control form-control-sm" value="55.0">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-geo-alt me-1"></i> Lưu Kế Hoạch Can Thiệp Hybrid OR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu-fill me-2 text-primary"></i>Kế Hoạch Phòng Mổ Hybrid Gần Nhất</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Can Thiệp</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Sai Số TRE</th>
                                            <th>Cản Quang (CIN Risk)</th>
                                            <th>Kết Quả</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_hybrid'] as $h)
                                        <tr>
                                            <td><strong>{{ strtoupper($h->procedure_type) }}</strong><br><small class="text-muted">{{ $h->navigation_modality }}</small></td>
                                            <td>{{ $h->patient->full_name ?? 'ID #' . $h->patient_id }}</td>
                                            <td><span class="badge bg-success">{{ $h->target_registration_error_mm }} mm</span></td>
                                            <td>{{ $h->contrast_media_volume_ml }} mL<br><span class="badge bg-warning text-dark">{{ $h->contrast_induced_nephropathy_risk }}</span></td>
                                            <td><span class="badge bg-primary">Thành Công</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có kế hoạch can thiệp Hybrid OR nào.</td></tr>
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
