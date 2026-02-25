<?php

namespace App\Notifications\Preparation;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies the student that they must accept the classroom rules before
 * the day's session begins.
 *
 * Fired after the student accepts course terms (sequential onboarding flow).
 * Rules acceptance is per class day — deduplication uses course_date_id.
 * Config key: preparation.rules_required  (user_controllable = false — always sent)
 */
class RulesRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     *
     * @param  int|null  $courseDateId  Used for per-day deduplication checks.
     */
    public function __construct(
        protected CourseAuth $courseAuth,
        protected ?int $courseDateId = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'preparation.rules_required',
            config('user_notifications.notifications.preparation.classroom_rules_required.channels', ['database', 'mail', 'browser']),
            (bool) config('user_notifications.notifications.preparation.classroom_rules_required.user_controllable', false),
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

        return (new MailMessage)
            ->subject('Next Step: Accept Classroom Rules for ' . $courseName)
            ->greeting('Hello, ' . ($notifiable->fname ?? $notifiable->name) . '.')
            ->line('Great — you have accepted the course terms for **' . $courseName . '**!')
            ->line('Your next step is to review and accept the **classroom rules** before the session begins.')
            ->action('Review Classroom Rules', route('classroom.dashboard'))
            ->line('Classroom rules must be accepted each class day before the session starts.');
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
            'type'           => 'preparation.rules_required',
            'title'          => 'Classroom Rules Acceptance Required',
            'message'        => 'Please review and accept the classroom rules for ' . $courseName . ' before your session.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_date_id' => $this->courseDateId,
            'url'            => route('classroom.dashboard'),
            'icon'           => 'clipboard-list',
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
