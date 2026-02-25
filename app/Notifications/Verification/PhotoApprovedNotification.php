<?php

namespace App\Notifications\Verification;

use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

/**
 * Sent to the student when a single validation photo (ID card or headshot) is approved.
 * Config key: verification.id_approved
 */
class PhotoApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected Validation $validation,
        protected string $validationType,   // 'id_card' | 'headshot'
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'verification.id_approved',
            config('user_notifications.notifications.verification.id_approved.channels', ['database']),
            (bool) config('user_notifications.notifications.verification.id_approved.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->validationType === 'id_card' ? 'Government-issued ID' : 'Daily headshot';

        return (new MailMessage)
            ->subject('Photo Approved — ' . $label)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '!')
            ->line('Your **' . strtolower($label) . '** has been reviewed and approved by your instructor.')
            ->line('You\'re one step closer to full identity verification.')
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('If you have any questions, please contact your instructor.');
    }

    public function toDatabase(object $notifiable): array
    {
        $label = $this->validationType === 'id_card' ? 'Government ID' : 'Headshot';

        return [
            'type'            => 'verification.id_approved',
            'title'           => $label . ' Approved',
            'message'         => 'Your ' . strtolower($label) . ' photo has been approved.',
            'validation_id'   => $this->validation->id,
            'validation_type' => $this->validationType,
            'url'             => route('classroom.dashboard'),
            'icon'            => 'id-badge',
            'priority'        => 'medium',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
