<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoboticExoskeletonRehab extends Model
{
    use HasFactory;

    protected $table = 'robotic_exoskeleton_rehabs';

    protected $fillable = [
        'patient_id',
        'therapist_id',
        'exoskeleton_device_model',
        'session_duration_minutes',
        'total_steps_achieved',
        'distance_meters',
        'assistance_level_pct',
        'hip_rom_degrees',
        'knee_rom_degrees',
        'gait_symmetry_index_pct',
        'therapist_clinical_notes',
    ];

    protected $casts = [
        'session_duration_minutes' => 'integer',
        'total_steps_achieved' => 'integer',
        'distance_meters' => 'float',
        'assistance_level_pct' => 'integer',
        'hip_rom_degrees' => 'float',
        'knee_rom_degrees' => 'float',
        'gait_symmetry_index_pct' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'therapist_id');
    }

    /**
     * Đánh giá chất lượng phục hồi chức năng vận động và độ đối xứng sải chân:
     * - Chỉ số đối xứng dáng đi Gait Symmetry >= 85% (lý tưởng >= 90%)
     * - Khả năng giảm dần tỷ lệ trợ lực của Robot (Weaning robotic assistance)
     */
    public static function evaluateGaitQuality(float $symmetryPct, int $assistancePct, int $steps): array
    {
        $symmetryOk = ($symmetryPct >= 85.0);
        $progressTier = ($assistancePct <= 40 && $steps >= 500) ? 'Độc lập vận động tốt' : 'Giai đoạn tập thích ứng nâng cao';

        return [
            'symmetry_acceptable' => $symmetryOk,
            'tier' => $progressTier,
            'summary' => $symmetryOk 
                ? "Dáng đi đạt độ đối xứng sinh học tốt ({$symmetryPct}%). Tiếp tục giảm mức trợ lực robot để kích hoạt thần kinh vận động tự thân." 
                : "Bất đối xứng sải chân ({$symmetryPct}% < 85%). Cần tăng cường chỉnh lực đẩy bên chi yếu liệt.",
        ];
    }
}
