<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\StudentUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CourseDedupeDates extends Command
{
    protected $signature = 'course:dedupe-dates
        {--dry-run : Show what would be deleted without deleting}
        {--all : Scan all dates (default: only from start of current month UTC)}
        {--from-date= : Optional YYYY-MM-DD lower bound (UTC date) overrides default scope}
        {--to-date= : Optional YYYY-MM-DD upper bound (UTC date)}';

    protected $description = 'Remove duplicate CourseDate rows (same course_unit_id + starts_at), keeping the best candidate and deleting unreferenced extras';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $scanAll = (bool) $this->option('all');

        $fromDateOpt = $this->option('from-date');
        $toDateOpt = $this->option('to-date');

        $fromUtc = null;
        if (is_string($fromDateOpt) && trim($fromDateOpt) !== '') {
            $fromUtc = Carbon::parse(trim($fromDateOpt), 'UTC')->startOfDay();
        } elseif (! $scanAll) {
            $fromUtc = Carbon::now('UTC')->startOfMonth();
        }

        $toUtc = null;
        if (is_string($toDateOpt) && trim($toDateOpt) !== '') {
            $toUtc = Carbon::parse(trim($toDateOpt), 'UTC')->endOfDay();
        }

        $baseQuery = CourseDate::query();
        if ($fromUtc) {
            $baseQuery->where('starts_at', '>=', $fromUtc->toIso8601String());
        }
        if ($toUtc) {
            $baseQuery->where('starts_at', '<=', $toUtc->toIso8601String());
        }

        $groups = (clone $baseQuery)
            ->select([
                'course_unit_id',
                'starts_at',
                DB::raw('COUNT(*) as dup_count'),
            ])
            ->groupBy('course_unit_id', 'starts_at')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('starts_at')
            ->get();

        if ($groups->isEmpty()) {
            $this->info('No duplicates found in the selected range.');
            return 0;
        }

        $this->info('Duplicate groups found: ' . $groups->count());
        $this->info($dryRun ? 'DRY RUN (no deletes will occur)' : 'LIVE RUN (will delete duplicates)');
        $this->newLine();

        $deletedTotal = 0;
        $skippedReferenced = 0;

        foreach ($groups as $group) {
            $courseUnitId = (int) $group->course_unit_id;
            $startsAt = $group->starts_at; // timestamptz value

            $rows = CourseDate::query()
                ->where('course_unit_id', $courseUnitId)
                ->where('starts_at', $startsAt)
                ->orderByDesc('is_active')
                ->orderBy('id')
                ->get();

            if ($rows->count() < 2) {
                continue;
            }

            // Pick keep candidate:
            // 1) any row with references (inst_units/student_units/classrooms)
            // 2) otherwise active row
            // 3) otherwise lowest id
            $keep = null;
            foreach ($rows as $row) {
                if ($this->hasReferences($row->id)) {
                    $keep = $row;
                    break;
                }
            }
            if (! $keep) {
                $keep = $rows->sortByDesc('is_active')->sortBy('id')->first();
            }

            $toDelete = $rows->where('id', '!=', $keep->id)->values();

            $this->line(sprintf(
                'Group unit=%d starts_at=%s dup_count=%d keep_id=%d delete_ids=[%s]',
                $courseUnitId,
                (string) $startsAt,
                (int) $rows->count(),
                (int) $keep->id,
                $toDelete->pluck('id')->implode(',')
            ));

            foreach ($toDelete as $delRow) {
                if ($this->hasReferences($delRow->id)) {
                    $skippedReferenced++;
                    $this->warn("  - SKIP referenced id {$delRow->id}");
                    continue;
                }

                if (! $dryRun) {
                    $delRow->delete();
                }
                $deletedTotal++;
            }
        }

        $this->newLine();
        $this->info('Deleted rows: ' . $deletedTotal);
        if ($skippedReferenced > 0) {
            $this->warn('Skipped referenced rows: ' . $skippedReferenced);
        }

        return 0;
    }

    private function hasReferences(int $courseDateId): bool
    {
        return InstUnit::query()->where('course_date_id', $courseDateId)->exists()
            || StudentUnit::query()->where('course_date_id', $courseDateId)->exists();
    }
}
