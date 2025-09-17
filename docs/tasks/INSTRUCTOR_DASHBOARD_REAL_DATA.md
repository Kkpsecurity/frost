# Instructor Dashboard Real Data Implementation

**Date Created:** September 16, 2025  
**Objective:** Replace dummy data in instructor dashboard with real database queries  
**Priority:** High  
**Status:** Planning Phase

## Current State Analysis

### Current Route Setup
- **Route:** `admin/instructors` → `admin.instructors.dashboard`
- **Current Implementation:** Direct view loading without controller
- **File:** `routes/admin/instructors.php` line 12-14
```php
Route::get('/', function () {
    return view('dashboards.instructor.offline');
})->name('dashboard');
```

### Current Blade Template Structure
- **File:** `resources/views/dashboards/instructor/offline.blade.php`
- **Sections with Dummy Data:**
  1. **Today's Lessons Table** (lines ~30-120)
     - Hardcoded 3 sample lessons
     - Static data: "Security Fundamentals", "Advanced Cyber Defense", "Penetration Testing"
     - Fixed student counts: 24, 18, 12
  2. **Class Overview Stats** (lines ~150-200)
     - Hardcoded numbers: 45 students, 6 courses, 87% completion, 12 pending grades
  3. **Recent Activity Timeline** (lines ~250-320)
     - 4 hardcoded activity items with static timestamps

### Database Schema Documentation

#### Primary Tables for Real Data:
1. **course_dates** - Scheduled course sessions
   - `id`, `course_unit_id`, `starts_at`, `ends_at`, `is_active`
   - Relationships: belongsTo CourseUnit

2. **course_units** - Course structure/modules  
   - `id`, `course_id`, `title`, `sequence`
   - Relationships: belongsTo Course, hasMany CourseDates

3. **courses** - Course definitions
   - `id`, `title`, `title_long`, `is_active`
   - Relationships: hasMany CourseUnits

4. **course_auths** - Student enrollments
   - `id`, `user_id`, `course_id`, `is_active`, `completed_at`
   - Used for counting enrolled students

#### Key Relationships:
```
CourseDate -> CourseUnit -> Course
CourseAuth -> Course (for student counts)
```

### Existing Utilities Available

#### ClassroomQueries Class
- **File:** `app/Classes/ClassroomQueries.php`
- **Available Trait:** `InstructorDashboardCourseDates`
- **Method:** `InstructorDashboardCourseDates()` - Gets today's active course dates
- **Features:** 
  - Uses SiteConfig for instructor timing
  - Filters by instructor pre/post start minutes
  - Returns Collection of CourseDate models

#### Existing Services
- **InstructorDashboardService** - Session validation, basic stats
- **CourseDatesService** - Course date queries and bulletin board data  
- **ClassroomService** - Classroom data management

## Data Structure Design

### Today's Lessons Table Data Structure

#### Empty State:
```php
[
    'lessons' => [],
    'message' => 'No courses scheduled for today (2025-09-16)',
    'has_lessons' => false,
    'date' => '2025-09-16',
    'count' => 0
]
```

#### Populated State:
```php
[
    'lessons' => [
        [
            'id' => 1,
            'time' => '09:00 AM',
            'duration' => '2 hours',
            'course_name' => 'Security Fundamentals',
            'course_code' => 'SEC-101', 
            'lesson_name' => 'Network Security Basics',
            'module' => 'Module 3',
            'student_count' => 24,
            'status' => 'scheduled|in-progress|completed',
            'starts_at' => '2025-09-16 09:00:00',
            'ends_at' => '2025-09-16 11:00:00'
        ]
    ],
    'has_lessons' => true,
    'date' => '2025-09-16',
    'count' => 3
]
```

### Class Overview Stats Data Structure
```php
[
    'total_students' => 45,        // COUNT from course_auths where is_active=true
    'active_courses' => 6,         // COUNT from courses where is_active=true  
    'completion_rate' => 87,       // % from course_auths completed vs total
    'pending_grades' => 12,        // COUNT from assignments (future feature)
    'active_course_auths' => 38,   // Active enrollments
    'completed_course_auths' => 32 // Completed enrollments
]
```

### Recent Activity Data Structure
```php
[
    'activities' => [
        [
            'id' => 1,
            'type' => 'lesson_completed|assignment_submitted|course_rated|course_created',
            'title' => 'Lesson Completed',
            'description' => 'Network Security Basics lesson completed by 24 students',
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'timestamp' => '2025-09-16 14:00:00',
            'time_ago' => '2 hours ago'
        ]
    ],
    'count' => 4
]
```

## Implementation Plan

### Phase 1: Route Modification
1. **File:** `routes/admin/instructors.php`
2. **Change:** Replace direct view() with controller method
3. **New Route:**
```php
Route::get('/', [InstructorDashboardController::class, 'offlineDashboard'])
    ->name('dashboard');
```

### Phase 2: Controller Method Creation
1. **File:** `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php`
2. **New Method:** `offlineDashboard()`
3. **Responsibilities:**
   - Gather today's lessons data
   - Gather class overview stats  
   - Gather recent activity
   - Pass data to view

### Phase 3: Service Method Enhancements
1. **CourseDatesService** - Add `getTodaysLessons()` method
2. **InstructorDashboardService** - Add `getClassOverviewStats()` method
3. **InstructorDashboardService** - Add `getRecentActivity()` method

### Phase 4: Blade Template Updates
1. **File:** `resources/views/dashboards/instructor/offline.blade.php`
2. **Changes:**
   - Replace hardcoded lesson data with `@foreach($todaysLessons['lessons'] as $lesson)`
   - Add empty state handling with `@if($todaysLessons['has_lessons'])`
   - Replace hardcoded stats with `{{ $stats['total_students'] }}`
   - Replace hardcoded activity with real activity loop

### Phase 5: Testing Scenarios
1. **Empty State Testing:**
   - No course_dates in database
   - Verify "No courses for today" message displays
   - Verify stats show 0 counts appropriately

2. **Populated State Testing:**
   - Add test course_dates for today
   - Verify lessons display correctly
   - Verify student counts are accurate
   - Verify time formatting is correct

## Files to Modify

### Routes
- `routes/admin/instructors.php` (lines 12-14)

### Controllers  
- `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php` (add new method)

### Services
- `app/Services/Frost/Instructors/CourseDatesService.php` (add getTodaysLessons)
- `app/Services/Frost/Instructors/InstructorDashboardService.php` (add stats methods)

### Views
- `resources/views/dashboards/instructor/offline.blade.php` (replace dummy data)

## Database Queries Needed

### Today's Lessons Query:
```sql
SELECT cd.*, cu.title as unit_title, c.title as course_title
FROM course_dates cd
JOIN course_units cu ON cd.course_unit_id = cu.id  
JOIN courses c ON cu.course_id = c.id
WHERE DATE(cd.starts_at) = CURRENT_DATE
AND cd.is_active = true
ORDER BY cd.starts_at ASC
```

### Student Count Query:
```sql
SELECT COUNT(*) 
FROM course_auths ca
WHERE ca.course_id = ? 
AND ca.is_active = true
```

### Stats Queries:
```sql
-- Total Students
SELECT COUNT(*) FROM course_auths WHERE is_active = true

-- Active Courses  
SELECT COUNT(*) FROM courses WHERE is_active = true

-- Completion Rate
SELECT 
  COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) * 100.0 / COUNT(*) as completion_rate
FROM course_auths
```

## Success Criteria

1. ✅ Dashboard loads without errors when no course_dates exist
2. ✅ "No courses for today" message displays appropriately
3. ✅ Real student counts display when courses exist
4. ✅ Lesson times format correctly (9:00 AM format)
5. ✅ Course names and modules display from database
6. ✅ Stats reflect real database counts
7. ✅ Recent activity shows real data (or appropriate empty state)

## Risk Assessment

### Low Risk:
- Route modification (simple change)
- Adding controller method (standard Laravel pattern)

### Medium Risk:
- Database queries (need to handle edge cases)
- Blade template modifications (could break layout)

### High Risk:
- Time zone handling in lesson times
- Performance with large datasets
- Caching requirements for stats

## Notification System Integration

### Notification Types & Recipients

#### 1. Course Schedule Notifications
**When:** Course dates are created, modified, or cancelled
**Recipients:**
- **Students:** Enrolled in the specific course
- **Instructors:** Assigned to the course  
- **Support:** All schedule changes
**Channels:** Email, Dashboard alerts, SMS (optional)

#### 2. Class Status Notifications
**When:** Class starts, ends, or status changes
**Recipients:**
- **Students:** "Class starting in 15 minutes", "Class has ended"
- **Instructors:** "Students waiting in classroom", "Class completed"
- **Support:** Status updates for monitoring
**Channels:** Dashboard real-time, Email

#### 3. Student Activity Notifications  
**When:** Students join/leave, complete lessons, submit assignments
**Recipients:**
- **Instructors:** Real-time student activity updates
- **Support:** Attendance tracking, completion rates
**Channels:** Dashboard real-time, Daily digest email

#### 4. System Status Notifications
**When:** Technical issues, maintenance, capacity alerts
**Recipients:**
- **Instructors:** "Zoom connection issues", "Classroom capacity reached"
- **Support:** All technical alerts
- **Students:** Service disruptions only
**Channels:** Dashboard alerts, Email, SMS for critical

### Notification Data Structure

#### Notification Model Structure:
```php
[
    'id' => 1,
    'type' => 'course_starting|course_completed|student_joined|system_alert',
    'recipient_type' => 'instructor|student|support|all',
    'recipient_id' => 123, // user_id or null for broadcast
    'title' => 'Security Fundamentals Starting Soon',
    'message' => 'Your class begins in 15 minutes. Students: 18/24 checked in.',
    'data' => [
        'course_date_id' => 45,
        'course_name' => 'Security Fundamentals',
        'student_count' => 18,
        'start_time' => '2025-09-16 09:00:00'
    ],
    'channels' => ['dashboard', 'email'],
    'priority' => 'high|medium|low',
    'read_at' => null,
    'created_at' => '2025-09-16 08:45:00'
]
```

#### Dashboard Notification Widget:
```php
[
    'unread_count' => 3,
    'recent_notifications' => [
        [
            'icon' => 'fas fa-users',
            'color' => 'info',
            'title' => '18 students checked in',
            'time' => '5 minutes ago',
            'course' => 'Security Fundamentals'
        ]
    ]
]
```

### Notification Triggers in Dashboard Context

#### Today's Lessons Table Integration:
- **15 minutes before class:** Notify instructor of upcoming class
- **Class start time:** Notify instructor if students are waiting
- **During class:** Real-time student join/leave notifications
- **Class end:** Summary notification with completion stats

#### Class Overview Stats Integration:
- **Daily:** Completion rate changes
- **Weekly:** Student enrollment summaries  
- **Monthly:** Performance trend alerts

#### Recent Activity Integration:
- Show notification-worthy activities in real-time
- Filter by recipient type (instructor-relevant activities)

### Implementation Plan for Notifications

#### Phase 1: Notification Infrastructure
1. **Database Tables:**
   - `notifications` table
   - `notification_settings` table (per-user preferences)
   
2. **Models:**
   - `Notification` model with relationships
   - `NotificationSetting` model
   
3. **Services:**
   - `NotificationService` for sending
   - `NotificationPreferenceService` for user settings

#### Phase 2: Dashboard Integration
1. **Notification Widget:**
   - Add notification dropdown to instructor dashboard header
   - Real-time notification count updates
   
2. **Embedded Notifications:**
   - Show relevant notifications in Today's Lessons section
   - Course-specific alerts in Class Overview
   
3. **Notification Center:**
   - Full notification history page
   - Mark as read/unread functionality

#### Phase 3: Automated Triggers
1. **Course Date Events:**
   - Course starting soon (15 min before)
   - Course completed (when end time reached)
   - Course cancelled/rescheduled
   
2. **Student Activity Events:**
   - Student joins classroom
   - Student completes lesson
   - Low attendance alerts
   
3. **System Events:**
   - Technical issues detected
   - Capacity warnings
   - Performance alerts

### Notification Delivery Channels

#### Dashboard Notifications (Real-time):
- **Location:** Top navigation bell icon
- **Technology:** WebSockets or Server-Sent Events
- **Features:** Auto-refresh, sound alerts, desktop notifications

#### Email Notifications:
- **Templates:** Course-specific email designs
- **Scheduling:** Immediate, digest (daily/weekly), scheduled
- **Personalization:** Recipient role-based content

#### SMS Notifications (Critical Only):
- **Triggers:** System emergencies, critical course changes
- **Integration:** Twilio or similar service
- **Opt-in:** User preference setting

### Database Schema for Notifications

#### Notifications Table:
```sql
CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    recipient_type VARCHAR(20) NOT NULL, -- 'instructor', 'student', 'support', 'all'
    recipient_id INTEGER, -- NULL for broadcast
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSONB, -- Additional context data
    channels JSONB, -- ['dashboard', 'email', 'sms']
    priority VARCHAR(10) DEFAULT 'medium',
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_notifications_recipient ON notifications(recipient_type, recipient_id);
CREATE INDEX idx_notifications_created ON notifications(created_at);
CREATE INDEX idx_notifications_unread ON notifications(recipient_id, read_at) WHERE read_at IS NULL;
```

#### Notification Settings Table:
```sql
CREATE TABLE notification_settings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    user_type VARCHAR(20) NOT NULL, -- 'instructor', 'student', 'support'
    notification_type VARCHAR(50) NOT NULL,
    channel VARCHAR(20) NOT NULL, -- 'dashboard', 'email', 'sms'
    enabled BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, user_type, notification_type, channel)
);
```

### Integration with Existing Services

#### InstructorDashboardService Additions:
```php
public function getNotificationSummary(): array
{
    return [
        'unread_count' => $this->getUnreadNotificationCount(),
        'recent_notifications' => $this->getRecentNotifications(5),
        'urgent_alerts' => $this->getUrgentAlerts()
    ];
}
```

#### CourseDatesService Integration:
```php
public function getTodaysLessons(): array
{
    $lessons = // ... existing logic
    
    // Add notification context to each lesson
    foreach ($lessons as &$lesson) {
        $lesson['notifications'] = $this->getNotificationsForCourse($lesson['id']);
        $lesson['alerts'] = $this->getCourseAlerts($lesson['id']);
    }
    
    return $lessons;
}
```

### User Experience Design

#### Instructor Dashboard Notifications:
- **Header Badge:** Unread count with red indicator
- **Notification Dropdown:** Last 5 notifications with "View All" link
- **Course Cards:** Inline notification badges for course-specific alerts
- **Real-time Updates:** New notifications appear without page refresh

#### Notification Preferences Page:
- **Channel Selection:** Toggle dashboard/email/SMS per notification type
- **Timing Options:** Immediate, digest, scheduled delivery
- **Course-Specific:** Enable/disable per enrolled course
- **Priority Filtering:** Choose which priority levels to receive

### Testing Scenarios for Notifications

#### Automated Testing:
1. **Course Starting:** Verify instructor gets notification 15 min before
2. **Student Activity:** Verify instructor gets student join/leave alerts  
3. **System Issues:** Verify support gets technical alerts
4. **Preference Handling:** Verify disabled notifications don't send

#### User Acceptance Testing:
1. **Notification Timing:** Confirm appropriate timing for all types
2. **Content Relevance:** Verify notifications contain useful information
3. **Channel Preferences:** Test email/dashboard/SMS delivery options
4. **Performance:** Ensure notifications don't slow dashboard loading

## Next Steps

1. Start with documenting exact current dummy data locations
2. Create test course_dates for development
3. **NEW:** Design notification database tables and models
4. **NEW:** Create notification service architecture  
5. Implement one section at a time (lessons → stats → activity → notifications)
6. Test empty states thoroughly before moving to populated states
7. **NEW:** Test notification delivery across all channels

---

**Last Updated:** September 16, 2025  
**Next Review:** After Phase 1 completion
