<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiohazardDirtyUtilitySluice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sluice_room_code',
        'bedpan_washer_temp_celsius',
        'washer_disinfection_cycle',
        'yellow_bag_infectious_kg',
        'sharps_container_full_pct',
        'air_deodorizer_fan_status',
        'compliance_audit_status',
    ];

    protected $casts = [
        'bedpan_washer_temp_celsius' => 'float',
        'yellow_bag_infectious_kg' => 'float',
        'sharps_container_full_pct' => 'integer',
    ];

    public function auditSafety(): string
    {
        // Chuẩn tiệt khuẩn bô vịt >= 85°C, hộp sắc nhọn đầy <= 75%
        if ($this->sharps_container_full_pct > 75) {
            $status = 'sharps_overfill_warning';
        } elseif ($this->bedpan_washer_temp_celsius < 85.0) {
            $status = 'washer_temp_insufficient';
        } else {
            $status = 'fully_compliant';
        }

        $this->update(['compliance_audit_status' => $status]);
        return $status;
    }
}
