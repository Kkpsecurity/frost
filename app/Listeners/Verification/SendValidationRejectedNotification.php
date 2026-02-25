<?php

namespace App\Listeners\Verification;

use App\Events\Verification\ValidationRejected;
use App\Models\User;
use App\Models\Validation;
use App\Notifications\Verification\VerificationRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Handles ValidationRejected event.
 *
 * Fires VerificationRejectedNotification so the student knows
 * they must re-upload their photo.
 */
class SendValidationRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ValidationRejected $event): void
    {
        $user = $this->resolveUser($event->validation);

        if (! $user) {
            Log::warning('SendValidationRejectedNotification: could not resolve user', [
                'validation_id'  => $event->validation->id,
                'course_auth_id' => $event->validation->course_auth_id,
                'student_unit_id' => $event->validation->student_unit_id,
            ]);
            return;
        }

        $user->notify(new VerificationRejectedNotification(
            $event->validation,
            $event->validationType,
            $event->reason,
        ));
    }

    /**
     * Resolve the student User from a Validation record.
     */
    private function resolveUser(Validation $validation): ?User
    {
        try {
            if ($validation->course_auth_id) {
                return $validation->CourseAuth?->User;
            }

            return $validation->StudentUnit?->CourseAuth?->User;
        } catch (\Throwable $e) {
            Log::error('SendValidationRejectedNotification: user resolution failed', [
                'validation_id' => $validation->id,
                'error'         => $e->getMessage(),
            ]);
            return null;
        }
    }
}
