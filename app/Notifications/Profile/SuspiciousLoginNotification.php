<?php

namespace App\Notifications\Profile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class SuspiciousLoginNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     *
     * @param  string  $ipAddress  The new/unknown IP address.
     * @param  string  $knownIp    The previously trusted IP address.
     * @param  string  $userAgent  The browser / client user-agent string.
     */
    public function __construct(
        protected string $ipAddress,
        protected string $knownIp,
        protected string $userAgent = '',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * Always sent — critical, non-controllable.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'profile.suspicious_login',
            config('user_notifications.notifications.profile.suspicious_login.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.profile.suspicious_login.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suspicious Login Detected on Your Account')
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('We detected a login to your account from an unrecognised IP address.')
            ->line('**New IP address:** ' . $this->ipAddress)
            ->line('**Previously known IP:** ' . ($this->knownIp ?: 'None recorded'))
            ->when($this->userAgent, fn($mail) => $mail->line('**Browser / device:** ' . $this->userAgent))
            ->line('If this was you, no action is needed. If you do not recognise this activity, please change your password immediately.')
            ->action('Change Password', url('/account?section=security'))
            ->salutation('The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'profile.suspicious_login',
            'title'          => 'Suspicious Login Detected',
            'message'        => 'A login was detected from a new IP address: ' . $this->ipAddress . '.',
            'icon'           => 'shield-exclamation',
            'priority_color' => 'danger',
            'url'            => url('/account?section=security'),
            'ip_address'     => $this->ipAddress,
            'known_ip'       => $this->knownIp,
        ];
    }
}
