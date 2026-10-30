@extends('layouts.admin')

@section('title', 'Tiệt Trùng CSSD, Giảm Đau PCA, Hóa Trị Gravimetric, Thần Kinh IONM & LVAD')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-tools text-primary me-2"></i>Tiệt Trùng CSSD, Giảm Đau PCA, Hóa Trị, Thần Kinh IONM & LVAD
            </h4>
            <p class="text-muted small mb-0">
                Wave 8 • Bản sao số chuỗi tiệt khuẩn dụng cụ mổ CSSD, Bơm tiêm giảm đau tự kiểm soát PCA (ngừa suy hô hấp), Cân trọng khối hóa trị ung thư Gravimetric, Giám sát thần kinh trong mổ IONM & Thiết bị hỗ trợ thất trái LVAD
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                <i class="bi bi-shield-check me-1"></i> Surgical Precision & Chemotherapy Safety Active
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
                            <span class="text-muted small">Khay Dụng Cụ Tiệt Trùng CSSD</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total_cssd_trays'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-box-seam-fill fs-5"></i>
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
                            <span class="text-muted small">Bơm Giảm Đau PCA Hoạt Động</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['active_pca_pumps'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-sliders fs-5"></i>
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
                            <span class="text-muted small">Mẻ Pha Chế Hóa Trị Gravimetric</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['chemo_preparations'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-eyedropper fs-5"></i>
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
                            <span class="text-muted small">Ca Mổ Giám Sát Thần Kinh IONM</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['ionm_monitored_surgeries'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-lightning fs-5"></i>
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
                            <span class="text-muted small">Thiết Bị LVAD Theo Dõi</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['monitored_lvad_patients'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-heart-pulse fs-5"></i>
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
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="surgTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="cssd-tab" data-bs-toggle="tab" data-bs-target="#cssd-pane" type="button" role="tab">
                <i class="bi bi-box-seam me-1"></i> 1. Tiệt Trùng Dụng Cụ CSSD
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="pca-tab" data-bs-toggle="tab" data-bs-target="#pca-pane" type="button" role="tab">
                <i class="bi bi-sliders me-1"></i> 2. Bơm Giảm Đau PCA Thông Minh
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="chemo-tab" data-bs-toggle="tab" data-bs-target="#chemo-pane" type="button" role="tab">
                <i class="bi bi-eyedropper me-1"></i> 3. Pha Chế Hóa Trị Gravimetric
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="ionm-tab" data-bs-toggle="tab" data-bs-target="#ionm-pane" type="button" role="tab">
                <i class="bi bi-lightning me-1"></i> 4. Giám Sát Thần Kinh IONM
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="lvad-tab" data-bs-toggle="tab" data-bs-target="#lvad-pane" type="button" role="tab">
                <i class="bi bi-heart-pulse me-1"></i> 5. Hỗ Trợ Thất Trái LVAD
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="surgTabContent">
        <!-- 1. Tiệt Trùng Dụng Cụ CSSD -->
        <div class="tab-pane fade show active" id="cssd-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Quản Lý Chuỗi Khử Khuẩn Khay Dụng Cụ CSSD</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.surgical-chemo-device.cssd') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Vạch 2D Khay *</label>
                                        <input type="text" name="tray_barcode_2d" class="form-control form-control-sm" required value="CSSD-TRAY-OR-{{ rand(1000, 9999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kỹ Thuật Viên CSSD *</label>
                                        <input type="number" name="cssd_supervisor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên Bộ Khay Phẫu Thuật *</label>
                                    <input type="text" name="tray_name" class="form-control form-control-sm" required value="Bộ Đại Phẫu Khớp Gối Nhân Tạo Stryker Triathlon">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phương Pháp Tiệt Trùng *</label>
                                        <select name="sterilization_method" class="form-select form-select-sm" required>
                                            <option value="autoclave_steam_134c" selected>Hấp tiệt trùng hơi nước 134°C</option>
                                            <option value="hydrogen_peroxide_plasma">Plasma nhiệt độ thấp (Ống soi)</option>
                                            <option value="ethylene_oxide_eto">Khí ETO nhiệt độ thấp</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Buồng Hấp Số *</label>
                                        <input type="number" name="sterilizer_chamber_number" class="form-control form-control-sm" required value="2">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trạng Thái Khay *</label>
                                        <select name="tray_status" class="form-select form-select-sm" required>
                                            <option value="sterile_storage" selected>Kho vô khuẩn (Sẵn sàng)</option>
                                            <option value="dispatched_to_or">Đã cấp phát vào phòng mổ</option>
                                            <option value="used_in_surgery">Đã sử dụng trong ca mổ</option>
                                            <option value="decontamination_washing">Đang rửa khử nhiễm</option>
                                            <option value="in_sterilizer">Đang trong buồng hấp</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phòng Mổ Tiếp Nhận</label>
                                        <input type="text" name="assigned_operating_room" class="form-control form-control-sm" value="OR 05 - Hybrid">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-shield-check text-success me-1"></i>Kiểm Định Chất Lượng Tiệt Khuẩn</h6>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="chemical_integrator_passed" value="1" id="chemSwitch" checked>
                                        <label class="form-check-label small" for="chemSwitch">Chỉ thị hóa học Class 5 đổi màu đạt chuẩn</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="biological_spore_test_passed" value="1" id="bioSwitch" checked>
                                        <label class="form-check-label small" for="bioSwitch">Chỉ thị sinh học bào tử (Geobacillus) 100% âm tính</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Cập Nhật Trạng Thái Khay & Cấp Phép Vô Khuẩn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-primary"></i>Danh Sách Khay Dụng Cụ Vô Trùng CSSD Gần Đây</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 khay mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Khay & Tên Bộ Dụng Cụ</th>
                                        <th>Phương Pháp</th>
                                        <th>Chỉ Thị Hóa / Sinh</th>
                                        <th>Hạn Vô Khuẩn</th>
                                        <th>Trạng Thái</th>
                                        <th>Phòng Mổ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_cssd'] ?? [] as $cs)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $cs->tray_barcode_2d }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $cs->tray_name }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $cs->sterilization_method) }}</span>
                                            </td>
                                            <td>
                                                @if($cs->chemical_integrator_passed && $cs->biological_spore_test_passed)
                                                    <span class="badge bg-success text-white">Đạt Chuẩn Vô Trùng</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Không Đạt</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $cs->sterile_expiration_date ? date('d/m/Y', strtotime($cs->sterile_expiration_date)) : 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info text-capitalize">{{ str_replace('_', ' ', $cs->tray_status) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $cs->assigned_operating_room ?? 'Kho trung tâm' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có khay dụng cụ nào được tiệt trùng.
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

        <!-- 2. Bơm Giảm Đau PCA -->
        <div class="tab-pane fade" id="pca-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-success"></i>Cài Đặt & Giám Sát Bơm Giảm Đau PCA Thông Minh</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.surgical-chemo-device.pca') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Gây Mê *</label>
                                        <input type="number" name="anesthesiologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thuốc Giảm Đau Opioid *</label>
                                        <input type="text" name="opioid_agent" class="form-control form-control-sm" required value="Morphine Sulfate 1mg/mL">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Khóa Lockout (Phút) *</label>
                                        <input type="number" name="lockout_interval_minutes" class="form-control form-control-sm" required value="8">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Truyền Nền (mg/h) *</label>
                                        <input type="number" step="0.1" name="basal_infusion_rate_mg_or_mcg_per_h" class="form-control form-control-sm" required value="1.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Bolus Bệnh Nhân Bấm (mg) *</label>
                                        <input type="number" step="0.1" name="pca_bolus_dose_mg_or_mcg" class="form-control form-control-sm" required value="1.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giới Hạn Liều 4 Giờ (mg) *</label>
                                        <input type="number" step="0.1" name="four_hour_dose_limit" class="form-control form-control-sm" required value="20.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thang Điểm Đau VAS *</label>
                                        <select name="pain_score_vas_10" class="form-select form-select-sm" required>
                                            <option value="vas_0_2_mild">VAS 0-2 (Đau nhẹ)</option>
                                            <option value="vas_3_5_moderate" selected>VAS 3-5 (Đau vừa)</option>
                                            <option value="vas_6_8_severe">VAS 6-8 (Đau nhiều)</option>
                                            <option value="vas_9_10_worst">VAS 9-10 (Đau dữ dội)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tổng Số Lần Bấm Nút *</label>
                                        <input type="number" name="total_demands_count" class="form-control form-control-sm" required value="14">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Lần Bơm Thuốc Thực Nhận *</label>
                                        <input type="number" name="delivered_bolus_count" class="form-control form-control-sm" required value="10">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-lungs text-danger me-1"></i>Giám Sát An Toàn Hô Hấp Liên Tục</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">EtCO2 (mmHg) *</label>
                                            <input type="number" step="0.1" name="etco2_respiratory_monitoring_mmhg" class="form-control form-control-sm" required value="37.5">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">SpO2 (%) *</label>
                                            <input type="number" step="0.1" name="spo2_monitoring_pct" class="form-control form-control-sm" required value="98.5">
                                        </div>
                                    </div>
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Nếu EtCO2 > 50 mmHg hoặc SpO2 < 92%, bơm sẽ tự động khóa cứng để ngừa suy hô hấp.</div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Kích Hoạt Giám Sát An Toàn Bơm Giảm Đau PCA
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-success"></i>Bệnh Nhân Dùng Bơm Giảm Đau PCA Sau Mổ</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Thuốc</th>
                                        <th>Liều Nền / Bolus</th>
                                        <th>Bấm Yêu Cầu / Thực Nhận</th>
                                        <th>EtCO2 & SpO2</th>
                                        <th>Thang Đau VAS</th>
                                        <th>Khóa Do Suy Hô Hấp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_pca'] ?? [] as $pca)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $pca->patient->full_name ?? 'BN #' . $pca->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $pca->opioid_agent }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $pca->basal_infusion_rate_mg_or_mcg_per_h }}</span> / {{ $pca->pca_bolus_dose_mg_or_mcg }} mg
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $pca->total_demands_count }} lần</span> / {{ $pca->delivered_bolus_count }} liều
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $pca->etco2_respiratory_monitoring_mmhg }} mmHg</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">SpO2: {{ $pca->spo2_monitoring_pct }}%</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark-subtle text-dark text-capitalize">{{ str_replace('_', ' ', $pca->pain_score_vas_10) }}</span>
                                            </td>
                                            <td>
                                                @if($pca->respiratory_depression_lockout_active)
                                                    <span class="badge bg-danger text-white"><i class="bi bi-lock-fill me-1"></i>KHÓA BƠM</span>
                                                @else
                                                    <span class="badge bg-success text-white">Bình Thường</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có bệnh nhân nào cài đặt bơm giảm đau PCA.
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

        <!-- 3. Pha Chế Hóa Trị Gravimetric -->
        <div class="tab-pane fade" id="chemo-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-eyedropper me-2 text-info"></i>Pha Chế Thuốc Hóa Trị Cân Trọng Khối Gravimetric</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.surgical-chemo-device.chemo') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dược Sĩ Pha Chế *</label>
                                        <input type="number" name="pharmacist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Barcode Túi Thuốc *</label>
                                        <input type="text" name="preparation_barcode" class="form-control form-control-sm" required value="CHEMO-BAG-{{ rand(10000, 99999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tên Thuốc Hóa Trị *</label>
                                        <input type="text" name="chemo_drug_name" class="form-control form-control-sm" required value="Paclitaxel 175mg/m2">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Chỉ Định (mg) *</label>
                                        <input type="number" step="0.1" name="prescribed_dose_mg" class="form-control form-control-sm" required value="280.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dung Môi Pha Truyền *</label>
                                        <input type="text" name="carrier_diluent" class="form-control form-control-sm" required value="NaCl 0.9% 500mL">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khối Lượng Lý Thuyết (g) *</label>
                                        <input type="number" step="0.1" name="expected_total_weight_grams" class="form-control form-control-sm" required value="542.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khối Lượng Cân Thực Tế (g) *</label>
                                        <input type="number" step="0.1" name="measured_actual_weight_grams" class="form-control form-control-sm" required value="543.5">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="biosafety_cabinet_hepa_certified" value="1" id="hepaChemoSwitch" checked>
                                        <label class="form-check-label small" for="hepaChemoSwitch">Pha chế trong tủ an toàn sinh học Class II đạt kiểm định</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Xác Thực Trọng Lượng Gravimetric & Cấp Phát
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-eyedropper me-2 text-info"></i>Nhật Ký Pha Chế Thuốc Hóa Trị Ung Thư</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 mẻ mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Barcode & Bệnh Nhân</th>
                                        <th>Thuốc Hóa Trị</th>
                                        <th>Liều Lượng</th>
                                        <th>Trọng Lượng Đo</th>
                                        <th>Độ Sai Lệch %</th>
                                        <th>Kiểm Định</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_chemo'] ?? [] as $ch)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $ch->preparation_barcode }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $ch->patient->full_name ?? 'BN #' . $ch->patient_id }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger">{{ $ch->chemo_drug_name }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $ch->carrier_diluent }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $ch->prescribed_dose_mg }} mg</span>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $ch->measured_actual_weight_grams }} g</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Chuẩn: {{ $ch->expected_total_weight_grams }} g</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $ch->weight_deviation_pct }}%</span>
                                            </td>
                                            <td>
                                                @if($ch->gravimetric_check_passed)
                                                    <span class="badge bg-success text-white">Đạt Chuẩn (≤ 5%)</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Sai Lệch Liều</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có mẻ thuốc hóa trị nào được pha chế.
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

        <!-- 4. Giám Sát Thần Kinh IONM -->
        <div class="tab-pane fade" id="ionm-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning me-2 text-warning"></i>Ghi Nhận Tín Hiệu Thần Kinh Trong Mổ (IONM)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.surgical-chemo-device.ionm') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Sinh Lý Thần Kinh *</label>
                                        <input type="number" name="neurophysiologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Loại Phẫu Thuật *</label>
                                    <select name="surgical_type" class="form-select form-select-sm" required>
                                        <option value="scoliosis_spinal_deformity" selected>Phẫu thuật chỉnh hình vẹo cột sống</option>
                                        <option value="spinal_cord_tumor">Phẫu thuật bóc u tủy sống</option>
                                        <option value="thyroid_recurrent_nerve">Cắt giáp bảo tồn thần kinh quặt ngược</option>
                                        <option value="brainstem_craniotomy">Phẫu thuật vùng thân não</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Biên Độ MEP (µV) *</label>
                                        <input type="number" step="0.1" name="mep_amplitude_uv" class="form-control form-control-sm" required value="320.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mức Tụt Biên Độ MEP (%) *</label>
                                        <input type="number" step="0.1" name="mep_amplitude_drop_pct" class="form-control form-control-sm" required value="15.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Độ Trễ SSEP (ms) *</label>
                                        <input type="number" step="0.1" name="ssep_latency_ms" class="form-control form-control-sm" required value="22.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mức Tăng Độ Trễ SSEP (%) *</label>
                                        <input type="number" step="0.1" name="ssep_latency_increase_pct" class="form-control form-control-sm" required value="4.5">
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="emg_spontaneous_train_activity" value="1" id="emgSwitch">
                                        <label class="form-check-label small text-danger fw-bold" for="emgSwitch">Xuất hiện chuỗi phóng điện co cơ bất thường trên EMG</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning text-dark btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Cảnh Báo Tổn Thương Thần Kinh Thời Gian Thực
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-warning"></i>Ca Mổ Được Giám Sát Điện Sinh Lý Thần Kinh</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Phẫu Thuật</th>
                                        <th>Biên Độ MEP (µV)</th>
                                        <th>Tụt MEP %</th>
                                        <th>Độ Trễ SSEP %</th>
                                        <th>Báo Động Tổn Thương</th>
                                        <th>Xử Trí Kíp Mổ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_ionm'] ?? [] as $im)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $im->patient->full_name ?? 'BN #' . $im->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $im->surgical_type) }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $im->mep_amplitude_uv }} µV</span>
                                            </td>
                                            <td>
                                                @if($im->mep_amplitude_drop_pct >= 50)
                                                    <span class="badge bg-danger text-white">Tụt {{ $im->mep_amplitude_drop_pct }}%</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">{{ $im->mep_amplitude_drop_pct }}% (An toàn)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">+{{ $im->ssep_latency_increase_pct }}%</span>
                                            </td>
                                            <td>
                                                @if($im->neuromonitoring_alert_triggered)
                                                    <span class="badge bg-danger text-white animate__animated animate__flash">BÁO ĐỘNG THẦN KINH</span>
                                                @else
                                                    <span class="badge bg-success text-white">Ổn định</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 140px;" title="{{ $im->surgical_corrective_action_taken }}">
                                                    {{ $im->surgical_corrective_action_taken ?? 'Không cần can thiệp' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có ca mổ IONM nào được ghi nhận.
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

        <!-- 5. Hỗ Trợ Thất Trái LVAD -->
        <div class="tab-pane fade" id="lvad-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Theo Dõi Vận Hành Máy Hỗ Trợ Thất Trái LVAD</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.surgical-chemo-device.lvad') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Tim Mạch Suy Tim *</label>
                                        <input type="number" name="cardiologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Serial Máy LVAD *</label>
                                        <input type="text" name="lvad_serial_number" class="form-control form-control-sm" required value="HM3-SN-{{ rand(10000, 99999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tốc Độ Vòng Quay (RPM) *</label>
                                        <input type="number" name="pump_speed_rpm" class="form-control form-control-sm" required value="5400">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lưu Lượng Dòng Chảy (L/min) *</label>
                                        <input type="number" step="0.1" name="pump_flow_l_per_min" class="form-control form-control-sm" required value="4.8">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Công Suất Điện (Watts) *</label>
                                        <input type="number" step="0.1" name="pump_power_watts" class="form-control form-control-sm" required value="4.5">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chỉ Số Xung Lực (PI Target 2-6) *</label>
                                        <input type="number" step="0.1" name="pulsatility_index_pi" class="form-control form-control-sm" required value="4.2">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chỉ Số Chống Đông INR *</label>
                                        <input type="number" step="0.1" name="inr_anticoagulation_target" class="form-control form-control-sm" required value="2.5">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Phân Tích Nguy Cơ Huyết Khối / Dính Hút Vách Tim
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart me-2 text-danger"></i>Hệ Thống Theo Dõi Bệnh Nhân Cấy LVAD</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Serial Máy</th>
                                        <th>Tốc Độ & Dòng Chảy</th>
                                        <th>Công Suất (W)</th>
                                        <th>Chỉ Số PI</th>
                                        <th>Huyết Khối Cánh Bơm</th>
                                        <th>Hút Dính Vách Tim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_lvad'] ?? [] as $lv)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $lv->patient->full_name ?? 'BN #' . $lv->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">SN: {{ $lv->lvad_serial_number }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $lv->pump_speed_rpm }} RPM</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Flow: {{ $lv->pump_flow_l_per_min }} L/min</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $lv->pump_power_watts }} W</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">PI: {{ $lv->pulsatility_index_pi }}</span>
                                            </td>
                                            <td>
                                                @if($lv->pump_thrombosis_suspected)
                                                    <span class="badge bg-danger text-white">Nghi Huyết Khối</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">An Toàn</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lv->suction_event_detected)
                                                    <span class="badge bg-warning text-dark">Hút Dính Vách</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Bình Thường</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có bệnh nhân cấy máy LVAD nào.
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
