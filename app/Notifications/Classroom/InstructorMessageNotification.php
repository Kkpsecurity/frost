<?php

namespace App\Notifications\Classroom;

use App\Models\ChatLog;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student of a new message from the instructor.
 * Triggered when the instructor sends a chat message via sendMessage().
 */
class InstructorMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly ChatLog $chatLog,
    ) {}

    /**
     * Delivery channels, respecting user preferences.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'classroom.instructor_message',
            config('user_notifications.notifications.classroom.instructor_message.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.instructor_message.user_controllable', true),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        $preview = mb_substr($this->chatLog->body ?? '', 0, 120);

        return [
            'type'           => 'classroom.instructor_message',
            'title'          => 'Message from Instructor',
            'message'        => $preview,
            'chat_id'        => $this->chatLog->id,
            'course_date_id' => $this->chatLog->course_date_id,
            'url'            => '/classroom',
            'icon'           => 'message-circle',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
