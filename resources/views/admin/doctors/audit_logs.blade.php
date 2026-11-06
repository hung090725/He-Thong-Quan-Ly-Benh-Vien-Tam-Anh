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
                                <div class="fw-semibold text-dark">{{ $log->description ?: 'Thực hiện thao tác điều phối' }}</div>
                                @php
                                    $old = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                                    $new = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                                @endphp
                                @if(!empty($old) || !empty($new))
                                    <div class="mt-2 d-flex flex-column gap-1">
                                        @if(!empty($old))
                                            <div class="small text-muted d-flex flex-wrap align-items-center gap-1">
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Trước:</span>
                                                @foreach($old as $k => $v)
                                                    @if($v !== null && $v !== '')
                                                        @php
                                                            $lbl = match($k) {
                                                                'cchn_number' => 'Số CCHN',
                                                                'cchn_issuer' => 'Cơ quan',
                                                                'cchn_expiry_date' => 'Ngày hết hạn',
                                                                'cme_hours' => 'Giờ CME',
                                                                default => $k
                                                            };
                                                            $val = is_string($v) ? str_replace('\\/', '/', $v) : json_encode($v, JSON_UNESCAPED_UNICODE);
                                                        @endphp
                                                        <span class="badge bg-light text-dark border px-2 py-1">
                                                            {{ $lbl }}: <strong>{{ $val }}</strong>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($new))
                                            <div class="small text-dark d-flex flex-wrap align-items-center gap-1">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Sau:</span>
                                                @foreach($new as $k => $v)
                                                    @if($v !== null && $v !== '')
                                                        @php
                                                            $lbl = match($k) {
                                                                'cchn_number' => 'Số CCHN',
                                                                'cchn_issuer' => 'Cơ quan',
                                                                'cchn_expiry_date' => 'Ngày hết hạn mới',
                                                                'cme_hours' => 'Tín chỉ CME',
                                                                default => $k
                                                            };
                                                            $val = is_string($v) ? str_replace('\\/', '/', $v) : json_encode($v, JSON_UNESCAPED_UNICODE);
                                                        @endphp
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                            {{ $lbl }}: <strong>{{ $val }}{{ $k === 'cme_hours' ? ' giờ' : '' }}</strong>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
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
