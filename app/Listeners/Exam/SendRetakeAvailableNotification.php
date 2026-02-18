<?php

namespace App\Listeners\Exam;

use App\Events\Exam\RetakeAvailable;
use App\Models\StudentActivity;
use App\Notifications\Exam\RetakeAvailableNotification;
use App\Services\StudentActivityTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRetakeAvailableNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected StudentActivityTracker $activityTracker
    ) {
        //
    }

    public function handle(RetakeAvailable $event): void
    {
        $examAuth = $event->examAuth;
        $user = $examAuth->CourseAuth->User;

        if ($user) {
            // Send notification
            $user->notify(new RetakeAvailableNotification($examAuth));

            // Track activity
            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_RETAKE_AVAILABLE,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'started_at' => now(),
                    'data' => ['next_attempt_at' => $examAuth->next_attempt_at],
                ]
            );
        }
    }
}
