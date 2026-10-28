<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RECEPTIONIST = 'receptionist';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_PATIENT = 'patient';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isReceptionist(): bool
    {
        return $this->role === self::ROLE_RECEPTIONIST;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }

        $roles = array_map('trim', $roles);
        return in_array($this->role, $roles, true);
    }

    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_RECEPTIONIST => 'Lễ tân tiếp nhận',
            self::ROLE_DOCTOR => 'Bác sĩ chuyên khoa',
            self::ROLE_PATIENT => 'Bệnh nhân',
            default => 'Người dùng',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'bg-danger text-white',
            self::ROLE_RECEPTIONIST => 'bg-info text-dark',
            self::ROLE_DOCTOR => 'bg-primary text-white',
            self::ROLE_PATIENT => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function createdAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'created_by_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_by_user_id');
    }
}
