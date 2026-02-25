<?php

namespace App\Listeners\Progress;

use App\Events\Progress\AllLessonsCompleted;
use App\Notifications\Progress\AllLessonsCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAllLessonsCompletedNotification implements ShouldQueue
{
    public function handle(AllLessonsCompleted $event): void
    {
        $user = $event->studentUnit->CourseAuth?->User;

        if (! $user) {
            return;
        }

        $user->notify(new AllLessonsCompletedNotification($event->studentUnit));
    }
}
