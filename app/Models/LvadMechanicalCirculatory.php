<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LvadMechanicalCirculatory extends Model
{
    use HasFactory;

    protected $table = 'lvad_mechanical_circulatories';

    protected $fillable = [
        'patient_id',
        'cardiologist_id',
        'lvad_serial_number',
        'pump_speed_rpm',
        'pump_flow_l_per_min',
        'pump_power_watts',
        'pulsatility_index_pi',
        'pump_thrombosis_suspected',
        'suction_event_detected',
        'driveline_infection_alert',
        'inr_anticoagulation_target',
        'lvad_diagnostic_summary',
    ];

    protected $casts = [
        'pump_speed_rpm' => 'integer',
        'pump_flow_l_per_min' => 'float',
        'pump_power_watts' => 'float',
        'pulsatility_index_pi' => 'float',
        'inr_anticoagulation_target' => 'float',
        'pump_thrombosis_suspected' => 'boolean',
        'suction_event_detected' => 'boolean',
        'driveline_infection_alert' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function cardiologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'cardiologist_id');
    }

    /**
     * Phân tích 4 thông số vận hành của thiết bị hỗ trợ thất trái LVAD (HeartMate 3):
     * - Tắc huyết khối bơm: Công suất (Power) và Lưu lượng (Flow) tăng cao bất thường
     * - Dính hút buồng tim (Suction): PI tụt dốc và Flow tụt đột ngột
     */
    public static function analyzeLvadTelemetry(float $flow, float $power, float $pi): array
    {
        $thrombosis = ($power >= 8.0 && $flow >= 6.5);
        $suction = ($pi <= 1.5 || $flow < 2.5);

        $status = 'normal_functioning';
        if ($thrombosis) {
            $status = 'pump_thrombosis_alert';
        } elseif ($suction) {
            $status = 'suction_event_alert';
        }

        return [
            'thrombosis_risk' => $thrombosis,
            'suction_event' => $suction,
            'status' => $status,
            'summary' => match ($status) {
                'pump_thrombosis_alert' => "CẢNH BÁO NGUY CƠ HUYẾT KHỐI CÁNH BƠM LVAD: Công suất đạt {$power} W, Lưu lượng {$flow} L/min tăng vọt. Cần xét nghiệm LDH và truyền Heparin khẩn!",
                'suction_event_alert' => "CẢNH BÁO HÚT DÍNH VÁCH TIM (SUCTION): Chỉ số PI {$pi} tụt thấp. Bệnh nhân thiếu thể tích tuần hoàn, cần bù dịch và giảm tốc độ vòng quay.",
                default => "Thiết bị LVAD vận hành hoàn hảo: Tốc độ bơm ổn định, công suất {$power} W, dòng chảy {$flow} L/min, PI: {$pi}.",
            },
        ];
    }
}
