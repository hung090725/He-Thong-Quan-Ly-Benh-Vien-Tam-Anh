@extends('layouts.admin')

@section('title', 'Thêm Chuyên khoa Mới')

@section('content')
<x-page-header title="Thêm Chuyên khoa Mới" subtitle="Tạo mới chuyên mục khám chữa bệnh trong bệnh viện">
    <x-slot:actions>
        <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.specialties.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên chuyên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Tim mạch học, Thần kinh, Da liễu..." required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="Để trống hệ thống sẽ tự động tạo từ tên chuyên khoa">
                        <small class="text-muted">Chuỗi định danh URL chuẩn SEO (chỉ gồm chữ thường không dấu và dấu gạch ngang)</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label fw-semibold">Icon đại diện (Bootstrap Icons)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', 'bi bi-heart-pulse') }}" placeholder="Ví dụ: bi bi-heart-pulse, bi bi-capsule, bi bi-activity">
                        <small class="text-muted">Tên class Bootstrap Icons, xem tại <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Mô tả chuyên khoa</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Mô tả chức năng, nhiệm vụ, kỹ thuật điều trị mũi nhọn của chuyên khoa...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Kích hoạt hoạt động ngay</label>
                        </div>
                        <small class="text-muted">Chuyên khoa kích hoạt sẽ hiển thị trên trang chủ và form đặt lịch khám bệnh.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.specialties.index') }}" class="btn btn-light">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-lg"></i> Lưu chuyên khoa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
