<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartNurseStationHub extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_code',
        'ward_name',
        'total_active_beds',
        'occupied_beds',
        'critical_alert_count',
        'pending_nurse_calls',
        'infusion_near_empty_count',
        'on_duty_head_nurse',
        'charge_nurse_list',
        'code_blue_standby_status',
        'dashboard_refresh_rate_sec',
    ];

    protected $casts = [
        'total_active_beds' => 'integer',
        'occupied_beds' => 'integer',
        'critical_alert_count' => 'integer',
        'pending_nurse_calls' => 'integer',
        'infusion_near_empty_count' => 'integer',
        'dashboard_refresh_rate_sec' => 'integer',
    ];

    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_active_beds === 0) {
            return 0.0;
        }
        return round(($this->occupied_beds / $this->total_active_beds) * 100, 1);
    }
}
