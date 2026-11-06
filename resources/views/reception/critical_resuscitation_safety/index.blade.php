@extends('layouts.admin')

@section('title', 'Hồi Sức Code Blue, Khí Y Tế MGPS, ROTEM & Trực Thăng Helipad')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Hồi Sức Code Blue, Khí Y Tế MGPS, ROTEM & Trực Thăng Helipad
            </h4>
            <p class="text-muted small mb-0">
                Wave 8 • Hồi sinh tim phổi nâng cao ACLS (ép tim CCF & ROSC), Quản lý đường ống khí y tế & bồn oxy lỏng LOX, Lọc máu hấp phụ ngộ độc cấp, Đàn nhớt ROTEM & Điều phối trực thăng cấp cứu Helipad
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="bi bi-shield-shaded me-1"></i> Critical Resuscitation & Facility Safety Active
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
                            <span class="text-muted small">Biến Cố Code Blue</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1">{{ $stats['total_code_blue_events'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded-circle text-danger">
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
                            <span class="text-muted small">Tái Lập Tuần Hoàn ROSC</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['successful_rosc_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
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
                            <span class="text-muted small">Trạm Khí Y Tế MGPS</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $stats['monitored_gas_zones'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary">
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
                            <span class="text-muted small">Lọc Máu Hấp Phụ Độc Chất</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['toxicology_cases'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded-circle text-warning">
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
                            <span class="text-muted small">Chuyến Bay Trực Thăng Helipad</span>
                            <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['helipad_missions'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-info-subtle p-3 rounded-circle text-info">
                            <i class="bi bi-airplane-engines-fill fs-5"></i>
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
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="resusTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="cblue-tab" data-bs-toggle="tab" data-bs-target="#cblue-pane" type="button" role="tab">
                <i class="bi bi-activity me-1"></i> 1. Code Blue & Ép Tim ACLS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="mgps-tab" data-bs-toggle="tab" data-bs-target="#mgps-pane" type="button" role="tab">
                <i class="bi bi-wind me-1"></i> 2. Khí Y Tế MGPS & Bồn Oxy Lỏng
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tox-tab" data-bs-toggle="tab" data-bs-target="#tox-pane" type="button" role="tab">
                <i class="bi bi-capsule me-1"></i> 3. Lọc Máu Hấp Phụ Ngộ Độc Cấp
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="rotem-tab" data-bs-toggle="tab" data-bs-target="#rotem-pane" type="button" role="tab">
                <i class="bi bi-droplet-half me-1"></i> 4. ROTEM Đàn Nhớt Chỉ Huy Bù Máu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="heli-tab" data-bs-toggle="tab" data-bs-target="#heli-pane" type="button" role="tab">
                <i class="bi bi-airplane-engines me-1"></i> 5. Trực Thăng Cấp Cứu Helipad
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="resusTabContent">
        <!-- 1. Code Blue AI & ACLS -->
        <div class="tab-pane fade show active" id="cblue-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-danger"></i>Ghi Nhận Biến Cố Hồi Sức Ngừng Tim Code Blue</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation-safety.code-blue') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Trưởng Kíp Code Blue *</label>
                                        <input type="number" name="code_team_leader_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Vị Trí Xảy Ra Ngừng Tim *</label>
                                        <input type="text" name="event_location" class="form-control form-control-sm" required placeholder="Khoa Tim mạch, Phòng 508...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nhịp Tim Ban Đầu *</label>
                                        <select name="initial_rhythm" class="form-select form-select-sm" required>
                                            <option value="ventricular_fibrillation_vf">Rung thất (VF) - Sốc được</option>
                                            <option value="pulseless_vt">Nhanh thất mất mạch (pVT) - Sốc được</option>
                                            <option value="asystole">Vô tâm thu (Asystole)</option>
                                            <option value="pea_pulseless_electrical_activity">Phân ly điện cơ (PEA)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tổng Phút Hồi Sức CPR *</label>
                                        <input type="number" name="cpr_total_duration_minutes" class="form-control form-control-sm" required value="16">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tỷ Lệ Ép Tim % (CCF Target >= 80%) *</label>
                                        <input type="number" step="0.1" name="chest_compression_fraction_pct" class="form-control form-control-sm" required value="86.5">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Số Lần Sốc Điện</label>
                                        <input type="number" name="defibrillation_shocks_count" class="form-control form-control-sm" value="2">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Liều Epinephrine</label>
                                        <input type="number" name="epinephrine_doses_given" class="form-control form-control-sm" value="3">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Liều Amiodarone</label>
                                        <input type="number" name="amiodarone_doses_given" class="form-control form-control-sm" value="1">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EtCO2 Khi Ép Tim (mmHg) *</label>
                                        <input type="number" step="0.1" name="etco2_during_cpr_mmhg" class="form-control form-control-sm" required value="24.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kết Cục Cuối Cùng *</label>
                                        <select name="final_outcome" class="form-select form-select-sm" required>
                                            <option value="admitted_to_icu">Tái lập tuần hoàn và chuyển về ICU</option>
                                            <option value="transferred_pci">Chuyển phòng can thiệp mạch vành PCI</option>
                                            <option value="mortality_in_code">Tử vong trong hồi sức</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="rosc_achieved" value="1" id="roscSwitch" checked>
                                        <label class="form-check-label small fw-bold text-success" for="roscSwitch">Tái Lập Tuần Hoàn Tự Nhiên (ROSC Đạt)</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="advanced_airway_placed" value="1" id="airwaySwitch" checked>
                                        <label class="form-check-label small" for="airwaySwitch">Đã đặt đường thở nâng cao (Nội khí quản / Mask thanh quản)</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu Hồ Sơ Code Blue & Phân Tích Chất Lượng Hồi Sức
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task me-2 text-danger"></i>Nhật Ký Biến Cố Ngừng Tim Code Blue Gần Đây</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Vị Trí</th>
                                        <th>Nhịp Ban Đầu</th>
                                        <th>Ép Tim CCF & Phút</th>
                                        <th>EtCO2</th>
                                        <th>Trạng Thái ROSC</th>
                                        <th>Kết Cục</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_code_blue'] ?? [] as $cb)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $cb->patient->full_name ?? 'BN #' . $cb->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $cb->event_location }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger-subtle text-danger text-uppercase">{{ str_replace('_', ' ', explode('_', $cb->initial_rhythm)[0]) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $cb->chest_compression_fraction_pct }}%</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $cb->cpr_total_duration_minutes }} phút CPR</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $cb->etco2_during_cpr_mmhg }} mmHg</span>
                                            </td>
                                            <td>
                                                @if($cb->rosc_achieved)
                                                    <span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>ROSC Thành Công</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">Chưa đạt ROSC</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-dark-subtle text-dark text-capitalize">{{ str_replace('_', ' ', $cb->final_outcome) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có biến cố Code Blue nào được ghi nhận.
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

        <!-- 2. Khí Y Tế MGPS & LOX -->
        <div class="tab-pane fade" id="mgps-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wind me-2 text-primary"></i>Cập Nhật Áp Suất Khí Y Tế Trung Tâm & Bồn Oxy Lỏng</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation-safety.medical-gas') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Trạm Khí (Zone) *</label>
                                        <input type="text" name="zone_manifold_code" class="form-control form-control-sm" required value="MGPS-ZONE-ICU-01">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tòa Nhà & Tầng *</label>
                                        <input type="text" name="building_floor" class="form-control form-control-sm" required value="Tòa A - Tầng 3 ICU">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Kỹ Sư Y Sinh Phụ Trách *</label>
                                        <input type="number" name="biomedical_engineer_id" class="form-control form-control-sm" required placeholder="Doctor/Engineer ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Số Máy Thở Đang Chạy *</label>
                                        <input type="number" name="active_ventilators_count" class="form-control form-control-sm" required value="28">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Áp Suất Oxy (Chuẩn 4-4.5 bar) *</label>
                                        <input type="number" step="0.1" name="oxygen_line_pressure_bar" class="form-control form-control-sm" required value="4.2">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Hút Chân Không (bar) *</label>
                                        <input type="number" step="0.01" name="vacuum_suction_pressure_bar" class="form-control form-control-sm" required value="-0.75">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Khí Nén Y Tế (bar)</label>
                                        <input type="number" step="0.1" name="medical_air_pressure_bar" class="form-control form-control-sm" required value="4.0">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Khí Mổ Cột Sống (bar)</label>
                                        <input type="number" step="0.1" name="surgical_air_pressure_bar" class="form-control form-control-sm" required value="7.0">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-bold">Khí Mê N2O (bar)</label>
                                        <input type="number" step="0.1" name="nitrous_oxide_pressure_bar" class="form-control form-control-sm" required value="4.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dung Tích Bồn Oxy Lỏng (%) *</label>
                                        <input type="number" step="0.1" name="liquid_oxygen_tank_level_pct" class="form-control form-control-sm" required value="78.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thể Tích Còn Lại (Lít) *</label>
                                        <input type="number" step="0.1" name="liquid_oxygen_remaining_liters" class="form-control form-control-sm" required value="14200.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Kiểm Định An Toàn Trạm Khí Y Tế MGPS
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3 me-2 text-primary"></i>Mạng Lưới Đường Ống Khí Y Tế Toàn Viện</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 trạm gần nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Trạm & Vị Trí</th>
                                        <th>Áp Lực O2</th>
                                        <th>Áp Lực Chân Không</th>
                                        <th>Bồn Oxy Lỏng</th>
                                        <th>Dự Báo Cạn Kiệt</th>
                                        <th>Báo Động Áp Lực</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_gas_pipelines'] ?? [] as $gp)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $gp->zone_manifold_code }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $gp->building_floor }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $gp->oxygen_line_pressure_bar }} bar</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $gp->vacuum_suction_pressure_bar }} bar</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $gp->liquid_oxygen_tank_level_pct }}%"></div>
                                                </div>
                                                <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ $gp->liquid_oxygen_tank_level_pct }}% ({{ $gp->liquid_oxygen_remaining_liters }} L)</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $gp->estimated_hours_until_depletion }} giờ</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Cho {{ $gp->active_ventilators_count }} máy thở</div>
                                            </td>
                                            <td>
                                                @if($gp->low_pressure_emergency_alarm)
                                                    <span class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i>TỤT ÁP KHẨN CẤP</span>
                                                @else
                                                    <span class="badge bg-success text-white">An Toàn</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có trạm khí MGPS nào được thiết lập.
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

        <!-- 3. Lọc Máu Hấp Phụ Ngộ Độc Cấp -->
        <div class="tab-pane fade" id="tox-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-capsule me-2 text-warning"></i>Ghi Nhận Ngộ Độc Cấp & Chỉ Định Lọc Máu Hấp Phụ</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation-safety.toxicology') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Chống Độc *</label>
                                        <input type="number" name="toxicologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Tên Độc Chất / Thuốc *</label>
                                        <input type="text" name="toxin_or_drug_name" class="form-control form-control-sm" required placeholder="Paracetamol, Paraquat, Digoxin...">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Gian Sau Uống (Giờ) *</label>
                                        <input type="number" step="0.1" name="estimated_ingestion_hours_ago" class="form-control form-control-sm" required value="4.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Liều Uống Ước Tính (mg/mL) *</label>
                                        <input type="number" step="0.1" name="ingested_dose_mg_or_ml" class="form-control form-control-sm" required value="16000.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Nồng Độ Huyết Thanh (mcg/mL) *</label>
                                        <input type="number" step="0.1" name="serum_toxin_concentration_mcg_ml" class="form-control form-control-sm" required value="240.0">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Đồ Thị Rumack-Matthew *</label>
                                        <select name="rumack_matthew_toxicity_risk" class="form-select form-select-sm" required>
                                            <option value="probable_toxicity" selected>Độc tính cao (Probable Toxicity)</option>
                                            <option value="possible_toxicity">Độc tính tiềm tàng (Possible Toxicity)</option>
                                            <option value="low_risk">Nguy cơ thấp</option>
                                            <option value="not_applicable">Không áp dụng (Chất độc khác)</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Phương Thức Lọc Máu *</label>
                                        <select name="extracorporeal_modality" class="form-select form-select-sm" required>
                                            <option value="hemoperfusion_resin" selected>Lọc hấp phụ quả Resin HA330</option>
                                            <option value="hemoperfusion_charcoal">Lọc hấp phụ than hoạt Charcoal</option>
                                            <option value="intermittent_hemodialysis">Chạy thận nhân tạo ngắt quãng HD</option>
                                            <option value="crrt_cvvhdf">Lọc máu liên tục CVVHDF</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Độ Thanh Thải Độc Chất (mL/min) *</label>
                                        <input type="number" step="0.1" name="toxin_clearance_ml_min" class="form-control form-control-sm" required value="150.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Thời Lượng Lọc Khuyến Nghị (Giờ) *</label>
                                        <input type="number" step="0.1" name="recommended_duration_hours" class="form-control form-control-sm" required value="4.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning text-dark btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Phân Tích Dược Động Học Độc Tố Lọc Máu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-exclamation me-2 text-warning"></i>Ca Ngộ Độc Cấp & Tiến Trình Lọc Máu Hấp Phụ</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân</th>
                                        <th>Độc Chất & Liều</th>
                                        <th>Nồng Độ & Giờ</th>
                                        <th>Phương Thức Lọc</th>
                                        <th>Chỉ Định Lọc Máu</th>
                                        <th>Thuốc Giải Độc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_toxicology'] ?? [] as $tx)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $tx->patient->full_name ?? 'BN #' . $tx->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Mã: {{ $tx->patient->patient_code ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger">{{ $tx->toxin_or_drug_name }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $tx->ingested_dose_mg_or_ml }} mg</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning-subtle text-dark">{{ $tx->serum_toxin_concentration_mcg_ml }} µg/mL</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">Sau {{ $tx->estimated_ingestion_hours_ago }} giờ</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $tx->extracorporeal_modality) }}</span>
                                            </td>
                                            <td>
                                                @if($tx->hemodialysis_indicated)
                                                    <span class="badge bg-danger text-white">Chỉ Định Khẩn</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Theo Dõi</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 140px;" title="{{ $tx->specific_antidote_given }}">
                                                    {{ $tx->specific_antidote_given ?? 'Chưa ghi nhận' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có ca ngộ độc cấp nào được ghi nhận.
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

        <!-- 4. ROTEM Đàn Nhớt Chỉ Huy Bù Máu -->
        <div class="tab-pane fade" id="rotem-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-droplet-half me-2 text-danger"></i>Ghi Nhận Thông Số Biểu Đồ ROTEM / TEG</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation-safety.rotem') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Gây Mê Hồi Sức *</label>
                                        <input type="number" name="anesthesiologist_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Bối Cảnh Lâm Sàng *</label>
                                    <select name="clinical_scenario" class="form-select form-select-sm" required>
                                        <option value="severe_polytrauma">Đa chấn thương mất máu nặng</option>
                                        <option value="cardiac_surgery_pump">Phẫu thuật tim hở chạy máy CPB</option>
                                        <option value="liver_transplant">Phẫu thuật ghép gan</option>
                                        <option value="massive_postpartum_hemorrhage">Băng huyết sau sinh trầm trọng</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EXTEM CT (Chuẩn 40-80s) *</label>
                                        <input type="number" name="extem_ct_seconds" class="form-control form-control-sm" required value="72">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EXTEM CFT (Chuẩn 35-110s) *</label>
                                        <input type="number" name="extem_cft_seconds" class="form-control form-control-sm" required value="95">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EXTEM Alpha Angle (Độ) *</label>
                                        <input type="number" step="0.1" name="extem_alpha_angle_degrees" class="form-control form-control-sm" required value="69.5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EXTEM MCF (Chuẩn 50-72mm) *</label>
                                        <input type="number" name="extem_mcf_mm" class="form-control form-control-sm" required value="56">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">FIBTEM MCF (Chuẩn >= 10mm) *</label>
                                        <input type="number" name="fibtem_mcf_mm" class="form-control form-control-sm" required value="14">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">EXTEM ML % (< 15%) *</label>
                                        <input type="number" step="0.1" name="extem_ml_pct" class="form-control form-control-sm" required value="8.0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Phân Tích ROTEM Chỉ Huy Truyền Chế Phẩm Máu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up me-2 text-danger"></i>Kết Quả Xét Nghiệm Đàn Nhớt ROTEM Gần Đây</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 ca mới nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bệnh Nhân & Bối Cảnh</th>
                                        <th>EXTEM CT / MCF</th>
                                        <th>FIBTEM MCF</th>
                                        <th>Tiêu Sợi Huyết (ML)</th>
                                        <th>TXA Khuyến Nghị</th>
                                        <th>Chế Phẩm Cần Bù</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_rotem'] ?? [] as $rt)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $rt->patient->full_name ?? 'BN #' . $rt->patient_id }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $rt->clinical_scenario) }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $rt->extem_ct_seconds }}s</span> / {{ $rt->extem_mcf_mm }} mm
                                            </td>
                                            <td>
                                                @if($rt->fibtem_mcf_mm < 10)
                                                    <span class="badge bg-danger text-white">{{ $rt->fibtem_mcf_mm }} mm (Thiếu Fibrinogen)</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">{{ $rt->fibtem_mcf_mm }} mm (Đủ)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $rt->extem_ml_pct }}%</span>
                                            </td>
                                            <td>
                                                @if($rt->tranexamic_acid_indicated)
                                                    <span class="badge bg-danger text-white">Chỉ Định TXA</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Không cần TXA</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 140px;" title="{{ $rt->targeted_blood_product_recommendation }}">
                                                    {{ $rt->targeted_blood_product_recommendation ?? 'Không cần bù' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có kết quả xét nghiệm ROTEM nào.
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

        <!-- 5. Trực Thăng Cấp Cứu Helipad -->
        <div class="tab-pane fade" id="heli-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-airplane-engines me-2 text-info"></i>Điều Phối Chuyến Bay Trực Thăng Y Tế Helipad</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reception.critical-resuscitation-safety.helipad') }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã BN ID *</label>
                                        <input type="number" name="patient_id" class="form-control form-control-sm" required placeholder="Patient ID">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">BS Chuyến Bay *</label>
                                        <input type="number" name="flight_physician_id" class="form-control form-control-sm" required placeholder="Doctor ID">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Mã Chuyến Bay *</label>
                                        <input type="text" name="flight_mission_code" class="form-control form-control-sm" required value="HELI-MEDEVAC-{{ rand(1000, 9999) }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Điểm Xuất Phát *</label>
                                        <input type="text" name="transfer_origin_location" class="form-control form-control-sm" required placeholder="Giàn khoan Cửu Long, BV Tỉnh...">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Loại Trực Thăng *</label>
                                        <select name="aircraft_type" class="form-select form-select-sm" required>
                                            <option value="airbus_h225_super_puma" selected>Airbus H225 Super Puma</option>
                                            <option value="eurocopter_ec155">Eurocopter EC155</option>
                                            <option value="mi_171_medevac">Mi-171 Cứu Hộ</option>
                                            <option value="bell_429">Bell 429 Cấp Cứu</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Điểm TRIPS (0-60) *</label>
                                        <input type="number" name="transfer_risk_score_trips" class="form-control form-control-sm" required value="16">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Khoa Tiếp Nhận Tại Bệnh Viện *</label>
                                    <select name="receiving_department" class="form-select form-select-sm" required>
                                        <option value="trauma_hybrid_or" selected>Phòng Mổ Hybrid Đa Chấn Thương</option>
                                        <option value="coronary_cathlab">Phòng Can Thiệp Mạch Vành Cathlab</option>
                                        <option value="icu_extracorporeal">Hồi Sức Tích Cực Ngoại Thể ICU ECMO</option>
                                        <option value="stroke_neuro_interv">Can Thiệp Đột Quỵ Não Khẩn Cấp</option>
                                    </select>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="helipad_landing_clearance_granted" value="1" id="clearanceSwitch" checked>
                                        <label class="form-check-label small fw-bold text-success" for="clearanceSwitch">Bãi Đáp Helipad Đã Cấp Phép Hạ Cánh An Toàn</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="inflight_ventilator_running" value="1" id="ventSwitch" checked>
                                        <label class="form-check-label small" for="ventSwitch">Máy thở di động đang hoạt động trên khoang máy bay</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="inflight_ecmo_running" value="1" id="ecmoHeliSwitch">
                                        <label class="form-check-label small text-danger" for="ecmoHeliSwitch">Hệ thống ECMO di động đang chạy trên trực thăng</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info text-white btn-sm w-100 fw-bold">
                                    <i class="bi bi-save me-1"></i> Lưu & Kích Hoạt Kíp Tiếp Đón Sân Bay Nóc Nhà
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-airplane me-2 text-info"></i>Lịch Trình Chuyến Bay Cấp Cứu Trực Thăng Helipad</h6>
                            <span class="badge bg-secondary-subtle text-secondary small">5 chuyến gần nhất</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Chuyến Bay & Điểm Xuất Phát</th>
                                        <th>Bệnh Nhân</th>
                                        <th>Máy Bay & Điểm TRIPS</th>
                                        <th>Khoa Tiếp Nhận</th>
                                        <th>Thiết Bị Khoang Bay</th>
                                        <th>Hạ Cánh Helipad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_helipad'] ?? [] as $hl)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $hl->flight_mission_code }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $hl->transfer_origin_location }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $hl->patient->full_name ?? 'BN #' . $hl->patient_id }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ str_replace('_', ' ', $hl->aircraft_type) }}</span>
                                                <div class="text-muted" style="font-size: 0.75rem;">TRIPS: {{ $hl->transfer_risk_score_trips }} điểm</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $hl->receiving_department) }}</span>
                                            </td>
                                            <td>
                                                @if($hl->inflight_ecmo_running)
                                                    <span class="badge bg-danger text-white me-1">ECMO</span>
                                                @endif
                                                @if($hl->inflight_ventilator_running)
                                                    <span class="badge bg-info text-white">Máy thở</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hl->helipad_landing_clearance_granted)
                                                    <span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Cho Phép Hạ Cánh</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Chờ Lệnh</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>Chưa có chuyến bay cấp cứu Helipad nào.
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
