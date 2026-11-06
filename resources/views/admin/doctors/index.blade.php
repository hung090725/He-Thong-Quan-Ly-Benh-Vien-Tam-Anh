@extends('layouts.admin')

@section('title', 'Quản lý Bác sĩ & Lịch trực')

@section('content')
<x-page-header title="Quản lý Bác sĩ" subtitle="Danh sách đội ngũ bác sĩ, chuyên khoa phân công và lịch khám bệnh">
    <x-slot:actions>
        <div class="dropdown d-inline-block">
            <button class="btn btn-outline-primary dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-gear-wide-connected"></i> Quản Trị Nâng Cao
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                <li><a class="dropdown-item py-2" href="{{ route('admin.doctors.kpi') }}"><i class="bi bi-trophy-fill text-warning me-2"></i> Bảng Điểm KPI & Năng Suất</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.doctors.licenses') }}"><i class="bi bi-patch-check-fill text-success me-2"></i> Giấy Phép CCHN & Hạn CME</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.doctors.workload-balancer') }}"><i class="bi bi-cpu-fill text-primary me-2"></i> Bản Đồ Nhiệt Cân Bằng Tải</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.doctors.reviews') }}"><i class="bi bi-star-fill text-warning me-2"></i> Đánh Giá Từ Bệnh Nhân</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.doctors.audit-logs') }}"><i class="bi bi-shield-lock-fill text-secondary me-2"></i> Nhật Ký Kiểm Toán Y Tế</a></li>
            </ul>
        </div>
        <a href="{{ route('admin.doctors.export.csv') }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1" title="Xuất danh sách bác sĩ ra file CSV">
            <i class="bi bi-file-earmark-excel"></i> Xuất CSV
        </a>
        <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#importDoctorCsvModal">
            <i class="bi bi-file-earmark-arrow-up"></i> Nhập CSV
        </button>
        <a href="{{ route('admin.roster') }}" class="btn btn-outline-info d-inline-flex align-items-center gap-1">
            <i class="bi bi-grid-3x3-gap-fill"></i> Ma trận ca trực
        </a>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-plus-fill"></i> Thêm bác sĩ mới
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Quick Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Tổng số Bác sĩ</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Sẵn sàng nhận khám</span>
                    <h4 class="fw-bold text-success mb-0">{{ $stats['available'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-slash-circle-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Tạm dừng nhận khám</span>
                    <h4 class="fw-bold text-warning mb-0">{{ $stats['unavailable'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <span class="text-muted small">Phòng khám vận hành</span>
                    <h4 class="fw-bold text-info mb-0">{{ $stats['rooms_count'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body p-3">
        <form id="doctorFilterForm" method="GET" action="{{ route('admin.doctors.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="doctorSearchInput" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Tìm tên bác sĩ, chuyên khoa, phòng khám..." autocomplete="off">
                        <button type="button" id="doctorClearBtn" class="btn btn-outline-light text-muted border-start-0 border {{ request('search') ? '' : 'd-none' }}" title="Xóa tìm kiếm">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <!-- Instant Dropdown Suggestions List -->
                    <div id="doctorSuggestionsBox" class="position-absolute start-0 w-100 bg-white border rounded-3 shadow-lg mt-1 p-2 d-none" style="z-index: 1060; max-height: 340px; overflow-y: auto;">
                        <div class="small fw-semibold text-muted px-2 py-1 border-bottom mb-1 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-check text-primary me-1"></i> Bác sĩ phù hợp</span>
                            <span class="badge bg-light text-muted" id="doctorSuggestCount">0 bác sĩ</span>
                        </div>
                        <div id="doctorSuggestionsList" class="list-group list-group-flush"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <select id="doctorSpecialtySelect" name="specialty_id" class="form-select">
                    <option value="">-- Tất cả chuyên khoa --</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <select id="doctorAvailabilitySelect" name="is_available" class="form-select">
                    <option value="">-- Trạng thái khám --</option>
                    <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Sẵn sàng nhận khám</option>
                    <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Tạm dừng nhận khám</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                @if(request('search') || request('specialty_id') || request()->filled('is_available'))
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Doctors Table Card -->
<div class="card shadow-sm border-0 rounded-4" id="doctorsTableContainer">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 60px;">ID</th>
                        <th>Bác sĩ</th>
                        <th>Chuyên khoa</th>
                        <th>Phòng khám</th>
                        <th>Giá khám</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end pe-3" style="width: 170px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr>
                            <td class="ps-3 text-muted fw-bold">#{{ $doctor->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold overflow-hidden" style="width: 42px; height: 42px; border: 1px solid #e2e8f0;">
                                        @if($doctor->avatar_url)
                                            <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->full_name_with_title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <i class="bi bi-person-fill fs-5"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $doctor->full_name_with_title }}</div>
                                        <small class="text-muted">{{ $doctor->user ? $doctor->user->email : '—' }}</small>
                                        @if($doctor->user && $doctor->user->phone)
                                            <span class="text-muted small ms-1">({{ $doctor->user->phone }})</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($doctor->specialty)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                        <i class="{{ $doctor->specialty->icon ?: 'bi bi-tag' }} me-1"></i> {{ $doctor->specialty->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Chưa phân khoa</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-door-open me-1"></i> {{ $doctor->room_number }}
                                </span>
                            </td>
                            <td class="fw-semibold text-primary">
                                {{ number_format($doctor->consultation_fee, 0, ',', '.') }} đ
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.doctors.toggle-availability', $doctor) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Bấm để đổi trạng thái nhận khám">
                                        @if($doctor->is_available)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i> Sẵn sàng
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-slash-circle me-1"></i> Tạm dừng
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-outline-secondary" title="Xem hồ sơ">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.doctors.schedule', $doctor) }}" class="btn btn-outline-info" title="Lịch trực tuần">
                                        <i class="bi bi-calendar3"></i>
                                    </a>
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" class="d-inline" data-confirm="Bạn có chắc chắn muốn xóa hồ sơ bác sĩ này? Thao tác không thể hoàn tác!">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa bác sĩ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không tìm thấy bác sĩ nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($doctors->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="importDoctorCsvModal" tabindex="-1" aria-labelledby="importDoctorCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.doctors.import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="importDoctorCsvModalLabel">
                        <i class="bi bi-file-earmark-arrow-up text-primary me-2"></i> Nhập Danh Bạ Bác Sĩ Từ File CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Hệ thống hỗ trợ nhập hàng loạt danh sách bác sĩ và tự động tạo tài khoản truy cập. Tải file mẫu bên dưới để xem định dạng các cột.
                    </p>
                    <div class="mb-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark small d-block">File CSV Mẫu Chuẩn</span>
                            <small class="text-muted">mau_nhap_lieu_bac_si.csv</small>
                        </div>
                        <a href="{{ route('admin.doctors.import.template') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-download me-1"></i> Tải file mẫu
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold small">Chọn file CSV từ máy tính *</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,text/csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-cloud-upload me-1"></i> Bắt đầu Nhập dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('doctorFilterForm');
    const searchInput = document.getElementById('doctorSearchInput');
    const specialtySelect = document.getElementById('doctorSpecialtySelect');
    const availabilitySelect = document.getElementById('doctorAvailabilitySelect');
    const tableContainer = document.getElementById('doctorsTableContainer');
    const clearBtn = document.getElementById('doctorClearBtn');
    const suggestionsBox = document.getElementById('doctorSuggestionsBox');
    const suggestionsList = document.getElementById('doctorSuggestionsList');
    const suggestCount = document.getElementById('doctorSuggestCount');
    let debounceTimer = null;

    function fetchDoctorSuggestions(query) {
        if (!suggestionsBox || !suggestionsList) return;
        if (!query || query.trim().length === 0) {
            suggestionsBox.classList.add('d-none');
            return;
        }

        const url = `${filterForm.action}?format=json&search=${encodeURIComponent(query)}`;
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(res => {
                const items = res.data || [];
                if (suggestCount) suggestCount.textContent = `${items.length} bác sĩ`;

                if (items.length === 0) {
                    suggestionsList.innerHTML = `
                        <div class="p-3 text-center text-muted small">
                            <i class="bi bi-search me-1"></i> Không tìm thấy bác sĩ nào khớp với "<strong>${query}</strong>"
                        </div>`;
                } else {
                    suggestionsList.innerHTML = items.map(item => `
                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 border-0 rounded-2 mb-1 doctor-suggestion-item" style="cursor: pointer;" data-name="${item.name}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center overflow-hidden fw-bold" style="width: 36px; height: 36px; border: 1px solid #e2e8f0;">
                                    ${item.avatar ? `<img src="${item.avatar}" style="width:100%;height:100%;object-fit:cover;">` : '<i class="bi bi-person-fill"></i>'}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">${item.name}</div>
                                    <small class="text-muted">${item.specialty} &bull; ${item.room} &bull; <span class="text-primary">${item.fee}</span></small>
                                </div>
                            </div>
                            <span class="badge ${item.is_available ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'} small">
                                ${item.is_available ? 'Sẵn sàng' : 'Tạm dừng'}
                            </span>
                        </div>
                    `).join('');

                    suggestionsList.querySelectorAll('.doctor-suggestion-item').forEach(el => {
                        el.addEventListener('click', function () {
                            searchInput.value = this.dataset.name;
                            suggestionsBox.classList.add('d-none');
                            if (clearBtn) clearBtn.classList.remove('d-none');
                            executeLiveSearch();
                        });
                    });
                }
                suggestionsBox.classList.remove('d-none');
            })
            .catch(() => {
                suggestionsBox.classList.add('d-none');
            });
    }

    function executeLiveSearch() {
        if (!filterForm || !tableContainer) return;

        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        const targetUrl = filterForm.action + '?' + params.toString();

        if (clearBtn) {
            clearBtn.classList.toggle('d-none', !searchInput.value);
        }

        fetch(targetUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('doctorsTableContainer');
            if (newTable) {
                tableContainer.innerHTML = newTable.innerHTML;
            }
            window.history.replaceState({}, '', targetUrl);
        })
        .catch(() => {
            filterForm.submit();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchDoctorSuggestions(query);
                executeLiveSearch();
            }, 180);
        });

        searchInput.addEventListener('focus', function () {
            if (this.value.trim().length > 0) {
                fetchDoctorSuggestions(this.value.trim());
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            executeLiveSearch();
            searchInput.focus();
        });
    }

    if (specialtySelect) {
        specialtySelect.addEventListener('change', function () {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            executeLiveSearch();
        });
    }

    if (availabilitySelect) {
        availabilitySelect.addEventListener('change', function () {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            executeLiveSearch();
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#doctorFilterForm')) {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && suggestionsBox) {
            suggestionsBox.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection
