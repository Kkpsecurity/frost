<?php

namespace App\Console\Commands;

use App\Events\Exam\ExamAuthorized;
use App\Models\CourseAuth;
use App\Models\ExamAuth;
use App\Notifications\Exam\ExamReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendExamReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:send-reminders
                            {--days=* : Specific reminder intervals to check (e.g., 3 7 14)}
                            {--dry-run : Display what would be done without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for exams that are ready but not started';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $reminderDays = $this->option('days') ?: config('user_notifications.exam_reminder_days', [3, 7, 14]);

        $this->info('Checking for exam reminders...');
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No notifications will be sent');
        }

        $totalReminders = 0;

        foreach ($reminderDays as $days) {
            $days = (int) $days;
            $count = $this->sendRemindersForInterval($days, $isDryRun);
            $totalReminders += $count;

            if ($count > 0) {
                $this->info("✓ Sent {$count} reminder(s) for exams waiting {$days} days");
            }
        }

        if ($totalReminders === 0) {
            $this->info('No reminders needed at this time.');
        } else {
            $this->info("Total reminders sent: {$totalReminders}");
        }

        Log::info('SendExamReminders completed', [
            'total_reminders' => $totalReminders,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Send reminders for a specific day interval
     */
    protected function sendRemindersForInterval(int $days, bool $isDryRun): int
    {
        $targetDate = Carbon::now()->subDays($days)->startOfDay();
        $count = 0;

        // Find course_auths where all lessons are completed
        // and exam is ready but not started
        $courseAuths = CourseAuth::with(['User', 'Course.Exam', 'ExamAuths'])
            ->whereNotNull('start_date')
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->where('is_passed', false)
            ->get();

        foreach ($courseAuths as $courseAuth) {
            // Skip if course not active
            if (!$courseAuth->IsActive()) {
                continue;
            }

            // Check if all lessons completed
            if (!$courseAuth->AllLessonsCompleted()) {
                continue;
            }

            // Check exam readiness
            $examReadinessReason = $courseAuth->ExamReadinessFailureReason();
            if ($examReadinessReason !== null) {
                continue; // Not ready
            }

            // Get latest exam auth
            $latestExam = $courseAuth->LatestExamAuth();

            // If exam already started or completed, skip
            if ($latestExam && ($latestExam->expires_at || $latestExam->completed_at)) {
                continue;
            }

            // Calculate when exam became ready (when all lessons completed)
            // Use the most recent StudentLesson completion as proxy
            $lastLessonCompleted = \App\Models\StudentLesson::whereIn(
                'student_unit_id',
                $courseAuth->StudentUnits->pluck('id')
            )
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            if (!$lastLessonCompleted || !$lastLessonCompleted->completed_at) {
                continue;
            }

            $examReadyDate = Carbon::parse($lastLessonCompleted->completed_at)->startOfDay();

            // Check if this is exactly N days ago
            if ($examReadyDate->ne($targetDate)) {
                continue;
            }

            // Check if reminder already sent for this interval
            // Use exam_auths meta or check notifications table
            if ($latestExam) {
                $reminderKey = "reminder_sent_{$days}d";
                $meta = $latestExam->meta ?? [];

                if (isset($meta[$reminderKey]) && $meta[$reminderKey] === true) {
                    continue; // Already sent
                }
            }

            // Send reminder
            $user = $courseAuth->User;
            if (!$user) {
                continue;
            }

            if ($isDryRun) {
                $this->line("Would send {$days}-day reminder to {$user->email} for course: {$courseAuth->Course->title}");
            } else {
                // Create or get exam auth for reminder tracking
                if (!$latestExam) {
                    $latestExam = ExamAuth::create([
                        'course_auth_id' => $courseAuth->id,
                    ]);
                    $latestExam->refresh();

                    // Dispatch ExamAuthorized event for initial notification
                    event(new ExamAuthorized($latestExam));
                }

                // Send reminder
                $user->notify(new ExamReminderNotification($latestExam, $days));

                // Mark as sent
                $meta = $latestExam->meta ?? [];
                $meta["reminder_sent_{$days}d"] = true;
                $latestExam->forceFill(['meta' => $meta])->save();

                Log::info("Sent {$days}-day exam reminder", [
                    'user_id' => $user->id,
                    'course_auth_id' => $courseAuth->id,
                    'exam_auth_id' => $latestExam->id,
                ]);
            }

            $count++;
        }

        return $count;
    }
}
