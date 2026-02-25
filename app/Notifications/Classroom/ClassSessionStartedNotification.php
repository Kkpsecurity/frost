<?php

namespace App\Notifications\Classroom;

use App\Models\InstUnit;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that their class session has started.
 * Triggered when the instructor calls startClass() and an InstUnit is created.
 */
class ClassSessionStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly InstUnit $instUnit,
    ) {}

    /**
     * Delivery channels, respecting user preferences.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'classroom.session_started',
            config('user_notifications.notifications.classroom.session_started.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.classroom.session_started.user_controllable', false),
        );
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'classroom.session_started',
            'title'          => 'Class Has Started',
            'message'        => 'Your class session is now live. Please report to your station.',
            'inst_unit_id'   => $this->instUnit->id,
            'course_date_id' => $this->instUnit->course_date_id,
            'url'            => '/classroom',
            'icon'           => 'play-circle',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate session-started notifications for the same InstUnit.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        $exists = $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['classroom.session_started'])
            ->whereRaw("(data::jsonb)->>'inst_unit_id' = ?", [(string) $this->instUnit->id])
            ->exists();

        return ! $exists;
    }
}
