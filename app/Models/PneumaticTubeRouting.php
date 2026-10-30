<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PneumaticTubeRouting extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_code',
        'destination_station',
        'capsule_barcode',
        'specimen_category',
        'velocity_meters_per_sec',
        'g_force_shock_peak',
        'hemolysis_risk_alert',
        'dispatch_timestamp',
        'delivery_status',
    ];

    protected $casts = [
        'velocity_meters_per_sec' => 'float',
        'g_force_shock_peak' => 'float',
        'hemolysis_risk_alert' => 'boolean',
        'dispatch_timestamp' => 'datetime',
    ];

    public function assessHemolysisRisk(): bool
    {
        // Chuẩn an toàn mẫu máu: gia tốc G-force <= 2.5G và vận tốc 6.0 - 8.0 m/s
        if ($this->g_force_shock_peak > 2.50) {
            $this->update([
                'hemolysis_risk_alert' => true,
                'delivery_status' => 'diverted_maintenance',
            ]);
            return true;
        }

        $this->update([
            'hemolysis_risk_alert' => false,
            'delivery_status' => 'arrived_safe',
        ]);
        return false;
    }
}
