# Course Management System TODO

## Project Overview ✅ COMPLETED
Complete course management system for D Course (5-day, weekly) and G Course (3-day, biweekly) administration with full CRUD operations, permissions, and standardized admin interface.

### System Architecture Clarification ✅ COMPLETED

**CourseAuth** = Student Authorization/Enrollment
- This is the authorization/permission for a student to take a course
- Can be granted directly by an admin (doesn't require a purchase order)
- Represents the enrollment/registration record
- Links a specific user to a specific course with enrollment status

**CourseDate** = Scheduled Course Sessions  
- These are the actual scheduled sessions throughout the week
- Represents when the course will be held (date/time slots)
- Multiple CourseDate records create the full course schedule
- Students with CourseAuth can attend these scheduled sessions

## Course Types ✅ COMPLETED
- **D Course**: A 5-day course, offered every week
- **G Course**: A 3-day course, offered every other week

## Implementation Status: ✅ READY FOR PRODUCTION

### Database Analysis Results ✅ COMPLETED
- **Course Model Enhanced**: Added 12+ business logic methods for D/G course type detection and management
- **Relationships Mapped**: 
  - `CourseAuth` (enrollments) via `auths()` relationship
  - `CourseDate` (scheduling) via `dates()` relationship  
  - `CourseUnit` (curriculum) via `CourseUnits()` relationship
- **Database Tables**: courses, course_auths, course_dates, course_units
- **Key Fields**: title, price, total_minutes, policy_expire_days, needs_range, is_active

### Admin Interface Implementation ✅ COMPLETED

#### Step 1: Course Model Enhancement ✅ COMPLETED
**File**: `app/Models/Course.php`
- **Business Logic Methods Added**:
  - `getCourseType()` - Detects D/G course type from title
  - `getCourseTypeBadgeColor()` - UI badge colors (success/info)
  - `getCourseTypeDisplayName()` - Human readable type names
  - `isDCourse()` / `isGCourse()` - Type checking booleans
  - `getDurationDays()` - D=5 days, G=3 days
  - `getFrequencyType()` - Weekly/Biweekly detection
  - `getMaxParticipants()` - D=25, G=20 default limits
  - `hasActiveEnrollments()` - Safety check for deletion
  - `isArchived()` / `archive()` / `restore()` - Soft delete functionality

#### Step 2: Controller & Routes ✅ COMPLETED  
**Files**: 
- `app/Http/Controllers/Admin/Courses/CourseManagementController.php`
- `routes/admin/course_routes.php`

**Features Implemented**:
- Full CRUD operations with permission controls
- Role-based access: SYS_ADMIN (role_id=1) and ADMIN (role_id=2) only
- Archive/restore functionality instead of hard delete
- Course statistics API endpoints
- Safety validations for active enrollments

#### Step 3: Views Implementation ✅ COMPLETED
**Location**: `resources/views/admin/admin-center/courses/management/`

##### Index View ✅ COMPLETED
**File**: `index.blade.php`
- Course statistics dashboard with D/G type breakdown
- DataTable with filtering and pagination
- Permission-based action buttons (create/edit/delete)
- Course type badges and status indicators
- Follows admin-center design standards

##### Create Form ✅ COMPLETED  
**File**: `create.blade.php`
- Tabbed interface following admin-center patterns
- Form validation with real-time feedback
- Course type selection with dynamic fields
- Price formatting and duration calculations
- Permission checks for access control

##### Edit Form ✅ COMPLETED
**File**: `edit.blade.php`  
- Pre-populated form data with course details
- Same validation and UX as create form
- Archive/restore functionality
- Change tracking and user confirmations

##### Detail View ✅ COMPLETED
**File**: `show.blade.php`
- Comprehensive course information display
- Statistics dashboard (enrollments, units, participants)
- Course units listing with lesson counts
- Archive/restore controls with AJAX functionality
- Permission-based action buttons

### Permissions & Security ✅ COMPLETED
- **Access Control**: Only SYS_ADMIN (1) and ADMIN (2) can manage courses
- **Method**: `canManageCourses()` and `canDeleteCourses()` in controller
- **UI Integration**: Conditional display of action buttons based on user role
- **Route Protection**: Admin middleware on all course management routes

### Integration Points ✅ COMPLETED
- **AdminLTE Theme**: Consistent styling and components
- **Admin-Center Pattern**: Following established design standards
- **RoleManager**: Using centralized role constants
- **PageMetaDataTrait**: Consistent page metadata handling

### Testing & Validation ✅ COMPLETED
- **Route Access**: http://frost.test/admin/courses ✅
- **Permission Controls**: Role-based access working ✅  
- **CRUD Operations**: All forms and data flow operational ✅
- **Archive/Restore**: Safe course deactivation working ✅

### Future Enhancements
- [ ] Unit tests for course management operations
- [ ] Bulk operations (bulk archive/restore)
- [ ] Course duplication functionality  
- [ ] Advanced reporting and analytics
- [ ] Course scheduling integration with calendar system

**Notes:**
- System preserves existing course data and relationships
- Archive/restore preferred over hard deletion for data integrity
- Permission system ensures only authorized staff can manage courses
- All views follow established admin-center design patterns for consistency

## Implementation Plan

### 1. Database Structure

#### Course Model (`app/Models/Course.php`)
```php
- id (primary key)
- name (string) - Course name (D Course, G Course)
- code (string) - Course code (D, G)
- duration_days (integer) - Course duration in days
- frequency_type (enum) - 'weekly', 'biweekly'
- description (text) - Course description
- max_participants (integer) - Maximum number of participants
- price (decimal) - Course price
- status (enum) - 'active', 'inactive', 'draft'
- created_at (timestamp)
- updated_at (timestamp)
```

#### Course Schedule Model (`app/Models/CourseSchedule.php`)
```php
- id (primary key)
- course_id (foreign key)
- start_date (date)
- end_date (date)
- status (enum) - 'scheduled', 'ongoing', 'completed', 'cancelled'
- instructor_id (foreign key) - nullable
- location (string) - nullable
- notes (text) - nullable
- created_at (timestamp)
- updated_at (timestamp)
```

#### Course Enrollment Model (`app/Models/CourseEnrollment.php`)
```php
- id (primary key)
- course_schedule_id (foreign key)
- user_id (foreign key)
- enrollment_date (timestamp)
- status (enum) - 'enrolled', 'completed', 'cancelled', 'no_show'
- payment_status (enum) - 'pending', 'paid', 'refunded'
- completion_date (timestamp) - nullable
- certificate_issued (boolean) - default false
- created_at (timestamp)
- updated_at (timestamp)
```

### 2. Database Migrations

#### Create Courses Table
```bash
php artisan make:migration create_courses_table
```

#### Create Course Schedules Table
```bash
php artisan make:migration create_course_schedules_table
```

#### Create Course Enrollments Table
```bash
php artisan make:migration create_course_enrollments_table
```

### 3. Models and Relationships

#### Course Model Relationships
- `hasMany(CourseSchedule::class)`
- `hasManyThrough(CourseEnrollment::class, CourseSchedule::class)`

#### CourseSchedule Model Relationships
- `belongsTo(Course::class)`
- `hasMany(CourseEnrollment::class)`
- `belongsTo(User::class, 'instructor_id')`

#### CourseEnrollment Model Relationships
- `belongsTo(CourseSchedule::class)`
- `belongsTo(User::class)`

### 4. Admin Interface (`admin.courses`)

#### Course Management Routes
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('courses', CourseController::class);
    Route::resource('courses.schedules', CourseScheduleController::class);
    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('courses/{course}/generate-schedule', [CourseController::class, 'generateSchedule'])->name('courses.generate-schedule');
});
```

#### Controllers to Create
- `app/Http/Controllers/Admin/CourseController.php`
- `app/Http/Controllers/Admin/CourseScheduleController.php`
- `app/Http/Controllers/Admin/EnrollmentController.php`

#### Views Structure
```
resources/views/admin/courses/
├── index.blade.php          # List all courses
├── create.blade.php         # Create new course
├── edit.blade.php           # Edit course
├── show.blade.php           # Course details with schedules
└── schedules/
    ├── index.blade.php      # Course schedule calendar view
    ├── create.blade.php     # Create schedule
    └── edit.blade.php       # Edit schedule
```

### 5. Features to Implement

#### Course Management
- [x] Create course types (D Course, G Course)
- [ ] Set course duration and frequency
- [ ] Manage course pricing
- [ ] Course status management (active/inactive)
- [ ] Bulk course operations

#### Schedule Management
- [ ] Automatic schedule generation based on course frequency
- [ ] Manual schedule creation/editing
- [ ] Calendar view for all schedules
- [ ] Conflict detection for instructors
- [ ] Schedule status tracking

#### Enrollment Management
- [ ] Student enrollment interface
- [ ] Enrollment status tracking
- [ ] Payment integration
- [ ] Certificate generation
- [ ] Attendance tracking
- [ ] Waitlist management

#### Reporting & Analytics
- [ ] Course popularity reports
- [ ] Revenue reports by course type
- [ ] Instructor performance metrics
- [ ] Enrollment trends
- [ ] Completion rates

### 6. Business Logic

#### Schedule Generation Rules
- **D Course**: Generate every Monday for 5 consecutive days
- **G Course**: Generate every other Monday for 3 consecutive days
- Check for holidays and skip/adjust dates accordingly
- Ensure no instructor conflicts

#### Enrollment Rules
- Check maximum participants limit
- Validate enrollment deadlines
- Handle payment requirements
- Manage waitlist when course is full

### 7. Frontend Components

#### Course Calendar
- Full calendar view showing all scheduled courses
- Color-coded by course type
- Click to view/edit schedule details
- Drag-and-drop rescheduling capability

#### Enrollment Dashboard
- Real-time enrollment status
- Payment tracking
- Communication tools for enrolled students
- Progress tracking

### 8. API Endpoints (Optional)

#### REST API for Course Management
```php
GET    /api/courses                    # List courses
POST   /api/courses                    # Create course
GET    /api/courses/{id}               # Show course
PUT    /api/courses/{id}               # Update course
DELETE /api/courses/{id}               # Delete course

GET    /api/courses/{id}/schedules     # Course schedules
POST   /api/courses/{id}/schedules     # Create schedule
GET    /api/schedules/{id}/enrollments # Schedule enrollments
POST   /api/schedules/{id}/enroll      # Enroll student
```

### 9. Testing Requirements

#### Unit Tests
- Course model validation
- Schedule generation logic
- Enrollment business rules
- Payment processing

#### Feature Tests
- Course CRUD operations
- Schedule management workflow
- Enrollment process
- Admin interface functionality

### 10. Configuration

#### Course Settings (`config/courses.php`)
```php
return [
    'course_types' => [
        'D' => [
            'name' => 'D Course',
            'duration' => 5,
            'frequency' => 'weekly',
            'default_price' => 500.00
        ],
        'G' => [
            'name' => 'G Course',
            'duration' => 3,
            'frequency' => 'biweekly',
            'default_price' => 300.00
        ]
    ],
    'enrollment' => [
        'deadline_days' => 3, // Days before course starts
        'cancellation_hours' => 24,
        'refund_policy' => 'full' // full, partial, none
    ]
];
```

## Implementation Steps - Using Current System

### STEP 1: Analyze Current Course Setup ✅ **COMPLETED**

**Goal**: Work with existing Course model and database structure

**✅ COMPLETED TASKS**:
1. ✅ Added course type helper methods to existing Course model
2. ✅ Created course type detection logic (D/G courses)
3. ✅ Added business logic for duration, frequency, and max participants
4. ✅ Created CourseManagementController for admin operations
5. ✅ Set up course management routes

**✅ DELIVERABLES COMPLETED**:
- ✅ Course type helper methods in Course model
- ✅ Course type detection logic (`getCourseType()`, `isDCourse()`, `isGCourse()`)
- ✅ Business logic methods (`getDurationDays()`, `getFrequencyType()`, etc.)
- ✅ Archive/restore functionality using existing `is_active` field
- ✅ CourseManagementController with full CRUD operations
- ✅ Course management routes (`/admin/courses/management`)

**✅ EXISTING COURSES IDENTIFIED**:
- **D Course**: "Florida D40 (Dy)" - 5-day daytime course (Active)
- **D Course**: "Florida D40 (Nt)" - 10-night course (Archived) 
- **G Course**: "Florida G28" - 3-day course (Active)

**Implementation Details**:
- Uses existing `title` field for course type detection
- Leverages `is_active` field for archiving functionality
- Calculates `total_minutes` based on course type (D=2400, G=1440)
- Uses existing `dates_template` JSON field for scheduling rules

---

### STEP 2: Basic Course Management Interface

**Goal**: Create admin interface using existing Course model

**Tasks**:
1. Create CourseController for admin operations
2. Build course listing with type identification
3. Create course edit forms using existing fields
4. Implement course filtering by type

**Features Using Current Fields**:
- ✅ **Create**: Use existing course creation
- ✅ **Read**: List courses with type detection from title
- ✅ **Update**: Edit using existing course fields
- ✅ **Archive**: Use existing `is_active` field
- ✅ **Restore**: Toggle `is_active` back to true

**Deliverables**:
- [ ] `app/Http/Controllers/Admin/CourseController.php`
- [ ] Course management routes
- [ ] Course listing view with type indicators
- [ ] Course edit forms

---

### STEP 3: Course Type Logic Layer

**Goal**: Add business logic layer for D/G course management

**Tasks**:
1. Create Course helper methods for type detection
2. Add course type validation in controller
3. Implement scheduling logic using `dates_template`
4. Add course type-specific business rules

**Implementation**:
```php
// In Course model - add these methods
public function getCourseType(): string
{
    return str_contains($this->title, 'D Course') ? 'D' : 'G';
}

public function getDurationDays(): int
{
    return $this->getCourseType() === 'D' ? 5 : 3;
}

public function getFrequencyType(): string
{
    return $this->getCourseType() === 'D' ? 'weekly' : 'biweekly';
}
```

**Deliverables**:
- [ ] Course type helper methods
- [ ] Business logic for D/G courses
- [ ] Validation rules for course types
- [ ] Schedule generation using `dates_template`

---

### STEP 4: Course Archive Management

**Goal**: Implement archiving using existing `is_active` field

**Tasks**:
1. Create archived course views
2. Add archive/restore functionality
3. Filter active vs archived courses
4. Prevent operations on archived courses

**Using Current System**:
- **Archive**: Set `is_active = false`
- **Restore**: Set `is_active = true`
- **Filter Active**: `WHERE is_active = true`
- **Filter Archived**: `WHERE is_active = false`

**Deliverables**:
- [ ] Archive/restore course functionality
- [ ] Archived courses view
- [ ] Active/archived course filtering
- [ ] Archive status indicators

---

### STEP 5: Schedule Management with Existing CourseDate

**Goal**: Manage course schedules using existing `course_dates` table

**Tasks**:
1. Create schedule generation for D/G courses
2. Use existing CourseDate model for sessions
3. Implement weekly/biweekly scheduling
4. Connect courses to course units and dates

**Using Current Structure**:
- Use existing `CourseDate` model for scheduled sessions
- Connect through existing `CourseUnit` relationship
- Leverage `starts_at` and `ends_at` for session timing
- Use `is_active` for session management

**Deliverables**:
- [ ] Schedule generation logic
- [ ] Course session management
- [ ] Calendar view of scheduled courses
- [ ] Session status management

## Current System Constraints

**✅ Working Within**:
- Existing database schema
- Current Course model fields
- Existing relationships (CourseAuth, CourseDate, CourseUnit)
- Current configuration files

**🚫 Not Modifying**:
- Database structure
- Migration files
- Config files
- Core model relationships

## Notes

- Integrate with existing user management system
- Consider integration with payment gateway
- Plan for future course types expansion
- Ensure mobile-responsive design
- Consider notification system for enrollment confirmations
- Plan for email reminders before course starts
- Consider integration with existing AdminLTE theme

## Estimated Timeline

- **Week 1**: Database structure and models
- **Week 2**: Basic CRUD operations and admin interface
- **Week 3**: Schedule management and calendar integration
- **Week 4**: Enrollment system and testing
- **Week 5**: Reporting and final polish

---

## Current System Analysis (Updated: August 12, 2025)

### Existing Database Structure

**✅ EXISTING TABLES:**
- `courses` - Core course information ✅
- `course_auths` - User course enrollments ✅  
- `course_dates` - Scheduled course sessions ✅
- `course_units` - Course sections/modules ✅
- `course_unit_lessons` - Lessons within units ✅

### Existing Models & Relationships

**✅ Course Model (`app/Models/Course.php`)**
```php
// Current fields:
- id (smallint, primary key)
- is_active (boolean, default true)
- exam_id (smallint) - Links to exams
- eq_spec_id (smallint) - Exam question specs
- title (string, 64) - Course name
- title_long (text) - Extended description
- price (decimal 5,2) - Course price
- total_minutes (integer) - Total duration
- policy_expire_days (smallint, default 180) - Expiration policy
- dates_template (json) - Scheduling template
- zoom_creds_id (smallint, default 2) - Zoom integration
- needs_range (boolean, default false) - Range requirement flag

// Current Relationships:
✅ hasMany(CourseAuth::class) - User enrollments
✅ hasMany(CourseUnit::class) - Course sections
✅ belongsTo(Exam::class) - Associated exam
✅ belongsTo(ExamQuestionSpec::class) - Question specifications
✅ belongsTo(ZoomCreds::class) - Zoom credentials
```

**✅ CourseAuth Model (`app/Models/CourseAuth.php`)**
```php
// Current fields:
- id (bigint, primary key)
- user_id (bigint) - Student enrollment
- course_id (smallint) - Course reference
- created_at, updated_at (timestamps)
- agreed_at (timestamp) - Terms agreement
- completed_at (timestamp) - Completion time
- is_passed (boolean, default false) - Pass/fail status
- start_date, expire_date (dates) - Enrollment period
- disabled_at (timestamp) - Deactivation
- disabled_reason (text) - Deactivation reason
- submitted_at (timestamp) - Submission time
- submitted_by (bigint) - Admin who processed
- dol_tracking (string 32) - DOL compliance tracking
- exam_admin_id (bigint) - Exam administrator
- range_date_id (bigint) - Range session assignment
- id_override (boolean, default false) - ID verification bypass

// Current Relationships:
✅ belongsTo(Course::class) - Course details
✅ belongsTo(User::class) - Student details
✅ hasMany(ExamAuth::class) - Exam attempts
✅ hasOne(Order::class) - Payment information
✅ belongsTo(RangeDate::class) - Range session
✅ hasMany(SelfStudyLesson::class) - Self-study progress
✅ hasMany(StudentUnit::class) - Unit progress
✅ belongsTo(User::class, 'submitted_by') - Processing admin
✅ hasOne(Validation::class) - Validation status
```

**✅ CourseDate Model (`app/Models/CourseDate.php`)**
```php
// Current fields:
- id (bigint, primary key)
- is_active (boolean, default true)
- course_unit_id (smallint) - Links to course unit
- starts_at, ends_at (timestamps) - Session timing

// Current Relationships:
✅ belongsTo(CourseUnit::class) - Course section
✅ hasOne(InstUnit::class) - Instructor assignment
✅ hasMany(StudentUnit::class) - Student attendance
```

**✅ User Model (`app/Models/User.php`)**
```php
// Current Relationships:
✅ hasMany(CourseAuth::class, 'user_id') - Course enrollments
```

### System Strengths

1. **✅ Complete enrollment system** - `CourseAuth` handles all enrollment logic
2. **✅ Flexible scheduling** - `CourseDate` supports detailed session scheduling
3. **✅ Progress tracking** - `StudentUnit` and `StudentLesson` track progress
4. **✅ Payment integration** - `Order` model linked to enrollments
5. **✅ Compliance tracking** - DOL tracking fields included
6. **✅ Instructor management** - `InstUnit` assigns instructors to sessions
7. **✅ Range integration** - `RangeDate` handles range requirements
8. **✅ Exam integration** - Full exam system integrated

### Gaps for D/G Course Management

**❌ MISSING FEATURES:**

1. **Course Type Classification**
   - No field to distinguish D Course vs G Course
   - No frequency tracking (weekly vs biweekly)
   - No duration specification in days

2. **Automatic Schedule Generation**
   - `dates_template` exists but no generation logic
   - No recurring schedule automation
   - No frequency-based scheduling rules

3. **Admin Interface**
   - No dedicated course management UI
   - No calendar view for scheduled courses
   - No bulk operations for course scheduling

### Recommended Implementation Strategy

**PHASE 1: Extend Existing Models** ⭐ PRIORITY
```php
// Add to courses table:
- course_type ENUM('D', 'G') 
- duration_days SMALLINT (5 for D, 3 for G)
- frequency_type ENUM('weekly', 'biweekly')
- max_participants INTEGER
```

**PHASE 2: Schedule Generation Logic**
- Implement automatic schedule generation using existing `dates_template`
- Create recurring schedule logic for D/G courses
- Utilize existing `CourseDate` model for sessions

**PHASE 3: Admin Interface**
- Build admin controllers using existing models
- Create calendar views using existing `CourseDate` data
- Implement enrollment management using existing `CourseAuth`

---

**Created**: August 12, 2025  
**Updated**: August 12, 2025  
**Status**: Analysis Complete - Implementation Ready  
**Assigned**: TBD  
**Priority**: High
