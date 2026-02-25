<?php

namespace App\Notifications\Classroom;

use App\Models\InstLesson;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that the lesson has resumed after a break.
 * Triggered when the instructor calls resumeLesson().
 */
class LessonResumedNotification extends Notification implements ShouldQueue
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
            'classroom.lesson_resumed',
            config('user_notifications.notifications.classroom.lesson_resumed.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.lesson_resumed.user_controllable', false),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        $lessonName = $this->instLesson->GetLesson()?->name ?? 'the lesson';

        return [
            'type'           => 'classroom.lesson_resumed',
            'title'          => 'Lesson Resumed',
            'message'        => 'Break is over — ' . $lessonName . ' is resuming. Please return to your station.',
            'inst_lesson_id' => $this->instLesson->id,
            'inst_unit_id'   => $this->instLesson->inst_unit_id,
            'lesson_id'      => $this->instLesson->lesson_id,
            'url'            => '/classroom',
            'icon'           => 'play',
            'priority'       => 'medium',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
