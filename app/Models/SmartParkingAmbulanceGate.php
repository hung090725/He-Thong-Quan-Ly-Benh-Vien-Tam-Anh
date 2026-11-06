<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartParkingAmbulanceGate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_license_plate',
        'vehicle_type',
        'alpr_camera_confidence_pct',
        'barrier_gate_status',
        'emergency_siren_detected',
        'assigned_parking_bay',
        'entry_timestamp',
    ];

    protected $casts = [
        'alpr_camera_confidence_pct' => 'float',
        'emergency_siren_detected' => 'boolean',
        'entry_timestamp' => 'datetime',
    ];

    public function fastTrackEmergencyAccess(): void
    {
        $this->barrier_gate_status = 'auto_opened_fast_track';
        $this->emergency_siren_detected = true;
        if ($this->exists) {
            $this->save();
        }
    }
}
