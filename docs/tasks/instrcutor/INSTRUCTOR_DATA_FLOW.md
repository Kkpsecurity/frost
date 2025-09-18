# Instructor Data Flow Architecture

**Date Created:** September 16, 2025  
**Purpose:** Define comprehensive data flow patterns for instructor dashboard system  
**Status:** Design Phase - Updated with Dual Sidebar Layout  
**Version:** 1.4

## Overview

This document defines how data flows through the instructor dashboard system, from database models through services, controllers, and to the frontend interface. It establishes patterns for data retrieval, transformation, caching, and real-time updates.

## Core Data Flow Architecture

### Template vs Instance Pattern

This system uses a **Template-Instance pattern*#### 4.2 Dashboard State Transitions:
```
[INITIAL STATE: No Course Accepted]
Dashboard shows:
├── Bulletin board with available CourseDate instances
├── "No course for the day" message
└── Course acceptance interface

    ↓ (Instructor accepts CourseDate)
    
[POST] /admin/instructors/action/accept-course
├── Create InstUnit from CourseDate
├── Copy CourseUnitLessons → InstUnitLessons
├── Update dashboard state

    ↓
    
[ACTIVE STATE: Course Accepted]
Dashboard transforms to dual sidebar:
├── Left: Lesson management (InstUnitLessons)
├── Right: Student management (StudentUnits)
├── Center: Widgets (screenshare, details, chat)
└── Real-time updates via polling
```

#### 4.3 Push Notification Flow: where:

1. **Templates** define how courses should work (used in eCommerce)
2. **Instances** are active copies when classes are scheduled and running

#### Course Structure Breakdown:
```
Course (e.g., "Course D")
├── Has 26 total lessons in master pool
├── Divided into CourseUnits (class days)
│   ├── CourseUnit "Day 1" → CourseUnitLessons (lessons 1,3,7,15)
│   ├── CourseUnit "Day 2" → CourseUnitLessons (lessons 2,9,11,20)  
│   ├── CourseUnit "Day 3" → CourseUnitLessons (lessons 5,8,12,19,23)
│   └── ... (continues for all class days)
│
└── When scheduled:
    ├── CourseDate created for "Day 3" at 9:00 AM Tuesday
    ├── Instructor arrives → InstUnit + InstUnitLessons (5 lessons active)
    └── Students join → StudentLessons track progress on today's 5 lessons
```

```
SCHEDULER PROCESS:
Active Courses (D, G) → CourseDate instances created → Ready for instructor

INSTRUCTOR ACTIVATION:
CourseDate + Instructor → InstUnit created → Template becomes live instance

STUDENT ENROLLMENT:
CourseAuth (enrollment) → StudentUnit (class instance) → StudentLessons (progress)
```

**Example Flow:**
- Course "D" (template) has 26 total lessons
- CourseUnit "Day 3" specifies lessons 5,8,12,19,23 for Tuesday
- CourseUnitLessons defines those 5 specific lessons for that day
- Scheduler creates CourseDate for "Course D - Day 3" at 9:00 AM Tuesday
- Instructor arrives → InstUnit created (copies Day 3 structure)
- InstUnitLessons created → contains the 5 lessons scheduled for today
- Students join → StudentUnits created for this CourseDate
- Lessons begin → StudentLessons track progress through today's 5 lessons

### 1. Data Source Layer (Database Models)

#### Primary Models & Relationships:

**Template vs Instance Architecture:**

**TEMPLATE LAYER (Metadata/eCommerce):**
- **Course**: Course catalog item for selling (e.g., "Course D", "Course G") with 26 total lessons
- **CourseUnit**: Template representing a specific class day (e.g., "Tuesday", "Day 3")
- **CourseUnitLessons**: Specifies which lessons from the course are taught on that day (e.g., lessons 5,8,12,19,23 on Tuesday)
- **Lesson**: Master lesson pool - all individual lessons available in the course

**INSTANCE LAYER (Active Classes):**
- **CourseDate**: Scheduled instance of a CourseUnit (specific class day scheduled by system)
- **InstUnit**: Active copy of CourseUnit when instructor takes control of that class day
- **InstUnitLessons**: Active copy of CourseUnitLessons - the specific lessons being taught today
- **StudentUnit**: Student enrolled in this specific CourseDate (class day) instance
- **StudentLessons**: Student's progress through the specific lessons scheduled for this day

**Instructor Data Flow:**
For instructors, a CourseDate means the course is scheduled and ready to start, but it's not active until an InstUnit is created. This tells the system that an instructor has taken position of that CourseDate and the template becomes a live instance.

**Key Relationships:**
- **CourseAuth**: Student enrollment (separate from instructor flow)
- **CourseDate**: Scheduled course instance
  - `InstUnit` - Instructor takes control (copies CourseUnit template)
  - `InstUnitLessons` - Active lessons (copies CourseUnitLessons)
  - `StudentUnits` - Students enrolled in this specific date instance

#### Model Relationship Flow:
```php
// TEMPLATE to INSTANCE mapping:
Course (26 lessons total) → CourseDate (scheduled class day)
CourseUnit (class day template) → InstUnit (active day instructor copy)
CourseUnitLessons (day's lesson selection) → InstUnitLessons (active lessons for today)
Lesson (master lesson pool) → StudentLessons (student progress on today's lessons)

// Core instructor data relationships  
CourseDate::with([
    'CourseUnit.Course',    // Template course details
    'InstUnit',             // Active instructor instance
    'StudentUnits',         // Student instances for this date
    'Classroom'             // Physical/virtual classroom
])

// When instructor takes control, templates become instances:
CourseDate::with([
    'InstUnit.InstUnitLessons',  // Active lesson instances
    'StudentUnits.StudentLessons' // Student progress instances
])

// Student enrollment (uses CourseAuth, not CourseDate directly)
User::with(['CourseAuths.Course'])  // Template enrollment
CourseAuth::with(['StudentUnit.StudentLessons']) // Instance progress
```

### 2. Service Layer (Data Processing)

#### Service Hierarchy:
```
InstructorDashboardService (Main coordinator)
├── CourseDatesService (Schedule & lessons)
    │   └── Manages CourseDate instances (scheduled class days)
    │   └── Uses ClassroomQueries::InstructorDashboardCourseDates()
    │   └── Handles CourseUnit (class day) → CourseDate mapping
    │   └── Retrieves which lessons are scheduled for each class day
    │
├── ClassroomService (Live class management)
    │   └── Manages InstUnit creation (class day → active session)
    │   └── Handles offline and online classroom state
    │   └── Coordinates CourseUnitLessons → InstUnitLessons copying
    │   └── Activates specific lessons for today's class day
    │
├── StudentService (Student data & activity)
    │   └── Manages StudentUnit instances (students in specific class day)
    │   └── Handles CourseAuth → StudentUnit → StudentLessons progression
    │   └── Tracks student progress through today's specific lessons
    │   └── Maps lesson completion within the context of class day structure
    │
├── NotificationService (Alerts & messaging)
    │   └── Notification system that manages and alerts students and instructors
    │   └── Based on classroom activity and state changes
    │
└── StatisticsService (Analytics & reporting)
    └── Aggregates data from multiple models for dashboard metrics
```

#### Current Service Implementation Status:
```
✓ InstructorDashboardService    - Session validation, course state detection
✓ CourseDatesService           - Bulletin board data (pre-acceptance)
✓ ClassroomService             - Active classroom dual sidebar data
    ├── getActiveClassroomData() - Combined left/right/center data
    ├── getLeftSidebarData()     - InstUnit + InstUnitLessons
    ├── getRightSidebarData()    - StudentUnits + online status
    └── getCenterWidgetsData()   - Screenshare, chat, selected student
✓ BackendStudentService        - Student enrollment and progress
✗ ScreenshareService           - Not yet implemented
✗ NotificationService          - Not yet implemented
✗ StatisticsService            - Logic exists in InstructorDashboardService
```

#### Data Transformation Flow:
```
Raw Database Data → Service Processing → Standardized Arrays → Controller → View/API
```

### 3. Controller Layer (Request Handling)

#### Request Flow Pattern:
```
Route → Middleware → Controller → Service(s) → Response
```

#### Controller Responsibilities:
- Request validation
- Service orchestration
- Response formatting
- Error handling

### 4. Frontend Layer (Data Consumption)

#### Current Route Structure:
```
# Dashboard States
/admin/instructors/                          # Main dashboard view
/admin/instructors/validate                  # Session validation + course state

# Pre-Acceptance (Bulletin Board State)
/admin/instructors/data/bulletin-board       # Available CourseDate instances
/admin/instructors/data/stats                # General statistics

# Post-Acceptance (Active Classroom State) 
/admin/instructors/data/classroom/active     # Full dual sidebar data
/admin/instructors/data/classroom/lessons    # Left sidebar: InstUnitLessons
/admin/instructors/data/classroom/students   # Right sidebar: StudentUnits
/admin/instructors/data/classroom/widgets    # Center: Screenshare, Chat, Selected

# Real-time Updates
/admin/instructors/data/students/online      # Student online status
/admin/instructors/data/chat/messages        # Chat messages
/admin/instructors/data/chat/send            # Send message

# Course Actions
/admin/instructors/action/accept-course      # Accept CourseDate → Create InstUnit
/admin/instructors/action/lesson/start       # Start specific lesson
/admin/instructors/action/lesson/complete    # Complete lesson
```

#### Frontend Data Flow:
```
React Components → API Calls → Controller → Services → Database
              ↓
Dashboard Widgets ← Formatted Data ← JSON Response ← Processing
```

## Instructor Dashboard Flow & Layout

### Instructor Course Acceptance Flow

#### Pre-Acceptance State:
- Instructor sees bulletin board with available CourseDate instances
- Shows scheduled class days awaiting instructor assignment
- Display: "No course accepted for today" or course selection interface

#### Post-Acceptance State:
- Instructor accepts a CourseDate → InstUnit created
- Dashboard transforms to **Dual Sidebar Layout**
- Class becomes active and ready for students

### Dashboard Layout Architecture

#### Layout Structure:
```
┌─────────────┬──────────────────────────────────┬─────────────┐
│             │                                  │             │
│  LEFT       │           CENTER                 │   RIGHT     │
│ SIDEBAR     │          WIDGETS                 │  SIDEBAR    │
│             │                                  │             │
│ Current     │  ┌─────────────────────────────┐ │  Student    │
│ Lesson      │  │      Screenshare            │ │ Management  │
│ Management  │  │      Interface              │ │             │
│             │  └─────────────────────────────┘ │ • Student   │
│ • Lesson    │                                  │   List      │
│   List      │  ┌─────────────────────────────┐ │ • Online    │
│ • Progress  │  │    Student Details          │ │   Status    │
│ • Controls  │  │   (Selected Student)        │ │ • Actions   │
│             │  └─────────────────────────────┘ │             │
│             │                                  │             │
│             │  ┌─────────────────────────────┐ │             │
│             │  │      Chat System            │ │             │
│             │  │   (Bulletin Board Style)    │ │             │
│             │  └─────────────────────────────┘ │             │
└─────────────┴──────────────────────────────────┴─────────────┘
```

### Dashboard Data Requirements

#### Left Sidebar - Current Lesson Management:
```json
{
  "current_lesson": {
    "inst_unit_id": 123,
    "class_day": "Day 3",
    "total_lessons": 5,
    "current_lesson_index": 2,
    "lessons": [
      {
        "id": 5,
        "title": "Lesson 5: Safety Procedures",
        "status": "completed",
        "duration": "15 min"
      },
      {
        "id": 8,
        "title": "Lesson 8: Equipment Check",
        "status": "active",
        "duration": "20 min"
      },
      {
        "id": 12,
        "title": "Lesson 12: Practical Exercise",
        "status": "pending",
        "duration": "30 min"
      }
    ]
  }
}
```

#### Right Sidebar - Student Management:
```json
{
  "students": {
    "online_count": 12,
    "total_enrolled": 15,
    "students": [
      {
        "student_unit_id": 456,
        "user_id": 789,
        "name": "John Doe",
        "status": "online",
        "lesson_progress": {
          "completed": 2,
          "current": 8,
          "remaining": 3
        },
        "last_activity": "2025-09-16T10:30:00Z"
      }
    ]
  }
}
```

#### Center Widgets:
```json
{
  "screenshare": {
    "active": false,
    "session_id": null,
    "participants": 0
  },
  "selected_student": {
    "student_unit_id": 456,
    "personal_info": { /* student details */ },
    "progress_data": { /* lesson completion */ },
    "validation_status": { /* ID verification */ }
  },
  "chat": {
    "type": "bulletin_board",
    "recent_messages": [ /* chat messages */ ],
    "active_participants": 12
  }
}
```

## Detailed Data Flow Patterns

### Pattern 0: Template to Instance Creation Flow

#### 0.1 Course Scheduling (Scheduler Process):
```
[SCHEDULER DAILY JOB]
    ↓
Active Courses Query (Course D, Course G)
    ↓
For each active course:
    - Find CourseUnit for today (e.g., "Day 3" for Tuesday)
    - Create CourseDate instance linking to that CourseUnit
    - CourseUnitLessons defines which lessons (e.g., 5,8,12,19,23)
    - Set scheduling metadata
    ↓
CourseDate instances ready for instructor assignment
(Each represents a specific class day with specific lessons)
```

#### 0.2 Instructor Takes Control:
```
[INSTRUCTOR ARRIVES]
    ↓
CourseDate exists (scheduled class day)
    ↓
Instructor assigns to CourseDate
    ↓
InstUnit created:
    - Copies CourseUnit (class day) structure
    - Links to CourseDate
    - Creates InstUnitLessons from CourseUnitLessons
      (copies the specific lessons for today, e.g., 5,8,12,19,23)
    ↓
Class becomes LIVE (today's lessons are now active)
```

#### 0.3 Student Joins Class:
```
[STUDENT HAS COURSEAUTH]
    ↓
Student joins active CourseDate (today's class day)
    ↓
StudentUnit created:
    - Links CourseAuth to CourseDate instance
    - Links to InstUnit (active instructor session)
    ↓
StudentLessons created from InstUnitLessons
    - Creates progress tracking for today's specific lessons
    - (e.g., lessons 5,8,12,19,23 for this class day)
    ↓
Student can participate in today's lessons
```

### Pattern 1: Instructor Dashboard State Flow

#### 1.1 Dashboard State Detection:
```
[GET] /admin/instructors/validate
    ↓
InstructorDashboardService::validateSession()
    ↓
Check for active InstUnit for today
    ↓
IF InstUnit exists:
    - Return: course_accepted = true
    - Load dual sidebar layout
    - Fetch lesson and student data
ELSE:
    - Return: course_accepted = false  
    - Show bulletin board with available CourseDate instances
    - Display: "No course for the day"
```

#### 1.2 Dual Sidebar Data Loading:
```
[GET] /admin/instructors/data/classroom/active
    ↓
InstructorDashboardController::getActiveClassroom()
    ↓
Parallel Data Fetching:
├── Left Sidebar: InstUnit → InstUnitLessons (today's lessons)
├── Right Sidebar: CourseDate → StudentUnits (enrolled students)  
└── Center Widgets: Screenshare, Chat, Selected Student
    ↓
Combined dashboard state response
```

### Pattern 2: Today's Lessons Data Flow (Legacy - for bulletin board)

#### 2.1 Data Retrieval Flow:
```
[GET] /admin/instructors/data/today/lessons
    ↓
InstructorDashboardController::getTodayLessons()
    ↓
CourseDatesService::getTodaysLessons()
    ↓
ClassroomQueries::InstructorDashboardCourseDates()
    ↓
CourseDate::where('starts_at', '>=', DateHelpers::DayStartSQL())
           ->where('ends_at', '<=', DateHelpers::DayEndSQL())
           ->where('is_active', true)
           ->with('InstUnit')
           ->get()
    ↓
Filtered Collection (by time window and site config)
```

#### 1.2 Data Transformation Flow:
```php
// Service processes CourseDate instances using template data via RCache
foreach ($courseDates as $courseDate) {
    // Get template data (Course D, Course G, etc.)
    $course = $courseDate->GetCourse();         // Template via RCache::Courses()
    $courseUnit = $courseDate->GetCourseUnit(); // Template via RCache::CourseUnits()
    
    // Check if template has been instantiated
    $instUnit = $courseDate->InstUnit;          // Active instance (null until instructor takes control)
    
    $transformed[] = [
        'id' => $courseDate->id,
        'course_name' => $course->title,        // From template (e.g., "Course D")
        'class_day' => $courseUnit->title,      // Class day (e.g., "Day 3", "Tuesday")
        'lesson_count' => $courseUnit->CourseUnitLessons()->count(), // # of lessons today
        'calendar_title' => $courseDate->CalendarTitle(), // Template-based method
        'instructor' => $instUnit?->GetCreatedBy()?->name ?? 'Unassigned',
        'start_time' => $courseDate->starts_at->format('h:i A'),
        'end_time' => $courseDate->ends_at->format('h:i A'),
        'student_count' => $courseDate->StudentUnits()->count(), // Active instances
        'is_scheduled' => $courseDate->is_active,  // Class day is scheduled
        'is_live' => !is_null($instUnit),         // Instructor has taken control
        'course_template_id' => $course->id,      // Reference to course template
        'class_day_template_id' => $courseUnit->id, // Reference to day template
        'active_instance_id' => $instUnit?->id,   // Reference to active instance
        'status' => $this->determineClassStatus($courseDate, $instUnit)
    ];
}
```

#### 1.3 Response Format:
```json
{
    "lessons": [...],
    "has_lessons": true,
    "message": "3 lessons scheduled for today",
    "metadata": {
        "date": "2025-09-16",
        "count": 3,
        "generated_at": "2025-09-16T08:00:00Z"
    }
}
```

### Pattern 2: Class Statistics Data Flow

#### 2.1 Data Aggregation Flow:
```
[GET] /admin/instructors/data/stats
    ↓
InstructorDashboardController::getStats()
    ↓
InstructorDashboardService::getInstructorStats()
    ↓
Multiple Database Queries:
    - DB::table('courses')->where('is_active', true)->count()
    - DB::table('users')->where('role_id', '>=', 5)->count()
    - DB::table('course_auths')->where('is_active', true)->count()
    - DB::table('course_auths')->where('completed_at', '!=', null)->count()
```

#### 2.2 Aggregation Queries:
```php
// Service combines multiple data sources using direct DB queries
$totalCourses = DB::table('courses')->where('is_active', true)->count();
$totalStudents = DB::table('users')->where('role_id', '>=', 5)->count();
$activeCourseAuths = DB::table('course_auths')->where('is_active', true)->count();
$completedCourseAuths = DB::table('course_auths')
    ->where('is_active', false)
    ->where('completed_at', '!=', null)
    ->count();

// Calculate completion rate
$totalCourseAuths = $activeCourseAuths + $completedCourseAuths;
$completionRate = $totalCourseAuths > 0 ? 
    round(($completedCourseAuths / $totalCourseAuths) * 100) : 0;

$stats = [
    'total_students' => $totalStudents,
    'active_courses' => $totalCourses,
    'completion_rate' => $completionRate,
    'pending_grades' => 12, // Placeholder for assignments table
    'active_course_auths' => $activeCourseAuths,
    'completed_course_auths' => $completedCourseAuths
];
```

### Pattern 3: Dual Sidebar Data Flow

#### 3.1 Active Classroom Data Flow:
```
[GET] /admin/instructors/data/classroom/active
    ↓
InstructorDashboardController::getActiveClassroom()
    ↓
ClassroomService::getActiveClassroomData()
    ↓
Parallel Service Calls:
├── getLeftSidebarData():
│   ├── InstUnit::with('InstUnitLessons')
│   ├── Current lesson tracking
│   └── Lesson progress status
│
├── getRightSidebarData():
│   ├── StudentUnits::with('CourseAuth.User')
│   ├── Online status detection
│   └── Student progress tracking
│
└── getCenterWidgetsData():
    ├── Screenshare session status
    ├── Selected student details
    └── Chat/bulletin board messages
    ↓
Combined JSON response for dual sidebar layout
```

#### 3.2 Left Sidebar - Lesson Management Data:
```php
// ClassroomService::getLeftSidebarData()
$instUnit = InstUnit::where('course_date_id', $courseDateId)
                   ->with(['InstUnitLessons.Lesson'])
                   ->first();

$leftSidebar = [
    'inst_unit_id' => $instUnit->id,
    'class_day' => $instUnit->GetCourseUnit()->title,
    'total_lessons' => $instUnit->InstUnitLessons()->count(),
    'current_lesson_index' => $this->getCurrentLessonIndex($instUnit),
    'lessons' => $instUnit->InstUnitLessons->map(function($instLesson) {
        return [
            'id' => $instLesson->lesson_id,
            'title' => $instLesson->Lesson->title,
            'status' => $this->getLessonStatus($instLesson),
            'duration' => $instLesson->Lesson->duration,
            'order' => $instLesson->order
        ];
    })
];
```

#### 3.3 Right Sidebar - Student Management Data:
```php
// ClassroomService::getRightSidebarData()
$studentUnits = StudentUnit::where('course_date_id', $courseDateId)
                          ->with(['CourseAuth.User', 'StudentLessons'])
                          ->get();

$rightSidebar = [
    'online_count' => $this->getOnlineStudentCount($studentUnits),
    'total_enrolled' => $studentUnits->count(),
    'students' => $studentUnits->map(function($studentUnit) {
        return [
            'student_unit_id' => $studentUnit->id,
            'user_id' => $studentUnit->CourseAuth->user_id,
            'name' => $studentUnit->CourseAuth->User->name,
            'status' => $this->getStudentOnlineStatus($studentUnit),
            'lesson_progress' => $this->getStudentProgress($studentUnit),
            'last_activity' => $studentUnit->updated_at
        ];
    })
];
```

### Pattern 4: Real-time Data Flow (WebSocket/Polling)

#### 4.1 Real-time Update Flow:
```
Frontend Polling (30s intervals)
    ↓
[GET] /admin/instructors/data/students/online
    ↓
InstructorDashboardController::getOnlineStudents()
    ↓
BackendStudentService::getOnlineStudentsForInstructor()
    ↓
StudentUnit::with(['CourseAuth.User', 'CourseDate'])
    ↓
Real-time Student Status (filtered by active sessions)
```

#### 3.2 Push Notification Flow:
```
Database Event (Student joins class)
    ↓
Model Observer (StudentUnit::created)
    ↓
NotificationService::notify()
    ↓
WebSocket Broadcast / Queue Job
    ↓
Frontend Real-time Update
```

## Data Caching Strategy

### 1. RCache Integration

#### Cache Key Patterns:
```php
// RCache patterns already in use (TEMPLATES):
RCache::Courses($course_id)              // Template course data (Course D, G)
RCache::CourseUnits($course_unit_id)     // Template unit structure
RCache::User($user_id)                   // User data
RCache::Admin($admin_id)                 // Admin user data
RCache::SiteConfig($config_key)          // Site configuration

// Proposed instructor-specific cache keys (INSTANCES):
"instructor:course_dates:today:{date}"           // Today's CourseDate instances (15 min)
"instructor:inst_units:active"                   // Active InstUnit instances (5 min)
"instructor:dashboard:stats:overview"            // Instance statistics (5 min)
"instructor:bulletin:scheduled"                  // Scheduled but not active (1 hour)

// Dual Sidebar Cache Keys:
"instructor:left_sidebar:{inst_unit_id}"         // Left sidebar lesson data (10 min)
"instructor:right_sidebar:{course_date_id}"      // Right sidebar student data (5 min)
"instructor:center_widgets:{course_date_id}"     // Center widget data (2 min)
"instructor:student_online:{course_date_id}"     // Online student status (1 min)
"instructor:lesson_progress:{inst_unit_id}"      // Lesson progress tracking (5 min)

// Template-Instance relationship cache:
"course_date:class_day_map:{course_date_id}"     // CourseDate → CourseUnit (class day) mapping
"course_unit:lessons:{course_unit_id}"           // CourseUnit → CourseUnitLessons (day's lessons)
"inst_unit:active_lessons:{inst_unit_id}"        // InstUnit → InstUnitLessons (active lessons)
"class_day:lesson_count:{course_unit_id}"        // Quick lesson count per class day
```

#### Cache Invalidation Triggers:
```php
// TEMPLATE changes (affects future instances):
Course::saved() → Clear RCache::Courses($course_id)
CourseUnit::saved() → Clear RCache::CourseUnits($course_unit_id)
CourseUnitLessons::saved() → Clear related template caches

// INSTANCE changes (affects current active classes):
CourseDate::saved() → Clear "instructor:course_dates:*"
InstUnit::saved() → Clear "instructor:inst_units:*" & "instructor:course_dates:*"
InstUnitLessons::saved() → Clear "inst_unit:lesson_map:*"

// STUDENT INSTANCE changes:
CourseAuth::saved() → Clear "instructor:dashboard:stats:*" (enrollment affects stats)
StudentUnit::saved() → Clear "instructor:student_units:*"
StudentLessons::saved() → Clear student progress caches

// TEMPLATE-INSTANCE mapping changes:
CourseDate::saved() → Clear "course_date:template_map:{course_date_id}"
InstUnit::created() → Clear "course_date:template_map:*" (instance becomes active)
```

### 2. Cache Warming Strategy

#### Proactive Cache Population:
```php
// Daily cache warming (scheduled task)
Schedule::command('cache:warm-instructor-data')->daily();

// Pre-load common queries
- Today's lessons for all instructors
- Course statistics
- Student enrollment counts
```

## Error Handling & Data Validation

### 1. Service Layer Error Handling

#### Error Response Pattern:
```php
try {
    $data = $this->processComplexQuery();
    return ['success' => true, 'data' => $data];
} catch (\Exception $e) {
    Log::error('Instructor data error: ' . $e->getMessage());
    return [
        'success' => false,
        'error' => 'Unable to load instructor data',
        'fallback_data' => $this->getEmptyState()
    ];
}
```

#### Graceful Degradation:
```php
// If primary query fails, return empty state with message
return [
    'lessons' => [],
    'has_lessons' => false,
    'message' => 'Unable to load lessons at this time',
    'error_state' => true
];
```

### 2. Data Validation Patterns

#### Input Validation:
```php
// Controller validates request parameters
$validated = $request->validate([
    'date' => 'date|before_or_equal:today',
    'instructor_id' => 'exists:users,id',
    'limit' => 'integer|min:1|max:100'
]);
```

#### Data Integrity Checks:
```php
// Service validates data consistency
if ($courseDate->starts_at > $courseDate->ends_at) {
    throw new DataIntegrityException('Invalid course schedule');
}
```

## Performance Optimization Patterns

### 1. Query Optimization

#### Eager Loading Strategy:
```php
// Current implementation (based on ClassroomQueries)
CourseDate::where('starts_at', '>=', DateHelpers::DayStartSQL())
          ->where('ends_at', '<=', DateHelpers::DayEndSQL())
          ->where('is_active', true)
          ->with('InstUnit')  // Load instructor assignment
          ->get();

// Optimized version with selective field loading
CourseDate::select(['id', 'course_unit_id', 'starts_at', 'ends_at', 'is_active'])
          ->with([
              'CourseUnit:id,course_id,title',
              'InstUnit:id,course_date_id,created_by,assistant_id',
              'StudentUnits:id,course_date_id,course_auth_id'
          ])
          ->where('starts_at', '>=', DateHelpers::DayStartSQL())
          ->where('ends_at', '<=', DateHelpers::DayEndSQL())
          ->where('is_active', true)
          ->get();
```

#### Query Chunking for Large Datasets:
```php
// Process large datasets in chunks (note: CourseAuth doesn't have active() scope)
DB::table('course_auths')
    ->where('is_active', true)
    ->chunk(100, function ($courseAuths) {
        $this->processEnrollmentBatch($courseAuths);
    });

// For student units processing
StudentUnit::whereHas('CourseDate', function ($query) {
        $query->where('is_active', true);
    })
    ->chunk(100, function ($studentUnits) {
        $this->processStudentBatch($studentUnits);
    });
```

### 2. Response Optimization

#### Data Pagination:
```php
// Paginate large result sets
return [
    'data' => $lessons->take(50),
    'pagination' => [
        'total' => $lessons->count(),
        'page' => 1,
        'per_page' => 50
    ]
];
```

#### Selective Field Loading:
```php
// Only load required fields for API responses
CourseDate::select(['id', 'starts_at', 'ends_at', 'course_unit_id'])
    ->with('CourseUnit:id,title,course_id')
    ->get();
```

## Security & Access Control

### 1. Data Access Patterns

#### Instructor Scope Filtering:
```php
// Only show data relevant to current instructor
$query->whereHas('InstUnit', function ($q) use ($instructorId) {
    $q->where('instructor_id', $instructorId);
});
```

#### Role-based Data Filtering:
```php
// Admin sees all, Instructor sees assigned only
if ($user->IsInstructor()) {
    $query->instructorScope($user->id);
} elseif ($user->IsAdmin()) {
    // No additional filtering for admin
}
```

### 2. Data Sanitization

#### Output Sanitization:
```php
// Sanitize user-generated content
'course_name' => TextTk::Sanitize($course->title),
'lesson_description' => strip_tags($lesson->description),
```

#### SQL Injection Prevention:
```php
// Use query builder with parameter binding
DB::table('course_dates')
    ->where('instructor_id', $instructorId) // Bound parameter
    ->whereDate('starts_at', $date);        // Bound parameter
```

## API Response Standards

### 1. Success Response Format
```json
{
    "success": true,
    "data": {
        // Actual data payload
    },
    "metadata": {
        "generated_at": "2025-09-16T08:00:00Z",
        "cache_hit": false,
        "query_time_ms": 45
    }
}
```

### 2. Error Response Format
```json
{
    "success": false,
    "error": {
        "message": "Unable to load lesson data",
        "code": "DATA_UNAVAILABLE",
        "type": "temporary"
    },
    "fallback_data": {
        // Empty state or cached data
    }
}
```

### 3. Empty State Response Format
```json
{
    "success": true,
    "data": {
        "lessons": [],
        "has_lessons": false,
        "message": "No courses scheduled for today"
    },
    "metadata": {
        "empty_state": true,
        "reason": "no_scheduled_courses"
    }
}
```

## Data Flow Monitoring & Logging

### 1. Performance Monitoring
```php
// Log slow queries
if ($queryTime > 1000) { // 1 second
    Log::warning('Slow instructor query', [
        'query' => $query->toSql(),
        'time_ms' => $queryTime,
        'instructor_id' => $instructorId
    ]);
}
```

### 2. Data Access Logging
```php
// Track data access patterns
Log::info('Instructor data access', [
    'endpoint' => '/data/lessons/today',
    'instructor_id' => $instructorId,
    'response_size' => strlen($response),
    'cache_hit' => $cacheHit
]);
```

## Implementation Checklist

### Phase 1: Core Data Flow
- [ ] Implement base service classes
- [ ] Set up model relationships
- [ ] Create controller methods
- [ ] Add basic error handling

### Phase 2: Caching Layer
- [ ] Implement RCache integration
- [ ] Set up cache invalidation
- [ ] Add cache warming jobs
- [ ] Monitor cache performance

### Phase 3: Real-time Features
- [ ] Add WebSocket support
- [ ] Implement polling endpoints
- [ ] Set up push notifications
- [ ] Test real-time updates

### Phase 4: Optimization
- [ ] Add query optimization
- [ ] Implement response caching
- [ ] Add performance monitoring
- [ ] Load testing and tuning

---

**Last Updated:** September 16, 2025 - Added Instructor Dashboard Flow & Dual Sidebar Layout  
**Next Review:** After implementation of dual sidebar components  
**Dependencies:** INSTRUCTOR_DASHBOARD_REAL_DATA.md  
**Architecture:** Pre-acceptance (bulletin board) → Post-acceptance (dual sidebar layout)  
**Layout:** Left (lessons) + Right (students) + Center (screenshare, details, chat)  
**Key States:** No course → Accept CourseDate → InstUnit created → Dashboard transforms  
**Validated Against:** CourseDate.php, InstUnit.php, CourseAuth.php, StudentUnit.php, ClassroomQueries, InstructorDashboardController.php
