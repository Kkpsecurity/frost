<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent 30 days before a student's course enrollment expires.
 * User-controllable (medium priority, database+mail).
 */
class CourseExpiring30DaysNotification extends Notification implements ShouldQueue
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
            'progress.expiring_30days',
            config('user_notifications.notifications.progress.course_expiring_30days.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.progress.course_expiring_30days.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course     = $this->courseAuth->GetCourse();
        $expireDate = $this->courseAuth->expire_date
            ? \Carbon\Carbon::parse($this->courseAuth->expire_date)->format('F j, Y')
            : '30 days from now';

        return (new MailMessage)
            ->subject('Course Access Expiring Soon — ' . $course->name)
            ->greeting('Hello, ' . ($notifiable->fname ?? 'Student') . '.')
            ->line('Your access to **' . $course->name . '** will expire on **' . $expireDate . '** (30 days from now).')
            ->line('Be sure to complete your course before it expires.')
            ->action('Go to My Course', url('/classroom'))
            ->line('Thank you for learning with us.');
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->GetCourse()->name ?? 'your course';
        $expireDate = $this->courseAuth->expire_date
            ? \Carbon\Carbon::parse($this->courseAuth->expire_date)->format('Y-m-d')
            : null;

        return [
            'type'           => 'progress.expiring_30days',
            'title'          => 'Course Expiring in 30 Days',
            'message'        => $courseName . ' access expires in 30 days. Complete your course soon.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'expire_date'    => $expireDate,
            'days_remaining' => 30,
            'url'            => '/classroom',
            'icon'           => 'clock',
            'priority'       => 'medium',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Deduplicate: only send one 30-day expiry notification per CourseAuth.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.expiring_30days'])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->exists();
    }
}
