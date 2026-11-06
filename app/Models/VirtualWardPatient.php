<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualWardPatient extends Model
{
    use HasFactory;

    protected $table = 'virtual_ward_patients';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'virtual_bed_identifier',
        'admission_diagnosis',
        'virtual_admission_date',
        'current_weight_kg',
        'weight_change_3days_kg',
        'latest_spo2_pct',
        'latest_sbp',
        'latest_dbp',
        'red_flag_alert_triggered',
        'virtual_ward_status',
        'care_coordinator_notes',
    ];

    protected $casts = [
        'virtual_admission_date' => 'date',
        'current_weight_kg' => 'float',
        'weight_change_3days_kg' => 'float',
        'latest_spo2_pct' => 'float',
        'latest_sbp' => 'float',
        'latest_dbp' => 'float',
        'red_flag_alert_triggered' => 'boolean',
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
     * Thuật toán phát hiện sớm mất bù cấp (Acute Decompensation) ở bệnh nhân Bệnh viện tại nhà (HAH):
     * - Tăng cân > 2.0 kg trong 3 ngày (dấu hiệu ứ dịch phù phổi/suy tim sung huyết)
     * - Hoặc SpO2 < 92%
     * - Hoặc SBP > 180 hoặc < 90 mmHg
     */
    public static function detectDecompensationRisk(
        float $weightChange3d,
        float $spo2,
        float $sbp
    ): array {
        $weightWarning = ($weightChange3d >= 2.0);
        $hypoxiaWarning = ($spo2 < 92.0);
        $bpWarning = ($sbp >= 180.0 || $sbp < 90.0);

        $isRedFlag = ($weightWarning || $hypoxiaWarning || $bpWarning);

        $reasons = [];
        if ($weightWarning) {
            $reasons[] = 'Tăng cân nhanh > 2kg/3 ngày (Nghi ngờ ứ dịch cấp do suy tim)';
        }
        if ($hypoxiaWarning) {
            $reasons[] = 'SpO2 giảm thấp nguy hiểm (< 92%)';
        }
        if ($bpWarning) {
            $reasons[] = 'Huyết áp dao động bất thường ngoài ngưỡng an toàn';
        }

        return [
            'red_flag' => $isRedFlag,
            'summary' => $isRedFlag 
                ? 'BÁO ĐỘNG ĐỎ: ' . implode(', ', $reasons) . '. Cần liên hệ Telehealth ngay hoặc điều xe cấp cứu chuyển vào viện!'
                : 'Các chỉ số sinh hiệu và diễn tiến cân nặng trong tầm kiểm soát an toàn.',
        ];
    }
}
