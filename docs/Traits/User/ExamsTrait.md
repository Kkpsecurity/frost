# ExamsTrait

**File:** `app/Models/Traits/User/ExamsTrait.php`  
**Namespace:** `App\Models\Traits\User`

## Overview

The ExamsTrait provides exam authorization management for users. It integrates with the course enrollment system to determine active exam access. This trait is essential for controlling when and how users can access examinations based on their course enrollments and authorization status.

## Dependencies

```php
use App\Models\ExamAuth;
```

**Trait Dependencies:** This trait depends on the `CourseAuthsTrait` being present in the same model, as it uses the `ActiveCourseAuths` relationship.

## Methods

### `ActiveExamAuth(): ?ExamAuth`

**Returns:** `ExamAuth|null` - Active exam authorization or null if none found

**Description:** Searches through all active course authorizations to find an active exam authorization. This method leverages the hierarchical relationship between courses and exams.

**Logic Flow:**
1. Iterates through all active course authorizations
2. Checks each course authorization for an active exam authorization
3. Returns the first active exam authorization found
4. Returns null if no active exam authorization exists

```php
public function ActiveExamAuth(): ?ExamAuth
{
    foreach ($this->ActiveCourseAuths as $CourseAuth) {
        // ActiveExamAuth() handles expiration
        if ($ExamAuth = $CourseAuth->ActiveExamAuth()) {
            return $ExamAuth;
        }
    }

    return null;
}
```

## Business Logic

### Exam Access Hierarchy

The trait implements a multi-level authorization system:

```
User → CourseAuth (Active) → ExamAuth (Active) → Exam Access
```

1. **User Level**: User must be enrolled in a course
2. **Course Level**: Course authorization must be active (not expired/disabled)
3. **Exam Level**: Exam authorization must be active within the course context

### Authorization Chain

```php
// Conceptual flow
$user = Auth::user();

// Step 1: Check course enrollment
$activeCourses = $user->ActiveCourseAuths; // From CourseAuthsTrait

// Step 2: Check exam authorization within courses
foreach ($activeCourses as $courseAuth) {
    $examAuth = $courseAuth->ActiveExamAuth(); // ExamAuth model method
    if ($examAuth) {
        return $examAuth; // Found active exam
    }
}

return null; // No active exam access
```

## Usage Patterns

### Basic Exam Access Check

```php
$user = Auth::user();
$examAuth = $user->ActiveExamAuth();

if ($examAuth) {
    echo "User has access to exam: " . $examAuth->exam->title;
    return redirect()->route('exam.take', $examAuth->exam);
} else {
    echo "No active exam authorization";
    return redirect()->route('dashboard');
}
```

### Controller Integration

```php
class ExamController extends Controller
{
    public function show(Exam $exam)
    {
        $user = Auth::user();
        $examAuth = $user->ActiveExamAuth();
        
        // Verify user has access to this specific exam
        if (!$examAuth || $examAuth->exam_id !== $exam->id) {
            abort(403, 'Unauthorized exam access');
        }
        
        return view('exam.show', compact('exam', 'examAuth'));
    }
    
    public function take()
    {
        $user = Auth::user();
        $examAuth = $user->ActiveExamAuth();
        
        if (!$examAuth) {
            return redirect()->route('dashboard')
                           ->with('error', 'No active exam available');
        }
        
        return view('exam.take', compact('examAuth'));
    }
}
```

### Middleware Usage

```php
class ExamAccessMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user->ActiveExamAuth()) {
            return redirect()->route('dashboard')
                           ->with('error', 'Exam access required');
        }
        
        return $next($request);
    }
}

// Route protection
Route::middleware('exam.access')->group(function () {
    Route::get('/exam/take', [ExamController::class, 'take']);
    Route::post('/exam/submit', [ExamController::class, 'submit']);
});
```

## Integration Points

### With CourseAuthsTrait

**Dependency:** This trait requires `CourseAuthsTrait` to function properly.

```php
class User extends Authenticatable
{
    use CourseAuthsTrait; // Required dependency
    use ExamsTrait;       // Uses ActiveCourseAuths from CourseAuthsTrait
    
    // Both traits work together to provide exam access control
}
```

### With ExamAuth Model

The trait delegates expiration logic to the `ExamAuth` model:

```php
// In ExamAuth model (conceptual)
public function ActiveExamAuth(): ?ExamAuth
{
    // Handle expiration logic
    if ($this->expire_date && $this->expire_date < Carbon::now()) {
        return null; // Expired
    }
    
    if ($this->disabled_at) {
        return null; // Disabled
    }
    
    return $this; // Active
}
```

### With Blade Templates

```php
@php
    $examAuth = Auth::user()->ActiveExamAuth();
@endphp

@if($examAuth)
    <div class="exam-access">
        <h3>Active Exam: {{ $examAuth->exam->title }}</h3>
        <p>Time Limit: {{ $examAuth->exam->time_limit }} minutes</p>
        <a href="{{ route('exam.take') }}" class="btn btn-primary">
            Start Exam
        </a>
    </div>
@else
    <div class="no-exam">
        <p>No active exams available.</p>
        <a href="{{ route('courses.browse') }}">Browse Courses</a>
    </div>
@endif
```

## Security Considerations

### Access Control

The trait provides secure exam access by:

1. **Course Enrollment Verification**: Only users with active course access can access exams
2. **Expiration Handling**: Expired authorizations are automatically excluded
3. **Delegation**: Specific expiration logic handled by the `ExamAuth` model

### Potential Security Issues

```php
// ❌ Don't bypass the trait
$examAuth = ExamAuth::where('user_id', $user->id)->first(); // Unsafe

// ✅ Use the trait method
$examAuth = $user->ActiveExamAuth(); // Safe, includes all checks
```

## Performance Considerations

### Optimization Opportunities

The current implementation could be optimized for performance:

```php
// Current implementation (N+1 potential)
foreach ($this->ActiveCourseAuths as $CourseAuth) {
    if ($ExamAuth = $CourseAuth->ActiveExamAuth()) {
        return $ExamAuth;
    }
}

// Optimized version (single query)
public function ActiveExamAuth(): ?ExamAuth
{
    return ExamAuth::whereIn('course_auth_id', 
                           $this->ActiveCourseAuths->pluck('id'))
                  ->where(function($query) {
                      $query->whereNull('expire_date')
                            ->orWhere('expire_date', '>', Carbon::now());
                  })
                  ->whereNull('disabled_at')
                  ->first();
}
```

### Caching Strategies

```php
// Cache active exam authorization
public function ActiveExamAuth(): ?ExamAuth
{
    $cacheKey = "user_active_exam_{$this->id}";
    
    return Cache::remember($cacheKey, 300, function () {
        foreach ($this->ActiveCourseAuths as $CourseAuth) {
            if ($ExamAuth = $CourseAuth->ActiveExamAuth()) {
                return $ExamAuth;
            }
        }
        return null;
    });
}
```

## Testing Strategies

### Unit Testing

```php
public function test_returns_active_exam_auth_when_available()
{
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $exam = Exam::factory()->create(['course_id' => $course->id]);
    
    // Create active course authorization
    $courseAuth = CourseAuth::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'expire_date' => null,
        'completed_at' => null,
        'disabled_at' => null,
    ]);
    
    // Create active exam authorization
    $examAuth = ExamAuth::factory()->create([
        'course_auth_id' => $courseAuth->id,
        'exam_id' => $exam->id,
        'expire_date' => Carbon::tomorrow(),
        'disabled_at' => null,
    ]);
    
    $this->assertEquals($examAuth->id, $user->ActiveExamAuth()->id);
}

public function test_returns_null_when_no_active_exam_auth()
{
    $user = User::factory()->create();
    
    $this->assertNull($user->ActiveExamAuth());
}

public function test_ignores_expired_exam_authorization()
{
    $user = User::factory()->create();
    $course = Course::factory()->create();
    
    $courseAuth = CourseAuth::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
    
    // Create expired exam authorization
    ExamAuth::factory()->create([
        'course_auth_id' => $courseAuth->id,
        'expire_date' => Carbon::yesterday(),
    ]);
    
    $this->assertNull($user->ActiveExamAuth());
}
```

### Integration Testing

```php
public function test_exam_access_requires_active_authorization()
{
    $user = User::factory()->create();
    
    // Try to access exam without authorization
    $this->actingAs($user)
         ->get('/exam/take')
         ->assertRedirect('/dashboard');
}

public function test_can_access_exam_with_valid_authorization()
{
    $user = User::factory()->create();
    // ... create valid exam authorization ...
    
    $this->actingAs($user)
         ->get('/exam/take')
         ->assertStatus(200);
}
```

## Error Handling

The trait handles various edge cases:

1. **No Course Enrollments**: Returns null gracefully
2. **No Exam Authorizations**: Returns null when no exams are authorized
3. **Expired Authorizations**: Delegates to ExamAuth model for expiration logic
4. **Multiple Active Exams**: Returns the first active exam found

## Common Use Cases

### Exam Dashboard Widget

```php
// In dashboard controller
public function dashboard()
{
    $user = Auth::user();
    $activeExam = $user->ActiveExamAuth();
    
    return view('dashboard', compact('activeExam'));
}
```

### Automatic Exam Redirect

```php
// In home controller
public function home()
{
    $user = Auth::user();
    
    if ($examAuth = $user->ActiveExamAuth()) {
        return redirect()->route('exam.instructions', $examAuth->exam);
    }
    
    return redirect($user->Dashboard());
}
```

### Exam Progress Tracking

```php
public function examProgress()
{
    $user = Auth::user();
    $examAuth = $user->ActiveExamAuth();
    
    if (!$examAuth) {
        return null;
    }
    
    return [
        'exam' => $examAuth->exam,
        'started_at' => $examAuth->started_at,
        'time_remaining' => $examAuth->timeRemaining(),
        'questions_completed' => $examAuth->questionsCompleted(),
    ];
}
```

## Related Models

- [ExamAuth](../Models/ExamAuth.md) - Exam authorization records
- [CourseAuth](../Models/CourseAuth.md) - Course authorization (dependency)
- [Exam](../Models/Exam.md) - Exam definitions
- [User](../Models/User.md) - User model that uses this trait

## Related Traits

- [CourseAuthsTrait](CourseAuthsTrait.md) - Required dependency for course enrollments
- [RolesTrait](RolesTrait.md) - May affect exam access permissions

---

*Last updated: July 28, 2025*
