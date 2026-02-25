<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a student passes their course (CourseAuth::MarkCompleted(true)).
 */
class CourseCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Congratulations! You Passed — ' . $course->name)
            ->greeting('Congratulations, ' . ($notifiable->fname ?? 'Student') . '!')
            ->line('You have successfully completed **' . $course->name . '**.')
            ->line('Your certificate will be available shortly.')
            ->action('View My Certificate', url('/classroom'))
            ->line('Thank you for completing your training with us.');
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->GetCourse()->name ?? 'your course';

        return [
            'type'           => 'progress.course_completed',
            'title'          => 'Course Completed!',
            'message'        => 'Congratulations! You have successfully completed ' . $courseName . '.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'url'            => '/classroom',
            'icon'           => 'award',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate course-completed notifications per CourseAuth.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.course_completed'])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->exists();
    }
}
