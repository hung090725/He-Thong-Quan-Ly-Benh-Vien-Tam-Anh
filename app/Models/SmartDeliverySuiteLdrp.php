<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartDeliverySuiteLdrp extends Model
{
    use HasFactory;

    protected $fillable = [
        'suite_code',
        'maternal_patient_id',
        'mother_name',
        'gestational_weeks',
        'current_stage',
        'bed_transformation_mode',
        'water_birth_tub_active',
        'infant_resuscitaire_warmed',
        'partner_companion_tag_id',
        'pain_management_type',
        'fetal_heart_rate_bpm',
        'maternal_comfort_score',
        'status',
    ];

    protected $casts = [
        'gestational_weeks' => 'float',
        'water_birth_tub_active' => 'boolean',
        'infant_resuscitaire_warmed' => 'boolean',
        'fetal_heart_rate_bpm' => 'integer',
        'maternal_comfort_score' => 'integer',
    ];

    public function maternalPatient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'maternal_patient_id');
    }

    public function advanceStage(string $newStage): void
    {
        $bedMode = match ($newStage) {
            'stage2_delivery' => 'active_delivery_mode',
            'stage4_recovery_postpartum' => 'comfort_relax_bed',
            default => $this->bed_transformation_mode ?: 'comfort_relax_bed',
        };

        $this->update([
            'current_stage' => $newStage,
            'bed_transformation_mode' => $bedMode,
        ]);
    }
}
