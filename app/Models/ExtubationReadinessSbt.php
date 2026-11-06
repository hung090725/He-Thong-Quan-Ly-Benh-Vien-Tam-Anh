<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtubationReadinessSbt extends Model
{
    use HasFactory;

    protected $table = 'extubation_readiness_sbts';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'sbt_duration_minutes',
        'respiratory_rate_f_bpm',
        'tidal_volume_vt_liters',
        'rsbi_score',
        'maximal_inspiratory_pressure_mip',
        'cuff_leak_test_positive',
        'sbt_outcome',
        'extubation_successful_no_reintubation_48h',
    ];

    protected $casts = [
        'sbt_duration_minutes' => 'integer',
        'respiratory_rate_f_bpm' => 'float',
        'tidal_volume_vt_liters' => 'float',
        'rsbi_score' => 'float',
        'maximal_inspiratory_pressure_mip' => 'float',
        'cuff_leak_test_positive' => 'boolean',
        'extubation_successful_no_reintubation_48h' => 'boolean',
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
     * Tính chỉ số thở nhanh nông RSBI (Yang & Tobin) và đánh giá độ sẵn sàng rút nội khí quản:
     * - RSBI = f / VT (L)
     * - RSBI < 105: Xác suất rút ống thành công > 80%
     * - MIP < -20 cmH2O và Cuff leak test dương tính (nghe tiếng xì khi xả bóng chèn)
     */
    public static function evaluateExtubationReadiness(float $f, float $vtLiters, float $mip, bool $cuffLeak): array
    {
        $rsbi = $vtLiters > 0 ? round($f / $vtLiters, 1) : 999.0;
        $isReady = ($rsbi < 105.0 && $mip <= -20.0 && $cuffLeak);

        return [
            'rsbi' => $rsbi,
            'ready' => $isReady,
            'outcome' => $isReady ? 'passed_ready_for_extubation' : 'failed_respiratory_muscle_fatigue',
            'recommendation' => $isReady 
                ? "ĐỦ TIÊU CHUẨN RÚT ỐNG NỘI KHÍ QUẢN (RSBI = {$rsbi} < 105, Nghiệm pháp rò cuff an toàn). Tiến hành hút đờm và rút ống." 
                : "CHƯA ĐỦ ĐIỀU KIỆN RÚT ỐNG (RSBI = {$rsbi} hoặc rò cuff âm tính). Tiếp tục thở máy hỗ trợ cơ hô hấp và đánh giá lại sau 24h.",
        ];
    }
}
