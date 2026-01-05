<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CourseDate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CourseActivateDates extends Command
{
    protected $signature = 'course:activate-dates
        {--date= : Optional target date (YYYY-MM-DD) evaluated in America/New_York}
        {--dry-run : Show what would be activated without writing}
        {--tz=America/New_York : Timezone used to interpret the target day}';

    protected $description = 'Activate CourseDate records for a given day (sets is_active=true), typically run daily at 6:00 AM ET.';

    public function handle(): int
    {
        $tz = (string) $this->option('tz') ?: 'America/New_York';
        $dryRun = (bool) $this->option('dry-run');

        $dateOpt = $this->option('date');
        if (is_string($dateOpt) && trim($dateOpt) !== '') {
            $targetDayEt = Carbon::parse(trim($dateOpt), $tz)->startOfDay();
        } else {
            $targetDayEt = Carbon::now($tz)->startOfDay();
        }

        // Use a timezone-safe window for the target day.
        $startUtc = $targetDayEt->copy()->tz('UTC');
        $endUtc = $targetDayEt->copy()->endOfDay()->tz('UTC');

        $this->line('Activating CourseDates...');
        $this->line('  Day (ET):  ' . $targetDayEt->toDateString());
        $this->line('  Window:    ' . $startUtc->toIso8601String() . ' -> ' . $endUtc->toIso8601String());
        $this->line('  Dry-run:   ' . ($dryRun ? 'yes' : 'no'));

        $query = CourseDate::query()
            ->where('is_active', false)
            ->whereBetween('starts_at', [
                $startUtc->toIso8601String(),
                $endUtc->toIso8601String(),
            ]);

        $toActivate = (int) $query->count();

        if ($toActivate === 0) {
            $this->info('Activated: 0 (nothing to do)');
            return 0;
        }

        if ($dryRun) {
            $ids = $query->orderBy('id')->limit(50)->pluck('id')->all();
            $this->info('Would activate: ' . $toActivate);
            $this->line('Sample ids: ' . implode(', ', $ids) . ($toActivate > 50 ? ' ...' : ''));
            return 0;
        }

        $activated = (int) $query->update(['is_active' => true]);
        $this->info("Activated: {$activated}");

        return 0;
    }
}
