<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleanroomPharmacyCompounding extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleanroom_code',
        'iso_classification',
        'cascade_pressure_pa',
        'air_particle_count_0_5um',
        'air_shower_interlock_active',
        'compounding_type',
        'pharmacist_lead_name',
        'batch_sterility_status',
    ];

    protected $casts = [
        'cascade_pressure_pa' => 'float',
        'air_particle_count_0_5um' => 'integer',
        'air_shower_interlock_active' => 'boolean',
    ];

    public function verifyIsoCompliance(): bool
    {
        // ISO Class 5 giới hạn hạt 0.5um <= 3,520 hạt/m3 và áp suất bậc thang >= +20 Pa
        $isIso5 = $this->iso_classification === 'iso_class_5';
        $maxParticles = $isIso5 ? 3520 : 352000;

        if ($this->air_particle_count_0_5um <= $maxParticles && $this->cascade_pressure_pa >= 15.0 && $this->air_shower_interlock_active) {
            $this->update(['batch_sterility_status' => 'certified_sterile_pass']);
            return true;
        }

        $this->update(['batch_sterility_status' => 'airlock_compromised']);
        return false;
    }
}
