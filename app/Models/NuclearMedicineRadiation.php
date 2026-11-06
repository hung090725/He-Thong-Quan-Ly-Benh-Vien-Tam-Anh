<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NuclearMedicineRadiation extends Model
{
    use HasFactory;

    protected $table = 'nuclear_medicine_radiations';

    protected $fillable = [
        'patient_id',
        'radiation_safety_officer_id',
        'radiopharmaceutical',
        'injected_activity_mbq',
        'injection_timestamp',
        'isotope_half_life_minutes',
        'decayed_residual_activity_mbq',
        'staff_personal_dosimeter_rate_usv_h',
        'patient_surface_radiation_rate_usv_h',
        'safe_for_discharge_home',
        'radioprotection_instructions',
    ];

    protected $casts = [
        'injected_activity_mbq' => 'float',
        'isotope_half_life_minutes' => 'float',
        'decayed_residual_activity_mbq' => 'float',
        'staff_personal_dosimeter_rate_usv_h' => 'float',
        'patient_surface_radiation_rate_usv_h' => 'float',
        'injection_timestamp' => 'datetime',
        'safe_for_discharge_home' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function safetyOfficer(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'radiation_safety_officer_id');
    }

    /**
     * Tính toán quy luật phân rã phóng xạ A(t) = A0 * e^(-lambda * t) và tiêu chuẩn xuất viện an toàn:
     * - Liều bức xạ đo tại khoảng cách 1 mét < 20 uSv/h theo khuyến nghị IAEA mới được cho xuất viện
     */
    public static function calculateRadiationDecayAndDischarge(float $initialMbq, float $halfLifeMin, float $elapsedMin, float $surfaceRateUsv): array
    {
        // lambda = ln(2) / half_life
        $lambda = log(2) / max(1.0, $halfLifeMin);
        $residualActivity = round($initialMbq * exp(-$lambda * $elapsedMin), 1);

        $safeDischarge = ($surfaceRateUsv < 20.0);

        return [
            'residual_activity_mbq' => $residualActivity,
            'safe_for_discharge' => $safeDischarge,
            'summary' => $safeDischarge 
                ? "Bức xạ đã phân rã an toàn: Hoạt độ còn {$residualActivity} MBq, suất liều bề mặt {$surfaceRateUsv} uSv/h (< 20 uSv/h). Đủ điều kiện xuất viện an toàn về nhà." 
                : "CHƯA ĐƯỢC XUẤT VIỆN: Suất liều bề mặt {$surfaceRateUsv} uSv/h vượt ngưỡng an toàn cộng đồng. Lưu bệnh nhân tại phòng lưu cách ly phóng xạ.",
        ];
    }
}
