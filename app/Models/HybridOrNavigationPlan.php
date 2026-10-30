<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HybridOrNavigationPlan extends Model
{
    use HasFactory;

    protected $table = 'hybrid_or_navigation_plans';

    protected $fillable = [
        'patient_id',
        'primary_operator_id',
        'procedure_type',
        'navigation_modality',
        'target_registration_error_mm',
        'contrast_media_volume_ml',
        'patient_baseline_egfr',
        'contrast_induced_nephropathy_risk',
        'fluoroscopy_time_minutes',
        'radiation_dose_dap_mgycm2',
        'procedural_success',
        'complications_encountered',
    ];

    protected $casts = [
        'target_registration_error_mm' => 'float',
        'contrast_media_volume_ml' => 'float',
        'patient_baseline_egfr' => 'float',
        'fluoroscopy_time_minutes' => 'float',
        'radiation_dose_dap_mgycm2' => 'float',
        'procedural_success' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function primaryOperator(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'primary_operator_id');
    }

    /**
     * Đánh giá độ chính xác sai số định vị Target Registration Error (TRE <= 1.0mm)
     */
    public static function assessTreAccuracy(float $treMm): array
    {
        $isAccurate = ($treMm <= 1.0);
        return [
            'accurate' => $isAccurate,
            'interpretation' => $isAccurate 
                ? "Sai số đăng ký hình ảnh xuất sắc ({$treMm} mm <= 1.0 mm), an toàn tuyệt đối cho can thiệp mạch máu não / cột sống."
                : "Sai số đăng ký cảnh báo ({$treMm} mm > 1.0 mm), cần hiệu chuẩn lại (re-calibrate) camera quang học.",
        ];
    }

    /**
     * Đánh giá rủi ro suy thận cấp do thuốc cản quang (Contrast-Induced Nephropathy - CIN)
     */
    public static function calculateCinRisk(float $contrastVolumeMl, ?float $baselineEgfr): array
    {
        $risk = 'low_cin_risk';
        $warning = 'Nguy cơ CIN thấp, bù dịch thông thường.';

        if ($baselineEgfr !== null && $baselineEgfr < 45.0) {
            if ($contrastVolumeMl > 100.0) {
                $risk = 'high_cin_risk';
                $warning = 'NGUY CƠ SUY THẬN CIN CAO: eGFR < 45 kèm lượng cản quang > 100mL. Cần truyền NaCl 0.9% tốc độ 1mL/kg/h 12h trước và sau thủ thuật.';
            } else {
                $risk = 'moderate_cin_risk';
                $warning = 'Nguy cơ CIN trung bình, cần hạn chế tối đa thuốc cản quang.';
            }
        } elseif ($contrastVolumeMl > 200.0) {
            $risk = 'moderate_cin_risk';
            $warning = 'Liều cản quang lớn (>200mL), tăng cường theo dõi chức năng thận sau 48h.';
        }

        return [
            'risk_level' => $risk,
            'warning' => $warning,
        ];
    }
}
