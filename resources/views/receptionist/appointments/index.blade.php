@extends('layouts.admin')

@section('title', 'Bàn Tiếp Nhận & Check-in - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bàn Tiếp Nhận & Check-in Ngoại Trú" subtitle="Quản lý và tiếp đón bệnh nhân đến viện theo lịch hẹn hoặc đăng ký trực tiếp tại quầy">
    <x-slot:actions>
        <a href="{{ route('reception.appointments.walkin') }}" class="btn btn-success d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-box-arrow-in-down-right"></i> Tiếp nhận tại quầy (Walk-in)
        </a>
        <a href="{{ route('reception.patients.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-vcard"></i> Quản lý bệnh nhân
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Statistics Row -->
@if(isset($stats))
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <a href="{{ route('reception.appointments.index', ['date' => $date]) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 border-secondary h-100">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Tổng ca hôm nay</div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('reception.appointments.index', ['date' => $date, 'status' => 'pending']) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
                <div class="card-body p-3">
                    <div class="text-warning small fw-bold text-uppercase">Chờ tiếp nhận</div>
                    <div class="fs-4 fw-bold text-warning">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('reception.appointments.index', ['date' => $date, 'status' => 'confirmed']) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
                <div class="card-body p-3">
                    <div class="text-primary small fw-bold text-uppercase">Đã Check-in (Chờ khám)</div>
                    <div class="fs-4 fw-bold text-primary">{{ $stats['confirmed'] }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('reception.appointments.index', ['date' => $date, 'status' => 'completed']) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
                <div class="card-body p-3">
                    <div class="text-success small fw-bold text-uppercase">Đã khám xong</div>
                    <div class="fs-4 fw-bold text-success">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('reception.appointments.index', ['date' => $date, 'status' => 'cancelled']) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
                <div class="card-body p-3">
                    <div class="text-danger small fw-bold text-uppercase">Đã hủy</div>
                    <div class="fs-4 fw-bold text-danger">{{ $stats['cancelled'] }}</div>
                </div>
            </div>
        </a>
    </div>
</div>
@endif

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reception.appointments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Ngày tiếp nhận:</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Ca trực:</label>
                <select name="shift" class="form-select form-select-sm">
                    <option value="">-- Tất cả ca --</option>
                    <option value="morning" {{ ($shift ?? '') === 'morning' ? 'selected' : '' }}>Ca Sáng (07:30 - 11:30)</option>
                    <option value="afternoon" {{ ($shift ?? '') === 'afternoon' ? 'selected' : '' }}>Ca Chiều (13:30 - 17:00)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Trạng thái:</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ tiếp nhận</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Đã tiếp nhận (Chờ khám)</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã hoàn tất</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Chuyên khoa:</label>
                <select name="specialty_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả khoa --</option>
                    @if(isset($specialties))
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}" {{ ($specialtyId ?? '') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-muted fw-bold d-block">Tra cứu nhanh:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Mã hẹn, Mã BN, Họ tên hoặc SĐT...">
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Áp dụng bộ lọc">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="{{ route('reception.appointments.index') }}" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Appointments Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 130px;">Mã hẹn</th>
                        <th>Bệnh nhân</th>
                        <th>Chuyên khoa</th>
                        <th>Bác sĩ phụ trách</th>
                        <th>Ca & Giờ hẹn</th>
                        <th>Triệu chứng / Ghi chú</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3" style="width: 190px;">Thao tác Tiếp nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-primary">
                                {{ $app->appointment_code }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('reception.patients.show', $app->patientProfile) }}" class="text-decoration-none text-dark">
                                        {{ $app->patientProfile->full_name }}
                                    </a>
                                </div>
                                <div class="small text-muted">
                                    <span>Mã: <code>{{ $app->patientProfile->patient_code }}</code></span> &bull; 
                                    <span>SĐT: {{ $app->patientProfile->phone }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $app->specialty ? $app->specialty->name : '--' }}</span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $app->doctor ? $app->doctor->full_name_with_title : '--' }}</div>
                                <small class="text-muted">Phòng {{ $app->doctor ? $app->doctor->room_number : '' }}</small>
                            </td>
                            <td>
                                <div>{{ $app->shift_name }}</div>
                                <small class="text-muted">{{ $app->appointment_time ? substr($app->appointment_time, 0, 5) : 'Trong ca' }}</small>
                            </td>
                            <td class="small" style="max-width: 190px;">
                                <div class="text-truncate" title="{{ $app->symptom_description }}">
                                    {{ $app->symptom_description }}
                                </div>
                                @if($app->cancellation_reason)
                                    <small class="text-danger d-block text-truncate" title="{{ $app->cancellation_reason }}">
                                        <i class="bi bi-info-circle"></i> {{ $app->cancellation_reason }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($app->status === 'pending')
                                        <form method="POST" action="{{ route('reception.appointments.checkin', $app) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-1" title="Check-in tiếp đón bệnh nhân">
                                                <i class="bi bi-check2-circle"></i> Check-in
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-warning" title="Điều phối / Đổi bác sĩ" data-bs-toggle="modal" data-bs-target="#reassignModal-{{ $app->id }}">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Hủy lịch" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $app->id }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @elseif($app->status === 'confirmed')
                                        <span class="badge bg-primary text-white py-2 px-2 d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-person-check-fill"></i> Đã Check-in
                                        </span>
                                        <button type="button" class="btn btn-outline-warning btn-sm" title="Điều phối lại bác sĩ" data-bs-toggle="modal" data-bs-target="#reassignModal-{{ $app->id }}">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                    @elseif($app->status === 'completed')
                                        <span class="badge bg-success text-white py-2 px-2">
                                            <i class="bi bi-clipboard-check"></i> Đã hoàn tất
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white py-2 px-2">
                                            <i class="bi bi-x-circle"></i> Đã hủy
                                        </span>
                                    @endif
                                </div>

                                <!-- Reassign Doctor Modal -->
                                @if(in_array($app->status, ['pending', 'confirmed']))
                                    <div class="modal fade" id="reassignModal-{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form method="POST" action="{{ route('reception.appointments.reassign', $app) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-warning">
                                                            <i class="bi bi-arrow-left-right me-1"></i> Điều Phối Ca Khám: {{ $app->appointment_code }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-3">
                                                            Bệnh nhân: <strong>{{ $app->patientProfile->full_name }}</strong> &bull; Hiện tại: <strong>{{ $app->doctor ? $app->doctor->full_name_with_title : 'Chưa gán' }}</strong>
                                                        </p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small">Bác sĩ phụ trách mới: <span class="text-danger">*</span></label>
                                                            <select name="doctor_id" class="form-select form-select-sm" required>
                                                                @if(isset($doctors))
                                                                    @foreach($doctors as $doc)
                                                                        <option value="{{ $doc->id }}" {{ $app->doctor_id == $doc->id ? 'selected' : '' }}>
                                                                            {{ $doc->full_name_with_title }} - {{ $doc->specialty ? $doc->specialty->name : '' }} ({{ $doc->room_number }})
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="row g-2 mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small">Ngày khám: <span class="text-danger">*</span></label>
                                                                <input type="date" name="appointment_date" value="{{ $app->appointment_date ? $app->appointment_date->format('Y-m-d') : date('Y-m-d') }}" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small">Ca khám: <span class="text-danger">*</span></label>
                                                                <select name="shift" class="form-select form-select-sm" required>
                                                                    <option value="morning" {{ $app->shift === 'morning' ? 'selected' : '' }}>Ca Sáng (07:30 - 11:30)</option>
                                                                    <option value="afternoon" {{ $app->shift === 'afternoon' ? 'selected' : '' }}>Ca Chiều (13:30 - 17:00)</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold small">Lý do điều phối / đổi bác sĩ:</label>
                                                            <input type="text" name="reassign_reason" class="form-control form-control-sm" placeholder="VD: Bác sĩ ban đầu bận ca mổ khẩn cấp, bệnh nhân xin đổi ca...">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-warning btn-sm">Xác nhận điều phối</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Cancel Modal -->
                                @if($app->status === 'pending')
                                    <div class="modal fade" id="cancelModal-{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form method="POST" action="{{ route('reception.appointments.cancel', $app) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger">Hủy lịch hẹn: {{ $app->appointment_code }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-2">Bệnh nhân: <strong>{{ $app->patientProfile->full_name }}</strong></p>
                                                        <label class="form-label fw-semibold small">Lý do hủy lịch khám: <span class="text-danger">*</span></label>
                                                        <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Bệnh nhân báo hủy, không đến viện..."></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">Xác nhận hủy ca</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không có ca khám nào trong ngày hoặc phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $appointments->links() }}
        </div>
    @endif
</div>
@endsection
