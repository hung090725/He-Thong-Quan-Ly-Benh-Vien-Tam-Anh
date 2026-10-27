<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thẻ Bệnh Nhân - {{ $patient->patient_code }} - BV Tâm Anh</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card-container {
            width: 500px;
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0d9488 100%);
            border-radius: 16px;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .card-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .barcode-stripes {
            height: 35px;
            background: repeating-linear-gradient(
                90deg,
                #ffffff,
                #ffffff 2px,
                transparent 2px,
                transparent 4px,
                #ffffff 4px,
                #ffffff 6px,
                transparent 6px,
                transparent 9px
            );
            width: 100%;
        }

        .smart-chip {
            width: 45px;
            height: 35px;
            background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%);
            border-radius: 6px;
            border: 1px solid #d97706;
            position: relative;
        }

        .smart-chip::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #b45309;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .card-container {
                box-shadow: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                margin: 20px auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print mb-4 d-flex gap-2">
        <a href="{{ route('reception.patients.show', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại hồ sơ
        </a>
        <button onclick="window.print()" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-printer-fill"></i> In thẻ bệnh nhân (Ctrl + P)
        </button>
    </div>

    <div class="card-container">
        <!-- Top Row: Logo & Chip -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-hospital fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white text-uppercase" style="letter-spacing: 0.5px;">BỆNH VIỆN ĐA KHOA TÂM ANH</h6>
                    <small class="text-white-50" style="font-size: 0.7rem;">THẺ BỆNH NHÂN NGOẠI TRÚ (PATIENT ID CARD)</small>
                </div>
            </div>
            <div class="smart-chip shadow-sm"></div>
        </div>

        <!-- Middle Row: Patient Info -->
        <div class="row align-items-center mb-3">
            <div class="col-8">
                <div class="text-white-50 small" style="font-size: 0.75rem;">HỌ VÀ TÊN BỆNH NHÂN</div>
                <div class="fs-5 fw-bold text-uppercase text-white text-truncate mb-2">
                    {{ $patient->full_name }}
                </div>

                <div class="row g-2 small">
                    <div class="col-6">
                        <span class="text-white-50 d-block" style="font-size: 0.7rem;">NGÀY SINH:</span>
                        <strong class="text-white">{{ $patient->dob ? $patient->dob->format('d/m/Y') : '--' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-white-50 d-block" style="font-size: 0.7rem;">GIỚI TÍNH:</span>
                        <strong class="text-white">{{ $patient->gender_name }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-white-50 d-block" style="font-size: 0.7rem;">SỐ ĐIỆN THOẠI:</span>
                        <strong class="text-white font-monospace">{{ $patient->phone }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-white-50 d-block" style="font-size: 0.7rem;">MÃ BHYT:</span>
                        <strong class="text-white font-monospace">{{ $patient->insurance_code ?: '--' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-white-50 d-block" style="font-size: 0.7rem;">NHÓM MÁU:</span>
                        <strong class="badge bg-danger text-white font-monospace px-2 py-0">{{ $patient->blood_group ?: 'Chưa rõ' }}</strong>
                    </div>
                </div>

                @if($patient->hasAllergies())
                    <div class="mt-2 p-1 bg-danger text-white rounded text-truncate fw-bold shadow-sm" style="font-size: 0.68rem;" title="{{ $patient->allergies }}">
                        <i class="bi bi-exclamation-triangle-fill"></i> DỊ ỨNG: {{ $patient->allergies }}
                    </div>
                @endif
            </div>

            <div class="col-4 text-center">
                <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center shadow" style="width: 75px; height: 75px; font-size: 2rem; font-weight: 800;">
                    {{ mb_substr($patient->full_name, 0, 1) }}
                </div>
                <div class="mt-2 text-white-50 font-monospace" style="font-size: 0.65rem;">
                    {{ $patient->identity_card ? 'CCCD: ' . $patient->identity_card : '' }}
                </div>
            </div>
        </div>

        <!-- Bottom Row: Barcode & Code -->
        <div class="pt-2 border-top border-white border-opacity-25">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="font-monospace fw-bold text-white fs-6" style="letter-spacing: 1.5px;">
                    {{ $patient->patient_code }}
                </div>
                <div class="text-white-50" style="font-size: 0.7rem;">
                    Hotline: 1800 6858
                </div>
            </div>
            <div class="barcode-stripes rounded-1 opacity-75"></div>
        </div>
    </div>

    <div class="no-print mt-3 text-muted small text-center" style="max-width: 500px;">
        <i class="bi bi-info-circle me-1"></i> Xuất trình thẻ này tại quầy Lễ tân hoặc các phòng khám để được tiếp đón và tra cứu bệnh án nhanh nhất.
    </div>

</body>
</html>
