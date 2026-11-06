<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartInfusionPumpDers extends Model
{
    use HasFactory;

    protected $table = 'smart_infusion_pump_ders';

    protected $fillable = [
        'patient_id',
        'pump_device_code',
        'care_unit_profile',
        'medication_name',
        'concentration_unit',
        'dose_rate',
        'dose_unit',
        'patient_weight_kg',
        'lower_soft_limit',
        'upper_soft_limit',
        'upper_hard_limit',
        'infusion_rate_ml_hr',
        'soft_limit_exceeded',
        'hard_limit_violation',
        'override_authorized_by',
        'clinical_indication',
    ];

    protected $casts = [
        'dose_rate' => 'float',
        'patient_weight_kg' => 'float',
        'lower_soft_limit' => 'float',
        'upper_soft_limit' => 'float',
        'upper_hard_limit' => 'float',
        'infusion_rate_ml_hr' => 'float',
        'soft_limit_exceeded' => 'boolean',
        'hard_limit_violation' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Đánh giá liều qua hệ thống Guardrail DERS
     */
    public static function evaluateDersGuardrail(float $doseRate, ?float $upperSoftLimit, float $upperHardLimit): array
    {
        $hardViolation = ($doseRate > $upperHardLimit);
        $softExceeded = ($upperSoftLimit !== null && $doseRate > $upperSoftLimit && !$hardViolation);

        $message = 'Liều trong giới hạn an toàn';
        if ($hardViolation) {
            $message = 'VI PHẠM HARD LIMIT: Bơm tiêm điện tự động khóa chặn, cấm truyền để bảo vệ tính mạng người bệnh!';
        } elseif ($softExceeded) {
            $message = 'VƯỢT SOFT LIMIT: Cần điều dưỡng trưởng hoặc bác sĩ xác thực override trước khi bơm khởi chạy.';
        }

        return [
            'soft_limit_exceeded' => $softExceeded,
            'hard_limit_violation' => $hardViolation,
            'message' => $message,
        ];
    }

    /**
     * Tính toán tốc độ truyền mL/h
     */
    public static function calculateInfusionRateMlHr(float $doseRate, float $weightKg, float $concMgPerMl = 1.0): float
    {
        if ($concMgPerMl <= 0) {
            return 0.0;
        }
        // Giả sử đơn vị mcg/kg/min -> mg/h: doseRate * weightKg * 60 / 1000
        $mgPerHour = ($doseRate * $weightKg * 60.0) / 1000.0;
        return round($mgPerHour / $concMgPerMl, 2);
    }
}
