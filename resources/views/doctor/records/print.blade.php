<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Thuốc - {{ $record->record_code }} - BV Tâm Anh</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
        }

        .prescription-paper {
            max-width: 720px;
            margin: 2rem auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .hospital-title {
            color: #0369a1;
            letter-spacing: 0.5px;
        }

        .med-item {
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .med-item:last-child {
            border-bottom: none;
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
            .prescription-paper {
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print bg-white border-bottom py-2 px-3 text-center sticky-top shadow-sm">
        <div class="container d-flex justify-content-between align-items-center" style="max-width: 720px;">
            <a href="{{ route('doctor.records.show', $record) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại hồ sơ
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-printer-fill"></i> In Đơn Thuốc (Ctrl + P)
                </button>
            </div>
        </div>
    </div>

    <!-- Prescription Paper Container -->
    <div class="prescription-paper">
        <!-- Hospital Header -->
        <div class="d-flex justify-content-between align-items-start pb-3 border-bottom mb-3">
            <div>
                <h5 class="fw-bold hospital-title text-uppercase mb-1">BỆNH VIỆN ĐA KHOA TÂM ANH</h5>
                <div class="small text-muted">108 Hoàng Như Tiếp, P. Bồ Đề, Q. Long Biên, TP. Hà Nội</div>
                <div class="small text-muted">Hotline: <strong>1800 6858</strong> &bull; Cấp cứu 24/7: <strong>024 3872 3872</strong></div>
            </div>
            <div class="text-end">
                <div class="small text-muted">Khoa: <strong>{{ $record->appointment->specialty ? $record->appointment->specialty->name : 'Ngoại Trú' }}</strong></div>
                <div class="small text-muted">Mã ĐT: <strong class="font-monospace text-primary">{{ $record->record_code }}</strong></div>
                <div class="small text-muted">Mã BN: <strong class="font-monospace">{{ $record->patientProfile->patient_code }}</strong></div>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center my-3">
            <h3 class="fw-bold text-dark text-uppercase mb-0" style="letter-spacing: 1px;">ĐƠN THUỐC NGOẠI TRÚ</h3>
            <div class="small text-muted">(Mẫu chuẩn Bộ Y Tế)</div>
        </div>

        <!-- Patient Demographics -->
        <div class="row g-2 mb-3 small border rounded p-3 bg-light bg-opacity-50">
            <div class="col-8">
                Họ và tên bệnh nhân: <strong class="text-uppercase text-dark fs-6">{{ $record->patientProfile->full_name }}</strong>
            </div>
            <div class="col-4 text-end">
                Giới tính: <strong>{{ $record->patientProfile->gender_name }}</strong> &bull; Tuổi: <strong>{{ $record->patientProfile->age_text }}</strong>
            </div>
            <div class="col-6">
                Ngày sinh: <strong>{{ $record->patientProfile->formatted_dob }}</strong>
            </div>
            <div class="col-6 text-end">
                Số điện thoại: <strong class="font-monospace">{{ $record->patientProfile->phone }}</strong>
            </div>
            @if($record->patientProfile->insurance_code)
                <div class="col-12">
                    Mã số thẻ BHYT: <strong class="font-monospace text-primary">{{ $record->patientProfile->insurance_code }}</strong>
                </div>
            @endif
            <div class="col-12">
                Địa chỉ: <span>{{ $record->patientProfile->address ?: 'Chưa cập nhật' }}</span>
            </div>
            <div class="col-12 pt-2 border-top">
                Chẩn đoán xác định: <strong class="text-danger fs-6">{{ $record->diagnosis }}</strong>
            </div>
        </div>

        <!-- Prescribed Medications List -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark text-uppercase small mb-2 pb-1 border-bottom">
                <i class="bi bi-capsule me-1 text-success"></i> CHỈ ĐỊNH DÙNG THUỐC:
            </h6>

            @forelse($record->prescriptions as $idx => $p)
                <div class="med-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <strong class="text-dark">{{ $idx + 1 }}. {{ $p->medication_name }}</strong>
                        </div>
                        <div class="text-end fw-semibold text-muted font-monospace small">
                            Dùng {{ $p->duration_days }} ngày
                        </div>
                    </div>
                    <div class="small ps-3 text-secondary">
                        <div>&bull; <strong>Liều dùng & Tần suất:</strong> {{ $p->dosage }} &bull; {{ $p->frequency }}</div>
                        @if($p->instructions)
                            <div>&bull; <em>Cách dùng:</em> {{ $p->instructions }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    Không có chỉ định thuốc ngoại trú. Bệnh nhân tuân thủ chế độ theo dõi lâm sàng.
                </div>
            @endforelse
        </div>

        <!-- Advice & Follow-up -->
        <div class="row g-2 mb-4 small">
            @if($record->treatment_plan)
                <div class="col-12">
                    <strong>Lời dặn của bác sĩ:</strong>
                    <div class="text-dark ps-2">{{ $record->treatment_plan }}</div>
                </div>
            @endif

            @if($record->follow_up_date)
                <div class="col-12 mt-2">
                    <div class="p-2 px-3 bg-warning-subtle text-warning-emphasis border rounded d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event"></i>
                        <span>Hẹn khám lại vào ngày: <strong>{{ $record->follow_up_date->format('d/m/Y') }}</strong> (hoặc khi có dấu hiệu bất thường).</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="row text-center mt-5 small">
            <div class="col-6">
                <div class="text-muted">BỆNH NHÂN / NGƯỜI NHẬN ĐƠN</div>
                <small class="text-muted">(Ký và ghi rõ họ tên)</small>
                <div style="height: 65px;"></div>
                <div class="fw-semibold text-dark">{{ $record->patientProfile->full_name }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted">Hà Nội, ngày {{ $record->created_at->format('d') }} tháng {{ $record->created_at->format('m') }} năm {{ $record->created_at->format('Y') }}</div>
                <div class="fw-bold text-primary">BÁC SĨ ĐIỀU TRỊ</div>
                <small class="text-muted">(Ký, ghi rõ họ tên và đóng dấu)</small>
                <div style="height: 65px;"></div>
                <div class="fw-bold text-dark">{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</div>
            </div>
        </div>

        <!-- Footer Instructions -->
        <div class="text-center pt-3 border-top mt-4 text-muted" style="font-size: 0.72rem;">
            <div>* Đơn thuốc có giá trị mua trong vòng 05 ngày kể từ ngày kê đơn.</div>
            <div>* Khi tái khám vui lòng mang theo đơn thuốc này và hồ sơ bệnh án liên quan.</div>
        </div>
    </div>

</body>
</html>
