@extends('layouts.admin')

@section('title', 'Tạo Mới Hồ Sơ Bệnh Nhân - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Tiếp Nhận & Tạo Hồ Sơ Bệnh Nhân Mới" subtitle="Đăng ký mã hồ sơ y tế chuẩn hóa và thông tin định danh bệnh nhân tại quầy lễ tân">
    <x-slot:actions>
        <a href="{{ route('reception.patients.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="card-title fw-bold text-primary mb-0">
                    <i class="bi bi-person-vcard-fill me-1"></i> Thông Tin Hành Chính Bệnh Nhân
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('reception.patients.store') }}" novalidate>
                    @csrf

                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>
                            Mã bệnh nhân dạng <strong>BN-YYYYMMDD-XXXX</strong> sẽ được hệ thống sinh tự động sau khi bấm lưu hồ sơ.
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Họ và tên bệnh nhân: <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control @error('full_name') is-invalid @enderror" placeholder="VD: Nguyễn Văn An" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số điện thoại liên hệ: <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="VD: 0987654321 hoặc +84987654321" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Ngày sinh: <span class="text-danger">*</span></label>
                            <input type="date" name="dob" value="{{ old('dob') }}" max="{{ date('Y-m-d') }}" class="form-control @error('dob') is-invalid @enderror" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Giới tính: <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Địa chỉ Email:</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="benhnhan@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Identity Card -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số CCCD / CMND / Hộ chiếu:</label>
                            <input type="text" name="identity_card" value="{{ old('identity_card') }}" class="form-control @error('identity_card') is-invalid @enderror" placeholder="VD: 001095012345">
                            @error('identity_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Insurance Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã số thẻ Bảo hiểm Y tế (BHYT):</label>
                            <input type="text" name="insurance_code" value="{{ old('insurance_code') }}" class="form-control @error('insurance_code') is-invalid @enderror" placeholder="VD: DN4010123456789">
                            @error('insurance_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Địa chỉ thường trú / tạm trú:</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('reception.patients.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check2-circle"></i> Lưu hồ sơ bệnh nhân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
