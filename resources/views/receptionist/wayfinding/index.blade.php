@extends('layouts.admin')

@section('title', 'Bản Đồ Điều Hướng Trong Nhà Thông Minh - BV Tâm Anh')

@section('content')
<x-page-header title="BẢN ĐỒ ĐIỀU HƯỚNG TRONG NHÀ THÔNG MINH (INDOOR WAYFINDING)" subtitle="Chỉ dẫn đường đi từng bước (Turn-by-turn navigation) từ Kiosk sảnh đến các phòng khám, xét nghiệm và bãi đỗ xe.">
    <x-slot:actions>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-add-route">
            <i class="bi bi-plus-circle-fill"></i> Thêm Lộ Trình Chỉ Dẫn Mới
        </button>
    </x-slot:actions>
</x-page-header>

<!-- Visual Interactive Map Preview -->
<div class="card border-0 shadow-sm mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); border-radius: 14px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center border-bottom border-indigo-900 pb-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-hospital fs-3 text-info"></i>
                <div>
                    <h6 class="fw-bold text-uppercase text-info mb-0">SƠ ĐỒ KHÔNG GIAN BỆNH VIỆN TÂM ANH (INTERACTIVE MAP)</h6>
                    <small class="text-white-50">Tự động dẫn đường tối ưu tiết kiệm thời gian đi lại cho người cao tuổi</small>
                </div>
            </div>
            <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-1.5 font-monospace">
                <i class="bi bi-geo-alt-fill me-1"></i>GPS Indoor Active
            </span>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10 h-100">
                    <strong class="text-info d-block mb-1"><i class="bi bi-1-circle me-1"></i>TẦNG 1 (SẢNH CHÍNH)</strong>
                    <div class="small text-white-50">Kiosk Tiếp Đón, Cấp Cứu 115, Quầy Dược Ngoại Trú, Thu Viện Phí VietQR</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10 h-100">
                    <strong class="text-info d-block mb-1"><i class="bi bi-2-circle me-1"></i>TẦNG 2 (CẬN LÂM SÀNG)</strong>
                    <div class="small text-white-50">Chụp MRI 3.0 Tesla, CT 768 Lát, Xét Nghiệm Máu Tự Động Roche, Siêu Âm Tim</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10 h-100">
                    <strong class="text-info d-block mb-1"><i class="bi bi-3-circle me-1"></i>TẦNG 3 (CHUYÊN KHOA)</strong>
                    <div class="small text-white-50">Trung Tâm Tim Mạch, Nội Tiết Tiểu Đường, Tiêu Hóa Gan Mật, Cơ Xương Khớp</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10 h-100">
                    <strong class="text-info d-block mb-1"><i class="bi bi-5-circle me-1"></i>TẦNG 5 (PHẪU THUẬT & ICU)</strong>
                    <div class="small text-white-50">Hệ Thống 12 Phòng Mổ Áp Lực Dương Hybrid, Khoa Hồi Sức Tích Cực ICU</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Routes Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-signpost-split text-primary fs-5"></i> DANH SÁCH LỘ TRÌNH ĐIỀU HƯỚNG TỪNG BƯỚC
        </h6>
        <span class="badge bg-light text-muted border">Tổng số: {{ $routes->total() }} lộ trình</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3">Điểm xuất phát</th>
                        <th>Khoa / Phòng đích đến</th>
                        <th class="text-center">Tầng</th>
                        <th class="text-center">Thời gian đi bộ</th>
                        <th>Các bước chỉ dẫn lộ trình (Turn-by-turn)</th>
                        <th class="text-center">Hỗ trợ xe lăn</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($routes as $r)
                    <tr>
                        <td class="ps-3 fw-semibold text-secondary">{{ $r->start_location }}</td>
                        <td>
                            <strong class="text-primary fs-6">{{ $r->destination_department }}</strong>
                        </td>
                        <td class="text-center fw-bold">Tầng {{ $r->floor_level }}</td>
                        <td class="text-center font-monospace fw-bold text-dark">
                            <i class="bi bi-stopwatch text-muted me-1"></i>{{ $r->estimated_walk_minutes }} phút
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @foreach($r->step_directions as $idx => $step)
                                    <div class="d-flex align-items-center gap-1.5 text-muted small">
                                        <span class="badge bg-primary-subtle text-primary rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem;">{{ $idx + 1 }}</span>
                                        <span>{{ $step }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                            @if($r->accessibility_friendly === 'wheelchair_accessible')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    ♿ Đi Thang Máy
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    Thang Bộ
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-map fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Chưa có lộ trình điều hướng nào được thiết lập.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($routes->hasPages())
        <div class="p-3 border-top">
            {{ $routes->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Thêm Lộ Trình (Bootstrap 5 Standard) -->
<div class="modal fade" id="modal-add-route" tabindex="-1" aria-labelledby="modalAddRouteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalAddRouteLabel">
                    <i class="bi bi-map-fill me-2"></i>Thêm Lộ Trình Bản Đồ Điều Hướng Mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reception.wayfinding.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Điểm Xuất Phát <span class="text-danger">*</span></label>
                        <input type="text" name="start_location" value="Kiosk Cảm Ứng Sảnh Cửa Số 01" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Khoa / Phòng Đích Đến <span class="text-danger">*</span></label>
                        <input type="text" name="destination_department" value="Phòng Khám Nội Tim Mạch (P.302)" required class="form-control">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tầng Số <span class="text-danger">*</span></label>
                            <input type="number" name="floor_level" value="3" required class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Ước Tính Đi Bộ (Phút) <span class="text-danger">*</span></label>
                            <input type="number" name="estimated_walk_minutes" value="3" required class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tiếp Cận Xe Lăn <span class="text-danger">*</span></label>
                            <select name="accessibility_friendly" class="form-select">
                                <option value="wheelchair_accessible">Có (Thang máy)</option>
                                <option value="stairs_only">Chỉ thang bộ</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Các Bước Hướng Dẫn Chi Tiết <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2">
                            <input type="text" name="step_directions[]" value="Từ cửa sảnh, đi thẳng 25m qua Quầy Lễ Tân Số 01." required class="form-control form-control-sm">
                            <input type="text" name="step_directions[]" value="Rẽ trái vào Cụm Thang Máy Khu B, bấm lên Tầng 3." required class="form-control form-control-sm">
                            <input type="text" name="step_directions[]" value="Bước ra khỏi thang máy rẽ phải 10m, phòng 302 ở phía tay trái." required class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-save-fill"></i> Lưu Lộ Trình
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
