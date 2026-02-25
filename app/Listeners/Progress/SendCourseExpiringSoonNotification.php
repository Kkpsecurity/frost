<?php

namespace App\Listeners\Progress;

use App\Events\Progress\CourseExpiringSoon;
use App\Notifications\Progress\CourseExpiring30DaysNotification;
use App\Notifications\Progress\CourseExpiring7DaysNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseExpiringSoonNotification implements ShouldQueue
{
    public function handle(CourseExpiringSoon $event): void
    {
        $user = $event->courseAuth->User;

        if (! $user) {
            return;
        }

        if ($event->daysRemaining === 30) {
            $user->notify(new CourseExpiring30DaysNotification($event->courseAuth));
        } elseif ($event->daysRemaining === 7) {
            $user->notify(new CourseExpiring7DaysNotification($event->courseAuth));
        }
    }
}
