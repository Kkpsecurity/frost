<?php

namespace App\Listeners\Enrollment;

use App\Events\Enrollment\CourseEnrolled;
use App\Notifications\Enrollment\CourseEnrollmentConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCourseEnrolledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CourseEnrolled $event): void
    {
        $user = $event->courseAuth->User;

        if ($user) {
            $user->notify(new CourseEnrollmentConfirmedNotification($event->courseAuth));
        }
    }
}
