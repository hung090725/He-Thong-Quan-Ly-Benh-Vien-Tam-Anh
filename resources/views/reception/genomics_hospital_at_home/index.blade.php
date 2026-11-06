@extends('layouts.admin')

@section('title', 'Di Truyền NIPT/Onco, Biobank, Command Center, JCI & Bệnh Viện Tại Nhà HAH')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-dna text-primary me-2"></i>Di Truyền NIPT/Onco, Biobank, Command Center, JCI & Bệnh Viện Tại Nhà HAH
            </h4>
            <p class="text-muted small mb-0">
                Wave 6 • Oncogenetics & Prenatal NIPT, Biobank Tế bào gốc (-196°C), Hospital Bed Flow Command Center, Đánh giá tự động 83 Tiêu chí BYT / JCI, Kho Thuốc Giải Độc Hiếm Antidote & Hospital-at-Home (HAH Virtual Ward)
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-hospital me-1"></i> Smart Hospital Operations
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
                            <span class="text-muted small">Báo Cáo Di Truyền / NIPT</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total_genomics_reports'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-fingerprint fs-5"></i>
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
                            <span class="text-muted small">Mẫu Tế Bào Gốc Biobank</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['biobank_units_stored'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
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
                            <span class="text-muted small">Tiêu Chí BYT/JCI Đạt Chuẩn</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['accreditation_kpis_achieved'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-award-fill fs-5"></i>
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
                            <span class="text-muted small">Báo Động Thuốc Độc Hiếm</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['antidote_low_stock_alerts'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
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
                            <span class="text-muted small">Bệnh Nhân HAH Tại Nhà</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['virtual_ward_active_patients'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-house-heart-fill fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="ghahTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="genomics-tab" data-bs-toggle="tab" data-bs-target="#genomics-pane" type="button" role="tab">
                <i class="bi bi-dna me-1"></i> 1. Di Truyền & NIPT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="biobank-tab" data-bs-toggle="tab" data-bs-target="#biobank-pane" type="button" role="tab">
                <i class="bi bi-snow me-1"></i> 2. Biobank Tế Bào Gốc
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="command-tab" data-bs-toggle="tab" data-bs-target="#command-pane" type="button" role="tab">
                <i class="bi bi-bar-chart-steps me-1"></i> 3. Command Center Giường
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi-pane" type="button" role="tab">
                <i class="bi bi-award me-1"></i> 4. 83 Tiêu Chí BYT & JCI
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="antidote-tab" data-bs-toggle="tab" data-bs-target="#antidote-pane" type="button" role="tab">
                <i class="bi bi-capsule me-1"></i> 5. Thuốc Giải Độc Hiếm
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="vward-tab" data-bs-toggle="tab" data-bs-target="#vward-pane" type="button" role="tab">
                <i class="bi bi-house-heart me-1"></i> 6. Bệnh Viện Tại Nhà (HAH)
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="ghahTabContent">
        <!-- 1. Genomics Tab -->
        <div class="tab-pane fade show active" id="genomics-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-dna me-2 text-primary"></i>Kết Quả Xét Nghiệm Gen Đích & NIPT</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.genomics') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Di Truyền *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phân Loại Xét Nghiệm</label>
                                        <select name="test_category" class="form-select form-select-sm">
                                            <option value="somatic_cancer_ngs">Đột Biến Gen Khối U Somatic NGS</option>
                                            <option value="hereditary_cancer_risk">Nguy Cơ Ung Thư Di Truyền Germline</option>
                                            <option value="prenatal_nipt_cfdna">Sàng Lọc Tiền Sản NIPT cfDNA</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Loại Mẫu Bệnh Phẩm</label>
                                        <input type="text" name="sample_type" class="form-control form-control-sm" required value="Mô sinh thiết FFPE">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tỷ Lệ DNA Thai Nhi NIPT (%)</label>
                                        <input type="number" step="0.1" name="nipt_fetal_fraction_pct" class="form-control form-control-sm" value="8.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nguy Cơ Lệch Bội Thai</label>
                                        <select name="nipt_aneuploidy_risk" class="form-select form-select-sm">
                                            <option value="na">Không áp dụng (Ca Ung Thư)</option>
                                            <option value="low_risk">Nguy Cơ Thấp (Low Risk)</option>
                                            <option value="high_risk_trisomy_21">Nguy Cơ Cao Trisomy 21 (Down)</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Lưu Kết Quả & Khớp Thuốc Đích FDA
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Hồ Sơ Di Truyền Gần Nhất</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Phân Loại</th>
                                            <th>Thuốc Đích Khớp Được</th>
                                            <th>NIPT Risk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_genomics'] as $g)
                                        <tr>
                                            <td>{{ $g->patient->full_name ?? 'ID #' . $g->patient_id }}</td>
                                            <td><span class="badge bg-secondary">{{ $g->test_category }}</span></td>
                                            <td>{{ $g->fda_approved_targeted_therapies ?? 'Đang hội chẩn' }}</td>
                                            <td>{{ $g->nipt_aneuploidy_risk }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có bản ghi gen nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Biobank Tab -->
        <div class="tab-pane fade" id="biobank-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-snow me-2 text-success"></i>Lưu Trữ Tế Bào Gốc Biobank (-196°C)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.biobank') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mã BN ID *</label>
                                    <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Mẫu Tế Bào Gốc *</label>
                                        <input type="text" name="specimen_code" class="form-control form-control-sm" required value="STEM-{{ rand(1000,9999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nguồn Tế Bào Gốc</label>
                                        <select name="stem_cell_source" class="form-select form-select-sm">
                                            <option value="umbilical_cord_blood">Máu Cuống Rốn (Cord Blood)</option>
                                            <option value="bone_marrow">Tủy Xương (Bone Marrow)</option>
                                            <option value="adipose_tissue">Mô Mỡ Tự Thân (Adipose)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">TNC (x10^8 tế bào)</label>
                                        <input type="number" step="0.1" name="total_nucleated_cells_10e8" class="form-control form-control-sm" required value="12.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">CD34+ (x10^6)</label>
                                        <input type="number" step="0.1" name="cd34_positive_count_10e6" class="form-control form-control-sm" required value="5.8">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Độ Sống Tế Bào Viability (%) *</label>
                                    <input type="number" step="0.1" name="cell_viability_pct" class="form-control form-control-sm" required value="94.5">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Nhập Kho Tế Bào Gốc Biobank
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-snow2 me-2 text-success"></i>Mẫu Tế Bào Gốc Trong Kho Bảo Quản</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Mã Mẫu</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Nguồn Tế Bào</th>
                                            <th>Độ Sống (%)</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_biobank'] as $b)
                                        <tr>
                                            <td><strong>{{ $b->specimen_code }}</strong></td>
                                            <td>{{ $b->patient->full_name ?? 'ID #' . $b->patient_id }}</td>
                                            <td>{{ $b->stem_cell_source }}</td>
                                            <td><span class="badge bg-success">{{ $b->cell_viability_pct }}%</span></td>
                                            <td><span class="badge bg-primary">{{ $b->storage_status }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có mẫu tế bào gốc nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Bed Command Tab -->
        <div class="tab-pane fade" id="command-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu me-2 text-warning"></i>Cập Nhật Lưu Lượng Giường Bệnh Lâm Sàng</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.bed-command') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Khoa Lâm Sàng *</label>
                                    <input type="text" name="department_name" class="form-control form-control-sm" required value="Khoa Hồi Sức Tích Cực (ICU)">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tổng Giường Chỉ Tiêu *</label>
                                        <input type="number" name="total_licensed_beds" class="form-control form-control-sm" required value="40">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giường Đang Nằm *</label>
                                        <input type="number" name="occupied_beds" class="form-control form-control-sm" required value="36">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chờ Nhập Từ Cấp Cứu *</label>
                                        <input type="number" name="pending_admissions_er" class="form-control form-control-sm" required value="4">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Xuất Viện Hôm Nay *</label>
                                        <input type="number" name="confirmed_discharges_today" class="form-control form-control-sm" required value="3">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark">
                                    <i class="bi bi-speedometer2 me-1"></i> Tính Toán Dự Báo Gridlock & Giường Trống
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-display me-2 text-warning"></i>Hospital Command Center - Tình Trạng Các Khoa</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Khoa</th>
                                            <th>Lấp Đầy (%)</th>
                                            <th>Dự Báo Trống Chiều</th>
                                            <th>Cảnh Báo Quá Tải</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['capacity_commands'] as $c)
                                        <tr>
                                            <td><strong>{{ $c->department_name }}</strong></td>
                                            <td>{{ $c->bed_occupancy_rate_pct }}%</td>
                                            <td><span class="badge bg-info text-dark">{{ $c->predicted_available_beds_afternoon }} giường</span></td>
                                            <td>
                                                @if($c->capacity_alert_status === 'gridlock_code_purple')
                                                    <span class="badge bg-danger">Báo Động Tím (Gridlock)</span>
                                                @elseif($c->capacity_alert_status === 'busy_yellow')
                                                    <span class="badge bg-warning text-dark">Bận Rộn</span>
                                                @else
                                                    <span class="badge bg-success">Bình Thường</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu Command Center.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. KPI JCI Tab -->
        <div class="tab-pane fade" id="kpi-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-info"></i>Đánh Giá Chỉ Số Chất Lượng (83 Tiêu Chí BYT / JCI)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.kpi') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Chỉ Số *</label>
                                        <input type="text" name="kpi_code" class="form-control form-control-sm" required value="JCI-IPSG-{{ rand(10,99) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lĩnh Vực</label>
                                        <input type="text" name="domain_category" class="form-control form-control-sm" required value="An Toàn Người Bệnh">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Chỉ Số Chất Lượng *</label>
                                    <input type="text" name="kpi_title" class="form-control form-control-sm" required value="Tỷ Lệ Tuân Thủ Vệ Sinh Tay Của Nhân Viên Y Tế">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Ngưỡng Chuẩn</label>
                                        <input type="number" step="0.1" name="benchmark_target" class="form-control form-control-sm" required value="90.0">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Thực Tế Đạt</label>
                                        <input type="number" step="0.1" name="actual_value" class="form-control form-control-sm" required value="94.2">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Đơn Vị</label>
                                        <input type="text" name="metric_unit" class="form-control form-control-sm" required value="%">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm text-white w-100 fw-bold">
                                    <i class="bi bi-patch-check me-1"></i> Đánh Giá Mức Độ Tuân Thủ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-check2-all me-2 text-info"></i>Bảng Điểm Chất Lượng Bệnh Viện</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted mb-0">Hệ thống hỗ trợ tự động đối chiếu các tiêu chí chất lượng quốc tế JCI 7th Edition và Bộ 83 Tiêu chí chất lượng Bệnh viện Việt Nam ban hành kèm Quyết định 6858/QĐ-BYT.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Antidote Tab -->
        <div class="tab-pane fade" id="antidote-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-capsule me-2 text-danger"></i>Quản Lý Thuốc Giải Độc Hiếm (Orphan Antidote)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.antidote') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Thuốc Giải Độc *</label>
                                    <input type="text" name="antidote_name" class="form-control form-control-sm" required value="Intralipid 20%">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Chỉ Định Ngộ Độc *</label>
                                    <input type="text" name="indication_poison" class="form-control form-control-sm" required value="Ngộ độc thuốc tê toàn thân LAST (Local Anesthetic Systemic Toxicity)">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lọ Khả Dụng *</label>
                                        <input type="number" name="stock_vials_available" class="form-control form-control-sm" required value="4">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Ngưỡng An Toàn Tối Thiểu *</label>
                                        <input type="number" name="minimum_safety_threshold" class="form-control form-control-sm" required value="6">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Tủ Thuốc</label>
                                        <input type="text" name="storage_location_cabinet" class="form-control form-control-sm" required value="Tủ Trực Cấp Cứu A1">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Hạn Dùng Gần Nhất</label>
                                        <input type="date" name="earliest_expiry_date" class="form-control form-control-sm" required value="{{ date('Y-m-d', strtotime('+6 months')) }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-plus me-1"></i> Cập Nhật Kho Thuốc Giải Độc
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-danger"></i>Danh Mục Thuốc Giải Độc Cấp Cứu</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Thuốc Giải Độc</th>
                                            <th>Chỉ Định</th>
                                            <th>Tồn Kho / Ngưỡng</th>
                                            <th>Cảnh Báo Mua Bù</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_antidotes'] as $a)
                                        <tr>
                                            <td><strong>{{ $a->antidote_name }}</strong></td>
                                            <td>{{ $a->indication_poison }}</td>
                                            <td>{{ $a->stock_vials_available }} / {{ $a->minimum_safety_threshold }} lọ</td>
                                            <td>
                                                @if($a->immediate_procurement_alert)
                                                    <span class="badge bg-danger">Cần Mua Bù Khẩn</span>
                                                @else
                                                    <span class="badge bg-success">Đủ Dự Trữ</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có thông tin thuốc giải độc.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Virtual Ward HAH Tab -->
        <div class="tab-pane fade" id="vward-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-house-heart me-2 text-primary"></i>Đăng Ký Bệnh Nhân Hospital-at-Home (HAH)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.genomics-hospital-at-home.virtual-ward') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Phụ Trách *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Giường Ảo</label>
                                        <input type="text" name="virtual_bed_identifier" class="form-control form-control-sm" required value="HAH-BED-{{ rand(10,99) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chẩn Đoán HAH</label>
                                        <select name="admission_diagnosis" class="form-select form-select-sm">
                                            <option value="chf_decompensation_post_discharge">Suy Tim Sung Huyết Xuất Viện</option>
                                            <option value="copd_mild_exacerbation">Đợt Cấp COPD Mức Độ Nhẹ</option>
                                            <option value="cellulitis_iv_antibiotics">Viêm Mô Tế Bào Truyền Kháng Sinh</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Ngày Nhập Viện Ảo</label>
                                        <input type="date" name="virtual_admission_date" class="form-control form-control-sm" required value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Cân Nặng (kg)</label>
                                        <input type="number" step="0.1" name="current_weight_kg" class="form-control form-control-sm" required value="68.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">&Delta; Cân Nặng 3 Ngày</label>
                                        <input type="number" step="0.1" name="weight_change_3days_kg" class="form-control form-control-sm" value="2.3">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">SpO2 Tại Nhà (%)</label>
                                        <input type="number" step="0.1" name="latest_spo2_pct" class="form-control form-control-sm" required value="91.5">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Huyết Áp SBP</label>
                                        <input type="number" step="1" name="latest_sbp" class="form-control form-control-sm" required value="145">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Trạng Thái Giám Sát</label>
                                    <select name="virtual_ward_status" class="form-select form-select-sm">
                                        <option value="monitoring_active">Đang Giám Sát Từ Xa Tại Nhà</option>
                                        <option value="escalated_to_hospital_admission">Cần Chuyển Vào Viện</option>
                                        <option value="graduated_discharged">Đã Khỏi Xuất Viện</option>
                                    </select>
                                    <input type="hidden" name="latest_dbp" value="85">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-broadcast me-1"></i> Lưu & Kích Hoạt AI Giám Sát Mất Bù
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Bệnh Nhân Bệnh Viện Tại Nhà Đang Theo Dõi</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Giường Ảo</th>
                                            <th>Bệnh Nhân</th>
                                            <th>Cân Nặng / SpO2</th>
                                            <th>Báo Động Red Flag</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_virtual_ward'] as $vw)
                                        <tr>
                                            <td><strong>{{ $vw->virtual_bed_identifier }}</strong></td>
                                            <td>{{ $vw->patient->full_name ?? 'ID #' . $vw->patient_id }}</td>
                                            <td>{{ $vw->current_weight_kg }} kg (+{{ $vw->weight_change_3days_kg }}kg)<br><span class="badge bg-secondary">SpO2: {{ $vw->latest_spo2_pct }}%</span></td>
                                            <td>
                                                @if($vw->red_flag_alert_triggered)
                                                    <span class="badge bg-danger">BÁO ĐỘNG ĐỎ MẤT BÙ</span>
                                                @else
                                                    <span class="badge bg-success">Ổn Định</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary">{{ $vw->virtual_ward_status }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có bệnh nhân HAH nào.</td></tr>
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
