@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Giấy Nghỉ Việc Hưởng BHXH - BV Tâm Anh')

@section('content')
<x-page-header title="Chỉnh Sửa Giấy Nghỉ Việc Hưởng BHXH" subtitle="Mã số giấy: {{ $existing->certificate_code }} &bull; Cập nhật thông tin chỉ định nghỉ">
    <x-slot:actions>
        <a href="{{ route('reception.certificates.print', $existing) }}" target="_blank" class="btn btn-outline-dark d-inline-flex align-items-center gap-1">
            <i class="bi bi-printer"></i> In bản hiện tại
        </a>
        <a href="{{ route('doctor.records.show', $record) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về bệnh án
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center mb-5">
    <div class="col-lg-9">
        <form method="POST" action="{{ route('reception.certificates.store', $record) }}">
            @csrf

            <!-- Patient Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard me-1"></i> Thông Tin Người Lao Động (Bệnh Nhân)
                    </h6>
                    <span class="badge bg-light text-dark font-monospace">Mã BN: {{ $record->patientProfile->patient_code }}</span>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-25">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-0">Họ và tên người lao động:</label>
                            <div class="fs-6 fw-bold text-dark text-uppercase">{{ $record->patientProfile->full_name }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-0">Ngày sinh:</label>
                            <div class="fw-semibold text-dark">{{ $record->patientProfile->dob?->format('d/m/Y') }} ({{ $record->patientProfile->gender_name }})</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-0">Số CCCD / CMND:</label>
                            <div class="font-monospace fw-semibold text-dark">{{ $record->patientProfile->identity_card ?: 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-0">Mã số BHXH / Thẻ BHYT:</label>
                            <div class="font-monospace fw-bold text-primary fs-6">{{ $record->patientProfile->insurance_code ?: 'Không có thẻ BHYT' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-0">Bác sĩ khám chỉ định:</label>
                            <div class="fw-semibold text-dark">{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificate Details Input Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="bi bi-file-earmark-medical me-1"></i> Nội Dung Chỉ Định Nghỉ Việc Hưởng BHXH
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">{{ $existing->certificate_code }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Employer Name -->
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Đơn vị công tác / Cơ quan làm việc của người lao động:</label>
                            <input type="text" name="employer_name" value="{{ old('employer_name', $existing->employer_name) }}" class="form-control" placeholder="Tên công ty / cơ quan..." autofocus>
                        </div>

                        <!-- Diagnosis -->
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Chẩn đoán và diễn giải tình trạng bệnh: <span class="text-danger">*</span></label>
                            <input type="text" name="diagnosis" value="{{ old('diagnosis', $existing->diagnosis) }}" class="form-control fw-semibold text-dark @error('diagnosis') is-invalid @enderror" required>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Treatment Method -->
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phương pháp điều trị: <span class="text-danger">*</span></label>
                            <select name="treatment_method" class="form-select @error('treatment_method') is-invalid @enderror" required>
                                <option value="Điều trị ngoại trú" {{ old('treatment_method', $existing->treatment_method) === 'Điều trị ngoại trú' ? 'selected' : '' }}>Điều trị ngoại trú</option>
                                <option value="Nghỉ dưỡng bệnh điều trị tại nhà" {{ old('treatment_method', $existing->treatment_method) === 'Nghỉ dưỡng bệnh điều trị tại nhà' ? 'selected' : '' }}>Nghỉ dưỡng bệnh tại nhà</option>
                                <option value="Chăm sóc sau phẫu thuật ngoại trú" {{ old('treatment_method', $existing->treatment_method) === 'Chăm sóc sau phẫu thuật ngoại trú' ? 'selected' : '' }}>Chăm sóc sau thủ thuật/tiểu phẫu</option>
                            </select>
                        </div>

                        <!-- Leave Days -->
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Số ngày cho nghỉ: <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="leave_days" id="leave_days" value="{{ old('leave_days', $existing->leave_days) }}" min="1" max="30" class="form-control fw-bold text-primary @error('leave_days') is-invalid @enderror" required onchange="calculateToDate()">
                                <span class="input-group-text">ngày</span>
                            </div>
                            @error('leave_days')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- From Date -->
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Nghỉ từ ngày: <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', $existing->from_date->format('Y-m-d')) }}" class="form-control fw-semibold" required onchange="calculateToDate()">
                        </div>

                        <!-- Calculated To Date (Visual Preview) -->
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Đến hết ngày (Tự động tính):</label>
                            <input type="text" id="to_date_preview" class="form-control bg-light fw-bold text-success" readonly>
                        </div>

                        <!-- Father / Mother Name -->
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Họ và tên cha/mẹ (Nghỉ chăm sóc con ốm dưới 7 tuổi):</label>
                            <input type="text" name="father_mother_name" value="{{ old('father_mother_name', $existing->father_mother_name) }}" class="form-control">
                        </div>

                        <!-- Doctor Notes -->
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Lời dặn chế độ sinh hoạt / Tái khám:</label>
                            <input type="text" name="notes" value="{{ old('notes', $existing->notes) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('doctor.records.show', $record) }}" class="btn btn-light px-4">Hủy</a>
                <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm">
                    <i class="bi bi-check-circle-fill fs-5"></i> Cập Nhật & Mở Bản In C65-HD
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function calculateToDate() {
        const fromInput = document.getElementById('from_date');
        const daysInput = document.getElementById('leave_days');
        const preview = document.getElementById('to_date_preview');

        if (!fromInput.value || !daysInput.value) return;

        const fromDate = new Date(fromInput.value);
        const days = parseInt(daysInput.value, 10);

        if (isNaN(fromDate.getTime()) || isNaN(days) || days < 1) return;

        const toDate = new Date(fromDate);
        toDate.setDate(toDate.getDate() + (days - 1));

        const dayStr = String(toDate.getDate()).padStart(2, '0');
        const monthStr = String(toDate.getMonth() + 1).padStart(2, '0');
        const yearStr = toDate.getFullYear();

        preview.value = `${dayStr}/${monthStr}/${yearStr} (${days} ngày)`;
    }

    document.addEventListener('DOMContentLoaded', calculateToDate);
</script>
@endpush
@endsection
