<?php

namespace App\Listeners\Progress;

use App\Events\Progress\CourseCompleted;
use App\Notifications\Progress\CertificateReadyNotification;
use App\Notifications\Progress\CourseCompletedNotification;
use App\Notifications\Progress\CourseFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseCompletedNotifications implements ShouldQueue
{
    public function handle(CourseCompleted $event): void
    {
        $user = $event->courseAuth->User;

        if (! $user) {
            return;
        }

        if ($event->isPassed) {
            // Student passed: congratulate + tell them certificate is ready
            $user->notify(new CourseCompletedNotification($event->courseAuth));
            $user->notify(new CertificateReadyNotification($event->courseAuth));
        } else {
            // Student failed: inform them of the outcome
            $user->notify(new CourseFailedNotification($event->courseAuth));
        }
    }
}
