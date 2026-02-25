<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\StudentEjectedFromClassroom;
use App\Notifications\Classroom\StudentKickedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Sends a kicked-from-classroom notification to the ejected student only.
 */
class SendStudentKickedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StudentEjectedFromClassroom $event): void
    {
        $studentUnit = $event->studentUnit;
        $reason      = $event->reason;

        $user = $studentUnit->CourseAuth?->User;

        if (! $user) {
            Log::warning('StudentEjected: no user found for StudentUnit', [
                'student_unit_id' => $studentUnit->id,
            ]);
            return;
        }

        try {
            $user->notify(new StudentKickedNotification($studentUnit, $reason));
        } catch (\Throwable $e) {
            Log::error('StudentKicked notification failed', [
                'user_id'         => $user->id,
                'student_unit_id' => $studentUnit->id,
                'reason'          => $reason,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}
