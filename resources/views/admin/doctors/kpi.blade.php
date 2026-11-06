@extends('layouts.admin')

@section('title', 'Bảng Điểm KPI & Năng Suất Khám Bác Sĩ')

@section('content')
<x-page-header title="Bảng Điểm KPI & Năng Suất Bác Sĩ" subtitle="Đo lường năng suất khám chữa bệnh, doanh thu tích lũy, tỷ lệ lấp đầy lịch trực và xếp hạng thi đua y khoa">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh sách bác sĩ
        </a>
        <a href="{{ route('admin.doctors.export.csv') }}" class="btn btn-success d-inline-flex align-items-center gap-1">
            <i class="bi bi-file-earmark-excel"></i> Xuất báo cáo CSV
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Quick Stats Summary -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-check2-all"></i>
                </div>
                <div>
                    <span class="text-muted small">Tổng ca khám hoàn thành</span>
                    <h4 class="fw-bold text-dark mb-0">{{ number_format($totalCompleted) }} lượt</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-muted small">Tổng doanh thu từ phí khám</span>
                    <h4 class="fw-bold text-success mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Điểm hài lòng trung bình</span>
                    <h4 class="fw-bold text-warning mb-0">⭐ {{ $avgRating }} / 5.0</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Scorecard Table -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-trophy-fill text-warning me-2"></i> Bảng Xếp Hạng Thi Đua & Đánh Giá Năng Suất Bác Sĩ
        </h6>
        <span class="badge bg-light text-muted border">Toàn viện</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">Xếp hạng</th>
                        <th>Bác sĩ</th>
                        <th>Chuyên khoa</th>
                        <th>Biểu phí</th>
                        <th class="text-center">Số ca khám</th>
                        <th class="text-end">Doanh thu tạo ra</th>
                        <th class="text-center">Tỷ lệ lấp đầy lịch trực</th>
                        <th class="text-center">Đánh giá</th>
                        <th class="text-center">Phân hạng KPI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $index => $doc)
                        <tr>
                            <td class="ps-3 fw-bold">
                                @if($index === 0)
                                    <span class="badge bg-warning text-dark rounded-circle p-2">🥇 1</span>
                                @elseif($index === 1)
                                    <span class="badge bg-secondary text-white rounded-circle p-2">🥈 2</span>
                                @elseif($index === 2)
                                    <span class="badge bg-danger bg-opacity-75 text-white rounded-circle p-2">🥉 3</span>
                                @else
                                    <span class="text-muted ps-2">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $doc->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                    {{ $doc->specialty }}
                                </span>
                            </td>
                            <td class="text-muted">
                                {{ number_format($doc->fee, 0, ',', '.') }} đ
                            </td>
                            <td class="text-center fw-bold text-primary">
                                {{ $doc->completed_count }} ca
                            </td>
                            <td class="text-end fw-bold text-success font-monospace">
                                {{ number_format($doc->revenue, 0, ',', '.') }} đ
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                        <div class="progress-bar {{ $doc->occupancy_rate > 80 ? 'bg-danger' : ($doc->occupancy_rate > 50 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min($doc->occupancy_rate, 100) }}%"></div>
                                    </div>
                                    <small class="fw-semibold">{{ $doc->occupancy_rate }}%</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">
                                    ⭐ {{ $doc->rating_avg }} ({{ $doc->rating_count }})
                                </span>
                            </td>
                            <td class="text-center">
                                @if(str_contains($doc->tier, 'A+'))
                                    <span class="badge bg-success px-3 py-1 rounded-pill shadow-sm">
                                        <i class="bi bi-star-fill me-1"></i> {{ $doc->tier }}
                                    </span>
                                @elseif(str_contains($doc->tier, 'A'))
                                    <span class="badge bg-primary px-3 py-1 rounded-pill">
                                        {{ $doc->tier }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        {{ $doc->tier }}
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
