<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricSafekeepingVault extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'patient_name',
        'sealed_bag_barcode',
        'item_inventory_manifest',
        'dual_sign_staff_1',
        'dual_sign_staff_2',
        'biometric_key_verified',
        'vault_locker_number',
        'custody_status',
        'released_at',
    ];

    protected $casts = [
        'biometric_key_verified' => 'boolean',
        'released_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function releaseToGuardian(): void
    {
        $this->update([
            'custody_status' => 'released_to_guardian',
            'released_at' => now(),
        ]);
    }
}
