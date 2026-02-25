<?php

namespace App\Notifications\Verification;

use App\Models\CourseAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

/**
 * Sent to the student when they need to take (or re-take) their daily headshot.
 * Config key: verification.headshot_required  (user_controllable = false — always sent)
 */
class HeadshotRequiredNotification extends Notification implements ShouldQueue
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
            'verification.headshot_required',
            config('user_notifications.notifications.verification.headshot_required.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.verification.headshot_required.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Action Required: Daily Headshot Needed')
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '!')
            ->line('Your instructor requires a **daily headshot** to verify your attendance and identity during today\'s class session.');

        if ($this->notes) {
            $mail->line('**Note from your instructor:** ' . $this->notes);
        }

        return $mail
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('Please take your headshot photo as soon as possible to continue participating in your class.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'verification.headshot_required',
            'title'          => 'Daily Headshot Required',
            'message'        => $this->notes
                ? 'Your instructor requires a daily headshot. Note: ' . $this->notes
                : 'Please take your daily headshot photo to continue with today\'s class.',
            'course_auth_id' => $this->courseAuth->id,
            'url'            => route('classroom.dashboard'),
            'icon'           => 'camera',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
