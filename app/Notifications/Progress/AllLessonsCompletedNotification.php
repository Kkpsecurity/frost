<?php

namespace App\Notifications\Progress;

use App\Models\StudentUnit;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Sent when a student completes all lessons in their course unit.
 */
class AllLessonsCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly StudentUnit $studentUnit,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'progress.all_lessons_complete',
            config('user_notifications.notifications.progress.all_lessons_complete.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.progress.all_lessons_complete.user_controllable', false),
        );
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->studentUnit->GetCourse()->name ?? 'your course';

        return [
            'type'            => 'progress.all_lessons_complete',
            'title'           => 'All Lessons Complete!',
            'message'         => 'You have completed all lessons for ' . $courseName . '. Your exam is now available.',
            'course_auth_id'  => $this->studentUnit->course_auth_id,
            'student_unit_id' => $this->studentUnit->id,
            'url'             => '/classroom',
            'icon'            => 'book-check',
            'priority'        => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate all-lessons-complete notifications per StudentUnit.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.all_lessons_complete'])
            ->whereRaw("(data::jsonb)->>'student_unit_id' = ?", [(string) $this->studentUnit->id])
            ->exists();
    }
}
