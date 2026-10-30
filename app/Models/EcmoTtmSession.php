<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcmoTtmSession extends Model
{
    use HasFactory;

    protected $table = 'ecmo_ttm_sessions';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'ecmo_modality',
        'pump_flow_l_per_min',
        'pump_speed_rpm',
        'sweep_gas_flow_l_per_min',
        'fio2_sweep_pct',
        'delta_pressure_membrane_mmhg',
        'membrane_thrombosis_risk',
        'ttm_targeted_temperature_active',
        'target_core_temp_c',
        'actual_core_temp_c',
        'session_status',
    ];

    protected $casts = [
        'pump_flow_l_per_min' => 'float',
        'pump_speed_rpm' => 'integer',
        'sweep_gas_flow_l_per_min' => 'float',
        'fio2_sweep_pct' => 'float',
        'delta_pressure_membrane_mmhg' => 'float',
        'membrane_thrombosis_risk' => 'boolean',
        'ttm_targeted_temperature_active' => 'boolean',
        'target_core_temp_c' => 'float',
        'actual_core_temp_c' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Giám sát chênh áp màng trao đổi oxy (Delta P) và nguy cơ đông màng ECMO
     * Chuẩn Delta P bình thường: 20 - 50 mmHg; Delta P > 60 mmHg hoặc tăng > 50% trong 24h là dấu hiệu huyết khối màng.
     */
    public static function evaluateMembraneSafety(float $deltaP): array
    {
        $thrombosisAlert = ($deltaP > 60.0);
        return [
            'thrombosis_risk' => $thrombosisAlert,
            'status' => $thrombosisAlert ? 'CẢNH BÁO ĐÔNG MÀNG OXY HÓA: Delta P tăng cao (' . $deltaP . ' mmHg > 60 mmHg). Chuẩn bị sẵn bộ màng ECMO dự phòng để thay thế khẩn!' : 'Chênh áp màng trong ngưỡng an toàn sinh lý.',
        ];
    }

    /**
     * Kiểm tra hạ thân nhiệt chỉ huy TTM đạt đích (33°C - 36°C)
     */
    public static function checkTtmCompliance(float $actualTemp, float $targetTemp): bool
    {
        return abs($actualTemp - $targetTemp) <= 0.5;
    }
}
