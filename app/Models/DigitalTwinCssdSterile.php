<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalTwinCssdSterile extends Model
{
    use HasFactory;

    protected $table = 'digital_twin_cssd_steriles';

    protected $fillable = [
        'tray_barcode_2d',
        'tray_name',
        'cssd_supervisor_id',
        'sterilization_method',
        'sterilizer_chamber_number',
        'sterilization_cycle_completed_at',
        'chemical_integrator_passed',
        'biological_spore_test_passed',
        'sterile_expiration_date',
        'tray_status',
        'assigned_operating_room',
    ];

    protected $casts = [
        'sterilization_cycle_completed_at' => 'datetime',
        'sterile_expiration_date' => 'date',
        'sterilizer_chamber_number' => 'integer',
        'chemical_integrator_passed' => 'boolean',
        'biological_spore_test_passed' => 'boolean',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'cssd_supervisor_id');
    }

    /**
     * Xác thực tính vô trùng đạt chuẩn và cấp phép đưa khay dụng cụ vào phòng mổ:
     * - Chỉ thị hóa học Class 5 đạt
     * - Chỉ thị sinh học Geobacillus stearothermophilus 100% âm tính
     */
    public static function verifySterileRelease(bool $chemicalPassed, bool $biologicalPassed, ?string $expirationDate): array
    {
        $isExpired = false;
        if ($expirationDate) {
            $isExpired = strtotime($expirationDate) < strtotime(date('Y-m-d'));
        }

        $released = ($chemicalPassed && $biologicalPassed && !$isExpired);

        return [
            'released_for_surgery' => $released,
            'is_expired' => $isExpired,
            'summary' => $released 
                ? 'Khay dụng cụ đạt chuẩn vô trùng tuyệt đối (Chỉ thị sinh học & hóa học PASS). Đủ điều kiện cấp phát vào phòng mổ.' 
                : 'CẢNH BÁO: Khay dụng cụ KHÔNG ĐẠT chuẩn vô khuẩn hoặc đã hết hạn! Nghiêm cấm đưa vào ca mổ.',
        ];
    }
}
