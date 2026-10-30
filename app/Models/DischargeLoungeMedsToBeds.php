<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DischargeLoungeMedsToBeds extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'patient_name',
        'departed_ward_room',
        'bed_freed_before_10am',
        'discharge_lounge_bay',
        'clinical_pharmacist_name',
        'meds_to_beds_delivered',
        'contactless_payment_settled',
        'discharge_ride_scheduled',
        'overall_discharge_status',
    ];

    protected $casts = [
        'bed_freed_before_10am' => 'boolean',
        'meds_to_beds_delivered' => 'boolean',
        'contactless_payment_settled' => 'boolean',
        'discharge_ride_scheduled' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function completeMedsDelivery(): void
    {
        $this->update([
            'meds_to_beds_delivered' => true,
            'overall_discharge_status' => 'counseled_meds_received',
        ]);
    }

    public function dispatchHome(): void
    {
        $this->update([
            'overall_discharge_status' => 'discharged_transport_home',
        ]);
    }
}
