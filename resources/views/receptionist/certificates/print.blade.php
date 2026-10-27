<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giấy Chứng Nhận Nghỉ Việc Hưởng BHXH - {{ $certificate->patientProfile->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #e2e8f0;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .paper {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px 45px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .dotted-line {
            border-bottom: 1px dotted #333;
            display: inline-block;
            font-weight: bold;
            padding: 0 4px;
        }

        .signature-space {
            height: 75px;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .paper {
                max-width: 100% !important;
                box-shadow: none !important;
                padding: 20px 25px !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm fw-semibold">
        <i class="bi bi-printer-fill me-1"></i> In Giấy Chứng Nhận (A4/A5)
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary ms-2">
        Đóng cửa sổ
    </button>
</div>

<div class="paper">
    <!-- Header -->
    <div class="row align-items-start pb-2 border-bottom border-dark mb-3">
        <div class="col-6 text-center">
            <div class="text-uppercase fw-bold" style="font-size: 13px;">BỆNH VIỆN ĐA KHOA TÂM ANH</div>
            <div style="font-size: 11px;">Mã số KCB: <strong>79-026</strong></div>
            <div style="font-size: 11px;">Khoa: <strong>{{ $certificate->medicalRecord->appointment->specialty?->name ?? 'Khoa Khám Bệnh Ngoại Trú' }}</strong></div>
            <div style="font-size: 11px;">Hotline: 1800 6858 &bull; tamanhhospital.vn</div>
        </div>
        <div class="col-6 text-center">
            <div class="fw-bold text-uppercase" style="font-size: 13px;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
            <div class="fw-bold" style="font-size: 13px;">Độc lập - Tự do - Hạnh phúc</div>
            <div class="text-muted" style="font-size: 10.5px; font-style: italic;">
                (Mẫu số: 01/GCN - Ban hành theo Thông tư 18/2022/TT-BYT)
            </div>
        </div>
    </div>

    <!-- Title -->
    <div class="text-center my-3">
        <h4 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">GIẤY CHỨNG NHẬN NGHỈ VIỆC HƯỞNG BẢO HIỂM XÃ HỘI</h4>
        <div class="font-monospace fw-bold text-dark" style="font-size: 13px;">
            Số lưu trữ / Mã chứng nhận: <u>{{ $certificate->certificate_code }}</u>
        </div>
    </div>

    <!-- Body Information -->
    <div class="mb-4">
        <div class="mb-2">
            <strong>I. Họ và tên người bệnh:</strong>
            <span class="text-uppercase fw-bold ms-1" style="font-size: 15px;">{{ $certificate->patientProfile->full_name }}</span>
            <span class="ms-4">Ngày sinh: <span class="fw-bold">{{ $certificate->patientProfile->dob?->format('d/m/Y') }}</span></span>
            <span class="ms-3">Giới tính: <span class="fw-bold">{{ $certificate->patientProfile->gender_name }}</span></span>
        </div>

        <div class="mb-2">
            <strong>Mã số BHXH:</strong>
            <span class="dotted-line ms-1 font-monospace" style="min-width: 170px;">{{ $certificate->patientProfile->insurance_code ?: '..........................................' }}</span>
            <span class="ms-3"><strong>Số CCCD/CMND:</strong></span>
            <span class="dotted-line ms-1 font-monospace" style="min-width: 150px;">{{ $certificate->patientProfile->identity_card ?: '................................' }}</span>
        </div>

        <div class="mb-2">
            <strong>Đơn vị làm việc:</strong>
            <span class="dotted-line ms-1" style="width: 78%;">
                {{ $certificate->employer_name ?: 'Theo kê khai của người bệnh khi lập hồ sơ' }}
            </span>
        </div>

        <div class="mb-2">
            <strong>Địa chỉ thường trú:</strong>
            <span class="dotted-line ms-1" style="width: 76%;">
                {{ $certificate->patientProfile->address ?: '................................................................................................................' }}
            </span>
        </div>

        @if($certificate->father_mother_name)
            <div class="mb-2">
                <strong>Họ tên cha/mẹ (Nghỉ chăm con ốm dưới 7 tuổi):</strong>
                <span class="dotted-line ms-1" style="width: 50%;">{{ $certificate->father_mother_name }}</span>
            </div>
        @endif

        <div class="mb-2 mt-3">
            <strong>II. Chẩn đoán và phương pháp điều trị:</strong>
        </div>

        <div class="ps-3 mb-2">
            - Chẩn đoán xác định:
            <span class="fw-bold text-dark ms-1">{{ $certificate->diagnosis }}</span>
            <span class="text-muted small ms-1">(Mã bệnh án: {{ $certificate->medicalRecord->record_code }})</span>
        </div>

        <div class="ps-3 mb-2">
            - Phương pháp điều trị:
            <span class="fw-semibold ms-1">{{ $certificate->treatment_method }}</span>
        </div>

        <div class="mb-2 mt-3">
            <strong>III. Số ngày được nghỉ việc hưởng BHXH:</strong>
        </div>

        <div class="ps-3 mb-2">
            Được nghỉ: <span class="fw-bold fs-6 text-primary">{{ $certificate->leave_days_text }}</span>.
        </div>

        <div class="ps-3 mb-3">
            Từ ngày: <span class="fw-bold">{{ $certificate->from_date->format('d/m/Y') }}</span>
            đến hết ngày: <span class="fw-bold">{{ $certificate->to_date->format('d/m/Y') }}</span>.
        </div>

        @if($certificate->notes)
            <div class="ps-3 mb-2 small text-muted">
                <em>* Lời dặn / Chế độ y khoa: {{ $certificate->notes }}</em>
            </div>
        @endif
    </div>

    <!-- Signatures -->
    <div class="row text-center mt-4 pt-2">
        <div class="col-4 d-flex flex-column align-items-center justify-content-center">
            <!-- Security QR Code -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode(url('/booking/lookup?code=' . $certificate->medicalRecord->appointment->appointment_code)) }}" alt="QR Xác thực" class="border p-1 rounded" style="width: 100px; height: 100px;">
            <div class="text-muted mt-1" style="font-size: 9.5px;">Quét mã tra cứu xác thực</div>
            <div class="font-monospace text-muted" style="font-size: 8.5px;">{{ $certificate->certificate_code }}</div>
        </div>

        <div class="col-4">
            <div class="fw-bold text-uppercase" style="font-size: 12px;">NGƯỜI HÀNH NGHỀ KCB</div>
            <div class="text-muted" style="font-size: 11px;">(Ký, ghi rõ họ tên)</div>
            <div class="signature-space"></div>
            <div class="fw-bold">{{ $certificate->doctor ? $certificate->doctor->full_name_with_title : '--' }}</div>
            <div style="font-size: 10.5px;" class="text-muted">CCHN số: {{ 100000 + $certificate->doctor_id }}/BYT-CCHN</div>
        </div>

        <div class="col-4">
            <div class="text-muted" style="font-size: 11px;">Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</div>
            <div class="fw-bold text-uppercase" style="font-size: 12px;">THỦ TRƯỞNG ĐƠN VỊ</div>
            <div class="text-muted" style="font-size: 11px;">(Ký tên, đóng dấu)</div>
            <div class="signature-space"></div>
            <div class="fw-bold">TS.BS. NGUYỄN VĂN AN</div>
            <div style="font-size: 10.5px;" class="text-muted">Giám đốc Bệnh viện</div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
</body>
</html>
