<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrrtCitrateAnticoagulation extends Model
{
    use HasFactory;

    protected $table = 'crrt_citrate_anticoagulations';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'crrt_modality',
        'effluent_dose_ml_per_kg_per_h',
        'transmembrane_pressure_tmp_mmhg',
        'filter_clotting_risk_pct',
        'pre_filter_citrate_infusion_mmol_per_l',
        'post_filter_ionized_calcium_mmol_per_l',
        'systemic_ionized_calcium_mmol_per_l',
        'calcium_chloride_compensation_rate_ml_per_h',
        'citrate_accumulation_acidosis_alert',
    ];

    protected $casts = [
        'effluent_dose_ml_per_kg_per_h' => 'float',
        'transmembrane_pressure_tmp_mmhg' => 'float',
        'filter_clotting_risk_pct' => 'float',
        'pre_filter_citrate_infusion_mmol_per_l' => 'float',
        'post_filter_ionized_calcium_mmol_per_l' => 'float',
        'systemic_ionized_calcium_mmol_per_l' => 'float',
        'calcium_chloride_compensation_rate_ml_per_h' => 'float',
        'citrate_accumulation_acidosis_alert' => 'boolean',
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
     * Giám sát an toàn chống đông Citrate cục bộ tại màng lọc (Regional Citrate Anticoagulation - RCA):
     * - Canxi ion hóa sau màng (Post-filter iCa): Mục tiêu 0.25 - 0.35 mmol/L (chống đông tối ưu)
     * - Canxi ion hóa toàn thân người bệnh: Mục tiêu 1.10 - 1.30 mmol/L (bù đủ Canxi Clorid)
     * - Nguy cơ tích lũy Citrate (Citrate toxicity): Tỷ lệ Canxi toàn phần / Canxi ion hóa > 2.5
     */
    public static function evaluateCitrateSafety(float $postFilterIca, float $systemicIca, float $tmp): array
    {
        $anticoagOk = ($postFilterIca >= 0.20 && $postFilterIca <= 0.38);
        $systemicSafe = ($systemicIca >= 1.05 && $systemicIca <= 1.35);
        $tmpHigh = ($tmp > 200.0);

        $clottingRisk = $tmpHigh ? 80.0 : ($postFilterIca > 0.40 ? 45.0 : 15.0);

        return [
            'safe' => ($anticoagOk && $systemicSafe && !$tmpHigh),
            'clotting_risk_pct' => $clottingRisk,
            'advice' => $anticoagOk 
                ? 'Canxi ion sau màng đạt chuẩn (0.25 - 0.35 mmol/L). Hiệu quả chống đông màng lọc tối ưu không gây chảy máu toàn thân.' 
                : ($postFilterIca > 0.38 ? 'Canxi sau màng còn cao: Tăng tốc độ bơm dung dịch Citrate để tránh tắc quả lọc.' : 'Canxi sau màng quá thấp: Giảm liều Citrate.'),
        ];
    }
}
