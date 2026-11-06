<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinenRfidLifecycleTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'linen_rfid_epc',
        'linen_type',
        'wash_cycle_count',
        'max_wash_lifespan',
        'last_wash_temp_celsius',
        'biological_spore_test',
        'linen_condition',
    ];

    protected $casts = [
        'wash_cycle_count' => 'integer',
        'max_wash_lifespan' => 'integer',
        'last_wash_temp_celsius' => 'float',
    ];

    public function recordWashCycle(): void
    {
        $this->wash_cycle_count += 1;
        if ($this->wash_cycle_count >= $this->max_wash_lifespan) {
            $this->linen_condition = 'retired_condemned';
        } else {
            $this->linen_condition = 'in_circulation_sterile';
        }
        if ($this->exists) {
            $this->save();
        }
    }
}
