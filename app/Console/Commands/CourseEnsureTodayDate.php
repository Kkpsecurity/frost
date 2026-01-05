<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class CourseEnsureTodayDate extends Command
{
    protected $signature = 'course:ensure-today-date
        {--simulate-weekend-monday : On weekends, create a Monday-style CourseDate for today (useful for continuous offline-class testing)}';

    protected $description = 'Ensure a CourseDate exists for today (or next class day) so daily classrooms can be created reliably.';

    public function handle(): int
    {
        $tz = 'America/New_York';
        $todayEt = Carbon::now($tz)->startOfDay();

        $simulateWeekendMonday = (bool) $this->option('simulate-weekend-monday');
        $isWeekend = $todayEt->isWeekend();

        // Default behavior: ensure the next scheduled class day exists.
        // If today is a weekend, CourseGenerateDates will advance to Monday.
        $command = 'course:generate-dates';
        $arguments = [
            '--days' => 1,
            '--start-date' => 'today',
        ];

        // Optional behavior: for weekend testing/dev flows where a "new day" class
        // should always exist, create a Monday-style schedule for *today*.
        if ($isWeekend && $simulateWeekendMonday) {
            $arguments = [
                '--simulate-monday-today' => true,
            ];
        }

        $this->line('Ensuring CourseDate exists...');
        $this->line('  Date (ET): ' . $todayEt->toDateString());
        $this->line('  Weekend: ' . ($isWeekend ? 'yes' : 'no'));
        $this->line('  Mode: ' . ($isWeekend && $simulateWeekendMonday ? 'simulate-monday-today' : 'next-class-day'));

        $exitCode = Artisan::call($command, $arguments, $this->output);
        return (int) $exitCode;
    }
}
