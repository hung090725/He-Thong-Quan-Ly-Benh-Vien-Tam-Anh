<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BradenSmartBedPressure extends Model
{
    use HasFactory;

    protected $table = 'braden_smart_bed_pressures';

    protected $fillable = [
        'patient_id',
        'head_nurse_id',
        'braden_sensory_perception',
        'braden_moisture',
        'braden_activity',
        'braden_mobility',
        'braden_nutrition',
        'braden_friction_shear',
        'total_braden_score',
        'sacrum_peak_pressure_mmhg',
        'heels_peak_pressure_mmhg',
        'bed_head_elevation_degrees',
        'last_repositioning_elapsed_minutes',
        'turn_reminder_due_alert',
        'decubitus_ulcer_risk_level',
    ];

    protected $casts = [
        'total_braden_score' => 'integer',
        'sacrum_peak_pressure_mmhg' => 'float',
        'heels_peak_pressure_mmhg' => 'float',
        'bed_head_elevation_degrees' => 'float',
        'last_repositioning_elapsed_minutes' => 'integer',
        'turn_reminder_due_alert' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'head_nurse_id');
    }

    /**
     * Tính toán thang điểm Braden (6 - 23 điểm) và cảnh báo nguy cơ loét tì đè:
     * - <= 9: Nguy cơ rất cao (Very High Risk)
     * - 10-12: Nguy cơ cao (High Risk)
     * - 13-14: Nguy cơ trung bình (Moderate Risk)
     * - Quá 120 phút chưa xoay trở tư thế -> Báo động điều dưỡng
     */
    public static function calculateBradenRisk(array $scores, int $elapsedMinutes, float $headElevation): array
    {
        $total = array_sum($scores);

        $riskLevel = 'low_risk';
        if ($total <= 9) {
            $riskLevel = 'very_high_risk';
        } elseif ($total <= 12) {
            $riskLevel = 'high_risk';
        } elseif ($total <= 14) {
            $riskLevel = 'moderate_risk';
        }

        $turnDue = ($elapsedMinutes >= 120);
        $shearWarning = ($headElevation > 30.0);

        return [
            'total_score' => $total,
            'risk_level' => $riskLevel,
            'turn_due' => $turnDue,
            'shear_warning' => $shearWarning,
            'summary' => "Điểm Braden: {$total}/23 ({$riskLevel}). " . 
                ($turnDue ? "ĐÃ ĐẾN GIỜ XOAY TRỞ TƯ THẾ ({$elapsedMinutes} phút)! " : "Tư thế ổn định. ") . 
                ($shearWarning ? "Hạ góc đầu giường <= 30° để chống lực trượt xé mô." : ""),
        ];
    }
}
