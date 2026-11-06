@extends('layouts.admin')

@section('title', 'Quản Lý Chứng Chỉ Hành Nghề (CCHN) Bác Sĩ')

@section('content')
<x-page-header title="Quản Lý Chứng Chỉ Hành Nghề & Giấy Phép Y Tế" subtitle="Theo dõi tính pháp lý hành nghề khám chữa bệnh, thời hạn giấy phép CCHN và cảnh báo gia hạn CME định kỳ theo quy định Bộ Y Tế">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh sách bác sĩ
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Alert Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">CCHN Hợp Lệ An Toàn</span>
                    <h4 class="fw-bold text-success mb-0">{{ $validCount }} Bác sĩ</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <span class="text-muted small">Sắp hết hạn (&le; 30 ngày)</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $expiringSoon->count() }} Bác sĩ</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Đã quá hạn giấy phép</span>
                    <h4 class="fw-bold text-danger mb-0">{{ $expired->count() }} Bác sĩ</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Warning Banners If Any Expiry Detected -->
@if($expired->isNotEmpty())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1 text-danger">CẢNH BÁO PHÁP LÝ: PHÁT HIỆN {{ $expired->count() }} BÁC SĨ ĐÃ QUÁ HẠN CHỨNG CHỈ HÀNH NGHỀ!</h6>
            <p class="small mb-0 text-danger-emphasis">
                Theo Luật Khám bệnh, chữa bệnh số 15/2023/QH15, bác sĩ có giấy phép hết hạn không được phân công ký hồ sơ bệnh án và đơn thuốc.
            </p>
        </div>
    </div>
@endif

@if($expiringSoon->isNotEmpty())
    <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
        <i class="bi bi-bell-fill fs-2 text-warning"></i>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1 text-warning-emphasis">NHẮC NHỞ GIA HẠN: CÓ {{ $expiringSoon->count() }} BÁC SĨ SẮP HẾT HẠN CCHN TRONG 30 NGÀY TỚI</h6>
            <p class="small mb-0 text-warning-emphasis">
                Vui lòng đôn đốc bác sĩ hoàn thành tín chỉ Đào tạo y khoa liên tục (CME) và nộp hồ sơ gia hạn lên Bộ Y Tế.
            </p>
        </div>
    </div>
@endif

<!-- Licenses Table -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-card-checklist text-primary me-2"></i> Danh Sách Chi Tiết Giấy Phép CCHN Bác Sĩ
        </h6>
        <span class="badge bg-light text-muted border">Tổng số: {{ $doctors->count() }} hồ sơ</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Bác sĩ</th>
                        <th>Chuyên khoa</th>
                        <th>Số CCHN</th>
                        <th>Cơ quan cấp</th>
                        <th>Ngày cấp</th>
                        <th>Ngày hết hạn</th>
                        <th class="text-center">Tình trạng pháp lý</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $d)
                        @php
                            $isExpiring = $d->is_cchn_expiring_soon;
                            $isExpired = $d->is_cchn_expired;
                        @endphp
                        <tr class="{{ $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '') }}">
                            <td class="ps-3 fw-bold text-muted">#{{ $d->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $d->full_name_with_title }}</div>
                                <small class="text-muted">{{ $d->user?->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                    {{ $d->specialty?->name }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold font-monospace text-dark">{{ $d->cchn_number ?: 'Chưa cập nhật' }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $d->cchn_issuer ?: 'Bộ Y Tế' }}
                            </td>
                            <td class="text-muted small">
                                {{ $d->cchn_issued_date ? $d->cchn_issued_date->format('d/m/Y') : '—' }}
                            </td>
                            <td>
                                @if($d->cchn_expiry_date)
                                    <span class="fw-semibold {{ $isExpired ? 'text-danger' : ($isExpiring ? 'text-warning' : 'text-dark') }}">
                                        {{ $d->cchn_expiry_date->format('d/m/Y') }}
                                    </span>
                                    @if($isExpiring)
                                        <small class="d-block text-warning fw-bold">Còn {{ $d->days_until_cchn_expiry }} ngày</small>
                                    @elseif($isExpired)
                                        <small class="d-block text-danger fw-bold">Quá hạn {{ abs($d->days_until_cchn_expiry) }} ngày</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($isExpired)
                                    <span class="badge bg-danger px-2 py-1 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> ĐÃ HẾT HẠN
                                    </span>
                                @elseif($isExpiring)
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">
                                        <i class="bi bi-clock-history me-1"></i> SẮP HẾT HẠN
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> HỢP LỆ
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.doctors.edit', $d) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square me-1"></i> Gia hạn
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
