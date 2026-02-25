<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\ClassSessionStarted;
use App\Models\StudentUnit;
use App\Notifications\Classroom\ClassSessionStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a ClassSessionStarted notification to every student enrolled in the course date.
 */
class SendClassSessionStartedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ClassSessionStarted $event): void
    {
        $instUnit = $event->instUnit;
        $courseDateId = $event->courseDateId;

        // Load all active students for this course date
        $studentUnits = StudentUnit::where('course_date_id', $courseDateId)
            ->whereNull('ejected_at')
            ->with('CourseAuth.User')
            ->get();

        $notification = new ClassSessionStartedNotification($instUnit);

        foreach ($studentUnits as $studentUnit) {
            $user = $studentUnit->CourseAuth?->User;

            if (! $user) {
                continue;
            }

            try {
                $user->notify($notification);
            } catch (\Throwable $e) {
                Log::error('ClassSessionStarted notification failed', [
                    'user_id'        => $user->id,
                    'inst_unit_id'   => $instUnit->id,
                    'course_date_id' => $courseDateId,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}
