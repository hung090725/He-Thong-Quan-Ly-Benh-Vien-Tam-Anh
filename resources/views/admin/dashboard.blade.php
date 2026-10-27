@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bảng Điều Khiển Quản Trị" subtitle="Tổng quan hệ thống tiếp nhận, điều phối và khám chữa bệnh ngoại trú" />

<!-- KPI Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Tổng Lịch Khám Hôm Nay</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_today'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-calendar2-day fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>{{ $stats['appointments_pending'] }} ca đang chờ tiếp nhận</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-info text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Check-in (Chờ khám)</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_confirmed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Đang trong hàng đợi các phòng khám</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Khám Hoàn Tất</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_completed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Đã lập bệnh án & kê đơn thuốc</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-dark text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đội Ngũ Bác Sĩ</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['total_doctors'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-heart-pulse-fill fs-3 text-danger"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Thuộc {{ $stats['total_specialties'] }} chuyên khoa y tế</span>
            </div>
        </div>
    </div>
</div>

<!-- Financial & Efficiency KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-success p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Doanh Thu Viện Phí Hôm Nay</div>
                    <h3 class="fw-bold text-success mb-0 mt-1 font-monospace">{{ number_format($todayRevenue, 0, ',', '.') }} đ</h3>
                    <small class="text-muted">Tính trên các ca đã Check-in & Hoàn tất</small>
                </div>
                <div class="rounded-circle bg-success-subtle p-3 text-success">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-primary p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Doanh Thu Viện Phí Tháng {{ date('m/Y') }}</div>
                    <h3 class="fw-bold text-primary mb-0 mt-1 font-monospace">{{ number_format($monthRevenue, 0, ',', '.') }} đ</h3>
                    <small class="text-muted">Tổng tích lũy ngoại trú trong tháng</small>
                </div>
                <div class="rounded-circle bg-primary-subtle p-3 text-primary">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-warning p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Tỷ Lệ Hoàn Tất Khám Hôm Nay</div>
                    <h3 class="fw-bold text-dark mb-0 mt-1 font-monospace">{{ $completionRate }}%</h3>
                </div>
                <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                    <i class="bi bi-speedometer2 fs-4"></i>
                </div>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $completionRate }}%;"></div>
            </div>
            <small class="text-muted mt-1 d-block">{{ $stats['appointments_completed'] }}/{{ $stats['appointments_today'] }} ca khám xong</small>
        </div>
    </div>
</div>

<!-- Interactive Charts Section (Chart.js) -->
<div class="row g-4 mb-4">
    <!-- 7-Day Trend Chart -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up text-primary"></i> Xu Hướng Tiếp Nhận Bệnh Nhân (7 Ngày Gần Nhất)
                </h6>
                <span class="badge bg-light text-dark border small">Ca Sáng vs Ca Chiều</span>
            </div>
            <div class="card-body p-3">
                <canvas id="trendChart" style="max-height: 290px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- Specialty Distribution Doughnut Chart -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-info"></i> Phân Bổ Theo Chuyên Khoa
                </h6>
            </div>
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                <div style="width: 100%; max-width: 250px; margin: 0 auto;">
                    <canvas id="specialtyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Link Shortcuts -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.users.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-primary p-2">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Quản lý Tài khoản</div>
                    <small class="text-muted">{{ $stats['total_users'] }} người dùng</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reception.pharmacy.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-success p-2">
                    <i class="bi bi-capsule-pill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Quầy Dược Phát Thuốc</div>
                    <small class="text-muted">Cấp phát & In nhãn túi</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reception.appointments.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-info p-2">
                    <i class="bi bi-clipboard2-check-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Bàn tiếp nhận</div>
                    <small class="text-muted">Check-in tại quầy</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('booking.lookup') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-warning p-2">
                    <i class="bi bi-search fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Tra cứu lịch hẹn</div>
                    <small class="text-muted">Kiểm tra mã AP</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Appointments Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold mb-0 text-dark">Các Lịch Hẹn Đặt Gần Đây</h5>
        <a href="{{ route('reception.appointments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
            Xem toàn bộ lịch hẹn <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mã lịch hẹn</th>
                        <th>Bệnh nhân</th>
                        <th>Chuyên khoa</th>
                        <th>Bác sĩ</th>
                        <th>Ngày khám</th>
                        <th>Ca khám</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAppointments as $app)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-primary">
                                {{ $app->appointment_code }}
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $app->patientProfile->full_name }}</div>
                                <small class="text-muted">{{ $app->patientProfile->phone }}</small>
                            </td>
                            <td>{{ $app->specialty->name }}</td>
                            <td>
                                <div>{{ $app->doctor->full_name_with_title }}</div>
                                <small class="text-muted">Phòng {{ $app->doctor->room_number }}</small>
                            </td>
                            <td>{{ $app->appointment_date->format('d/m/Y') }}</td>
                            <td>{{ $app->shift_name }}</td>
                            <td>
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('booking.lookup', ['keyword' => $app->appointment_code]) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Chưa có lịch hẹn nào được ghi nhận.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. 7-Day Trend Line/Bar Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [
                        {
                            label: 'Ca Sáng (07:30 - 11:30)',
                            data: @json($trendMorning),
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0284c7',
                            pointRadius: 4
                        },
                        {
                            label: 'Ca Chiều (13:30 - 17:00)',
                            data: @json($trendAfternoon),
                            borderColor: '#0d9488',
                            backgroundColor: 'rgba(13, 148, 136, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0d9488',
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 12 },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            padding: 10,
                            boxPadding: 4
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // 2. Specialty Distribution Doughnut Chart
        const specialtyCtx = document.getElementById('specialtyChart');
        if (specialtyCtx) {
            new Chart(specialtyCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($specialtyLabels),
                    datasets: [{
                        data: @json($specialtyCounts),
                        backgroundColor: [
                            '#0284c7', // Primary Blue
                            '#0d9488', // Teal
                            '#f59e0b', // Amber
                            '#ec4899', // Pink
                            '#8b5cf6', // Purple
                            '#64748b'  // Slate
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 11 },
                                boxWidth: 12,
                                padding: 12
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endpush
@endsection
