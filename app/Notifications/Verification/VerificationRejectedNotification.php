<?php

namespace App\Notifications\Verification;

use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

/**
 * Sent to the student when a validation photo is rejected by the instructor.
 * Student must re-upload the rejected photo.
 * Config key: verification.rejected  (user_controllable = false — always sent)
 */
class VerificationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected Validation $validation,
        protected string $validationType,   // 'id_card' | 'headshot'
        protected string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'verification.rejected',
            config('user_notifications.notifications.verification.verification_rejected.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.verification.verification_rejected.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->validationType === 'id_card' ? 'Government-issued ID' : 'Daily headshot';

        return (new MailMessage)
            ->subject('Action Required: ' . $label . ' Rejected')
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your **' . strtolower($label) . '** could not be verified and has been rejected.')
            ->line('**Reason:** ' . $this->reason)
            ->line('Please log in and upload a new photo to continue with your course.')
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('If you have questions about this decision, please contact your instructor.');
    }

    public function toDatabase(object $notifiable): array
    {
        $label = $this->validationType === 'id_card' ? 'Government ID' : 'Headshot';

        return [
            'type'            => 'verification.rejected',
            'title'           => $label . ' Rejected — Action Required',
            'message'         => 'Your ' . strtolower($label) . ' was rejected: ' . $this->reason,
            'validation_id'   => $this->validation->id,
            'validation_type' => $this->validationType,
            'reason'          => $this->reason,
            'url'             => route('classroom.dashboard'),
            'icon'            => 'exclamation-circle',
            'priority'        => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
