@extends('layouts.admin')

@section('title', 'Nhật Ký Bảo Mật & Giám Sát Truy Cập - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Nhật Ký Giám Sát & Kiểm Toán Bảo Mật" subtitle="Theo dõi mọi sự kiện xác thực, phân quyền, truy cập bất thường và an ninh hệ thống" />

<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-primary">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold">Tổng số sự kiện</span>
                <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-info">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold">Sự kiện hôm nay</span>
                <div class="fs-4 fw-bold text-info mt-1">{{ number_format($stats['today']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-warning">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold">Cảnh báo (Warnings)</span>
                <div class="fs-4 fw-bold text-warning mt-1">{{ number_format($stats['warnings']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-danger">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold">Nghiêm trọng (Critical)</span>
                <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($stats['critical']) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions Card -->
<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body">
        <form action="{{ route('admin.security-audit.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Loại sự kiện:</label>
                <select name="event_type" class="form-select">
                    <option value="">-- Tất cả loại sự kiện --</option>
                    @foreach($eventTypes as $t)
                        <option value="{{ $t }}" {{ ($filters['event_type'] ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Mức độ cảnh báo:</label>
                <select name="severity" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="info" {{ ($filters['severity'] ?? '') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ ($filters['severity'] ?? '') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="critical" {{ ($filters['severity'] ?? '') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tìm kiếm (IP, mô tả):</label>
                <input type="text" name="search" class="form-control" placeholder="Nhập địa chỉ IP hoặc từ khóa..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                <a href="{{ route('admin.security-audit.index') }}" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
                <a href="{{ route('admin.security-audit.export', request()->query()) }}" class="btn btn-outline-success text-nowrap" title="Xuất CSV chuẩn UTF-8 BOM cho Excel">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Xuất CSV (UTF-8)
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Audit Log Table -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 170px;">Thời gian</th>
                    <th style="width: 140px;">Mức độ</th>
                    <th style="width: 200px;">Sự kiện</th>
                    <th style="width: 180px;">Tài khoản</th>
                    <th style="width: 140px;">Địa chỉ IP</th>
                    <th>Nội dung chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="font-monospace text-muted small">#{{ $log->id }}</td>
                        <td class="small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($log->severity === 'critical')
                                <span class="badge bg-danger text-uppercase px-2 py-1"><i class="bi bi-fire me-1"></i>CRITICAL</span>
                            @elseif($log->severity === 'warning')
                                <span class="badge bg-warning text-dark text-uppercase px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>WARNING</span>
                            @else
                                <span class="badge bg-info-subtle text-info-emphasis text-uppercase px-2 py-1"><i class="bi bi-info-circle me-1"></i>INFO</span>
                            @endif
                        </td>
                        <td>
                            <code class="fw-bold">{{ $log->event_type }}</code>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="fw-semibold small text-dark">{{ $log->user->name }}</div>
                                <div class="text-muted small font-monospace">{{ $log->user->email }}</div>
                            @else
                                <span class="text-muted small fst-italic">Khách vãng lai / Hệ thống</span>
                            @endif
                        </td>
                        <td class="font-monospace small">{{ $log->ip_address }}</td>
                        <td class="small text-secondary">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check display-5 text-success d-block mb-2"></i>
                            Không tìm thấy bản ghi nhật ký kiểm toán bảo mật nào phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
