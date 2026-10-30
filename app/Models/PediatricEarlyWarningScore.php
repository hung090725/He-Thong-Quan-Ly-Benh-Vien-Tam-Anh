<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PediatricEarlyWarningScore extends Model
{
    use HasFactory;

    protected $table = 'pediatric_early_warning_scores';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'age_group',
        'behavior_score',
        'cardiovascular_score',
        'respiratory_score',
        'nebulizer_used',
        'persistent_vomiting_postop',
        'total_pews_score',
        'risk_action_level',
        'nursing_interventions',
    ];

    protected $casts = [
        'behavior_score' => 'integer',
        'cardiovascular_score' => 'integer',
        'respiratory_score' => 'integer',
        'nebulizer_used' => 'boolean',
        'persistent_vomiting_postop' => 'boolean',
        'total_pews_score' => 'integer',
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
     * Tính thang điểm PEWS và khuyến nghị can thiệp lâm sàng Nhi khoa
     */
    public static function calculatePews(
        int $behavior,
        int $cardio,
        int $resp,
        bool $nebulizer = false,
        bool $vomiting = false
    ): array {
        $total = $behavior + $cardio + $resp;
        if ($nebulizer) {
            $total += 2;
        }
        if ($vomiting) {
            $total += 2;
        }

        if ($total >= 5 || $behavior === 3 || $cardio === 3 || $resp === 3) {
            return [
                'total_score' => $total,
                'action_level' => 'high_pediatric_met_activation',
                'recommendation' => 'KÍCH HOẠT ĐỘI CẤP CỨU NHI NỘI VIỆN (Pediatric MET/Code Blue). Bác sĩ Hồi sức Nhi có mặt tại giường trong vòng 5 phút, chuẩn bị oxy/đặt nội khí quản.',
            ];
        }

        if ($total >= 3) {
            return [
                'total_score' => $total,
                'action_level' => 'medium_nurse_assessment',
                'recommendation' => 'Cảnh báo nguy cơ diễn tiến nặng: Điều dưỡng trưởng & Bác sĩ điều trị trực tiếp đánh giá lại trong 15 phút, tăng tần suất đo sinh hiệu 1 giờ/lần.',
            ];
        }

        return [
            'total_score' => $total,
            'action_level' => 'low_routine_monitor',
            'recommendation' => 'Nguy cơ thấp: Tiếp tục theo dõi sinh hiệu thường quy 4 giờ/lần theo quy trình khoa Nhi.',
        ];
    }
}
