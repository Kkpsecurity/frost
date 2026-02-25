<?php

namespace App\Notifications\Preparation;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies the student that they must select a range date for their course
 * before they can participate in the classroom session.
 *
 * Fired once at enrollment when the course has needs_range = true and
 * no range_date_id has been assigned yet.
 * Config key: preparation.range_date_required  (user_controllable = false — always sent)
 */
class RangeDateRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected CourseAuth $courseAuth,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'preparation.range_date_required',
            config('user_notifications.notifications.preparation.range_date_required.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.preparation.range_date_required.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';

        return (new MailMessage)
            ->subject('Action Required: Select a Range Date for ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your enrollment in **' . $courseName . '** requires you to select a range date.')
            ->line('A range date must be chosen before you can attend your classroom session.')
            ->action('Select Range Date', route('range_date.select'))
            ->line('Please complete this step as soon as possible to avoid delays on class day.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';

        return [
            'type'           => 'preparation.range_date_required',
            'title'          => 'Range Date Selection Required',
            'message'        => 'Please select a range date for ' . $courseName . ' to proceed.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'url'            => route('range_date.select'),
            'icon'           => 'calendar-alt',
            'priority'       => 'high',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
