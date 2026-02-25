<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent 7 days before a student's course enrollment expires.
 * Non-controllable (high priority, database+mail+browser).
 */
class CourseExpiring7DaysNotification extends Notification implements ShouldQueue
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
            'progress.expiring_7days',
            config('user_notifications.notifications.progress.course_expiring_7days.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.progress.course_expiring_7days.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course     = $this->courseAuth->GetCourse();
        $expireDate = $this->courseAuth->expire_date
            ? \Carbon\Carbon::parse($this->courseAuth->expire_date)->format('F j, Y')
            : '7 days from now';

        return (new MailMessage)
            ->subject('⚠️ Course Access Expiring in 7 Days — ' . $course->name)
            ->greeting('Hello, ' . ($notifiable->fname ?? 'Student') . '.')
            ->line('**Urgent:** Your access to **' . $course->name . '** will expire on **' . $expireDate . '** — only 7 days remaining.')
            ->line('Please log in and complete your course before it expires.')
            ->action('Complete My Course Now', url('/classroom'))
            ->line('If you need an extension, please contact support immediately.');
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->GetCourse()->name ?? 'your course';
        $expireDate = $this->courseAuth->expire_date
            ? \Carbon\Carbon::parse($this->courseAuth->expire_date)->format('Y-m-d')
            : null;

        return [
            'type'           => 'progress.expiring_7days',
            'title'          => 'Course Expiring in 7 Days',
            'message'        => 'Urgent: ' . $courseName . ' access expires in 7 days.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'expire_date'    => $expireDate,
            'days_remaining' => 7,
            'url'            => '/classroom',
            'icon'           => 'alert-triangle',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Deduplicate: only send one 7-day expiry notification per CourseAuth.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.expiring_7days'])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->exists();
    }
}
