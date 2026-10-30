<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAssetTrackingBle extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag_ble',
        'asset_name',
        'equipment_category',
        'current_location_zone',
        'rssi_signal_strength_dbm',
        'in_geofence_perimeter',
        'asset_status',
        'last_movement_at',
    ];

    protected $casts = [
        'rssi_signal_strength_dbm' => 'integer',
        'in_geofence_perimeter' => 'boolean',
        'last_movement_at' => 'datetime',
    ];

    public function triggerTheftAlarm(): void
    {
        $this->update([
            'in_geofence_perimeter' => false,
            'asset_status' => 'out_of_bounds_theft_alarm',
            'last_movement_at' => now(),
        ]);
    }
}
