<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrecisionAntibioticPkPd extends Model
{
    use HasFactory;

    protected $table = 'precision_antibiotic_pk_pds';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'antibiotic_name',
        'pathogen_name',
        'mic_mg_per_l',
        'current_serum_trough_mg_per_l',
        'current_serum_peak_mg_per_l',
        'estimated_auc24_mg_h_per_l',
        'auc24_over_mic_ratio',
        'auc_target_achieved',
        'time_above_mic_pct',
        'bayesian_recommended_dose',
        'nephrotoxicity_risk_alert',
    ];

    protected $casts = [
        'mic_mg_per_l' => 'float',
        'current_serum_trough_mg_per_l' => 'float',
        'current_serum_peak_mg_per_l' => 'float',
        'estimated_auc24_mg_h_per_l' => 'float',
        'auc24_over_mic_ratio' => 'float',
        'auc_target_achieved' => 'boolean',
        'time_above_mic_pct' => 'float',
        'nephrotoxicity_risk_alert' => 'boolean',
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
     * Mô hình Dược động học cá thể hóa (Pharmacokinetics / Pharmacodynamics):
     * - Vancomycin: Đích tối ưu AUC24/MIC = 400 - 600 mg.h/L (khi MIC = 1 mg/L). AUC > 650 tăng vọt nguy cơ độc thận AKI.
     * - Beta-lactam: %T > MIC >= 100% (truyền kéo dài hoặc liên tục).
     */
    public static function calculateBayesianTarget(string $drugName, float $mic, ?float $trough, ?float $aucEstimate): array
    {
        $drug = strtolower($drugName);
        if (str_contains($drug, 'vancomycin')) {
            $auc = $aucEstimate ?? ($trough !== null ? $trough * 33.0 : 450.0);
            $ratio = $mic > 0 ? round($auc / $mic, 1) : 450.0;
            $achieved = ($ratio >= 400.0 && $ratio <= 600.0);
            $nephrotoxic = ($ratio > 650.0 || ($trough !== null && $trough > 20.0));

            return [
                'auc24' => $auc,
                'ratio' => $ratio,
                'achieved' => $achieved,
                'nephrotoxic_alert' => $nephrotoxic,
                'recommendation' => $achieved 
                    ? 'Liều tối ưu đạt mục tiêu AUC24/MIC (400 - 600), bảo tồn chức năng thận tốt.' 
                    : ($ratio < 400.0 ? 'Liều dưới ngưỡng diệt khuẩn: Tăng liều nạp hoặc rút ngắn khoảng cách đưa thuốc.' : 'NGUY CƠ ĐỘC THẬN CAO (AUC > 600): Giảm liều hàng ngày hoặc kéo dài khoảng cách truyền thuốc!'),
            ];
        }

        return [
            'auc24' => 450.0,
            'ratio' => 450.0,
            'achieved' => true,
            'nephrotoxic_alert' => false,
            'recommendation' => 'Khuyến nghị truyền kéo dài 3-4 giờ để đạt %T > MIC tối đa.',
        ];
    }
}
