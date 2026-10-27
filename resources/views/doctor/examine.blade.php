@extends('layouts.admin')

@section('title', 'Bàn Khám Bác Sĩ - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bàn Khám Bệnh Ngoại Trú" subtitle="Ghi nhận hồ sơ bệnh án, chẩn đoán lâm sàng và kê đơn thuốc điều trị">
    <x-slot:actions>
        <a href="{{ route('doctor.queue') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại hàng đợi
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row g-4 mb-5">
    <!-- Left Column: Patient Profile & Initial Vitals -->
    <div class="col-lg-4">
        <!-- Patient Demographics Card -->
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 1rem;">
            <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge me-1"></i> Bệnh Nhân Đang Khám
                </h6>
                @if($appointment->is_priority)
                    <span class="badge bg-danger text-white border border-white small">
                        <i class="bi bi-star-fill text-warning me-1"></i> ƯU TIÊN
                    </span>
                @endif
            </div>
            <div class="card-body p-3">
                <div class="text-center pb-3 border-bottom">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fs-2 mb-2" style="width: 64px; height: 64px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark text-uppercase mb-0">{{ $appointment->patientProfile->full_name }}</h5>
                    <div class="text-muted small">
                        Mã BN: <strong class="font-monospace text-primary">{{ $appointment->patientProfile->patient_code }}</strong>
                    </div>
                </div>

                <ul class="list-unstyled mb-0 py-3 d-flex flex-column gap-2 small border-bottom">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Tuổi / Giới tính:</span>
                        <strong class="text-dark">{{ $appointment->patientProfile->age_text }} &bull; {{ $appointment->patientProfile->gender_name }}</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Số điện thoại:</span>
                        <span class="font-monospace fw-semibold">{{ $appointment->patientProfile->phone }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Mã lịch hẹn:</span>
                        <span class="font-monospace fw-bold text-primary">{{ $appointment->appointment_code }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Số thứ tự khám:</span>
                        <span class="badge bg-primary text-white font-monospace fs-6">#{{ str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT) }}</span>
                    </li>
                </ul>

                <!-- Initial Vitals Intake from Reception Desk -->
                <div class="pt-3">
                    <h6 class="fw-bold small text-info-emphasis text-uppercase mb-2">
                        <i class="bi bi-heart-pulse-fill text-danger me-1"></i> Sinh hiệu đo tại quầy đón tiếp:
                    </h6>
                    @if(str_contains($appointment->symptom_description ?? '', '[Sinh hiệu:'))
                        @php
                            preg_match('/\[Sinh hiệu: (.*?)\]/', $appointment->symptom_description, $vMatch);
                            $vitalsStr = $vMatch[1] ?? '';
                            $cleanDesc = trim(preg_replace('/\[Sinh hiệu: .*?\]/', '', preg_replace('/\[ƯU TIÊN\]/', '', $appointment->symptom_description)));
                        @endphp
                        <div class="p-2 bg-info bg-opacity-10 rounded border border-info small mb-2 text-dark">
                            <strong>{{ $vitalsStr }}</strong>
                        </div>
                        <div class="small text-muted">
                            Lý do bệnh nhân đến khám: <br>
                            <span class="text-dark fw-medium">{{ $cleanDesc ?: 'Khám chuyên khoa' }}</span>
                        </div>
                    @else
                        <div class="p-2 bg-light rounded border small text-muted">
                            {{ $appointment->symptom_description }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Past Medical History Accordion -->
        @if(isset($pastRecords) && $pastRecords->isNotEmpty())
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 small text-uppercase">
                        <i class="bi bi-clock-history me-1"></i> Tiền Sử Khám Cũ ({{ $pastRecords->count() }} lần)
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="accordion accordion-flush" id="pastRecordsAccordion">
                        @foreach($pastRecords as $idx => $past)
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="heading-{{ $past->id }}">
                                    <button class="accordion-button collapsed py-2 px-2 small" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $past->id }}">
                                        <div>
                                            <strong>{{ $past->created_at->format('d/m/Y') }}</strong>:
                                            <span class="text-primary">{{ $past->diagnosis }}</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $past->id }}" class="accordion-collapse collapse" data-bs-parent="#pastRecordsAccordion">
                                    <div class="accordion-body p-2 small bg-light">
                                        <div class="mb-1"><strong>Bác sĩ:</strong> {{ $past->doctor ? $past->doctor->full_name_with_title : '--' }}</div>
                                        <div class="mb-1"><strong>Triệu chứng:</strong> {{ $past->symptoms }}</div>
                                        @if($past->treatment_plan)
                                            <div class="mb-1"><strong>Điều trị:</strong> {{ $past->treatment_plan }}</div>
                                        @endif
                                        @if($past->prescriptions->count() > 0)
                                            <div class="mt-2 pt-1 border-top">
                                                <strong>Thuốc đã kê:</strong>
                                                <ul class="mb-0 ps-3">
                                                    @foreach($past->prescriptions as $p)
                                                        <li>{{ $p->medication_name }} ({{ $p->dosage }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Medical Record Examination Form & Prescriptions -->
    <div class="col-lg-8">
        <form method="POST" action="{{ route('doctor.examine.store', $appointment) }}" id="examinationForm">
            @csrf

            <!-- 1. Clinical Diagnosis Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="bi bi-clipboard2-pulse-fill me-1"></i> 1. Chẩn Đoán & Khám Lâm Sàng
                    </h6>
                    <span class="badge bg-light text-dark border small">Bác sĩ: {{ Auth::user()->name }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Symptoms -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Triệu chứng lâm sàng ghi nhận: <span class="text-danger">*</span></label>
                            @php
                                $defaultSymptoms = preg_replace('/\[Sinh hiệu: .*?\]/', '', preg_replace('/\[ƯU TIÊN\]/', '', $appointment->symptom_description));
                            @endphp
                            <textarea name="symptoms" class="form-control @error('symptoms') is-invalid @enderror" rows="2" placeholder="Ghi nhận diễn tiến bệnh, thời gian khởi phát..." required>{{ old('symptoms', trim($defaultSymptoms)) }}</textarea>
                            @error('symptoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Diagnosis -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Chẩn đoán kết luận của bác sĩ: <span class="text-danger">*</span></label>
                            <input type="text" name="diagnosis" value="{{ old('diagnosis', $appointment->medicalRecord?->diagnosis) }}" class="form-control fw-bold text-primary @error('diagnosis') is-invalid @enderror" placeholder="VD: Viêm họng cấp / Tăng huyết áp độ 1 / Viêm dạ dày tá tràng..." required>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clinical Notes & Treatment Plan -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Ghi chú lâm sàng / Cận lâm sàng:</label>
                            <textarea name="clinical_notes" class="form-control" rows="2" placeholder="Kết quả tim phổi, xét nghiệm máu, X-Quang, siêu âm...">{{ old('clinical_notes', $appointment->medicalRecord?->clinical_notes) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phác đồ / Hướng điều trị:</label>
                            <textarea name="treatment_plan" class="form-control" rows="2" placeholder="Nghỉ ngơi, chế độ ăn giảm muối, uống nhiều nước...">{{ old('treatment_plan', $appointment->medicalRecord?->treatment_plan) }}</textarea>
                        </div>

                        <!-- Follow up date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Hẹn ngày tái khám (nếu có):</label>
                            <input type="date" name="follow_up_date" min="{{ date('Y-m-d') }}" value="{{ old('follow_up_date', $appointment->medicalRecord?->follow_up_date?->format('Y-m-d')) }}" class="form-control form-control-sm">
                            <div class="form-text small">Hệ thống sẽ đồng bộ lịch tái khám vào cổng hồ sơ của bệnh nhân.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Dynamic Prescription Builder -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h6 class="fw-bold text-success mb-0">
                            <i class="bi bi-capsule-pill me-1"></i> 2. Kê Đơn Thuốc Ngoại Trú (Prescription)
                        </h6>
                        <small class="text-muted">Kê đơn thuốc điều trị ngoại trú theo danh mục thuốc chuẩn Bộ Y Tế</small>
                    </div>

                    <!-- Quick medication dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-success btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Chọn thuốc nhanh mẫu
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" style="max-height: 280px; overflow-y: auto;">
                            @foreach($commonMeds as $m)
                                <li>
                                    <a class="dropdown-item small" href="javascript:void(0)" onclick="addQuickMedication('{{ addslashes($m['name']) }}', '{{ addslashes($m['dosage']) }}', '{{ addslashes($m['frequency']) }}', '{{ $m['duration'] }}', '{{ addslashes($m['instructions']) }}')">
                                        <strong>{{ $m['name'] }}</strong>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $m['dosage'] }} &bull; {{ $m['frequency'] }}</div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div id="prescriptionItemsContainer">
                        <!-- Prescription Rows inserted here by JS -->
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm" onclick="addPrescriptionRow()">
                            <i class="bi bi-plus-circle-fill"></i> Thêm thuốc khác
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Action Card -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('doctor.queue') }}" class="btn btn-light px-4">Hủy / Quay lại</a>
                <button type="submit" class="btn btn-success px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle fs-5"></i> Lưu Bệnh Án & Hoàn Tất Ca Khám
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let prescriptionIndex = 0;

    function addPrescriptionRow(medName = '', dosage = '', frequency = '', duration = 5, instructions = '') {
        const container = document.getElementById('prescriptionItemsContainer');
        const idx = prescriptionIndex++;

        const rowHtml = `
            <div class="card border rounded p-3 mb-2 bg-light bg-opacity-50 prescription-row" id="pres-row-${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="badge bg-secondary font-monospace small">Thuốc #${idx + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removePrescriptionRow(${idx})" title="Xóa dòng thuốc">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">Tên thuốc & Hàm lượng: <span class="text-danger">*</span></label>
                        <input type="text" name="prescriptions[${idx}][medication_name]" value="${medName}" class="form-control form-control-sm fw-semibold" placeholder="VD: Augmentin 1g" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Liều lượng:</label>
                        <input type="text" name="prescriptions[${idx}][dosage]" value="${dosage}" class="form-control form-control-sm" placeholder="VD: 1 viên/lần" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold mb-1">Tần suất uống:</label>
                        <input type="text" name="prescriptions[${idx}][frequency]" value="${frequency}" class="form-control form-control-sm" placeholder="VD: Ngày 2 lần (sáng, tối)" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Số ngày dùng:</label>
                        <input type="number" name="prescriptions[${idx}][duration_days]" value="${duration}" min="1" max="90" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label small fw-semibold mb-1">Lời dặn / Hướng dẫn uống:</label>
                        <input type="text" name="prescriptions[${idx}][instructions]" value="${instructions}" class="form-control form-control-sm" placeholder="VD: Uống sau bữa ăn no 15 phút...">
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', rowHtml);
    }

    function removePrescriptionRow(idx) {
        const row = document.getElementById(`pres-row-${idx}`);
        if (row) row.remove();
    }

    function addQuickMedication(name, dosage, frequency, duration, instructions) {
        addPrescriptionRow(name, dosage, frequency, duration, instructions);
    }

    // Populate initial existing prescriptions or 1 empty default row
    document.addEventListener('DOMContentLoaded', function () {
        @if(isset($appointment->medicalRecord) && $appointment->medicalRecord->prescriptions->count() > 0)
            @foreach($appointment->medicalRecord->prescriptions as $p)
                addPrescriptionRow(
                    "{{ addslashes($p->medication_name) }}",
                    "{{ addslashes($p->dosage) }}",
                    "{{ addslashes($p->frequency) }}",
                    {{ $p->duration_days }},
                    "{{ addslashes($p->instructions) }}"
                );
            @endforeach
        @else
            // Default 1 blank row for convenience
            addPrescriptionRow('Paracetamol 500mg', '1 viên/lần', 'Ngày 2 lần khi sốt > 38.5°C', 5, 'Uống sau ăn no');
        @endif
    });
</script>
@endpush
@endsection
