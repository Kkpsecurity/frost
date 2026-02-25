<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class SupportResponseNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $subject,
        protected string $preview = '',
        protected string $ticketUrl = '',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * User-controllable — students who opt out will miss support replies via notification
     * (but will still receive the reply via mail directly if configured separately).
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'system.support_response',
            config('user_notifications.notifications.system.support_response.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.system.support_response.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Support Reply: ' . $this->subject)
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('You have received a reply to your support request: **' . $this->subject . '**');

        if ($this->preview) {
            $mail->line('"' . $this->preview . '"');
        }

        $url = $this->ticketUrl ?: url('/account?section=inbox');

        return $mail
            ->action('View Full Reply', $url)
            ->salutation('The ' . config('app.name') . ' Support Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'system.support_response',
            'title'          => 'Support Reply: ' . $this->subject,
            'message'        => $this->preview ?: 'You have received a reply to your support request.',
            'icon'           => 'headset',
            'priority_color' => 'info',
            'url'            => $this->ticketUrl ?: url('/account?section=inbox'),
            'subject'        => $this->subject,
        ];
    }
}
