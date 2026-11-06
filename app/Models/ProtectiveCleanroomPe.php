<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtectiveCleanroomPe extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'patient_id',
        'patient_name',
        'differential_pressure_pa',
        'hepa_filter_status',
        'air_changes_per_hour',
        'door_airlock_alarm',
        'aspergillus_spore_count',
        'safety_compliance',
    ];

    protected $casts = [
        'differential_pressure_pa' => 'float',
        'air_changes_per_hour' => 'float',
        'door_airlock_alarm' => 'boolean',
        'aspergillus_spore_count' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function evaluateCompliance(): string
    {
        // Chuẩn JCI / CDC: Phòng PE ghép tủy cần áp suất dương > +8 Pa và ACH >= 12
        if ($this->differential_pressure_pa < 8.0 || $this->air_changes_per_hour < 12.0) {
            $status = 'pressure_drop_alert';
        } elseif ($this->aspergillus_spore_count > 0.05 || $this->door_airlock_alarm) {
            $status = 'compromised_evacuate';
        } else {
            $status = 'pe_compliant';
        }

        $this->update(['safety_compliance' => $status]);
        return $status;
    }
}
