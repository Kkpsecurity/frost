<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a student fails their course (exceeded exam attempts).
 */
class CourseFailedNotification extends Notification implements ShouldQueue
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
            'progress.course_completed',
            config('user_notifications.notifications.progress.course_completed.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.progress.course_completed.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->courseAuth->GetCourse();

        return (new MailMessage)
            ->subject('Course Result — ' . $course->name)
            ->greeting('Hello, ' . ($notifiable->fname ?? 'Student') . '.')
            ->line('Unfortunately, you did not pass **' . $course->name . '**.')
            ->line('You have exceeded the maximum number of exam attempts.')
            ->line('Please contact support if you have questions about retake options.')
            ->action('Contact Support', url('/'))
            ->line('Thank you.');
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->GetCourse()->name ?? 'your course';

        return [
            'type'           => 'progress.course_failed',
            'title'          => 'Course Not Passed',
            'message'        => 'You have not passed ' . $courseName . '. Please contact support for next steps.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'url'            => '/classroom',
            'icon'           => 'x-circle',
            'priority'       => 'high',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate course-failed notifications per CourseAuth.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.course_failed'])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->exists();
    }
}
