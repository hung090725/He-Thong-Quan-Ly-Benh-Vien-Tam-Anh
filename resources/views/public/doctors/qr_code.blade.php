@extends('layouts.master')

@section('title', 'Mã QR Tiếp Đón Thông Minh - ' . $doctor->full_name_with_title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-4 p-md-5 bg-white">
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-qr-code-scan me-1"></i> Tiếp Đón Kiosk & Buồng Khám Thông Minh
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-1">{{ $doctor->full_name_with_title }}</h3>
                <p class="text-primary fw-semibold mb-3">{{ $doctor->specialty?->name }}</p>

                <!-- Dynamic QR Code Card -->
                <div class="p-3 bg-light rounded-4 d-inline-block mx-auto mb-4 border shadow-sm">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code Bác sĩ {{ $doctor->full_name_with_title }}" class="img-fluid" style="width: 240px; height: 240px;">
                    <div class="small text-muted mt-2">
                        <i class="bi bi-camera me-1"></i> Quét camera điện thoại để mở hồ sơ
                    </div>
                </div>

                <!-- Clinic Location Quick Info -->
                <div class="row g-2 mb-4 text-start">
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <span class="text-muted small d-block">Phòng khám</span>
                            <strong class="text-dark"><i class="bi bi-door-open text-primary me-1"></i>{{ $doctor->room_number }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <span class="text-muted small d-block">Vị trí</span>
                            <strong class="text-dark"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $doctor->building }} - {{ $doctor->floor }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="{{ route('public.doctors.wayfinding', $doctor) }}" class="btn btn-outline-primary btn-lg rounded-pill fw-semibold">
                        <i class="bi bi-signpost-split me-1"></i> Xem sơ đồ chỉ dẫn tới phòng khám
                    </a>
                    <a href="{{ route('public.doctors.show', $doctor) }}" class="btn btn-primary btn-lg rounded-pill fw-bold">
                        <i class="bi bi-person-lines-fill me-1"></i> Xem đầy đủ hồ sơ bác sĩ
                    </a>
                    <a href="{{ route('public.doctors.vcard', $doctor) }}" class="btn btn-outline-secondary btn-sm rounded-pill mt-2">
                        <i class="bi bi-download me-1"></i> Tải danh thiếp vCard vào điện thoại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
