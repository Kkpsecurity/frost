<?php

namespace App\Console\Commands;

use App\Models\CourseAuth;
use App\Models\ExamAuth;
use App\Models\User;
use App\Notifications\Exam\ExamReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

/**
 * Sends exam reminder notifications to students whose exam is ready but not yet started.
 *
 * Deduplication: uses the existing `notifications` table — checks whether a reminder
 * with the matching course_auth_id + days_waiting was previously stored.  No extra
 * columns or tables required.
 *
 * Run daily at 09:00 ET via Kernel.php:
 *   $schedule->command('exams:send-reminders')->dailyAt('09:00')
 *
 * Manual usage:
 *   php artisan exams:send-reminders
 *   php artisan exams:send-reminders --dry-run
 *   php artisan exams:send-reminders --days=3 --days=7
 */
class SendExamReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:send-reminders
                            {--days=* : Specific reminder intervals to check (e.g., --days=3 --days=7 --days=14)}
                            {--dry-run : Display what would be done without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for exams that are ready but not yet started';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun     = $this->option('dry-run');
        $reminderDays = $this->option('days') ?: config('user_notifications.exam_reminder_days', [3, 7, 14]);

        $this->info('Checking for exam reminders...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No notifications will be sent');
        }

        $totalReminders = 0;

        foreach ($reminderDays as $days) {
            $days  = (int) $days;
            $count = $this->sendRemindersForInterval($days, $isDryRun);
            $totalReminders += $count;

            if ($count > 0) {
                $this->info("✓ Sent {$count} reminder(s) for exams waiting {$days}+ days");
            }
        }

        if ($totalReminders === 0) {
            $this->info('No reminders needed at this time.');
        } else {
            $this->info("Total reminders sent: {$totalReminders}");
        }

        Log::info('SendExamReminders completed', [
            'total_reminders' => $totalReminders,
            'dry_run'         => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Find and notify eligible students for a given reminder interval.
     *
     * Eligibility rules:
     *  1. CourseAuth is active, not passed, not disabled
     *  2. All lessons are completed — exam is ready to take (ExamReady() === true)
     *  3. No exam is currently in-progress (active timer not expired)
     *  4. At least $days calendar days have passed since the exam became ready
     *  5. A reminder for this exact $days interval has NOT been sent before
     *     (checked via the notifications table)
     */
    protected function sendRemindersForInterval(int $days, bool $isDryRun): int
    {
        $count = 0;

        // Load only the columns/relations we need; StudentUnits is needed by
        // AllLessonsCompleted() / CompletedLessons() via LessonsTrait.
        $courseAuths = CourseAuth::with([
            'User',
            'Course',
            'ExamAuths',
            'StudentUnits.StudentLessons',
            'SelfStudyLessons',
        ])
            ->whereNotNull('start_date')
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->where('is_passed', false)
            ->get();

        foreach ($courseAuths as $courseAuth) {

            // ExamReady() covers: IsActive, AllLessonsCompleted, not in cooldown, not passed.
            if (! $courseAuth->ExamReady()) {
                continue;
            }

            // Skip if an exam is currently in-progress (timer running, not yet submitted).
            $hasActiveExam = $courseAuth->ExamAuths->contains(function (ExamAuth $ea) {
                return $ea->expires_at && ! $ea->completed_at && ! $ea->IsExpired();
            });

            if ($hasActiveExam) {
                continue;
            }

            // Determine when exam became ready (latest lesson/self-study completion date).
            $lastCompletedAt = collect($courseAuth->CompletedLessons())->max(); // Carbon|null

            if (! $lastCompletedAt) {
                continue;
            }

            // Only send once the exam has been sitting ready for AT LEAST $days calendar days.
            // Using diffInDays (truncated) so a cron outage doesn't permanently miss students.
            $daysSinceReady = (int) Carbon::now()->diffInDays(Carbon::parse($lastCompletedAt));

            if ($daysSinceReady < $days) {
                continue;
            }

            $user = $courseAuth->User;

            if (! $user) {
                continue;
            }

            // Deduplicate via the notifications table — check for a previously sent reminder
            // with the same course_auth_id + days_waiting payload.
            // Uses PostgreSQL ->> JSON operator (safe; this project is exclusively PostgreSQL).
            $alreadySent = DatabaseNotification::where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->where('type', ExamReminderNotification::class)
                ->whereRaw("data->>'course_auth_id' = ?", [(string) $courseAuth->id])
                ->whereRaw("data->>'days_waiting' = ?", [(string) $days])
                ->exists();

            if ($alreadySent) {
                continue;
            }

            if ($isDryRun) {
                $this->line(
                    "Would send {$days}-day reminder to {$user->email} "
                        . "(course_auth_id={$courseAuth->id}, ready {$daysSinceReady} days ago) "
                        . "for: " . ($courseAuth->Course->title ?? '?')
                );
            } else {
                $user->notify(new ExamReminderNotification($courseAuth, $days));

                Log::info('Sent exam reminder', [
                    'user_id'        => $user->id,
                    'course_auth_id' => $courseAuth->id,
                    'days_interval'  => $days,
                    'days_since_ready' => $daysSinceReady,
                ]);
            }

            $count++;
        }

        return $count;
    }
}
