<?php

namespace App\Notifications\Classroom;

use App\Models\InstLesson;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that a new lesson has started in their class.
 * Triggered when the instructor calls startLesson() and an InstLesson is created.
 */
class LessonStartedNotification extends Notification implements ShouldQueue
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
            'classroom.lesson_started',
            config('user_notifications.notifications.classroom.lesson_started.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.lesson_started.user_controllable', false),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        $lessonName = $this->instLesson->GetLesson()?->name ?? 'Lesson';

        return [
            'type'           => 'classroom.lesson_started',
            'title'          => 'Lesson Started',
            'message'        => 'The instructor has started: ' . $lessonName . '. Please pay attention.',
            'inst_lesson_id' => $this->instLesson->id,
            'inst_unit_id'   => $this->instLesson->inst_unit_id,
            'lesson_id'      => $this->instLesson->lesson_id,
            'lesson_name'    => $lessonName,
            'url'            => '/classroom',
            'icon'           => 'book-open',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate lesson-started notifications for the same InstLesson.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        $exists = $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['classroom.lesson_started'])
            ->whereRaw("(data::jsonb)->>'inst_lesson_id' = ?", [(string) $this->instLesson->id])
            ->exists();

        return ! $exists;
    }
}
