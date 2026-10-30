<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadLinedRadiologySafety extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'modality_type',
        'radiation_warning_light',
        'lead_door_sealed',
        'lead_glass_thickness_mm',
        'ferromagnetic_mri_alarm',
        'interlock_lock_engaged',
        'ambient_radiation_dose_rate_usvh',
        'safety_status',
    ];

    protected $casts = [
        'lead_door_sealed' => 'boolean',
        'lead_glass_thickness_mm' => 'float',
        'ferromagnetic_mri_alarm' => 'boolean',
        'interlock_lock_engaged' => 'boolean',
        'ambient_radiation_dose_rate_usvh' => 'float',
    ];

    public function triggerBeamEmission(): bool
    {
        // Điều kiện phát tia: Cửa chì đóng kín 100%, khóa liên động kích hoạt, không có báo động từ trường
        if ($this->lead_door_sealed && $this->interlock_lock_engaged && !$this->ferromagnetic_mri_alarm) {
            $this->update([
                'radiation_warning_light' => 'red_xray_emitting',
                'safety_status' => 'scanning_in_progress',
            ]);
            return true;
        }

        $this->update([
            'radiation_warning_light' => 'amber_prep',
            'safety_status' => 'door_ajar_interlock_tripped',
        ]);
        return false;
    }
}
