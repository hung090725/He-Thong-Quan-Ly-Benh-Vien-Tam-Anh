@extends('layouts.admin')

@section('title', 'Chỉnh sửa Bác sĩ: ' . $doctor->full_name_with_title)

@section('content')
<x-page-header title="Chỉnh sửa Bác sĩ" subtitle="Cập nhật thông tin chuyên môn cho {{ $doctor->full_name_with_title }}">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.schedule', $doctor) }}" class="btn btn-outline-info d-inline-flex align-items-center gap-1">
            <i class="bi bi-calendar3"></i> Cấu hình lịch trực
        </a>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-person-lock text-primary me-2"></i> 1. Thông tin Cá nhân & Tài khoản
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Họ và tên bác sĩ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $doctor->user ? $doctor->user->name : '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email liên hệ <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $doctor->user ? $doctor->user->email : '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $doctor->user ? $doctor->user->phone : '') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-briefcase text-primary me-2"></i> 2. Hồ sơ Chuyên môn & Phòng khám
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="specialty_id" class="form-label fw-semibold">Chuyên khoa trực thuộc <span class="text-danger">*</span></label>
                            <select name="specialty_id" id="specialty_id" class="form-select @error('specialty_id') is-invalid @enderror" required>
                                <option value="">-- Chọn chuyên khoa --</option>
                                @foreach($specialties as $sp)
                                    <option value="{{ $sp->id }}" {{ old('specialty_id', $doctor->specialty_id) == $sp->id ? 'selected' : '' }}>
                                        {{ $sp->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="qualification" class="form-label fw-semibold">Học vị / Học hàm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" required>
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="room_number" class="form-label fw-semibold">Phòng khám được phân công <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number', $doctor->room_number) }}" required>
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="consultation_fee" class="form-label fw-semibold">Giá khám bệnh (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="10000" min="0" class="form-control @error('consultation_fee') is-invalid @enderror" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee', (int)$doctor->consultation_fee) }}" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('consultation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="avatar" class="form-label fw-semibold">Ảnh chân dung bác sĩ (Avatar)</label>
                            @if($doctor->avatar_url)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->full_name_with_title }}" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Đã có ảnh đại diện. Chọn tệp mới nếu muốn thay đổi.</small>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Định dạng JPG, PNG, WEBP (Tối đa 2MB)</small>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="bio" class="form-label fw-semibold">Tiểu sử & Quá trình công tác</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $doctor->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_available" name="is_available" value="1" {{ old('is_available', $doctor->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_available">Sẵn sàng nhận lịch khám bệnh</label>
                            </div>
                        </div>
                    </div>

                    <!-- Hồ sơ Pháp lý & Chứng Chỉ Hành Nghề (CCHN) -->
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-patch-check text-success me-2"></i> 3. Giấy Phép Hành Nghề Y Tế (CCHN & Đào tạo CME)
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cchn_number" class="form-label fw-semibold">Số Chứng Chỉ Hành Nghề (CCHN)</label>
                            <input type="text" class="form-control @error('cchn_number') is-invalid @enderror" id="cchn_number" name="cchn_number" value="{{ old('cchn_number', $doctor->cchn_number) }}" placeholder="Ví dụ: 004812/BYT-CCHN">
                            @error('cchn_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="cchn_issuer" class="form-label fw-semibold">Cơ quan cấp CCHN</label>
                            <input type="text" class="form-control @error('cchn_issuer') is-invalid @enderror" id="cchn_issuer" name="cchn_issuer" value="{{ old('cchn_issuer', $doctor->cchn_issuer ?? 'Bộ Y Tế') }}" placeholder="Ví dụ: Bộ Y Tế, Sở Y Tế Hà Nội...">
                            @error('cchn_issuer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cchn_issued_date" class="form-label fw-semibold">Ngày cấp phép</label>
                            <input type="date" class="form-control @error('cchn_issued_date') is-invalid @enderror" id="cchn_issued_date" name="cchn_issued_date" value="{{ old('cchn_issued_date', optional($doctor->cchn_issued_date)->format('Y-m-d')) }}">
                            @error('cchn_issued_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cchn_expiry_date" class="form-label fw-semibold">Ngày hết hạn CCHN</label>
                            <input type="date" class="form-control @error('cchn_expiry_date') is-invalid @enderror" id="cchn_expiry_date" name="cchn_expiry_date" value="{{ old('cchn_expiry_date', optional($doctor->cchn_expiry_date)->format('Y-m-d')) }}">
                            <small class="text-muted">Để trống nếu giấy phép không thời hạn</small>
                            @error('cchn_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cme_hours" class="form-label fw-semibold">Số giờ đào tạo CME liên tục</label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control @error('cme_hours') is-invalid @enderror" id="cme_hours" name="cme_hours" value="{{ old('cme_hours', $doctor->cme_hours ?? 48) }}">
                                <span class="input-group-text">Giờ / chu kỳ</span>
                            </div>
                            @error('cme_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.doctors.index') }}" class="btn btn-light">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-lg"></i> Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
