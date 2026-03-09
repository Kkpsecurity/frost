<?php

namespace App\Notifications\Classroom;

use App\Models\SelfStudyLesson;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student when their self-study lesson session ends.
 * Fired from StudentLessonSessionController::completeSession().
 */
class SelfStudyLessonCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly SelfStudyLesson $selfStudyLesson,
        protected readonly bool $passed,
        protected readonly int $quotaMinutesConsumed,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'self_study.lesson_completed',
            config('user_notifications.notifications.classroom.self_study_lesson_completed.channels', ['database']),
            (bool) config('user_notifications.notifications.classroom.self_study_lesson_completed.user_controllable', true),
        );
    }

    public function toDatabase(object $notifiable): array
    {
        $lessonName = $this->selfStudyLesson->GetLesson()?->name ?? 'Lesson';

        if ($this->passed) {
            $title   = 'Self-Study Lesson Completed';
            $message = 'You completed "' . $lessonName . '". ' . $this->quotaMinutesConsumed . ' min deducted from your quota.';
            $icon    = 'check-circle';
        } else {
            $title   = 'Self-Study Session Ended';
            $message = 'Your session for "' . $lessonName . '" ended without meeting the 80% threshold. ' . $this->quotaMinutesConsumed . ' min deducted.';
            $icon    = 'clock';
        }

        return [
            'type'                    => 'self_study.lesson_completed',
            'title'                   => $title,
            'message'                 => $message,
            'passed'                  => $this->passed,
            'lesson_id'               => $this->selfStudyLesson->lesson_id,
            'lesson_name'             => $lessonName,
            'course_auth_id'          => $this->selfStudyLesson->course_auth_id,
            'self_study_lesson_id'    => $this->selfStudyLesson->id,
            'quota_minutes_consumed'  => $this->quotaMinutesConsumed,
            'url'                     => '/classroom',
            'icon'                    => $icon,
            'priority'                => 'low',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate notifications for the same SelfStudyLesson record.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['self_study.lesson_completed'])
            ->whereRaw("(data::jsonb)->>'self_study_lesson_id' = ?", [(string) $this->selfStudyLesson->id])
            ->exists();
    }
}
