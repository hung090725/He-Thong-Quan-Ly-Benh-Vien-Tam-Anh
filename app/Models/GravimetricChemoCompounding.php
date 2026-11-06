<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GravimetricChemoCompounding extends Model
{
    use HasFactory;

    protected $table = 'gravimetric_chemo_compoundings';

    protected $fillable = [
        'patient_id',
        'pharmacist_id',
        'preparation_barcode',
        'chemo_drug_name',
        'prescribed_dose_mg',
        'carrier_diluent',
        'expected_total_weight_grams',
        'measured_actual_weight_grams',
        'weight_deviation_pct',
        'gravimetric_check_passed',
        'biosafety_cabinet_hepa_certified',
        'compounded_at',
        'beyond_use_date_time',
    ];

    protected $casts = [
        'prescribed_dose_mg' => 'float',
        'expected_total_weight_grams' => 'float',
        'measured_actual_weight_grams' => 'float',
        'weight_deviation_pct' => 'float',
        'gravimetric_check_passed' => 'boolean',
        'biosafety_cabinet_hepa_certified' => 'boolean',
        'compounded_at' => 'datetime',
        'beyond_use_date_time' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'pharmacist_id');
    }

    /**
     * Xác thực trọng lượng dung dịch pha chế hóa chất ung thư bằng cân Gravimetric:
     * - Sai lệch khối lượng so với lý thuyết <= +-5% là đạt chuẩn an toàn
     */
    public static function verifyGravimetricAccuracy(float $expectedWeight, float $actualWeight): array
    {
        $deviation = 0.0;
        if ($expectedWeight > 0) {
            $deviation = round(abs($actualWeight - $expectedWeight) / $expectedWeight * 100.0, 2);
        }

        $passed = ($deviation <= 5.0);

        return [
            'deviation_pct' => $deviation,
            'passed' => $passed,
            'summary' => $passed 
                ? "Kiểm tra trọng lượng đạt chuẩn (Sai lệch: {$deviation}% <= 5%). Cho phép in nhãn mã vạch 2D và cấp phát hóa chất." 
                : "SAI LỆCH LIỀU HÓA CHẤT: Độ lệch {$deviation}% vượt ngưỡng cho phép 5%! Hủy mẻ pha chế để bảo đảm tính mạng người bệnh.",
        ];
    }
}
