@extends('layouts.admin')

@section('title', 'Ung Thư Chính Xác, Xạ Trị 4D Linac, CAR-T & Hội Đồng Phân Tử MTB')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-radioactive text-danger me-2"></i>Ung Thư Chính Xác, Xạ Trị 4D Linac, CAR-T & Hội Đồng Phân Tử MTB
            </h4>
            <p class="text-muted small mb-0">
                Wave 7 • Xạ trị gia tốc 4D-CT & DIBH nín thở bảo vệ tim, Liệu pháp tế bào CAR-T phân độ bão Cytokine (ASTCT), Hội đồng Ung bướu phân tử MTB, Phẫu thuật não khi tỉnh thức & Dược động học cá thể hóa Bayesian PK/PD
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-shield-shaded me-1"></i> Precision Oncology Active
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
                            <span class="text-muted small">Kế Hoạch Xạ Trị 4D Đang Chạy</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['active_radiation_plans'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-bullseye fs-5"></i>
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
                            <span class="text-muted small">Ca Truyền Tế Bào CAR-T</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['cart_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-capsule fs-5"></i>
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
                            <span class="text-muted small">Cảnh Báo Bão Cytokine Nặng</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['severe_crs_alerts'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
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
                            <span class="text-muted small">Hội Đồng MTB Phê Duyệt</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['mtb_approved_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-people-fill fs-5"></i>
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
                            <span class="text-muted small">Dược Động Học Đạt Đích</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['pkpd_target_achieved'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-graph-up fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="oncoTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="rad-tab" data-bs-toggle="tab" data-bs-target="#rad-pane" type="button" role="tab">
                <i class="bi bi-bullseye me-1"></i> 1. Xạ Trị 4D & DIBH
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart-pane" type="button" role="tab">
                <i class="bi bi-capsule me-1"></i> 2. Liệu Pháp Tế Bào CAR-T & CRS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="mtb-tab" data-bs-toggle="tab" data-bs-target="#mtb-pane" type="button" role="tab">
                <i class="bi bi-diagram-3 me-1"></i> 3. Hội Đồng MTB Phân Tử
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="awake-tab" data-bs-toggle="tab" data-bs-target="#awake-pane" type="button" role="tab">
                <i class="bi bi-brain me-1"></i> 4. Mổ Não Khi Tỉnh Thức
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="pkpd-tab" data-bs-toggle="tab" data-bs-target="#pkpd-pane" type="button" role="tab">
                <i class="bi bi-graph-up me-1"></i> 5. Bayesian PK/PD Kháng Sinh
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="oncoTabContent">
        <!-- 1. Xạ trị 4D -->
        <div class="tab-pane fade show active" id="rad-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-danger"></i>Lập Kế Hoạch Xạ Trị Linac & Nín Thở DIBH</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.oncology-advanced.radiation-plan') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Xạ Trị *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Kế Hoạch</label>
                                        <input type="text" name="plan_code" class="form-control form-control-sm" required value="RAD-LINAC-{{ rand(100,999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Khối U</label>
                                        <input type="text" name="cancer_site" class="form-control form-control-sm" required value="Ung thư vú trái giai đoạn IIA">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kỹ Thuật Xạ</label>
                                        <select name="radiation_technique" class="form-select form-select-sm">
                                            <option value="vmat">Điều Biến Thể Tích Cung Quay (VMAT)</option>
                                            <option value="imrt">Xạ Trị Điều Biến Liều (IMRT)</option>
                                            <option value="sbrt">Xạ Phẫu Định Vị Thân (SBRT)</option>
                                            <option value="srs">Xạ Phẫu Não Định Vị (SRS)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tổng Liều (Gy)</label>
                                        <input type="number" step="0.1" name="total_prescribed_dose_gy" class="form-control form-control-sm" required value="50.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Phân Liều (Fractions)</label>
                                        <input type="number" name="fraction_count" class="form-control form-control-sm" required value="25">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Mỗi Phân Liều (Gy)</label>
                                        <input type="number" step="0.1" name="dose_per_fraction_gy" class="form-control form-control-sm" required value="2.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Tim Heart Mean (Gy)</label>
                                        <input type="number" step="0.1" name="heart_mean_dose_gy" class="form-control form-control-sm" value="1.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Tủy Sống Max (Gy)</label>
                                        <input type="number" step="0.1" name="spinal_cord_max_dose_gy" class="form-control form-control-sm" value="22.5">
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="dibh_breath_hold_used" value="1" id="dibhCheck" checked>
                                    <label class="form-check-label small" for="dibhCheck">Kỹ thuật nín thở chủ động hít sâu (DIBH) bảo vệ tim</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="four_d_ct_motion_tracked" value="1" id="fdCheck" checked>
                                    <label class="form-check-label small" for="fdCheck">Theo dõi chuyển động hô hấp theo thời gian thực (4D-CT)</label>
                                </div>
                                <input type="hidden" name="treatment_status" value="in_treatment">
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-check me-1"></i> Thẩm Định Liều OAR & Lưu Kế Hoạch Xạ Trị
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-danger"></i>Kế Hoạch Xạ Trị Đang Thực Hiện</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Mã Kế Hoạch</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Tổng Liều / Phân Liều</th>
                                            <th>Kỹ Thuật</th>
                                            <th>Dung Nạp OAR</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_radiation'] as $r)
                                        <tr>
                                            <td><strong>{{ $r->plan_code }}</strong><br><small class="text-muted">{{ $r->cancer_site }}</small></td>
                                            <td>{{ $r->patient->full_name ?? 'ID #' . $r->patient_id }}</td>
                                            <td>{{ $r->total_prescribed_dose_gy }} Gy ({{ $r->fraction_count }} fractions)</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($r->radiation_technique) }}</span></td>
                                            <td>
                                                @if($r->oar_tolerance_verified)
                                                    <span class="badge bg-success">Đạt Chuẩn QUANTEC</span>
                                                @else
                                                    <span class="badge bg-danger">Vượt Ngưỡng</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có kế hoạch xạ trị nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CAR-T Tab -->
        <div class="tab-pane fade" id="cart-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-capsule me-2 text-success"></i>Giám Sát Truyền Tế Bào CAR-T & Phân Độ Bão Cytokine (CRS)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.oncology-advanced.cart') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Huyết Học *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Sản Phẩm CAR-T</label>
                                        <input type="text" name="cart_product_name" class="form-control form-control-sm" required value="Tisagenlecleucel (Kymriah)">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kháng Nguyên Đích</label>
                                        <input type="text" name="target_antigen" class="form-control form-control-sm" required value="CD19">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Thân Nhiệt Đỉnh (°C)</label>
                                        <input type="number" step="0.1" name="peak_temperature_c" class="form-control form-control-sm" required value="39.2">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Huyết Áp SBP</label>
                                        <input type="number" step="1" name="systolic_bp" class="form-control form-control-sm" required value="85">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">SpO2 (%)</label>
                                        <input type="number" step="1" name="spo2_pct" class="form-control form-control-sm" required value="92">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Độc Tính Thần Kinh ICANS</label>
                                        <select name="icans_neurotoxicity_grade" class="form-select form-select-sm">
                                            <option value="grade_0">Grade 0 (Bình thường)</option>
                                            <option value="grade_1">Grade 1 (Run nhẹ / Lơ mơ)</option>
                                            <option value="grade_2">Grade 2 (Lú lẫn trung bình)</option>
                                            <option value="grade_3">Grade 3 (Co giật / Hôn mê)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Đáp Ứng Lâm Sàng</label>
                                        <select name="therapy_outcome" class="form-select form-select-sm">
                                            <option value="monitoring">Đang Theo Dõi Cấp</option>
                                            <option value="complete_remission">Lui Bệnh Hoàn Toàn (CR)</option>
                                            <option value="partial_response">Đáp Ứng Một Phần (PR)</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-shaded me-1"></i> Tính Phân Độ ASTCT & Phác Đồ Tocilizumab
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-success"></i>Bệnh Nhân Điều Trị CAR-T & Cảnh Báo Bão Cytokine</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Sản Phẩm CAR-T</th>
                                            <th>Phân Độ CRS (ASTCT)</th>
                                            <th>Chỉ Định Tocilizumab</th>
                                            <th>Kết Quả</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_cart'] as $c)
                                        <tr>
                                            <td>{{ $c->patient->full_name ?? 'ID #' . $c->patient_id }}</td>
                                            <td><strong>{{ $c->cart_product_name }}</strong><br><small class="text-muted">{{ $c->target_antigen }}</small></td>
                                            <td>
                                                @if(in_array($c->astct_crs_grade, ['grade_3_vasopressors_high_o2', 'grade_4_mechanical_ventilation']))
                                                    <span class="badge bg-danger">{{ $c->astct_crs_grade }}</span>
                                                @elseif($c->astct_crs_grade === 'grade_2_hypotension_fluids')
                                                    <span class="badge bg-warning text-dark">{{ $c->astct_crs_grade }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $c->astct_crs_grade }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($c->tocilizumab_indicated)
                                                    <span class="badge bg-danger">KÍCH HOẠT TOCILIZUMAB</span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa Cần</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary">{{ $c->therapy_outcome }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có ca điều trị CAR-T nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. MTB Tab -->
        <div class="tab-pane fade" id="mtb-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill me-2 text-info"></i>Hội Đồng Ung Bướu Phân Tử (Molecular Tumor Board)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.oncology-advanced.mtb') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chủ Tịch Hội Đồng *</label>
                                        <input type="number" name="chairperson_doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mã Phiên Hội Đồng</label>
                                    <input type="text" name="board_session_code" class="form-control form-control-sm" required value="MTB-2026-{{ rand(10,99) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Chẩn Đoán Ung Thư</label>
                                    <input type="text" name="primary_cancer_diagnosis" class="form-control form-control-sm" required value="Ung thư đại trực tràng di căn gan kháng Oxaliplatin">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trạng Thái MSI</label>
                                        <select name="msi_status" class="form-select form-select-sm">
                                            <option value="msi_high">MSI-High (Vi Vệ Tinh Kém Ổn Định)</option>
                                            <option value="msi_stable">MSI-Stable (Ổn Định)</option>
                                            <option value="undetermined">Chưa Xác Định</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tải Lượng TMB</label>
                                        <input type="text" name="tumor_mutational_burden_tmb" class="form-control form-control-sm" value="16.5 mut/Mb">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm text-white w-100 fw-bold">
                                    <i class="bi bi-patch-check me-1"></i> Bỏ Phiếu Đồng Thuận Phác Đồ MTB
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-info"></i>Các Quyết Định Đồng Thuận Của Hội Đồng MTB</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Phiên MTB</th>
                                            <th>Bệnh Nhân</th>
                                            <th>MSI / TMB</th>
                                            <th>Quyết Định Hội Đồng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_mtb'] as $m)
                                        <tr>
                                            <td><strong>{{ $m->board_session_code }}</strong></td>
                                            <td>{{ $m->patient->full_name ?? 'ID #' . $m->patient_id }}</td>
                                            <td><span class="badge bg-secondary">{{ $m->msi_status }}</span></td>
                                            <td><span class="badge bg-success">{{ $m->mtb_consensus_decision }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có phiên hội đồng MTB nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Awake Craniotomy Tab -->
        <div class="tab-pane fade" id="awake-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-brain me-2 text-primary"></i>Phẫu Thuật Mở Hộp Sọ Khi Tỉnh Thức (Awake Craniotomy)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.oncology-advanced.awake-craniotomy') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phẫu Thuật Viên TK *</label>
                                        <input type="number" name="neurosurgeon_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Vị Trí U Não Chức Năng</label>
                                    <input type="text" name="tumor_eloquent_location" class="form-control form-control-sm" required value="Vùng vỏ não vận động ngôn ngữ Broca bán cầu ưu thế">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phương Pháp Vô Cảm</label>
                                        <select name="anesthesia_technique" class="form-select form-select-sm">
                                            <option value="asleep_awake_asleep_aaa">Ngủ - Tỉnh - Ngủ (AAA Technique)</option>
                                            <option value="monitored_anesthesia_care_mac">Gây Mê Giám Sát MAC</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dòng Kích Thích Vỏ Não (mA)</label>
                                        <input type="number" step="0.1" name="cortical_stimulation_current_ma" class="form-control form-control-sm" required value="2.5">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bờ An Toàn Đến Vùng Chức Năng (mm)</label>
                                        <input type="number" step="0.5" name="safety_margin_to_eloquent_cortex_mm" class="form-control form-control-sm" required value="12.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tỷ Lệ Cắt U (%)</label>
                                        <input type="number" step="0.5" name="extent_of_resection_pct" class="form-control form-control-sm" required value="95.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-activity me-1"></i> Ghi Nhận Giám Sát Vùng Vỏ Não Khi Thức Tỉnh
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Hồ Sơ Phẫu Thuật Não Khi Tỉnh Thức</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Vị Trí U Chức Năng</th>
                                            <th>Bờ An Toàn</th>
                                            <th>Tỷ Lệ Cắt Bỏ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_awake'] as $aw)
                                        <tr>
                                            <td>{{ $aw->patient->full_name ?? 'ID #' . $aw->patient_id }}</td>
                                            <td>{{ $aw->tumor_eloquent_location }}</td>
                                            <td><span class="badge bg-success">{{ $aw->safety_margin_to_eloquent_cortex_mm }} mm</span></td>
                                            <td><span class="badge bg-primary">{{ $aw->extent_of_resection_pct }}%</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có ca mổ não thức tỉnh nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Bayesian PK/PD Tab -->
        <div class="tab-pane fade" id="pkpd-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up me-2 text-warning"></i>Dược Động Học Cá Thể Hóa Mô Hình Bayesian (AUC/MIC)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.oncology-advanced.pkpd') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dược Sĩ Lâm Sàng *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kháng Sinh Tĩnh Mạch</label>
                                        <input type="text" name="antibiotic_name" class="form-control form-control-sm" required value="Vancomycin">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vi Khuẩn Gây Bệnh</label>
                                        <input type="text" name="pathogen_name" class="form-control form-control-sm" required value="MRSA (Tụ cầu vàng kháng Methicillin)">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nồng Độ MIC (mg/L) *</label>
                                        <input type="number" step="0.1" name="mic_mg_per_l" class="form-control form-control-sm" required value="1.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nồng Độ Đáy Đo Được (mg/L)</label>
                                        <input type="number" step="0.1" name="current_serum_trough_mg_per_l" class="form-control form-control-sm" value="15.2">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark">
                                    <i class="bi bi-calculator me-1"></i> Tính Toán Tỷ Lệ AUC24/MIC & Liều Tối Ưu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-warning"></i>Bảng Tối Ưu Hóa Liều Kháng Sinh Đa Kháng</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Kháng Sinh</th>
                                            <th>Bệnh Nhân</th>
                                            <th>AUC24/MIC</th>
                                            <th>Mục Tiêu Đạt Được</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_pkpd'] as $p)
                                        <tr>
                                            <td><strong>{{ $p->antibiotic_name }}</strong></td>
                                            <td>{{ $p->patient->full_name ?? 'ID #' . $p->patient_id }}</td>
                                            <td>{{ $p->auc24_over_mic_ratio }}</td>
                                            <td>
                                                @if($p->auc_target_achieved)
                                                    <span class="badge bg-success">Đạt Đích (400 - 600)</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Cần Hiệu Chỉnh</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có bản ghi PK/PD nào.</td></tr>
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
