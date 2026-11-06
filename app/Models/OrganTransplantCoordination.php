<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganTransplantCoordination extends Model
{
    use HasFactory;

    protected $table = 'organ_transplant_coordinations';

    protected $fillable = [
        'patient_id',
        'lead_surgeon_id',
        'organ_type',
        'donor_type',
        'donor_code',
        'hla_mismatch_count',
        'crossmatch_result',
        'cold_ischemia_time_hours',
        'warm_ischemia_time_minutes',
        'immunosuppression_protocol',
        'tacrolimus_trough_level_ng_ml',
        'graft_status_day30',
        'clinical_notes',
    ];

    protected $casts = [
        'hla_mismatch_count' => 'integer',
        'cold_ischemia_time_hours' => 'float',
        'warm_ischemia_time_minutes' => 'float',
        'tacrolimus_trough_level_ng_ml' => 'float',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function leadSurgeon(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'lead_surgeon_id');
    }

    /**
     * Đánh giá mức độ tương thích kháng nguyên bạch cầu người (HLA 0-6 mismatches) & Phản ứng chéo Crossmatch
     */
    public static function evaluateHlaCompatibility(int $mismatches, string $crossmatch): array
    {
        if ($crossmatch === 'positive') {
            return [
                'compatibility_tier' => 'Chống chỉ định ghép (Crossmatch Dương Tính)',
                'recommendation' => 'Nguy cơ thải ghép tối cấp tức thì. Cần giải mẫn cảm (Desensitization) bằng Plasmapheresis và Rituximab hoặc đổi người hiến.',
            ];
        }

        if ($mismatches <= 1) {
            return [
                'compatibility_tier' => 'Khớp kháng nguyên HLA rất tốt',
                'recommendation' => 'Tiên lượng mảnh ghép dài hạn xuất sắc (>15-20 năm), phác đồ ức chế miễn dịch tiêu chuẩn liều thấp.',
            ];
        }

        if ($mismatches <= 3) {
            return [
                'compatibility_tier' => 'Khớp trung bình khá',
                'recommendation' => 'Đạt tiêu chuẩn ghép an toàn, theo dõi nồng độ Tacrolimus C0 chặt chẽ.',
            ];
        }

        return [
            'compatibility_tier' => 'Bất tương thích cao (Lệch 4-6 HLA)',
            'recommendation' => 'Cần điều trị cảm ứng mạnh bằng Kháng thể đơn dòng kháng Thymocyte (ATG) liều cao.',
        ];
    }

    /**
     * Đánh giá giới hạn an toàn thời gian thiếu máu lạnh (Cold Ischemia Time) theo từng tạng
     */
    public static function evaluateColdIschemiaLimit(string $organ, float $coldHours): array
    {
        $limits = [
            'heart' => 4.0,
            'lung' => 6.0,
            'liver' => 12.0,
            'kidney' => 24.0,
            'cornea' => 336.0,
        ];

        $max = $limits[strtolower($organ)] ?? 12.0;
        $isSafe = ($coldHours <= $max);

        return [
            'safe' => $isSafe,
            'limit_hours' => $max,
            'recommendation' => $isSafe 
                ? 'Thời gian thiếu máu lạnh trong giới hạn an toàn tế bào.' 
                : "VƯỢT GIỚI HẠN AN TOÀN ({$coldHours}h > {$max}h): Nguy cơ hoại tử ống thận cấp hoặc suy mảnh ghép kỳ đầu rất cao!",
        ];
    }
}
