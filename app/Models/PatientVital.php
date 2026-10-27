<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientVital extends Model
{
    use HasFactory;

    protected $table = 'patient_vitals';

    protected $fillable = [
        'patient_profile_id',
        'appointment_id',
        'recorded_by_user_id',
        'recorded_at',
        'systolic_bp',
        'diastolic_bp',
        'heart_rate',
        'temperature',
        'spo2',
        'weight',
        'height',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'systolic_bp' => 'integer',
        'diastolic_bp' => 'integer',
        'heart_rate' => 'integer',
        'temperature' => 'float',
        'spo2' => 'integer',
        'weight' => 'float',
        'height' => 'float',
    ];

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    /**
     * AHA / VNHA Blood Pressure Category.
     */
    public function getBpClassificationAttribute(): array
    {
        if (!$this->systolic_bp || !$this->diastolic_bp) {
            return ['label' => 'Chưa đo HA', 'color' => 'secondary', 'alert' => false];
        }

        $sys = $this->systolic_bp;
        $dia = $this->diastolic_bp;

        if ($sys > 180 || $dia > 120) {
            return ['label' => 'Cơn Tăng Huyết Áp Nguy Cấp', 'color' => 'danger', 'alert' => true];
        }

        if ($sys >= 160 || $dia >= 100) {
            return ['label' => 'Tăng Huyết Áp Độ 2', 'color' => 'danger', 'alert' => true];
        }

        if ($sys >= 140 || $dia >= 90) {
            return ['label' => 'Tăng Huyết Áp Độ 1', 'color' => 'warning', 'alert' => true];
        }

        if ($sys >= 130 || $dia >= 85) {
            return ['label' => 'Bình Thường Cao (Tiền THA)', 'color' => 'info', 'alert' => false];
        }

        if ($sys < 90 || $dia < 60) {
            return ['label' => 'Huyết Áp Thấp', 'color' => 'warning', 'alert' => true];
        }

        return ['label' => 'Huyết Áp Tối Ưu', 'color' => 'success', 'alert' => false];
    }

    /**
     * Calculate Body Mass Index (BMI).
     */
    public function getBmiAttribute(): ?float
    {
        if ($this->weight && $this->height && $this->height > 0) {
            $heightMeters = $this->height / 100;
            return round($this->weight / ($heightMeters * $heightMeters), 1);
        }
        return null;
    }

    /**
     * WHO Asian BMI Classification.
     */
    public function getBmiClassificationAttribute(): ?string
    {
        $bmi = $this->bmi;
        if (!$bmi) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Thiếu cân';
        }
        if ($bmi < 23.0) {
            return 'Bình thường';
        }
        if ($bmi < 25.0) {
            return 'Tiền béo phì';
        }
        if ($bmi < 30.0) {
            return 'Béo phì Độ 1';
        }
        return 'Béo phì Độ 2';
    }

    public function isFever(): bool
    {
        return $this->temperature !== null && $this->temperature >= 37.5;
    }

    public function isHypoxia(): bool
    {
        return $this->spo2 !== null && $this->spo2 < 95;
    }

    public function isTachycardia(): bool
    {
        return $this->heart_rate !== null && $this->heart_rate > 100;
    }

    public function isBradycardia(): bool
    {
        return $this->heart_rate !== null && $this->heart_rate < 60;
    }
}
