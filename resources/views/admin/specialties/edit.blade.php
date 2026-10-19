@extends('layouts.admin')

@section('title', 'Chỉnh sửa Chuyên khoa: ' . $specialty->name)

@section('content')
<x-page-header title="Chỉnh sửa Chuyên khoa" subtitle="Cập nhật thông tin chuyên khoa: {{ $specialty->name }}">
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
                <form method="POST" action="{{ route('admin.specialties.update', $specialty) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên chuyên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $specialty->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $specialty->slug) }}">
                        <small class="text-muted">Định danh chuẩn SEO (chỉ chỉnh sửa nếu thật sự cần thiết)</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label fw-semibold">Icon đại diện (Bootstrap Icons)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="{{ $specialty->icon ?: 'bi bi-heart-pulse' }}"></i></span>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $specialty->icon) }}">
                        </div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Mô tả chuyên khoa</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $specialty->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $specialty->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Trạng thái hoạt động</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.specialties.index') }}" class="btn btn-light">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-lg"></i> Cập nhật chuyên khoa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
