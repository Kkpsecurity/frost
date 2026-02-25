<?php

namespace App\Notifications\Preparation;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies the student that they must accept the course terms and conditions
 * before their upcoming class can begin.
 *
 * Fired once at enrollment time when terms have not yet been accepted.
 * Config key: preparation.terms_required  (user_controllable = false — always sent)
 */
class TermsRequiredNotification extends Notification implements ShouldQueue
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
            'preparation.terms_required',
            config('user_notifications.notifications.preparation.terms_required.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.terms_required.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your upcoming course';

        return (new MailMessage)
            ->subject('Action Required: Accept Course Terms for ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('You are enrolled in **' . $courseName . '**.')
            ->line('Before you can attend class, you must review and accept the course terms and conditions.')
            ->action('Accept Terms Now', route('classroom.dashboard'))
            ->line('This step is required to gain access to your classroom session.');
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
            'type'           => 'preparation.terms_required',
            'title'          => 'Terms & Conditions Required',
            'message'        => 'Please accept the terms and conditions for ' . $courseName . ' before your class.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'url'            => route('classroom.dashboard'),
            'icon'           => 'file-contract',
            'priority'       => 'critical',
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
