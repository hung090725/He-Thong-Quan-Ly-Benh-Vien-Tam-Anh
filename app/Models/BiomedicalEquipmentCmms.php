<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiomedicalEquipmentCmms extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_serial_no',
        'device_name',
        'maintenance_type',
        'leakage_current_microamp',
        'ground_resistance_ohm',
        'mtbf_hours_calculated',
        'calibration_expiry_date',
        'certification_status',
        'bio_engineer_name',
    ];

    protected $casts = [
        'leakage_current_microamp' => 'float',
        'ground_resistance_ohm' => 'float',
        'mtbf_hours_calculated' => 'integer',
        'calibration_expiry_date' => 'date',
    ];

    public function verifyElectricalSafetyIec62353(): string
    {
        // Chuẩn an toàn điện y tế IEC 62353: dòng rò < 100 uA và điện trở tiếp đất < 0.2 Ohm
        if ($this->leakage_current_microamp >= 100.0 || $this->ground_resistance_ohm >= 0.20) {
            $status = 'quarantined_high_leakage';
        } else {
            $status = 'certified_safe_pass';
        }

        $this->update(['certification_status' => $status]);
        return $status;
    }
}
