<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CiedRemoteMonitoring extends Model
{
    use HasFactory;

    protected $table = 'cied_remote_monitorings';

    protected $fillable = [
        'patient_id',
        'cardiologist_id',
        'device_type',
        'device_serial_number',
        'battery_longevity_remaining_years',
        'lead_impedance_ohms',
        'ventricular_tachycardia_episodes_30d',
        'shocks_delivered_appropriate',
        'shocks_delivered_inappropriate',
        'electrical_storm_alert',
        'lead_fracture_risk',
        'remote_transmission_summary',
    ];

    protected $casts = [
        'battery_longevity_remaining_years' => 'float',
        'lead_impedance_ohms' => 'float',
        'ventricular_tachycardia_episodes_30d' => 'integer',
        'shocks_delivered_appropriate' => 'integer',
        'shocks_delivered_inappropriate' => 'integer',
        'electrical_storm_alert' => 'boolean',
        'lead_fracture_risk' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function cardiologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'cardiologist_id');
    }

    /**
     * Thuật toán AI phân tích tín hiệu điện học thiết bị tim mạch cấy ghép:
     * - Bão điện học (VT/VF Storm): >= 3 cơn loạn nhịp thất hoặc sốc trong 24 giờ
     * - Cảnh báo gãy dây điện cực: Trở kháng dây > 1500 Ohms hoặc < 200 Ohms
     * - Cảnh báo pin sắp cạn (ERI/EOL): Pin < 1 năm
     */
    public static function analyzeCiedTransmission(int $vtCount, int $inappShocks, float $impedance, float $batteryYears): array
    {
        $isStorm = ($vtCount >= 3);
        $leadBroken = ($impedance > 1500.0 || $impedance < 200.0);
        $batteryLow = ($batteryYears <= 1.0);

        $alerts = [];
        if ($isStorm) {
            $alerts[] = 'BÁO ĐỘNG ĐỎ CƠN BÃO ĐIỆN HỌC (VT/VF Storm): Nhập viện cấp cứu khẩn cấp để dùng thuốc chống loạn nhịp và triệt đốt catheter!';
        }
        if ($inappShocks > 0) {
            $alerts[] = 'CẢNH BÁO SỐC PHÁ RUNG KHÔNG THÍCH HỢP (' . $inappShocks . ' lần): Phân biệt rung nhĩ dẫn truyền nhanh, cần tinh chỉnh thuật toán phân biệt SVT/VT.';
        }
        if ($leadBroken) {
            $alerts[] = 'NGUY CƠ ĐỨT GÃY DÂY ĐIỆN CỰC: Trở kháng bất thường (' . $impedance . ' Ohms).';
        }

        return [
            'electrical_storm' => $isStorm,
            'lead_fracture' => $leadBroken,
            'battery_low' => $batteryLow,
            'summary' => !empty($alerts) ? implode(' | ', $alerts) : 'Các thông số điện học máy tạo nhịp/khử rung tim trong tầm kiểm soát an toàn.',
        ];
    }
}
