<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamStarted;
use App\Models\StudentActivity;
use App\Notifications\Exam\ExamStartedNotification;
use App\Services\StudentActivityTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExamStartedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected StudentActivityTracker $activityTracker
    ) {
        //
    }

    public function handle(ExamStarted $event): void
    {
        $examAuth = $event->examAuth;
        $user = $examAuth->CourseAuth->User;

        if ($user) {
            // Send notification
            $user->notify(new ExamStartedNotification($examAuth));

            // Track activity
            $exam = $examAuth->GetExam();
            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_EXAM_STARTED,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'started_at' => now(),
                    'data' => [
                        'expires_at' => $examAuth->expires_at,
                        'time_limit_seconds' => $exam->policy_expire_seconds,
                    ],
                ]
            );
        }
    }
}
