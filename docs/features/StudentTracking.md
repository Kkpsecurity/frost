# Student Tracking System Documentation

> **Server-Level Student Activity & Session Monitoring**

## 🎯 Overview

The Student Tracking System provides comprehensive server-level monitoring of all student activities, sessions, and progress across both online (live class) and offline (self-study) learning modes. This system creates a complete audit trail for student engagement analytics, progress tracking, and learning behavior analysis.

---

## 📊 System Architecture

### **Core Components**

1. **StudentActivity Model** - Individual activity logging
2. **StudentSession Model** - Session management and duration tracking  
3. **StudentActivityTrackingTrait** - Activity logging methods for StudentDataLayer
4. **Database Migrations** - Complete tracking schema
5. **Integration Layer** - Controller updates for automatic tracking

---

## 🗄️ Database Schema

### **student_activities Table**

Tracks every individual student action with full context:

```sql
CREATE TABLE student_activities (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- User & Course Context
    user_id             BIGINT NOT NULL,
    course_auth_id      BIGINT NOT NULL,
    
    -- Class Context (NULL for offline activities)
    course_date_id      BIGINT NULL,
    student_unit_id     BIGINT NULL,
    lesson_id           SMALLINT NULL,
    
    -- Activity Classification
    activity_type       VARCHAR(50) NOT NULL,     -- lesson_start, lesson_complete, etc.
    category           VARCHAR(20) NOT NULL,      -- online, offline, system, interaction
    data               JSON NULL,                 -- Additional context data
    
    -- Tracking Metadata
    ip_address         VARCHAR(45) NULL,
    user_agent         TEXT NULL,
    
    created_at         TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at         TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Performance Indexes
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_course_auth_created (course_auth_id, created_at),
    INDEX idx_course_date_created (course_date_id, created_at),
    INDEX idx_activity_category (activity_type, category),
    INDEX idx_lesson (lesson_id)
);
```

### **student_sessions Table**

Tracks learning sessions with duration and metrics:

```sql
CREATE TABLE student_sessions (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    user_id             BIGINT NOT NULL,
    course_auth_id      BIGINT NOT NULL,
    course_date_id      BIGINT NULL,              -- NULL for offline sessions
    
    session_type        VARCHAR(20) NOT NULL,     -- online, offline
    started_at          TIMESTAMPTZ NOT NULL,
    ended_at            TIMESTAMPTZ NULL,
    duration_minutes    INTEGER NULL,
    
    lessons_accessed    JSON NULL,                -- Array of lesson IDs
    activities_count    INTEGER DEFAULT 0,
    completion_rate     DECIMAL(5,2) DEFAULT 0,   -- 0-100%
    
    ip_address          VARCHAR(45) NULL,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    INDEX idx_user_started (user_id, started_at),
    INDEX idx_course_auth_session (course_auth_id, session_type),
    INDEX idx_course_date (course_date_id)
);
```

---

## 📝 Activity Types & Categories

### **Activity Type Enums**

#### **Online Activities (Live Class)**
```php
'online_session_start'    // Student joins live class
'online_session_end'      // Student leaves live class  
'online_lesson_start'     // Begins lesson in live class
'online_lesson_complete'  // Completes lesson in live class
'online_chat_message'     // Sends chat message
'online_breakout_join'    // Joins breakout room
'online_hand_raise'       // Raises hand in class
```

#### **Offline Activities (Self-Study)**
```php
'offline_session_start'   // Student starts self-study
'offline_session_end'     // Student ends self-study
'offline_lesson_start'    // Begins self-study lesson
'offline_lesson_complete' // Completes self-study lesson
'offline_resource_view'   // Views course resource/material
'offline_assignment_submit' // Submits assignment
```

#### **System Activities**
```php
'login'                   // Student logs in
'logout'                  // Student logs out
'course_access'           // Accesses course dashboard
'profile_update'          // Updates profile information
'password_change'         // Changes password
```

#### **Interaction Activities**
```php
'forum_post'              // Posts in course forum
'peer_message'            // Messages another student
'instructor_question'     // Asks instructor question
'rating_submit'           // Submits lesson/course rating
```

### **Category Classification**
- **online** - Live class activities
- **offline** - Self-study activities  
- **system** - Platform interactions
- **interaction** - Social/communication activities

---

## 🏗️ Model Implementation

### **StudentActivity Model**

```php
<?php
// app/Models/StudentActivity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PgTimestamps;
use App\Traits\Observable;

class StudentActivity extends Model
{
    use PgTimestamps, Observable;

    protected $table = 'student_activities';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'course_auth_id' => 'integer',
        'course_date_id' => 'integer',
        'student_unit_id' => 'integer',
        'lesson_id' => 'integer',
        'activity_type' => 'string',
        'category' => 'string',
        'data' => 'json',
        'ip_address' => 'string',
        'user_agent' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $guarded = ['id'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseAuth()
    {
        return $this->belongsTo(CourseAuth::class);
    }

    public function courseDate()
    {
        return $this->belongsTo(CourseDate::class);
    }

    public function studentUnit()
    {
        return $this->belongsTo(StudentUnit::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
```

### **StudentSession Model**

```php
<?php
// app/Models/StudentSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PgTimestamps;

class StudentSession extends Model
{
    use PgTimestamps;

    protected $table = 'student_sessions';
    
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'course_auth_id' => 'integer',
        'course_date_id' => 'integer',
        'session_type' => 'string',
        'started_at' => 'timestamp',
        'ended_at' => 'timestamp',
        'duration_minutes' => 'integer',
        'lessons_accessed' => 'json',
        'activities_count' => 'integer',
        'completion_rate' => 'decimal:2',
        'ip_address' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $guarded = ['id'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseAuth()
    {
        return $this->belongsTo(CourseAuth::class);
    }

    public function courseDate()
    {
        return $this->belongsTo(CourseDate::class);
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class, 'user_id', 'user_id')
                   ->whereBetween('created_at', [$this->started_at, $this->ended_at ?? now()]);
    }
}
```

---

## 🔧 StudentDataLayer Integration

### **StudentActivityTrackingTrait**

Add comprehensive tracking capabilities to the StudentDataLayer:

```php
<?php
// app/Classes/StudentDataLayer/StudentActivityTrackingTrait.php

namespace App\Classes\StudentDataLayer;

use App\Models\StudentActivity;
use App\Models\StudentSession;

trait StudentActivityTrackingTrait
{
    protected $currentSession;

    /**
     * Log student activity with full context
     */
    public function logActivity(
        string $activityType,
        string $category,
        array $data = [],
        ?int $lessonId = null,
        ?int $courseDateId = null,
        ?int $studentUnitId = null
    ): StudentActivity {
        return StudentActivity::create([
            'user_id' => $this->userId,
            'course_auth_id' => $this->courseAuthId,
            'course_date_id' => $courseDateId,
            'student_unit_id' => $studentUnitId,
            'lesson_id' => $lessonId,
            'activity_type' => $activityType,
            'category' => $category,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Start a new student session
     */
    public function startSession(string $sessionType, ?int $courseDateId = null): StudentSession
    {
        $this->currentSession = StudentSession::create([
            'user_id' => $this->userId,
            'course_auth_id' => $this->courseAuthId,
            'course_date_id' => $courseDateId,
            'session_type' => $sessionType,
            'started_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return $this->currentSession;
    }

    /**
     * End current session and calculate metrics
     */
    public function endSession(StudentSession $session): void
    {
        $endTime = now();
        $duration = $endTime->diffInMinutes($session->started_at);
        
        $activities = $this->getSessionActivities($session);
        $lessonsAccessed = $activities->whereNotNull('lesson_id')
                                    ->pluck('lesson_id')
                                    ->unique()
                                    ->values()
                                    ->toArray();
        
        $session->update([
            'ended_at' => $endTime,
            'duration_minutes' => $duration,
            'activities_count' => $activities->count(),
            'lessons_accessed' => $lessonsAccessed,
            'completion_rate' => $this->calculateSessionCompletionRate($activities),
        ]);
    }

    /**
     * Get comprehensive activity report
     */
    public function getActivityReport(array $filters = []): array
    {
        $query = StudentActivity::where('user_id', $this->userId)
                               ->where('course_auth_id', $this->courseAuthId);

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (isset($filters['course_date_id'])) {
            $query->where('course_date_id', $filters['course_date_id']);
        }

        $activities = $query->with(['courseDate', 'lesson'])
                           ->orderBy('created_at', 'desc')
                           ->get();

        return [
            'total_activities' => $activities->count(),
            'by_category' => $activities->groupBy('category')->map->count(),
            'by_type' => $activities->groupBy('activity_type')->map->count(),
            'by_date' => $activities->groupBy(fn($a) => $a->created_at->format('Y-m-d'))->map->count(),
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->activity_type,
                    'category' => $activity->category,
                    'lesson' => $activity->lesson?->title,
                    'class' => $activity->courseDate?->starts_at?->format('M j, Y H:i'),
                    'data' => $activity->data,
                    'timestamp' => $activity->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];
    }

    /**
     * Get session summary for date range
     */
    public function getSessionSummary(string $dateFrom, string $dateTo): array
    {
        $sessions = StudentSession::where('user_id', $this->userId)
                                 ->where('course_auth_id', $this->courseAuthId)
                                 ->whereBetween('started_at', [$dateFrom, $dateTo])
                                 ->get();

        $onlineSessions = $sessions->where('session_type', 'online');
        $offlineSessions = $sessions->where('session_type', 'offline');

        return [
            'total_sessions' => $sessions->count(),
            'online_sessions' => $onlineSessions->count(),
            'offline_sessions' => $offlineSessions->count(),
            'total_time_minutes' => $sessions->sum('duration_minutes'),
            'online_time_minutes' => $onlineSessions->sum('duration_minutes'),
            'offline_time_minutes' => $offlineSessions->sum('duration_minutes'),
            'average_completion_rate' => $sessions->avg('completion_rate'),
            'sessions_by_date' => $sessions->groupBy(fn($s) => $s->started_at->format('Y-m-d'))
                                          ->map(function($daySessions) {
                return [
                    'count' => $daySessions->count(),
                    'total_minutes' => $daySessions->sum('duration_minutes'),
                    'avg_completion' => $daySessions->avg('completion_rate'),
                ];
            }),
        ];
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities(int $limit = 10): array
    {
        return StudentActivity::where('user_id', $this->userId)
                             ->where('course_auth_id', $this->courseAuthId)
                             ->with(['lesson', 'courseDate'])
                             ->orderBy('created_at', 'desc')
                             ->limit($limit)
                             ->get()
                             ->map(function($activity) {
                                 return [
                                     'type' => $activity->activity_type,
                                     'category' => $activity->category,
                                     'lesson' => $activity->lesson?->title,
                                     'timestamp' => $activity->created_at->diffForHumans(),
                                 ];
                             })
                             ->toArray();
    }

    /**
     * Calculate session completion rate based on activities
     */
    private function calculateSessionCompletionRate($activities): float
    {
        $lessonStarts = $activities->where('activity_type', 'like', '%lesson_start')->count();
        $lessonCompletes = $activities->where('activity_type', 'like', '%lesson_complete')->count();
        
        if ($lessonStarts === 0) return 0;
        
        return min(100, ($lessonCompletes / $lessonStarts) * 100);
    }

    /**
     * Get activities for a specific session
     */
    private function getSessionActivities(StudentSession $session)
    {
        return StudentActivity::where('user_id', $session->user_id)
                             ->where('course_auth_id', $session->course_auth_id)
                             ->whereBetween('created_at', [
                                 $session->started_at,
                                 $session->ended_at ?? now()
                             ])
                             ->get();
    }
}
```

---

## 🎯 Implementation Integration

### **Update StudentDataLayer Main Class**

```php
<?php
// app/Classes/StudentDataLayer.php

class StudentDataLayer
{
    use StudentOnlineDataTrait;
    use StudentOfflineDataTrait;
    use StudentProgressTrait;
    use StudentDashboardTrait;
    use StudentCachingTrait;
    use StudentActivityTrackingTrait; // 🔥 NEW

    protected $currentSession;

    public function getStudentDashboardData()
    {
        // Start session tracking
        $sessionType = $this->hasActiveCourseDate($this->getCourseAuth()) ? 'online' : 'offline';
        $this->currentSession = $this->startSession($sessionType);
        
        // Log dashboard access
        $this->logActivity('course_access', 'system', [
            'dashboard_type' => $sessionType,
            'timestamp' => now(),
        ]);

        // Get regular data
        $data = parent::getStudentDashboardData();

        // Add activity data
        $data['tracking'] = [
            'session_id' => $this->currentSession->id,
            'session_type' => $sessionType,
            'recent_activities' => $this->getRecentActivities(),
        ];

        return $data;
    }
}
```

### **Controller Integration**

Update controllers to automatically log activities:

```php
// app/Http/Controllers/StudentPortalController.php

public function startLesson(Request $request)
{
    $studentDataLayer = new StudentDataLayer(auth()->id(), $request->course_auth_id);
    
    // Determine if online or offline
    $isOnline = $request->filled('course_date_id');
    $category = $isOnline ? 'online' : 'offline';
    $activityType = $isOnline ? 'online_lesson_start' : 'offline_lesson_start';
    
    // Log activity
    $studentDataLayer->logActivity(
        $activityType,
        $category,
        [
            'lesson_title' => $request->lesson_title,
            'unit_id' => $request->unit_id,
        ],
        $request->lesson_id,
        $request->course_date_id,
        $request->student_unit_id
    );
    
    return response()->json(['status' => 'lesson_started']);
}

public function completeLesson(Request $request)
{
    $studentDataLayer = new StudentDataLayer(auth()->id(), $request->course_auth_id);
    
    // Log completion
    $isOnline = $request->filled('course_date_id');
    $activityType = $isOnline ? 'online_lesson_complete' : 'offline_lesson_complete';
    
    $studentDataLayer->logActivity(
        $activityType,
        $isOnline ? 'online' : 'offline',
        [
            'completion_time_seconds' => $request->duration,
            'progress_percentage' => $request->progress,
        ],
        $request->lesson_id,
        $request->course_date_id,
        $request->student_unit_id
    );
    
    return response()->json(['status' => 'lesson_completed']);
}
```

---

## 📊 Database Migrations

### **Create StudentActivities Migration**

```php
<?php
// database/migrations/create_student_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // User and Course Context
            $table->unsignedBigInteger('user_id')->notNull();
            $table->unsignedBigInteger('course_auth_id')->notNull();
            
            // Class Context (NULL for offline activities)
            $table->unsignedBigInteger('course_date_id')->nullable();
            $table->unsignedBigInteger('student_unit_id')->nullable();
            $table->unsignedSmallInteger('lesson_id')->nullable();
            
            // Activity Classification
            $table->string('activity_type', 50)->notNull();
            $table->string('category', 20)->notNull();
            $table->json('data')->nullable();
            
            // Tracking Data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestampTz('created_at')->notNull()->useCurrent();
            $table->timestampTz('updated_at')->notNull()->useCurrent();
            
            // Indexes for efficient querying
            $table->index(['user_id', 'created_at']);
            $table->index(['course_auth_id', 'created_at']);
            $table->index(['course_date_id', 'created_at']);
            $table->index(['activity_type', 'category']);
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
};
```

### **Create StudentSessions Migration**

```php
<?php
// database/migrations/create_student_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('user_id')->notNull();
            $table->unsignedBigInteger('course_auth_id')->notNull();
            $table->unsignedBigInteger('course_date_id')->nullable(); // NULL for offline
            
            $table->string('session_type', 20)->notNull(); // online, offline
            $table->timestampTz('started_at')->notNull();
            $table->timestampTz('ended_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            
            $table->json('lessons_accessed')->nullable();
            $table->unsignedInteger('activities_count')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0); // 0-100%
            
            $table->string('ip_address', 45)->nullable();
            $table->timestampTz('created_at')->notNull()->useCurrent();
            $table->timestampTz('updated_at')->notNull()->useCurrent();
            
            $table->index(['user_id', 'started_at']);
            $table->index(['course_auth_id', 'session_type']);
            $table->index('course_date_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sessions');
    }
};
```

---

## 🚀 Usage Examples

### **Basic Activity Logging**

```php
$studentDataLayer = new StudentDataLayer($userId, $courseAuthId);

// Log lesson start
$studentDataLayer->logActivity(
    'offline_lesson_start',
    'offline',
    ['lesson_title' => 'Introduction to Laravel'],
    $lessonId
);

// Log session start
$session = $studentDataLayer->startSession('offline');

// Log session end
$studentDataLayer->endSession($session);
```

### **Activity Reports**

```php
// Get recent activity
$recentActivities = $studentDataLayer->getRecentActivities(20);

// Get activity report with filters
$report = $studentDataLayer->getActivityReport([
    'date_from' => '2024-01-01',
    'date_to' => '2024-01-31',
    'category' => 'offline'
]);

// Get session summary
$summary = $studentDataLayer->getSessionSummary('2024-01-01', '2024-01-31');
```

---

## 📈 Analytics & Reporting

### **Key Metrics Available**

1. **Session Analytics**
   - Total sessions (online/offline)
   - Session duration tracking
   - Completion rates
   - Daily/weekly patterns

2. **Activity Analytics**
   - Activity counts by type
   - Category distribution
   - Time-based patterns
   - Lesson engagement metrics

3. **Progress Analytics**
   - Learning velocity
   - Engagement patterns
   - Completion trends
   - Resource usage

### **Dashboard Integration**

The tracking system automatically integrates with student dashboards to show:

- Recent activity timeline
- Session statistics
- Progress indicators
- Engagement metrics

---

## 🔒 Privacy & Security

### **Data Protection**

- IP addresses stored for security auditing
- User agents tracked for device analysis
- All tracking data tied to course enrollment
- Automatic cleanup policies available

### **GDPR Compliance**

- Activity data can be exported
- Complete data deletion supported
- Consent tracking integration ready
- Anonymization options available

---

## 🛠️ Installation Steps

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Add Trait to StudentDataLayer**
   ```php
   use StudentActivityTrackingTrait;
   ```

3. **Update Controllers**
   - Add activity logging to lesson actions
   - Implement session management
   - Add tracking to API endpoints

4. **Test Implementation**
   - Verify activity logging
   - Check session tracking
   - Validate report generation

---

## 📝 Notes

- System designed for high-volume activity logging
- Optimized database indexes for performance
- Flexible data structure for future enhancements
- Compatible with existing StudentDataLayer architecture
- Ready for real-time analytics integration

---

**Implementation Status:** ✅ Architecture Complete - Ready for Development

**Next Steps:** Run migrations → Add trait → Update controllers → Test tracking
