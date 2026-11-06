@extends('layouts.master')

@section('title', $doctor->full_name_with_title . ' - Bệnh Viện Đa Khoa Tâm Anh')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('public.doctors.index') }}" class="text-decoration-none">Danh bạ Bác sĩ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $doctor->full_name_with_title }}</li>
        </ol>
    </nav>
    <!-- Bilingual Switcher -->
    <div class="btn-group btn-group-sm shadow-xs rounded-pill overflow-hidden border">
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'vi']) }}" class="btn {{ ($lang ?? 'vi') === 'vi' ? 'btn-primary' : 'btn-light text-dark' }} px-3 fw-semibold">
            🇻🇳 Tiếng Việt
        </a>
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="btn {{ ($lang ?? 'vi') === 'en' ? 'btn-primary' : 'btn-light text-dark' }} px-3 fw-semibold">
            🇬🇧 English
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Doctor Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center mb-3 fs-1 overflow-hidden" style="width: 100px; height: 100px; border: 3px solid #e2e8f0;">
                    @if($doctor->avatar_url)
                        <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->full_name_with_title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="bi bi-person-badge"></i>
                    @endif
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $doctor->full_name_with_title }}</h3>
                <div class="mb-3 d-flex justify-content-center gap-1 flex-wrap">
                    @if($doctor->specialty)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                            <i class="{{ $doctor->specialty->icon ?: 'bi bi-tag' }} me-1"></i> {{ $doctor->specialty->name }}
                        </span>
                    @endif
                    @if($doctor->current_leave)
                        <span class="badge bg-warning text-dark border border-warning rounded-pill px-3 py-1 fw-bold">
                            <i class="bi bi-calendar2-x-fill me-1"></i> Đang Nghỉ Phép
                        </span>
                    @elseif($doctor->is_available)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i> Sẵn sàng nhận khám
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-slash-circle me-1"></i> Tạm dừng nhận khám
                        </span>
                    @endif
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Phòng khám:</span>
                        <span class="fw-semibold text-dark">{{ $doctor->room_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Giá khám:</span>
                        <span class="fw-bold text-primary">{{ number_format($doctor->consultation_fee, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    @if($doctor->current_leave && $doctor->current_leave->substituteDoctor)
                        <a href="{{ route('booking.create') }}?doctor_id={{ $doctor->current_leave->substitute_doctor_id }}&specialty_id={{ $doctor->specialty_id }}" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-sm text-dark">
                            <i class="bi bi-arrow-repeat me-1"></i> Đặt lịch BS trực thay ({{ $doctor->current_leave->substituteDoctor->clean_name }})
                        </a>
                    @elseif($doctor->current_leave)
                        <button class="btn btn-secondary btn-lg rounded-pill fw-bold shadow-sm" disabled>
                            <i class="bi bi-calendar2-x me-1"></i> Bác sĩ đang tạm vắng mặt
                        </button>
                    @else
                        <a href="{{ route('booking.create') }}?doctor_id={{ $doctor->id }}&specialty_id={{ $doctor->specialty_id }}" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-calendar-check me-1"></i> Đặt lịch khám với Bác sĩ
                        </a>
                    @endif
                </div>

                <!-- Digital Business Card & Smart QR -->
                <div class="border-top pt-3 mt-2 text-center">
                    <div class="d-inline-flex align-items-center gap-1 text-primary small fw-semibold mb-2">
                        <i class="bi bi-qr-code"></i> Danh thiếp số & Mã QR Bác sĩ
                    </div>
                    <div class="p-2 bg-white rounded-3 border d-inline-block shadow-xs mb-2">
                        <img src="{{ $qrCodeUrl }}" alt="QR Bác sĩ {{ $doctor->full_name_with_title }}" class="img-fluid" style="width: 140px; height: 140px;">
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">
                        Quét bằng Camera điện thoại để lưu danh bạ hoặc đặt lịch khám nhanh.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('public.doctors.wayfinding', $doctor) }}" class="btn btn-primary btn-sm rounded-pill fw-semibold shadow-xs">
                            <i class="bi bi-signpost-split me-1"></i> Sơ đồ chỉ dẫn tới phòng khám
                        </a>
                        <a href="{{ route('public.doctors.qr_code', $doctor) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold">
                            <i class="bi bi-qr-code-scan me-1"></i> Mã QR Tiếp đón Kiosk
                        </a>
                        <a href="{{ route('public.doctors.vcard', $doctor) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="bi bi-person-lines-fill me-1"></i> Lưu Danh bạ điện thoại (.vcf)
                        </a>
                        <a href="{{ route('public.doctors.desk_card', $doctor) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="bi bi-printer me-1"></i> In Thẻ để bàn phòng khám
                        </a>
                        <a href="{{ route('public.doctors.print', $doctor) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Xuất Hồ Sơ Y Khoa (CV In ấn)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 fw-bold border-bottom">
                <i class="bi bi-shield-check text-success me-2"></i> Cam kết dịch vụ y tế
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-secondary">
                    <li><i class="bi bi-check2-circle text-primary me-2"></i> Bác sĩ trực tiếp thăm khám và tư vấn phác đồ chuẩn y khoa.</li>
                    <li><i class="bi bi-check2-circle text-primary me-2"></i> Trang thiết bị chẩn đoán hình ảnh và xét nghiệm tân tiến.</li>
                    <li><i class="bi bi-check2-circle text-primary me-2"></i> Tiếp nhận đúng giờ hẹn, không phải chờ đợi lâu.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Doctor Details & Schedules -->
    <div class="col-lg-8">
        @if($doctor->current_leave)
            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-2 text-warning">
                        <i class="bi bi-calendar2-range-fill"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">
                            Thông báo vắng mặt: {{ $doctor->current_leave->reason }}
                        </h6>
                        <p class="mb-2 small text-dark">
                            Bác sĩ có kế hoạch công tác / vắng mặt từ ngày <strong>{{ $doctor->current_leave->start_date->format('d/m/Y') }}</strong> đến ngày <strong>{{ $doctor->current_leave->end_date->format('d/m/Y') }}</strong>.
                        </p>
                        @if($doctor->current_leave->substituteDoctor)
                            <div class="p-2 bg-white rounded-3 border d-inline-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-pill"><i class="bi bi-shield-check"></i> Bác sĩ trực thay</span>
                                <span class="fw-bold text-dark">{{ $doctor->current_leave->substituteDoctor->full_name_with_title }}</span>
                                <a href="{{ route('public.doctors.show', $doctor->current_leave->substituteDoctor) }}" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 ms-2">
                                    Xem hồ sơ BS trực thay
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Bio -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-journal-text text-primary me-2"></i> {{ ($lang ?? 'vi') === 'en' ? 'Professional Background & Biography' : 'Lý lịch chuyên môn & Tiểu sử' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="lh-lg text-secondary">
                    @if(($lang ?? 'vi') === 'en' && $doctor->bio_en)
                        {!! nl2br(e($doctor->bio_en)) !!}
                    @elseif(($lang ?? 'vi') === 'en')
                        Senior medical specialist at Tam Anh General Hospital with extensive clinical experience and dedication to international healthcare standards.
                    @else
                        {!! nl2br(e($doctor->bio ?: 'Bác sĩ chuyên khoa tại Bệnh viện Đa khoa Tâm Anh với nhiều năm kinh nghiệm khám và điều trị chuyên sâu, luôn đặt sự an tâm và sức khỏe của bệnh nhân lên hàng đầu.')) !!}
                    @endif
                </div>
            </div>
        </div>

        <!-- Weekly Schedule -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-calendar-week text-primary me-2"></i> {{ ($lang ?? 'vi') === 'en' ? 'Weekly Consultation Schedule' : 'Lịch trực khám bệnh trong tuần' }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">{{ ($lang ?? 'vi') === 'en' ? 'Day of Week' : 'Thứ trong tuần' }}</th>
                                <th>{{ ($lang ?? 'vi') === 'en' ? 'Shift' : 'Ca trực' }}</th>
                                <th>{{ ($lang ?? 'vi') === 'en' ? 'Working Hours' : 'Khung giờ làm việc' }}</th>
                                <th class="text-end pe-4">{{ ($lang ?? 'vi') === 'en' ? 'Action' : 'Thao tác' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($doctor->schedules->sortBy('id') as $sched)
                                @php
                                    $isToday = (strtolower(now()->format('l')) === $sched->day_of_week);
                                    $isLeaveSlot = $isToday && $doctor->current_leave;
                                @endphp
                                <tr class="{{ $isLeaveSlot ? 'table-warning bg-opacity-50' : ($isToday ? 'table-warning bg-opacity-25' : '') }}">
                                    <td class="ps-4 fw-bold text-dark">
                                        {{ $days[$sched->day_of_week] ?? $sched->day_name }}
                                        @if($isToday)
                                            <span class="badge bg-warning text-dark border border-warning ms-1" style="font-size: 0.7rem;">
                                                <i class="bi bi-star-fill me-1"></i> {{ ($lang ?? 'vi') === 'en' ? 'Today' : 'Hôm nay' }}
                                            </span>
                                        @endif
                                        @if($isLeaveSlot)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: 0.7rem;">
                                                <i class="bi bi-calendar2-x-fill me-1"></i> {{ ($lang ?? 'vi') === 'en' ? 'On Leave' : 'Nghỉ phép' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $sched->shift === 'morning' ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-info-subtle text-info border border-info-subtle' }} rounded-pill px-3 py-1">
                                            @if(($lang ?? 'vi') === 'en')
                                                {{ $sched->shift === 'morning' ? 'Morning Shift' : 'Afternoon Shift' }}
                                            @else
                                                {{ $sched->shift === 'morning' ? 'Ca Sáng' : 'Ca Chiều' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary fw-medium">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($isLeaveSlot && $doctor->current_leave && $doctor->current_leave->substituteDoctor)
                                            <a href="{{ route('booking.create') }}?doctor_id={{ $doctor->current_leave->substitute_doctor_id }}&specialty_id={{ $doctor->specialty_id }}&shift={{ $sched->shift }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill shadow-xs">
                                                <i class="bi bi-arrow-repeat me-1"></i> {{ ($lang ?? 'vi') === 'en' ? 'Substitute: ' . $doctor->current_leave->substituteDoctor->clean_name : 'BS ' . $doctor->current_leave->substituteDoctor->clean_name . ' trực thay' }}
                                            </a>
                                        @elseif($isLeaveSlot)
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">
                                                {{ ($lang ?? 'vi') === 'en' ? 'Slot Paused' : 'Tạm dừng ca này' }}
                                            </span>
                                        @else
                                            <a href="{{ route('booking.create') }}?doctor_id={{ $doctor->id }}&specialty_id={{ $doctor->specialty_id }}&shift={{ $sched->shift }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                {{ ($lang ?? 'vi') === 'en' ? 'Book this slot' : 'Chọn ca này' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Hiện chưa có lịch trực nào được công bố cho bác sĩ này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Patient Reviews & Rating Section -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-star-fill text-warning fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Đánh Giá & Nhận Xét Từ Người Bệnh</h5>
                </div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                    ⭐ {{ $doctor->rating_avg }} / 5.0 ({{ $doctor->rating_count }} lượt)
                </span>
            </div>
            <div class="card-body p-4">
                <!-- Reviews List -->
                <div class="mb-4">
                    @forelse($doctor->reviews->take(5) as $rev)
                        <div class="p-3 rounded-3 bg-light mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">{{ $rev->patient_name }}</strong>
                                <span class="text-warning small">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="bi bi-star{{ $s <= $rev->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="text-secondary small mb-1">"{{ $rev->comment }}"</p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Bệnh nhân đã khám thực tế &bull; {{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}
                            </small>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">Chưa có nhận xét nào. Hãy là người đầu tiên chia sẻ trải nghiệm khám bệnh!</p>
                    @endforelse
                </div>

                <!-- Submit Review Form -->
                <div class="p-3 bg-light bg-opacity-50 rounded-4 border">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square text-primary me-1"></i> Gửi Đánh Giá Của Bạn</h6>
                    <form method="POST" action="{{ route('public.doctors.reviews.store', $doctor) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Họ và tên của bạn *</label>
                                <input type="text" name="patient_name" class="form-control form-control-sm" placeholder="VD: Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Số điện thoại (tùy chọn)</label>
                                <input type="text" name="patient_phone" class="form-control form-control-sm" placeholder="VD: 0912 345 678">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Mức độ hài lòng *</label>
                                <select name="rating" class="form-select form-select-sm" required>
                                    <option value="5" selected>⭐⭐⭐⭐⭐ - Rất hài lòng (5 sao)</option>
                                    <option value="4">⭐⭐⭐⭐ - Hài lòng (4 sao)</option>
                                    <option value="3">⭐⭐⭐ - Bình thường (3 sao)</option>
                                    <option value="2">⭐⭐ - Chưa hài lòng (2 sao)</option>
                                    <option value="1">⭐ - Rất kém (1 sao)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Nội dung nhận xét về bác sĩ *</label>
                                <textarea name="comment" rows="2" class="form-control form-control-sm" placeholder="Chia sẻ trải nghiệm của bạn về thái độ, chuyên môn của bác sĩ..." required></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                                    <i class="bi bi-send me-1"></i> Gửi nhận xét
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
