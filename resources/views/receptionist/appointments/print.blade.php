<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Khám Bệnh - {{ $appointment->appointment_code }} - BV Tâm Anh</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #1e293b;
        }

        .ticket-card {
            max-width: 480px;
            margin: 2rem auto;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .queue-badge {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            display: inline-block;
            text-align: center;
        }

        .queue-number {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 2px;
        }

        .barcode-stripes {
            height: 40px;
            background: repeating-linear-gradient(
                90deg,
                #000000,
                #000000 2px,
                #ffffff 2px,
                #ffffff 4px,
                #000000 4px,
                #000000 7px,
                #ffffff 7px,
                #ffffff 9px
            );
            width: 80%;
            margin: 0 auto;
        }

        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .ticket-card {
                max-width: 100% !important;
                margin: 0 !important;
                border: 1px solid #000000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 15px !important;
            }
            .queue-badge {
                background: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print bg-white border-bottom py-2 px-3 text-center sticky-top shadow-sm">
        <div class="container d-flex justify-content-between align-items-center" style="max-width: 480px;">
            <a href="{{ route('reception.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-printer-fill"></i> In phiếu khám (Ctrl + P)
                </button>
            </div>
        </div>
    </div>

    <!-- Ticket Container -->
    <div class="ticket-card">
        <!-- Hospital Header -->
        <div class="text-center pb-3 border-bottom">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-hospital fs-6"></i>
                </div>
                <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; color: #0369a1;">BỆNH VIỆN ĐA KHOA TÂM ANH</h6>
            </div>
            <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                108 Hoàng Như Tiếp, Long Biên, Hà Nội &bull; Hotline: <strong>1800 6858</strong>
            </p>
        </div>

        <!-- Ticket Title -->
        <div class="text-center my-3">
            <h5 class="fw-bold mb-1 text-dark text-uppercase">PHIẾU TIẾP NHẬN NGOẠI TRÚ</h5>
            <small class="text-muted">Mã hẹn: <strong class="font-monospace text-primary">{{ $appointment->appointment_code }}</strong></small>
        </div>

        @if($appointment->is_priority)
            <div class="alert alert-danger py-1 px-2 text-center mb-3 fw-bold small border-2 border-danger">
                <i class="bi bi-star-fill text-warning me-1"></i> ★ ĐỐI TƯỢNG ƯU TIÊN TIẾP ĐÓN & KHÁM ★
            </div>
        @endif

        <!-- Big Queue Number & Clinic Room -->
        <div class="text-center mb-3">
            <div class="queue-badge w-100 shadow-sm">
                <div class="small text-uppercase text-white-50 fw-bold" style="letter-spacing: 1px;">SỐ THỨ TỰ KHÁM (STT)</div>
                <div class="queue-number my-1">
                    #{{ str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="fw-semibold text-white">
                    {{ $appointment->shift_name }}
                </div>
            </div>
        </div>

        <!-- Designated Clinic Room Banner -->
        <div class="alert alert-warning py-2 px-3 text-center mb-3 border">
            <div class="small text-muted fw-bold text-uppercase">Phòng khám chỉ định:</div>
            <div class="fs-5 fw-bold text-danger">
                {{ $appointment->doctor ? $appointment->doctor->room_number : 'Phòng Tiếp Đón' }}
            </div>
            <div class="small fw-semibold text-dark">
                {{ $appointment->doctor ? $appointment->doctor->full_name_with_title : '--' }}
            </div>
            <div class="small text-muted">
                Chuyên khoa: <strong>{{ $appointment->specialty ? $appointment->specialty->name : '--' }}</strong>
            </div>
        </div>

        <!-- Patient Demographics -->
        <div class="border rounded p-3 mb-3 bg-light bg-opacity-50 small">
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span class="text-muted">Bệnh nhân:</span>
                <strong class="text-dark text-uppercase">{{ $appointment->patientProfile->full_name }}</strong>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span class="text-muted">Mã hồ sơ (Mã BN):</span>
                <span class="font-monospace fw-bold text-primary">{{ $appointment->patientProfile->patient_code }}</span>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span class="text-muted">Năm sinh / Giới tính:</span>
                <span>
                    {{ $appointment->patientProfile->dob ? $appointment->patientProfile->dob->format('Y') . ' (' . $appointment->patientProfile->dob->age . ' tuổi)' : '--' }}
                    &bull; {{ $appointment->patientProfile->gender_name }}
                </span>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span class="text-muted">Số điện thoại:</span>
                <span class="font-monospace">{{ $appointment->patientProfile->phone }}</span>
            </div>
            @if($appointment->patientProfile->insurance_code)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Thẻ BHYT:</span>
                    <span class="font-monospace fw-semibold">{{ $appointment->patientProfile->insurance_code }}</span>
                </div>
            @endif
            @if(str_contains($appointment->symptom_description ?? '', '[Sinh hiệu:'))
                @php
                    preg_match('/\[Sinh hiệu: (.*?)\]/', $appointment->symptom_description, $vMatch);
                    $vitalsText = $vMatch[1] ?? '';
                    $cleanDesc = trim(preg_replace('/\[Sinh hiệu: .*?\]/', '', preg_replace('/\[ƯU TIÊN\]/', '', $appointment->symptom_description)));
                @endphp
                @if($vitalsText)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted"><i class="bi bi-heart-pulse-fill text-danger me-1"></i> Sinh hiệu đo tại quầy:</span>
                        <strong class="text-dark text-end">{{ $vitalsText }}</strong>
                    </div>
                @endif
                <div class="py-1">
                    <span class="text-muted d-block mb-1">Lý do khám / Triệu chứng:</span>
                    <div class="text-dark fw-medium">{{ $cleanDesc ?: 'Khám tổng quát' }}</div>
                </div>
            @else
                <div class="py-1">
                    <span class="text-muted d-block mb-1">Lý do khám / Triệu chứng:</span>
                    <div class="text-dark fw-medium">{{ $appointment->symptom_description }}</div>
                </div>
            @endif
        </div>

        <!-- Barcode Simulation -->
        <div class="text-center mb-3">
            <div class="barcode-stripes mb-1"></div>
            <div class="font-monospace text-muted small" style="font-size: 0.7rem; letter-spacing: 3px;">
                *{{ $appointment->appointment_code }}*
            </div>
        </div>

        <!-- Instructions & Footer -->
        <div class="text-center pt-2 border-top small text-muted" style="font-size: 0.75rem;">
            <p class="mb-1 fw-semibold text-dark">
                <i class="bi bi-info-circle me-1"></i> Quý khách vui lòng ngồi chờ tại cửa phòng khám.
            </p>
            <p class="mb-0">
                Nhân viên y tế sẽ gọi số thứ tự vào phòng khám. Chúc Quý khách nhiều sức khỏe!
            </p>
            <div class="mt-2 text-muted" style="font-size: 0.7rem;">
                Thời gian in phiếu: {{ date('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>

</body>
</html>
