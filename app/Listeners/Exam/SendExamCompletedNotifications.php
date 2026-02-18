<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamCompleted;
use App\Models\StudentActivity;
use App\Notifications\Exam\ExamExpiredNotification;
use App\Notifications\Exam\ExamFailedNotification;
use App\Notifications\Exam\ExamPassedNotification;
use App\Notifications\Exam\ExamSubmittedNotification;
use App\Notifications\Exam\FinalAttemptWarningNotification;
use App\Notifications\Exam\NoAttemptsRemainingNotification;
use App\Services\StudentActivityTracker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExamCompletedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected StudentActivityTracker $activityTracker
    ) {
        //
    }

    public function handle(ExamCompleted $event): void
    {
        $examAuth = $event->examAuth;
        $user = $examAuth->CourseAuth->User;

        if (!$user) {
            return;
        }

        // Track submission
        $this->activityTracker->trackExamActivity(
            $user->id,
            StudentActivity::TYPE_EXAM_SUBMITTED,
            $examAuth->id,
            $examAuth->course_auth_id,
            [
                'ended_at' => now(),
                'data' => ['score' => $examAuth->score],
            ]
        );

        // Always notify that exam was submitted
        $user->notify(new ExamSubmittedNotification($examAuth));

        // Check if exam expired (time ran out)
        if ($examAuth->expired_at && $examAuth->expired_at <= now()) {
            $user->notify(new ExamExpiredNotification($examAuth));

            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_EXAM_EXPIRED,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'ended_at' => now(),
                    'data' => ['expired_at' => $examAuth->expired_at],
                ]
            );
        }

        // Check pass/fail status
        if ($examAuth->passed) {
            $user->notify(new ExamPassedNotification($examAuth));

            $exam = $examAuth->GetExam();
            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_EXAM_PASSED,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'ended_at' => now(),
                    'data' => [
                        'score' => $examAuth->score,
                        'pass_threshold' => $exam->num_to_pass,
                    ],
                ]
            );
        } else {
            $user->notify(new ExamFailedNotification($examAuth));

            $this->activityTracker->trackExamActivity(
                $user->id,
                StudentActivity::TYPE_EXAM_FAILED,
                $examAuth->id,
                $examAuth->course_auth_id,
                [
                    'ended_at' => now(),
                    'data' => ['score' => $examAuth->score],
                ]
            );

            // Check remaining attempts
            $attemptsRemaining = $examAuth->attempts_remaining ?? 0;

            if ($attemptsRemaining === 1) {
                // This is their final attempt warning
                $user->notify(new FinalAttemptWarningNotification($examAuth));
            } elseif ($attemptsRemaining <= 0) {
                // No attempts remaining
                $user->notify(new NoAttemptsRemainingNotification($examAuth));
            }
        }
    }
}
