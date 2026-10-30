<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathologyMortuaryStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'storage_type',
        'deceased_record_id',
        'rfid_tag_identifier',
        'temperature_celsius',
        'formalin_vapor_ppm',
        'exhaust_ventilation_status',
        'identity_verified_by_two_staff',
        'storage_status',
    ];

    protected $casts = [
        'temperature_celsius' => 'float',
        'formalin_vapor_ppm' => 'float',
        'identity_verified_by_two_staff' => 'boolean',
    ];

    public function checkEnvironmentalSafety(): bool
    {
        // An toàn khi nồng độ hơi formalin < 0.1 ppm và thông gió bình thường
        if ($this->formalin_vapor_ppm > 0.10) {
            $this->update(['exhaust_ventilation_status' => 'high_speed_purge']);
            return false;
        }

        return true;
    }
}
