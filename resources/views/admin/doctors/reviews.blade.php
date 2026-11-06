@extends('layouts.admin')

@section('title', 'Quản Lý Đánh Giá & Phản Hồi Bác Sĩ')

@section('content')
<x-page-header title="Quản Lý Đánh Giá Bác Sĩ Từ Người Bệnh" subtitle="Kiểm duyệt và quản trị các ý kiến phản hồi, đánh giá chất lượng dịch vụ khám bệnh của đội ngũ bác sĩ">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh sách bác sĩ
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Reviews Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Điểm trung bình toàn viện</span>
                    <h4 class="fw-bold text-warning mb-0">⭐ {{ $avgScore }} / 5.0</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-chat-quote-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Tổng số lượt đánh giá</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalReviews }} nhận xét</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <span class="text-muted small">Tỷ lệ hài lòng</span>
                    <h4 class="fw-bold text-success mb-0">98.5% Tích cực</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-stars text-warning me-2"></i> Danh Sách Nhận Xét Chi Tiết
        </h6>
        <span class="badge bg-light text-muted border">Bệnh nhân đã xác thực</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Bác sĩ được đánh giá</th>
                        <th>Người bệnh nhận xét</th>
                        <th class="text-center">Số sao</th>
                        <th>Nội dung nhận xét</th>
                        <th>Thời gian</th>
                        <th class="text-center">Trạng thái duyệt</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">#{{ $rev->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rev->doctor?->full_name_with_title }}</div>
                                <small class="text-muted">{{ $rev->doctor?->specialty?->name }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rev->patient_name }}</div>
                                <small class="text-muted">{{ $rev->patient_phone }}</small>
                            </td>
                            <td class="text-center text-warning fw-bold">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="bi bi-star{{ $s <= $rev->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </td>
                            <td>
                                <p class="mb-0 small text-dark" style="max-width: 320px;">
                                    "{{ $rev->comment }}"
                                </p>
                            </td>
                            <td class="text-muted small">
                                {{ $rev->created_at ? $rev->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="text-center">
                                @if($rev->is_approved)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> Đã hiển thị
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">
                                        <i class="bi bi-eye-slash me-1"></i> Đang ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <form method="POST" action="{{ route('admin.doctors.reviews.toggle', $rev) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $rev->is_approved ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3">
                                        <i class="bi bi-{{ $rev->is_approved ? 'eye-slash' : 'check2' }} me-1"></i>
                                        {{ $rev->is_approved ? 'Ẩn nhận xét' : 'Duyệt hiển thị' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Chưa có nhận xét nào từ bệnh nhân.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
