<?php

namespace App\Listeners\Verification;

use App\Events\Verification\ValidationApproved;
use App\Models\User;
use App\Models\Validation;
use App\Notifications\Verification\PhotoApprovedNotification;
use App\Notifications\Verification\VerificationCompleteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Handles ValidationApproved event.
 *
 * Always fires PhotoApprovedNotification (partial approval confirmation).
 * Additionally fires VerificationCompleteNotification when both photos are approved.
 */
class SendValidationApprovedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ValidationApproved $event): void
    {
        $user = $this->resolveUser($event->validation);

        if (! $user) {
            Log::warning('SendValidationApprovedNotifications: could not resolve user', [
                'validation_id'  => $event->validation->id,
                'course_auth_id' => $event->validation->course_auth_id,
                'student_unit_id' => $event->validation->student_unit_id,
            ]);
            return;
        }

        // Always notify about the individual photo approval
        $user->notify(new PhotoApprovedNotification($event->validation, $event->validationType));

        // If both photos are now approved → send the "fully verified" notification
        if ($event->fullyVerified) {
            $user->notify(new VerificationCompleteNotification($event->studentUnit));
        }
    }

    /**
     * Resolve the student User from a Validation record.
     *
     * - ID card validations have course_auth_id set
     * - Headshot validations have student_unit_id set
     */
    private function resolveUser(Validation $validation): ?User
    {
        try {
            if ($validation->course_auth_id) {
                return $validation->CourseAuth?->User;
            }

            return $validation->StudentUnit?->CourseAuth?->User;
        } catch (\Throwable $e) {
            Log::error('SendValidationApprovedNotifications: user resolution failed', [
                'validation_id' => $validation->id,
                'error'         => $e->getMessage(),
            ]);
            return null;
        }
    }
}
