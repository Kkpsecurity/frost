<?php

namespace App\Notifications\Preparation;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires ~15 minutes after the student's class has started and they still haven't joined.
 *
 * Sent by the `preparation:send-reminders` scheduled command (runs every 15 min).
 * Deduplication: one notification per student per course_date_id.
 * Config key: preparation.class_late  (user_controllable = false — always sent, critical)
 */
class ClassLateNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected CourseAuth $courseAuth,
        protected CourseDate $courseDate,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'preparation.class_late',
            config('user_notifications.notifications.preparation.class_late.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.class_late.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';
        $classTime  = $this->courseDate->starts_at?->format('g:i A T');

        return (new MailMessage)
            ->subject('⚠️ You Are Late — Class Already Started: ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your class **' . $courseName . '** started at **' . $classTime . '** and is already underway.')
            ->line('Please log in now and join the classroom — attendance is being tracked.')
            ->action('Join Classroom Now', route('classroom.dashboard'));
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';

        return [
            'type'           => 'preparation.class_late',
            'title'          => '⚠️ You Are Late to Class',
            'message'        => $courseName . ' started at ' . $this->courseDate->starts_at?->format('g:i A T') . '. Class is already in session — join now!',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_date_id' => $this->courseDate->id,
            'class_date'     => $this->courseDate->starts_at?->toDateString(),
            'url'            => route('classroom.dashboard'),
            'icon'           => 'exclamation-triangle',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
