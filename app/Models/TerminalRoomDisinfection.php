<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalRoomDisinfection extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'ward_department',
        'discharge_patient_name',
        'cleaning_trigger_event',
        'disinfection_method',
        'disinfection_robot_id',
        'run_duration_minutes',
        'cleaning_staff_name',
        'qr_verified_clean_at',
        'atp_bioluminescence_rlu',
        'room_ready_status',
    ];

    protected $casts = [
        'run_duration_minutes' => 'integer',
        'atp_bioluminescence_rlu' => 'integer',
        'qr_verified_clean_at' => 'datetime',
    ];

    public function certifyClean(): bool
    {
        // Chuẩn ATP quang học vi sinh đạt độ sạch vô khuẩn khi RLU <= 30
        if ($this->atp_bioluminescence_rlu <= 30) {
            $this->update([
                'room_ready_status' => 'certified_clean_ready',
                'qr_verified_clean_at' => now(),
            ]);
            return true;
        }

        $this->update([
            'room_ready_status' => 'quarantine_waiting',
        ]);
        return false;
    }
}
