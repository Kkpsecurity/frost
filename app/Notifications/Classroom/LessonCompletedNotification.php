<?php

namespace App\Notifications\Classroom;

use App\Models\InstLesson;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that the current lesson has been completed.
 * Triggered when the instructor calls completeLesson().
 */
class LessonCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly InstLesson $instLesson,
    ) {}

    /**
     * Delivery channels, respecting user preferences.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'classroom.lesson_completed',
            config('user_notifications.notifications.classroom.lesson_completed.channels', ['database']),
            (bool) config('user_notifications.notifications.classroom.lesson_completed.user_controllable', true),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        $lessonName = $this->instLesson->GetLesson()?->name ?? 'Lesson';

        return [
            'type'           => 'classroom.lesson_completed',
            'title'          => 'Lesson Completed',
            'message'        => $lessonName . ' has been completed. Well done!',
            'inst_lesson_id' => $this->instLesson->id,
            'inst_unit_id'   => $this->instLesson->inst_unit_id,
            'lesson_id'      => $this->instLesson->lesson_id,
            'lesson_name'    => $lessonName,
            'url'            => '/classroom',
            'icon'           => 'check-circle',
            'priority'       => 'low',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate lesson-completed notifications for the same InstLesson.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        $exists = $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['classroom.lesson_completed'])
            ->whereRaw("(data::jsonb)->>'inst_lesson_id' = ?", [(string) $this->instLesson->id])
            ->exists();

        return ! $exists;
    }
}
