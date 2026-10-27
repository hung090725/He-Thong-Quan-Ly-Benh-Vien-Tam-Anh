@extends('layouts.admin')

@section('title', 'Tạo Mới Hồ Sơ Bệnh Nhân - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Tiếp Nhận & Tạo Hồ Sơ Bệnh Nhân Mới" subtitle="Đăng ký mã hồ sơ y tế chuẩn hóa và thông tin định danh bệnh nhân tại quầy lễ tân">
    <x-slot:actions>
        <a href="{{ route('reception.patients.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="card-title fw-bold text-primary mb-0">
                    <i class="bi bi-person-vcard-fill me-1"></i> Thông Tin Hành Chính Bệnh Nhân
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#qrCccdModal">
                    <i class="bi bi-qr-code-scan"></i> Quét mã QR CCCD / BHYT
                </button>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('reception.patients.store') }}" novalidate>
                    @csrf

                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>
                            Mã bệnh nhân dạng <strong>BN-YYYYMMDD-XXXX</strong> sẽ được hệ thống sinh tự động sau khi bấm lưu hồ sơ.
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Họ và tên bệnh nhân: <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control @error('full_name') is-invalid @enderror" placeholder="VD: Nguyễn Văn An" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số điện thoại liên hệ: <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="VD: 0987654321 hoặc +84987654321" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Ngày sinh: <span class="text-danger">*</span></label>
                            <input type="date" name="dob" value="{{ old('dob') }}" max="{{ date('Y-m-d') }}" class="form-control @error('dob') is-invalid @enderror" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Giới tính: <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Địa chỉ Email:</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="benhnhan@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Identity Card -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số CCCD / CMND / Hộ chiếu:</label>
                            <input type="text" name="identity_card" value="{{ old('identity_card') }}" class="form-control @error('identity_card') is-invalid @enderror" placeholder="VD: 001095012345">
                            @error('identity_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Insurance Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã số thẻ Bảo hiểm Y tế (BHYT):</label>
                            <input type="text" name="insurance_code" value="{{ old('insurance_code') }}" class="form-control @error('insurance_code') is-invalid @enderror" placeholder="VD: DN4010123456789">
                            @error('insurance_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Địa chỉ thường trú / tạm trú:</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('reception.patients.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check2-circle"></i> Lưu hồ sơ bệnh nhân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Quét / Trích xuất QR CCCD & BHYT -->
<div class="modal fade" id="qrCccdModal" tabindex="-1" aria-labelledby="qrCccdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold fs-6" id="qrCccdModalLabel">
                    <i class="bi bi-qr-code-scan me-1"></i> Bóc Tách Dữ Liệu QR CCCD / BHYT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Hỗ trợ quét trực tiếp bằng máy đọc mã vạch 2D hoặc dán chuỗi QR chuẩn Bộ Công An / Bảo hiểm xã hội Việt Nam.
                </p>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Chuỗi dữ liệu quét từ mã QR:</label>
                    <textarea id="rawQrInput" class="form-control font-monospace small" rows="3" placeholder="Dán hoặc bắn mã QR vào đây..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillSampleCccd()">
                        <i class="bi bi-magic me-1"></i> Dùng thử CCCD mẫu
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="fillSampleBhyt()">
                        <i class="bi bi-shield-check me-1"></i> Dùng thử BHYT mẫu
                    </button>
                </div>

                <div id="qrAlertBox" class="alert alert-danger py-2 px-3 small d-none mt-3"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="parseAndFillQrData()">
                    <i class="bi bi-arrow-down-square-fill me-1"></i> Trích xuất & Điền vào form
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillSampleCccd() {
        document.getElementById('rawQrInput').value = '001092019999||Nguyễn Văn Hưng|15081992|Nam|108 Hoàng Như Tiếp, Bồ Đề, Long Biên, Hà Nội|10102021';
    }

    function fillSampleBhyt() {
        document.getElementById('rawQrInput').value = 'DN4010123456789|Nguyễn Bệnh Nhân BHYT|20/05/1990|1|108 Hoàng Như Tiếp, Long Biên, Hà Nội|01001|01/01/2026-31/12/2026';
    }

    function parseAndFillQrData() {
        const raw = document.getElementById('rawQrInput').value.trim();
        const alertBox = document.getElementById('qrAlertBox');
        alertBox.classList.add('d-none');

        if (!raw) {
            alertBox.textContent = 'Vui lòng nhập hoặc quét chuỗi QR trước khi trích xuất.';
            alertBox.classList.remove('d-none');
            return;
        }

        const parts = raw.split('|');

        // Case 1: Thẻ CCCD gắn chip (7 trường: Số_CCCD | Số_CMND_cũ | Họ_Tên | NgàySinh_ddMMyyyy | GiớiTính | ĐịaChỉ | NgàyCấp)
        if (parts.length >= 6 && /^\d{12}$/.test(parts[0])) {
            const cccd = parts[0];
            const fullName = parts[2];
            const rawDob = parts[3]; // ddmmyyyy
            const genderStr = parts[4]; // Nam / Nữ
            const address = parts[5];

            if (fullName) document.querySelector('input[name="full_name"]').value = fullName;
            if (cccd) document.querySelector('input[name="identity_card"]').value = cccd;
            if (address) document.querySelector('textarea[name="address"]').value = address;

            // Format ddmmyyyy -> yyyy-mm-dd
            if (rawDob && rawDob.length === 8) {
                const day = rawDob.substring(0, 2);
                const month = rawDob.substring(2, 4);
                const year = rawDob.substring(4, 8);
                document.querySelector('input[name="dob"]').value = `${year}-${month}-${day}`;
            }

            // Gender
            const genderSelect = document.querySelector('select[name="gender"]');
            if (genderSelect) {
                if (genderStr.toLowerCase().includes('nam')) genderSelect.value = 'male';
                else if (genderStr.toLowerCase().includes('nữ')) genderSelect.value = 'female';
            }

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('qrCccdModal')).hide();
            alert('Đã trích xuất thành công thông tin công dân từ thẻ CCCD gắn chip!');
            return;
        }

        // Case 2: Thẻ BHYT (Mã_thẻ | Họ_Tên | dd/mm/yyyy | Giới_tính (1=Nam, 2=Nữ) | Địa_chỉ | ...)
        if (parts.length >= 5 && /^[A-Z]{2}\d/.test(parts[0])) {
            const insuranceCode = parts[0];
            const fullName = parts[1];
            const rawDob = parts[2]; // dd/mm/yyyy
            const genderCode = parts[3]; // 1: Nam, 2: Nữ
            const address = parts[4];

            if (insuranceCode) document.querySelector('input[name="insurance_code"]').value = insuranceCode;
            if (fullName) document.querySelector('input[name="full_name"]').value = fullName;
            if (address) document.querySelector('textarea[name="address"]').value = address;

            if (rawDob && rawDob.includes('/')) {
                const [d, m, y] = rawDob.split('/');
                document.querySelector('input[name="dob"]').value = `${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
            }

            const genderSelect = document.querySelector('select[name="gender"]');
            if (genderSelect) {
                genderSelect.value = (genderCode === '1') ? 'male' : 'female';
            }

            bootstrap.Modal.getInstance(document.getElementById('qrCccdModal')).hide();
            alert('Đã trích xuất thành công thông tin thẻ Bảo hiểm Y tế (BHYT)!');
            return;
        }

        alertBox.textContent = 'Định dạng mã QR không hợp lệ hoặc chưa được hỗ trợ. Vui lòng kiểm tra lại dữ liệu.';
        alertBox.classList.remove('d-none');
    }
</script>
@endpush
@endsection
