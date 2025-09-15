<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseDate;
use App\Models\Classroom;

/**
 * Feature tests for the classroom auto-create Artisan command
 * 
 * Tests the full command execution including options and output formatting.
 */
class AutoCreateTodaysClassroomsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Enable the feature for testing
        config(['auto_classroom.enabled' => true]);
        config(['auto_classroom.timezone' => 'America/New_York']);
    }

    /** @test */
    public function it_executes_successfully_with_no_course_dates()
    {
        $this->artisan('classrooms:auto-create-today')
             ->expectsOutput('Starting classroom auto-creation...')
             ->expectsOutput('📅 No course dates found for today.')
             ->assertExitCode(0);
    }

    /** @test */
    public function it_creates_classrooms_and_shows_success_message()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act & Assert
        $this->artisan('classrooms:auto-create-today')
             ->expectsOutput('Starting classroom auto-creation...')
             ->expectsOutput('🎉 Successfully created 1 classroom(s)!')
             ->assertExitCode(0);

        $this->assertEquals(1, Classroom::count());
    }

    /** @test */
    public function it_shows_dry_run_mode_properly()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act & Assert
        $this->artisan('classrooms:auto-create-today --dry-run')
             ->expectsOutput('DRY RUN MODE: No changes will be made')
             ->expectsOutput('💡 This was a DRY RUN. No actual changes were made.')
             ->expectsOutput('Remove --dry-run to execute the actual creation.')
             ->assertExitCode(0);

        $this->assertEquals(0, Classroom::count());
    }

    /** @test */
    public function it_processes_only_specified_course_date()
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

        // Act & Assert
        $this->artisan("classrooms:auto-create-today --only={$courseDate2->id}")
             ->expectsOutput("Processing only CourseDate ID: {$courseDate2->id}")
             ->expectsOutput('🎉 Successfully created 1 classroom(s)!')
             ->assertExitCode(0);

        $this->assertEquals(1, Classroom::count());
        $this->assertDatabaseHas('classrooms', ['course_date_id' => $courseDate2->id]);
        $this->assertDatabaseMissing('classrooms', ['course_date_id' => $courseDate1->id]);
    }

    /** @test */
    public function it_displays_configuration_information()
    {
        $this->artisan('classrooms:auto-create-today')
             ->expectsOutput('=== Classroom Auto-Creation Configuration ===')
             ->expectsOutput('Enabled: ✓ Yes')
             ->expectsOutput('Timezone: America/New_York')
             ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_statistics_table()
    {
        // Arrange
        $course = Course::factory()->create(['is_active' => true]);
        $courseUnit = CourseUnit::factory()->create(['course_id' => $course->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        CourseDate::factory()->create([
            'course_unit_id' => $courseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act & Assert
        $this->artisan('classrooms:auto-create-today')
             ->expectsTable(
                 ['Metric', 'Count'],
                 [
                     ['Created', '1'],
                     ['Skipped', '0'],
                     ['Failed', '0'],
                     ['Total Processed', '1'],
                 ]
             )
             ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_feature_disabled_error()
    {
        // Arrange
        config(['auto_classroom.enabled' => false]);

        // Act & Assert
        $this->artisan('classrooms:auto-create-today')
             ->expectsOutput('✗ Operation completed with errors')
             ->expectsOutput('Errors encountered:')
             ->expectsOutput('  • Feature flag auto_classroom.enabled is false')
             ->assertExitCode(1);
    }

    /** @test */
    public function it_requires_allow_recreate_flag_with_force()
    {
        $this->artisan('classrooms:auto-create-today --force')
             ->expectsOutput('The --force option requires --allow-recreate flag for safety.')
             ->assertExitCode(1);
    }

    /** @test */
    public function it_shows_force_recreation_warning()
    {
        $this->artisan('classrooms:auto-create-today --force --allow-recreate')
             ->expectsOutput('Force recreation is not yet implemented in this version.')
             ->assertExitCode(1);
    }

    /** @test */
    public function it_shows_current_time_in_et()
    {
        $expectedTime = Carbon::now('America/New_York')->format('Y-m-d H:i:s T');
        
        $this->artisan('classrooms:auto-create-today')
             ->expectsOutputToContain("Current time (ET): {$expectedTime}")
             ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_mixed_success_and_failures()
    {
        // Arrange - Create one valid and one invalid course date
        $activeCourse = Course::factory()->create(['is_active' => true]);
        $inactiveCourse = Course::factory()->create(['is_active' => false]);
        
        $activeCourseUnit = CourseUnit::factory()->create(['course_id' => $activeCourse->id]);
        $inactiveCourseUnit = CourseUnit::factory()->create(['course_id' => $inactiveCourse->id]);
        
        $today = Carbon::now('America/New_York')->startOfDay();
        
        // Valid course date
        CourseDate::factory()->create([
            'course_unit_id' => $activeCourseUnit->id,
            'starts_at' => $today->copy()->addHours(9),
            'ends_at' => $today->copy()->addHours(12),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Invalid course date (inactive course)
        CourseDate::factory()->create([
            'course_unit_id' => $inactiveCourseUnit->id,
            'starts_at' => $today->copy()->addHours(14),
            'ends_at' => $today->copy()->addHours(17),
            'is_active' => true,
            'classroom_created_at' => null,
        ]);

        // Act & Assert
        $this->artisan('classrooms:auto-create-today')
             ->expectsTable(
                 ['Metric', 'Count'],
                 [
                     ['Created', '1'],
                     ['Skipped', '1'],
                     ['Failed', '0'],
                     ['Total Processed', '2'],
                 ]
             )
             ->expectsOutput('✓ Operation completed successfully')
             ->assertExitCode(0);

        $this->assertEquals(1, Classroom::count());
    }
}
