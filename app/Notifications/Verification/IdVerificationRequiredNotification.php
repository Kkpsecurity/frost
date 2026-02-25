<?php

namespace App\Notifications\Verification;

use App\Models\CourseAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

/**
 * Sent to the student when they need to upload (or re-upload) their government-issued ID.
 * Config key: verification.id_required  (user_controllable = false — always sent)
 */
class IdVerificationRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected CourseAuth $courseAuth,
        protected ?string $notes = null,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'verification.id_required',
            config('user_notifications.notifications.verification.id_required.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.verification.id_required.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Action Required: Upload Your Government-Issued ID')
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '!')
            ->line('To comply with Florida training regulations, you are required to upload a photo of your **government-issued photo ID** (e.g., driver\'s license or state ID).');

        if ($this->notes) {
            $mail->line('**Note from your instructor:** ' . $this->notes);
        }

        return $mail
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('Please complete this step as soon as possible to continue your course.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'verification.id_required',
            'title'          => 'ID Verification Required',
            'message'        => $this->notes
                ? 'Please upload your government-issued photo ID. Note: ' . $this->notes
                : 'Please upload your government-issued photo ID to verify your identity.',
            'course_auth_id' => $this->courseAuth->id,
            'url'            => route('classroom.dashboard'),
            'icon'           => 'id-card',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
