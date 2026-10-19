@extends('layouts.admin')

@section('title', 'Cấu hình Lịch trực: ' . $doctor->full_name_with_title)

@section('content')
<x-page-header title="Cấu hình Lịch trực Tuần" subtitle="Thiết lập ca khám sáng/chiều và giới hạn bệnh nhân cho {{ $doctor->full_name_with_title }}">
    <x-slot:actions>
        <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-badge"></i> Xem hồ sơ bác sĩ
        </a>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row justify-content-center">
    <div class="col-lg-11">
        <form method="POST" action="{{ route('admin.doctors.schedule.update', $doctor) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-calendar3-week text-primary me-2"></i> Lịch phân ca từ Thứ Hai đến Chủ Nhật
                        </h5>
                        <small class="text-muted">Các ca được kích hoạt sẽ xuất hiện để bệnh nhân lựa chọn khi đặt lịch khám trực tuyến.</small>
                    </div>
                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">
                        <i class="bi bi-geo-alt me-1"></i> {{ $doctor->room_number }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 140px;">Thứ trong tuần</th>
                                    <th style="width: 120px;">Ca trực</th>
                                    <th style="width: 130px;">Kích hoạt ca</th>
                                    <th>Giờ bắt đầu</th>
                                    <th>Giờ kết thúc</th>
                                    <th style="width: 180px;">Bệnh nhân tối đa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $dayKey => $dayName)
                                    @php
                                        $morningKey = "{$dayKey}_morning";
                                        $afternoonKey = "{$dayKey}_afternoon";
                                        $mSched = $schedules->get($morningKey);
                                        $aSched = $schedules->get($afternoonKey);
                                    @endphp

                                    <!-- Ca Sáng -->
                                    <tr>
                                        <td rowspan="2" class="fw-bold text-center bg-light text-dark">
                                            {{ $dayName }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">
                                                <i class="bi bi-sun me-1"></i> Sáng
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="schedules[{{ $morningKey }}][is_active]" value="1"
                                                    {{ ($mSched && $mSched->is_active) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm text-center"
                                                name="schedules[{{ $morningKey }}][start_time]"
                                                value="{{ $mSched ? substr($mSched->start_time, 0, 5) : '07:30' }}">
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm text-center"
                                                name="schedules[{{ $morningKey }}][end_time]"
                                                value="{{ $mSched ? substr($mSched->end_time, 0, 5) : '11:30' }}">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="1" max="100" class="form-control text-center"
                                                    name="schedules[{{ $morningKey }}][max_patients]"
                                                    value="{{ $mSched ? $mSched->max_patients : 15 }}">
                                                <span class="input-group-text">lượt</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Ca Chiều -->
                                    <tr class="border-bottom-2">
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1">
                                                <i class="bi bi-sunset me-1"></i> Chiều
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="schedules[{{ $afternoonKey }}][is_active]" value="1"
                                                    {{ ($aSched && $aSched->is_active) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm text-center"
                                                name="schedules[{{ $afternoonKey }}][start_time]"
                                                value="{{ $aSched ? substr($aSched->start_time, 0, 5) : '13:30' }}">
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm text-center"
                                                name="schedules[{{ $afternoonKey }}][end_time]"
                                                value="{{ $aSched ? substr($aSched->end_time, 0, 5) : '17:00' }}">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="1" max="100" class="form-control text-center"
                                                    name="schedules[{{ $afternoonKey }}][max_patients]"
                                                    value="{{ $aSched ? $aSched->max_patients : 15 }}">
                                                <span class="input-group-text">lượt</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Giờ khám mặc định: Ca Sáng 07:30 - 11:30 | Ca Chiều 13:30 - 17:00.
                    </span>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="bi bi-save"></i> Lưu cấu hình lịch trực
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
