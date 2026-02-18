<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamReviewAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_exam.exam_review')
            ->where('value', true)
            ->exists();

        if ($emailEnabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('📝 Exam Review Available - ' . $courseName)
            ->greeting('Review Your Exam')
            ->line('Your exam answers and results are now available for review.')
            ->line('**Course:** ' . $courseName)
            ->action('Review Exam', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
            ->line('See which questions you answered correctly and which need more study.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Review Available',
            'message' => 'Your exam results are ready for review. See your answers and feedback.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'icon' => 'search',
            'color' => 'info',
            'priority' => 'low',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
