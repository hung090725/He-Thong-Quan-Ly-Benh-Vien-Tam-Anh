<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IcuAbcdefBundlePics extends Model
{
    use HasFactory;

    protected $table = 'icu_abcdef_bundle_pics';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'bundle_a_pain_assessed_controlled',
        'bundle_b_both_sat_sbt_performed',
        'bundle_c_sedative_choice',
        'bundle_d_cam_icu_delirium',
        'bundle_e_early_mobility_level',
        'bundle_f_family_engagement_diary_active',
        'pics_post_intensive_care_risk_score',
        'multidisciplinary_icu_notes',
    ];

    protected $casts = [
        'bundle_a_pain_assessed_controlled' => 'boolean',
        'bundle_b_both_sat_sbt_performed' => 'boolean',
        'bundle_f_family_engagement_diary_active' => 'boolean',
        'pics_post_intensive_care_risk_score' => 'float',
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
     * Đánh giá tuân thủ gói can thiệp ICU ABCDEF Bundle và dự báo nguy cơ hội chứng PICS (Post-Intensive Care Syndrome):
     * Tuân thủ đầy đủ 6 yếu tố A-B-C-D-E-F giúp giảm 50% tỷ lệ tử vong và giảm 40% nguy cơ suy yếu tâm thần vận động PICS.
     */
    public static function evaluateBundleCompliance(bool $a, bool $b, string $sedative, string $camIcu, string $mobility, bool $f): array
    {
        $compliantCount = 0;
        if ($a) $compliantCount++;
        if ($b) $compliantCount++;
        if (strtolower($sedative) !== 'midazolam' && strtolower($sedative) !== 'diazepam') $compliantCount++;
        if ($camIcu === 'negative_clear') $compliantCount++;
        if ($mobility !== 'level_1_passive_bed') $compliantCount++;
        if ($f) $compliantCount++;

        // Tính điểm nguy cơ PICS (Càng nhiều mục tuân thủ thì nguy cơ PICS càng thấp)
        $picsRisk = max(10.0, round(100.0 - ($compliantCount * 15.0), 1));

        return [
            'compliant_elements' => "{$compliantCount}/6",
            'pics_risk_pct' => $picsRisk,
            'summary' => $compliantCount >= 5 
                ? 'Tuân thủ gói ICU ABCDEF xuất sắc! Người bệnh được kiểm soát đau, thức tỉnh sớm và vận động tích cực, giảm tối đa biến chứng suy yếu sau hồi sức PICS.' 
                : 'Cần tăng cường ngắt an thần SAT và kích hoạt vận động sớm tại giường để phòng ngừa mê sảng ICU.',
        ];
    }
}
