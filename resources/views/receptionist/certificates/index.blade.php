@extends('layouts.admin')

@section('title', 'Sổ Cấp Giấy Nghỉ Việc Hưởng BHXH (C65-HD) - BV Tâm Anh')

@section('content')
<x-page-header title="Sổ Cấp Giấy Nghỉ Việc Hưởng BHXH (Mẫu C65-HD)" subtitle="Quản lý và tra cứu các chứng từ y tế cấp cho người lao động nghỉ việc hưởng chế độ ốm đau BHXH">
    <x-slot:actions>
        <a href="{{ route('reception.appointments.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Về Quầy tiếp nhận
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Filters and Search Toolbar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reception.certificates.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" placeholder="Lọc theo ngày có hiệu lực" onchange="this.form.submit()">
                </div>
            </div>

            <div class="col-md-9">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Tìm theo tên bệnh nhân, mã số BHXH, số CCCD, mã giấy hoặc tên cơ quan...">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                    @if(!empty($search) || !empty($date))
                        <a href="{{ route('reception.certificates.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table of Issued Certificates -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-journal-medical text-primary fs-5"></i> Danh Sách Giấy Chứng Nhận Đã Phát Hành
        </h6>
        <span class="text-muted small">Tổng số: {{ $certificates->total() }} chứng từ</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th>Mã Chứng Nhận</th>
                        <th>Người Lao Động (Bệnh Nhân)</th>
                        <th>Thời Gian Chỉ Định Nghỉ</th>
                        <th>Chẩn Đoán Bệnh</th>
                        <th>Bác Sĩ Chỉ Định</th>
                        <th class="text-end pe-3" style="min-width: 140px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($certificates as $idx => $cert)
                        <tr>
                            <td class="ps-3 text-muted fw-bold">{{ $certificates->firstItem() + $idx }}</td>
                            <td>
                                <strong class="font-monospace text-primary fs-6">{{ $cert->certificate_code }}</strong>
                                <div class="text-muted" style="font-size: 0.72rem;">Cấp: {{ $cert->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <strong class="text-dark d-block text-uppercase">{{ $cert->patientProfile->full_name }}</strong>
                                <div class="text-muted" style="font-size: 0.74rem;">
                                    BHXH: <span class="font-monospace fw-semibold text-primary">{{ $cert->patientProfile->insurance_code ?: '--' }}</span> &bull;
                                    CCCD: {{ $cert->patientProfile->identity_card ?: '--' }}
                                </div>
                                @if($cert->employer_name)
                                    <div class="text-truncate text-secondary" style="font-size: 0.72rem; max-width: 220px;" title="{{ $cert->employer_name }}">
                                        <i class="bi bi-building"></i> {{ $cert->employer_name }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2 py-1">
                                    {{ $cert->leave_days }} ngày
                                </span>
                                <div class="text-dark fw-medium mt-1" style="font-size: 0.76rem;">
                                    {{ $cert->from_date->format('d/m/Y') }} ➔ {{ $cert->to_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $cert->diagnosis }}">
                                    {{ $cert->diagnosis }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $cert->treatment_method }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $cert->doctor ? $cert->doctor->full_name_with_title : '--' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Người lập: {{ $cert->issuedBy?->name ?? 'Lễ tân' }}</div>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('reception.certificates.print', $cert) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" title="In giấy chứng nhận C65-HD">
                                        <i class="bi bi-printer-fill"></i> In bản C65-HD
                                    </a>
                                    <a href="{{ route('reception.certificates.create', $cert->medicalRecord) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('reception.certificates.destroy', $cert) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy giấy chứng nhận này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-medical fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                <h6>Chưa có giấy chứng nhận nghỉ việc hưởng BHXH nào được lập</h6>
                                <p class="small mb-0">Có thể cấp trực tiếp từ chi tiết Hồ sơ Bệnh án của Bác sĩ.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
