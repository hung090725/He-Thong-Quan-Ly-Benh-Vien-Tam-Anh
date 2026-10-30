<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicianFatigueCircadian extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'doctor_name',
        'shift_duration_hours',
        'consecutive_surgical_hours',
        'sleep_deficit_hours',
        'fatigue_risk_index_score',
        'mandatory_rest_alert',
        'relief_physician_assigned',
        'fitness_for_duty_status',
    ];

    protected $casts = [
        'shift_duration_hours' => 'float',
        'consecutive_surgical_hours' => 'float',
        'sleep_deficit_hours' => 'float',
        'fatigue_risk_index_score' => 'integer',
        'mandatory_rest_alert' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function evaluateFatigueRisk(): string
    {
        // Thang điểm Fatigue Risk Index (0-100)
        // Ca mổ > 6h hoặc thức > 18h hoặc điểm > 70 => Bắt buộc nghỉ ngủ bù
        if ($this->fatigue_risk_index_score >= 70 || $this->consecutive_surgical_hours >= 6.0 || $this->shift_duration_hours >= 20.0) {
            $status = 'grounded_mandatory_sleep';
            $this->mandatory_rest_alert = true;
        } elseif ($this->fatigue_risk_index_score >= 40) {
            $status = 'fatigue_warning_moderate';
            $this->mandatory_rest_alert = false;
        } else {
            $status = 'fit_for_duty';
            $this->mandatory_rest_alert = false;
        }

        $this->update([
            'fitness_for_duty_status' => $status,
            'mandatory_rest_alert' => $this->mandatory_rest_alert,
        ]);

        return $status;
    }
}
