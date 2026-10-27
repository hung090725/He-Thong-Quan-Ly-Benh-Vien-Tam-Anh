<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhãn Túi Thuốc - {{ $record->patientProfile->full_name }} - {{ $record->record_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }

        .label-container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px dashed #0284c7;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .drug-table th {
            background-color: #f8fafc;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .instruction-highlight {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .label-container {
                max-width: 100% !important;
                border: 1px solid #333 !important;
                box-shadow: none !important;
                padding: 15px !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> In Ngay Nhãn Túi Thuốc
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm ms-2">
        Đóng cửa sổ
    </button>
</div>

<div class="label-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-2 border-primary mb-3">
        <div>
            <h5 class="fw-bold text-primary text-uppercase mb-0" style="letter-spacing: 0.5px;">Bệnh Viện Đa Khoa Tâm Anh</h5>
            <div class="text-muted" style="font-size: 11px;">
                <strong>NHÀ THUỐC BỆNH VIỆN (OUTPATIENT PHARMACY)</strong> &bull; Hotline: 1800 6858
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-primary fs-6 px-3 py-1 font-monospace">{{ $record->record_code }}</span>
            <div class="text-muted" style="font-size: 10px; margin-top: 2px;">PHIẾU HƯỚNG DẪN DÙNG THUỐC</div>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="row g-2 mb-3 bg-light rounded p-2" style="font-size: 12.5px;">
        <div class="col-7">
            <span class="text-muted">Họ tên:</span>
            <strong class="text-uppercase text-dark fs-6">{{ $record->patientProfile->full_name }}</strong>
        </div>
        <div class="col-5 text-end">
            <span class="text-muted">Mã BN:</span>
            <strong class="font-monospace text-primary">{{ $record->patientProfile->patient_code }}</strong>
        </div>
        <div class="col-4">
            <span class="text-muted">Tuổi / Giới tính:</span>
            <strong>{{ $record->patientProfile->age_text }} ({{ $record->patientProfile->gender_name }})</strong>
        </div>
        <div class="col-4">
            <span class="text-muted">Bác sĩ kê đơn:</span>
            <strong>{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</strong>
        </div>
        <div class="col-4 text-end">
            <span class="text-muted">Ngày kê:</span>
            <strong>{{ $record->created_at->format('d/m/Y') }}</strong>
        </div>
        <div class="col-12">
            <span class="text-muted">Chẩn đoán:</span>
            <span class="fw-semibold text-danger">{{ $record->diagnosis }}</span>
        </div>
    </div>

    <!-- Prescriptions List -->
    <div class="mb-3">
        <table class="table table-bordered drug-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">#</th>
                    <th>Tên Thuốc & Hàm Lượng</th>
                    <th style="width: 140px;">Liều Dùng</th>
                    <th style="width: 80px;" class="text-center">Số Ngày</th>
                    <th>Cách Uống & Lời Dặn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($record->prescriptions as $idx => $p)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                        <td>
                            <strong class="text-dark d-block fs-6">{{ $p->medication_name }}</strong>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $p->dosage }}</div>
                            <div class="small text-muted">{{ $p->frequency }}</div>
                        </td>
                        <td class="text-center fw-bold">
                            {{ $p->duration_days }} ngày
                        </td>
                        <td>
                            <span class="instruction-highlight">{{ $p->instructions ?: 'Uống sau ăn no' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Không có thuốc trong đơn.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Safety Instructions & Signature Footer -->
    <div class="row align-items-center pt-2 border-top" style="font-size: 11px;">
        <div class="col-7">
            <strong class="text-danger d-block mb-1">
                <i class="bi bi-exclamation-triangle-fill"></i> LƯU Ý KHI SỬ DỤNG THUỐC:
            </strong>
            <ul class="mb-0 ps-3 text-muted">
                <li>Uống thuốc đúng giờ, đúng liều lượng chỉ định của Bác sĩ.</li>
                <li>Bảo quản thuốc nơi khô ráo, thoáng mát (dưới 30°C), tránh xa tầm tay trẻ em.</li>
                <li>Khi có dấu hiệu bất thường (dị ứng, nổi mề đay, nôn nao), ngưng thuốc và liên hệ BV ngay.</li>
            </ul>
        </div>
        <div class="col-5 text-center">
            <div class="text-muted">Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</div>
            <strong class="d-block mt-1">DƯỢC SĨ CẤP PHÁT THUỐC</strong>
            <div style="height: 48px;"></div>
            <div class="fw-bold text-dark">{{ $record->dispensedBy?->name ?? (Auth::user()?->name ?? 'Dược sĩ trực') }}</div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Automatically open print dialog
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
</body>
</html>
