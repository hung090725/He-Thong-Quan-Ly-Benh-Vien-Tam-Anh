<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriticalHelipadTransfer extends Model
{
    use HasFactory;

    protected $table = 'critical_helipad_transfers';

    protected $fillable = [
        'patient_id',
        'flight_physician_id',
        'flight_mission_code',
        'transfer_origin_location',
        'aircraft_type',
        'flight_departure_at',
        'estimated_helipad_touchdown_at',
        'transfer_risk_score_trips',
        'inflight_ventilator_running',
        'inflight_ecmo_running',
        'helipad_landing_clearance_granted',
        'receiving_department',
        'flight_transfer_logistics',
    ];

    protected $casts = [
        'flight_departure_at' => 'datetime',
        'estimated_helipad_touchdown_at' => 'datetime',
        'transfer_risk_score_trips' => 'integer',
        'inflight_ventilator_running' => 'boolean',
        'inflight_ecmo_running' => 'boolean',
        'helipad_landing_clearance_granted' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function flightPhysician(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'flight_physician_id');
    }

    /**
     * Đánh giá mức độ an toàn và sẵn sàng tiếp nhận chuyến bay trực thăng y tế:
     * - Thang điểm rủi ro chuyển viện TRIPS (0-60): Càng cao càng cần kíp hồi sức chuyên sâu
     * - Bãi đáp Helipad nóc tòa nhà mở sẵn sàng, thang máy chuyên dụng thông suốt
     */
    public static function evaluateHelipadTransferReadiness(int $tripsScore, bool $ecmo, bool $ventilator, bool $clearance): array
    {
        $highRiskTransfer = ($tripsScore >= 20 || $ecmo);
        $ready = $clearance;

        return [
            'high_risk' => $highRiskTransfer,
            'helipad_cleared' => $ready,
            'summary' => $ready 
                ? "Bãi đáp trực thăng Helipad đã sẵn sàng tiếp nhận! Điểm TRIPS: {$tripsScore} " . ($ecmo ? '(Đang chạy ECMO di động trên khoang bay)' : '') . ". Kíp cấp cứu trực tại sàn nóc tòa nhà." 
                : "CHƯA ĐƯỢC PHÉP HẠ CÁNH: Cần xác nhận an toàn khí tượng hoặc dọn chướng ngại vật tại bãi đáp!",
        ];
    }
}
