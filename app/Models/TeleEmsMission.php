<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeleEmsMission extends Model
{
    protected $table = 'tele_ems_missions';

    protected $fillable = [
        'patient_id',
        'ambulance_callsign',
        'paramedic_in_charge',
        'location_gps',
        'eta_hospital_minutes',
        'heart_rate',
        'sbp',
        'dbp',
        'spo2',
        'ecg_12lead_finding',
        'efast_ultrasound_positive',
        'pre_activation_target',
        'hospital_team_ready',
        'mission_status',
    ];

    protected $casts = [
        'eta_hospital_minutes' => 'decimal:1',
        'sbp' => 'decimal:1',
        'dbp' => 'decimal:1',
        'spo2' => 'decimal:1',
        'efast_ultrasound_positive' => 'boolean',
        'hospital_team_ready' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public static function determinePreActivation(?string $ecgFinding, bool $efastPositive, ?float $spo2): string
    {
        $ecg = mb_strtolower((string)$ecgFinding, 'UTF-8');
        if (str_contains($ecg, 'stemi') || str_contains($ecg, 'st chênh lên') || str_contains($ecg, 'st elevation')) {
            return 'cathlab_stemi';
        }
        if ($efastPositive) {
            return 'trauma_or';
        }
        if ($spo2 !== null && $spo2 < 85.0) {
            return 'icu';
        }
        return 'none';
    }

    public static function evaluatePreActivation(string $target, float $etaMinutes): array
    {
        $isCritical = in_array($target, ['cathlab_stemi', 'stroke_team', 'trauma_or']) && $etaMinutes <= 30.0;
        return [
            'critical_urgency' => $isCritical,
            'message' => $isCritical 
                ? "Kích hoạt Code Khẩn cấp ngoại viện {$target}! Kíp bệnh viện sẵn sàng tại cửa đón trong {$etaMinutes} phút."
                : "Bệnh nhân chuyển viện thường quy, theo dõi sát sinh hiệu.",
        ];
    }
}
