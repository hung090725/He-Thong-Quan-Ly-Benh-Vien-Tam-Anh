<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HemodialysisCentralRoWater extends Model
{
    use HasFactory;

    protected $fillable = [
        'ro_plant_code',
        'conductivity_us_cm',
        'bacteria_cfu_per_ml',
        'endotoxin_eu_per_ml',
        'water_temperature_celsius',
        'thermal_disinfection_active',
        'permeate_flow_rate_lpm',
        'aami_iso23500_compliance',
    ];

    protected $casts = [
        'conductivity_us_cm' => 'float',
        'bacteria_cfu_per_ml' => 'integer',
        'endotoxin_eu_per_ml' => 'float',
        'water_temperature_celsius' => 'float',
        'thermal_disinfection_active' => 'boolean',
        'permeate_flow_rate_lpm' => 'float',
    ];

    public function auditIsoCompliance(): string
    {
        // Chuẩn AAMI/ISO 23500: độ dẫn < 10 uS/cm, vi khuẩn < 100 CFU/ml, endotoxin < 0.25 EU/ml
        if ($this->endotoxin_eu_per_ml >= 0.25) {
            $status = 'endotoxin_breach_shutoff';
        } elseif ($this->conductivity_us_cm >= 10.0 || $this->bacteria_cfu_per_ml >= 100) {
            $status = 'chemical_alarm_divert';
        } else {
            $status = 'iso_ultra_pure_pass';
        }

        $this->update(['aami_iso23500_compliance' => $status]);
        return $status;
    }
}
