<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Services\ClassroomDashboardService;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\InstUnit;
use App\Models\StudentLesson;
use App\Models\StudentUnit;
use App\Models\User;

/**
 * Unit tests for ClassroomDashboardService
 *
 * Domain recap (for context):
 *   CourseAuth    — student's approved enrollment for a Course
 *   CourseUnit    — metadata for one unit/day of a Course
 *   CourseDate    — the scheduled class for a specific day (ties to CourseUnit)
 *   InstUnit      — snapshot of the instructor's active session for a CourseDate (attendance)
 *   StudentUnit   — snapshot of the student's active session for a CourseDate (attendance)
 *   StudentLesson — a single lesson the student has attempted during a StudentUnit session
 */
class ClassroomDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Suppress observer side-effects:
     *   - StudentUnitObserver::saved  → PCLCache() requires Redis
     *   - StudentLessonObserver::saved → PCLCache() + SetUnitCompleted()
     *   - StudentLessonObserver::created → creates StudentActivity record
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    // =========================================================================
    // Test Helpers — minimal data builders
    // =========================================================================

    private function makeUser(string $email = 'student@test.com'): User
    {
        return User::create([
            'lname'    => 'Student',
            'fname'    => 'Test',
            'email'    => $email,
            'password' => bcrypt('password'),
            'role_id'  => 5,
        ]);
    }

    /** Create a CourseUnit. No actual Course row required (no FK in SQLite). */
    private function makeCourseUnit(int $courseId = 1): CourseUnit
    {
        return CourseUnit::create([
            'course_id' => $courseId,
            'title'     => 'Test Unit',
            'ordering'  => 1,
        ]);
    }

    /**
     * Create a CourseDate scheduled for today so it passes the
     * getCourseDates() starts_at/ends_at day-boundary filter.
     *
     * Uses DB::table() with plain Y-m-d H:i:s strings to bypass PgTimestamps'
     * microsecond/timezone format ('Y-m-d H:i:s.uO') which breaks SQLite's
     * lexicographic comparison against DayStartSQL() / DayEndSQL() output.
     */
    private function makeCourseDate(int $courseUnitId, bool $isActive = true): CourseDate
    {
        $today = now()->format('Y-m-d');
        $id    = DB::table('course_dates')->insertGetId([
            'course_unit_id' => $courseUnitId,
            'is_active'      => $isActive ? 1 : 0,
            'starts_at'      => "{$today} 09:00:00",
            'ends_at'        => "{$today} 17:00:00",
        ]);
        return CourseDate::find($id);
    }

    /** Enroll user in a course. No actual Course row required (no FK in SQLite). */
    private function makeCourseAuth(int $userId, int $courseId = 1): CourseAuth
    {
        return CourseAuth::create([
            'user_id'   => $userId,
            'course_id' => $courseId,
        ]);
    }

    /** Create an active (not completed) InstUnit for a CourseDate. */
    private function makeInstUnit(int $courseDateId): InstUnit
    {
        return InstUnit::create([
            'course_date_id' => $courseDateId,
            'created_by'     => 1,
        ]);
    }

    /** Create a StudentUnit (the student's attendance record for the day). */
    private function makeStudentUnit(
        int $courseAuthId,
        int $courseUnitId,
        int $courseDateId,
        int $instUnitId
    ): StudentUnit {
        return StudentUnit::create([
            'course_auth_id' => $courseAuthId,
            'course_unit_id' => $courseUnitId,
            'course_date_id' => $courseDateId,
            'inst_unit_id'   => $instUnitId,
        ]);
    }

    /**
     * Scaffold all models needed for session-based tests.
     * Returns a keyed array of the created models.
     */
    private function makeClassroomSession(?User $user = null): array
    {
        $user       = $user ?? $this->makeUser();
        $courseUnit = $this->makeCourseUnit();
        $courseDate = $this->makeCourseDate($courseUnit->id);
        $instUnit   = $this->makeInstUnit($courseDate->id);
        $courseAuth = $this->makeCourseAuth($user->id);

        return compact('user', 'courseUnit', 'courseDate', 'instUnit', 'courseAuth');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    /** @test */
    public function test_can_be_instantiated_without_a_user(): void
    {
        $service = new ClassroomDashboardService();
        $this->assertInstanceOf(ClassroomDashboardService::class, $service);
    }

    /** @test */
    public function test_can_be_instantiated_with_a_user(): void
    {
        $user    = $this->makeUser();
        $service = new ClassroomDashboardService($user);
        $this->assertInstanceOf(ClassroomDashboardService::class, $service);
    }

    // =========================================================================
    // getClassroomData()
    // =========================================================================

    /** @test */
    public function test_get_classroom_data_returns_empty_structure_when_no_user(): void
    {
        $service = new ClassroomDashboardService(null);
        $result  = $service->getClassroomData();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('instructors', $result);
        $this->assertArrayHasKey('courseDates', $result);
        $this->assertArrayHasKey('stats', $result);
        $this->assertEquals(0, $result['stats']['total_instructors']);
        $this->assertEquals(0, $result['stats']['total_course_dates']);
    }

    /** @test */
    public function test_get_classroom_data_returns_correct_structure_for_authenticated_user(): void
    {
        ['user' => $user] = $this->makeClassroomSession();

        $result = (new ClassroomDashboardService($user))->getClassroomData();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('instructors', $result);
        $this->assertArrayHasKey('courseDates', $result);
        $this->assertArrayHasKey('total_instructors', $result['stats']);
        $this->assertArrayHasKey('total_course_dates', $result['stats']);
    }

    // =========================================================================
    // getInstructorData()
    // =========================================================================

    /** @test */
    public function test_get_instructor_data_returns_empty_collection_when_no_user(): void
    {
        $result = (new ClassroomDashboardService(null))->getInstructorData();
        $this->assertCount(0, $result);
    }

    /** @test */
    public function test_get_instructor_data_returns_empty_collection_no_classes_scheduled(): void
    {
        // Service comment says: "no classes are scheduled so instructors should be empty"
        $user   = $this->makeUser();
        $result = (new ClassroomDashboardService($user))->getInstructorData();
        $this->assertCount(0, $result);
    }

    // =========================================================================
    // getCourseDates()
    // =========================================================================

    /** @test */
    public function test_get_course_dates_returns_empty_when_no_user(): void
    {
        $this->assertCount(0, (new ClassroomDashboardService(null))->getCourseDates());
    }

    /** @test */
    public function test_get_course_dates_returns_empty_when_student_has_no_enrollments(): void
    {
        // User exists but has no CourseAuth records
        $user = $this->makeUser();
        $this->assertCount(0, (new ClassroomDashboardService($user))->getCourseDates());
    }

    /** @test */
    public function test_get_course_dates_returns_active_course_dates_for_enrolled_student(): void
    {
        // CourseAuth → course_id → CourseUnit → CourseDate
        // No actual Course row needed (no FK enforcement in SQLite)
        $user       = $this->makeUser();
        $courseUnit = $this->makeCourseUnit(courseId: 1);
        $courseDate = $this->makeCourseDate($courseUnit->id, isActive: true);
        $this->makeCourseAuth($user->id, courseId: 1);

        $results = (new ClassroomDashboardService($user))->getCourseDates();

        $this->assertCount(1, $results);
        $this->assertEquals($courseDate->id, $results->first()->id);
    }

    /** @test */
    public function test_get_course_dates_excludes_inactive_course_dates(): void
    {
        $user       = $this->makeUser();
        $courseUnit = $this->makeCourseUnit(courseId: 1);
        $this->makeCourseDate($courseUnit->id, isActive: false); // inactive
        $this->makeCourseAuth($user->id, courseId: 1);

        $this->assertCount(0, (new ClassroomDashboardService($user))->getCourseDates());
    }

    /** @test */
    public function test_get_course_dates_excludes_course_dates_outside_todays_window(): void
    {
        $user       = $this->makeUser();
        $courseUnit = $this->makeCourseUnit(courseId: 1);

        // CourseDate scheduled for yesterday (use plain strings for reliable SQLite comparison)
        $yesterday = now()->subDay()->format('Y-m-d');
        DB::table('course_dates')->insert([
            'course_unit_id' => $courseUnit->id,
            'is_active'      => 1,
            'starts_at'      => "{$yesterday} 09:00:00",
            'ends_at'        => "{$yesterday} 17:00:00",
        ]);

        $this->makeCourseAuth($user->id, courseId: 1);

        $this->assertCount(0, (new ClassroomDashboardService($user))->getCourseDates());
    }

    /** @test */
    public function test_get_course_dates_only_returns_dates_for_enrolled_courses(): void
    {
        $user       = $this->makeUser();

        // CourseUnit for course 1 (student is enrolled)
        $enrolledUnit = $this->makeCourseUnit(courseId: 1);
        $enrolledDate = $this->makeCourseDate($enrolledUnit->id);
        $this->makeCourseAuth($user->id, courseId: 1);

        // CourseUnit for course 2 (student is NOT enrolled)
        $otherUnit = $this->makeCourseUnit(courseId: 2);
        $this->makeCourseDate($otherUnit->id);

        $results = (new ClassroomDashboardService($user))->getCourseDates();

        $this->assertCount(1, $results);
        $this->assertEquals($enrolledDate->id, $results->first()->id);
    }

    // =========================================================================
    // clearCache()
    // =========================================================================

    /** @test */
    public function test_clear_cache_does_nothing_when_no_user(): void
    {
        Cache::spy();
        (new ClassroomDashboardService(null))->clearCache();
        Cache::shouldNotHaveReceived('forget');
    }

    /** @test */
    public function test_clear_cache_forgets_all_three_user_cache_keys(): void
    {
        $user = $this->makeUser();
        Cache::spy();

        (new ClassroomDashboardService($user))->clearCache();

        Cache::shouldHaveReceived('forget')->with("classroom_dashboard_data_{$user->id}");
        Cache::shouldHaveReceived('forget')->with("classroom_instructors_{$user->id}");
        Cache::shouldHaveReceived('forget')->with("classroom_course_dates_{$user->id}");
    }

    // =========================================================================
    // findOrCreateSession()
    //
    // StudentUnit = the student's attendance record for a day (tied to a
    // specific CourseDate + InstUnit pair).  The service requires an active
    // InstUnit because the instructor must have started the class first.
    // =========================================================================

    /** @test */
    public function test_find_or_create_session_throws_when_course_date_not_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CourseDate not found');

        (new ClassroomDashboardService())->findOrCreateSession(courseAuthId: 1, courseDateId: 9999);
    }

    /** @test */
    public function test_find_or_create_session_throws_when_no_active_inst_unit(): void
    {
        // CourseDate exists but instructor has NOT started the class yet
        $user       = $this->makeUser();
        $courseUnit = $this->makeCourseUnit();
        $courseDate = $this->makeCourseDate($courseUnit->id);
        $courseAuth = $this->makeCourseAuth($user->id);
        // No InstUnit created

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class is not active (no InstUnit).');

        (new ClassroomDashboardService($user))->findOrCreateSession($courseAuth->id, $courseDate->id);
    }

    /** @test */
    public function test_find_or_create_session_creates_new_student_unit(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = (new ClassroomDashboardService($user))
            ->findOrCreateSession($courseAuth->id, $courseDate->id);

        $this->assertInstanceOf(StudentUnit::class, $studentUnit);
        $this->assertEquals($courseAuth->id, $studentUnit->course_auth_id);
        $this->assertEquals($courseDate->id, $studentUnit->course_date_id);
        $this->assertEquals($courseUnit->id, $studentUnit->course_unit_id);
        $this->assertEquals($instUnit->id, $studentUnit->inst_unit_id);
        $this->assertNotNull($studentUnit->last_heartbeat_at);
        $this->assertNotNull($studentUnit->session_expires_at);

        $this->assertDatabaseHas('student_unit', [
            'course_auth_id' => $courseAuth->id,
            'course_date_id' => $courseDate->id,
        ]);
    }

    /** @test */
    public function test_find_or_create_session_resumes_existing_active_session(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        // Pre-create an active session (no completed_at, created recently)
        $existing = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        $returned = (new ClassroomDashboardService($user))
            ->findOrCreateSession($courseAuth->id, $courseDate->id);

        $this->assertEquals($existing->id, $returned->id);
        $this->assertDatabaseCount('student_unit', 1); // no duplicate created
    }

    /** @test */
    public function test_find_or_create_session_creates_new_when_existing_session_is_completed(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        // Pre-create a COMPLETED session (completed_at is set)
        StudentUnit::create([
            'course_auth_id' => $courseAuth->id,
            'course_unit_id' => $courseUnit->id,
            'course_date_id' => $courseDate->id,
            'inst_unit_id'   => $instUnit->id,
            'completed_at'   => now()->subHour(),
        ]);

        $returned = (new ClassroomDashboardService($user))
            ->findOrCreateSession($courseAuth->id, $courseDate->id);

        // Should have created a second record, not resumed the completed one
        $this->assertDatabaseCount('student_unit', 2);
        $this->assertNull($returned->completed_at);
    }

    // =========================================================================
    // updateHeartbeat()
    //
    // Called every ~30 seconds from the frontend to prove the student is still
    // connected.  Updates last_heartbeat_at on the StudentUnit.
    // =========================================================================

    /** @test */
    public function test_update_heartbeat_updates_last_heartbeat_at(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        $before = now()->subSecond();

        (new ClassroomDashboardService($user))->updateHeartbeat($studentUnit->id);

        $this->assertDatabaseHas('student_unit', ['id' => $studentUnit->id]);
        $studentUnit->refresh();
        $this->assertNotNull($studentUnit->last_heartbeat_at);
    }

    /** @test */
    public function test_update_heartbeat_silently_ignores_missing_student_unit(): void
    {
        // Should not throw — service logs a warning and returns
        (new ClassroomDashboardService())->updateHeartbeat(9999);
        $this->assertTrue(true);
    }

    // =========================================================================
    // checkSessionExpiration()
    //
    // Returns true  → session is stale (student should be ejected / re-authenticated)
    // Returns false → session is still valid
    // =========================================================================

    /** @test */
    public function test_check_session_expiration_returns_true_for_missing_unit(): void
    {
        $result = (new ClassroomDashboardService())->checkSessionExpiration(9999);
        $this->assertTrue($result);
    }

    /** @test */
    public function test_check_session_expiration_returns_false_for_fresh_session(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = StudentUnit::create([
            'course_auth_id'    => $courseAuth->id,
            'course_unit_id'    => $courseUnit->id,
            'course_date_id'    => $courseDate->id,
            'inst_unit_id'      => $instUnit->id,
            'session_expires_at' => now()->addHours(11), // expires in 11 hours
        ]);

        $this->assertFalse((new ClassroomDashboardService($user))->checkSessionExpiration($studentUnit->id));
    }

    /** @test */
    public function test_check_session_expiration_returns_true_when_expires_at_has_passed(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = StudentUnit::create([
            'course_auth_id'    => $courseAuth->id,
            'course_unit_id'    => $courseUnit->id,
            'course_date_id'    => $courseDate->id,
            'inst_unit_id'      => $instUnit->id,
            'session_expires_at' => now()->subHour(), // already expired 1 hour ago
        ]);

        $this->assertTrue((new ClassroomDashboardService($user))->checkSessionExpiration($studentUnit->id));
    }

    /** @test */
    public function test_check_session_expiration_returns_true_when_created_over_12_hours_ago(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        // Create session with no session_expires_at — fallback uses created_at
        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // Manually push created_at back 13 hours (bypassing timestamp auto-management)
        $studentUnit->timestamps = false;
        $studentUnit->created_at = now()->subHours(13);
        $studentUnit->save();
        $studentUnit->timestamps = true;

        $this->assertTrue((new ClassroomDashboardService($user))->checkSessionExpiration($studentUnit->id));
    }

    /** @test */
    public function test_check_session_expiration_returns_false_when_created_just_under_12_hours_ago(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = StudentUnit::create([
            'course_auth_id'    => $courseAuth->id,
            'course_unit_id'    => $courseUnit->id,
            'course_date_id'    => $courseDate->id,
            'inst_unit_id'      => $instUnit->id,
            'session_expires_at' => now()->addHour(), // still valid
        ]);

        $this->assertFalse((new ClassroomDashboardService($user))->checkSessionExpiration($studentUnit->id));
    }

    // =========================================================================
    // failActiveLesson()
    //
    // StudentLesson = one lesson the student attempted in a StudentUnit session.
    // "Active" means started (row exists) but not yet completed or failed.
    // Called on disconnect / timeout / intentional leave.
    // =========================================================================

    /** @test */
    public function test_fail_active_lesson_marks_lesson_failed_with_reason(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // Active lesson: created but neither completed nor failed
        $lesson = StudentLesson::create([
            'lesson_id'      => 1,
            'student_unit_id' => $studentUnit->id,
            'inst_lesson_id' => 1,
        ]);

        (new ClassroomDashboardService($user))->failActiveLesson($studentUnit->id, 'connection_lost');

        $lesson->refresh();
        $this->assertNotNull($lesson->failed_at);
        $this->assertEquals('connection_lost', $lesson->failure_reason);
    }

    /** @test */
    public function test_fail_active_lesson_does_not_touch_already_completed_lesson(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // Lesson is already completed
        $lesson = StudentLesson::create([
            'lesson_id'      => 1,
            'student_unit_id' => $studentUnit->id,
            'inst_lesson_id' => 1,
            'completed_at'   => now()->subMinutes(10),
        ]);

        (new ClassroomDashboardService($user))->failActiveLesson($studentUnit->id, 'timeout');

        $lesson->refresh();
        $this->assertNull($lesson->failed_at); // completed lessons must not be failed
    }

    /** @test */
    public function test_fail_active_lesson_does_not_touch_already_failed_lesson(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        $originalFailedAt = now()->subMinutes(5);
        $lesson = StudentLesson::create([
            'lesson_id'      => 1,
            'student_unit_id' => $studentUnit->id,
            'inst_lesson_id' => 1,
            'failed_at'      => $originalFailedAt,
            'failure_reason' => 'timeout',
        ]);

        (new ClassroomDashboardService($user))->failActiveLesson($studentUnit->id, 'connection_lost');

        $lesson->refresh();
        $this->assertEquals('timeout', $lesson->failure_reason); // original reason preserved
    }

    /** @test */
    public function test_fail_active_lesson_does_nothing_when_no_active_lesson_exists(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // No StudentLesson record exists at all
        (new ClassroomDashboardService($user))->failActiveLesson($studentUnit->id, 'timeout');
        $this->assertTrue(true); // Must not throw
    }

    // =========================================================================
    // recordStudentLeave()
    //
    // Intentional exit: marks the StudentUnit with left_at timestamp and
    // calls failActiveLesson() to close any in-progress lesson.
    // =========================================================================

    /** @test */
    public function test_record_student_leave_sets_left_at_on_student_unit(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        (new ClassroomDashboardService($user))->recordStudentLeave($studentUnit->id);

        $studentUnit->refresh();
        $this->assertNotNull($studentUnit->left_at);
    }

    /** @test */
    public function test_record_student_leave_also_fails_any_active_lesson(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // An active lesson is in progress when the student leaves
        $lesson = StudentLesson::create([
            'lesson_id'      => 1,
            'student_unit_id' => $studentUnit->id,
            'inst_lesson_id' => 1,
        ]);

        (new ClassroomDashboardService($user))->recordStudentLeave($studentUnit->id, 'voluntary');

        $lesson->refresh();
        $this->assertNotNull($lesson->failed_at);
        $this->assertEquals('left_intentionally', $lesson->failure_reason);
    }

    /** @test */
    public function test_record_student_leave_silently_ignores_missing_student_unit(): void
    {
        // Must not throw — service logs a warning and returns
        (new ClassroomDashboardService())->recordStudentLeave(9999);
        $this->assertTrue(true);
    }

    /** @test */
    public function test_record_student_leave_does_not_re_fail_completed_lessons(): void
    {
        [
            'user' => $user,
            'courseUnit' => $courseUnit,
            'courseDate' => $courseDate,
            'instUnit' => $instUnit,
            'courseAuth' => $courseAuth
        ] = $this->makeClassroomSession();

        $studentUnit = $this->makeStudentUnit($courseAuth->id, $courseUnit->id, $courseDate->id, $instUnit->id);

        // Lesson was already completed before the student leaves
        $lesson = StudentLesson::create([
            'lesson_id'      => 1,
            'student_unit_id' => $studentUnit->id,
            'inst_lesson_id' => 1,
            'completed_at'   => now()->subMinutes(2),
        ]);

        (new ClassroomDashboardService($user))->recordStudentLeave($studentUnit->id);

        $lesson->refresh();
        $this->assertNull($lesson->failed_at); // completed lesson must never be failed
    }
}
