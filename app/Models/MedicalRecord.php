<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_code',
        'appointment_id',
        'patient_profile_id',
        'doctor_id',
        'symptoms',
        'diagnosis',
        'clinical_notes',
        'treatment_plan',
        'follow_up_date',
        'dispensed_at',
        'dispensed_by_user_id',
        'dispense_notes',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'dispensed_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by_user_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function isDispensed(): bool
    {
        return !is_null($this->dispensed_at);
    }

    public static function generateRecordCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "MR-{$today}-";

        $latest = self::where('record_code', 'LIKE', "{$prefix}%")
            ->orderBy('record_code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->record_code, -4);
            $nextNumber = str_pad((string)($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$nextNumber}";
    }
}
