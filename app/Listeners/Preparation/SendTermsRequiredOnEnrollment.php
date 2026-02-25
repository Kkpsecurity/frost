<?php

namespace App\Listeners\Preparation;

use App\Events\Enrollment\CourseEnrolled;
use App\Events\Preparation\RangeDateRequired;
use App\Models\User;
use App\Notifications\Preparation\TermsRequiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * On enrollment, informs the student about pending preparation steps:
 *  1. Terms and conditions must be accepted (always required for new enrollments)
 *  2. Range date selection required (when course->needs_range = true and no range_date_id set)
 *
 * Listens on the existing CourseEnrolled event alongside SendCourseEnrolledNotification.
 */
class SendTermsRequiredOnEnrollment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CourseEnrolled $event): void
    {
        $courseAuth = $event->courseAuth;

        $courseAuth->loadMissing(['User', 'Course']);

        $user = $courseAuth->User;

        if (! $user) {
            Log::warning('SendTermsRequiredOnEnrollment: no user found for courseAuth', [
                'course_auth_id' => $courseAuth->id,
            ]);
            return;
        }

        // --- 1. Terms Required ---
        // agreed_at is null until the student accepts terms on class day.
        // Send once per enrollment to prompt them to review before class.
        if ($courseAuth->agreed_at === null) {
            $alreadySent = DatabaseNotification::where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->where('type', TermsRequiredNotification::class)
                ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $courseAuth->id])
                ->exists();

            if (! $alreadySent) {
                $user->notify(new TermsRequiredNotification($courseAuth));

                Log::info('SendTermsRequiredOnEnrollment: sent terms_required', [
                    'user_id'        => $user->id,
                    'course_auth_id' => $courseAuth->id,
                ]);
            }
        }

        // --- 2. Range Date Required ---
        // If the course mandates a range date and none has been assigned yet,
        // fire the RangeDateRequired event — which triggers its own notification listener.
        $course = $courseAuth->Course;

        if ($course && $course->needs_range && ! $courseAuth->range_date_id) {
            event(new RangeDateRequired($courseAuth));
        }
    }
}
