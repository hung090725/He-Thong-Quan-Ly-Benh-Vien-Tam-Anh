<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalAgvRobotFleet extends Model
{
    use HasFactory;

    protected $fillable = [
        'robot_code',
        'robot_model',
        'payload_category',
        'current_floor',
        'destination_location',
        'battery_percentage',
        'navigation_status',
        'obstacle_detected',
        'dispatch_supervisor_name',
    ];

    protected $casts = [
        'battery_percentage' => 'integer',
        'obstacle_detected' => 'boolean',
    ];

    public function checkBatteryAndReturn(): void
    {
        if ($this->battery_percentage <= 20) {
            $this->update([
                'navigation_status' => 'charging_return',
                'destination_location' => 'Dock Sạc Tầng Hầm B1',
            ]);
        }
    }

    public function deliverPayload(): void
    {
        $this->update([
            'navigation_status' => 'delivering_payload',
            'obstacle_detected' => false,
        ]);
    }
}
