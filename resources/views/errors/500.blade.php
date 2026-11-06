<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Hệ Thống Đang Xử Lý | Bệnh Viện Đa Khoa Tâm Anh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            padding: 1.5rem;
        }
        .error-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(148, 163, 184, 0.15);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .brand-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }
    </style>
</head>
<body>
    <div class="error-card text-center">
        <div class="brand-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-hospital fs-4 text-primary"></i>
                </div>
                <div class="text-start">
                    <h6 class="fw-bold mb-0 text-white text-uppercase tracking-wide">BỆNH VIỆN ĐA KHOA TÂM ANH</h6>
                    <small class="text-white-50">Hệ Thống Đang Được Bảo Trì & Nâng Cấp Tự Động</small>
                </div>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 px-2.5 py-1.5 font-monospace">
                HTTP 500
            </span>
        </div>

        <div class="p-4 p-md-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-gear-wide-connected" style="font-size: 2.75rem;"></i>
            </div>
            <h3 class="fw-extrabold text-dark mb-2">HỆ THỐNG ĐANG XỬ LÝ YÊU CẦU</h3>
            <p class="text-muted mb-4 fs-6">
                Máy chủ vừa gặp một gián đoạn tạm thời trong quá trình đồng bộ dữ liệu. Đội ngũ kỹ thuật đã được thông báo và đang tự động xử lý. Vui lòng thử lại sau giây lát.
            </p>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button type="button" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Thử Lại Ngay
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-house-door"></i> Về Trang Chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>
