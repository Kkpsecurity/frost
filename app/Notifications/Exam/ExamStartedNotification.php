<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $examAuth;

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
        $channels = ['database'];

        // Check user preferences for email
        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_exam.exam_started')
            ->where('value', true)
            ->exists();

        if ($emailEnabled) {
            $channels[] = 'mail';
        }

        return $channels;
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
