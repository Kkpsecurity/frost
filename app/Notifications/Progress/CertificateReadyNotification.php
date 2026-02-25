<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a student's certificate is ready to download.
 * Triggered after a passed CourseCompleted event.
 */
class CertificateReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly CourseAuth $courseAuth,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'progress.certificate_ready',
            config('user_notifications.notifications.progress.certificate_ready.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.progress.certificate_ready.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->courseAuth->GetCourse();

        return (new MailMessage)
            ->subject('Your Certificate Is Ready — ' . $course->name)
            ->greeting('Congratulations, ' . ($notifiable->fname ?? 'Student') . '!')
            ->line('Your certificate of completion for **' . $course->name . '** is now ready to download.')
            ->line('Sign in to your student dashboard to access and print your certificate.')
            ->action('Download Certificate', url('/classroom/certificate/' . $this->courseAuth->id))
            ->line('Keep up the great work!');
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->GetCourse()->name ?? 'your course';

        return [
            'type'           => 'progress.certificate_ready',
            'title'          => 'Certificate Ready',
            'message'        => 'Your certificate for ' . $courseName . ' is ready to download.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'url'            => '/classroom/certificate/' . $this->courseAuth->id,
            'icon'           => 'award',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate certificate-ready notifications per CourseAuth.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.certificate_ready'])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->exists();
    }
}
