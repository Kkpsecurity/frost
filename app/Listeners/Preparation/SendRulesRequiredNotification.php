<?php

namespace App\Listeners\Preparation;

use App\Events\Preparation\TermsAccepted;
use App\Models\User;
use App\Notifications\Preparation\RulesRequiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * After a student accepts the course terms, prompt them to accept
 * the classroom rules — the next sequential step in daily onboarding.
 *
 * Deduplication: one rules_required notification per student per class day
 * (keyed by course_auth_id + course_date_id).
 */
class SendRulesRequiredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TermsAccepted $event): void
    {
        $courseAuth  = $event->courseAuth;
        $studentUnit = $event->studentUnit;

        $courseAuth->loadMissing(['User', 'Course']);

        $user = $courseAuth->User;

        if (! $user) {
            Log::warning('SendRulesRequiredNotification: no user found for courseAuth', [
                'course_auth_id' => $courseAuth->id,
            ]);
            return;
        }

        // Deduplicate: only send once per student per class day.
        $alreadySent = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('type', RulesRequiredNotification::class)
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $courseAuth->id])
            ->whereRaw("(data::jsonb)->>'course_date_id' = ?", [(string) $studentUnit->course_date_id])
            ->exists();

        if ($alreadySent) {
            return;
        }

        $user->notify(new RulesRequiredNotification($courseAuth, $studentUnit->course_date_id));

        Log::info('SendRulesRequiredNotification: sent rules_required', [
            'user_id'        => $user->id,
            'course_auth_id' => $courseAuth->id,
            'course_date_id' => $studentUnit->course_date_id,
        ]);
    }
}
