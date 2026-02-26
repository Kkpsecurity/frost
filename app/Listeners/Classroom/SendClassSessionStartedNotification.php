<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\ClassSessionStarted;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Notifications\Classroom\ClassSessionStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a ClassSessionStarted notification to every student enrolled in the course.
 *
 * NOTE: StudentUnit records do NOT exist yet when the instructor first starts the class —
 * they are created during student onboarding. Querying StudentUnit here always returned
 * an empty set. We now resolve enrolled students via CourseAuth instead.
 */
class SendClassSessionStartedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ClassSessionStarted $event): void
    {
        $instUnit     = $event->instUnit;
        $courseDateId = $event->courseDateId;

        // Resolve course_id from CourseDate → CourseUnit
        $courseDate = CourseDate::with('CourseUnit')->find($courseDateId);
        $courseId   = $courseDate?->CourseUnit?->course_id;

        if (! $courseId) {
            Log::warning('ClassSessionStarted: could not resolve course_id', [
                'course_date_id' => $courseDateId,
            ]);
            return;
        }

        // All active enrolled students for this course
        $courseAuths = CourseAuth::with('User')
            ->where('course_id', $courseId)
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->get();

        $notification = new ClassSessionStartedNotification($instUnit);

        foreach ($courseAuths as $courseAuth) {
            $user = $courseAuth->User;

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
