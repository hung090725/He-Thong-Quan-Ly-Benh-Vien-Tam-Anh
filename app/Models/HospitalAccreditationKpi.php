<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalAccreditationKpi extends Model
{
    use HasFactory;

    protected $table = 'hospital_accreditation_kpis';

    protected $fillable = [
        'kpi_code',
        'kpi_title',
        'domain_category',
        'benchmark_target',
        'actual_value',
        'metric_unit',
        'target_achieved',
        'compliance_level',
        'action_improvement_plan',
    ];

    protected $casts = [
        'benchmark_target' => 'float',
        'actual_value' => 'float',
        'target_achieved' => 'boolean',
    ];

    /**
     * Đánh giá mức độ tuân thủ theo thang mức 1-5 (83 Tiêu chí BYT / JCI International)
     * $higherIsBetter: true (VD: Tỷ lệ rửa tay %), false (VD: Tỷ lệ nhiễm khuẩn bệnh viện SSI)
     */
    public static function evaluateKpiCompliance(
        float $benchmark,
        float $actual,
        bool $higherIsBetter = true
    ): array {
        $achieved = $higherIsBetter ? ($actual >= $benchmark) : ($actual <= $benchmark);

        $ratio = $higherIsBetter 
            ? ($benchmark > 0 ? $actual / $benchmark : 1.0) 
            : ($actual > 0 ? $benchmark / $actual : 1.0);

        if ($ratio >= 1.05) {
            $level = 'level_5_excellent';
        } elseif ($ratio >= 0.95) {
            $level = 'level_4_good';
        } elseif ($ratio >= 0.80) {
            $level = 'level_3_fair';
        } else {
            $level = 'level_2_poor';
        }

        return [
            'achieved' => $achieved,
            'level' => $level,
        ];
    }
}
