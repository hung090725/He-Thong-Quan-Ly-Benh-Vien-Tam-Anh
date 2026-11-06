@extends('layouts.admin')

@section('title', 'Quản Lý Buồng Bệnh & Điều Phối Giường Trống Smart Bed - BV Tâm Anh')

@section('content')
<x-page-header title="QUẢN LÝ BUỒNG BỆNH, KHỬ KHUẨN & ĐIỀU PHỐI GIƯỜNG TRỐNG" subtitle="Sơ đồ buồng bệnh thời gian thực, quy trình luân chuyển giường khử khuẩn vô trùng trước khi nhận bệnh mới.">
    <x-slot:actions>
        <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                <i class="bi bi-check-circle-fill me-1"></i> Giường Trống: {{ $stats['available_clean'] }}
            </span>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6">
                <i class="bi bi-person-fill me-1"></i> Đang Nằm: {{ $stats['occupied'] }}
            </span>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 fs-6">
                <i class="bi bi-hourglass-split me-1"></i> Chờ Khử Trùng: {{ $stats['dirty_pending'] }}
            </span>
        </div>
    </x-slot:actions>
</x-page-header>

<!-- Rooms and Beds Visual Grid -->
<div class="d-flex flex-column gap-4">
    @forelse($rooms as $room)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary fs-6 font-monospace px-2.5 py-1.5">
                    {{ $room->room_number }}
                </span>
                <div>
                    <h6 class="fw-bold text-dark mb-0">{{ $room->department }}</h6>
                    <small class="text-muted">Tầng {{ $room->floor }} &bull; Loại: {{ $room->room_type }}</small>
                </div>
            </div>
            <span class="badge bg-light text-muted border">Sức chứa: {{ $room->beds->count() }} giường</span>
        </div>

        <div class="card-body p-4">
            <div class="row g-3">
                @foreach($room->beds as $bed)
                @php
                    $cardBorder = match($bed->status) {
                        'available_clean' => 'border-success',
                        'occupied' => 'border-danger',
                        default => 'border-warning',
                    };
                    $cardBg = match($bed->status) {
                        'available_clean' => 'bg-success-subtle bg-opacity-25',
                        'occupied' => 'bg-danger-subtle bg-opacity-25',
                        default => 'bg-warning-subtle bg-opacity-25',
                    };
                @endphp
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-2 {{ $cardBorder }} {{ $cardBg }} shadow-xs">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold font-monospace text-dark mb-0">{{ $bed->bed_code }}</h5>
                                    @if($bed->status === 'available_clean')
                                        <span class="badge bg-success text-white text-uppercase" style="font-size: 0.68rem;">Trống Sạch</span>
                                    @elseif($bed->status === 'occupied')
                                        <span class="badge bg-danger text-white text-uppercase" style="font-size: 0.68rem;">Có Người</span>
                                    @else
                                        <span class="badge bg-warning text-dark text-uppercase" style="font-size: 0.68rem;">Chờ Khử Trùng</span>
                                    @endif
                                </div>

                                @if($bed->status === 'occupied' && $bed->currentPatient)
                                    <div class="p-2 bg-white rounded-3 border mb-3">
                                        <div class="small text-muted mb-0.5" style="font-size: 0.72rem;">Bệnh nhân:</div>
                                        <strong class="text-primary d-block">{{ $bed->currentPatient->full_name }}</strong>
                                        <div class="text-muted small font-monospace" style="font-size: 0.72rem;">{{ $bed->currentPatient->patient_code }}</div>
                                    </div>
                                @elseif($bed->status === 'dirty_pending')
                                    <div class="alert alert-warning py-2 px-2 small mb-3" style="font-size: 0.74rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Bệnh nhân vừa trả giường. Cần lau khử trùng Cloramin B trước khi nhận bệnh mới.
                                    </div>
                                @else
                                    <div class="alert alert-success py-2 px-2 small mb-3" style="font-size: 0.74rem;">
                                        <i class="bi bi-shield-check me-1"></i> Đã khử khuẩn vô trùng lúc {{ $bed->last_disinfected_at ? $bed->last_disinfected_at->format('H:i') : '08:00' }}. Sẵn sàng đón người bệnh.
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2">
                                @if($bed->status === 'occupied')
                                    <form action="{{ route('reception.bed-board.discharge', $bed) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-box-arrow-right"></i> Xuất Viện / Trả Giường
                                        </button>
                                    </form>
                                @elseif($bed->status === 'dirty_pending')
                                    <form action="{{ route('reception.bed-board.clean', $bed) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cleaner_name" value="NV. Nguyễn Thị Lan (Hộ lý)">
                                        <input type="hidden" name="disinfectant_chemical" value="Cloramin B 0.5% + Aniospray 29M">
                                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 text-dark shadow-sm">
                                            <i class="bi bi-spray"></i> Đã Khử Khuẩn Xong
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="openAssignModal({{ $bed->id }}, '{{ $bed->bed_code }}')" class="btn btn-success btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                                        <i class="bi bi-person-plus-fill"></i> Xếp Bệnh Nhân Vào
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="card shadow-sm border-0 text-center py-5 text-muted">
        <i class="bi bi-hospital fs-1 text-secondary opacity-50 d-block mb-2"></i>
        Chưa có buồng bệnh nào được thiết lập.
    </div>
    @endforelse
</div>

<!-- Modal Xếp Bệnh Nhân Vào Giường (Bootstrap 5 Standard) -->
<div class="modal fade" id="modal-assign-bed" tabindex="-1" aria-labelledby="modalAssignBedLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="modalAssignBedLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Xếp Bệnh Nhân Vào Giường: <span id="assign-bed-code" class="font-monospace text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-assign-bed" action="{{ route('reception.bed-board.assign-direct') }}" method="POST">
                @csrf
                <input type="hidden" name="bed_id" id="assign-bed-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Chọn Bệnh Nhân Nội Trú / Hồi Tỉnh <span class="text-danger">*</span></label>
                        <select name="patient_profile_id" required class="form-select">
                            <option value="">-- Chọn bệnh nhân --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-check2-circle"></i> Xác Nhận Nhận Giường
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAssignModal(bedId, bedCode) {
        document.getElementById('assign-bed-code').innerText = bedCode;
        document.getElementById('assign-bed-id').value = bedId;
        document.getElementById('form-assign-bed').action = '/reception/bed-board/assign/' + bedId;
        const modalEl = document.getElementById('modal-assign-bed');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
@endsection
