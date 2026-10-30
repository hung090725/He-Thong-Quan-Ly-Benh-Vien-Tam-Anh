<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegativePressureAiir extends Model
{
    use HasFactory;

    protected $table = 'negative_pressure_aiirs';

    protected $fillable = [
        'room_code',
        'patient_id',
        'pathogen_isolation_type',
        'pressure_differential_pa',
        'air_changes_per_hour_ach',
        'hepa_filter_exhaust_passed',
        'airlock_doors_interlocked',
        'pressure_failure_alarm',
        'uvc_disinfection_active',
    ];

    protected $casts = [
        'pressure_differential_pa' => 'float',
        'air_changes_per_hour_ach' => 'float',
        'hepa_filter_exhaust_passed' => 'boolean',
        'airlock_doors_interlocked' => 'boolean',
        'pressure_failure_alarm' => 'boolean',
        'uvc_disinfection_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Kiểm tra tuân thủ phòng cách ly áp lực âm lây truyền qua đường không khí theo chuẩn CDC/WHO:
     * - Áp suất chênh lệch <= -2.5 Pa (hoặc -0.01 inch H2O)
     * - Tần suất luân chuyển khí sạch >= 12 ACH
     */
    public static function evaluateAiirCompliance(float $pressurePa, float $ach): array
    {
        $pressureOk = ($pressurePa <= -2.5);
        $achOk = ($ach >= 12.0);

        $compliant = ($pressureOk && $achOk);

        return [
            'compliant' => $compliant,
            'pressure_ok' => $pressureOk,
            'ach_ok' => $achOk,
            'alert' => !$pressureOk,
            'summary' => $compliant 
                ? 'Buồng cách ly áp lực âm đạt chuẩn CDC an toàn tuyệt đối chống lây nhiễm chéo.' 
                : 'BÁO ĐỘNG PHÒNG CÁCH LY: Áp lực âm bị suy giảm hoặc tốc độ đổi khí không đủ 12 ACH. Khẩn trương kiểm tra hệ thống quạt hút và đóng kín cửa buồng đệm!',
        ];
    }
}
