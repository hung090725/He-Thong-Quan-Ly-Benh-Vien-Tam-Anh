@extends('layouts.admin')

@section('title', 'Chi Tiết Bệnh Án - ' . $record->record_code . ' - BV Tâm Anh')

@section('content')
<x-page-header title="Hồ Sơ Bệnh Án Ngoại Trú" subtitle="Mã bệnh án: {{ $record->record_code }} &bull; Đã hoàn tất khám và lưu trữ an toàn">
    <x-slot:actions>
        <a href="{{ route('doctor.records.print', $record) }}" target="_blank" class="btn btn-outline-dark d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-printer-fill"></i> In đơn thuốc (A4/A5)
        </a>
        <a href="{{ route('doctor.examine', $record->appointment) }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-1">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa bệnh án
        </a>
        <a href="{{ route('doctor.queue') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về danh sách chờ
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row g-4 mb-5">
    <!-- Left Column: Patient Summary -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-person-bounding-box me-1"></i> Thông Tin Bệnh Nhân
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center pb-3 border-bottom mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fs-2 mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark text-uppercase mb-0">{{ $record->patientProfile->full_name }}</h5>
                    <div class="text-muted small">
                        Mã BN: <strong class="font-monospace text-primary">{{ $record->patientProfile->patient_code }}</strong>
                    </div>
                </div>

                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Tuổi / Giới tính:</span>
                        <strong class="text-dark">{{ $record->patientProfile->age_text }} &bull; {{ $record->patientProfile->gender_name }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Số điện thoại:</span>
                        <span class="font-monospace">{{ $record->patientProfile->phone }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Mã lịch hẹn:</span>
                        <span class="font-monospace fw-bold text-primary">{{ $record->appointment->appointment_code }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Bác sĩ khám:</span>
                        <strong class="text-dark">{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Thời gian khám:</span>
                        <span>{{ $record->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        @if($record->follow_up_date)
            <div class="alert alert-warning border shadow-sm p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-calendar-event fs-5 text-warning-emphasis"></i>
                    <strong class="text-warning-emphasis">Hẹn ngày tái khám:</strong>
                </div>
                <div class="fs-5 fw-bold text-dark">{{ $record->follow_up_date->format('d/m/Y') }}</div>
                <div class="small text-muted">Bệnh nhân đã được nhắc lịch trên cổng tra cứu y tế.</div>
            </div>
        @endif
    </div>

    <!-- Right Column: Medical Record Details & Prescriptions -->
    <div class="col-lg-8">
        <!-- Diagnosis Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-clipboard2-pulse me-1"></i> Kết Luận Lâm Sàng & Phác Đồ Điều Trị
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                    {{ $record->record_code }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="small text-muted fw-bold text-uppercase mb-1">Chẩn đoán kết luận của Bác sĩ:</div>
                    <div class="fs-5 fw-bold text-primary">{{ $record->diagnosis }}</div>
                </div>

                <div class="row g-3 mb-3 small">
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Triệu chứng lâm sàng ghi nhận:</strong>
                        <p class="text-dark bg-white p-2 rounded border">{{ $record->symptoms }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Ghi chú lâm sàng / Cận lâm sàng:</strong>
                        <p class="text-dark bg-white p-2 rounded border">{{ $record->clinical_notes ?: 'Không có ghi chú thêm.' }}</p>
                    </div>
                    @if($record->treatment_plan)
                        <div class="col-12">
                            <strong class="text-muted d-block mb-1">Kế hoạch điều trị & Dặn dò y khoa:</strong>
                            <p class="text-dark bg-white p-2 rounded border">{{ $record->treatment_plan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prescriptions Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-success mb-0">
                    <i class="bi bi-capsule text-success me-1"></i> Đơn Thuốc Điều Trị Ngoại Trú ({{ $record->prescriptions->count() }} loại thuốc)
                </h6>
                <a href="{{ route('doctor.records.print', $record) }}" target="_blank" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1">
                    <i class="bi bi-printer"></i> In đơn thuốc
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3" style="width: 45px;">#</th>
                                <th>Tên thuốc</th>
                                <th>Liều lượng</th>
                                <th>Tần suất</th>
                                <th>Số ngày</th>
                                <th>Hướng dẫn / Lời dặn</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($record->prescriptions as $idx => $p)
                                <tr>
                                    <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $p->medication_name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $p->dosage }}</span></td>
                                    <td>{{ $p->frequency }}</td>
                                    <td>{{ $p->duration_days }} ngày</td>
                                    <td class="text-secondary">{{ $p->instructions ?: 'Uống theo chỉ dẫn' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Không kê đơn thuốc ngoại trú cho ca khám này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
