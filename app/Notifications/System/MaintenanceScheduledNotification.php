<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class MaintenanceScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $scheduledAt,
        protected string $message = '',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * User-controllable — students who opt out will not receive this.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'system.maintenance_scheduled',
            config('user_notifications.notifications.system.maintenance_scheduled.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.system.maintenance_scheduled.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Scheduled Maintenance Notice — ' . config('app.name'))
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('We have scheduled a maintenance window for **' . $this->scheduledAt . '**.')
            ->line('During this time the platform may be briefly unavailable.');

        if ($this->message) {
            $mail->line($this->message);
        }

        return $mail
            ->line('We will restore service as quickly as possible. Thank you for your patience.')
            ->salutation('The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'system.maintenance_scheduled',
            'title'          => 'Scheduled Maintenance',
            'message'        => 'Platform maintenance is scheduled for ' . $this->scheduledAt . '.',
            'icon'           => 'tools',
            'priority_color' => 'warning',
            'url'            => url('/'),
            'scheduled_at'   => $this->scheduledAt,
        ];
    }
}
