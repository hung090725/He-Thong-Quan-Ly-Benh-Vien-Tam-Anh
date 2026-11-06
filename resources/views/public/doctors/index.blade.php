@extends('layouts.master')

@section('title', 'Đội Ngũ Bác Sĩ Chuyên Khoa - Bệnh Viện Đa Khoa Tâm Anh')

@section('content')
<!-- Header Banner -->
<div class="bg-primary text-white p-4 p-md-5 rounded-4 mb-4 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-2">Đội ngũ Chuyên gia Hàng đầu</span>
            <h1 class="fw-bold mb-2 display-6">Danh Bạ Bác Sĩ Bệnh Viện Tâm Anh</h1>
            <p class="mb-0 text-white-50 lead fs-6">
                Quy tụ các Giáo sư, Tiến sĩ, Bác sĩ Chuyên khoa giàu kinh nghiệm, tận tâm vì sức khỏe người bệnh và ứng dụng các kỹ thuật y khoa hiện đại.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('booking.create') }}" class="btn btn-light btn-lg px-4 rounded-pill fw-bold text-primary shadow-sm">
                <i class="bi bi-calendar-check me-1"></i> Đặt lịch hẹn ngay
            </a>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('public.doctors.index') }}" class="row g-3" id="doctor-filter-form">
            <div class="col-md-7 col-lg-8">
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="doctor-search-input" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Tìm kiếm bác sĩ theo tên, chuyên khoa, phòng khám..." autocomplete="off">
                        <button type="button" id="publicDoctorClearBtn" class="btn btn-outline-light text-muted border-start-0 border {{ request('search') ? '' : 'd-none' }}" title="Xóa tìm kiếm">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <!-- Instant Dropdown Suggestions List -->
                    <div id="publicDoctorSuggestionsBox" class="position-absolute start-0 w-100 bg-white border rounded-3 shadow-lg mt-1 p-2 d-none" style="z-index: 1060; max-height: 320px; overflow-y: auto;">
                        <div class="small fw-semibold text-muted px-2 py-1 border-bottom mb-1 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-check text-primary me-1"></i> Bác sĩ gợi ý nhanh</span>
                            <span class="badge bg-light text-muted" id="publicDoctorSuggestCount">0 bác sĩ</span>
                        </div>
                        <div id="publicDoctorSuggestionsList" class="list-group list-group-flush"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-lg-4 d-flex gap-2">
                <select name="specialty_id" id="doctor-specialty-select" class="form-select">
                    <option value="">-- Tất cả chuyên khoa --</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1 flex-shrink-0">
                    <i class="bi bi-search"></i> Tìm
                </button>
                @if(request('search') || request('specialty_id') || request()->boolean('on_duty_today'))
                    <a href="{{ route('public.doctors.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
            <div class="col-12 mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="on_duty_today" value="1" id="on_duty_today" {{ request()->boolean('on_duty_today') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark small" for="on_duty_today">
                        <i class="bi bi-clock-history text-success me-1"></i> Chỉ hiển thị bác sĩ có lịch trực hôm nay
                    </label>
                </div>
                <span class="text-muted small" id="doctor-count-badge">
                    <i class="bi bi-info-circle me-1"></i> Tìm thấy <strong>{{ $doctors->total() }}</strong> bác sĩ phù hợp
                </span>
            </div>
        </form>
    </div>
</div>

<!-- AJAX Dynamic Doctor Grid Container -->
<div id="doctor-grid-container" class="position-relative">
    @include('public.doctors.partials.doctor_grid')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('doctor-search-input');
    const specialtySelect = document.getElementById('doctor-specialty-select');
    const onDutyCheckbox = document.getElementById('on_duty_today');
    const container = document.getElementById('doctor-grid-container');
    const clearBtn = document.getElementById('publicDoctorClearBtn');
    const suggestionsBox = document.getElementById('publicDoctorSuggestionsBox');
    const suggestionsList = document.getElementById('publicDoctorSuggestionsList');
    const suggestCount = document.getElementById('publicDoctorSuggestCount');

    let debounceTimer = null;

    function fetchSuggestions(query) {
        if (!suggestionsBox || !suggestionsList) return;
        if (!query || query.trim().length === 0) {
            suggestionsBox.classList.add('d-none');
            return;
        }

        const url = `${window.location.pathname}?format=json&search=${encodeURIComponent(query)}`;
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
                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 border-0 rounded-2 mb-1 public-doctor-item" style="cursor: pointer;" data-name="${item.name}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center overflow-hidden" style="width: 36px; height: 36px; border: 1px solid #e2e8f0;">
                                    ${item.avatar ? `<img src="${item.avatar}" style="width:100%;height:100%;object-fit:cover;">` : '<i class="bi bi-person-fill"></i>'}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">${item.name}</div>
                                    <small class="text-muted">${item.specialty} &bull; ${item.room}</small>
                                </div>
                            </div>
                            <span class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                                Xem hồ sơ
                            </span>
                        </div>
                    `).join('');

                    suggestionsList.querySelectorAll('.public-doctor-item').forEach(el => {
                        el.addEventListener('click', function () {
                            searchInput.value = this.dataset.name;
                            suggestionsBox.classList.add('d-none');
                            if (clearBtn) clearBtn.classList.remove('d-none');
                            triggerSearchNow();
                        });
                    });
                }
                suggestionsBox.classList.remove('d-none');
            })
            .catch(() => {
                suggestionsBox.classList.add('d-none');
            });
    }

    function fetchDoctorsAjax(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
        })
        .catch(err => {
            console.error('AJAX Live Search Error:', err);
        });
    }

    function triggerSearchNow() {
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) {
            params.set('search', searchInput.value.trim());
        }
        if (specialtySelect && specialtySelect.value) {
            params.set('specialty_id', specialtySelect.value);
        }
        if (onDutyCheckbox && onDutyCheckbox.checked) {
            params.set('on_duty_today', '1');
        }

        if (clearBtn) {
            clearBtn.classList.toggle('d-none', !searchInput.value);
        }

        const targetUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.replaceState({}, '', targetUrl);
        fetchDoctorsAjax(targetUrl);
    }

    function triggerSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = searchInput ? searchInput.value.trim() : '';
            fetchSuggestions(query);
            triggerSearchNow();
        }, 180);
    }

    if (searchInput) {
        searchInput.addEventListener('input', triggerSearch);
        searchInput.addEventListener('focus', function () {
            if (this.value.trim().length > 0) {
                fetchSuggestions(this.value.trim());
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            triggerSearchNow();
            searchInput.focus();
        });
    }

    if (specialtySelect) {
        specialtySelect.addEventListener('change', function () {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            triggerSearchNow();
        });
    }

    if (onDutyCheckbox) {
        onDutyCheckbox.addEventListener('change', function () {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
            triggerSearchNow();
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#doctor-filter-form')) {
            if (suggestionsBox) suggestionsBox.classList.add('d-none');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && suggestionsBox) {
            suggestionsBox.classList.add('d-none');
        }
    });

    // Intercept pagination clicks for smooth AJAX page switching
    document.addEventListener('click', function (e) {
        const pageLink = e.target.closest('#doctor-grid-container .pagination a');
        if (pageLink) {
            e.preventDefault();
            const pageUrl = pageLink.getAttribute('href');
            if (pageUrl) {
                window.history.replaceState({}, '', pageUrl);
                fetchDoctorsAjax(pageUrl);
                window.scrollTo({
                    top: container.offsetTop - 120,
                    behavior: 'smooth'
                });
            }
        }
    });
});
</script>
@endpush
@endsection
