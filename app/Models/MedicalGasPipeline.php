<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalGasPipeline extends Model
{
    use HasFactory;

    protected $table = 'medical_gas_pipelines';

    protected $fillable = [
        'zone_manifold_code',
        'building_floor',
        'biomedical_engineer_id',
        'oxygen_line_pressure_bar',
        'medical_air_pressure_bar',
        'surgical_air_pressure_bar',
        'vacuum_suction_pressure_bar',
        'nitrous_oxide_pressure_bar',
        'liquid_oxygen_tank_level_pct',
        'liquid_oxygen_remaining_liters',
        'active_ventilators_count',
        'estimated_hours_until_depletion',
        'low_pressure_emergency_alarm',
        'pipeline_leak_detected',
        'manifold_status_summary',
    ];

    protected $casts = [
        'oxygen_line_pressure_bar' => 'float',
        'medical_air_pressure_bar' => 'float',
        'surgical_air_pressure_bar' => 'float',
        'vacuum_suction_pressure_bar' => 'float',
        'nitrous_oxide_pressure_bar' => 'float',
        'liquid_oxygen_tank_level_pct' => 'float',
        'liquid_oxygen_remaining_liters' => 'float',
        'estimated_hours_until_depletion' => 'float',
        'active_ventilators_count' => 'integer',
        'low_pressure_emergency_alarm' => 'boolean',
        'pipeline_leak_detected' => 'boolean',
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'biomedical_engineer_id');
    }

    /**
     * Kiểm định độ an toàn của hệ thống khí y tế trung tâm (MGPS):
     * - Áp suất oxy chuẩn: 4.0 - 4.5 bar
     * - Hút chân không âm: -0.6 đến -0.8 bar
     * - Dự báo thời gian cạn bồn oxy lỏng (LOX)
     */
    public static function evaluateMedicalGasSafety(float $o2Pressure, float $vacuum, float $tankLiters, int $ventilators): array
    {
        $o2Safe = ($o2Pressure >= 3.8 && $o2Pressure <= 4.8);
        $vacuumSafe = ($vacuum <= -0.5);

        // Mỗi máy thở tiêu thụ trung bình ~15-20 L/phút khí oxy (khoảng 1.2 m3/giờ hay 1.5 L LOX/giờ)
        $hourlyConsumption = max(5.0, $ventilators * 1.5);
        $hoursRemaining = round($tankLiters / $hourlyConsumption, 1);

        $isEmergency = (!$o2Safe || $hoursRemaining < 24.0);

        return [
            'o2_pressure_safe' => $o2Safe,
            'vacuum_safe' => $vacuumSafe,
            'hours_remaining' => $hoursRemaining,
            'emergency_alert' => $isEmergency,
            'summary' => $isEmergency 
                ? "CẢNH BÁO KHÍ Y TẾ: Áp suất oxy {$o2Pressure} bar hoặc bồn LOX còn {$hoursRemaining} giờ cho {$ventilators} máy thở. Cần kích hoạt trạm nạp khẩn cấp!" 
                : "Hệ thống MGPS hoạt động an toàn: Áp lực O2 đạt {$o2Pressure} bar, dự trữ bồn lỏng đáp ứng đủ {$hoursRemaining} giờ vận hành liên tục.",
        ];
    }
}
