<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartHospitalMicrogridEsg extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_zone_code',
        'grid_power_status',
        'ats_transfer_time_sec',
        'backup_generator_running',
        'ups_reserve_minutes',
        'rooftop_solar_generation_kw',
        'carbon_emissions_avoided_kg',
        'grid_safety_rating',
    ];

    protected $casts = [
        'ats_transfer_time_sec' => 'float',
        'backup_generator_running' => 'boolean',
        'ups_reserve_minutes' => 'integer',
        'rooftop_solar_generation_kw' => 'float',
        'carbon_emissions_avoided_kg' => 'float',
    ];

    public function triggerGridBlackoutEmergency(): void
    {
        $this->update([
            'grid_power_status' => 'grid_blackout_detected',
            'backup_generator_running' => true,
            'ats_transfer_time_sec' => 7.20,
        ]);
    }
}
