<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicOpdRoomAllocator extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'department_name',
        'assigned_doctor_name',
        'doctor_specialty',
        'shift_session',
        'epaper_display_status',
        'current_queue_number',
        'next_queue_number',
        'waiting_patient_count',
        'average_consult_time_min',
        'last_epaper_synced_at',
    ];

    protected $casts = [
        'current_queue_number' => 'integer',
        'next_queue_number' => 'integer',
        'waiting_patient_count' => 'integer',
        'average_consult_time_min' => 'integer',
        'last_epaper_synced_at' => 'datetime',
    ];

    public function advanceQueue(): void
    {
        $this->current_queue_number = $this->next_queue_number;
        $this->next_queue_number += 1;
        if ($this->waiting_patient_count > 0) {
            $this->waiting_patient_count -= 1;
        }
        $this->last_epaper_synced_at = now();
        $this->save();
    }
}
