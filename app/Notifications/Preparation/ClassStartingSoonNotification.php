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
 * Urgent reminder: the student's class begins in approximately 1 hour.
 *
 * Sent by the `preparation:send-reminders` scheduled command (runs every 15 min).
 * Deduplication: one notification per student per course_date_id.
 * Config key: preparation.class_starting_soon  (user_controllable = false — always sent, critical)
 */
class ClassStartingSoonNotification extends Notification implements ShouldQueue
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
            'preparation.class_starting_soon',
            config('user_notifications.notifications.preparation.class_starting_soon.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.class_starting_soon.user_controllable', false),
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
            ->subject('⏰ Class Starting in ~1 Hour: ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your class **' . $courseName . '** starts in approximately **1 hour** at **' . $classTime . '**.')
            ->line('Please log in now and make sure all onboarding steps are complete.')
            ->action('Enter Classroom', route('classroom.dashboard'))
            ->line('The class will begin promptly. Do not be late!');
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
            'type'           => 'preparation.class_starting_soon',
            'title'          => '⏰ Class Starting in ~1 Hour',
            'message'        => $courseName . ' starts at ' . $this->courseDate->starts_at?->format('g:i A T') . '. Log in now!',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_date_id' => $this->courseDate->id,
            'class_date'     => $this->courseDate->starts_at?->toDateString(),
            'url'            => route('classroom.dashboard'),
            'icon'           => 'alarm-clock',
            'priority'       => 'critical',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
