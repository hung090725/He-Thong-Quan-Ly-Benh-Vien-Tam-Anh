<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty_id',
        'qualification',
        'cchn_number',
        'cchn_issued_date',
        'cchn_expiry_date',
        'cchn_issuer',
        'consultation_fee',
        'bio',
        'room_number',
        'building',
        'floor',
        'avatar',
        'is_available',
        'rating_avg',
        'rating_count',
        'qualification_en',
        'bio_en',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'is_available' => 'boolean',
            'cchn_issued_date' => 'date',
            'cchn_expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(DoctorAuditLog::class);
    }

    public function getIsCchnExpiringSoonAttribute(): bool
    {
        if (!$this->cchn_expiry_date) return false;
        $days = now()->diffInDays($this->cchn_expiry_date, false);
        return $days >= 0 && $days <= 30;
    }

    public function getIsCchnExpiredAttribute(): bool
    {
        if (!$this->cchn_expiry_date) return false;
        return now()->isAfter($this->cchn_expiry_date);
    }

    public function getDaysUntilCchnExpiryAttribute(): ?int
    {
        if (!$this->cchn_expiry_date) return null;
        return (int) now()->diffInDays($this->cchn_expiry_date, false);
    }

    public function getCleanNameAttribute(): string
    {
        $name = $this->user ? $this->user->name : 'Bác sĩ';
        return preg_replace('/^(Bác\.|Bà\.|Cụ\.|Em\.|Ông\.|Chị\.|Cô\.|Anh\.)\s+/u', '', $name);
    }

    public function getFullNameWithTitleAttribute(): string
    {
        $qualification = $this->qualification ? "{$this->qualification} " : '';
        return "{$qualification}{$this->clean_name}";
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Check if doctor has any active shift today.
     */
    public function getIsOnDutyTodayAttribute(): bool
    {
        if ($this->current_leave) {
            return false;
        }
        $today = strtolower(now()->format('l'));
        return $this->schedules->where('day_of_week', $today)->where('is_active', true)->isNotEmpty();
    }

    /**
     * Get active shifts for today.
     */
    public function getTodaySchedulesAttribute()
    {
        $today = strtolower(now()->format('l'));
        return $this->schedules->where('day_of_week', $today)->where('is_active', true);
    }

    /**
     * Get human-readable active shifts today.
     */
    public function getTodayShiftsTextAttribute(): string
    {
        $shifts = $this->today_schedules->pluck('shift')->map(function ($s) {
            return $s === 'morning' ? 'Sáng' : 'Chiều';
        })->join(' & ');

        return $shifts ?: 'Nghỉ trực';
    }

    /**
     * Registered leave requests for this doctor.
     */
    public function leaves()
    {
        return $this->hasMany(DoctorLeave::class, 'doctor_id');
    }

    /**
     * Substitution assignments where this doctor acts as a substitute.
     */
    public function substitutions()
    {
        return $this->hasMany(DoctorLeave::class, 'substitute_doctor_id');
    }

    /**
     * Check if doctor is on approved leave on a given date (defaults to today).
     */
    public function isOnLeave(?string $date = null): bool
    {
        $checkDate = $date ? \Carbon\Carbon::parse($date)->toDateString() : \Carbon\Carbon::today()->toDateString();

        return $this->leaves()
            ->where('status', 'approved')
            ->where('start_date', '<=', $checkDate)
            ->where('end_date', '>=', $checkDate)
            ->exists();
    }

    /**
     * Get active leave object for today if present.
     */
    public function getCurrentLeaveAttribute(): ?DoctorLeave
    {
        $today = \Carbon\Carbon::today()->toDateString();

        return $this->leaves()
            ->with('substituteDoctor.user')
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }

    /**
     * Check if currently on leave today.
     */
    public function getIsOnLeaveTodayAttribute(): bool
    {
        return $this->isOnLeave();
    }
}
