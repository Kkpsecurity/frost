<?php

namespace App\Listeners\Classroom;

use App\Events\Classroom\InstructorMessageSent;
use App\Models\StudentUnit;
use App\Notifications\Classroom\InstructorMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out an InstructorMessage notification to every student in the active classroom session.
 * Each student gets an individual database + browser push notification for the chat message.
 */
class SendInstructorMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InstructorMessageSent $event): void
    {
        $chatLog      = $event->chatLog;
        $courseDateId = $event->courseDateId;

        // Fan-out to all non-ejected students in this course date
        $studentUnits = StudentUnit::where('course_date_id', $courseDateId)
            ->whereNull('ejected_at')
            ->whereNotNull('inst_unit_id')   // only students in an active session
            ->with('CourseAuth.User')
            ->get();

        $notification = new InstructorMessageNotification($chatLog);

        foreach ($studentUnits as $studentUnit) {
            $user = $studentUnit->CourseAuth?->User;

            if (! $user) {
                continue;
            }

            try {
                $user->notify($notification);
            } catch (\Throwable $e) {
                Log::error('InstructorMessage notification failed', [
                    'user_id'        => $user->id,
                    'chat_id'        => $chatLog->id,
                    'course_date_id' => $courseDateId,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}
