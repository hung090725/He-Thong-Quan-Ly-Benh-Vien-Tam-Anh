<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassiveTransfusionProtocol extends Model
{
    use HasFactory;

    protected $table = 'massive_transfusion_protocols';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'protocol_code',
        'rbc_units_issued',
        'ffp_units_issued',
        'platelet_doses_issued',
        'cryoprecipitate_units',
        'tranexamic_acid_administered',
        'teg_r_time_minutes',
        'teg_k_time_minutes',
        'teg_maximum_amplitude_mm',
        'rotem_fibtem_a10_mm',
        'balanced_ratio_achieved',
        'target_hemostasis_reached',
        'mtp_status',
        'clinical_notes',
    ];

    protected $casts = [
        'rbc_units_issued' => 'integer',
        'ffp_units_issued' => 'integer',
        'platelet_doses_issued' => 'integer',
        'cryoprecipitate_units' => 'integer',
        'tranexamic_acid_administered' => 'boolean',
        'teg_r_time_minutes' => 'float',
        'teg_k_time_minutes' => 'float',
        'teg_maximum_amplitude_mm' => 'float',
        'rotem_fibtem_a10_mm' => 'float',
        'balanced_ratio_achieved' => 'boolean',
        'target_hemostasis_reached' => 'boolean',
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
     * Đánh giá tỷ lệ truyền máu cân bằng (chuẩn quốc tế 1:1:1 hoặc 1:1:2)
     */
    public static function evaluateTransfusionRatio(int $rbc, int $ffp, int $platelet): array
    {
        $isBalanced = ($rbc > 0 && abs($rbc - $ffp) <= 2 && $platelet >= 1);
        return [
            'balanced' => $isBalanced,
            'ratio_str' => "{$rbc} RBC : {$ffp} FFP : {$platelet} PLT",
        ];
    }

    /**
     * Phân tích đồ thị TEG/ROTEM để ra y lệnh cầm máu theo mục tiêu (Goal-directed hemostasis)
     */
    public static function analyzeTegRotem(?float $rTime, ?float $ma, ?float $fibtemA10): array
    {
        $recommendations = [];

        if ($rTime !== null && $rTime > 10.0) {
            $recommendations[] = 'R-time kéo dài (>10 phút): Thiếu hụt yếu tố đông máu huyết tương, truyền thêm 2-4 đơn vị FFP.';
        }
        if ($ma !== null && $ma < 50.0) {
            $recommendations[] = 'MA giảm (<50mm): Rối loạn chức năng hoặc thiếu số lượng tiểu cầu, truyền 1 pool tiểu cầu đậm đặc.';
        }
        if ($fibtemA10 !== null && $fibtemA10 < 10.0) {
            $recommendations[] = 'ROTEM FIBTEM A10 giảm (<10mm): Thiếu Fibrinogen máu nặng, bù Cryoprecipitate 10 đơn vị hoặc Fibrinogen đậm đặc 2g.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Các chỉ số đông máu đàn hồi TEG/ROTEM trong giới hạn bình thường.';
        }

        return [
            'recommendation' => implode(' | ', $recommendations),
        ];
    }
}
