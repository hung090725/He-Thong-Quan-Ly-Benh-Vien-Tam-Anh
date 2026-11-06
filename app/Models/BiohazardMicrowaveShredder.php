<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiohazardMicrowaveShredder extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'batch_barcode',
        'waste_input_weight_kg',
        'microwave_temp_celsius',
        'exposure_time_minutes',
        'shredder_blade_count',
        'volume_reduction_pct',
        'spore_bacillus_atrophaeus_killed',
        'processing_status',
    ];

    protected $casts = [
        'waste_input_weight_kg' => 'float',
        'microwave_temp_celsius' => 'float',
        'exposure_time_minutes' => 'integer',
        'shredder_blade_count' => 'integer',
        'volume_reduction_pct' => 'float',
        'spore_bacillus_atrophaeus_killed' => 'boolean',
    ];

    public function completeDecontaminationCycle(): bool
    {
        // Chuẩn vi sóng: nhiệt độ >= 100°C và giảm thể tích >= 80%
        $isSafe = ($this->microwave_temp_celsius >= 100.0 && $this->volume_reduction_pct >= 80.0 && $this->spore_bacillus_atrophaeus_killed);
        $this->processing_status = $isSafe ? 'decontaminated_inert' : 'maintenance_blade_dull';
        if ($this->exists) {
            $this->save();
        }
        return (bool) $isSafe;
    }
}
