<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamAuthorized;
use App\Models\StudentActivity;
use App\Notifications\Exam\ExamAuthorizedNotification;
use App\Services\StudentActivityTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExamAuthorizedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected StudentActivityTracker $activityTracker
    ) {
        //
    }

    public function handle(ExamAuthorized $event): void
    {
        $examAuth = $event->examAuth;
        $user = $examAuth->CourseAuth->User;

        if ($user) {
            // Send notification
            $user->notify(new ExamAuthorizedNotification($examAuth));

            // Track activity
            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_EXAM_AUTHORIZED,
                $examAuth->id,
                $examAuth->course_auth_id,
                ['started_at' => now()]
            );
        }
    }
}
