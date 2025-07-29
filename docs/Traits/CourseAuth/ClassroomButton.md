# ClassroomButton Trait

**File:** `app/Models/Traits/CourseAuth/ClassroomButton.php`  
**Namespace:** `App\Models\Traits\CourseAuth`

## Overview

The ClassroomButton trait provides business logic for determining when a student can access the classroom for a course. It implements complex timing and authorization rules to control course access based on multiple factors including expiration, completion status, administrative overrides, and scheduled class times.

## Dependencies

```php
use Illuminate\Support\Carbon;
```

**Model Dependencies:** This trait is designed for the `CourseAuth` model and requires:
- `IsExpired()` method (likely from another trait)
- `ClassroomCourseDate()` method (from ClassroomCourseDate trait)
- Access to CourseAuth model properties

## Methods

### `ClassroomButton(): bool`

**Returns:** `bool` - True if user can access the classroom, false otherwise

**Description:** Determines whether a student should see and be able to click the classroom access button. Implements a multi-layered authorization system with timing controls.

## Business Logic Flow

The method implements a sequential check system with early returns:

### 1. Configuration
```php
$allow_minutes_pre = 60; // Allow access 60 minutes before class starts
```

### 2. Expiration Check
```php
if ($this->IsExpired()) {
    return false;
}
```
**Logic:** Expired course authorizations cannot access the classroom.

### 3. Completion/Disabled Check
```php
if ($this->completed_at or $this->disabled_at) {
    return false;
}
```
**Logic:** Completed or disabled course authorizations are blocked.

### 4. Administrative Override Check
```php
if ($this->start_date or $this->exam_admin_id) {
    return true;
}
```
**Logic:** Two override conditions allow immediate access:
- **start_date**: Student has previously accessed the course
- **exam_admin_id**: Administrator has granted exam access

### 5. Scheduled Class Time Check
```php
if ($CourseDate = $this->ClassroomCourseDate()) {
    return Carbon::now()->gt(
        Carbon::parse($CourseDate->starts_at)->subMinutes($allow_minutes_pre)
    );
}
```
**Logic:** If there's a scheduled class today:
- Allow access 60 minutes before class starts
- Uses `ClassroomCourseDate()` to find today's scheduled class

### 6. Default Denial
```php
return false; // Student must wait for the next CourseDate
```
**Logic:** No scheduled class and no overrides = no access.

## Complete Method Implementation

```php
public function ClassroomButton(): bool
{
    $allow_minutes_pre = 60;

    // CourseAuth is expired
    if ($this->IsExpired()) {
        return false;
    }

    // CourseAuth is completed or disabled
    if ($this->completed_at or $this->disabled_at) {
        return false;
    }

    // Student has accessed the Course at least once
    // or admin has allowed Exam access
    if ($this->start_date or $this->exam_admin_id) {
        return true;
    }

    // Do not allow student access to the Course until
    // there is a live class in progress or about to start
    if ($CourseDate = $this->ClassroomCourseDate()) {
        return Carbon::now()->gt(
            Carbon::parse($CourseDate->starts_at)->subMinutes($allow_minutes_pre)
        );
    }

    // Student must wait for the next CourseDate
    return false;
}
```

## Access Control Matrix

| Condition | start_date | exam_admin_id | CourseDate | Time Check | Result |
|-----------|------------|---------------|------------|------------|---------|
| Expired | Any | Any | Any | Any | ❌ False |
| Completed | Any | Any | Any | Any | ❌ False |
| Disabled | Any | Any | Any | Any | ❌ False |
| Valid | ✅ Set | Any | Any | Any | ✅ True |
| Valid | Any | ✅ Set | Any | Any | ✅ True |
| Valid | ❌ None | ❌ None | ✅ Today | ✅ Within window | ✅ True |
| Valid | ❌ None | ❌ None | ✅ Today | ❌ Too early | ❌ False |
| Valid | ❌ None | ❌ None | ❌ None | Any | ❌ False |

## Usage Patterns

### Controller Integration

```php
class ClassroomController extends Controller
{
    public function show(Course $course)
    {
        $user = Auth::user();
        $courseAuth = $user->ActiveCourseAuths()
                          ->where('course_id', $course->id)
                          ->first();
        
        if (!$courseAuth || !$courseAuth->ClassroomButton()) {
            return redirect()->route('dashboard')
                           ->with('error', 'Classroom not available at this time');
        }
        
        return view('classroom.show', compact('course', 'courseAuth'));
    }
}
```

### Blade Template Usage

```php
@php
    $courseAuth = $user->ActiveCourseAuths()
                      ->where('course_id', $course->id)
                      ->first();
@endphp

@if($courseAuth && $courseAuth->ClassroomButton())
    <a href="{{ route('classroom.enter', $course) }}" 
       class="btn btn-primary btn-lg">
        <i class="fas fa-play"></i>
        Enter Classroom
    </a>
@else
    <button class="btn btn-secondary btn-lg" disabled>
        <i class="fas fa-clock"></i>
        Classroom Not Available
        @if($courseAuth)
            @php
                $nextClass = $courseAuth->ClassroomCourseDate();
            @endphp
            @if($nextClass)
                <small class="d-block">
                    Next class: {{ $nextClass->starts_at->format('M j, Y g:i A') }}
                </small>
            @endif
        @endif
    </button>
@endif
```

### API Endpoint

```php
class ClassroomApiController extends Controller
{
    public function checkAccess(Course $course)
    {
        $user = Auth::user();
        $courseAuth = $user->ActiveCourseAuths()
                          ->where('course_id', $course->id)
                          ->first();
        
        if (!$courseAuth) {
            return response()->json([
                'can_access' => false,
                'reason' => 'Not enrolled in course'
            ]);
        }
        
        $canAccess = $courseAuth->ClassroomButton();
        
        return response()->json([
            'can_access' => $canAccess,
            'reason' => $canAccess ? 'Access granted' : 'Access denied',
            'course_date' => $courseAuth->ClassroomCourseDate(),
            'start_date' => $courseAuth->start_date,
            'exam_admin_id' => $courseAuth->exam_admin_id,
        ]);
    }
}
```

## Timing Logic Details

### Pre-Class Access Window

```php
$allow_minutes_pre = 60; // 60 minutes before class

// Example: Class starts at 2:00 PM
// Access granted from: 1:00 PM onwards
// Current time: 1:30 PM → Access granted
// Current time: 12:30 PM → Access denied
```

### Administrative Overrides

#### Start Date Override
```php
if ($this->start_date) {
    return true; // Immediate access
}
```
**Use Case:** Student has already attended at least one class session.

#### Exam Admin Override
```php
if ($this->exam_admin_id) {
    return true; // Immediate access
}
```
**Use Case:** Administrator has specifically granted exam access.

## Security Considerations

### Authorization Validation
- **No Direct DB Queries**: Relies on model relationships and properties
- **Status Validation**: Checks expiration, completion, and disabled status
- **Time-based Access**: Prevents unauthorized early access

### Potential Security Issues
```php
// ❌ Don't bypass the trait method
if (Carbon::now() > $courseDate->starts_at) { // Unsafe - missing validations
    // Allow access
}

// ✅ Use the trait method
if ($courseAuth->ClassroomButton()) { // Safe - includes all checks
    // Allow access
}
```

## Performance Considerations

### Optimization Opportunities

The current implementation calls `ClassroomCourseDate()` which may involve database queries:

```php
// Current (potential DB query each time)
if ($CourseDate = $this->ClassroomCourseDate()) {
    // Check timing
}

// Optimized (cache the result)
public function ClassroomButton(): bool
{
    $cacheKey = "classroom_button_{$this->id}";
    
    return Cache::remember($cacheKey, 300, function () {
        // Original logic here
    });
}
```

### Debugging Support

The trait includes commented debug logging:
```php
/*
if ( Carbon::now()->gt( Carbon::parse( $CourseDate->starts_at )->subMinutes( $allow_minutes_pre ) ) )
{
    logger( "{$this->id} / {$this->course_id} :: Ready" );
}
else
{
    logger( "{$this->id} / {$this->course_id} :: Not Ready" );
}
*/
```

## Testing Strategies

### Unit Testing

```php
public function test_blocks_expired_course_auth()
{
    $courseAuth = CourseAuth::factory()->expired()->make();
    
    $this->assertFalse($courseAuth->ClassroomButton());
}

public function test_allows_access_with_start_date()
{
    $courseAuth = CourseAuth::factory()->make([
        'start_date' => Carbon::yesterday(),
        'completed_at' => null,
        'disabled_at' => null,
    ]);
    
    $this->assertTrue($courseAuth->ClassroomButton());
}

public function test_allows_access_with_exam_admin_override()
{
    $courseAuth = CourseAuth::factory()->make([
        'exam_admin_id' => 1,
        'start_date' => null,
        'completed_at' => null,
        'disabled_at' => null,
    ]);
    
    $this->assertTrue($courseAuth->ClassroomButton());
}

public function test_timing_window_for_scheduled_class()
{
    // Mock ClassroomCourseDate to return scheduled class
    $courseDate = CourseDate::factory()->make([
        'starts_at' => Carbon::now()->addMinutes(30) // 30 minutes from now
    ]);
    
    $courseAuth = Mockery::mock(CourseAuth::class)->makePartial();
    $courseAuth->shouldReceive('ClassroomCourseDate')->andReturn($courseDate);
    $courseAuth->shouldReceive('IsExpired')->andReturn(false);
    
    $courseAuth->completed_at = null;
    $courseAuth->disabled_at = null;
    $courseAuth->start_date = null;
    $courseAuth->exam_admin_id = null;
    
    // Should allow access (within 60-minute window)
    $this->assertTrue($courseAuth->ClassroomButton());
}
```

### Integration Testing

```php
public function test_classroom_access_flow()
{
    $user = User::factory()->create();
    $course = Course::factory()->create();
    
    // Create course authorization
    $courseAuth = CourseAuth::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'start_date' => null,
        'exam_admin_id' => null,
    ]);
    
    // Create scheduled course date
    CourseDate::factory()->create([
        'course_unit_id' => $course->courseUnits->first()->id,
        'starts_at' => Carbon::now()->addMinutes(30),
        'is_active' => true,
    ]);
    
    $this->actingAs($user)
         ->get(route('classroom.show', $course))
         ->assertStatus(200);
}
```

## Common Issues and Solutions

### Issue: ClassroomCourseDate Returns Null
**Problem:** No scheduled class found for today
**Solution:** Check CourseDate records and is_active status

### Issue: Timing Window Too Restrictive
**Problem:** Students complain about not being able to access early enough
**Solution:** Adjust `$allow_minutes_pre` configuration

### Issue: Administrative Overrides Not Working
**Problem:** start_date or exam_admin_id not being set properly
**Solution:** Verify database values and model relationships

## Related Traits

- [ClassroomCourseDate](ClassroomCourseDate.md) - Provides scheduled class lookup
- [SetStartDateTrait](SetStartDateTrait.md) - Manages start_date setting
- [ExamsTrait](ExamsTrait.md) - Exam authorization management

## Related Models

- [CourseAuth](../Models/CourseAuth.md) - Model that uses this trait
- [CourseDate](../Models/CourseDate.md) - Scheduled class information
- [Course](../Models/Course.md) - Course definitions

---

*Last updated: July 28, 2025*
