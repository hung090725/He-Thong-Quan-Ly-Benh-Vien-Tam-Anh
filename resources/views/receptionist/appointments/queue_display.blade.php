<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điện Tử Gọi Khám Ngoại Trú - BV Đa Khoa Tâm Anh</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --tam-bg: #0b1329;
            --tam-card: #152243;
            --tam-card-border: #233866;
            --tam-cyan: #06b6d4;
            --tam-accent: #10b981;
        }

        body {
            background-color: var(--tam-bg);
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .header-bar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 2px solid var(--tam-cyan);
            padding: 1rem 2rem;
        }

        .queue-card {
            background-color: var(--tam-card);
            border: 1px solid var(--tam-card-border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            transition: transform 0.2s;
        }

        .queue-card:hover {
            transform: translateY(-2px);
            border-color: var(--tam-cyan);
        }

        .queue-num-badge {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 800;
            padding: 0.25rem 0.85rem;
            border-radius: 8px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .room-badge {
            background-color: #f59e0b;
            color: #000000;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 1rem;
        }

        .live-clock {
            font-family: 'JetBrains Mono', monospace;
            color: var(--tam-cyan);
            font-size: 1.6rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Header Bar -->
    <header class="header-bar d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                <i class="bi bi-hospital fs-4 text-white"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-white text-uppercase" style="letter-spacing: 1px;">BỆNH VIỆN ĐA KHOA TÂM ANH</h4>
                <div class="small text-secondary">HỆ THỐNG ĐIỀU PHỐI & HÀNG ĐỢI TIẾP NHẬN NGOẠI TRÚ TỰ ĐỘNG</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <div class="small text-secondary" id="currentDateString">Đang tải ngày...</div>
                <div class="live-clock" id="liveClock">00:00:00</div>
            </div>
            <a href="{{ route('reception.appointments.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-left"></i> Bàn lễ tân
            </a>
            <button onclick="toggleFullScreen()" class="btn btn-primary btn-sm">
                <i class="bi bi-arrows-fullscreen"></i> Toàn màn hình
            </button>
        </div>
    </header>

    <!-- Main Live Queue Container -->
    <main class="container-fluid px-4 py-4">
        <div class="row g-4">
            <!-- Left Column: Patients Waiting to Enter Consultation Room -->
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary">
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                        <span class="spinner-grow spinner-grow-sm text-info" role="status"></span>
                        DANH SÁCH MỜI VÀO PHÒNG KHÁM (ĐÃ TIẾP NHẬN)
                    </h5>
                    <span class="badge bg-info text-dark fs-6 px-3 py-1">
                        {{ $waitingAppointments->count() }} bệnh nhân đang chờ
                    </span>
                </div>

                <div class="row g-3">
                    @forelse($waitingAppointments as $app)
                        <div class="col-md-6">
                            <div class="queue-card h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="queue-num-badge">
                                        #{{ str_pad($app->queue_number, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="room-badge shadow-sm">
                                        <i class="bi bi-door-open-fill me-1"></i> {{ $app->doctor ? $app->doctor->room_number : 'Phòng khám' }}
                                    </div>
                                </div>

                                <div class="fw-bold fs-5 text-white mb-1">
                                    @php
                                        // Privacy masking: e.g. "Nguyen Van Hung" -> "Nguyễn V*** Hưng"
                                        $nameParts = explode(' ', trim($app->patientProfile->full_name));
                                        if (count($nameParts) > 2) {
                                            $maskedName = $nameParts[0] . ' ' . mb_substr($nameParts[1], 0, 1) . '*** ' . end($nameParts);
                                        } else {
                                            $maskedName = $app->patientProfile->full_name;
                                        }
                                    @endphp
                                    {{ $maskedName }}
                                </div>

                                <div class="small text-secondary mb-2">
                                    Mã BN: <strong class="text-info font-monospace">{{ $app->patientProfile->patient_code }}</strong>
                                </div>

                                <div class="pt-2 border-top border-secondary small d-flex justify-content-between text-secondary">
                                    <span>Khoa: <strong class="text-light">{{ $app->specialty ? $app->specialty->name : '--' }}</strong></span>
                                    <span>BS: <strong class="text-light">{{ $app->doctor ? $app->doctor->full_name_with_title : '--' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-check2-circle fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                            <h5 class="text-secondary">Hiện tại không có bệnh nhân nào trong hàng đợi chờ khám.</h5>
                            <p class="small text-muted">Bệnh nhân sau khi check-in tại quầy sẽ tự động hiển thị số thứ tự tại đây.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Recent Consultations & Hospital Info -->
            <div class="col-lg-4">
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary">
                        <h6 class="fw-bold text-white mb-0 text-uppercase">
                            <i class="bi bi-check-all text-success me-1"></i> Ca Vừa Hoàn Tất Khám
                        </h6>
                        <span class="badge bg-success-subtle text-success small">Hôm nay</span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @forelse($recentCompleted as $c)
                            <div class="p-2 px-3 rounded bg-dark bg-opacity-50 border border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-light small">{{ $c->patientProfile->full_name }}</div>
                                    <small class="text-secondary font-monospace">{{ $c->appointment_code }} &bull; {{ $c->doctor ? $c->doctor->room_number : '' }}</small>
                                </div>
                                <span class="badge bg-success">Đã khám</span>
                            </div>
                        @empty
                            <div class="text-secondary small p-3 text-center border border-secondary rounded">
                                Chưa có ca khám nào hoàn tất trong ngày hôm nay.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Hospital Announcements Card -->
                <div class="p-3 rounded border border-info bg-info bg-opacity-10 text-white">
                    <h6 class="fw-bold text-info mb-2">
                        <i class="bi bi-megaphone-fill me-1"></i> Hướng Dẫn Dành Cho Bệnh Nhân
                    </h6>
                    <ul class="small mb-0 ps-3 text-secondary d-flex flex-column gap-1">
                        <li>Quý khách vui lòng kiểm tra <strong>Số thứ tự (STT)</strong> trên phiếu tiếp nhận.</li>
                        <li>Ngồi chờ tại khu vực ghế trước cửa phòng khám ghi trên phiếu.</li>
                        <li>Khi màn hình và loa gọi đến số thứ tự, xin mời vào phòng khám.</li>
                        <li>Cần trợ giúp khẩn cấp? Vui lòng liên hệ bàn Lễ tân hoặc gọi Hotline <strong>1800 6858</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', { hour12: false });
            document.getElementById('liveClock').textContent = timeString;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDateString').textContent = now.toLocaleDateString('vi-VN', options);
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Auto reload page every 15 seconds to fetch new check-in queue
        setTimeout(() => {
            window.location.reload();
        }, 15000);

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</body>
</html>
