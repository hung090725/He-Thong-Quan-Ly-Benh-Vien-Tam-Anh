<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecordsPhysicalArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'compactor_bay_code',
        'section_code',
        'total_paper_charts_stored',
        'temperature_celsius',
        'relative_humidity_pct',
        'smoke_detector_active',
        'fm200_cylinder_pressure_bar',
        'fire_suppression_status',
        'rfid_chart_locator_status',
    ];

    protected $casts = [
        'total_paper_charts_stored' => 'integer',
        'temperature_celsius' => 'float',
        'relative_humidity_pct' => 'float',
        'smoke_detector_active' => 'boolean',
        'fm200_cylinder_pressure_bar' => 'float',
    ];

    public function checkFireReadiness(): bool
    {
        // Áp suất bình FM-200 tiêu chuẩn >= 24 bar và không có khói
        if ($this->smoke_detector_active) {
            $this->update(['fire_suppression_status' => 'gas_discharge_countdown']);
            return false;
        }

        if ($this->fm200_cylinder_pressure_bar < 22.0) {
            $this->update(['fire_suppression_status' => 'service_inspection']);
            return false;
        }

        $this->update(['fire_suppression_status' => 'armed_standby']);
        return true;
    }
}
