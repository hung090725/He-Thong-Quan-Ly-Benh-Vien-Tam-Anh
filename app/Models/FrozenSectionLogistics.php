<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FrozenSectionLogistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'specimen_label_code',
        'patient_id',
        'patient_name',
        'operating_room',
        'transit_time_minutes',
        'frozen_biopsy_cold_box_temp',
        'pathology_consultant_name',
        'histopathology_quick_result',
        'turnaround_total_minutes',
        'telepathology_screen_shared',
    ];

    protected $casts = [
        'transit_time_minutes' => 'float',
        'frozen_biopsy_cold_box_temp' => 'float',
        'turnaround_total_minutes' => 'integer',
        'telepathology_screen_shared' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function verifyTurnaroundCompliance(): bool
    {
        // Chuẩn sinh thiết lạnh tức thì: tổng thời gian <= 20 phút và chuyển mẫu <= 3 phút
        return $this->turnaround_total_minutes <= 20 && $this->transit_time_minutes <= 3.0;
    }
}
