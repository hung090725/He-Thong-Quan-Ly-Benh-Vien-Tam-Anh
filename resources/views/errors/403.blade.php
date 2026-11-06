<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy Cập Bị Hạn Chế | Bệnh Viện Đa Khoa Tâm Anh</title>
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
            max-width: 680px;
            width: 100%;
            overflow: hidden;
        }
        .brand-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }
        .role-badge {
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <!-- Header with Hospital Branding -->
        <div class="brand-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-hospital fs-4 text-primary"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white text-uppercase tracking-wide">BỆNH VIỆN ĐA KHOA TÂM ANH</h6>
                    <small class="text-white-50">Hệ Thống Phân Quyền An Toàn & Bảo Mật Y Tế</small>
                </div>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 px-2.5 py-1.5 font-monospace">
                HTTP 403
            </span>
        </div>

        <div class="p-4 p-md-5">
            <!-- Alert Icon and Title -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-shield-lock-fill" style="font-size: 2.75rem;"></i>
                </div>
                <h3 class="fw-extrabold text-dark mb-2">QUYỀN HẠN BỊ HẠN CHẾ</h3>
                <p class="text-muted mb-0 fs-6">
                    {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập vào chức năng hoặc khu vực được yêu cầu.' }}
                </p>
            </div>

            <!-- Current Identity Info Box -->
            @auth
                <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-badge fs-4 text-secondary"></i>
                            <div>
                                <small class="text-muted d-block">Tài khoản đang đăng nhập:</small>
                                <strong class="text-dark">{{ auth()->user()->name }}</strong>
                                <span class="text-muted small">({{ auth()->user()->email }})</span>
                            </div>
                        </div>
                        <div>
                            @php
                                $roleDisplay = match(auth()->user()->role) {
                                    'admin' => ['label' => 'Quản Trị Viên', 'class' => 'bg-danger text-white'],
                                    'doctor' => ['label' => 'Bác Sĩ Khám Bệnh', 'class' => 'bg-primary text-white'],
                                    'receptionist' => ['label' => 'Lễ Tân Tiếp Đón', 'class' => 'bg-info text-dark'],
                                    'patient' => ['label' => 'Bệnh Nhân', 'class' => 'bg-success text-white'],
                                    default => ['label' => auth()->user()->role, 'class' => 'bg-secondary text-white'],
                                };
                            @endphp
                            <span class="badge {{ $roleDisplay['class'] }} px-2.5 py-1.5 rounded-pill role-badge">
                                {{ $roleDisplay['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- How to gain access guide -->
            <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm">
                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-key-fill text-warning"></i> Làm sao để có quyền truy cập?
                </h6>
                <p class="text-muted small mb-2">
                    Khu vực bạn vừa truy cập yêu cầu vai trò chuyên biệt. Để kiểm tra hoặc sử dụng đầy đủ chức năng, bạn có thể chuyển sang tài khoản có thẩm quyền tương ứng:
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Vai trò</th>
                                <th>Email đăng nhập</th>
                                <th>Mật khẩu</th>
                                <th>Quyền hạn chính</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle">Admin</span></td>
                                <td class="font-monospace">admin@tamanh.vn</td>
                                <td class="font-monospace">123456</td>
                                <td>Toàn quyền hệ thống, Báo cáo & Cài đặt</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info-subtle text-info border border-info-subtle">Lễ tân</span></td>
                                <td class="font-monospace">receptionist@tamanh.vn</td>
                                <td class="font-monospace">123456</td>
                                <td>Tiếp đón, Check-in, Walk-in, Giường bệnh</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">Bác sĩ</span></td>
                                <td class="font-monospace">doctor@tamanh.vn</td>
                                <td class="font-monospace">123456</td>
                                <td>Khám bệnh, Kê đơn, Hồ sơ bệnh án EMR</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success-subtle text-success border border-success-subtle">Bệnh nhân</span></td>
                                <td class="font-monospace">patient@gmail.com</td>
                                <td class="font-monospace">123456</td>
                                <td>Đặt lịch khám, Xem bệnh án cá nhân</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 d-inline-flex align-items-center gap-2" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'">
                    <i class="bi bi-arrow-left"></i> Quay lại trang trước
                </button>

                @auth
                    @php
                        $homeUrl = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'receptionist' => route('reception.appointments.index'),
                            'doctor' => route('doctor.consultation.index'),
                            'patient' => route('patient.history'),
                            default => url('/'),
                        };
                    @endphp
                    <a href="{{ $homeUrl }}" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-speedometer2"></i> Về Bàn Làm Việc Của Bạn
                    </a>
                @else
                    <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-house-door"></i> Về Trang Chủ Bệnh Viện
                    </a>
                @endauth

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-3 py-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Đổi tài khoản khác
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
