@extends('layouts.master')

@section('title', 'Tra Cứu Lịch Hẹn Khám Bệnh - Bệnh Viện Tâm Anh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px;">
                    <i class="bi bi-search fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">TRA CỨU TIẾN TRÌNH LỊCH HẸN KHÁM</h3>
                <p class="text-muted mb-4 small">
                    Nhập <strong>Mã lịch hẹn</strong> (VD: <code>AP-20261017-0001</code>) hoặc <strong>Số điện thoại</strong> đã đăng ký để kiểm tra trạng thái ca khám.
                </p>

                <form method="GET" action="{{ route('booking.lookup') }}" class="row g-2 justify-content-center">
                    <div class="col-md-7">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc-scan text-muted"></i></span>
                            <input type="text" name="keyword" value="{{ $keyword ?? '' }}" class="form-control border-start-0" placeholder="Nhập mã lịch hẹn hoặc số điện thoại..." required autofocus>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                            <i class="bi bi-search me-1"></i> Tra cứu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($appointment))
            <!-- Single exact Appointment result -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom">
                    <div>
                        <span class="text-muted small">Phiếu hẹn:</span>
                        <strong class="fs-5 text-primary ms-1 font-monospace">{{ $appointment->appointment_code }}</strong>
                    </div>
                    <div>
                        <x-status-badge :status="$appointment->status" />
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Họ và tên bệnh nhân:</span>
                            <strong class="fs-6">{{ $appointment->patientProfile->full_name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Số điện thoại liên hệ:</span>
                            <code class="fs-6">{{ \Illuminate\Support\Str::mask($appointment->patientProfile->phone, '*', 3, -3) }}</code>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Bác sĩ khám:</span>
                            <span class="fw-semibold text-primary">{{ $appointment->doctor->full_name_with_title }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Chuyên khoa & Phòng khám:</span>
                            <span>{{ $appointment->specialty->name }} &bull; Phòng {{ $appointment->doctor->room_number }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Ngày & Ca khám:</span>
                            <strong>{{ $appointment->appointment_date->format('d/m/Y') }}</strong> - {{ $appointment->shift_name }}
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Lý do đăng ký khám:</span>
                            <span>{{ $appointment->symptom_description }}</span>
                        </div>
                        @if($appointment->cancellation_reason)
                            <div class="col-12">
                                <div class="alert alert-danger py-2 small mb-0">
                                    <strong>Lý do hủy ca:</strong> {{ $appointment->cancellation_reason }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Medical Privacy Card: Hiding Clinical Data from Public View -->
                    <div class="card border-primary-subtle bg-light-subtle shadow-sm mt-3">
                        <div class="card-body p-4 text-center">
                            <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-2">
                                <i class="bi bi-shield-lock-fill fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">BẢO MẬT HỒ SƠ Y TẾ & KẾT QUẢ KHÁM</h6>
                            <p class="text-muted small mx-auto mb-3" style="max-width: 580px;">
                                Để bảo vệ quyền riêng tư và bí mật bệnh án theo quy chuẩn Bộ Y Tế, thông tin chẩn đoán lâm sàng, kết quả xét nghiệm và đơn thuốc điện tử chỉ được cung cấp trực tiếp qua <strong>Cổng thông tin Bệnh nhân</strong> có xác thực tài khoản.
                            </p>
                            @auth
                                <a href="{{ route('patient.appointments.show', $appointment) }}" class="btn btn-primary btn-sm px-4 fw-semibold">
                                    <i class="bi bi-folder2-open me-1"></i> Xem Kết Quả Khám & Đơn Thuốc Trong Cổng Bệnh Nhân
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4 fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Để Xem Kết Quả Khám & Đơn Thuốc
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        @elseif(isset($appointments) && $appointments->isNotEmpty())
            <!-- List of appointments for phone search -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        Tìm thấy {{ $appointments->count() }} lịch hẹn liên quan đến số điện thoại <code>{{ \Illuminate\Support\Str::mask($keyword, '*', 3, -3) }}</code>:
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mã hẹn</th>
                                    <th>Bệnh nhân</th>
                                    <th>Chuyên khoa / Bác sĩ</th>
                                    <th>Thời gian khám</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-3">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $app)
                                    <tr>
                                        <td class="ps-3 fw-bold font-monospace text-primary">{{ $app->appointment_code }}</td>
                                        <td>{{ $app->patientProfile->full_name }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $app->doctor->full_name_with_title }}</div>
                                            <small class="text-muted">{{ $app->specialty->name }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $app->appointment_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $app->shift_name }}</small>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$app->status" />
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('booking.lookup', ['keyword' => $app->appointment_code]) }}" class="btn btn-sm btn-outline-primary">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($keyword)
            <div class="alert alert-warning text-center p-4 shadow-sm border-0">
                <i class="bi bi-search fs-1 d-block mb-2 text-warning"></i>
                <h5 class="fw-bold">Không tìm thấy thông tin lịch hẹn!</h5>
                <p class="text-muted mb-0 small">
                    Không có lịch hẹn nào trùng khớp với mã hoặc số điện thoại <strong>"{{ $keyword }}"</strong>. Vui lòng kiểm tra lại tính chính xác của dữ liệu.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
