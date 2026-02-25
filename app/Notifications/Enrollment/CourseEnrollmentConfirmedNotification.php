<?php

namespace App\Notifications\Enrollment;

use App\Models\CourseAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class CourseEnrollmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    protected CourseAuth $courseAuth;

    /**
     * Create a new notification instance.
     */
    public function __construct(CourseAuth $courseAuth)
    {
        $this->courseAuth = $courseAuth;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'enrollment.confirmed',
            config('user_notifications.notifications.enrollment.course_enrollment_confirmed.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.enrollment.course_enrollment_confirmed.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->courseAuth->GetCourse();

        return (new MailMessage)
            ->subject('You\'re Enrolled! — ' . $course->name)
            ->greeting('Welcome, ' . $notifiable->fname . '!')
            ->line('Your enrollment in **' . $course->name . '** has been confirmed.')
            ->line('**Enrollment Date:** ' . $this->courseAuth->created_at->format('M j, Y'))
            ->line('You now have access to your student portal where you can view your course details and prepare for class.')
            ->action('Go to My Classroom', route('classroom.dashboard'))
            ->line('If you have any questions about your enrollment, please contact our support team.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $course = $this->courseAuth->GetCourse();

        return [
            'type'           => 'enrollment.confirmed',
            'title'          => 'Enrollment Confirmed',
            'message'        => 'You have been successfully enrolled in ' . $course->name . '.',
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $course->name,
            'url'            => route('classroom.dashboard'),
            'icon'           => 'check-circle',
            'priority'       => 'high',
        ];
    }

    /**
     * Get the array representation (used by toDatabase).
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
