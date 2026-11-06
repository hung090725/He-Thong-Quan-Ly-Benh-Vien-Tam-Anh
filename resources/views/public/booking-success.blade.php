@extends('layouts.master')

@section('title', 'Đặt Lịch Thành Công - Mã hẹn: ' . $appointment->appointment_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-header bg-success text-white text-center py-4">
                <div class="rounded-circle bg-white text-success d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 58px; height: 58px;">
                    <i class="bi bi-check-lg fs-2"></i>
                </div>
                <h3 class="fw-bold mb-1">ĐẶT LỊCH KHÁM THÀNH CÔNG!</h3>
                <p class="mb-0 text-white-50">Hệ thống đã tiếp nhận yêu cầu khám bệnh của Quý khách.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                @if($appointment->status === 'waitlist')
                    <div class="alert alert-warning border-start border-4 border-warning mb-4 p-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                            <div>
                                <h5 class="fw-bold mb-1">QUÝ KHÁCH ĐANG Ở DANH SÁCH CHỜ (WAITLIST)</h5>
                                <div class="mb-0">
                                    Vị trí thứ tự chờ của Quý khách: <strong class="badge bg-warning text-dark fs-6">#{{ $appointment->waitlist_position }}</strong>.
                                    Khi có bệnh nhân khác hủy lịch hoặc chuyển ca, hệ thống thông minh sẽ tự động ưu tiên xếp chỗ và gửi email thông báo cho Quý khách.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Appointment Code Highlight Box -->
                <div class="p-3 p-md-4 bg-light border border-2 border-primary rounded-3 mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8 text-center text-md-start">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Mã tra cứu lịch hẹn của Quý khách</span>
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 my-2">
                                <span class="fs-2 fw-bold text-primary font-monospace" id="appointmentCodeText">{{ $appointment->appointment_code }}</span>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 no-print" onclick="window.copyWithFeedback('{{ $appointment->appointment_code }}', this, 'Đã sao chép mã lịch hẹn {{ $appointment->appointment_code }}!')" title="Sao chép">
                                    <i class="bi bi-clipboard me-1"></i> Sao chép
                                </button>
                            </div>
                            <small class="text-danger d-block mb-3">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Quý khách vui lòng chụp màn hình hoặc đưa mã QR bên cạnh vào Kiosk tự phục vụ tại sảnh để check-in.
                            </small>

                            <!-- Calendar Sync Quick Actions -->
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 pt-2 border-top no-print">
                                <a href="{{ $googleCalendarUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-google me-1"></i> Thêm vào Google Calendar
                                </a>
                                <a href="{{ $icsDownloadUrl ?? route('calendar.ics', $appointment->appointment_code) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-calendar-plus me-1"></i> Tải file Lịch (.ics cho iPhone/Outlook)
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center border-start-md">
                            <div class="p-2 bg-white border rounded-3 d-inline-block shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($appointment->appointment_code) }}" alt="QR Check-in {{ $appointment->appointment_code }}" style="width: 115px; height: 115px;" class="img-fluid">
                            </div>
                            <div class="small text-muted fw-semibold mt-1" style="font-size: 0.72rem;">MÃ QR KIOSK CHECK-IN</div>
                        </div>
                    </div>
                </div>

                <!-- Summary Info Table -->
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Chi tiết phiếu hẹn</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light text-muted" style="width: 35%;">Họ tên bệnh nhân:</th>
                                <td class="fw-bold">{{ $appointment->patientProfile->full_name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Mã số hồ sơ y tế:</th>
                                <td><code>{{ $appointment->patientProfile->patient_code }}</code></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Số điện thoại:</th>
                                <td>{{ $appointment->patientProfile->phone }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Chuyên khoa khám:</th>
                                <td><span class="badge bg-info text-dark">{{ $appointment->specialty->name }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Bác sĩ phụ trách:</th>
                                <td class="fw-semibold text-primary">{{ $appointment->doctor->full_name_with_title }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Phòng khám dự kiến:</th>
                                <td>{{ $appointment->doctor->room_number }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Thời gian khám:</th>
                                <td>
                                    <strong>{{ $appointment->appointment_date->format('d/m/Y') }}</strong>
                                    &bull; {{ $appointment->shift_name }}
                                    @if($appointment->appointment_time)
                                        (khoảng {{ substr($appointment->appointment_time, 0, 5) }})
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Triệu chứng ban đầu:</th>
                                <td>{{ $appointment->symptom_description }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Trạng thái hiện tại:</th>
                                <td>
                                    <x-status-badge :status="$appointment->status" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Next steps guide -->
                <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                    <i class="bi bi-signpost-2-fill fs-3 text-warning"></i>
                    <div class="small">
                        <strong>Hướng dẫn khi đến viện:</strong><br>
                        1. Quý khách vui lòng có mặt tại <strong>Quầy Lễ tân Bệnh viện Tâm Anh</strong> trước giờ khám 15 phút.<br>
                        2. Cung cấp <strong>Mã hẹn ({{ $appointment->appointment_code }})</strong> hoặc <strong>Số điện thoại</strong> để nhân viên check-in và cấp số thứ tự vào phòng khám.<br>
                        3. Đem theo CCCD/Thẻ BHYT và hồ sơ xét nghiệm trước đó (nếu có).
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                    <a href="{{ route('booking.lookup', ['keyword' => $appointment->appointment_code]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Tra cứu trạng thái phiếu hẹn
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                            <i class="bi bi-printer me-1"></i> In phiếu hẹn
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="bi bi-house me-1"></i> Về Trang chủ
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
