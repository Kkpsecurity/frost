# CourseAuthsTrait

**File:** `app/Models/Traits/User/CourseAuthsTrait.php`  
**Namespace:** `App\Models\Traits\User`

## Overview

The CourseAuthsTrait provides methods for managing user course enrollments and authorization status. It handles active/inactive course access, enrollment checking, and expiration logic. This trait is essential for determining what courses a user can access and their current enrollment status.

## Dependencies

```php
use Illuminate\Support\Carbon;
use App\Models\Course;
use App\Models\CourseAuth;
```

## Methods

### `ActiveCourseAuths()`

**Returns:** `HasMany` relationship  
**Description:** Retrieves all active course authorizations for the user.

**Query Logic:**
- Filters out expired courses (`expire_date` is null or future)
- Excludes completed courses (`completed_at` is null)
- Excludes disabled courses (`disabled_at` is null)

```php
public function ActiveCourseAuths()
{
    return $this->hasMany(CourseAuth::class, 'user_id')
        ->where(function ($query) {
            $query->whereNull('expire_date')
                ->orWhere('expire_date', '>', Carbon::now());
        })
        ->whereNull('completed_at')
        ->whereNull('disabled_at');
}
```

**Usage Example:**
```php
$user = User::find(1);
$activeCourses = $user->ActiveCourseAuths;

foreach ($activeCourses as $courseAuth) {
    echo "Course: " . $courseAuth->course->title;
}
```

### `InactiveCourseAuths()`

**Returns:** `HasMany` relationship  
**Description:** Retrieves all inactive course authorizations for the user.

**Query Logic:**
- Returns course authorizations NOT in the active list
- Includes expired, completed, or disabled courses

```php
public function InactiveCourseAuths()
{
    return $this->hasMany(CourseAuth::class, 'user_id')
        ->whereNotIn('id', $this->ActiveCourseAuths->pluck('id')->toArray());
}
```

**Usage Example:**
```php
$user = User::find(1);
$inactiveCourses = $user->InactiveCourseAuths;

foreach ($inactiveCourses as $courseAuth) {
    echo "Inactive Course: " . $courseAuth->course->title;
    if ($courseAuth->expire_date && $courseAuth->expire_date < Carbon::now()) {
        echo " (Expired)";
    }
}
```

### `IsEnrolled(Course|int $Course_or_id): bool`

**Parameters:**
- `$Course_or_id` - Either a Course model instance or course ID (integer)

**Returns:** `bool` - True if user is enrolled in the course, false otherwise

**Description:** Checks if the user is currently enrolled in a specific course by examining active course authorizations.

**Type Safety:** Uses PHP 8+ union types for flexible parameter handling.

```php
public function IsEnrolled(Course|int $Course_or_id): bool
{
    return in_array(
        (is_int($Course_or_id) ? $Course_or_id : $Course_or_id->id),
        $this->ActiveCourseAuths->pluck('course_id')->toArray()
    );
}
```

**Usage Examples:**
```php
$user = User::find(1);
$course = Course::find(10);

// Check enrollment with Course model
if ($user->IsEnrolled($course)) {
    echo "User is enrolled in " . $course->title;
}

// Check enrollment with course ID
if ($user->IsEnrolled(10)) {
    echo "User is enrolled in course ID 10";
}

// Use in conditional logic
$canAccessCourse = $user->IsEnrolled($courseId) && $user->IsStudent();
```

## Business Logic

### Course Status States

The trait recognizes several course authorization states:

| State | Condition | Description |
|-------|-----------|-------------|
| **Active** | `expire_date` null or future, `completed_at` null, `disabled_at` null | User can access course |
| **Expired** | `expire_date` in past | Course access has expired |
| **Completed** | `completed_at` not null | User has completed the course |
| **Disabled** | `disabled_at` not null | Course access has been disabled |

### Expiration Handling

The trait uses Carbon for date comparisons:
- Null `expire_date` means no expiration
- Future `expire_date` means still active
- Past `expire_date` means expired and inactive

### Performance Considerations

**Potential N+1 Issues:**
The `InactiveCourseAuths()` method uses a subquery that could be inefficient with large datasets:

```php
// Current implementation
->whereNotIn('id', $this->ActiveCourseAuths->pluck('id')->toArray())

// More efficient alternative would be to use a single query with OR conditions
```

**Optimization Recommendations:**
1. Consider eager loading CourseAuth relationships
2. Cache active course IDs for frequently accessed users
3. Use database-level computed columns for status

## Integration Points

### With User Model
```php
class User extends Authenticatable
{
    use CourseAuthsTrait;
    
    // User can now access:
    // $user->ActiveCourseAuths
    // $user->InactiveCourseAuths  
    // $user->IsEnrolled($course)
}
```

### With Authorization Policies
```php
// In CoursePolicy
public function view(User $user, Course $course)
{
    return $user->IsEnrolled($course);
}
```

### With Blade Templates
```php
@if($user->IsEnrolled($course))
    <a href="{{ route('course.show', $course) }}">Access Course</a>
@else
    <button disabled>Enroll First</button>
@endif
```

## Error Handling

The trait handles several edge cases:

1. **Non-existent courses**: `IsEnrolled()` returns false for invalid course IDs
2. **Null relationships**: Methods gracefully handle users with no course authorizations
3. **Type safety**: Union types ensure proper parameter handling

## Testing Considerations

### Unit Testing
```php
// Test active course enrollment
$user = User::factory()->create();
$course = Course::factory()->create();
CourseAuth::factory()->create([
    'user_id' => $user->id,
    'course_id' => $course->id,
    'expire_date' => null,
    'completed_at' => null,
    'disabled_at' => null,
]);

$this->assertTrue($user->IsEnrolled($course));
$this->assertCount(1, $user->ActiveCourseAuths);
```

### Integration Testing
```php
// Test expired course handling
$expiredAuth = CourseAuth::factory()->create([
    'user_id' => $user->id,
    'course_id' => $course->id,
    'expire_date' => Carbon::yesterday(),
]);

$this->assertFalse($user->IsEnrolled($course));
$this->assertCount(1, $user->InactiveCourseAuths);
```

## Related Models

- [CourseAuth](../Models/CourseAuth.md) - Course authorization records
- [Course](../Models/Course.md) - Course definitions
- [User](../Models/User.md) - User model that uses this trait

## Related Traits

- [ExamsTrait](ExamsTrait.md) - Depends on active course authorizations
- [RolesTrait](RolesTrait.md) - Works with course access permissions

---

*Last updated: July 28, 2025*
