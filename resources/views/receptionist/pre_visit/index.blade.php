@extends('layouts.admin')

@section('title', 'Cổng Khai Báo Tiền Sử & Triệu Chứng Từ Xa Pre-Visit - BV Tâm Anh')

@section('content')
<x-page-header title="CỔNG TỰ KHAI BÁO TIỀN SỬ & TRIỆU CHỨNG TỪ XA (PRE-VISIT)" subtitle="Bệnh nhân khai báo triệu chứng, thuốc tại nhà & tiền sử dị ứng qua Mobile trước ngày khám để tự động đồng bộ vào màn hình Bác sĩ.">
    <x-slot:actions>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-previsit">
            <i class="bi bi-phone-fill"></i> Mô Phỏng Khai Báo Bệnh Nhân
        </button>
    </x-slot:actions>
</x-page-header>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-bold text-uppercase">Tổng Lượt Khai Báo Trước Khám</div>
                <div class="fs-3 fw-bold text-primary mt-1">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
            <div class="card-body p-3">
                <div class="text-danger small fw-bold text-uppercase">Cờ Đỏ Cấp Cứu Khẩn (Red Flag)</div>
                <div class="fs-3 fw-bold text-danger mt-1">{{ $stats['urgent_red_flags'] }} ca</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body p-3">
                <div class="text-success small fw-bold text-uppercase">Đã Tự Động Đồng Bộ EMR Bác Sĩ</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $stats['synced'] }} ca</div>
            </div>
        </div>
    </div>
</div>

<!-- Screenings Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-card-checklist text-primary fs-5"></i> DANH SÁCH HỒ SƠ TỰ KHAI BÁO BỆNH TRƯỚC LỊCH HẸN
        </h6>
        <span class="badge bg-light text-muted border">Tổng cộng: {{ $screenings->total() }} hồ sơ</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3">Mã lịch hẹn</th>
                        <th>Họ tên & SĐT</th>
                        <th>Triệu chứng tự khai</th>
                        <th>Thuốc đang uống tại nhà</th>
                        <th>Dị ứng ghi nhận</th>
                        <th class="text-center">Phân tầng nguy cơ</th>
                        <th class="text-center">Đồng bộ EMR</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($screenings as $sc)
                    <tr>
                        <td class="ps-3 font-monospace fw-bold text-primary">{{ $sc->appointment_code }}</td>
                        <td>
                            <strong class="text-dark d-block">{{ $sc->patient_name }}</strong>
                            <span class="text-muted small">{{ $sc->patient_phone }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($sc->symptoms_declared as $sym)
                                    @php
                                        $isRedFlag = str_contains($sym, 'cap') || str_contains($sym, 'du_doi');
                                    @endphp
                                    <span class="badge {{ $isRedFlag ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ str_replace('_', ' ', $sym) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-muted">{{ $sc->home_medications ?? 'Không dùng' }}</td>
                        <td>
                            @if($sc->known_allergies)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $sc->known_allergies }}
                                </span>
                            @else
                                <span class="text-muted small">Không dị ứng</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sc->risk_stratification === 'urgent_red_flag')
                                <span class="badge bg-danger text-white px-2 py-1 text-uppercase shadow-sm">
                                    <i class="bi bi-flag-fill me-1"></i>CỜ ĐỎ KHẨN CẤP
                                </span>
                            @elseif($sc->risk_stratification === 'priority')
                                <span class="badge bg-warning text-dark px-2 py-1 text-uppercase">
                                    <i class="bi bi-star-fill me-1"></i>Ưu Tiên
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success px-2 py-1 text-uppercase">
                                    Thường Quy
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-success fw-bold small">
                                <i class="bi bi-check-circle-fill me-1"></i>Đã đồng bộ EMR
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-phone fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Chưa có hồ sơ tự khai báo nào trước khám.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($screenings->hasPages())
        <div class="p-3 border-t">
            {{ $screenings->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Khai Báo Pre-Visit (Bootstrap 5 Standard) -->
<div class="modal fade" id="modal-previsit" tabindex="-1" aria-labelledby="modalPrevisitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalPrevisitLabel">
                    <i class="bi bi-phone-fill me-2"></i>Mô Phỏng Cổng Bệnh Nhân Tự Khai Báo Pre-Visit
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reception.pre-visit.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã Lịch Hẹn Khám <span class="text-danger">*</span></label>
                            <input type="text" name="appointment_code" value="AP-{{ date('Ymd') }}-PRE1" required class="form-control font-monospace">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Họ Tên Bệnh Nhân <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" value="Nguyễn Hoàng Minh" required class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Số Điện Thoại <span class="text-danger">*</span></label>
                        <input type="text" name="patient_phone" value="0918765432" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Các Triệu Chứng Đang Gặp Phải (Tích Chọn) <span class="text-danger">*</span></label>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="symptoms_declared[]" value="sot_nhe" id="sym1">
                                        <label class="form-check-label small" for="sym1">Sốt nhẹ</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="symptoms_declared[]" value="ho_khan" id="sym2">
                                        <label class="form-check-label small" for="sym2">Ho khan</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="symptoms_declared[]" value="dau_dau" id="sym3">
                                        <label class="form-check-label small" for="sym3">Đau đầu</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="symptoms_declared[]" value="dau_nguc_du_doi" id="sym4">
                                        <label class="form-check-label small text-danger fw-bold" for="sym4">Đau ngực dữ dội (CỜ ĐỎ)</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="symptoms_declared[]" value="kho_tho_cap" id="sym5">
                                        <label class="form-check-label small text-danger fw-bold" for="sym5">Khó thở cấp (CỜ ĐỎ)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Thuốc Đang Uống Tại Nhà</label>
                        <input type="text" name="home_medications" value="Amlodipine 5mg (1v/ngày), Glucophage 500mg (2v/ngày)" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tiền Sử Dị Ứng</label>
                        <input type="text" name="known_allergies" value="Dị ứng tôm cua, kháng sinh Ampicillin" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-send-fill"></i> Gửi Hồ Sơ & Đồng Bộ EMR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
