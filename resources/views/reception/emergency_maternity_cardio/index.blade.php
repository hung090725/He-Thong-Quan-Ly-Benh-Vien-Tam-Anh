@extends('layouts.admin')

@section('title', 'Cấp Cứu Ngoại Viện 5G, Sản Nhi & Can Thiệp Tim Mạch - Đột Quỵ')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Cấp Cứu Ngoại Viện 5G, Sản Nhi & Can Thiệp Tim Mạch - Đột Quỵ
            </h4>
            <p class="text-muted small mb-0">
                Wave 6 • Tele-EMS 5G Mobile Pre-hospital, START Triage Thảm họa hàng loạt, Tim thai CTG AI & FIGO, Chấm điểm phôi IVF Gardner AI, Cảnh báo sớm Nhi PEWS, FFR/IVUS Can thiệp Mạch vành & Đột quỵ mở rộng DAWN/DEFUSE-3 (6-24h)
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-broadcast-pin me-1"></i> Rapid Critical Response
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
                            <span class="text-muted small">Tele-EMS 5G Đang Di Chuyển</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_tele_ems'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-truck fs-5"></i>
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
                            <span class="text-muted small">Thảm Họa Thẻ Đỏ (Red Tag)</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['mci_red_tags'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-tag-fill fs-5"></i>
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
                            <span class="text-muted small">CTG Tim Thai Bệnh Lý (Mổ Cấp)</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['ctg_pathological'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                            <i class="bi bi-activity fs-5"></i>
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
                            <span class="text-muted small">PEWS Nhi Cảnh Báo Code Blue</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['high_risk_pews'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-person-fill-exclamation fs-5"></i>
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
                            <span class="text-muted small">Đột Quỵ Đủ ĐK Can Thiệp 24h</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['eligible_stroke_thrombectomy'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-bezier2 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="emcTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tele-tab" data-bs-toggle="tab" data-bs-target="#tele-pane" type="button" role="tab">
                <i class="bi bi-broadcast me-1"></i> 1. Cấp Cứu 5G & START
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="maternity-tab" data-bs-toggle="tab" data-bs-target="#maternity-pane" type="button" role="tab">
                <i class="bi bi-gender-female me-1"></i> 2. Tim Thai CTG AI & IVF
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="pews-tab" data-bs-toggle="tab" data-bs-target="#pews-pane" type="button" role="tab">
                <i class="bi bi-emoji-smile me-1"></i> 3. PEWS Cảnh Báo Sớm Nhi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="cardio-tab" data-bs-toggle="tab" data-bs-target="#cardio-pane" type="button" role="tab">
                <i class="bi bi-heart me-1"></i> 4. FFR/IVUS Mạch Vành
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="stroke-tab" data-bs-toggle="tab" data-bs-target="#stroke-pane" type="button" role="tab">
                <i class="bi bi-brain me-1"></i> 5. Đột Quỵ Mở Rộng 6-24h
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="emcTabContent">
        <!-- 1. Tele-EMS & START -->
        <div class="tab-pane fade show active" id="tele-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-truck me-2 text-danger"></i>Cập Nhật Xe Cấp Cứu Ngoại Viện 5G (Tele-EMS)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.tele-ems') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Biển Hiệu Xe Cấp Cứu</label>
                                        <input type="text" name="ambulance_callsign" class="form-control form-control-sm" required value="AMB-5G-01">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kíp Cứu Thương</label>
                                        <input type="text" name="paramedic_in_charge" class="form-control form-control-sm" required value="BS. Trần Minh - ĐD. Huy">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Về Viện (ETA - phút)</label>
                                        <input type="number" step="0.5" name="eta_hospital_minutes" class="form-control form-control-sm" required value="12.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mục Tiêu Kích Hoạt Trước</label>
                                        <select name="pre_activation_target" class="form-select form-select-sm">
                                            <option value="cathlab_stemi">Phòng Can Thiệp Mạch Vành STEMI</option>
                                            <option value="stroke_team">Đội Cấp Cứu Đột Quỵ (Stroke Team)</option>
                                            <option value="trauma_or">Phòng Mổ Cấp Cứu Đa Chấn Thương</option>
                                            <option value="icu">Khoa Hồi Sức Tích Cực (ICU)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">SpO2 (%)</label>
                                        <input type="number" step="1" name="spo2" class="form-control form-control-sm" value="95">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Huyết Áp SBP</label>
                                        <input type="number" step="1" name="sbp" class="form-control form-control-sm" value="130">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Trạng Thái</label>
                                        <select name="mission_status" class="form-select form-select-sm">
                                            <option value="transporting">Đang Chuyển Viện</option>
                                            <option value="arrived_hospital">Đã Về Đến Viện</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-broadcast me-1"></i> Báo Động Kíp Bệnh Viện Đón Tiếp
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3-fill me-2 text-warning"></i>Phân Loại START Thảm Họa Hàng Loạt (MCI)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.mci-triage') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tên Thảm Họa</label>
                                        <input type="text" name="incident_name" class="form-control form-control-sm" required value="Tai Nạn Giao Thông Liên Hoàn Cao Tốc">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Thảm Họa</label>
                                        <input type="text" name="disaster_code" class="form-control form-control-sm" required value="MCI-2026-01">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Thẻ Nạn Nhân Tag ID *</label>
                                        <input type="text" name="victim_tag_number" class="form-control form-control-sm" required value="VIC-{{ rand(100,999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Người Phân Loại</label>
                                        <input type="text" name="triaged_by" class="form-control form-control-sm" required value="BS. Cấp Cứu Thảm Họa">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Tự Đi Lại?</label>
                                        <select name="can_walk" class="form-select form-select-sm">
                                            <option value="0">Không Thể Đi</option>
                                            <option value="1">Có Thể Đi Lại</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Nhịp Thở /phút</label>
                                        <input type="number" name="respiratory_rate" class="form-control form-control-sm" value="32">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Mạch Quay & Tri Giác</label>
                                        <select name="mental_status" class="form-select form-select-sm">
                                            <option value="follows_commands">Theo Y Lệnh</option>
                                            <option value="does_not_follow">Không Theo Y Lệnh</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark">
                                    <i class="bi bi-tag-fill me-1"></i> Tính Toán START Triage Phân Vùng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Maternity CTG & IVF -->
        <div class="tab-pane fade" id="maternity-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-primary"></i>Phân Tích Tim Thai Trung Tâm CTG AI (FIGO & ACOG)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.ctg') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Sản Phụ ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Sản Phụ Khoa *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phòng Sinh / Giường</label>
                                        <input type="text" name="delivery_room_bed" class="form-control form-control-sm" required value="Phòng Sinh 03 - Giường 1">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nhịp Tim Thai Cơ Bản (bpm)</label>
                                        <input type="number" name="baseline_fhr_bpm" class="form-control form-control-sm" required value="138">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Độ Dao Động Biến Thiên</label>
                                        <select name="variability" class="form-select form-select-sm">
                                            <option value="moderate_normal">Bình Thường (Moderate 6-25 bpm)</option>
                                            <option value="minimal">Tối Thiểu (Minimal &le; 5 bpm)</option>
                                            <option value="absent">Mất Dao Động (Absent 0 bpm)</option>
                                            <option value="marked">Biến Thiên Quá Mức (&gt;25 bpm)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nhịp Giảm (Decelerations)</label>
                                        <select name="decelerations_type" class="form-select form-select-sm">
                                            <option value="none">Không Có Nhịp Giảm (None)</option>
                                            <option value="early_benign">Nhịp Giảm Sớm (Lành tính chèn đầu thai)</option>
                                            <option value="variable_cord">Nhịp Giảm Biến Đổi (Chèn ép dây rốn)</option>
                                            <option value="late_uteroplacental_hypoxia">Nhịp Giảm Muộn (Thiếu oxy bánh rau)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Cơn Co Tử Cung Trong 10 Phút</label>
                                    <input type="number" name="uterine_contractions_per_10min" class="form-control form-control-sm" required value="3">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-robot me-1"></i> Đánh Giá CTG Tim Thai Bằng AI
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-egg me-2 text-success"></i>Chấm Điểm Phôi IVF & RFID Electronic Witnessing</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.ivf') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Người Vợ ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Hiếm Muộn *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Chu Kỳ IVF</label>
                                        <input type="text" name="cycle_code" class="form-control form-control-sm" required value="IVF-CYC-2026-08">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Định Danh Phôi</label>
                                        <input type="text" name="embryo_id_tag" class="form-control form-control-sm" required value="EMB-DAY5-4AA-01">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phân Loại Gardner</label>
                                        <input type="text" name="gardner_grade" class="form-control form-control-sm" required value="4AA">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Giai Đoạn Phát Triển</label>
                                        <select name="development_stage" class="form-select form-select-sm">
                                            <option value="day5_blastocyst">Phôi Nang Ngày 5 (Day 5)</option>
                                            <option value="day6_blastocyst">Phôi Nang Ngày 6 (Day 6)</option>
                                            <option value="day3_cleavage">Phôi Ngày 3 (Day 3)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Noãn Chọc Hút</label>
                                        <input type="number" name="oocytes_retrieved" class="form-control form-control-sm" required value="14">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Noãn Thụ Tinh 2PN</label>
                                        <input type="number" name="oocytes_fertilized" class="form-control form-control-sm" required value="11">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Trạng Thái Phôi</label>
                                    <select name="embryo_status" class="form-select form-select-sm">
                                        <option value="cryopreserved">Trữ Đông (Cryopreserved)</option>
                                        <option value="transferred">Chuyển Phôi Tươi (Transferred)</option>
                                        <option value="in_culture">Đang Nuôi Cấy (In Culture)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-lock me-1"></i> Lưu & Chấm Điểm AI Phôi Học
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PEWS Tab -->
        <div class="tab-pane fade" id="pews-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Đánh Giá Thang Điểm Cảnh Báo Sớm Nhi Khoa (PEWS)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.pews') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bệnh Nhi ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Bác Sĩ Nhi *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nhóm Tuổi Bệnh Nhi</label>
                                    <select name="age_group" class="form-select form-select-sm">
                                        <option value="toddler_1_4y">1 - 4 Tuổi (Toddler)</option>
                                        <option value="infant_3_12m">3 - 12 Tháng (Infant)</option>
                                        <option value="neonate_under_3m">Sơ Sinh &lt; 3 Tháng (Neonate)</option>
                                        <option value="child_5_11y">5 - 11 Tuổi (Child)</option>
                                        <option value="adolescent_12y_plus">&ge; 12 Tuổi (Adolescent)</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Hành Vi / Tri Giác</label>
                                        <select name="behavior_score" class="form-select form-select-sm">
                                            <option value="0">0: Chơi bình thường</option>
                                            <option value="1">1: Ngủ kích thích dậy</option>
                                            <option value="2">2: Li bì kích thích đau</option>
                                            <option value="3">3: Hôn mê / Không đáp ứng</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Tuần Hoàn / CRT</label>
                                        <select name="cardiovascular_score" class="form-select form-select-sm">
                                            <option value="0">0: Hồng / CRT &lt; 2s</option>
                                            <option value="1">1: Tái da / CRT 2-3s</option>
                                            <option value="2">2: Xám / CRT 3-4s</option>
                                            <option value="3">3: Xám tro / CRT &gt; 5s</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Hô Hấp / Co Kéo</label>
                                        <select name="respiratory_score" class="form-select form-select-sm">
                                            <option value="0">0: Bình thường</option>
                                            <option value="1">1: Thở nhanh + co kéo nhẹ</option>
                                            <option value="2">2: Co kéo vừa</option>
                                            <option value="3">3: Thở rên + co kéo nặng</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-exclamation me-1"></i> Tính Điểm PEWS & Khuyến Nghị Can Thiệp
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-danger"></i>Đánh Giá PEWS Gần Đây</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhi</th>
                                            <th>Nhóm Tuổi</th>
                                            <th>Điểm PEWS</th>
                                            <th>Phân Tầng Nguy Cơ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_pews'] as $pew)
                                        <tr>
                                            <td>{{ $pew->patient->full_name ?? 'ID #' . $pew->patient_id }}</td>
                                            <td>{{ $pew->age_group }}</td>
                                            <td><span class="badge bg-danger fs-6">{{ $pew->total_pews_score }}</span></td>
                                            <td>
                                                @if($pew->risk_action_level === 'high_pediatric_met_activation')
                                                    <span class="badge bg-danger">Kích Hoạt MET Nhi Cấp Cứu</span>
                                                @elseif($pew->risk_action_level === 'medium_nurse_assessment')
                                                    <span class="badge bg-warning text-dark">Theo Dõi Sát 1h/lần</span>
                                                @else
                                                    <span class="badge bg-success">Thường Quy</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có bản ghi PEWS nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Cardio FFR/IVUS Tab -->
        <div class="tab-pane fade" id="cardio-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Đánh Giá Sinh Lý & Hình Ảnh Mạch Vành FFR / IVUS / OCT</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.coronary') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Can Thiệp *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nhánh Mạch Vành Tổn Thương</label>
                                        <input type="text" name="target_artery" class="form-control form-control-sm" required value="LAD (Động mạch liên thất trước)">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Hình Ảnh Học Trong Lòng Mạch</label>
                                        <select name="imaging_modality" class="form-select form-select-sm">
                                            <option value="ivus">Siêu Âm Nội Mạch (IVUS)</option>
                                            <option value="oct">Cắt Lớp Quang Học (OCT)</option>
                                            <option value="angiography_only">Chụp Mạch Đơn Thuần</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Chỉ Số FFR (&le;0.80)</label>
                                        <input type="number" step="0.01" name="ffr_ratio" class="form-control form-control-sm" value="0.75">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Chỉ Số iFR (&le;0.89)</label>
                                        <input type="number" step="0.01" name="ifr_ratio" class="form-control form-control-sm" value="0.86">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">MLA (mm²)</label>
                                        <input type="number" step="0.1" name="minimal_lumen_area_mm2" class="form-control form-control-sm" value="3.1">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-clipboard-pulse me-1"></i> Lưu Phân Tích & Xác Nhận Chỉ Định Can Thiệp
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle me-2 text-danger"></i>Ngưỡng Chuẩn Can Thiệp Huyết Động Học Mạch Vành</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Chỉ số FFR (Fractional Flow Reserve):</span>
                                    <span class="badge bg-danger">&le; 0.80 Có chỉ định đặt Stent</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Chỉ số iFR (Instantaneous Wave-Free Ratio):</span>
                                    <span class="badge bg-danger">&le; 0.89 Thiếu máu cơ tim</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Diện tích lòng mạch tối thiểu MLA Thân Chung (LM):</span>
                                    <span class="badge bg-warning text-dark">&le; 6.0 mm² Nguy cơ cao</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Diện tích lòng mạch MLA LAD / LCx / RCA:</span>
                                    <span class="badge bg-secondary">&le; 3.5 - 4.0 mm²</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Stroke Extended Window Tab -->
        <div class="tab-pane fade" id="stroke-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-brain me-2 text-primary"></i>Đột Quỵ Cửa Sổ Mở Rộng 6-24h (DAWN & DEFUSE-3)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.emergency-maternity-cardio.stroke-extended') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Can Thiệp Thần Kinh *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Khởi Phát Đến Viện (h) *</label>
                                        <input type="number" name="onset_to_arrival_hours" class="form-control form-control-sm" required value="10" min="1" max="48">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thể Tích Lõi Hoại Tử Core (mL) *</label>
                                        <input type="number" step="0.1" name="ischemic_core_volume_ml" class="form-control form-control-sm" required value="22.5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Thể Tích Tưới Máu Kém Hypoperfused (mL) *</label>
                                    <input type="number" step="0.1" name="hypoperfused_volume_ml" class="form-control form-control-sm" required value="95.0">
                                    <small class="text-muted">Vùng tranh tối tranh sáng Penumbra = Thể tích tưới máu kém - Lõi hoại tử</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-activity me-1"></i> Đánh Giá Bất Tương Xứng & Chỉ Định Can Thiệp
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Hồ Sơ Đột Quỵ Can Thiệp Cửa Sổ Mở Rộng</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Khởi Phát</th>
                                            <th>Core / Mismatch</th>
                                            <th>Chỉ Định Can Thiệp</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_stroke'] as $st)
                                        <tr>
                                            <td>{{ $st->patient->full_name ?? 'ID #' . $st->patient_id }}</td>
                                            <td>{{ $st->onset_to_arrival_hours }} giờ</td>
                                            <td>{{ $st->ischemic_core_volume_ml }} mL / <span class="badge bg-info-subtle text-info">{{ $st->mismatch_volume_ml }} mL</span></td>
                                            <td>
                                                @if($st->extended_window_decision === 'eligible_for_mechanical_thrombectomy')
                                                    <span class="badge bg-success">Đủ ĐK Lấy Huyết Khối</span>
                                                @else
                                                    <span class="badge bg-secondary">Điều Trị Bảo Tồn</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có ca đột quỵ cửa sổ mở rộng nào.</td></tr>
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
