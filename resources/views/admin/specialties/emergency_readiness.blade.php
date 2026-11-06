@extends('layouts.admin')

@section('title', 'Ma Trận Cấp Cứu & Báo Động Đỏ Chuyên Khoa')

@section('content')
<x-page-header title="Ma Trận Năng Lực Cấp Cứu & Báo Động Đỏ" subtitle="Giám sát thời gian thực năng lực tiếp nhận cấp cứu chuyên sâu, can thiệp mạch vành, ECMO và hồi sức tích cực">
    <x-slot:actions>
        <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh mục chuyên khoa
        </a>
        <a href="{{ route('admin.roster') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-calendar-week"></i> Ma trận ca trực tuần
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Emergency Readiness KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">STEMI Can thiệp 24/7</span>
                    <h4 class="fw-bold text-danger mb-0">{{ $stats['stemi_ready'] }} Khoa</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <div>
                    <span class="text-muted small">ECMO & Hồi sức ICU</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $stats['icu_ecmo_ready'] }} Khoa</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <span class="text-muted small">Khoa kích hoạt Code-Red</span>
                    <h4 class="fw-bold text-primary mb-0">{{ $stats['code_red_supported'] }} Khoa</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Tổng chuyên khoa vận hành</span>
                    <h4 class="fw-bold text-success mb-0">{{ $stats['total_specialties'] }} Khoa</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Matrix Table -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-bell-fill text-danger me-2"></i> Bảng Phân Bổ Năng Lực Trực Cấp Cứu Theo Chuyên Khoa
        </h6>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
            <i class="bi bi-broadcast me-1"></i> Trực Tuyến 24/7
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Chuyên khoa</th>
                        <th>Phân loại Năng lực Cấp cứu</th>
                        <th>Hotline Cấp cứu</th>
                        <th>Bác sĩ Trực ban Hôm nay</th>
                        <th class="text-center">Trạng thái Tiếp nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($specialties as $sp)
                        @php
                            $badge = $sp->emergency_readiness_badge;
                            $onDutyDocs = $sp->doctors->filter(fn($d) => $d->is_on_duty_today);
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold text-muted">#{{ $sp->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        <i class="{{ $sp->icon ?: 'bi bi-heart-pulse' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $sp->name }}</div>
                                        <small class="text-muted">{{ $sp->doctors->count() }} bác sĩ chuyên khoa</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $badge['class'] }} px-2 py-1 rounded-pill">
                                    <i class="bi bi-activity me-1"></i> {{ $badge['label'] }}
                                </span>
                                @if($sp->is_code_red_supported)
                                    <span class="badge bg-danger px-2 py-1 rounded-pill ms-1" title="Hỗ trợ kích hoạt Báo động đỏ toàn viện">
                                        CODE RED
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-danger font-monospace">
                                    <i class="bi bi-telephone-fill me-1"></i> {{ $sp->emergency_hotline ?: '1800 6858' }}
                                </span>
                            </td>
                            <td>
                                @if($onDutyDocs->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($onDutyDocs as $doc)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                                <i class="bi bi-person-check-fill me-1"></i> {{ $doc->full_name_with_title }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border">
                                        <i class="bi bi-clock-history me-1"></i> Thường trực trực ngoại viện
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($sp->is_code_red_supported)
                                    <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> SẴN SÀNG TIẾP NHẬN
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-clock me-1"></i> Khám Giờ Hành Chính
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
