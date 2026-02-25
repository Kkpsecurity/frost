<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\LessonStarted;
use App\Models\StudentUnit;
use App\Notifications\Classroom\LessonStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a LessonStarted notification to every student enrolled in the active classroom session.
 */
class SendLessonStartedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LessonStarted $event): void
    {
        $instLesson = $event->instLesson;
        $instUnit   = $event->instUnit;

        $studentUnits = StudentUnit::where('inst_unit_id', $instUnit->id)
            ->whereNull('ejected_at')
            ->with('CourseAuth.User')
            ->get();

        $notification = new LessonStartedNotification($instLesson);

        foreach ($studentUnits as $studentUnit) {
            $user = $studentUnit->CourseAuth?->User;

            if (! $user) {
                continue;
            }

            try {
                $user->notify($notification);
            } catch (\Throwable $e) {
                Log::error('LessonStarted notification failed', [
                    'user_id'        => $user->id,
                    'inst_lesson_id' => $instLesson->id,
                    'inst_unit_id'   => $instUnit->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}
