@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Hồ Sơ Bệnh Nhân - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Chỉnh Sửa Hồ Sơ Bệnh Nhân" subtitle="Cập nhật thông tin hành chính, số liên lạc và thông tin bảo hiểm y tế">
    <x-slot:actions>
        <a href="{{ route('reception.patients.show', $patient) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại chi tiết
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="card-title fw-bold text-primary mb-0">
                    <i class="bi bi-pencil-square me-1"></i> Cập Nhật Hồ Sơ: {{ $patient->full_name }}
                </h6>
                <span class="badge bg-primary font-monospace">{{ $patient->patient_code }}</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('reception.patients.update', $patient) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Họ và tên bệnh nhân: <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name', $patient->full_name) }}" class="form-control @error('full_name') is-invalid @enderror" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số điện thoại liên hệ: <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Ngày sinh: <span class="text-danger">*</span></label>
                            <input type="date" name="dob" value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" class="form-control @error('dob') is-invalid @enderror" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Giới tính: <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Địa chỉ Email:</label>
                            <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Identity Card -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số CCCD / CMND / Hộ chiếu:</label>
                            <input type="text" name="identity_card" value="{{ old('identity_card', $patient->identity_card) }}" class="form-control @error('identity_card') is-invalid @enderror">
                            @error('identity_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Insurance Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã số thẻ Bảo hiểm Y tế (BHYT):</label>
                            <input type="text" name="insurance_code" value="{{ old('insurance_code', $patient->insurance_code) }}" class="form-control @error('insurance_code') is-invalid @enderror">
                            @error('insurance_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Địa chỉ thường trú / tạm trú:</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clinical Safety & Patient Demographics (Blood Group, Allergies, History) -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-danger d-flex align-items-center gap-1 mb-2">
                                <i class="bi bi-shield-exclamation"></i> An Toàn Lâm Sàng & Tiền Sử Y Khoa
                            </h6>
                            <p class="text-muted small mb-3">Dữ liệu hỗ trợ Bác sĩ cảnh báo tương tác thuốc và sốc phản vệ khi khám chữa bệnh.</p>
                        </div>

                        <!-- Blood Group -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Nhóm máu:</label>
                            <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                <option value="">-- Chưa rõ --</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>Nhóm {{ $bg }}</option>
                                @endforeach
                            </select>
                            @error('blood_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Allergies -->
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small">Tiền sử Dị ứng (Thuốc, Thức ăn, Hóa chất):</label>
                            <input type="text" name="allergies" value="{{ old('allergies', $patient->allergies) }}" class="form-control @error('allergies') is-invalid @enderror" placeholder="VD: Dị ứng Penicillin, Aspirin, hải sản (Nếu không có, ghi 'Không')">
                            @error('allergies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medical History -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Tiền sử bệnh nền / Phẫu thuật / Bệnh mãn tính:</label>
                            <input type="text" name="medical_history" value="{{ old('medical_history', $patient->medical_history) }}" class="form-control @error('medical_history') is-invalid @enderror" placeholder="VD: Tăng huyết áp độ 1, Đái tháo đường Type 2, Tiền sử mổ ruột thừa 2020...">
                            @error('medical_history')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('reception.patients.show', $patient) }}" class="btn btn-light px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check2-circle"></i> Cập nhật thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
