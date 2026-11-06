<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientControlledAnalgesia extends Model
{
    use HasFactory;

    protected $table = 'patient_controlled_analgesias';

    protected $fillable = [
        'patient_id',
        'anesthesiologist_id',
        'opioid_agent',
        'basal_infusion_rate_mg_or_mcg_per_h',
        'pca_bolus_dose_mg_or_mcg',
        'lockout_interval_minutes',
        'four_hour_dose_limit',
        'total_demands_count',
        'delivered_bolus_count',
        'etco2_respiratory_monitoring_mmhg',
        'spo2_monitoring_pct',
        'respiratory_depression_lockout_active',
        'pain_score_vas_10',
    ];

    protected $casts = [
        'basal_infusion_rate_mg_or_mcg_per_h' => 'float',
        'pca_bolus_dose_mg_or_mcg' => 'float',
        'four_hour_dose_limit' => 'float',
        'etco2_respiratory_monitoring_mmhg' => 'float',
        'spo2_monitoring_pct' => 'float',
        'lockout_interval_minutes' => 'integer',
        'total_demands_count' => 'integer',
        'delivered_bolus_count' => 'integer',
        'respiratory_depression_lockout_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function anesthesiologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'anesthesiologist_id');
    }

    /**
     * Đánh giá mức độ an toàn hô hấp khi bệnh nhân dùng bơm giảm đau PCA:
     * - EtCO2 > 50 mmHg hoặc SpO2 < 92% -> Cảnh báo ức chế hô hấp và khóa bơm PCA
     * - Tỷ lệ bấm nút (Demands / Delivered) phản ánh mức độ đau thực tế
     */
    public static function evaluatePcaSafety(float $etco2, float $spo2, int $demands, int $delivered): array
    {
        $depression = ($etco2 >= 50.0 || $spo2 < 92.0);
        $painControlled = ($demands <= $delivered * 1.5);

        return [
            'respiratory_depression_alert' => $depression,
            'lock_pump' => $depression,
            'pain_controlled' => $painControlled,
            'summary' => $depression 
                ? "BÁO ĐỘNG SUY HÔ HẤP DO OPIOID: EtCO2 đạt {$etco2} mmHg, SpO2 {$spo2}%. Tự động khóa bơm PCA và kích hoạt Naloxone cấp cứu!" 
                : "Hô hấp bệnh nhân an toàn (EtCO2: {$etco2} mmHg, SpO2: {$spo2}%). Hiệu quả giảm đau đạt mục tiêu.",
        ];
    }
}
