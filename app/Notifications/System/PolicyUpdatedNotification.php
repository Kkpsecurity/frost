<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class PolicyUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $policyName,
        protected string $summary = '',
        protected string $policyUrl = '',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * Non-controllable — always delivered to all students.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'system.policy_updated',
            config('user_notifications.notifications.system.policy_updated.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.system.policy_updated.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->policyName . ' Has Been Updated — ' . config('app.name'))
            ->greeting('Hello ' . ($notifiable->fname ?? 'there') . ',')
            ->line('We have updated our **' . $this->policyName . '**.');

        if ($this->summary) {
            $mail->line($this->summary);
        }

        if ($this->policyUrl) {
            $mail->action('View Updated ' . $this->policyName, $this->policyUrl);
        }

        return $mail
            ->line('By continuing to use ' . config('app.name') . ' you agree to the updated policy.')
            ->salutation('The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'system.policy_updated',
            'title'          => $this->policyName . ' Updated',
            'message'        => $this->summary ?: 'Our ' . $this->policyName . ' has been updated. Please review the changes.',
            'icon'           => 'file-alt',
            'priority_color' => 'info',
            'url'            => $this->policyUrl ?: url('/'),
            'policy_name'    => $this->policyName,
        ];
    }
}
