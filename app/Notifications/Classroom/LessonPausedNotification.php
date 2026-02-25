<?php

namespace App\Notifications\Classroom;

use App\Models\InstLesson;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that the current lesson has been paused (class is on break).
 * Triggered when the instructor calls pauseLesson().
 */
class LessonPausedNotification extends Notification implements ShouldQueue
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
            'classroom.lesson_paused',
            config('user_notifications.notifications.classroom.lesson_paused.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.lesson_paused.user_controllable', false),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        $breakNumber = $this->instLesson->BreaksTaken();

        return [
            'type'           => 'classroom.lesson_paused',
            'title'          => 'Class Is On Break',
            'message'        => 'The instructor has paused the lesson. Break #' . $breakNumber . ' — please stand by.',
            'inst_lesson_id' => $this->instLesson->id,
            'inst_unit_id'   => $this->instLesson->inst_unit_id,
            'lesson_id'      => $this->instLesson->lesson_id,
            'break_number'   => $breakNumber,
            'url'            => '/classroom',
            'icon'           => 'pause-circle',
            'priority'       => 'medium',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
