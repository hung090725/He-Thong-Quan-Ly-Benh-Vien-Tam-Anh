<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản trị Hệ thống') - Bệnh Viện Tâm Anh</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --tam-primary: #0284c7;
            --tam-primary-dark: #0369a1;
            --tam-accent: #0d9488;
            --tam-sidebar-bg: #0f172a;
            --tam-sidebar-hover: #1e293b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Fix pagination SVG icon sizes */
        .pagination svg, svg.w-5 {
            width: 1rem !important;
            height: 1rem !important;
            display: inline-block !important;
        }

        .sidebar {
            width: 260px;
            background-color: var(--tam-sidebar-bg);
            min-height: 100vh;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--tam-sidebar-hover);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--tam-primary);
        }

        .sidebar-heading {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 0.75rem 1rem 0.25rem 1rem;
            font-weight: 700;
        }

        .main-content {
            flex-grow: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                bottom: 0;
            }
            .sidebar.show {
                left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <aside class="sidebar p-3 d-flex flex-column" id="adminSidebar">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 mb-4 text-decoration-none px-2 text-white">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                <i class="bi bi-hospital fs-5"></i>
            </div>
            <div>
                <span class="fw-bold d-block lh-1 text-white" style="font-size: 1.1rem;">BV TÂM ANH</span>
                <small class="text-secondary" style="font-size: 0.65rem; text-transform: uppercase;">Medical Management</small>
            </div>
        </a>

        <div class="flex-grow-1 overflow-y-auto">
            <ul class="nav flex-column">

                @if(Auth::check() && Auth::user()->isAdmin())
                    <li class="sidebar-heading">Bảng Quản trị (Admin)</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Quản lý Người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/specialties*') ? 'active' : '' }}" href="{{ url('/admin/specialties') }}">
                            <i class="bi bi-diagram-3"></i> Quản lý Chuyên khoa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/doctors*') ? 'active' : '' }}" href="{{ url('/admin/doctors') }}">
                            <i class="bi bi-person-badge"></i> Bác sĩ & Lịch trực
                        </a>
                    </li>
                @endif

                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isReceptionist()))
                    <li class="sidebar-heading">Nghiệp vụ Tiếp nhận (Lễ tân)</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/appointments*') ? 'active' : '' }}" href="{{ route('reception.appointments.index') }}">
                            <i class="bi bi-calendar-check"></i> Tiếp nhận & Check-in
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/patients*') ? 'active' : '' }}" href="{{ url('/reception/patients') }}">
                            <i class="bi bi-person-vcard"></i> Quản lý Bệnh nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/pharmacy') || (request()->is('reception/pharmacy*') && !request()->is('reception/pharmacy/inventory*')) ? 'active' : '' }}" href="{{ route('reception.pharmacy.index') }}">
                            <i class="bi bi-capsule-pill"></i> Quầy Dược Phát Thuốc
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/pharmacy/inventory*') ? 'active' : '' }}" href="{{ route('reception.pharmacy.inventory.index') }}">
                            <i class="bi bi-box-seam"></i> Kho Dược & Tồn Kho
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/certificates*') ? 'active' : '' }}" href="{{ route('reception.certificates.index') }}">
                            <i class="bi bi-journal-medical"></i> Sổ Giấy Nghỉ BHXH
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/consents*') ? 'active' : '' }}" href="{{ route('reception.consents.index') }}">
                            <i class="bi bi-file-earmark-check"></i> Bản Cam Đoan Ký Số
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/invoices*') ? 'active' : '' }}" href="{{ route('reception.invoices.index') }}">
                            <i class="bi bi-wallet2"></i> Hóa Đơn & Thu Phí VietQR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/day-beds*') ? 'active' : '' }}" href="{{ route('reception.day-beds.index') }}">
                            <i class="bi bi-hospital"></i> Giường Ngoại Trú & Lưu Ngày
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/vaccinations*') ? 'active' : '' }}" href="{{ route('reception.vaccinations.index') }}">
                            <i class="bi bi-shield-check"></i> Tiêm Chủng & Sổ Vắc-Xin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/code-red*') ? 'active' : '' }}" href="{{ route('reception.code-red.index') }}">
                            <i class="bi bi-bell-fill text-danger"></i> Báo Động Đỏ Cấp Cứu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/deliveries*') ? 'active' : '' }}" href="{{ route('reception.deliveries.index') }}">
                            <i class="bi bi-box2-heart"></i> Giao Thuốc Tận Nhà
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/surveys*') ? 'active' : '' }}" href="{{ route('reception.surveys.index') }}">
                            <i class="bi bi-star-half"></i> Khảo Sát Hài Lòng BYT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/notifications*') ? 'active' : '' }}" href="{{ route('reception.notifications.index') }}">
                            <i class="bi bi-chat-quote"></i> Thông Báo ZNS & SMS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reception/analytics*') ? 'active' : '' }}" href="{{ route('reception.analytics.index') }}">
                            <i class="bi bi-graph-up-arrow"></i> Giám Sát Hiệu Suất (BI)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('consultations*') ? 'active' : '' }}" href="{{ route('consultations.index') }}">
                            <i class="bi bi-people-fill"></i> Hội Chẩn Đa Chuyên Khoa
                        </a>
                    </li>
                @endif

                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDoctor()))
                    <li class="sidebar-heading">Bàn khám Bác sĩ</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('doctor/queue*') ? 'active' : '' }}" href="{{ route('doctor.queue') }}">
                            <i class="bi bi-heart-pulse"></i> Hàng đợi khám hôm nay
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('consultations*') ? 'active' : '' }}" href="{{ route('consultations.index') }}">
                            <i class="bi bi-people-fill"></i> Hội Chẩn Đa Chuyên Khoa
                        </a>
                    </li>
                @endif

                @if(Auth::check() && Auth::user()->isPatient())
                    <li class="sidebar-heading">Cổng thông tin Bệnh nhân</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('patient/history*') ? 'active' : '' }}" href="{{ route('patient.history') }}">
                            <i class="bi bi-journal-medical"></i> Lịch sử khám bệnh
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('booking') ? 'active' : '' }}" href="{{ route('booking.create') }}">
                            <i class="bi bi-calendar-plus"></i> Đặt lịch khám mới
                        </a>
                    </li>
                @endif

                <li class="sidebar-heading">Cài đặt cá nhân</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('profile/password*') ? 'active' : '' }}" href="{{ route('profile.password') }}">
                        <i class="bi bi-shield-lock"></i> Đổi mật khẩu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> Xem Trang chủ Web
                    </a>
                </li>
            </ul>
        </div>

        <div class="pt-3 border-top border-secondary border-opacity-25 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggleBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="fw-semibold text-muted small d-none d-sm-block">
                    HỆ THỐNG QUẢN LÝ BỆNH VIỆN ĐA KHOA TÂM ANH
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('booking.create') }}" class="btn btn-sm btn-primary d-none d-md-inline-flex align-items-center gap-1 rounded-pill">
                    <i class="bi bi-plus-circle"></i> Đặt lịch hẹn
                </a>

                <div class="dropdown">
                    <button class="btn btn-light bg-white border dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill" type="button" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5 text-primary"></i>
                        <span class="fw-medium text-dark">{{ Auth::user()->name ?? 'Người dùng' }}</span>
                        @if(Auth::check())
                            <span class="badge {{ Auth::user()->role_badge_class }} rounded-pill" style="font-size: 0.65rem;">
                                {{ Auth::user()->role_name }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="adminUserDropdown">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold">{{ Auth::user()->name ?? '' }}</div>
                            <div class="small text-muted">{{ Auth::user()->email ?? '' }}</div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.password') }}">
                                <i class="bi bi-shield-lock me-2 text-warning"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
            <x-alert />
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-muted small text-center text-md-start d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>Bệnh Viện Đa Khoa Tâm Anh</strong> - Hệ thống quản lý khám chữa bệnh ngoại trú.
            </div>
            <div>
                Phiên bản đồ án chuẩn hóa MVC (PHP 8.2 + Laravel 12)
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebar = document.getElementById('adminSidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
