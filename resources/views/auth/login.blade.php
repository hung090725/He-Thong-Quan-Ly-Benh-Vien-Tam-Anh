@extends('layouts.master')

@section('title', 'Đăng nhập Hệ thống - Bệnh Viện Tâm Anh')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow border-0 overflow-hidden">
            <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 54px; height: 54px;">
                    <i class="bi bi-shield-lock-fill fs-3 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-1">ĐĂNG NHẬP HỆ THỐNG</h4>
                <p class="mb-0 small text-light opacity-90">Cổng thông tin & Điều phối Bệnh viện Tâm Anh</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@tamanh.vn">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Nhập mật khẩu của bạn">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                        <a href="{{ route('booking.lookup') }}" class="small text-decoration-none text-primary">
                            Tra cứu lịch hẹn?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> ĐĂNG NHẬP
                    </button>
                </form>

                <!-- Quick Test Credentials Box (For Project Defense & Review) -->
                <div class="mt-4 pt-3 border-top">
                    <div class="text-center small text-muted fw-semibold mb-2 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        Tài khoản mẫu để demo (Mật khẩu chung: <code>123456</code>)
                    </div>
                    <div class="d-grid gap-2">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="button" class="btn btn-outline-danger btn-quick-login" data-email="admin@tamanh.vn">
                                <i class="bi bi-shield-check"></i> Admin
                            </button>
                            <button type="button" class="btn btn-outline-info text-dark btn-quick-login" data-email="receptionist@tamanh.vn">
                                <i class="bi bi-person-badge"></i> Lễ tân
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-quick-login" data-email="vandat271205@gmail.com">
                                <i class="bi bi-heart-pulse"></i> Bác sĩ
                            </button>
                            <button type="button" class="btn btn-outline-success btn-quick-login" data-email="patient@gmail.com">
                                <i class="bi bi-person"></i> Bệnh nhân
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-light text-center py-3 border-0 small text-muted">
                Khách vãng lai muốn đặt lịch khám? <a href="{{ route('booking.create') }}" class="text-primary fw-semibold text-decoration-none">Đặt lịch trực tuyến tại đây</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-quick-login').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('email').value = this.getAttribute('data-email');
            document.getElementById('password').value = '123456';
        });
    });
</script>
@endpush
