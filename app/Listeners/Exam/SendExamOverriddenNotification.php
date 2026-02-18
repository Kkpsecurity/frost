<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamOverridden;
use App\Models\StudentActivity;
use App\Notifications\Exam\ExamAdminOverrideNotification;
use App\Services\StudentActivityTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class SendExamOverriddenNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected StudentActivityTracker $activityTracker
    ) {
        //
    }

    public function handle(ExamOverridden $event): void
    {
        $examAuth = $event->examAuth;
        $user = $examAuth->CourseAuth->User;

        if ($user) {
            // Send notification
            $user->notify(new ExamAdminOverrideNotification($examAuth, $event->action));

            // Track activity
            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_ADMIN_OVERRIDE,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'started_at' => now(),
                    'data' => [
                        'action' => $event->action,
                        'admin_user_id' => Auth::id(),
                    ],
                ]
            );
        }
    }
}
