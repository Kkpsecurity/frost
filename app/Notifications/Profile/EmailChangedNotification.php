<?php

namespace App\Notifications\Profile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class EmailChangedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     *
     * @param  string  $oldEmail  The previous email address.
     * @param  string  $newEmail  The newly set email address.
     */
    public function __construct(
        protected string $oldEmail,
        protected string $newEmail,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * Always sent via mail (to old + new address) and database — non-controllable.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'profile.email_changed',
            config('user_notifications.notifications.profile.email_changed.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.profile.email_changed.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     *
     * Sent to the new email address; the old email is mentioned in the body so
     * the student is aware of the change even if they no longer check the old address.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Email Address Has Been Changed')
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('The email address on your account has been updated.')
            ->line('**Previous email:** ' . $this->oldEmail)
            ->line('**New email:** ' . $this->newEmail)
            ->line('If you did not make this change, please contact support immediately.')
            ->action('Go to Account', url('/account?section=profile'))
            ->salutation('The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'profile.email_changed',
            'title'          => 'Email Address Changed',
            'message'        => 'Your account email address has been updated to ' . $this->newEmail . '.',
            'icon'           => 'envelope',
            'priority_color' => 'danger',
            'url'            => url('/account?section=profile'),
            'old_email'      => $this->oldEmail,
            'new_email'      => $this->newEmail,
        ];
    }
}
