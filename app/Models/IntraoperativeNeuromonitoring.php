<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntraoperativeNeuromonitoring extends Model
{
    use HasFactory;

    protected $table = 'intraoperative_neuromonitorings';

    protected $fillable = [
        'patient_id',
        'neurophysiologist_id',
        'surgical_type',
        'mep_amplitude_uv',
        'mep_amplitude_drop_pct',
        'ssep_latency_ms',
        'ssep_latency_increase_pct',
        'emg_spontaneous_train_activity',
        'neuromonitoring_alert_triggered',
        'surgical_corrective_action_taken',
        'postop_neurological_deficit',
    ];

    protected $casts = [
        'mep_amplitude_uv' => 'float',
        'mep_amplitude_drop_pct' => 'float',
        'ssep_latency_ms' => 'float',
        'ssep_latency_increase_pct' => 'float',
        'emg_spontaneous_train_activity' => 'boolean',
        'neuromonitoring_alert_triggered' => 'boolean',
        'postop_neurological_deficit' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function neurophysiologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'neurophysiologist_id');
    }

    /**
     * Đánh giá biến cố điện sinh lý thần kinh trong mổ (IONM):
     * - MEP tụt > 50% biên độ: Tổn thương bó tháp vận động
     * - SSEP trễ > 10% thời gian: Tổn thương đường dẫn truyền cảm giác
     */
    public static function evaluateNeuromonitoringSignal(float $mepDrop, float $ssepLatencyInc, bool $emgTrain): array
    {
        $alert = ($mepDrop >= 50.0 || $ssepLatencyInc >= 10.0 || $emgTrain);

        return [
            'alert_triggered' => $alert,
            'summary' => $alert 
                ? "BÁO ĐỘNG THẦN KINH TRONG MỔ: Biên độ MEP tụt {$mepDrop}% hoặc độ trễ SSEP tăng {$ssepLatencyInc}%. Yêu cầu phẫu thuật viên nới lỏng kéo nắn cột sống và nâng HA trung bình MAP > 85 mmHg ngay!" 
                : "Tín hiệu điện sinh lý thần kinh ổn định (MEP/SSEP/EMG an toàn). Tiếp tục phẫu thuật an toàn.",
        ];
    }
}
