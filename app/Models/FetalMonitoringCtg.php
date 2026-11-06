<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FetalMonitoringCtg extends Model
{
    use HasFactory;

    protected $table = 'fetal_monitoring_ctgs';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'delivery_room_bed',
        'baseline_fhr_bpm',
        'variability',
        'accelerations_count',
        'decelerations_type',
        'sinusoidal_pattern',
        'uterine_contractions_per_10min',
        'figo_classification',
        'acog_category',
        'emergency_csection_alert',
        'ai_trace_interpretation',
    ];

    protected $casts = [
        'baseline_fhr_bpm' => 'integer',
        'accelerations_count' => 'integer',
        'sinusoidal_pattern' => 'boolean',
        'uterine_contractions_per_10min' => 'integer',
        'emergency_csection_alert' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Phân loại CTG theo chuẩn FIGO 2015 & ACOG:
     * - FIGO: Normal, Suspicious, Pathological
     * - ACOG: Category I, Category II, Category III (Báo động toan chuyển hóa thai nhi / Cần mổ cấp cứu)
     */
    public static function classifyCtgTraces(
        int $baseline,
        string $variability,
        string $decelType,
        bool $sinusoidal,
        int $contractions10m
    ): array {
        // Nhịp hình sin (Sinusoidal) hoặc nhịp chậm kéo dài kèm biến thiên mất -> Pathological / ACOG III
        if ($sinusoidal || ($variability === 'absent' && in_array($decelType, ['late_uteroplacental_hypoxia', 'variable_cord', 'prolonged']))) {
            return [
                'figo' => 'pathological',
                'acog' => 'category_3_abnormal_acidosis',
                'emergency_alert' => true,
                'interpretation' => 'Báo động đỏ: Suy thai cấp nặng hoặc toan máu thai nhi. Đề xuất mổ lấy thai tối khẩn cấp (Crash C-Section) trong vòng 30 phút.',
            ];
        }

        // Nhịp tim thai bình thường: 110 - 160 bpm, dao động vừa, không có nhịp giảm muộn/biến đổi
        $isNormalBaseline = ($baseline >= 110 && $baseline <= 160);
        $isNormalVariability = ($variability === 'moderate_normal');
        $isBenignDecel = in_array($decelType, ['none', 'early_benign']);

        if ($isNormalBaseline && $isNormalVariability && $isBenignDecel && $contractions10m <= 5) {
            return [
                'figo' => 'normal',
                'acog' => 'category_1_normal',
                'emergency_alert' => false,
                'interpretation' => 'Nhịp tim thai bình thường. Kiềm toan bình thường, tiếp tục theo dõi chuyển dạ thường quy.',
            ];
        }

        // Trường hợp còn lại: Nghi ngờ / Category 2
        return [
            'figo' => 'suspicious',
            'acog' => 'category_2_indeterminate',
            'emergency_alert' => false,
            'interpretation' => 'Đường ghi CTG bất định loại II: Cần hồi sức trong tử cung (nằm nghiêng trái, bù dịch, thở oxy mẹ) và đánh giá lại sau 15-30 phút.',
        ];
    }
}
