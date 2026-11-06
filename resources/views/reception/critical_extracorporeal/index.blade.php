@extends('layouts.admin')

@section('title', 'Hồi Sức Tuần Hoàn Sống Còn, ECMO, Buồng Áp Lực Âm AIIR & CRRT')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Hồi Sức Tuần Hoàn Sống Còn, ECMO, Buồng Áp Lực Âm AIIR & CRRT
            </h4>
            <p class="text-muted small mb-0">
                Wave 7 • Tuần hoàn ngoài cơ thể VA/VV ECMO & TTM hạ thân nhiệt, Buồng cách ly áp lực âm AIIR (ACH & HEPA H14), Thử nghiệm thở tự nhiên cai máy RSBI/SBT, Đa chấn thương ISS & Trauma Level 1, CRRT Citrate RCA & ICU ABCDEF Bundle
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-activity me-1"></i> Life Support 24/7 Active
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
                            <span class="text-muted small">ECMO Đang Chạy</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['active_ecmo_sessions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                            <i class="bi bi-heart-fill fs-5"></i>
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
                            <span class="text-muted small">Phòng Áp Lực Âm AIIR</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['occupied_aiir_rooms'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-wind fs-5"></i>
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
                            <span class="text-muted small">Đủ Chuẩn Rút Nội Khí Quản</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['ready_for_extubation_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="bi bi-lungs-fill fs-5"></i>
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
                            <span class="text-muted small">Đa Chấn Thương Level 1</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['major_trauma_level_1'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
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
                            <span class="text-muted small">Lọc Máu CRRT Citrate</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['active_crrt_citrate'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="bi bi-droplet-half fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="eclsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="ecmo-tab" data-bs-toggle="tab" data-bs-target="#ecmo-pane" type="button" role="tab">
                <i class="bi bi-heart me-1"></i> 1. ECMO & Hạ Thân Nhiệt TTM
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="aiir-tab" data-bs-toggle="tab" data-bs-target="#aiir-pane" type="button" role="tab">
                <i class="bi bi-wind me-1"></i> 2. Áp Lực Âm AIIR & ACH
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="sbt-tab" data-bs-toggle="tab" data-bs-target="#sbt-pane" type="button" role="tab">
                <i class="bi bi-lungs me-1"></i> 3. Cai Máy Thở RSBI / SBT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="trauma-tab" data-bs-toggle="tab" data-bs-target="#trauma-pane" type="button" role="tab">
                <i class="bi bi-lightning-charge me-1"></i> 4. Đa Chấn Thương ISS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="crrt-tab" data-bs-toggle="tab" data-bs-target="#crrt-pane" type="button" role="tab">
                <i class="bi bi-droplet me-1"></i> 5. Lọc Máu CRRT & Citrate
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="bundle-tab" data-bs-toggle="tab" data-bs-target="#bundle-pane" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i> 6. ICU ABCDEF & PICS
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="eclsTabContent">
        <!-- 1. ECMO Tab -->
        <div class="tab-pane fade show active" id="ecmo-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Giám Sát Thông Số Máy ECMO & Hạ Thân Nhiệt TTM</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.ecmo') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Hồi Sức *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phương Thức ECMO</label>
                                        <select name="ecmo_modality" class="form-select form-select-sm">
                                            <option value="va_ecmo_venoarterial">VA-ECMO (Hỗ Trợ Tim & Phổi)</option>
                                            <option value="vv_ecmo_venovenous">VV-ECMO (Hỗ Trợ Phổi ARDS)</option>
                                            <option value="vav_hybrid">VAV-ECMO Hybrid</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Lưu Lượng Bơm (L/phút)</label>
                                        <input type="number" step="0.1" name="pump_flow_l_per_min" class="form-control form-control-sm" required value="3.8">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tốc Độ Vòng Quay (RPM)</label>
                                        <input type="number" name="pump_speed_rpm" class="form-control form-control-sm" required value="3400">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chênh Áp Màng Delta P (mmHg)</label>
                                        <input type="number" step="1" name="delta_pressure_membrane_mmhg" class="form-control form-control-sm" required value="38">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khí Quét Sweep Gas (L/min)</label>
                                        <input type="number" step="0.5" name="sweep_gas_flow_l_per_min" class="form-control form-control-sm" required value="4.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">FiO2 Khí Quét (%)</label>
                                        <input type="number" step="1" name="fio2_sweep_pct" class="form-control form-control-sm" required value="100">
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="ttm_targeted_temperature_active" value="1" id="ttmCheck" checked>
                                    <label class="form-check-label small" for="ttmCheck">Kích hoạt hạ thân nhiệt chỉ huy TTM (33.0°C - 36.0°C) bảo vệ não</label>
                                </div>
                                <input type="hidden" name="session_status" value="running">
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-activity me-1"></i> Kiểm Soát An Toàn Màng & Lưu Thông Số ECMO
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-danger"></i>Ca ECMO Đang Vận Hành</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Loại ECMO</th>
                                            <th>Lưu Lượng Dòng</th>
                                            <th>Delta P Màng</th>
                                            <th>Nguy Cơ Đông Màng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_ecmo'] as $e)
                                        <tr>
                                            <td>{{ $e->patient->full_name ?? 'ID #' . $e->patient_id }}</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($e->ecmo_modality) }}</span></td>
                                            <td>{{ $e->pump_flow_l_per_min }} L/min ({{ $e->pump_speed_rpm }} RPM)</td>
                                            <td><span class="badge bg-info text-dark">{{ $e->delta_pressure_membrane_mmhg }} mmHg</span></td>
                                            <td>
                                                @if($e->membrane_thrombosis_risk)
                                                    <span class="badge bg-danger">CẢNH BÁO ĐÔNG MÀNG</span>
                                                @else
                                                    <span class="badge bg-success">An Toàn</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có ca ECMO nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. AIIR Tab -->
        <div class="tab-pane fade" id="aiir-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wind me-2 text-info"></i>Giám Sát Buồng Cách Ly Áp Lực Âm (AIIR)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.aiir') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Buồng AIIR *</label>
                                        <input type="text" name="room_code" class="form-control form-control-sm" required value="AIIR-01">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID (nếu có)</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" placeholder="Patient ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tác Nhân Lây Truyền Cách Ly</label>
                                    <input type="text" name="pathogen_name" class="form-control form-control-sm" name="pathogen_isolation_type" required value="Lao phổi đa kháng thuốc (MDR-TB)">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chênh Áp Pa (&le; -2.5 Pa) *</label>
                                        <input type="number" step="0.1" name="pressure_differential_pa" class="form-control form-control-sm" required value="-8.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Đổi Khí Mỗi Giờ (&ge; 12 ACH) *</label>
                                        <input type="number" step="0.5" name="air_changes_per_hour_ach" class="form-control form-control-sm" required value="14.5">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm text-white w-100 fw-bold">
                                    <i class="bi bi-shield-check me-1"></i> Cập Nhật & Kiểm Tra Chuẩn CDC Buồng Cách Ly
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-building me-2 text-info"></i>Tình Trạng Các Buồng Cách Ly Áp Lực Âm</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Mã Buồng</th>
                                            <th>Tác Nhân</th>
                                            <th>Chênh Áp (Pa)</th>
                                            <th>Lưu Khí (ACH)</th>
                                            <th>Cảnh Báo Áp Suất</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_aiir'] as $a)
                                        <tr>
                                            <td><strong>{{ $a->room_code }}</strong></td>
                                            <td>{{ $a->pathogen_isolation_type }}</td>
                                            <td><span class="badge bg-secondary">{{ $a->pressure_differential_pa }} Pa</span></td>
                                            <td>{{ $a->air_changes_per_hour_ach }} ACH</td>
                                            <td>
                                                @if($a->pressure_failure_alarm)
                                                    <span class="badge bg-danger">MẤT ÁP LỰC ÂM</span>
                                                @else
                                                    <span class="badge bg-success">Đạt Chuẩn CDC</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có buồng cách ly nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. SBT Tab -->
        <div class="tab-pane fade" id="sbt-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lungs me-2 text-success"></i>Thử Nghiệm Thở Tự Nhiên (SBT) & Tính RSBI</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.sbt') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Hồi Sức *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Thử Nghiệm (phút)</label>
                                        <input type="number" name="sbt_duration_minutes" class="form-control form-control-sm" required value="30">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tần Số Thở f (bpm) *</label>
                                        <input type="number" step="1" name="respiratory_rate_f_bpm" class="form-control form-control-sm" required value="20">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Khí Lưu Thông VT (Lít) *</label>
                                        <input type="number" step="0.01" name="tidal_volume_vt_liters" class="form-control form-control-sm" required value="0.45">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Áp Lực Hít Vào Max (MIP cmH2O)</label>
                                        <input type="number" step="1" name="maximal_inspiratory_pressure_mip" class="form-control form-control-sm" required value="-26">
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="cuff_leak_test_positive" value="1" id="cuffCheck" checked>
                                    <label class="form-check-label small" for="cuffCheck">Nghiệm pháp rò bóng chèn (Cuff leak test) dương tính (An toàn không phù thanh môn)</label>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-calculator me-1"></i> Đánh Giá RSBI & Độ Sẵn Sàng Rút Ống
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-success"></i>Đánh Giá Rút Nội Khí Quản Gần Nhất</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Chỉ Số RSBI (f/VT)</th>
                                            <th>MIP (cmH2O)</th>
                                            <th>Kết Luận Rút Ống</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_sbt'] as $s)
                                        <tr>
                                            <td>{{ $s->patient->full_name ?? 'ID #' . $s->patient_id }}</td>
                                            <td><span class="badge bg-success fs-6">{{ $s->rsbi_score }}</span></td>
                                            <td>{{ $s->maximal_inspiratory_pressure_mip }} cmH2O</td>
                                            <td>
                                                @if($s->sbt_outcome === 'passed_ready_for_extubation')
                                                    <span class="badge bg-success">ĐỦ ĐIỀU KIỆN RÚT ỐNG</span>
                                                @else
                                                    <span class="badge bg-danger">Chưa Đạt Yêu Cầu</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có ca cai máy thở nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Trauma ISS Tab -->
        <div class="tab-pane fade" id="trauma-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge me-2 text-warning"></i>Phân Loại Đa Chấn Thương Thang Điểm ISS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.trauma-iss') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trưởng Kíp Chấn Thương *</label>
                                        <input type="number" name="trauma_leader_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Đầu Cổ AIS (0-6)</label>
                                        <input type="number" name="ais_head_neck" class="form-control form-control-sm" required value="3" min="0" max="6">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Mặt AIS (0-6)</label>
                                        <input type="number" name="ais_face" class="form-control form-control-sm" required value="1" min="0" max="6">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Ngực AIS (0-6)</label>
                                        <input type="number" name="ais_chest" class="form-control form-control-sm" required value="4" min="0" max="6">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Bụng Chậu AIS</label>
                                        <input type="number" name="ais_abdomen_pelvis" class="form-control form-control-sm" required value="3" min="0" max="6">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Tứ Chi AIS</label>
                                        <input type="number" name="ais_extremities_pelvic_girdle" class="form-control form-control-sm" required value="2" min="0" max="6">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Bề Mặt Bỏng AIS</label>
                                        <input type="number" name="ais_external_burns" class="form-control form-control-sm" required value="0" min="0" max="6">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark">
                                    <i class="bi bi-shield-fill-exclamation me-1"></i> Tính ISS & Kích Hoạt Trauma Level 1
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-warning"></i>Ca Đa Chấn Thương Kích Hoạt Báo Động</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Điểm ISS</th>
                                            <th>Kích Hoạt Trauma Level 1</th>
                                            <th>Tỷ Lệ Sống TRISS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_trauma'] as $t)
                                        <tr>
                                            <td>{{ $t->patient->full_name ?? 'ID #' . $t->patient_id }}</td>
                                            <td><span class="badge bg-danger fs-6">{{ $t->calculated_iss_score }}</span></td>
                                            <td>
                                                @if($t->trauma_level_1_activated)
                                                    <span class="badge bg-danger">KÍCH HOẠT LEVEL 1 (ISS &ge; 16)</span>
                                                @else
                                                    <span class="badge bg-secondary">Mức Độ Nhẹ</span>
                                                @endif
                                            </td>
                                            <td>{{ $t->predicted_survival_triss_pct }}%</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có ca đa chấn thương nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. CRRT Tab -->
        <div class="tab-pane fade" id="crrt-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-droplet-half me-2 text-primary"></i>Lọc Máu Liên Tục CRRT & Chống Đông Citrate RCA</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.crrt') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Hồi Sức *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Chế Độ Lọc CRRT</label>
                                        <select name="crrt_modality" class="form-select form-select-sm">
                                            <option value="cvvhdf_hemodiafiltration">CVVHDF (Siêu Lọc Thẩm Tách Liên Tục)</option>
                                            <option value="cvvh_hemofiltration">CVVH (Siêu Lọc)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Dịch Lọc (mL/kg/h)</label>
                                        <input type="number" step="1" name="effluent_dose_ml_per_kg_per_h" class="form-control form-control-sm" required value="25">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Áp Lực Xuyên Màng TMP (mmHg)</label>
                                        <input type="number" step="1" name="transmembrane_pressure_tmp_mmhg" class="form-control form-control-sm" required value="110">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Citrate Trước Màng</label>
                                        <input type="number" step="0.1" name="pre_filter_citrate_infusion_mmol_per_l" class="form-control form-control-sm" required value="3.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Canxi Ion Sau Màng (0.25-0.35)</label>
                                        <input type="number" step="0.01" name="post_filter_ionized_calcium_mmol_per_l" class="form-control form-control-sm" required value="0.28">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Canxi Ion Toàn Thân (1.1-1.3)</label>
                                        <input type="number" step="0.01" name="systemic_ionized_calcium_mmol_per_l" class="form-control form-control-sm" required value="1.18">
                                    </div>
                                </div>
                                <input type="hidden" name="calcium_chloride_compensation_rate_ml_per_h" value="12.0">
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-shield-check me-1"></i> Lưu Thông Số & Giám Sát Chống Đông Citrate
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Phiên Lọc Máu CRRT Đang Theo Dõi</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Chế Độ</th>
                                            <th>Canxi Sau Màng</th>
                                            <th>Canxi Toàn Thân</th>
                                            <th>An Toàn Citrate</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_crrt'] as $cr)
                                        <tr>
                                            <td>{{ $cr->patient->full_name ?? 'ID #' . $cr->patient_id }}</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($cr->crrt_modality) }}</span></td>
                                            <td><span class="badge bg-success">{{ $cr->post_filter_ionized_calcium_mmol_per_l }} mmol/L</span></td>
                                            <td>{{ $cr->systemic_ionized_calcium_mmol_per_l }} mmol/L</td>
                                            <td><span class="badge bg-primary">Đạt Chuẩn Chống Đông</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có phiên lọc máu CRRT nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. ICU Bundle Tab -->
        <div class="tab-pane fade" id="bundle-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-2 text-success"></i>Gói Can Thiệp ICU ABCDEF & Phòng Ngừa PICS</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-extracorporeal.bundle') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Hồi Sức *</label>
                                        <input type="number" name="doctor_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">C: Lựa Chọn Thuốc An Thần</label>
                                    <input type="text" name="bundle_c_sedative_choice" class="form-control form-control-sm" required value="Dexmedetomidine (Precedex)">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">D: Mê Sảng CAM-ICU</label>
                                        <select name="bundle_d_cam_icu_delirium" class="form-select form-select-sm">
                                            <option value="negative_clear">Âm Tính (Tỉnh táo / Không mê sảng)</option>
                                            <option value="positive_delirium_present">Dương Tính (Có mê sảng)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">E: Mức Độ Vận Động Sớm</label>
                                        <select name="bundle_e_early_mobility_level" class="form-select form-select-sm">
                                            <option value="level_2_active_sitting_edge">Mức 2: Ngồi dậy bên mép giường</option>
                                            <option value="level_3_standing_transfer">Mức 3: Đứng dậy chuyển ghế</option>
                                            <option value="level_4_walking_gait">Mức 4: Tập đi có trợ giúp</option>
                                            <option value="level_1_passive_bed">Mức 1: Vận động thụ động tại giường</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="bundle_a_pain_assessed_controlled" value="1" id="aCheck" checked>
                                    <label class="form-check-label small" for="aCheck">A: Đánh giá đau CPOT/BPS định kỳ và kiểm soát tốt</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="bundle_b_both_sat_sbt_performed" value="1" id="bCheck" checked>
                                    <label class="form-check-label small" for="bCheck">B: Ngắt an thần hàng ngày (SAT) kết hợp thở tự nhiên (SBT)</label>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Đánh Giá Tuân Thủ Gói ICU & Nguy Cơ PICS
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-success"></i>Bệnh Nhân ICU Áp Dụng Gói ABCDEF</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bệnh Nhân</th>
                                            <th>Thuốc An Thần</th>
                                            <th>CAM-ICU Delirium</th>
                                            <th>Nguy Cơ PICS (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @forelse($stats['recent_bundle'] as $bu)
                                        <tr>
                                            <td>{{ $bu->patient->full_name ?? 'ID #' . $bu->patient_id }}</td>
                                            <td>{{ $bu->bundle_c_sedative_choice }}</td>
                                            <td>
                                                @if($bu->bundle_d_cam_icu_delirium === 'negative_clear')
                                                    <span class="badge bg-success">Âm Tính</span>
                                                @else
                                                    <span class="badge bg-danger">Có Mê Sảng</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info text-dark">{{ $bu->pics_post_intensive_care_risk_score }}%</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có đánh giá gói ICU nào.</td></tr>
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
