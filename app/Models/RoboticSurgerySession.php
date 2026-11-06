<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoboticSurgerySession extends Model
{
    use HasFactory;

    protected $table = 'robotic_surgery_sessions';

    protected $fillable = [
        'patient_id',
        'surgeon_id',
        'robot_system_serial',
        'surgical_procedure_name',
        'console_time_minutes',
        'docking_time_minutes',
        'instruments_used_json',
        'instrument_lifespan_remaining',
        'instrument_retirement_alert',
        'conversion_to_open_surgery',
        'estimated_blood_loss_ml',
        'postop_hospital_stay_days',
        'clinical_summary',
    ];

    protected $casts = [
        'console_time_minutes' => 'integer',
        'docking_time_minutes' => 'integer',
        'instruments_used_json' => 'array',
        'instrument_lifespan_remaining' => 'integer',
        'instrument_retirement_alert' => 'boolean',
        'conversion_to_open_surgery' => 'boolean',
        'estimated_blood_loss_ml' => 'float',
        'postop_hospital_stay_days' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function surgeon(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'surgeon_id');
    }

    /**
     * Kiểm tra độ an toàn của tay dao EndoWrist da Vinci (chuẩn giới hạn 10 lần dùng)
     */
    public static function checkInstrumentSafety(int $remainingUses): array
    {
        $shouldRetire = ($remainingUses <= 1);
        $message = 'Tay dao Robot an toàn, còn ' . $remainingUses . ' lần sử dụng.';
        if ($remainingUses === 0) {
            $message = 'BÁO ĐỘNG HẾT HẠN: Tay dao đã dùng đủ 10 lần, chip RFID trên máy Da Vinci tự động vô hiệu hóa.';
        } elseif ($remainingUses <= 2) {
            $message = 'CẢNH BÁO SẮP HẾT HẠN: Tay dao chỉ còn ' . $remainingUses . ' lần sử dụng. Đề nghị trung tâm CSSD chuẩn bị dụng cụ dự phòng.';
        }

        return [
            'alert' => $shouldRetire,
            'message' => $message,
        ];
    }
}
