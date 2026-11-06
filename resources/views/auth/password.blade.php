@extends('layouts.admin')

@section('title', 'Đổi Mật Khẩu - Bệnh Viện Tâm Anh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <x-page-header title="Đổi Mật Khẩu" subtitle="Cập nhật mật khẩu định kỳ để bảo vệ tài khoản cá nhân của bạn" />

        <!-- Quick Security Navigation Tabs -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('two-factor.setup') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-shield-lock me-1"></i> Xác thực 2 bước (2FA)
            </a>
            <a href="{{ route('sessions.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-display me-1"></i> Phiên thiết bị đang hoạt động
            </a>
            <a href="{{ route('profile.password') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-key-fill me-1"></i> Đổi mật khẩu
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-muted"></i></span>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Nhập mật khẩu đang dùng">
                            <button class="btn btn-light border text-muted" type="button" onclick="togglePwdField('current_password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Tối thiểu 6 ký tự" oninput="checkPwdStrength(this.value)">
                            <button class="btn btn-light border text-muted" type="button" onclick="togglePwdField('password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div id="pwdStrengthBar" class="progress-bar bg-secondary" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small id="pwdStrengthText" class="text-muted">Độ mạnh mật khẩu: Chưa nhập</small>
                            <small class="text-muted">Gợi ý: Kết hợp chữ hoa, chữ thường, số & ký tự</small>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-check2-circle text-muted"></i></span>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới">
                            <button class="btn btn-light border text-muted" type="button" onclick="togglePwdField('password_confirmation', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Lưu mật khẩu mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePwdField(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input) return;
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        icon.className = isPwd ? 'bi bi-eye-slash text-primary' : 'bi bi-eye';
    }

    function checkPwdStrength(val) {
        const bar = document.getElementById('pwdStrengthBar');
        const txt = document.getElementById('pwdStrengthText');
        if (!bar || !txt) return;
        let score = 0;
        if (val.length >= 6) score += 25;
        if (val.length >= 10) score += 25;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 25;
        if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 25;

        bar.style.width = score + '%';
        if (score === 0) {
            bar.className = 'progress-bar bg-secondary';
            txt.textContent = 'Độ mạnh mật khẩu: Quá ngắn (< 6 ký tự)';
        } else if (score <= 25) {
            bar.className = 'progress-bar bg-danger';
            txt.textContent = 'Độ mạnh mật khẩu: Yếu';
        } else if (score <= 50) {
            bar.className = 'progress-bar bg-warning';
            txt.textContent = 'Độ mạnh mật khẩu: Trung bình';
        } else if (score <= 75) {
            bar.className = 'progress-bar bg-info';
            txt.textContent = 'Độ mạnh mật khẩu: Khá tốt';
        } else {
            bar.className = 'progress-bar bg-success';
            txt.textContent = 'Độ mạnh mật khẩu: Rất mạnh (Chuẩn an toàn)';
        }
    }
</script>
@endsection
