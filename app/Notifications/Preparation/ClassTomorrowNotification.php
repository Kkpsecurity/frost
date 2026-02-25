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
 * Reminds the student that their class starts tomorrow.
 *
 * Sent by the `preparation:send-reminders` scheduled command.
 * Deduplication: one notification per student per course_date_id.
 * Config key: preparation.class_tomorrow  (user_controllable = false — always sent)
 */
class ClassTomorrowNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected CourseAuth $courseAuth,
        protected CourseDate $courseDate,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'preparation.class_tomorrow',
            config('user_notifications.notifications.preparation.class_tomorrow.channels', ['database', 'mail', 'browser', 'webpush']),
            (bool) config('user_notifications.notifications.preparation.class_tomorrow.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';
        $classTime  = $this->courseDate->starts_at?->format('g:i A T');

        return (new MailMessage)
            ->subject('Your Class is Tomorrow: ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('A reminder that your class **' . $courseName . '** begins **tomorrow** at **' . $classTime . '**.')
            ->line('Please make sure the following are complete before class:')
            ->line('✅ Course terms accepted')
            ->line('✅ Government-issued ID uploaded')
            ->line('✅ Profile headshot ready')
            ->action('Prepare for Class', route('classroom.dashboard'))
            ->line('See you tomorrow!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';

        return [
            'type'           => 'preparation.class_tomorrow',
            'title'          => 'Class Starts Tomorrow',
            'message'        => $courseName . ' starts tomorrow at ' . $this->courseDate->starts_at?->format('g:i A T') . '. Make sure you\'re ready.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_date_id' => $this->courseDate->id,
            'class_date'     => $this->courseDate->starts_at?->toDateString(),
            'url'            => route('classroom.dashboard'),
            'icon'           => 'bell',
            'priority'       => 'high',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get the Web Push payload for this notification.
     * Delivered via the WebPushChannel to subscribed devices.
     */
    public function toWebPush(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';
        $classTime  = $this->courseDate->starts_at?->format('g:i A T');

        return [
            'title' => 'Class Tomorrow: ' . $courseName,
            'body'  => 'Your class starts tomorrow at ' . $classTime . '. Make sure you\'re ready!',
            'url'   => route('classroom.dashboard'),
            'icon'  => '/images/frost-icon-192.png',
            'tag'   => 'class_tomorrow_' . $this->courseDate->id,
        ];
    }
}
