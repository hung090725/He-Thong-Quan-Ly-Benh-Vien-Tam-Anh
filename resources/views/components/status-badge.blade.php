@props(['status'])

@php
    $badgeClass = match ($status) {
        'waitlist' => 'bg-warning-subtle text-warning-emphasis border border-warning',
        'pending' => 'bg-warning text-dark',
        'confirmed' => 'bg-primary text-white',
        'in_progress' => 'bg-info text-white',
        'completed' => 'bg-success text-white',
        'cancelled' => 'bg-danger text-white',
        default => 'bg-secondary text-white',
    };

    $label = match ($status) {
        'waitlist' => 'Danh sách chờ (Waitlist)',
        'pending' => 'Chờ tiếp nhận',
        'confirmed' => 'Đã tiếp nhận (Chờ khám)',
        'in_progress' => 'Đang khám',
        'completed' => 'Đã khám xong',
        'cancelled' => 'Đã hủy',
        default => $status,
    };

    $icon = match ($status) {
        'waitlist' => 'bi-hourglass-top',
        'pending' => 'bi-hourglass-split',
        'confirmed' => 'bi-calendar-check',
        'in_progress' => 'bi-activity',
        'completed' => 'bi-check-circle-fill',
        'cancelled' => 'bi-x-circle-fill',
        default => 'bi-info-circle',
    };
@endphp

<span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
    <i class="bi {{ $icon }}"></i>
    <span>{{ $label }}</span>
</span>
