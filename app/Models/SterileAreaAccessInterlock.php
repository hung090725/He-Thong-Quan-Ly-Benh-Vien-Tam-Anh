<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SterileAreaAccessInterlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_point_name',
        'zone_classification',
        'staff_badge_rfid',
        'staff_name',
        'staff_role',
        'biometric_face_matched',
        'interlock_outer_door_closed',
        'interlock_inner_door_unlocked',
        'access_decision',
        'access_timestamp',
    ];

    protected $casts = [
        'biometric_face_matched' => 'boolean',
        'interlock_outer_door_closed' => 'boolean',
        'interlock_inner_door_unlocked' => 'boolean',
        'access_timestamp' => 'datetime',
    ];

    public function processAccessRequest(bool $isAuthorizedRole): string
    {
        if (!$isAuthorizedRole) {
            $decision = 'denied_unauthorized_role';
        } elseif (!$this->interlock_outer_door_closed) {
            $decision = 'denied_airlock_breached';
        } else {
            $decision = 'granted_normal';
            $this->interlock_inner_door_unlocked = true;
        }

        $this->update([
            'access_decision' => $decision,
            'access_timestamp' => now(),
        ]);

        return $decision;
    }
}
