<?php

namespace App\Console\Commands;

use App\Models\CourseAuth;
use App\Notifications\Progress\LicenseRenewalReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sends license renewal reminders for completed (passed) course enrollments.
 *
 * Thresholds: 180 days (6 months), 90 days (3 months), 30 days, 15 days, and 0 (expired yesterday).
 * Deduplication is handled inside LicenseRenewalReminderNotification::shouldSend() — safe to run daily.
 *
 * Schedule: daily at 08:00 ET via Kernel.php:
 *   $schedule->command('licenses:send-renewal-reminders')->dailyAt('08:00')
 *
 * Manual usage:
 *   php artisan licenses:send-renewal-reminders
 *   php artisan licenses:send-renewal-reminders --dry-run
 */
class SendLicenseRenewalReminders extends Command
{
    protected $signature = 'licenses:send-renewal-reminders
                            {--dry-run : Show what would be sent without dispatching notifications}';

    protected $description = 'Send license renewal reminders at 180, 90, 30, 15 days and on expiry';

    /** Days-remaining values to check. 0 = expired (expire_date was yesterday). */
    private const THRESHOLDS = [180, 90, 30, 15, 0];

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $today    = Carbon::today();

        $this->info('Checking license renewal thresholds...');

        if ($isDryRun) {
            $this->warn('DRY RUN — No notifications will be sent');
        }

        $totals = array_fill_keys(self::THRESHOLDS, 0);

        foreach (self::THRESHOLDS as $days) {
            // For 0-day (expired): notify students whose license expired yesterday.
            // This avoids hammering every already-expired enrollment daily.
            $targetDate = $days === 0
                ? $today->copy()->subDay()   // expired yesterday
                : $today->copy()->addDays($days);

            $records = CourseAuth::whereNotNull('completed_at')
                ->where('is_passed', true)
                ->whereNotNull('expire_date')
                ->whereDate('expire_date', $targetDate)
                ->with('Course', 'User')
                ->get();

            foreach ($records as $courseAuth) {
                $user = $courseAuth->User;

                if (! $user) {
                    continue;
                }

                $this->line(sprintf(
                    '[%s-day] CourseAuth #%d | user_id=%d | course=%s | expires=%s',
                    $days,
                    $courseAuth->id,
                    $user->id,
                    $courseAuth->Course?->title ?? 'N/A',
                    $courseAuth->expire_date?->toDateString() ?? 'n/a',
                ));

                if (! $isDryRun) {
                    $user->notify(new LicenseRenewalReminderNotification($courseAuth, $days));
                }

                $totals[$days]++;
            }
        }

        $summary = collect($totals)
            ->map(fn($count, $days) => $days . '-day:' . $count)
            ->implode(', ');

        $this->info('Done — ' . $summary . ($isDryRun ? ' (dry-run)' : ''));

        Log::info('SendLicenseRenewalReminders completed', array_merge(
            ['dry_run' => $isDryRun],
            collect($totals)->mapWithKeys(fn($c, $d) => ["threshold_{$d}" => $c])->toArray(),
        ));

        return self::SUCCESS;
    }
}
