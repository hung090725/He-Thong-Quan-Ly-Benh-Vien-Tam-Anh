<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwakeCraniotomyMapping extends Model
{
    use HasFactory;

    protected $table = 'awake_craniotomy_mappings';

    protected $fillable = [
        'patient_id',
        'neurosurgeon_id',
        'tumor_eloquent_location',
        'anesthesia_technique',
        'cortical_stimulation_current_ma',
        'speech_arrest_detected',
        'motor_twitching_detected',
        'safety_margin_to_eloquent_cortex_mm',
        'extent_of_resection_pct',
        'intraoperative_seizure_occurred',
        'postop_neurological_status',
    ];

    protected $casts = [
        'cortical_stimulation_current_ma' => 'float',
        'speech_arrest_detected' => 'boolean',
        'motor_twitching_detected' => 'boolean',
        'safety_margin_to_eloquent_cortex_mm' => 'float',
        'extent_of_resection_pct' => 'float',
        'intraoperative_seizure_occurred' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function neurosurgeon(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'neurosurgeon_id');
    }

    /**
     * Đánh giá ranh giới phẫu thuật bảo tồn chức năng thần kinh khi kích thích vỏ não:
     * - Khoảng cách an toàn đến vùng nói/vận động >= 10mm giúp bảo tồn chức năng không di chứng
     * - Dừng cắt ngay lập tức khi xuất hiện ngừng ngôn ngữ (Speech arrest) hoặc co giật
     */
    public static function evaluateSurgicalBoundary(bool $speechArrest, bool $motorTwitch, float $marginMm): array
    {
        $hasDeficit = ($speechArrest || $motorTwitch);
        $safeMargin = ($marginMm >= 10.0);

        return [
            'stop_resection_alert' => $hasDeficit,
            'safe_margin_achieved' => $safeMargin,
            'instruction' => $hasDeficit 
                ? 'CHẠM VÙNG NÃO CHỨC NĂNG NGUY CẤP: Dừng cắt hút nhu mô não ngay lập tức tại vị trí này để bảo tồn giọng nói và vận động của bệnh nhân!' 
                : 'Khoảng cách an toàn đạt chuẩn (> 10mm). Tiếp tục phẫu tích u dưới định vị neuron-navigation.',
        ];
    }
}
