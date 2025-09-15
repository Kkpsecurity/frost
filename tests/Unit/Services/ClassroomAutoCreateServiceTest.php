<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use App\Services\ClassroomAutoCreateService;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseDate;
use App\Models\Classroom;
use App\Models\User;

/**
 * Tests for ClassroomAutoCreateService
 * 
 * Covers unit testing for the auto-create classroom functionality including
 * happy path, edge cases, and error conditions.
 */
class ClassroomAutoCreateServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClassroomAutoCreateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClassroomAutoCreateService();
        
        // Set timezone for consistent testing
        config(['auto_classroom.timezone' => 'America/New_York']);
        config(['auto_classroom.enabled' => true]);
    }

    /** @test */
    public function it_creates_classroom_for_todays_course_date()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9), // 9 AM today
            'ends_at' => $today->copy()->addHours(17),   // 5 PM today
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(1, $results['stats']['created']);
        $this->assertEquals(0, $results['stats']['failed']);
        
        $this->assertDatabaseHas('classrooms', [
            'course_date_id' => $courseDate->id,
            'course_unit_id' => $courseUnit->id,
            'status' => 'ready',
        ]);

        // Verify course date is stamped
        $courseDate->refresh();
        $this->assertNotNull($courseDate->classroom_created_at);
    }

    /** @test */
    public function it_skips_course_dates_that_already_have_classrooms()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => now(), // Already processed
        ]);

        // Pre-create classroom
        Classroom::factory()->create([
            'course_date_id' => $courseDate->id,
            'course_unit_id' => $courseUnit->id,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(0, $results['stats']['created']);
        $this->assertEquals(0, $results['stats']['failed']);
        
        // Should still be only one classroom
        $this->assertEquals(1, Classroom::count());
    }

    /** @test */
    public function it_skips_inactive_course_dates()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => false, // Inactive
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(0, $results['stats']['created']);
        $this->assertEquals(0, $results['stats']['failed']);
        $this->assertEquals(0, Classroom::count());
    }

    /** @test */
    public function it_handles_inactive_courses()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => false]); // Inactive course
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']); // No failure, just skipped
        $this->assertEquals(0, $results['stats']['created']);
        $this->assertEquals(1, $results['stats']['skipped']);
        $this->assertEquals(0, Classroom::count());
    }

    /** @test */
    public function it_processes_multiple_course_dates_on_same_day()
    {
        // Arrange
        $course1 = Course::factory()->create(['is_active' => true]);
        $course2 = Course::factory()->create(['is_active' => true]);
        $courseUnit1 = CourseUnit::factory()->create(['course_id' => $course1->id]);
        $courseUnit2 = CourseUnit::factory()->create(['course_id' => $course2->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        
        $courseDate1 = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit1->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(12),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        $courseDate2 = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit2->id,
            'starts_at' => $today->copy()->addHours(14),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(2, $results['stats']['created']);
        $this->assertEquals(0, $results['stats']['failed']);
        $this->assertEquals(2, Classroom::count());
        
        $this->assertDatabaseHas('classrooms', ['course_date_id' => $courseDate1->id]);
        $this->assertDatabaseHas('classrooms', ['course_date_id' => $courseDate2->id]);
    }

    /** @test */
    public function it_handles_dry_run_mode()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms(true); // Dry run

        // Assert
        $this->assertTrue($results['success']);
        $this->assertTrue($results['dry_run']);
        $this->assertEquals(1, $results['stats']['created']); // Would have created
        $this->assertEquals(0, Classroom::count()); // But didn't actually create
        
        // Course date should not be stamped
        $courseDate->refresh();
        $this->assertNull($courseDate->classroom_created_at);
    }

    /** @test */
    public function it_processes_only_specified_course_date_id()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        
        $courseDate1 = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(12),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        $courseDate2 = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(14),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act - Process only the second course date
        $results = $this->service->createTodaysClassrooms(false, (string)$courseDate2->id);

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(1, $results['stats']['created']);
        $this->assertEquals(1, Classroom::count());
        
        // Only second course date should have classroom
        $this->assertDatabaseMissing('classrooms', ['course_date_id' => $courseDate1->id]);
        $this->assertDatabaseHas('classrooms', ['course_date_id' => $courseDate2->id]);
    }

    /** @test */
    public function it_handles_feature_flag_disabled()
    {
        // Arrange
        config(['auto_classroom.enabled' => false]);
        
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertFalse($results['success']);
        $this->assertContains('Feature flag auto_classroom.enabled is false', $results['errors']);
        $this->assertEquals(0, Classroom::count());
    }

    /** @test */
    public function it_returns_no_ops_message_when_no_course_dates_found()
    {
        // Arrange - No course dates for today

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        $this->assertEquals(0, $results['stats']['created']);
        $this->assertEquals('No course dates scheduled for today', $results['stats']['message']);
    }

    /** @test */
    public function it_creates_classroom_materials()
    {
        // Arrange
        config(['auto_classroom.default_materials' => [
            'syllabus' => [
                'title' => 'Course Syllabus',
                'type' => 'syllabus',
                'is_required' => true,
                'sort_order' => 1,
            ],
            'attendance' => [
                'title' => 'Attendance Sheet',
                'type' => 'attendance_sheet',
                'is_required' => true,
                'sort_order' => 2,
            ],
        ]]);

        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        
        $classroom = Classroom::first();
        $this->assertEquals(2, $classroom->materials()->count());
        
        $this->assertDatabaseHas('classroom_materials', [
            'classroom_id' => $classroom->id,
            'type' => 'syllabus',
            'title' => 'Course Syllabus',
            'is_required' => true,
        ]);
        
        $this->assertDatabaseHas('classroom_materials', [
            'classroom_id' => $classroom->id,
            'type' => 'attendance_sheet',
            'title' => 'Attendance Sheet',
            'is_required' => true,
        ]);
    }

    /** @test */
    public function it_sets_proper_classroom_status_and_timestamps()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        $startTime = $today->copy()->addHours(9);
        $endTime = $today->copy()->addHours(17);
        
        $courseDate = CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $startTime,
            'ends_at' => $endTime,
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act
        $results = $this->service->createTodaysClassrooms();

        // Assert
        $this->assertTrue($results['success']);
        
        $classroom = Classroom::first();
        $this->assertEquals('ready', $classroom->status);
        $this->assertEquals($startTime->timestamp, $classroom->starts_at->timestamp);
        $this->assertEquals($endTime->timestamp, $classroom->ends_at->timestamp);
        $this->assertNotNull($classroom->classroom_created_at);
        
        // Course date should be stamped
        $courseDate->refresh();
        $this->assertNotNull($courseDate->classroom_created_at);
        $this->assertIsArray($courseDate->classroom_metadata);
        $this->assertEquals($classroom->id, $courseDate->classroom_metadata['classroom_id']);
    }
}
