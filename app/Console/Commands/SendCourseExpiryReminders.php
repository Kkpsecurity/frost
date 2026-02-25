<?php

namespace App\Console\Commands;

use App\Events\Progress\CourseExpiringSoon;
use App\Models\CourseAuth;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Dispatches CourseExpiringSoon events for enrollments expiring in 30 or 7 days.
 *
 * Listeners handle which notification (30-day vs 7-day) gets sent.
 * Deduplication is handled inside each notification's shouldSend() via the
 * `notifications` table — safe to run once per day.
 *
 * Schedule: daily at 08:00 ET via Kernel.php:
 *   $schedule->command('progress:send-expiry-reminders')->dailyAt('08:00')
 *
 * Manual usage:
 *   php artisan progress:send-expiry-reminders
 *   php artisan progress:send-expiry-reminders --dry-run
 */
class SendCourseExpiryReminders extends Command
{
    protected $signature = 'progress:send-expiry-reminders
                            {--dry-run : Display what would be dispatched without actually sending}';

    protected $description = 'Dispatch CourseExpiringSoon events for enrollments expiring in 30 or 7 days';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('Checking for expiring course enrollments...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No events will be dispatched');
        }

        $total30 = 0;
        $total7  = 0;

        // 30-day window
        $expiring30 = CourseAuth::whereNull('completed_at')
            ->whereNull('disabled_at')
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', Carbon::today()->addDays(30))
            ->get();

        foreach ($expiring30 as $courseAuth) {
            $this->line(sprintf(
                '[30-day] CourseAuth #%d | user_id=%d | course_id=%d | expires=%s',
                $courseAuth->id,
                $courseAuth->user_id,
                $courseAuth->course_id,
                $courseAuth->expire_date?->toDateString() ?? 'n/a',
            ));

            if (! $isDryRun) {
                event(new CourseExpiringSoon($courseAuth, 30));
            }

            $total30++;
        }

        // 7-day window
        $expiring7 = CourseAuth::whereNull('completed_at')
            ->whereNull('disabled_at')
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', Carbon::today()->addDays(7))
            ->get();

        foreach ($expiring7 as $courseAuth) {
            $this->line(sprintf(
                '[7-day]  CourseAuth #%d | user_id=%d | course_id=%d | expires=%s',
                $courseAuth->id,
                $courseAuth->user_id,
                $courseAuth->course_id,
                $courseAuth->expire_date?->toDateString() ?? 'n/a',
            ));

            if (! $isDryRun) {
                event(new CourseExpiringSoon($courseAuth, 7));
            }

            $total7++;
        }

        $this->info(sprintf(
            'Done — 30-day: %d, 7-day: %d%s',
            $total30,
            $total7,
            $isDryRun ? ' (dry-run, no events dispatched)' : '',
        ));

        Log::info('SendCourseExpiryReminders completed', [
            'dry_run'     => $isDryRun,
            'expiring_30' => $total30,
            'expiring_7'  => $total7,
        ]);

        return self::SUCCESS;
    }
}
