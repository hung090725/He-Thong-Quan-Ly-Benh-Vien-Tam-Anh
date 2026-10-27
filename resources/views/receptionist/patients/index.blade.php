@extends('layouts.admin')

@section('title', 'Quản Lý Hồ Sơ Bệnh Nhân - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Danh Sách Hồ Sơ Bệnh Nhân" subtitle="Quản lý thông tin định danh, tra cứu lịch sử khám và tiếp đón bệnh nhân tại quầy">
    <x-slot:actions>
        <a href="{{ route('reception.appointments.walkin') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-in-down-right"></i> Tiếp nhận tại quầy (Walk-in)
        </a>
        <a href="{{ route('reception.patients.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-person-plus-fill"></i> Tạo hồ sơ mới
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Statistics Row -->
@if(isset($stats))
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-bold text-uppercase">Tổng số hồ sơ</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">Toàn viện</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body p-3">
                <div class="text-info small fw-bold text-uppercase">Bệnh nhân Nam</div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['male']) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $stats['total'] > 0 ? round(($stats['male'] / $stats['total']) * 100) : 0 }}% tổng số</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
            <div class="card-body p-3">
                <div class="text-danger small fw-bold text-uppercase">Bệnh nhân Nữ</div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['female']) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $stats['total'] > 0 ? round(($stats['female'] / $stats['total']) * 100) : 0 }}% tổng số</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body p-3">
                <div class="text-success small fw-bold text-uppercase">Có thẻ BHYT</div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($stats['insured']) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">Khám diện bảo hiểm</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
            <div class="card-body p-3">
                <div class="text-warning small fw-bold text-uppercase">Thêm tháng này</div>
                <div class="fs-4 fw-bold text-dark">+{{ number_format($stats['new_this_month']) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">Tháng {{ date('m/Y') }}</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reception.patients.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <label class="small text-muted fw-bold d-block">Tra cứu hồ sơ:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Mã BN, Họ tên, SĐT, CCCD, BHYT hoặc Địa chỉ...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Giới tính:</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">-- Tất cả giới tính --</option>
                    <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Bảo hiểm y tế:</label>
                <select name="insurance" class="form-select form-select-sm">
                    <option value="">-- Tất cả diện --</option>
                    <option value="yes" {{ ($insurance ?? '') === 'yes' ? 'selected' : '' }}>Có thẻ BHYT</option>
                    <option value="no" {{ ($insurance ?? '') === 'no' ? 'selected' : '' }}>Khám dịch vụ / Không BHYT</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold d-block">Sắp xếp:</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="latest" {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Mới đăng ký nhất</option>
                    <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Tên bệnh nhân (A - Z)</option>
                    <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Tên bệnh nhân (Z - A)</option>
                    <option value="dob_desc" {{ ($sort ?? '') === 'dob_desc' ? 'selected' : '' }}>Tuổi (Trẻ nhất)</option>
                    <option value="dob_asc" {{ ($sort ?? '') === 'dob_asc' ? 'selected' : '' }}>Tuổi (Lớn nhất)</option>
                    <option value="visits_desc" {{ ($sort ?? '') === 'visits_desc' ? 'selected' : '' }}>Lượt khám nhiều nhất</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Áp dụng bộ lọc">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="{{ route('reception.patients.index') }}" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Patients List Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 160px;">Mã bệnh nhân</th>
                        <th>Họ và tên</th>
                        <th>Ngày sinh / Tuổi</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Định danh (CCCD / BHYT)</th>
                        <th class="text-center">Số lượt khám</th>
                        <th class="text-end pe-3" style="width: 220px;">Thao tác Tiếp nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('reception.patients.show', $patient) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                        {{ $patient->patient_code }}
                                    </a>
                                    <button type="button" class="btn btn-link btn-sm text-secondary p-0" onclick="copyToClipboard('{{ $patient->patient_code }}')" title="Sao chép mã">
                                        <i class="bi bi-copy" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('reception.patients.show', $patient) }}" class="text-decoration-none text-dark">
                                        {{ $patient->full_name }}
                                    </a>
                                </div>
                                @if($patient->email)
                                    <small class="text-muted"><i class="bi bi-envelope"></i> {{ $patient->email }}</small>
                                @endif
                                @if($patient->address)
                                    <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $patient->address }}">
                                        <i class="bi bi-geo-alt"></i> {{ $patient->address }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $patient->formatted_dob }}</div>
                                <small class="text-muted">({{ $patient->age_text }})</small>
                            </td>
                            <td>
                                <span class="badge {{ $patient->gender_badge_class }}">
                                    {{ $patient->gender_name }}
                                </span>
                            </td>
                            <td class="font-monospace fw-medium">
                                {{ $patient->phone }}
                            </td>
                            <td>
                                @if($patient->identity_card)
                                    <div><small class="text-muted">CCCD:</small> <code>{{ $patient->identity_card }}</code></div>
                                @endif
                                @if($patient->insurance_code)
                                    <div><small class="text-muted">BHYT:</small> <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">{{ $patient->insurance_code }}</span></div>
                                @endif
                                @if(!$patient->identity_card && !$patient->insurance_code)
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-journal-medical text-primary"></i> {{ $patient->appointments_count }} lượt
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('reception.appointments.walkin', ['patient_id' => $patient->id]) }}" class="btn btn-outline-success" title="Tiếp nhận khám ngay tại quầy">
                                        <i class="bi bi-calendar-plus"></i> Khám
                                    </a>
                                    <a href="{{ route('reception.patients.print-card', $patient) }}" target="_blank" class="btn btn-outline-info" title="In thẻ bệnh nhân">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </a>
                                    <a href="{{ route('reception.patients.show', $patient) }}" class="btn btn-outline-primary" title="Xem chi tiết hồ sơ">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('reception.patients.edit', $patient) }}" class="btn btn-outline-secondary" title="Chỉnh sửa hồ sơ">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Xóa hồ sơ" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $patient->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Modal xác nhận xóa -->
                                <div class="modal fade" id="deleteModal-{{ $patient->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form method="POST" action="{{ route('reception.patients.destroy', $patient) }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Xác nhận xóa hồ sơ bệnh nhân</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa hồ sơ của bệnh nhân:</p>
                                                    <div class="alert alert-light border">
                                                        <div class="fw-bold fs-6 text-dark">{{ $patient->full_name }}</div>
                                                        <div class="small text-muted font-monospace">Mã BN: {{ $patient->patient_code }} | SĐT: {{ $patient->phone }}</div>
                                                    </div>
                                                    @if($patient->appointments_count > 0 || $patient->medical_records_count > 0)
                                                        <div class="alert alert-warning small mb-0">
                                                            <i class="bi bi-shield-lock-fill me-1"></i> Bệnh nhân này đã có <strong>{{ $patient->appointments_count }} lịch khám</strong>. Hệ thống sẽ chặn xóa để bảo đảm toàn vẹn dữ liệu y tế.
                                                        </div>
                                                    @else
                                                        <p class="small text-muted mb-0">Thao tác này sẽ xóa hoàn toàn thông tin định danh của bệnh nhân khỏi hệ thống.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy bỏ</button>
                                                    <button type="submit" class="btn btn-danger btn-sm">Xác nhận xóa</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy hồ sơ bệnh nhân nào phù hợp với yêu cầu tìm kiếm.
                                <div class="mt-2">
                                    <a href="{{ route('reception.patients.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Đăng ký hồ sơ mới
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($patients->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $patients->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Đã sao chép mã bệnh nhân: ' + text);
        }).catch(err => {
            console.error('Không thể sao chép: ', err);
        });
    }
</script>
@endpush
@endsection
