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
 * Reminds the student that their class is approaching in 3 days.
 *
 * Sent by the `preparation:send-reminders` scheduled command.
 * Deduplication: one notification per student per course_date_id.
 * Config key: preparation.class_approaching  (user_controllable = true)
 */
class ClassApproachingNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected CourseAuth $courseAuth,
        protected CourseDate $courseDate,
        protected int $daysUntilClass = 3,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'preparation.class_approaching',
            config('user_notifications.notifications.preparation.class_approaching.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.class_approaching.user_controllable', true),
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
        $classDate  = $this->courseDate->starts_at?->format('l, F j, Y');
        $classTime  = $this->courseDate->starts_at?->format('g:i A T');
        $dayLabel   = $this->daysUntilClass === 1 ? '1 day' : $this->daysUntilClass . ' days';

        return (new MailMessage)
            ->subject('Reminder: Your Class Starts in ' . $dayLabel . ' — ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Your class **' . $courseName . '** is coming up in **' . $dayLabel . '**.')
            ->line('📅 **Date:** ' . $classDate)
            ->line('🕐 **Time:** ' . $classTime)
            ->line('Make sure you are prepared: terms accepted, ID and headshot uploaded.')
            ->action('Go to My Dashboard', route('classroom.dashboard'))
            ->line('We look forward to seeing you in class!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $courseName = $this->courseAuth->Course?->title
            ?? $this->courseAuth->Course?->title_long
            ?? 'your course';
        $dayLabel   = $this->daysUntilClass === 1 ? 'in 1 day' : 'in ' . $this->daysUntilClass . ' days';

        return [
            'type'             => 'preparation.class_approaching',
            'title'            => 'Class Starting ' . ucfirst($dayLabel),
            'message'          => $courseName . ' is scheduled ' . $dayLabel . ' on ' . $this->courseDate->starts_at?->format('M j'),
            'course_auth_id'   => $this->courseAuth->id,
            'course_id'        => $this->courseAuth->course_id,
            'course_date_id'   => $this->courseDate->id,
            'days_until_class' => $this->daysUntilClass,
            'class_date'       => $this->courseDate->starts_at?->toDateString(),
            'url'              => route('classroom.dashboard'),
            'icon'             => 'calendar-check',
            'priority'         => 'high',
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
