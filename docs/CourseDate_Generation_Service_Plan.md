# CourseDate Generation Service - Planning Document

## Overview
Create a service that automatically generates CourseDate records for Monday through Friday, enabling consistent daily class scheduling.

## Requirements Analysis

### 1. **Core Functionality**
- Generate CourseDate records for weekdays (Mon-Fri)
- Skip weekends automatically
- Handle holidays/blackout dates
- Support different course types/schedules
- Prevent duplicate CourseDate creation

### 2. **Business Logic Questions**
**Schedule Pattern:**
- Should it generate dates for the current week or future weeks?
- How far in advance should dates be generated? (1 week, 1 month, etc.)
- What time should classes start/end each day?
- Should different courses have different schedules?

**Course Selection:**
- Which courses should have dates generated?
- Should all active courses get daily dates?
- How to handle course prerequisites/sequences?
- Should course units be assigned in order?

**Timing & Frequency:**
- When should the service run? (Daily, weekly, on-demand?)
- Should it run at a specific time? (e.g., Sunday night for the week ahead)
- How to handle missed runs or failures?

### 3. **Data Structure Dependencies**

**Required Models:**
```php
CourseDate {
    id: integer
    is_active: boolean
    course_unit_id: integer (FK to CourseUnit)
    starts_at: timestamp
    ends_at: timestamp
    classroom_created_at: timestamp
    classroom_metadata: array
}

CourseUnit {
    id: integer
    course_id: integer (FK to Course)
    title: string
    admin_title: string
    sequence: integer
    ordering: integer
}

Course {
    id: integer
    title: string
    is_active: boolean
}
```

### 4. **Service Architecture**

**Service Location:**
- `app/Services/Frost/Scheduling/CourseDateGeneratorService.php`

**Key Methods:**
```php
class CourseDateGeneratorService {
    // Main generation method
    public function generateWeeklyCourseDates(Carbon $startDate = null): array
    
    // Generate for specific date range
    public function generateCourseDatesForRange(Carbon $start, Carbon $end): array
    
    // Generate for single day
    public function generateCourseDatesForDate(Carbon $date): array
    
    // Validation and checks
    public function shouldGenerateForDate(Carbon $date): bool
    public function getActiveCourses(): Collection
    public function getNextCourseUnit(Course $course): ?CourseUnit
    
    // Cleanup and maintenance
    public function removeDuplicateCourseDates(): int
    public function cleanupOldCourseDates(): int
}
```

### 5. **Configuration Options**

**Config File:** `config/course_scheduling.php`
```php
return [
    'generation' => [
        'advance_days' => 7,              // Generate 7 days ahead
        'start_time' => '09:00',          // Default class start time
        'duration_hours' => 3,            // Default class duration
        'weekdays_only' => true,          // Skip weekends
        'skip_holidays' => true,          // Skip holiday dates
    ],
    
    'schedule' => [
        'monday' => ['start' => '09:00', 'duration' => 3],
        'tuesday' => ['start' => '09:00', 'duration' => 3],
        'wednesday' => ['start' => '09:00', 'duration' => 3],
        'thursday' => ['start' => '09:00', 'duration' => 3],
        'friday' => ['start' => '09:00', 'duration' => 3],
    ],
    
    'holidays' => [
        '2025-12-25', // Christmas
        '2025-01-01', // New Year
        // ... more holidays
    ]
];
```

### 6. **Integration Points**

**Command Line Interface:**
```bash
php artisan course:generate-dates --week=current
php artisan course:generate-dates --range="2025-09-23,2025-09-30"
php artisan course:generate-dates --date="2025-09-24"
```

**Laravel Scheduler:**
```php
// In app/Console/Kernel.php
$schedule->command('course:generate-dates --week=next')
         ->weeklyOn(0, '22:00'); // Sunday at 10 PM
```

**API Endpoints:**
```php
// For manual triggering
POST /admin/courses/generate-dates
GET  /admin/courses/generated-dates/preview
```

### 7. **Error Handling & Validation**

**Validation Checks:**
- Prevent duplicate CourseDate creation
- Validate course/unit relationships
- Check for conflicting schedules
- Ensure proper date/time formatting

**Error Scenarios:**
- Missing CourseUnits for active Courses
- Invalid date ranges
- Database connection issues
- Conflicting existing CourseDates

### 8. **Logging & Monitoring**

**Log Events:**
- CourseDate generation started/completed
- Number of dates created per course
- Skipped dates (holidays, duplicates)
- Errors and failures

**Metrics to Track:**
- Total CourseDates generated
- Success/failure rates
- Processing time
- Database impact

### 9. **Testing Strategy**

**Unit Tests:**
- Date range calculations
- Holiday detection
- Duplicate prevention
- Course/unit selection logic

**Integration Tests:**
- Full generation workflow
- Database operations
- Command execution
- Scheduler integration

### 10. **Deployment Considerations**

**Database Impact:**
- Consider batch inserts for performance
- Add database indexes if needed
- Monitor query performance

**Rollback Strategy:**
- Ability to undo generation for specific date ranges
- Soft delete vs hard delete for cleanup
- Backup/restore procedures

## Next Steps

1. **Finalize Requirements** - Confirm business rules and schedule patterns
2. **Create Configuration** - Set up config file with scheduling rules
3. **Implement Core Service** - Build the main generation logic
4. **Add Command Interface** - Create Artisan commands for manual usage
5. **Integrate Scheduler** - Set up automatic generation
6. **Add Monitoring** - Implement logging and error tracking
7. **Test Thoroughly** - Unit and integration testing
8. **Deploy & Monitor** - Production deployment with monitoring

## Questions for Clarification

1. **Schedule Pattern**: What time should classes start each day? Same time or different?
2. **Course Selection**: Should all active courses get daily dates, or specific courses?
3. **Advance Generation**: How far ahead should dates be generated?
4. **Course Unit Progression**: Should course units be assigned in sequence (Day 1, Day 2, etc.)?
5. **Conflict Resolution**: What happens if a CourseDate already exists for a course/date?
6. **Holiday Handling**: Should we maintain a holiday calendar or use a service?
