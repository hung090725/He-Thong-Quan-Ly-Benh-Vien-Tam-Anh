<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiationOncologyPlan extends Model
{
    use HasFactory;

    protected $table = 'radiation_oncology_plans';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'plan_code',
        'cancer_site',
        'radiation_technique',
        'total_prescribed_dose_gy',
        'fraction_count',
        'dose_per_fraction_gy',
        'dibh_breath_hold_used',
        'four_d_ct_motion_tracked',
        'heart_mean_dose_gy',
        'spinal_cord_max_dose_gy',
        'oar_tolerance_verified',
        'treatment_status',
    ];

    protected $casts = [
        'total_prescribed_dose_gy' => 'float',
        'fraction_count' => 'integer',
        'dose_per_fraction_gy' => 'float',
        'dibh_breath_hold_used' => 'boolean',
        'four_d_ct_motion_tracked' => 'boolean',
        'heart_mean_dose_gy' => 'float',
        'spinal_cord_max_dose_gy' => 'float',
        'oar_tolerance_verified' => 'boolean',
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
     * Thẩm định dung nạp liều cơ quan lành (Organs At Risk - OAR):
     * - Liều trung bình thân tim (Heart Mean Dose) <= 4.0 Gy (khi xạ vú/phổi)
     * - Liều tối đa tủy sống (Spinal Cord Max Dose) <= 45.0 Gy (tránh viêm tủy do tia xạ)
     */
    public static function verifyOarTolerances(?float $heartMeanDose, ?float $spinalCordMaxDose): array
    {
        $heartSafe = ($heartMeanDose === null || $heartMeanDose <= 4.0);
        $cordSafe = ($spinalCordMaxDose === null || $spinalCordMaxDose <= 45.0);

        $passed = ($heartSafe && $cordSafe);

        return [
            'verified' => $passed,
            'summary' => $passed 
                ? 'Đạt tiêu chuẩn an toàn bảo vệ mô lành (OAR) theo quy chuẩn quốc tế QUANTEC.' 
                : 'CẢNH BÁO VƯỢT NGƯỠNG OAR: Liều tia chiếu vào Tim hoặc Tủy sống vượt quá giới hạn dung nạp sinh học. Cần tái lập kế hoạch tối ưu hóa góc chiếu!',
        ];
    }
}
