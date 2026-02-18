<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $examAuth;
    protected $daysWaiting;

    /**
     * Create a new notification instance.
     */
    public function __construct(ExamAuth $examAuth, int $daysWaiting = 3)
    {
        $this->examAuth = $examAuth;
        $this->daysWaiting = $daysWaiting;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences for email
        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_exam.exam_reminder')
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

        return (new MailMessage)
            ->subject('⏰ Exam Reminder - ' . $courseName)
            ->greeting('Don\'t Forget Your Exam!')
            ->line('Your exam has been ready for ' . $this->daysWaiting . ' days but you haven\'t started it yet.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Available:** ' . $this->examAuth->AttemptsRemaining())
            ->action('Start Exam Now', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
            ->line('Complete your certification by taking the exam today!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Reminder',
            'message' => 'Your exam has been ready for ' . $this->daysWaiting . ' days. Don\'t forget to complete it!',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'days_waiting' => $this->daysWaiting,
            'icon' => 'clock',
            'color' => 'warning',
            'priority' => 'medium',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
