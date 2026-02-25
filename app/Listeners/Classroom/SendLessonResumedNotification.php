<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\LessonResumed;
use App\Models\StudentUnit;
use App\Notifications\Classroom\LessonResumedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a LessonResumed notification to every student in the active classroom session.
 */
class SendLessonResumedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LessonResumed $event): void
    {
        $instLesson = $event->instLesson;
        $instUnit   = $event->instUnit;

        $studentUnits = StudentUnit::where('inst_unit_id', $instUnit->id)
            ->whereNull('ejected_at')
            ->with('CourseAuth.User')
            ->get();

        $notification = new LessonResumedNotification($instLesson);

        foreach ($studentUnits as $studentUnit) {
            $user = $studentUnit->CourseAuth?->User;

            if (! $user) {
                continue;
            }

            try {
                $user->notify($notification);
            } catch (\Throwable $e) {
                Log::error('LessonResumed notification failed', [
                    'user_id'        => $user->id,
                    'inst_lesson_id' => $instLesson->id,
                    'inst_unit_id'   => $instUnit->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}
