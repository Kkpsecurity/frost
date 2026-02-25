<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamAuthorizedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;

    /**
     * Create a new notification instance.
     */
    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_authorized',
            config('user_notifications.notifications.exams.exam_authorized.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_authorized.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('✅ Exam Authorized - ' . $courseName)
            ->greeting('Ready to Start!')
            ->line('Your exam has been authorized and you can now begin.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Remaining:** ' . $this->examAuth->AttemptsRemaining())
            ->action('Start Exam', route('classroom.course', $this->examAuth->course_auth_id))
            ->line('The timer will begin as soon as you start the exam.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Authorized',
            'message' => 'Your exam is ready to start. Click to begin.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'icon' => 'play-circle',
            'color' => 'primary',
            'priority' => 'high',
            'url' => route('classroom.course', $this->examAuth->course_auth_id),
        ];
    }
}
