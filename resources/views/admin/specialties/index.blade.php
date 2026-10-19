@extends('layouts.admin')

@section('title', 'Quản lý Chuyên khoa')

@section('content')
<x-page-header title="Quản lý Chuyên khoa" subtitle="Danh mục các chuyên khoa khám chữa bệnh tại Bệnh viện Tâm Anh">
    <x-slot:actions>
        <a href="{{ route('admin.specialties.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Thêm chuyên khoa mới
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Filter & Search Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.specialties.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Tìm theo tên chuyên khoa hoặc mô tả...">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                @if(request('search') || request()->filled('status'))
                    <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Specialties Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Chuyên khoa</th>
                        <th>Đường dẫn (Slug)</th>
                        <th class="text-center">Số bác sĩ</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-3" style="width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($specialties as $specialty)
                        <tr>
                            <td class="ps-3 text-muted fw-bold">#{{ $specialty->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fs-5" style="width: 40px; height: 40px; border: 1px solid #e2e8f0;">
                                        <i class="{{ $specialty->icon ?: 'bi bi-heart-pulse' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $specialty->name }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 320px;">
                                            {{ $specialty->description ?: 'Chưa có mô tả' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code>{{ $specialty->slug }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1">
                                    <i class="bi bi-person-badge text-primary me-1"></i> {{ $specialty->doctors_count }} bác sĩ
                                </span>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.specialties.toggle-status', $specialty) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Bấm để đổi trạng thái">
                                        @if($specialty->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i> Hoạt động
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-pause-circle me-1"></i> Tạm dừng
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small">
                                {{ $specialty->created_at ? $specialty->created_at->format('d/m/Y') : '—' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.specialties.show', $specialty) }}" class="btn btn-outline-secondary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.specialties.destroy', $specialty) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chuyên khoa này? Thao tác không thể hoàn tác!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa chuyên khoa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không tìm thấy chuyên khoa nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($specialties->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $specialties->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
