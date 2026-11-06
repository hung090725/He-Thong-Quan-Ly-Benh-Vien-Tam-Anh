@extends('layouts.admin')

@section('title', 'Nhật Ký Kiểm Toán Y Tế (Doctor Audit Trail)')

@section('content')
<x-page-header title="Nhật Ký Kiểm Toán Hồ Sơ & Lịch Trực Bác Sĩ" subtitle="Lưu vết an toàn y tế toàn bộ can thiệp: điều chỉnh biểu phí, phân ca trực, duyệt nghỉ phép và cập nhật thông tin chuyên môn">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh sách bác sĩ
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Audit Logs Table -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-shield-lock-fill text-primary me-2"></i> Lịch Sử Can Thiệp & Truy Vết Bảo Mật (Audit Trail)
        </h6>
        <span class="badge bg-light text-muted border">Bất biến (Immutable)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Thời gian</th>
                        <th>Người thực hiện</th>
                        <th>Bác sĩ chịu tác động</th>
                        <th>Hành động y tế</th>
                        <th>Mô tả chi tiết can thiệp</th>
                        <th>Địa chỉ IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">#{{ $log->id }}</td>
                            <td class="text-muted small font-monospace">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}
                            </td>
                            <td>
                                <span class="badge bg-dark bg-opacity-75 text-white px-2 py-1 rounded-pill">
                                    <i class="bi bi-person-fill me-1"></i> {{ $log->user?->name ?: 'Hệ thống Admin' }}
                                </span>
                            </td>
                            <td>
                                @if($log->doctor)
                                    <div class="fw-semibold text-dark">{{ $log->doctor->full_name_with_title }}</div>
                                @else
                                    <span class="text-muted">Hồ sơ chung</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $log->description ?: 'Thực hiện thao tác điều phối' }}</div>
                                @if($log->old_values && $log->new_values)
                                    <div class="text-muted small mt-1 font-monospace" style="font-size: 0.75rem;">
                                        Trước: {{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }} &rarr; Sau: {{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-muted small font-monospace">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Chưa có bản ghi nhật ký kiểm toán nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
