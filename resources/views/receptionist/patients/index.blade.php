@extends('layouts.admin')

@section('title', 'Quản Lý Hồ Sơ Bệnh Nhân - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Danh Sách Hồ Sơ Bệnh Nhân" subtitle="Quản lý thông tin định danh, tra cứu lịch sử khám và tiếp đón bệnh nhân tại quầy">
    <x-slot:actions>
        <a href="{{ route('reception.appointments.walkin') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-in-down-right"></i> Tiếp nhận tại quầy (Walk-in)
        </a>
        <a href="{{ route('reception.patients.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-plus-fill"></i> Tạo hồ sơ mới
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reception.patients.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <label class="small text-muted fw-bold d-block">Tìm kiếm thông tin bệnh nhân:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Nhập Mã BN, Họ tên, Số điện thoại, CCCD hoặc Thẻ BHYT...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="small text-muted fw-bold d-block">Giới tính:</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">-- Tất cả giới tính --</option>
                    <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter"></i> Tìm kiếm
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
                        <th class="ps-3" style="width: 140px;">Mã bệnh nhân</th>
                        <th>Họ và tên</th>
                        <th>Ngày sinh / Tuổi</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Định danh (CCCD / BHYT)</th>
                        <th class="text-center">Số lượt khám</th>
                        <th class="text-end pe-3" style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-primary">
                                <a href="{{ route('reception.patients.show', $patient) }}" class="text-decoration-none text-primary">
                                    {{ $patient->patient_code }}
                                </a>
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
                            </td>
                            <td>
                                <div>{{ $patient->dob ? $patient->dob->format('d/m/Y') : 'Chưa cập nhật' }}</div>
                                @if($patient->dob)
                                    <small class="text-muted">({{ $patient->dob->age }} tuổi)</small>
                                @endif
                            </td>
                            <td>
                                @if($patient->gender === 'male')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Nam</span>
                                @elseif($patient->gender === 'female')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nữ</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Khác</span>
                                @endif
                            </td>
                            <td class="font-monospace fw-medium">
                                {{ $patient->phone }}
                            </td>
                            <td>
                                @if($patient->identity_card)
                                    <div><small class="text-muted">CCCD:</small> <code>{{ $patient->identity_card }}</code></div>
                                @endif
                                @if($patient->insurance_code)
                                    <div><small class="text-muted">BHYT:</small> <span class="badge bg-light text-dark border">{{ $patient->insurance_code }}</span></div>
                                @endif
                                @if(!$patient->identity_card && !$patient->insurance_code)
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-journal-medical text-primary"></i> {{ $patient->appointments_count }} ca
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('reception.appointments.walkin', ['patient_id' => $patient->id]) }}" class="btn btn-outline-success" title="Tiếp nhận khám ngay tại quầy">
                                        <i class="bi bi-calendar-plus"></i> Khám
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
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($patients->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $patients->links() }}
        </div>
    @endif
</div>
@endsection
