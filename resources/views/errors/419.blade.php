<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Phiên Làm Việc Hết Hạn | Bệnh Viện Đa Khoa Tâm Anh</title>
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
                    <small class="text-white-50">Hệ Thống Bảo Mật Dữ Liệu Y Tế (CSRF)</small>
                </div>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 px-2.5 py-1.5 font-monospace">
                HTTP 419
            </span>
        </div>

        <div class="p-4 p-md-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-hourglass-bottom" style="font-size: 2.75rem;"></i>
            </div>
            <h3 class="fw-extrabold text-dark mb-2">PHIÊN LÀM VIỆC ĐÃ HẾT HẠN</h3>
            <p class="text-muted mb-4 fs-6">
                Để bảo vệ an toàn thông tin y tế của người bệnh, phiên kết nối đã tự động làm mới do bạn để màn hình quá lâu mà không thao tác. Vui lòng tải lại trang hoặc đăng nhập lại.
            </p>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button type="button" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Tải Lại Trang (F5)
                </button>
                <a href="{{ route('login') }}" class="btn btn-outline-primary px-4 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập Lại
                </a>
            </div>
        </div>
    </div>
</body>
</html>
