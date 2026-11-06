@extends('layouts.master')

@section('title', 'Sơ Đồ Chỉ Dẫn Tới Phòng Khám - ' . $doctor->full_name_with_title)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('public.doctors.index') }}" class="text-decoration-none">Đội ngũ Bác sĩ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('public.doctors.show', $doctor) }}" class="text-decoration-none">{{ $doctor->full_name_with_title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉ dẫn đường đi</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 p-md-5 bg-white">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="bi bi-compass-fill me-1"></i> Hệ Thống Điều Hướng Bệnh Viện Thông Minh
                </span>
                <h2 class="fw-bold text-dark mb-2">Chỉ Dẫn Đường Tới Buồng Khám Của {{ $doctor->full_name_with_title }}</h2>
                <p class="text-muted lead fs-6 mb-0">
                    Phòng khám: <strong>{{ $doctor->room_number }}</strong> &bull; Vị trí: <strong>{{ $doctor->building }} - {{ $doctor->floor }}</strong> &bull; Chuyên khoa: <strong>{{ $doctor->specialty?->name }}</strong>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('booking.create') }}?doctor_id={{ $doctor->id }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-calendar-check me-1"></i> Đặt lịch hẹn khám
                </a>
            </div>
        </div>
    </div>

    <!-- Step-by-Step Wayfinding Path -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-primary border-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 46px; height: 46px;">1</span>
                    <h5 class="fw-bold text-dark mb-0">Sảnh Tiếp Đón Trung Tâm</h5>
                </div>
                <p class="text-muted small mb-0">
                    Từ Cổng chính số 108 Hoàng Như Tiếp, bước vào Sảnh chính Tầng 1. Quý khách xuất trình Căn cước công dân hoặc mã QR đặt hẹn tại Quầy Kiosk Tiếp đón số 1-5 để nhận số thứ tự khám bệnh.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-info border-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 46px; height: 46px;">2</span>
                    <h5 class="fw-bold text-dark mb-0">Di Chuyển Thang Máy</h5>
                </div>
                <p class="text-muted small mb-0">
                    Đi thẳng qua sảnh tiếp đón về phía Cụm thang máy <strong>{{ $doctor->building }}</strong>. Bấm thang máy lên <strong>{{ $doctor->floor }}</strong> (Khu khám bệnh chuyên khoa {{ $doctor->specialty?->name }}).
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-success border-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 46px; height: 46px;">3</span>
                    <h5 class="fw-bold text-dark mb-0">Đến Buồng Khám Bác Sĩ</h5>
                </div>
                <p class="text-muted small mb-0">
                    Rời thang máy rẽ phải theo biển chỉ dẫn hành lang đến cửa buồng khám <strong>{{ $doctor->room_number }}</strong>. Màn hình LED trước cửa phòng sẽ hiển thị tên Bác sĩ và số thứ tự của quý khách.
                </p>
            </div>
        </div>
    </div>

    <!-- Special Assistance Notice -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-1 text-primary">
                <i class="bi bi-person-wheelchair"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1">Hỗ trợ xe lăn & Dẫn đường cho người cao tuổi / phụ nữ mang thai</h6>
                <p class="text-muted small mb-0">
                    Nếu quý khách gặp khó khăn khi di chuyển, vui lòng thông báo tại Bàn tiếp đón sảnh Tầng 1 hoặc gọi hotline nội viện <strong>024 3872 3872 (Phím 0)</strong> để điều dưỡng hỗ trợ xe lăn đón tận nơi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
