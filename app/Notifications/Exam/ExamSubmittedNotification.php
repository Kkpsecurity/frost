<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_submitted',
            config('user_notifications.notifications.exams.exam_submitted.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_submitted.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('📝 Exam Submitted - ' . $courseName)
            ->greeting('Exam Submitted!')
            ->line('Your exam has been submitted for grading.')
            ->line('**Course:** ' . $courseName)
            ->line('Results will be available shortly.')
            ->action('View Dashboard', route('classroom.dashboard'))
            ->line('Thank you for completing your exam!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Submitted',
            'message' => 'Your exam has been submitted for grading. Results coming soon.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'icon' => 'check-circle',
            'color' => 'info',
            'priority' => 'medium',
            'url' => route('classroom.dashboard'),
        ];
    }
}
