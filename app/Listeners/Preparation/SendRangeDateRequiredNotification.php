<?php

namespace App\Listeners\Preparation;

use App\Events\Preparation\RangeDateRequired;
use App\Models\User;
use App\Notifications\Preparation\RangeDateRequiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * When a student enrolls in a course that requires a range date selection,
 * notify them to complete this step before their class day.
 *
 * Deduplication: one notification per enrollment (course_auth_id).
 */
class SendRangeDateRequiredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(RangeDateRequired $event): void
    {
        $courseAuth = $event->courseAuth;

        $courseAuth->loadMissing(['User', 'Course']);

        $user = $courseAuth->User;

        if (! $user) {
            Log::warning('SendRangeDateRequiredNotification: no user found for courseAuth', [
                'course_auth_id' => $courseAuth->id,
            ]);
            return;
        }

        // Deduplicate: only send once per enrollment.
        $alreadySent = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('type', RangeDateRequiredNotification::class)
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $courseAuth->id])
            ->exists();

        if ($alreadySent) {
            return;
        }

        $user->notify(new RangeDateRequiredNotification($courseAuth));

        Log::info('SendRangeDateRequiredNotification: sent range_date_required', [
            'user_id'        => $user->id,
            'course_auth_id' => $courseAuth->id,
            'course_id'      => $courseAuth->course_id,
        ]);
    }
}
