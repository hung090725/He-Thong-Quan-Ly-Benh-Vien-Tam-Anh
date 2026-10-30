<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartInpatientRoomConsole extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'patient_id',
        'patient_name',
        'room_class',
        'temperature_celsius',
        'circadian_lighting_mode',
        'curtain_status',
        'nurse_call_type',
        'nurse_call_status',
        'patient_pain_score',
        'dietary_order',
        'iot_device_status',
        'notes',
    ];

    protected $casts = [
        'temperature_celsius' => 'float',
        'patient_pain_score' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function triggerNurseCall(string $type, int $painScore = 0): void
    {
        $this->update([
            'nurse_call_type' => $type,
            'nurse_call_status' => 'pending',
            'patient_pain_score' => $painScore,
        ]);
    }

    public function resolveNurseCall(): void
    {
        $this->update([
            'nurse_call_status' => 'resolved',
            'nurse_call_type' => 'none',
        ]);
    }
}
