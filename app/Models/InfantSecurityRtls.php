<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfantSecurityRtls extends Model
{
    use HasFactory;

    protected $table = 'infant_security_rtls';

    protected $fillable = [
        'infant_patient_id',
        'mother_patient_id',
        'infant_rtls_tag_id',
        'mother_rtls_tag_id',
        'skin_tamper_sensor_intact',
        'mother_infant_dual_pairing_verified',
        'current_floor_room',
        'distance_to_nearest_exit_meters',
        'geofence_perimeter_breach_alarm',
        'elevator_emergency_lockdown_triggered',
        'infant_safety_status',
    ];

    protected $casts = [
        'distance_to_nearest_exit_meters' => 'float',
        'skin_tamper_sensor_intact' => 'boolean',
        'mother_infant_dual_pairing_verified' => 'boolean',
        'geofence_perimeter_breach_alarm' => 'boolean',
        'elevator_emergency_lockdown_triggered' => 'boolean',
    ];

    public function infant(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'infant_patient_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'mother_patient_id');
    }

    /**
     * Thuật toán giám sát an ninh sơ sinh và chống bắt cóc/nhầm lẫn trẻ:
     * - Cắt dây hoặc tuột cảm biến da -> Tamper alarm tức thì
     * - Tiến gần cửa thoát hiểm (< 5m) không có mẹ đi cùng -> Khóa thang máy khẩn cấp
     */
    public static function evaluateInfantSecurityStatus(bool $tamperIntact, bool $motherPaired, float $distanceToExit): array
    {
        $tamperAlarm = !$tamperIntact;
        $geofenceBreach = ($distanceToExit <= 5.0 && !$motherPaired);
        $lockdownElevator = ($tamperAlarm || $geofenceBreach);

        $status = 'safe_in_room';
        if ($tamperAlarm) {
            $status = 'tamper_cut_alarm';
        } elseif ($geofenceBreach) {
            $status = 'geofence_alert';
        } elseif (!$motherPaired) {
            $status = 'pairing_mismatch';
        }

        return [
            'tamper_alarm' => $tamperAlarm,
            'geofence_breach' => $geofenceBreach,
            'lockdown_elevator' => $lockdownElevator,
            'status' => $status,
            'summary' => match ($status) {
                'tamper_cut_alarm' => 'BÁO ĐỘNG ĐỎ AN NINH SƠ SINH: Vòng tay bé bị cắt đứt hoặc tháo gỡ trái phép! Tự động khóa toàn bộ cửa khoa Sản.',
                'geofence_alert' => "CẢNH BÁO BẮT CÓC / RỜI KHOA: Bé cách lối thoát hiểm {$distanceToExit}m mà không có mẹ đi cùng! Khóa thang máy khẩn cấp.",
                'pairing_mismatch' => 'CẢNH BÁO NHẦM LẪN MẸ - BÉ: Mã vòng tay đối chiếu không trùng khớp với người mẹ hiện tại!',
                default => 'Bé an toàn trong phòng bệnh, ghép đôi mẹ - con chuẩn xác 100%.',
            },
        ];
    }
}
