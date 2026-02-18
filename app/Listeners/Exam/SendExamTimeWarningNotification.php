<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamTimeWarning;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExamTimeWarningNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(ExamTimeWarning $event): void
    {
        // NOTE: Time warnings during active exam were removed from Phase 1
        // as they were deemed impractical/distracting during exam attempts.
        // This listener is kept as a placeholder for potential future use
        // or alternative time-based notifications.

        // If time warnings are re-enabled, implementation would be:
        // $examAuth = $event->examAuth;
        // $user = $examAuth->CourseAuth->User;
        // if ($user) {
        //     $user->notify(new ExamTimeWarningNotification($examAuth, $event->minutesRemaining));
        // }
    }
}
