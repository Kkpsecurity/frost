# RolesTrait

**File:** `app/Models/Traits/User/RolesTrait.php`  
**Namespace:** `App\Models\Traits\User`

## Overview

The RolesTrait provides role-based permission checking and dashboard routing for users. It implements a hierarchical role system where lower role IDs have higher privileges. This trait is fundamental to the application's authorization system and user interface routing.

## Role Hierarchy

The trait implements a hierarchical role system with the following structure:

| Role ID | Role Name | Access Level | Description |
|---------|-----------|--------------|-------------|
| 1 | SysAdmin | Highest | System administrators with full access |
| 2 | Administrator | High | Application administrators |
| 3 | Support | Medium | Support staff with limited admin access |
| 4 | Instructor | Medium | Course instructors and content creators |
| 5 | Student | Lowest | Regular students with basic access |

**Hierarchy Logic:** Lower role IDs inherit permissions from higher role IDs.

## Methods

### Role Checking Methods

#### `IsSysAdmin(): bool`
**Returns:** `bool` - True if user is a system administrator  
**Role ID:** 1 only

```php
public function IsSysAdmin(): bool
{
    return $this->role_id == 1;
}
```

**Usage:**
```php
if ($user->IsSysAdmin()) {
    // Full system access
    echo "Welcome, System Administrator!";
}
```

#### `IsAdministrator(): bool`
**Returns:** `bool` - True if user is administrator level or higher  
**Role IDs:** 1-2 (SysAdmin, Administrator)

```php
public function IsAdministrator(): bool
{
    return $this->role_id <= 2;
}
```

**Usage:**
```php
if ($user->IsAdministrator()) {
    // Admin panel access
    return redirect()->route('admin.dashboard');
}
```

#### `IsSupport(): bool`
**Returns:** `bool` - True if user has support level access or higher  
**Role IDs:** 1-3 (SysAdmin, Administrator, Support)

```php
public function IsSupport(): bool
{
    return $this->role_id <= 3;
}
```

**Usage:**
```php
if ($user->IsSupport()) {
    // Support ticket access
    $tickets = SupportTicket::all();
}
```

#### `IsInstructor(): bool`
**Returns:** `bool` - True if user has instructor level access or higher  
**Role IDs:** 1-4 (SysAdmin, Administrator, Support, Instructor)

```php
public function IsInstructor(): bool
{
    return $this->role_id <= 4;
}
```

**Usage:**
```php
if ($user->IsInstructor()) {
    // Course management access
    $courses = $user->managedCourses;
}
```

#### `IsStudent(): bool`
**Returns:** `bool` - True if user is specifically a student  
**Role ID:** 5 only

```php
public function IsStudent(): bool
{
    return $this->role_id == 5;
}
```

**Usage:**
```php
if ($user->IsStudent()) {
    // Student-specific features
    $enrolledCourses = $user->ActiveCourseAuths;
}
```

#### `IsAnyAdmin(): bool`
**Returns:** `bool` - True if user has any administrative privileges  
**Role IDs:** 1-4 (All except Student)

```php
public function IsAnyAdmin(): bool
{
    return $this->role_id <= 4;
}
```

**Usage:**
```php
if ($user->IsAnyAdmin()) {
    // Any admin functionality
    echo "Admin Menu Available";
}
```

### Dashboard Routing

#### `Dashboard(): string`
**Returns:** `string` - Appropriate dashboard route URL based on user role

**Route Mapping:**
- **Role 1-3**: `admin.dashboard` - Administrative dashboard
- **Role 4**: `admin.instructors.dashboard` - Instructor-specific dashboard  
- **Role 5+**: `classroom.dashboard` - Student dashboard

```php
public function Dashboard(): string
{
    switch ($this->role_id) {
        case '1':
            return route('admin.dashboard');
        case '2':
            return route('admin.dashboard');
        case '3':
            return route('admin.dashboard');
        case '4':
            return route('admin.instructors.dashboard');
        default:
            return route('classroom.dashboard');
    }
}
```

**Usage Examples:**
```php
// Redirect user to appropriate dashboard
return redirect($user->Dashboard());

// Generate dashboard links
<a href="{{ Auth::user()->Dashboard() }}">My Dashboard</a>

// Controller usage
public function home()
{
    return redirect(Auth::user()->Dashboard());
}
```

## Permission Matrix

### Access Control Matrix

| Feature | SysAdmin | Admin | Support | Instructor | Student |
|---------|----------|-------|---------|------------|---------|
| System Config | ✅ | ❌ | ❌ | ❌ | ❌ |
| User Management | ✅ | ✅ | ❌ | ❌ | ❌ |
| Support Tickets | ✅ | ✅ | ✅ | ❌ | ❌ |
| Course Management | ✅ | ✅ | ✅ | ✅ | ❌ |
| Student Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |

### Method Return Values by Role

```php
// Role ID: 1 (SysAdmin)
$user->IsSysAdmin()     // true
$user->IsAdministrator() // true  
$user->IsSupport()      // true
$user->IsInstructor()   // true
$user->IsStudent()      // false
$user->IsAnyAdmin()     // true

// Role ID: 5 (Student)
$user->IsSysAdmin()     // false
$user->IsAdministrator() // false
$user->IsSupport()      // false
$user->IsInstructor()   // false
$user->IsStudent()      // true
$user->IsAnyAdmin()     // false
```

## Integration Patterns

### With Laravel Policies

```php
// In CoursePolicy
public function update(User $user, Course $course)
{
    return $user->IsInstructor();
}

// In UserPolicy  
public function viewAny(User $user)
{
    return $user->IsAdministrator();
}
```

### With Middleware

```php
// Custom middleware
class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();
        
        switch ($role) {
            case 'admin':
                if (!$user->IsAdministrator()) {
                    abort(403);
                }
                break;
            case 'instructor':
                if (!$user->IsInstructor()) {
                    abort(403);
                }
                break;
        }
        
        return $next($request);
    }
}

// Route usage
Route::middleware('role:admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

### With Blade Templates

```php
@if(Auth::user()->IsAdministrator())
    <div class="admin-panel">
        <a href="{{ route('admin.users') }}">Manage Users</a>
    </div>
@endif

@if(Auth::user()->IsInstructor())
    <div class="instructor-tools">
        <a href="{{ route('courses.create') }}">Create Course</a>
    </div>
@endif

@if(Auth::user()->IsStudent())
    <div class="student-dashboard">
        <a href="{{ route('courses.enrolled') }}">My Courses</a>
    </div>
@endif
```

### With Route Model Binding

```php
// In RouteServiceProvider or Controller
public function boot()
{
    Route::bind('adminUser', function ($value) {
        $user = User::findOrFail($value);
        
        if (!Auth::user()->IsAdministrator()) {
            abort(403);
        }
        
        return $user;
    });
}
```

## Security Considerations

### Privilege Escalation Prevention

The hierarchical system prevents privilege escalation:
```php
// Safe: Lower role IDs have higher privileges
if ($user->IsAdministrator()) {
    // Admin can access support features
    $supportFeatures = true;
}

// Dangerous: Don't check role_id directly
if ($user->role_id < 3) { // Avoid this pattern
    // Direct ID comparison can be error-prone
}
```

### Role Assignment Validation

```php
// In User model or service
public function assignRole(int $roleId)
{
    // Validate role assignment permissions
    if (Auth::user()->role_id >= $roleId) {
        throw new UnauthorizedException('Cannot assign higher or equal role');
    }
    
    $this->role_id = $roleId;
    $this->save();
}
```

## Testing Strategies

### Unit Testing

```php
public function test_sys_admin_has_all_permissions()
{
    $user = User::factory()->create(['role_id' => 1]);
    
    $this->assertTrue($user->IsSysAdmin());
    $this->assertTrue($user->IsAdministrator());
    $this->assertTrue($user->IsSupport());
    $this->assertTrue($user->IsInstructor());
    $this->assertFalse($user->IsStudent());
    $this->assertTrue($user->IsAnyAdmin());
}

public function test_student_has_limited_permissions()
{
    $user = User::factory()->create(['role_id' => 5]);
    
    $this->assertFalse($user->IsSysAdmin());
    $this->assertFalse($user->IsAdministrator());
    $this->assertFalse($user->IsSupport());
    $this->assertFalse($user->IsInstructor());
    $this->assertTrue($user->IsStudent());
    $this->assertFalse($user->IsAnyAdmin());
}

public function test_dashboard_routes_correctly()
{
    $admin = User::factory()->create(['role_id' => 2]);
    $instructor = User::factory()->create(['role_id' => 4]);
    $student = User::factory()->create(['role_id' => 5]);
    
    $this->assertEquals(route('admin.dashboard'), $admin->Dashboard());
    $this->assertEquals(route('admin.instructors.dashboard'), $instructor->Dashboard());
    $this->assertEquals(route('classroom.dashboard'), $student->Dashboard());
}
```

### Feature Testing

```php
public function test_admin_can_access_user_management()
{
    $admin = User::factory()->create(['role_id' => 2]);
    
    $this->actingAs($admin)
         ->get('/admin/users')
         ->assertStatus(200);
}

public function test_student_cannot_access_admin_panel()
{
    $student = User::factory()->create(['role_id' => 5]);
    
    $this->actingAs($student)
         ->get('/admin')
         ->assertStatus(403);
}
```

## Performance Considerations

### Database Optimization

```php
// Efficient role-based queries
$instructors = User::where('role_id', '<=', 4)->get();
$students = User::where('role_id', 5)->get();

// Index on role_id column recommended
// CREATE INDEX idx_users_role_id ON users(role_id);
```

### Caching Role Checks

```php
// Cache expensive role computations
public function getAdminUsersAttribute()
{
    return Cache::remember('admin_users', 3600, function () {
        return User::where('role_id', '<=', 4)->get();
    });
}
```

## Common Patterns

### Menu Generation

```php
public function getMenuItems()
{
    $items = [];
    
    if ($this->IsStudent()) {
        $items[] = ['title' => 'My Courses', 'route' => 'courses.enrolled'];
        $items[] = ['title' => 'Grades', 'route' => 'grades.index'];
    }
    
    if ($this->IsInstructor()) {
        $items[] = ['title' => 'Manage Courses', 'route' => 'courses.manage'];
        $items[] = ['title' => 'Student Progress', 'route' => 'progress.index'];
    }
    
    if ($this->IsAdministrator()) {
        $items[] = ['title' => 'User Management', 'route' => 'admin.users'];
        $items[] = ['title' => 'System Settings', 'route' => 'admin.settings'];
    }
    
    return $items;
}
```

### Conditional Loading

```php
public function scopeForRole($query, $role)
{
    switch ($role) {
        case 'admin':
            return $query->where('role_id', '<=', 2);
        case 'instructor':
            return $query->where('role_id', 4);
        case 'student':
            return $query->where('role_id', 5);
        default:
            return $query;
    }
}
```

## Related Models

- [User](../Models/User.md) - User model that uses this trait
- [Role](../Models/Role.md) - Role definitions and metadata

## Related Traits

- [CourseAuthsTrait](CourseAuthsTrait.md) - Works with instructor course management
- [ExamsTrait](ExamsTrait.md) - Integrates with role-based exam access

---

*Last updated: July 28, 2025*
