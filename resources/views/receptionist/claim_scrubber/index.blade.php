@extends('layouts.admin')

@section('title', 'Giám Định BHYT Thời Gian Thực & Xuất XML 4210/BYT - BV Tâm Anh')

@section('content')
<x-page-header title="GIÁM ĐỊNH BHYT THỜI GIAN THỰC & CỔNG XML 4210/QĐ-BYT" subtitle="Bộ lọc kiểm soát lỗi chỉ định thuốc, xét nghiệm, chẩn đoán ICD-10 chống cơ quan BHXH xuất toán tiền tỷ.">
    <x-slot:actions>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-scrub-claim">
            <i class="bi bi-shield-check"></i> Thẩm Định Hồ Sơ Chi Phí BHYT
        </button>
    </x-slot:actions>
</x-page-header>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-bold text-uppercase">Tổng Hồ Sơ Giám Định</div>
                <div class="fs-3 fw-bold text-primary mt-1">{{ $stats['total_claims'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body p-3">
                <div class="text-success small fw-bold text-uppercase">Hồ Sơ Chuẩn (Clean Claim)</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $stats['clean_pass'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
            <div class="card-body p-3">
                <div class="text-danger small fw-bold text-uppercase">Nguy Cơ Bị Từ Chối (Xuất Toán)</div>
                <div class="fs-3 fw-bold text-danger mt-1">{{ $stats['rejected_risk'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body p-3">
                <div class="text-info small fw-bold text-uppercase">Đã Xuất XML Cổng 4210</div>
                <div class="fs-3 fw-bold text-info mt-1">{{ $stats['xml_exported'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Claims Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-check text-primary fs-5"></i> DANH SÁCH HỒ SƠ GIÁM ĐỊNH BHYT ĐIỆN TỬ
        </h6>
        <span class="badge bg-light text-muted border">Tổng số: {{ $claims->total() }} hồ sơ</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3">Bệnh nhân</th>
                        <th>Chẩn đoán ICD-10</th>
                        <th class="text-end">Chi phí đề nghị BHYT</th>
                        <th class="text-center">Kết quả lọc vi phạm</th>
                        <th>Lỗi cảnh báo xuất toán phát hiện</th>
                        <th class="text-center">Cổng XML 4210</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($claims as $c)
                    <tr>
                        <td class="ps-3">
                            <strong class="text-dark d-block">{{ $c->patientProfile->full_name }}</strong>
                            <span class="text-muted small">Mã BN: {{ $c->patientProfile->patient_code }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold">{{ $c->icd10_code }}</span>
                            <div class="text-muted small mt-0.5">{{ $c->icd10_name }}</div>
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark">
                            {{ number_format($c->claim_amount) }} đ
                        </td>
                        <td class="text-center">
                            @if($c->scrubbing_status === 'clean')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>HỢP LỆ (CLEAN)
                                </span>
                            @elseif($c->scrubbing_status === 'warning')
                                <span class="badge bg-warning text-dark px-2 py-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>CẢNH BÁO
                                </span>
                            @else
                                <span class="badge bg-danger text-white px-2 py-1">
                                    <i class="bi bi-x-circle-fill me-1"></i>VI PHẠM QUY TẮC
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(count($c->flagged_errors ?? []) > 0)
                                <div class="d-flex flex-column gap-1">
                                    @foreach($c->flagged_errors as $err)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-start text-wrap">
                                            <i class="bi bi-exclamation-octagon me-1"></i>{{ $err }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-success small fw-semibold">
                                    <i class="bi bi-check-all me-1"></i>Không có lỗi quy tắc thanh toán
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($c->xml_4210_exported)
                                <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace">
                                    XML1,2,3,4,5 READY
                                </span>
                            @else
                                <span class="text-muted small">Bị chặn do lỗi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-slash fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            Chưa có hồ sơ giám định BHYT nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($claims->hasPages())
        <div class="p-3 border-top">
            {{ $claims->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Giám Định BHYT (Bootstrap 5 Standard) -->
<div class="modal fade" id="modal-scrub-claim" tabindex="-1" aria-labelledby="modalScrubClaimLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalScrubClaimLabel">
                    <i class="bi bi-shield-check me-2"></i>Giám Định Thời Gian Thực Hồ Sơ BHYT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reception.claim-scrubber.scrub') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Chọn Bệnh Nhân BHYT <span class="text-danger">*</span></label>
                        <select name="patient_profile_id" required class="form-select">
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã Bệnh ICD-10 <span class="text-danger">*</span></label>
                            <input type="text" name="icd10_code" value="I10" required class="form-control font-monospace fw-bold">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tổng Tiền Đề Nghị BHYT (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="claim_amount" value="1850000" required class="form-control font-monospace fw-bold">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tên Bệnh Theo Mã ICD-10 <span class="text-danger">*</span></label>
                        <input type="text" name="icd10_name" value="Bệnh tăng huyết áp vô căn (nguyên phát)" required class="form-control">
                    </div>
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <span class="fw-semibold small d-block mb-2 text-dark">Chọn Thuốc & Kỹ Thuật Chỉ Định Để Quét Lỗi BHYT:</span>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="designated_tests[]" value="mri_brain" id="test1">
                            <label class="form-check-label text-danger small fw-semibold" for="test1">
                                Chỉ định Chụp MRI Sọ Não (Thử nghiệm quét lỗi nếu ICD-10 không phải bệnh lý thần kinh)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="prescribed_drugs[]" value="meropenem" id="drug1">
                            <label class="form-check-label text-warning-emphasis small fw-semibold" for="drug1">
                                Kháng sinh Meropenem 1g (Thử nghiệm cảnh báo thiếu biên bản hội chẩn thuốc hạn chế)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-search"></i> Quét & Kiểm Tra BHYT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
