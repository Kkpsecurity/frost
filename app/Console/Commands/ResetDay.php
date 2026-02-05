<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CourseDate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ResetDay extends Command
{
    protected $signature = 'classrooms:reset-day
                            {--course_date_id= : CourseDate id to reset (defaults to today)}
                            {--user_id= : Optional user id; when set, only student-owned data is reset}
                            {--reset-classroom : Also reset shared classroom state (inst lessons + breaks). Defaults to ON unless --user_id is provided}
                            {--delete-student-units : Also delete student_unit rows (dangerous; requires --user_id unless you intend to wipe ALL students for the day)}
                            {--yes : Actually execute (otherwise dry-run)}';

    protected $description = 'Reset today\'s classroom/student state for re-testing (dry-run by default).';

    public function handle(): int
    {
        if (App::environment('production')) {
            $this->error('Refusing to run in production');
            return 1;
        }

        $courseDateId = (int) ($this->option('course_date_id') ?? 0);
        $userId = (int) ($this->option('user_id') ?? 0);
        $doExecute = (bool) $this->option('yes');
        $deleteStudentUnits = (bool) $this->option('delete-student-units');

        if (!$courseDateId) {
            $courseDateId = (int) (DB::table('course_dates')
                ->whereDate('starts_at', today())
                ->orderBy('starts_at', 'asc')
                ->value('id') ?? 0);
        }

        if (!$courseDateId) {
            $this->error('Could not determine course_date_id for today. Pass --course_date_id=...');
            return 1;
        }

        $courseDate = CourseDate::find($courseDateId);
        if (!$courseDate) {
            $this->error("course_date_id {$courseDateId} not found.");
            return 1;
        }

        $instUnitId = $courseDate->InstUnit?->id;

        $resetClassroomRequested = (bool) $this->option('reset-classroom');
        $resetClassroom = $userId ? $resetClassroomRequested : true;

        if ($deleteStudentUnits && !$userId) {
            $this->warn('--delete-student-units without --user_id will delete ALL student_unit rows for this course_date.');
        }

        $this->line('=== RESET DAY (for testing) ===');
        $this->line("course_date_id: {$courseDateId}");
        $this->line('user_id filter: ' . ($userId ? (string) $userId : 'ALL'));
        $this->line('reset-classroom: ' . ($resetClassroom ? 'YES' : 'NO'));
        $this->line('delete-student-units: ' . ($deleteStudentUnits ? 'YES' : 'NO'));
        $this->line('mode: ' . ($doExecute ? 'EXECUTE' : 'DRY-RUN (add --yes to run)'));
        $this->line(str_repeat('=', 60));

        // StudentUnit IDs for this course date (optionally filtered by user)
        $studentUnitIdsQuery = DB::table('student_unit')
            ->where('student_unit.course_date_id', $courseDateId);

        if ($userId) {
            $studentUnitIdsQuery = $studentUnitIdsQuery
                ->join('course_auths', 'student_unit.course_auth_id', '=', 'course_auths.id')
                ->where('course_auths.user_id', $userId)
                ->select('student_unit.id');
        } else {
            $studentUnitIdsQuery = $studentUnitIdsQuery->select('student_unit.id');
        }

        $studentUnitIds = $studentUnitIdsQuery->pluck('id')->map(fn($v) => (int) $v)->values();

        $studentLessonCount = $studentUnitIds->isEmpty()
            ? 0
            : (int) DB::table('student_lesson')->whereIn('student_unit_id', $studentUnitIds)->count();

        $studentActivityCount = (int) DB::table('student_activity')
            ->whereDate('created_at', today())
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->count();

        $instLessonIds = collect();
        $instLessonCount = 0;
        $instBreakCount = 0;

        if ($resetClassroom && $instUnitId) {
            $instLessonIds = DB::table('inst_lesson')->where('inst_unit_id', $instUnitId)->pluck('id');
            $instLessonCount = (int) $instLessonIds->count();

            if ($instLessonIds->isNotEmpty()) {
                $instBreakCount = (int) DB::table('inst_lesson_breaks')->whereIn('inst_lesson_id', $instLessonIds)->count();
            }
        }

        $this->line('Will delete:');
        $this->line("- student_activity rows: {$studentActivityCount}");
        $this->line("- student_lesson rows:   {$studentLessonCount}");
        if ($resetClassroom) {
            $this->line("- inst_lesson_breaks:    {$instBreakCount}");
            $this->line("- inst_lesson rows:      {$instLessonCount}");
        }
        if ($deleteStudentUnits) {
            $this->line('- student_unit rows:    ' . (int) $studentUnitIds->count());
        }

        if (!$doExecute) {
            $this->info('Dry-run complete. Re-run with --yes to execute.');
            return 0;
        }

        DB::beginTransaction();
        try {
            $deletedStudentActivity = DB::table('student_activity')
                ->whereDate('created_at', today())
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->delete();

            $deletedStudentLessons = 0;
            if ($studentUnitIds->isNotEmpty()) {
                $deletedStudentLessons = DB::table('student_lesson')
                    ->whereIn('student_unit_id', $studentUnitIds)
                    ->delete();
            }

            $deletedBreaks = 0;
            $deletedInstLessons = 0;
            if ($resetClassroom && $instUnitId) {
                if ($instLessonIds->isNotEmpty()) {
                    $deletedBreaks = DB::table('inst_lesson_breaks')
                        ->whereIn('inst_lesson_id', $instLessonIds)
                        ->delete();
                }

                $deletedInstLessons = DB::table('inst_lesson')
                    ->where('inst_unit_id', $instUnitId)
                    ->delete();
            }

            $deletedStudentUnits = 0;
            if ($deleteStudentUnits && $studentUnitIds->isNotEmpty()) {
                $deletedStudentUnits = DB::table('student_unit')
                    ->whereIn('id', $studentUnitIds)
                    ->delete();
            }

            DB::commit();

            $this->info('Reset complete. Deleted:');
            $this->line("- student_activity: {$deletedStudentActivity}");
            $this->line("- student_lesson:   {$deletedStudentLessons}");
            if ($resetClassroom) {
                $this->line("- inst_lesson_breaks: {$deletedBreaks}");
                $this->line("- inst_lesson:      {$deletedInstLessons}");
            }
            if ($deleteStudentUnits) {
                $this->line("- student_unit:     {$deletedStudentUnits}");
            }

            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Reset failed: ' . $e->getMessage());
            return 1;
        }
    }
}
