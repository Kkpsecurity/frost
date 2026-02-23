<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamStartedNotification extends Notification implements ShouldQueue
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
            'notification_exam.exam_started',
            config('user_notifications.notifications.exams.exam_started.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_started.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';
        $timeLimit = $this->examAuth->TimeLimit ?? 'N/A';

        return (new MailMessage)
            ->subject('⏱️ Exam Started - ' . $courseName)
            ->greeting('Timer Started!')
            ->line('Your exam timer has begun.')
            ->line('**Course:** ' . $courseName)
            ->line('**Time Limit:** ' . $timeLimit . ' minutes')
            ->action('Continue Exam', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
            ->line('Answer all questions before time expires. Good luck!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Started',
            'message' => 'Your exam timer has started. Complete all questions before time expires.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'time_limit' => $this->examAuth->TimeLimit,
            'icon' => 'stopwatch',
            'color' => 'info',
            'priority' => 'medium',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
