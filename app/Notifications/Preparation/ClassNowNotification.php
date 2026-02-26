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
 * Fires when the student's class is starting right now (0–10 min window).
 *
 * Sent by the `preparation:send-reminders` scheduled command (runs every 15 min).
 * Deduplication: one notification per student per course_date_id.
 * Config key: preparation.class_now  (user_controllable = false — always sent, critical)
 */
class ClassNowNotification extends Notification implements ShouldQueue
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
            'preparation.class_now',
            config('user_notifications.notifications.preparation.class_now.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.class_now.user_controllable', false),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';
        $classTime  = $this->courseDate->starts_at?->format('g:i A T');

        return (new MailMessage)
            ->subject('🚀 Class Is Starting Now: ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your class **' . $courseName . '** is starting **right now** at **' . $classTime . '**.')
            ->line('Log in immediately and enter the classroom.')
            ->action('Enter Classroom Now', route('classroom.dashboard'));
    }

    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';

        return [
            'type'           => 'preparation.class_now',
            'title'          => '🚀 Class Is Starting Now',
            'message'        => $courseName . ' is starting now at ' . $this->courseDate->starts_at?->format('g:i A T') . '. Enter the classroom immediately!',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_date_id' => $this->courseDate->id,
            'class_date'     => $this->courseDate->starts_at?->toDateString(),
            'url'            => route('classroom.dashboard'),
            'icon'           => 'rocket',
            'priority'       => 'critical',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
