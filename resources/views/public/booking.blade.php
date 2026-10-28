@extends('layouts.master')

@section('title', 'Đặt Lịch Khám Bệnh Trực Tuyến - Bệnh Viện Tâm Anh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Hero Header -->
        <div class="card bg-primary text-white border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%) !important;">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 mb-2 fw-semibold">CỔNG TIẾP NHẬN NGOẠI TRÚ</span>
                        <h2 class="fw-bold mb-2">ĐẶT LỊCH KHÁM BỆNH TRỰC TUYẾN</h2>
                        <p class="mb-0 text-white-50">
                            Chủ động chọn Bác sĩ, Chuyên khoa và Khung giờ khám tiện lợi. Tiết kiệm tối đa thời gian chờ đợi tại viện.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('booking.lookup') }}" class="btn btn-outline-light rounded-pill px-3">
                            <i class="bi bi-search me-1"></i> Tra cứu mã hẹn
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                    @csrf

                    <!-- BƯỚC 1: CHUYÊN KHOA VÀ BÁC SĨ -->
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">1</div>
                        <h5 class="fw-bold mb-0 text-primary">Thông tin Khám chữa bệnh</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="specialty_id" class="form-label fw-semibold">Chuyên khoa khám <span class="text-danger">*</span></label>
                            <select class="form-select @error('specialty_id') is-invalid @enderror" id="specialty_id" name="specialty_id" required>
                                <option value="">-- Vui lòng chọn chuyên khoa --</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" {{ (old('specialty_id', $selectedSpecialtyId) == $specialty->id) ? 'selected' : '' }}>
                                        {{ $specialty->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="doctor_id" class="form-label fw-semibold">Bác sĩ phụ trách <span class="text-danger">*</span></label>
                            <select class="form-select @error('doctor_id') is-invalid @enderror" id="doctor_id" name="doctor_id" required>
                                <option value="">-- Vui lòng chọn chuyên khoa trước --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ (old('doctor_id', $selectedDoctorId) == $doctor->id) ? 'selected' : '' }}>
                                        {{ $doctor->full_name_with_title }} (Phòng {{ $doctor->room_number }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="doctorHelp" class="form-text text-muted small"></div>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="appointment_date" class="form-label fw-semibold">Ngày khám dự kiến <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="shift" class="form-label fw-semibold">Ca khám <span class="text-danger">*</span></label>
                            <select class="form-select @error('shift') is-invalid @enderror" id="shift" name="shift" required>
                                <option value="">-- Chọn ca khám --</option>
                                <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Ca Sáng (07:30 - 11:30)</option>
                                <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Ca Chiều (13:30 - 17:00)</option>
                            </select>
                            <div id="slotNotice" class="small mt-1"></div>
                            @error('shift')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="appointment_time" class="form-label fw-semibold">Giờ dự kiến (Tùy chọn)</label>
                            <input type="time" class="form-control @error('appointment_time') is-invalid @enderror" id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}">
                            @error('appointment_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- BƯỚC 2: THÔNG TIN BỆNH NHÂN -->
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">2</div>
                        <h5 class="fw-bold mb-0 text-primary">Thông tin Bệnh nhân</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label fw-semibold">Họ và tên bệnh nhân <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $patientProfile->full_name ?? Auth::user()->name ?? '') }}" required placeholder="VÍ DỤ: NGUYỄN VĂN AN">
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $patientProfile->phone ?? Auth::user()->phone ?? '') }}" required placeholder="0987654321">
                            <small class="text-muted">Dùng để nhận tin nhắn xác nhận và đối soát tại quầy Lễ tân.</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="dob" class="form-label fw-semibold">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ old('dob', isset($patientProfile->dob) ? $patientProfile->dob->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="gender" class="form-label fw-semibold">Giới tính <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender', $patientProfile->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $patientProfile->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $patientProfile->gender ?? '') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="identity_card" class="form-label fw-semibold">Số CCCD / Định danh</label>
                            <input type="text" class="form-control @error('identity_card') is-invalid @enderror" id="identity_card" name="identity_card" value="{{ old('identity_card', $patientProfile->identity_card ?? '') }}" placeholder="12 số">
                            @error('identity_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email nhận phiếu hẹn</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $patientProfile->email ?? Auth::user()->email ?? '') }}" placeholder="email@domain.com">
                            <small class="text-muted">Hệ thống sẽ gửi email xác nhận kèm mã tra cứu vào địa chỉ này.</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="address" class="form-label fw-semibold">Địa chỉ nơi cư trú</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $patientProfile->address ?? '') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện...">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- BƯỚC 3: MÔ TẢ TRIỆU CHỨNG -->
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">3</div>
                        <h5 class="fw-bold mb-0 text-primary">Mô tả Triệu chứng & Lý do khám</h5>
                    </div>

                    <div class="mb-4">
                        <label for="symptom_description" class="form-label fw-semibold">Mô tả chi tiết triệu chứng hoặc vấn đề sức khỏe đang gặp phải <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('symptom_description') is-invalid @enderror" id="symptom_description" name="symptom_description" rows="4" required placeholder="Ví dụ: Đau đầu, sốt nhẹ về chiều kéo dài 3 ngày, ho có đờm, đã uống thuốc hạ sốt nhưng chưa đỡ...">{{ old('symptom_description') }}</textarea>
                        <small class="text-muted">Thông tin này giúp Bác sĩ chuyên khoa chuẩn bị tốt nhất trước khi tiếp nhận ca khám.</small>
                        @error('symptom_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cam kết & Nút Submit -->
                    <div class="p-3 bg-light rounded-3 mb-4 small text-muted">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i> Bằng việc bấm <strong>Xác nhận Đặt Lịch Khám</strong>, Quý khách cam kết các thông tin khai báo là chính xác. Bệnh viện Tâm Anh tôn trọng và bảo mật tuyệt đối dữ liệu y tế cá nhân theo quy định pháp luật.
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary px-4 py-2">Quay lại Trang chủ</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold shadow-sm" id="submitBtn">
                            <i class="bi bi-calendar2-check-fill me-1"></i> XÁC NHẬN ĐẶT HẸN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const specialtySelect = document.getElementById('specialty_id');
        const doctorSelect = document.getElementById('doctor_id');
        const appointmentDate = document.getElementById('appointment_date');
        const shiftSelect = document.getElementById('shift');
        const doctorHelp = document.getElementById('doctorHelp');
        const slotNotice = document.getElementById('slotNotice');

        // Dynamic Doctors by Specialty
        specialtySelect.addEventListener('change', function () {
            const specialtyId = this.value;
            doctorSelect.innerHTML = '<option value="">-- Đang tải danh sách bác sĩ... --</option>';
            doctorHelp.innerHTML = '';
            slotNotice.innerHTML = '';

            if (!specialtyId) {
                doctorSelect.innerHTML = '<option value="">-- Vui lòng chọn chuyên khoa trước --</option>';
                return;
            }

            fetch(`{{ url('/booking/doctors-by-specialty') }}/${specialtyId}`)
                .then(res => res.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="">-- Chọn bác sĩ phụ trách --</option>';
                    if (data.length === 0) {
                        doctorSelect.innerHTML = '<option value="">-- Hiện chưa có bác sĩ trực thuộc khoa này --</option>';
                    } else {
                        data.forEach(doc => {
                            const opt = document.createElement('option');
                            opt.value = doc.id;
                            opt.textContent = `${doc.name} (Phòng ${doc.room_number}) - Phí: ${doc.consultation_fee}`;
                            doctorSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    doctorSelect.innerHTML = '<option value="">-- Lỗi tải danh sách bác sĩ --</option>';
                });
        });

        // Check availability slots
        function checkSlots() {
            const doctorId = doctorSelect.value;
            const dateVal = appointmentDate.value;

            if (!doctorId || !dateVal) {
                slotNotice.innerHTML = '';
                return;
            }

            fetch(`{{ url('/booking/doctor-slots') }}?doctor_id=${doctorId}&date=${dateVal}`)
                .then(res => res.json())
                .then(data => {
                    if (data.slots && data.slots.length > 0) {
                        let html = '<div class="alert alert-info py-2 px-3 mt-2 mb-0 small"><i class="bi bi-calendar3 me-1"></i><strong>Tình trạng ca khám ngày ' + data.date + ' (' + data.day_name + '):</strong><ul class="mb-0 ps-3 mt-1">';
                        data.slots.forEach(s => {
                            if (s.available) {
                                html += `<li class="text-success">${s.shift_name}: Còn <strong>${s.remaining}</strong> chỗ trống (Đã nhận ${s.booked_count}/${s.max_patients})</li>`;
                            } else {
                                html += `<li class="text-danger">${s.shift_name}: <strong>ĐÃ ĐẦY (${s.booked_count}/${s.max_patients})</strong></li>`;
                            }
                        });
                        html += '</ul></div>';
                        slotNotice.innerHTML = html;
                    } else {
                        slotNotice.innerHTML = '<div class="alert alert-warning py-2 px-3 mt-2 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i>Bác sĩ không có lịch trực vào ngày này. Vui lòng chọn ngày khác.</div>';
                    }
                })
                .catch(err => console.error(err));
        }

        doctorSelect.addEventListener('change', checkSlots);
        appointmentDate.addEventListener('change', checkSlots);

        // Anti-Double Submit Protection
        const bookingForm = document.querySelector('form[action="{{ route('booking.store') }}"]');
        const submitBtn = document.getElementById('submitBtn');
        if (bookingForm && submitBtn) {
            bookingForm.addEventListener('submit', function (e) {
                // If form is valid according to HTML5 validation, disable button to prevent double clicks
                if (bookingForm.checkValidity()) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ĐANG XỬ LÝ ĐĂNG KÝ...';
                }
            });
        }
    });
</script>
@endpush
