@extends('layouts.admin')

@section('title', 'Chi tiết Lịch Khám: ' . $appointment->appointment_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <x-page-header title="Chi tiết Lịch Hẹn Khám" subtitle="Thông tin đầy đủ về ca khám, kết quả chẩn đoán và đơn thuốc của bạn">
            <x-slot:actions>
                <a href="{{ route('patient.history') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="window.print();">
                    <i class="bi bi-printer"></i> In phiếu
                </button>
            </x-slot:actions>
        </x-page-header>

        <!-- General Info Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Mã lịch hẹn:</span>
                    <strong class="fs-5 text-primary ms-1 font-monospace">{{ $appointment->appointment_code }}</strong>
                </div>
                <x-status-badge :status="$appointment->status" />
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Bệnh nhân</label>
                        <strong class="fs-6 text-dark">{{ $appointment->patientProfile->full_name }}</strong>
                        <div class="small text-muted">Mã BN: <code>{{ $appointment->patientProfile->patient_code }}</code> &bull; SĐT: {{ $appointment->patientProfile->phone }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Bác sĩ phụ trách</label>
                        <strong class="fs-6 text-primary">{{ $appointment->doctor->full_name_with_title }}</strong>
                        <div class="small text-muted">{{ $appointment->specialty->name }} &bull; Phòng {{ $appointment->doctor->room_number }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Thời gian khám</label>
                        <span class="fw-semibold">{{ $appointment->appointment_date->format('d/m/Y') }}</span>
                        <div class="small text-muted">{{ $appointment->shift_name }} {{ $appointment->appointment_time ? '(' . substr($appointment->appointment_time, 0, 5) . ')' : '' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Lý do / Triệu chứng ban đầu</label>
                        <span>{{ $appointment->symptom_description }}</span>
                    </div>

                    @if($appointment->cancellation_reason)
                        <div class="col-12">
                            <div class="alert alert-danger py-2 small mb-0">
                                <strong>Lý do hủy hẹn:</strong> {{ $appointment->cancellation_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Medical Record & Prescription Section -->
        @if($appointment->medicalRecord)
            <div class="card shadow-sm border-0 mb-4 border-top border-3 border-success">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-medical-fill text-success fs-5"></i>
                        <h5 class="fw-bold mb-0 text-success">Kết quả Chẩn đoán Y khoa</h5>
                        <span class="badge bg-light text-muted border">Mã BA: {{ $appointment->medicalRecord->record_code }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($appointment->medicalRecord->sickLeaveCertificate)
                            <a href="{{ route('patient.certificates.print', $appointment->medicalRecord->sickLeaveCertificate) }}" target="_blank" class="btn btn-outline-info btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                                <i class="bi bi-file-earmark-medical-fill"></i> In Giấy nghỉ việc BHXH (C65-HD)
                            </a>
                        @endif
                        <a href="{{ route('patient.appointments.prescription.print', $appointment) }}" target="_blank" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-printer-fill"></i> In đơn thuốc ngoại trú (A4/A5)
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="text-muted small d-block">Triệu chứng lâm sàng ghi nhận</label>
                            <div class="p-2 bg-light rounded">{{ $appointment->medicalRecord->symptoms }}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small d-block">Chẩn đoán kết luận của bác sĩ</label>
                            <div class="p-3 bg-light rounded border-start border-3 border-primary fw-bold text-dark fs-6">
                                {{ $appointment->medicalRecord->diagnosis }}
                            </div>
                        </div>
                        @if($appointment->medicalRecord->clinical_notes)
                            <div class="col-md-12">
                                <label class="text-muted small d-block">Ghi chú chuyên môn & Chỉ số sinh tồn</label>
                                <div class="p-2 bg-light rounded">{{ $appointment->medicalRecord->clinical_notes }}</div>
                            </div>
                        @endif
                        @if($appointment->medicalRecord->treatment_plan)
                            <div class="col-md-12">
                                <label class="text-muted small d-block">Kế hoạch điều trị & Lời dặn dò</label>
                                <div class="p-2 bg-light rounded">{{ $appointment->medicalRecord->treatment_plan }}</div>
                            </div>
                        @endif
                        @if($appointment->medicalRecord->follow_up_date)
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Lịch hẹn tái khám</label>
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="bi bi-calendar-event me-1"></i> {{ $appointment->medicalRecord->follow_up_date->format('d/m/Y') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Prescriptions Table -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-capsule text-danger me-1"></i> Đơn thuốc điều trị
                    </h6>
                    @if($appointment->medicalRecord->prescriptions->isEmpty())
                        <p class="text-muted small">Không có đơn thuốc nào được kê cho ca khám này.</p>
                    @else
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width: 40px;">STT</th>
                                        <th>Tên thuốc</th>
                                        <th>Liều lượng</th>
                                        <th>Tần suất dùng</th>
                                        <th>Số ngày</th>
                                        <th>Hướng dẫn sử dụng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointment->medicalRecord->prescriptions as $idx => $rx)
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td class="fw-semibold text-primary">{{ $rx->medication_name }}</td>
                                            <td>{{ $rx->dosage }}</td>
                                            <td>{{ $rx->frequency }}</td>
                                            <td>{{ $rx->duration_days }} ngày</td>
                                            <td class="small">{{ $rx->instructions ?? 'Theo chỉ định' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Patient Medical Attachments (X-Quang, Xét nghiệm) -->
                    @if($appointment->medicalRecord->attachments->isNotEmpty())
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-images text-info me-1"></i> Kết Quả Cận Lâm Sàng & Phim Chụp ({{ $appointment->medicalRecord->attachments->count() }} tệp)
                        </h6>
                        <div class="row g-3">
                            @foreach($appointment->medicalRecord->attachments as $att)
                                <div class="col-sm-6 col-md-4">
                                    <div class="card h-100 border shadow-none bg-light">
                                        <div class="position-relative text-center bg-white rounded-top p-2" style="height: 140px; overflow: hidden;">
                                            @if($att->is_image)
                                                <img src="{{ $att->url }}" alt="{{ $att->original_name }}" class="w-100 h-100 object-fit-cover rounded" onclick="openPatientLightbox('{{ $att->url }}', '{{ addslashes($att->original_name) }}')" style="cursor: zoom-in;" title="Bấm để xem ảnh lớn">
                                            @else
                                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-danger">
                                                    <i class="bi bi-file-earmark-pdf-fill fs-2"></i>
                                                    <span class="small fw-semibold mt-1">Kết Quả Xét Nghiệm (PDF)</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    @if($att->file_type === 'x_ray')
                                                        <span class="badge bg-primary small">X-Quang / Siêu Âm</span>
                                                    @elseif($att->file_type === 'lab_result')
                                                        <span class="badge bg-success small">Xét Nghiệm</span>
                                                    @else
                                                        <span class="badge bg-secondary small">Tài liệu</span>
                                                    @endif
                                                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $att->formatted_size }}</span>
                                                </div>
                                                <div class="small fw-semibold text-truncate text-dark" title="{{ $att->original_name }}">
                                                    {{ $att->original_name }}
                                                </div>
                                            </div>
                                            <div class="pt-2 mt-2 border-top d-flex justify-content-between align-items-center">
                                                @if($att->is_image)
                                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openPatientLightbox('{{ $att->url }}', '{{ addslashes($att->original_name) }}')">
                                                        <i class="bi bi-zoom-in"></i> Xem
                                                    </button>
                                                @endif
                                                <a href="{{ $att->url }}" target="_blank" download class="btn btn-sm btn-outline-secondary py-0 px-2">
                                                    <i class="bi bi-download"></i> Tải về
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0 text-center py-5 text-muted">
                <i class="bi bi-hourglass-top fs-1 d-block mb-2 text-warning opacity-75"></i>
                <h5 class="fw-bold">Chưa có kết quả khám bệnh</h5>
                <p class="small mb-0">
                    Bác sĩ chưa hoàn thành lượt khám hoặc hồ sơ bệnh án đang trong quá trình lập. Kết quả sẽ được cập nhật tại đây ngay sau khi ca khám hoàn tất.
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Lightbox Preview for Patient -->
<div class="modal fade" id="patientImageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-bottom border-secondary py-2">
                <h6 class="modal-title text-white small text-truncate" id="patientLightboxTitle">Xem Phim Cận Lâm Sàng</h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="patientLightboxDownloadBtn" target="_blank" download class="btn btn-sm btn-outline-light py-1 px-2">
                        <i class="bi bi-download"></i> Tải về
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-2 text-center bg-black">
                <img src="" id="patientLightboxImage" class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;" alt="Xem hình ảnh cận lâm sàng">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openPatientLightbox(url, title) {
        const modalEl = document.getElementById('patientImageLightboxModal');
        const imgEl = document.getElementById('patientLightboxImage');
        const titleEl = document.getElementById('patientLightboxTitle');
        const downloadBtn = document.getElementById('patientLightboxDownloadBtn');

        imgEl.src = url;
        titleEl.textContent = title;
        downloadBtn.href = url;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
@endpush
@endsection
