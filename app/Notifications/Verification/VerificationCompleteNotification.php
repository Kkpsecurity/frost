<?php

namespace App\Notifications\Verification;

use App\Models\StudentUnit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

/**
 * Sent to the student when both their ID card AND headshot have been approved
 * (StudentUnit.verified = true).
 * Config key: verification.complete
 */
class VerificationCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected ?StudentUnit $studentUnit = null,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'verification.complete',
            config('user_notifications.notifications.verification.verification_complete.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.verification.verification_complete.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Identity Verification Complete')
            ->greeting('Great news, ' . ($notifiable->fname ?? $notifiable->name) . '!')
            ->line('Your identity has been **fully verified** by your instructor.')
            ->line('Both your government-issued ID and your daily headshot have been approved.')
            ->line('You\'re all set to continue with your course.')
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('Thank you for completing the verification process.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'verification.complete',
            'title'           => 'Identity Verification Complete',
            'message'         => 'Your identity has been fully verified. Both photos have been approved.',
            'student_unit_id' => $this->studentUnit?->id,
            'url'             => route('classroom.dashboard'),
            'icon'            => 'shield-check',
            'priority'        => 'medium',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
