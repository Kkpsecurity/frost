<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamPassedNotification extends Notification implements ShouldQueue
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
            ->where('key', 'notification_exam.exam_passed')
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
        $score = number_format($this->examAuth->score, 1);

        return (new MailMessage)
            ->subject('🎉 Congratulations - You Passed!')
            ->greeting('Excellent Work!')
            ->line('Congratulations! You have successfully passed your exam.')
            ->line('**Course:** ' . $courseName)
            ->line('**Score:** ' . $score . '%')
            ->line('**Required:** ' . $this->examAuth->PassScore . '%')
            ->action('View Results', route('exam.review', $this->examAuth->id))
            ->line('You can now review your exam answers and continue with your certification.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Passed!',
            'message' => 'Congratulations! You passed with a score of ' . number_format($this->examAuth->score, 1) . '%',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'score' => $this->examAuth->score,
            'pass_score' => $this->examAuth->PassScore,
            'icon' => 'trophy',
            'color' => 'success',
            'priority' => 'high',
            'url' => route('exam.review', $this->examAuth->id),
        ];
    }
}
