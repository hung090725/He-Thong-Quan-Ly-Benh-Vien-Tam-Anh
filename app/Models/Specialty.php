<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'emergency_readiness_level',
        'is_code_red_supported',
        'emergency_hotline',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_code_red_supported' => 'boolean',
        ];
    }

    public function getEmergencyReadinessBadgeAttribute(): array
    {
        return match ($this->emergency_readiness_level) {
            'level_1_stemi' => ['label' => 'STEMI Can Thiệp 24/7', 'class' => 'bg-danger text-white'],
            'icu_ecmo' => ['label' => 'ECMO & Hồi Sức Tích Cực', 'class' => 'bg-danger text-white'],
            'trauma_surgery' => ['label' => 'Phẫu Thuật Đa Chấn Thương', 'class' => 'bg-warning text-dark'],
            'pediatric_icu' => ['label' => 'Cấp Cứu Nhi Chuyên Sâu', 'class' => 'bg-info text-white'],
            default => ['label' => 'Tiêu Chuẩn Lâm Sàng', 'class' => 'bg-secondary text-white'],
        };
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
