@extends('layouts.admin')

@section('title', 'Chi tiết Chuyên khoa: ' . $specialty->name)

@section('content')
<x-page-header title="{{ $specialty->name }}" subtitle="Thông tin chi tiết chuyên khoa và danh sách bác sĩ trực thuộc">
    <x-slot:actions>
        <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa
        </a>
        <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row g-4">
    <!-- Specialty Info -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center mb-3 fs-1" style="width: 80px; height: 80px; border: 2px solid #e2e8f0;">
                    <i class="{{ $specialty->icon ?: 'bi bi-heart-pulse' }}"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $specialty->name }}</h4>
                <div class="mb-3">
                    <code>{{ $specialty->slug }}</code>
                </div>
                <div>
                    @if($specialty->is_active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Đang hoạt động
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-pause-circle me-1"></i> Tạm dừng
                        </span>
                    @endif
                </div>
            </div>
            <ul class="list-group list-group-flush border-top">
                <li class="list-group-item px-4 py-3">
                    <span class="text-muted d-block small">Tổng số bác sĩ phụ trách</span>
                    <span class="fw-bold fs-5 text-dark">{{ $specialty->doctors->count() }} bác sĩ</span>
                </li>
                <li class="list-group-item px-4 py-3">
                    <span class="text-muted d-block small">Ngày tạo chuyên khoa</span>
                    <span class="fw-medium text-dark">{{ $specialty->created_at ? $specialty->created_at->format('d/m/Y H:i') : '—' }}</span>
                </li>
            </ul>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent fw-bold py-3">Mô tả chuyên khoa</div>
            <div class="card-body p-4 text-secondary">
                {{ $specialty->description ?: 'Chưa có mô tả chi tiết cho chuyên khoa này.' }}
            </div>
        </div>
    </div>

    <!-- Doctors List in Specialty -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-badge text-primary me-2"></i> Bác sĩ trực thuộc chuyên khoa ({{ $specialty->doctors->count() }})
                </h5>
                <a href="{{ route('admin.doctors.create') }}?specialty_id={{ $specialty->id }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person-plus"></i> Thêm bác sĩ
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Họ tên & Học vị</th>
                                <th>Phòng khám</th>
                                <th>Giá khám</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-end pe-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialty->doctors as $doc)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $doc->full_name_with_title }}</div>
                                        <small class="text-muted">{{ $doc->user ? $doc->user->email : '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-geo-alt me-1"></i> {{ $doc->room_number }}
                                        </span>
                                    </td>
                                    <td class="fw-medium text-primary">
                                        {{ number_format($doc->consultation_fee, 0, ',', '.') }} VNĐ
                                    </td>
                                    <td class="text-center">
                                        @if($doc->is_available)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Sẵn sàng</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">Tạm dừng</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.doctors.show', $doc) }}" class="btn btn-outline-secondary" title="Xem bác sĩ">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.doctors.schedule', $doc) }}" class="btn btn-outline-info" title="Lịch trực tuần">
                                                <i class="bi bi-calendar3"></i>
                                            </a>
                                            <a href="{{ route('admin.doctors.edit', $doc) }}" class="btn btn-outline-primary" title="Sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Chưa có bác sĩ nào được phân công vào chuyên khoa này.
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
@endsection
