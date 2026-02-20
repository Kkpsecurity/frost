<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamReadyNotification extends Notification implements ShouldQueue
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
            'notification_exam.exam_ready',
            config('user_notifications.notifications.exams.exam_ready.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_ready.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('🎓 Exam Ready - ' . $courseName)
            ->greeting('Congratulations!')
            ->line('You have completed all lessons and your exam is now available.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Allowed:** ' . $this->examAuth->MaxAttempts)
            ->action('Start Exam', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
            ->line('Good luck on your exam! Take your time and read each question carefully.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Ready',
            'message' => 'All lessons complete! Your exam is now available.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'icon' => 'clipboard-check',
            'color' => 'success',
            'priority' => 'high',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
