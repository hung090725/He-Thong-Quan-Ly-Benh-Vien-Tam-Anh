<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view or download the medical attachment.
     * Allowed:
     * - Admin: always allowed
     * - Doctor who treated the patient or uploaded the file
     * - Receptionist for administrative lookup
     * - The patient who owns the medical record
     */
    public function view(User $user, Attachment $attachment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        if ($attachment->uploaded_by_user_id === $user->id) {
            return true;
        }

        $record = $attachment->medicalRecord;
        if (! $record) {
            return false;
        }

        // Doctor who examined the record
        if ($user->role === User::ROLE_DOCTOR && $user->doctor && $user->doctor->id === $record->doctor_id) {
            return true;
        }

        // Patient who owns the profile
        if ($user->role === User::ROLE_PATIENT && $user->patientProfile && $user->patientProfile->id === $record->patient_profile_id) {
            return true;
        }

        // Receptionist can view for operational verification
        if ($user->role === User::ROLE_RECEPTIONIST) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the attachment.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $attachment->uploaded_by_user_id === $user->id;
    }
}
