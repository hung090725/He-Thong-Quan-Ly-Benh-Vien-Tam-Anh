<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodeBlueAclsTracker extends Model
{
    use HasFactory;

    protected $table = 'code_blue_acls_trackers';

    protected $fillable = [
        'patient_id',
        'code_team_leader_id',
        'event_location',
        'arrest_witnessed_at',
        'initial_rhythm',
        'cpr_total_duration_minutes',
        'chest_compression_fraction_pct',
        'defibrillation_shocks_count',
        'epinephrine_doses_given',
        'amiodarone_doses_given',
        'advanced_airway_placed',
        'etco2_during_cpr_mmhg',
        'rosc_achieved',
        'rosc_timestamp',
        'final_outcome',
        'resuscitation_timeline_notes',
    ];

    protected $casts = [
        'arrest_witnessed_at' => 'datetime',
        'rosc_timestamp' => 'datetime',
        'chest_compression_fraction_pct' => 'float',
        'etco2_during_cpr_mmhg' => 'float',
        'advanced_airway_placed' => 'boolean',
        'rosc_achieved' => 'boolean',
        'cpr_total_duration_minutes' => 'integer',
        'defibrillation_shocks_count' => 'integer',
        'epinephrine_doses_given' => 'integer',
        'amiodarone_doses_given' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'code_team_leader_id');
    }

    /**
     * Đánh giá chất lượng hồi sức tim phổi nâng cao ACLS theo AHA:
     * - Tỷ lệ ép tim (CCF) >= 80%
     * - EtCO2 > 20 mmHg báo hiệu khả năng tái lập tuần hoàn tự nhiên (ROSC) cao
     */
    public static function evaluateAclsResuscitationQuality(float $ccfPct, float $etco2, int $shocks, bool $rosc): array
    {
        $highQualityCpr = ($ccfPct >= 80.0);
        $perfusionAdequate = ($etco2 >= 15.0);

        return [
            'high_quality_cpr' => $highQualityCpr,
            'perfusion_adequate' => $perfusionAdequate,
            'rosc_achieved' => $rosc,
            'summary' => $rosc 
                ? "Tái lập tuần hoàn tự nhiên (ROSC) thành công! EtCO2 đạt {$etco2} mmHg, ép tim hiệu quả {$ccfPct}%. Chuyển khẩn cấp về ICU để chăm sóc sau ngừng tim." 
                : "Hồi sinh tim phổi tích cực: Đã sốc điện {$shocks} lần. Cần duy trì ép tim liên tục không gián đoạn (CCF: {$ccfPct}%).",
        ];
    }
}
