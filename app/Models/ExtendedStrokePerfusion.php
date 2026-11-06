<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtendedStrokePerfusion extends Model
{
    use HasFactory;

    protected $table = 'extended_stroke_perfusions';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'onset_to_arrival_hours',
        'ischemic_core_volume_ml',
        'hypoperfused_volume_ml',
        'mismatch_volume_ml',
        'mismatch_ratio',
        'dawn_criteria_met',
        'defuse3_criteria_met',
        'extended_window_decision',
        'neurointervention_plan',
    ];

    protected $casts = [
        'onset_to_arrival_hours' => 'integer',
        'ischemic_core_volume_ml' => 'float',
        'hypoperfused_volume_ml' => 'float',
        'mismatch_volume_ml' => 'float',
        'mismatch_ratio' => 'float',
        'dawn_criteria_met' => 'boolean',
        'defuse3_criteria_met' => 'boolean',
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
     * Đánh giá chỉ định lấy huyết khối cơ học cửa sổ mở rộng 6 - 24 giờ
     * Theo tiêu chuẩn thử nghiệm DAWN & DEFUSE-3:
     * - Ischemic Core Volume < 70ml
     * - Mismatch Volume (Penumbra) >= 15ml
     * - Mismatch Ratio >= 1.8
     */
    public static function evaluateExtendedWindow(
        int $onsetHours,
        float $coreVolume,
        float $hypoperfusedVolume
    ): array {
        $mismatchVolume = max(0.0, $hypoperfusedVolume - $coreVolume);
        $mismatchRatio = $coreVolume > 0 ? round($hypoperfusedVolume / $coreVolume, 2) : 10.0;

        $dawnMet = ($onsetHours <= 24 && $coreVolume < 51.0);
        $defuse3Met = ($onsetHours <= 16 && $coreVolume < 70.0 && $mismatchVolume >= 15.0 && $mismatchRatio >= 1.8);

        $eligible = ($coreVolume < 70.0 && ($mismatchVolume >= 15.0 || $dawnMet));

        return [
            'mismatch_volume' => $mismatchVolume,
            'mismatch_ratio' => $mismatchRatio,
            'dawn_met' => $dawnMet,
            'defuse3_met' => $defuse3Met,
            'decision' => $eligible ? 'eligible_for_mechanical_thrombectomy' : 'ineligible_large_completed_infarct',
            'plan' => $eligible 
                ? 'ĐỦ ĐIỀU KIỆN CAN THIỆP LẤY HUYẾT KHỐI CƠ HỌC (Cửa sổ 6-24h). Vùng não tranh tối tranh sáng lớn, nguy cơ xuất huyết chuyển dạng thấp.' 
                : 'KHÔNG CÓ CHỈ ĐỊNH CAN THIỆP CƠ HỌC: Lõi hoại tử quá lớn (>70mL) hoặc không còn nhu mô não có thể cứu sống. Chuyển điều trị bảo tồn chống phù não.',
        ];
    }
}
