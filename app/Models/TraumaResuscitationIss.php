<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraumaResuscitationIss extends Model
{
    use HasFactory;

    protected $table = 'trauma_resuscitation_isses';

    protected $fillable = [
        'patient_id',
        'trauma_leader_id',
        'ais_head_neck',
        'ais_face',
        'ais_chest',
        'ais_abdomen_pelvis',
        'ais_extremities_pelvic_girdle',
        'ais_external_burns',
        'calculated_iss_score',
        'revised_trauma_score_rts',
        'trauma_level_1_activated',
        'predicted_survival_triss_pct',
        'trauma_interventions_summary',
    ];

    protected $casts = [
        'ais_head_neck' => 'integer',
        'ais_face' => 'integer',
        'ais_chest' => 'integer',
        'ais_abdomen_pelvis' => 'integer',
        'ais_extremities_pelvic_girdle' => 'integer',
        'ais_external_burns' => 'integer',
        'calculated_iss_score' => 'integer',
        'revised_trauma_score_rts' => 'float',
        'trauma_level_1_activated' => 'boolean',
        'predicted_survival_triss_pct' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function traumaLeader(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'trauma_leader_id');
    }

    /**
     * Tính toán thang điểm tổn thương giải phẫu ISS (Injury Severity Score):
     * Lấy 3 vùng cơ thể có điểm AIS cao nhất, bình phương và cộng lại: ISS = A^2 + B^2 + C^2
     * Nếu bất kỳ vùng nào có AIS = 6 (tổn thương trí mạng không thể cứu), ISS tự động = 75.
     * Khi ISS >= 16: Đa chấn thương nặng (Major Trauma) -> Kích hoạt Báo động Đội Cấp Cứu Chấn Thương Cấp 1 (Trauma Level 1).
     */
    public static function calculateIss(array $aisScores): array
    {
        // Kiểm tra xem có tổn thương trí mạng AIS = 6
        foreach ($aisScores as $score) {
            if ($score >= 6) {
                return [
                    'iss' => 75,
                    'level_1' => true,
                    'severity' => 'Chấn thương trí mạng nguy cơ tử vong tức thì (ISS = 75)',
                ];
            }
        }

        // Sắp xếp giảm dần và lấy 3 điểm cao nhất
        rsort($aisScores);
        $top3 = array_slice($aisScores, 0, 3);
        $iss = 0;
        foreach ($top3 as $s) {
            $iss += ($s * $s);
        }

        $level1 = ($iss >= 16);

        return [
            'iss' => $iss,
            'level_1' => $level1,
            'severity' => $level1 
                ? "ĐA CHẤN THƯƠNG NẶNG (ISS = {$iss} >= 16): KÍCH HOẠT CODE TRAUMA LEVEL 1! Bác sĩ Gây mê, Phẫu thuật tổng quát, Chấn thương chỉnh hình có mặt tại phòng Cấp cứu trong 5 phút!" 
                : "Chấn thương mức độ nhẹ/vừa (ISS = {$iss} < 16). Xử trí cấp cứu theo phác đồ thông thường.",
        ];
    }
}
