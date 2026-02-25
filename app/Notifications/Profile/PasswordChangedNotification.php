<?php

namespace App\Notifications\Profile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $ipAddress = '',
        protected string $userAgent = '',
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'profile.password_changed',
            config('user_notifications.notifications.profile.password_changed.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.profile.password_changed.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Password Has Been Changed')
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('Your account password was successfully changed.')
            ->when($this->ipAddress, fn($mail) => $mail->line('Login IP: ' . $this->ipAddress))
            ->line('If you did not make this change, please contact support immediately and reset your password.')
            ->action('Go to Account', url('/account?section=security'))
            ->salutation('The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'profile.password_changed',
            'title'          => 'Password Changed',
            'message'        => 'Your account password was successfully changed.',
            'icon'           => 'lock',
            'priority_color' => 'warning',
            'url'            => url('/account?section=security'),
            'ip_address'     => $this->ipAddress,
        ];
    }
}
