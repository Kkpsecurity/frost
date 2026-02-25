<?php

namespace App\Listeners\Progress;

use App\Events\Progress\LessonMilestoneReached;
use App\Notifications\Progress\LessonMilestoneNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLessonMilestoneNotification implements ShouldQueue
{
    public function handle(LessonMilestoneReached $event): void
    {
        $user = $event->studentUnit->CourseAuth?->User;

        if (! $user) {
            return;
        }

        $user->notify(new LessonMilestoneNotification(
            $event->studentUnit,
            $event->milestone,
            $event->completedLessons,
            $event->totalLessons,
        ));
    }
}
