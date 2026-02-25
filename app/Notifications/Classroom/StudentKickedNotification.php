<?php

namespace App\Notifications\Classroom;

use App\Models\StudentUnit;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies a student that they have been ejected from the classroom session.
 * Sends via database, browser push, and email.
 */
class StudentKickedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly StudentUnit $studentUnit,
        protected readonly string $reason,
    ) {}

    /**
     * Delivery channels, respecting user preferences.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'classroom.kicked',
            config('user_notifications.notifications.classroom.kicked_from_classroom.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.classroom.kicked_from_classroom.user_controllable', false),
        );
    }

    /**
     * Email representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You Have Been Removed from the Classroom')
            ->greeting('Hello, ' . ($notifiable->fname ?? 'Student') . '.')
            ->line('You have been removed from your classroom session.')
            ->line('**Reason:** ' . $this->reason)
            ->line('If you believe this was an error, please contact your instructor or support.')
            ->action('Contact Support', url('/'))
            ->line('Thank you.');
    }

    /**
     * Database payload.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'              => 'classroom.kicked',
            'title'             => 'Removed from Classroom',
            'message'           => 'You have been removed from the classroom. Reason: ' . $this->reason,
            'student_unit_id'   => $this->studentUnit->id,
            'course_date_id'    => $this->studentUnit->course_date_id,
            'reason'            => $this->reason,
            'url'               => '/classroom',
            'icon'              => 'user-x',
            'priority'          => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate kick notifications for the same StudentUnit ejection.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        $exists = $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['classroom.kicked'])
            ->whereRaw("(data::jsonb)->>'student_unit_id' = ?", [(string) $this->studentUnit->id])
            ->exists();

        return ! $exists;
    }
}
