<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalIncidentCommandHics extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_event_code',
        'disaster_type',
        'command_center_location',
        'incident_commander_name',
        'casualty_intake_expected',
        'beds_mobilized_count',
        'drill_or_actual',
        'activation_status',
        'activated_at',
    ];

    protected $casts = [
        'casualty_intake_expected' => 'integer',
        'beds_mobilized_count' => 'integer',
        'activated_at' => 'datetime',
    ];

    public function deEscalateToStandby(): void
    {
        $this->update(['activation_status' => 'de_escalated_standby']);
    }

    public function terminateIncident(): void
    {
        $this->update(['activation_status' => 'terminated_post_incident']);
    }
}
