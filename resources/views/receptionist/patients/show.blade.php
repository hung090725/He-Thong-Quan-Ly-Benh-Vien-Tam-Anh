@extends('layouts.admin')

@section('title', 'Hồ Sơ Bệnh Nhân: ' . $patient->full_name . ' - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="{{ $patient->full_name }}" subtitle="Mã hồ sơ: {{ $patient->patient_code }} | Quản lý thông tin định danh và lịch sử khám chữa bệnh">
    <x-slot:actions>
        <a href="{{ route('reception.patients.print-card', $patient) }}" target="_blank" class="btn btn-outline-info d-inline-flex align-items-center gap-1">
            <i class="bi bi-credit-card-2-front"></i> In thẻ bệnh nhân
        </a>
        <a href="{{ route('reception.appointments.walkin', ['patient_id' => $patient->id]) }}" class="btn btn-success d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-calendar-plus"></i> Đăng ký tiếp nhận tại quầy
        </a>
        <a href="{{ route('reception.patients.edit', $patient) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i> Chỉnh sửa
        </a>
        <a href="{{ route('reception.patients.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Danh sách
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row g-4">
    <!-- Left Column: Patient Profile Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
            <div class="card-body p-4 text-center border-bottom bg-light bg-opacity-50">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 72px; height: 72px; font-size: 1.8rem; font-weight: 700;">
                    {{ mb_substr($patient->full_name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1 text-dark">{{ $patient->full_name }}</h5>
                <div class="font-monospace text-primary fw-bold mb-2">
                    <code>{{ $patient->patient_code }}</code>
                </div>
                <div>
                    @if($patient->gender === 'male')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Nam</span>
                    @elseif($patient->gender === 'female')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nữ</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Khác</span>
                    @endif
                    <span class="badge bg-light text-dark border px-2 py-1 ms-1">
                        {{ $patient->dob ? $patient->dob->age . ' tuổi' : 'N/A' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <h6 class="text-uppercase text-muted fw-bold small mb-3">Thông Tin Hành Chính</h6>
                
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> Ngày sinh:</span>
                        <span class="fw-semibold text-dark">{{ $patient->dob ? $patient->dob->format('d/m/Y') : '--' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-telephone me-1"></i> Điện thoại:</span>
                        <span class="fw-semibold font-monospace text-dark">{{ $patient->phone }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-envelope me-1"></i> Email:</span>
                        <span class="fw-medium text-dark text-truncate" style="max-width: 170px;">{{ $patient->email ?: '--' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-card-heading me-1"></i> Số CCCD:</span>
                        <span class="font-monospace fw-semibold text-dark">{{ $patient->identity_card ?: '--' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-shield-check me-1"></i> Mã BHYT:</span>
                        <span class="font-monospace fw-semibold text-dark">{{ $patient->insurance_code ?: '--' }}</span>
                    </li>
                    <li class="py-1 border-bottom">
                        <span class="text-muted d-block mb-1"><i class="bi bi-geo-alt me-1"></i> Địa chỉ thường trú:</span>
                        <span class="fw-medium text-dark">{{ $patient->address ?: 'Chưa cập nhật' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted"><i class="bi bi-clock-history me-1"></i> Ngày mở hồ sơ:</span>
                        <span class="text-muted">{{ $patient->created_at ? $patient->created_at->format('d/m/Y H:i') : '--' }}</span>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-white border-top p-3 text-center">
                <a href="{{ route('reception.appointments.walkin', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-box-arrow-in-down-right"></i> Tiếp nhận ca khám mới
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Medical & Appointment History -->
    <div class="col-lg-8">
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-3 gap-2" id="patientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4" id="appointments-tab" data-bs-toggle="pill" data-bs-target="#appointments" type="button" role="tab">
                    <i class="bi bi-calendar-week me-1"></i> Lịch Sử Đặt Hẹn & Tiếp Nhận ({{ $patient->appointments->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4" id="records-tab" data-bs-toggle="pill" data-bs-target="#records" type="button" role="tab">
                    <i class="bi bi-file-earmark-medical me-1"></i> Hồ Sơ Bệnh Án & Đơn Thuốc ({{ $patient->medicalRecords->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content" id="patientTabsContent">
            <!-- TAB 1: Appointments -->
            <div class="tab-pane fade show active" id="appointments" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="card-title fw-bold text-dark mb-0">Danh Sách Ca Khám Của Bệnh Nhân</h6>
                        <span class="small text-muted">Tổng cộng: {{ $patient->appointments->count() }} lượt</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th class="ps-3">Mã lịch hẹn</th>
                                        <th>Ngày khám & Ca</th>
                                        <th>Chuyên khoa</th>
                                        <th>Bác sĩ phụ trách</th>
                                        <th>Triệu chứng ban đầu</th>
                                        <th class="text-end pe-3">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patient->appointments as $app)
                                        <tr>
                                            <td class="ps-3 fw-bold font-monospace text-primary">
                                                {{ $app->appointment_code }}
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $app->appointment_date ? $app->appointment_date->format('d/m/Y') : '--' }}</div>
                                                <small class="text-muted">{{ $app->shift_name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $app->specialty ? $app->specialty->name : '--' }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $app->doctor ? $app->doctor->full_name_with_title : '--' }}</div>
                                                <small class="text-muted">{{ $app->doctor ? $app->doctor->room_number : '' }}</small>
                                            </td>
                                            <td class="small text-muted" style="max-width: 200px;">
                                                <span class="text-truncate d-inline-block" style="max-width: 190px;" title="{{ $app->symptom_description }}">
                                                    {{ $app->symptom_description }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <x-status-badge :status="$app->status" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                                Chưa có lịch hẹn khám nào được ghi nhận cho bệnh nhân này.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Medical Records & Prescriptions -->
            <div class="tab-pane fade" id="records" role="tabpanel">
                @forelse($patient->medicalRecords as $record)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace me-2">{{ $record->record_code }}</span>
                                <strong class="text-dark">Khám ngày: {{ $record->created_at ? $record->created_at->format('d/m/Y H:i') : '--' }}</strong>
                            </div>
                            <div class="small text-muted">
                                Bác sĩ điều trị: <strong>{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</strong>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border">
                                        <div class="small text-muted fw-bold mb-1"><i class="bi bi-activity text-danger me-1"></i> Triệu chứng ghi nhận:</div>
                                        <div class="text-dark">{{ $record->symptoms }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded border">
                                        <div class="small text-muted fw-bold mb-1"><i class="bi bi-clipboard2-pulse text-primary me-1"></i> Chẩn đoán kết luận:</div>
                                        <div class="fw-bold text-primary">{{ $record->diagnosis }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($record->clinical_notes || $record->treatment_plan)
                                <div class="row g-3 mb-3 small">
                                    @if($record->clinical_notes)
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Chỉ số sinh tồn / Ghi chú:</strong>
                                            <p class="mb-0 text-secondary">{{ $record->clinical_notes }}</p>
                                        </div>
                                    @endif
                                    @if($record->treatment_plan)
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Phác đồ điều trị:</strong>
                                            <p class="mb-0 text-secondary">{{ $record->treatment_plan }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($record->follow_up_date)
                                <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>Hẹn tái khám vào ngày: <strong>{{ \Carbon\Carbon::parse($record->follow_up_date)->format('d/m/Y') }}</strong></span>
                                </div>
                            @endif

                            <!-- Prescriptions -->
                            @if($record->prescriptions && $record->prescriptions->count() > 0)
                                <h6 class="fw-bold text-dark small text-uppercase mb-2">
                                    <i class="bi bi-capsule text-success me-1"></i> Đơn thuốc điều trị ({{ $record->prescriptions->count() }} loại thuốc)
                                </h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>#</th>
                                                <th>Tên thuốc</th>
                                                <th>Liều dùng</th>
                                                <th>Tần suất</th>
                                                <th>Số ngày</th>
                                                <th>Hướng dẫn</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            @foreach($record->prescriptions as $idx => $pres)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td class="fw-semibold text-primary">{{ $pres->medication_name }}</td>
                                                    <td>{{ $pres->dosage }}</td>
                                                    <td>{{ $pres->frequency }}</td>
                                                    <td>{{ $pres->duration_days }} ngày</td>
                                                    <td class="text-muted">{{ $pres->instructions ?: '--' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <!-- Attachments -->
                            @if($record->attachments && $record->attachments->count() > 0)
                                <h6 class="fw-bold text-dark small text-uppercase mb-2">
                                    <i class="bi bi-paperclip text-info me-1"></i> Tệp kết quả cận lâm sàng / X-quang
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($record->attachments as $att)
                                        <span class="badge bg-light text-dark border p-2 d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <span>{{ $att->original_name }}</span>
                                            <small class="text-muted">({{ number_format($att->file_size / 1024, 1) }} KB)</small>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5 text-center text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Bệnh nhân chưa có bệnh án điều trị nào hoàn tất tại viện.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
