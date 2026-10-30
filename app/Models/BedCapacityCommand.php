<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedCapacityCommand extends Model
{
    use HasFactory;

    protected $table = 'bed_capacity_commands';

    protected $fillable = [
        'department_name',
        'total_licensed_beds',
        'occupied_beds',
        'pending_admissions_er',
        'confirmed_discharges_today',
        'beds_in_sanitization',
        'bed_occupancy_rate_pct',
        'predicted_available_beds_afternoon',
        'capacity_alert_status',
        'discharge_lounge_active',
    ];

    protected $casts = [
        'total_licensed_beds' => 'integer',
        'occupied_beds' => 'integer',
        'pending_admissions_er' => 'integer',
        'confirmed_discharges_today' => 'integer',
        'beds_in_sanitization' => 'integer',
        'bed_occupancy_rate_pct' => 'float',
        'predicted_available_beds_afternoon' => 'integer',
        'discharge_lounge_active' => 'boolean',
    ];

    /**
     * Tính toán tỷ lệ lấp đầy giường và dự báo quá tải buổi chiều (Hospital Gridlock)
     */
    public static function calculateCapacityMetrics(
        int $total,
        int $occupied,
        int $erPending,
        int $discharges
    ): array {
        $occupancyRate = $total > 0 ? round(($occupied / $total) * 100, 1) : 0.0;
        // Giường trống hiện tại = total - occupied
        $currentEmpty = max(0, $total - $occupied);
        // Dự kiến buổi chiều có thêm giường xuất viện, trừ đi bệnh nhân cấp cứu chờ nhập
        $predictedAfternoonEmpty = max(0, $currentEmpty + $discharges - $erPending);

        $alertStatus = 'normal_green';
        if ($occupancyRate >= 95.0 || ($currentEmpty < $erPending && $predictedAfternoonEmpty < 2)) {
            $alertStatus = 'gridlock_code_purple';
        } elseif ($occupancyRate >= 85.0) {
            $alertStatus = 'busy_yellow';
        }

        return [
            'occupancy_rate' => $occupancyRate,
            'predicted_empty' => $predictedAfternoonEmpty,
            'alert_status' => $alertStatus,
        ];
    }
}
