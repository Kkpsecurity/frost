<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CourseDate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CourseGenerateDates extends Command
{
    protected $signature = 'course:generate-dates
        {--days=5 : Number of upcoming weekdays to generate}
        {--cleanup : Cleanup old CourseDate records (safe mode)}
        {--cleanup-days=30 : Only cleanup records older than this many days}
        {--start-date= : Optional YYYY-MM-DD start date (America/New_York)}
        {--force-weekday= : Optional ISO weekday override (1=Mon..7=Sun)}
        {--simulate-monday-today : Create a Monday schedule for today regardless of weekday}';

    protected $description = 'Generate CourseDate records for upcoming class days (D40 + G28 patterns)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cleanup = (bool) $this->option('cleanup');
        $cleanupDays = max(1, (int) $this->option('cleanup-days'));

        $tz = 'America/New_York';
        $nowEt = Carbon::now($tz);

        $startDateOpt = $this->option('start-date');
        $forceWeekdayOpt = $this->option('force-weekday');
        $simulateMondayToday = (bool) $this->option('simulate-monday-today');

        if ($simulateMondayToday) {
            $startEt = $nowEt->copy()->startOfDay();
            $forceWeekdayOpt = '1';
            $days = 1;
        } elseif (is_string($startDateOpt) && trim($startDateOpt) !== '') {
            $startEt = Carbon::parse(trim($startDateOpt), $tz)->startOfDay();
        } else {
            // Default behavior: called on Sunday night -> generate for upcoming Monday
            $startEt = $nowEt->copy()->startOfDay();
            if ((int) $startEt->isoWeekday() !== 1) {
                $startEt = $startEt->next(Carbon::MONDAY);
            }
        }

        $forceWeekday = null;
        if (is_string($forceWeekdayOpt) && trim($forceWeekdayOpt) !== '') {
            $forceWeekday = (int) trim($forceWeekdayOpt);
            if ($forceWeekday < 1 || $forceWeekday > 7) {
                $this->error('--force-weekday must be 1..7');
                return 1;
            }
        }

        if ($cleanup) {
            // Safe cleanup: only remove inactive records that ended a long time ago.
            $cutoffUtc = Carbon::now('UTC')->subDays($cleanupDays);
            $deleted = CourseDate::query()
                ->where('is_active', false)
                ->where('ends_at', '<', $cutoffUtc)
                ->delete();

            $this->info("Cleanup: deleted {$deleted} inactive records older than {$cleanupDays} days");
        }

        $created = 0;
        $skipped = 0;

        // G28 rules: only Mon–Wed AND only every other week.
        // Anchor the cadence to the most recent existing G28 "Day 1" (unit 16) prior to this run,
        // so weekly scheduler runs don't accidentally generate G28 every week.
        $g28AnchorMondayEt = $this->resolveG28AnchorMondayEt($tz, $startEt);

        $cursorEt = $startEt->copy();
        $generatedDays = 0;

        while ($generatedDays < $days) {
            // Skip weekends unless forced.
            if ($forceWeekday === null) {
                if ($cursorEt->isWeekend()) {
                    $cursorEt->addDay();
                    continue;
                }
            }

            $isoWeekday = $forceWeekday ?? (int) $cursorEt->isoWeekday();

            // D40 pattern: course_unit_id = ISO weekday (Mon=1..Fri=5)
            if ($isoWeekday >= 1 && $isoWeekday <= 5) {
                $created += $this->createCourseDateIfMissing(
                    $cursorEt,
                    $tz,
                    $isoWeekday,
                    8,
                    17,
                    $skipped
                );
            }

            // G28 pattern: only Mon–Wed AND bi-weekly
            if (in_array($isoWeekday, [1, 2, 3], true) && $this->shouldGenerateG28ForDay($cursorEt, $g28AnchorMondayEt)) {
                if ($isoWeekday === 1) {
                    $created += $this->createCourseDateIfMissing($cursorEt, $tz, 16, 8, 17, $skipped);
                } elseif ($isoWeekday === 2) {
                    $created += $this->createCourseDateIfMissing($cursorEt, $tz, 17, 9, 17, $skipped);
                } elseif ($isoWeekday === 3) {
                    $created += $this->createCourseDateIfMissing($cursorEt, $tz, 18, 9, 16, $skipped);
                }
            }

            $generatedDays++;
            $cursorEt->addDay();
        }

        $this->newLine();
        $this->info("Created: {$created}");
        $this->info("Skipped (already existed): {$skipped}");

        return 0;
    }

    /**
     * G28 is bi-weekly. We generate for weeks whose Monday matches the anchor parity.
     */
    private function shouldGenerateG28ForDay(Carbon $dayEt, Carbon $anchorMondayEt): bool
    {
        $weekMondayEt = $dayEt->copy()->startOfDay()->startOfWeek(Carbon::MONDAY);
        $weeksSinceAnchor = $anchorMondayEt->diffInWeeks($weekMondayEt);
        return ($weeksSinceAnchor % 2) === 0;
    }

    private function resolveG28AnchorMondayEt(string $tz, Carbon $startEt): Carbon
    {
        $startWeekMondayEt = $startEt->copy()->startOfDay()->startOfWeek(Carbon::MONDAY);
        $startWeekMondayUtcIso = $startWeekMondayEt->copy()->tz('UTC')->toIso8601String();

        // Find the most recent G28 Day 1 (unit 16) that starts before this generation week.
        $existingDay1 = CourseDate::query()
            ->where('course_unit_id', 16)
            ->where('starts_at', '<', $startWeekMondayUtcIso)
            ->orderByDesc('starts_at')
            ->first(['starts_at']);

        if ($existingDay1 && $existingDay1->starts_at) {
            return Carbon::parse($existingDay1->starts_at, 'UTC')
                ->tz($tz)
                ->startOfDay()
                ->startOfWeek(Carbon::MONDAY);
        }

        // No historical anchor found; treat this generation week as the first (anchor) G28 week.
        return $startWeekMondayEt;
    }

    private function createCourseDateIfMissing(
        Carbon $dayEt,
        string $tz,
        int $courseUnitId,
        int $startHourEt,
        int $endHourEt,
        int &$skipped
    ): int {
        $startsAtUtc = $dayEt->copy()->tz($tz)->hour($startHourEt)->minute(0)->second(0)->tz('UTC');
        $endsAtUtc = $dayEt->copy()->tz($tz)->hour($endHourEt)->minute(0)->second(0)->tz('UTC');

        // Use ISO-8601 strings (with offsets) for Postgres timestamptz safety.
        // This avoids the DB/session timezone affecting comparisons or inserts.
        $startsAtIso = $startsAtUtc->toIso8601String();
        $endsAtIso = $endsAtUtc->toIso8601String();

        // starts_at is timestamptz; compare within a small tolerance window to avoid
        // false misses due to offset/precision differences.
        $existing = CourseDate::query()
            ->where('course_unit_id', $courseUnitId)
            ->whereBetween('starts_at', [
                $startsAtUtc->copy()->subSeconds(30)->toIso8601String(),
                $startsAtUtc->copy()->addSeconds(30)->toIso8601String(),
            ])
            ->orderBy('id')
            ->first();

        if ($existing) {
            $skipped++;
            $this->line("SKIP unit {$courseUnitId} {$startsAtUtc->toDateTimeString()} (id {$existing->id})");
            return 0;
        }

        $created = CourseDate::create([
            'is_active' => true,
            'course_unit_id' => $courseUnitId,
            'starts_at' => $startsAtIso,
            'ends_at' => $endsAtIso,
        ]);

        $this->info("CREATE unit {$courseUnitId} {$startsAtUtc->toDateTimeString()} (id {$created->id})");
        return 1;
    }
}
