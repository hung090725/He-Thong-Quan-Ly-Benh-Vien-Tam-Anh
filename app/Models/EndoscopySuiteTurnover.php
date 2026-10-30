<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndoscopySuiteTurnover extends Model
{
    use HasFactory;

    protected $fillable = [
        'suite_code',
        'procedure_type',
        'endoscope_serial_code',
        'turnaround_minutes',
        'aer_reprocessor_id',
        'peracetic_acid_test_passed',
        'biopsy_channel_dried',
        'readiness_status',
    ];

    protected $casts = [
        'turnaround_minutes' => 'integer',
        'peracetic_acid_test_passed' => 'boolean',
        'biopsy_channel_dried' => 'boolean',
    ];

    public function certifySuiteReady(): bool
    {
        if ($this->peracetic_acid_test_passed && $this->biopsy_channel_dried) {
            $this->update(['readiness_status' => 'suite_ready']);
            return true;
        }

        $this->update(['readiness_status' => 'scope_in_reprocessor']);
        return false;
    }
}
