<?php

namespace App\Notifications\Classroom;

use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that the instructor requires their immediate attention.
 * This can be dispatched independently (no event) when needed.
 */
class AttentionRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly int $courseDateId,
        protected readonly string $message = 'The instructor requires your immediate attention.',
    ) {}

    /**
     * Delivery channels, respecting user preferences.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'classroom.attention_required',
            config('user_notifications.notifications.classroom.attention_required.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.attention_required.user_controllable', false),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'classroom.attention_required',
            'title'          => 'Attention Required',
            'message'        => $this->message,
            'course_date_id' => $this->courseDateId,
            'url'            => '/classroom',
            'icon'           => 'alert-triangle',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
