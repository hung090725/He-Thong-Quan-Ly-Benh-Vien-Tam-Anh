<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayCareInfusionLounge extends Model
{
    use HasFactory;

    protected $fillable = [
        'recliner_bay_code',
        'patient_id',
        'patient_name',
        'infusion_regimen_type',
        'scheduled_duration_hours',
        'infusion_rate_ml_hr',
        'recliner_massage_heat_active',
        'personal_tablet_connected',
        'nurse_call_urgency',
        'session_status',
    ];

    protected $casts = [
        'scheduled_duration_hours' => 'float',
        'infusion_rate_ml_hr' => 'integer',
        'recliner_massage_heat_active' => 'boolean',
        'personal_tablet_connected' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function triggerAnaphylaxisEmergency(): void
    {
        $this->update([
            'nurse_call_urgency' => 'anaphylaxis_stat_call',
            'infusion_rate_ml_hr' => 0, // Ngắt dịch truyền ngay lập tức
        ]);
    }

    public function completeInfusion(): void
    {
        $this->update([
            'session_status' => 'infusion_completed',
            'nurse_call_urgency' => 'normal',
        ]);
    }
}
