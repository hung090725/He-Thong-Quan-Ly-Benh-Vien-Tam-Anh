@extends('layouts.master')

@section('title', 'Quản Lý Phiên Đăng Nhập & Thiết Bị - Bệnh Viện Tâm Anh')

@section('content')
@php
    $ua = $currentSession['user_agent'] ?? '';
    $osName = 'Hệ điều hành Khác';
    $osIcon = 'bi-pc-display';
    if (stripos($ua, 'Windows') !== false) { $osName = 'Windows PC'; $osIcon = 'bi-windows'; }
    elseif (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS') !== false) { $osName = 'macOS'; $osIcon = 'bi-apple'; }
    elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) { $osName = 'iOS Mobile'; $osIcon = 'bi-phone'; }
    elseif (stripos($ua, 'Android') !== false) { $osName = 'Android'; $osIcon = 'bi-phone'; }
    elseif (stripos($ua, 'Linux') !== false) { $osName = 'Linux'; $osIcon = 'bi-ubuntu'; }

    $browserName = 'Trình duyệt Web';
    if (stripos($ua, 'Edg/') !== false) { $browserName = 'Microsoft Edge'; }
    elseif (stripos($ua, 'Chrome/') !== false) { $browserName = 'Google Chrome'; }
    elseif (stripos($ua, 'Firefox/') !== false) { $browserName = 'Mozilla Firefox'; }
    elseif (stripos($ua, 'Safari/') !== false) { $browserName = 'Apple Safari'; }
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Quick Security Navigation Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('two-factor.setup') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-shield-lock me-1"></i> Xác thực 2 bước (2FA)
                    </a>
                    <a href="{{ route('sessions.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-display me-1"></i> Phiên thiết bị
                    </a>
                    <a href="{{ route('profile.password') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-key me-1"></i> Đổi mật khẩu
                    </a>
                </div>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-speedometer2 me-1"></i> Về Dashboard Admin
                    </a>
                @elseif(Auth::user()->isPatient())
                    <a href="{{ route('patient.history') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-journal-medical me-1"></i> Về Lịch sử khám
                    </a>
                @endif
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-display me-2"></i>Quản Lý Phiên Thiết Bị & Đăng Xuất Từ Xa</h5>
                    <span class="badge bg-primary">Bảo mật tài khoản</span>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i>
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <h6 class="fw-bold mb-3 text-secondary">THIẾT BỊ ĐANG SỬ DỤNG (PHIÊN HIỆN TẠI):</h6>
                    <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success-subtle text-success p-3">
                                <i class="bi bi-laptop fs-3"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark d-flex flex-wrap align-items-center gap-2">
                                    <span>Thiết bị này ({{ $osName }} &bull; {{ $browserName }})</span>
                                    <span class="badge bg-success">Đang hoạt động</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2 my-1">
                                    <span class="badge bg-white text-dark border"><i class="bi {{ $osIcon }} text-primary me-1"></i>{{ $osName }}</span>
                                    <span class="badge bg-white text-dark border"><i class="bi bi-globe2 text-info me-1"></i>{{ $browserName }}</span>
                                    <span class="badge bg-white text-dark border font-monospace"><i class="bi bi-router text-success me-1"></i>IP: {{ $currentSession['ip_address'] }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-clock me-1"></i>Hoạt động gần nhất: {{ $currentSession['last_active'] }}
                                </div>
                                <div class="small text-secondary text-truncate" style="max-width: 450px;" title="{{ $currentSession['user_agent'] }}">
                                    {{ $currentSession['user_agent'] }}
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-shield-check text-success fs-3"></i>
                    </div>

                    <div class="border-top pt-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-box-arrow-right me-1"></i>Đăng xuất khỏi tất cả các thiết bị khác</h6>
                        <p class="small text-muted mb-3">
                            Nếu bạn nghi ngờ tài khoản của mình đã đăng nhập trên máy tính công cộng, thiết bị lạ hoặc ai đó biết mật khẩu, bạn có thể buộc đăng xuất tất cả các phiên làm việc khác từ xa ngay lập tức.
                        </p>

                        <form action="{{ route('sessions.logout-others') }}" method="POST" class="p-3 border border-danger-subtle rounded bg-danger-subtle bg-opacity-10">
                            @csrf
                            <label class="form-label fw-bold small text-dark">Nhập mật khẩu hiện tại của bạn để xác nhận hành động:</label>
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu tài khoản" required>
                                </div>
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-danger w-100 fw-bold" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất khỏi tất cả các thiết bị khác không?');">
                                        <i class="bi bi-shield-x me-1"></i>Đăng xuất thiết bị khác
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
