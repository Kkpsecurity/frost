Student Area Build Plan — Corrected Model

Task Status: 🟡 In Prog7) Lifecycle (don't overcomplicate)

Template Draft → Template Published → CourseDate Scheduled → Live → Done/Archived (retain template version snapshot for audit).

---

## ACTUAL MODEL VERIFICATION (Based on Codebase Analysis)

**✅ VERIFIED MODELS & FIELDS:**

### Core Models (Templates - Offline Data)
```php
// Course.php - Products/commerce
protected $casts = [
    'id' => 'integer',
    'is_active' => 'boolean',
    'exam_id' => 'integer',
    'eq_spec_id' => 'integer', 
    'title' => 'string',      // 64 chars
    'title_long' => 'string', // text
    // ... more fields
];

// CourseUnit.php - Day templates
protected $casts = [
    'id' => 'integer',
    'course_id' => 'integer',
    'title' => 'string',        // 64 chars
    'admin_title' => 'string',  // 64 chars
    'ordering' => 'integer',
    // ... more fields
];

// CourseUnitLesson.php - Lessons in templates (assumed to exist)
// Template lessons inside course units
```

### Instance Models (Online Data)
```php
// CourseDate.php - Scheduled class instances
protected $casts = [
    'id' => 'integer',
    'is_active' => 'boolean',
    'course_unit_id' => 'integer',  // ✅ Links to CourseUnit template
    'starts_at' => 'timestamp',
    'ends_at' => 'timestamp',
    // ... more fields
];
```

### Instructor Models (Live Classes)
```php
// InstUnit.php - Instructor session
protected $casts = [
    'id' => 'integer',
    'course_date_id' => 'integer',    // ✅ Links to CourseDate
    'created_at' => 'timestamp',
    'created_by' => 'integer',        // instructor_id
    'completed_at' => 'timestamp',
    'completed_by' => 'integer',
    'assistant_id' => 'integer',
    // ... more fields
];

// InstLesson.php - Lesson snapshots (assumed to exist)
// Tracks instructor's lesson delivery
```

### Student Models (Progress Tracking)
```php
// StudentUnit.php - ONLINE class enrollment
protected $table = 'student_unit';
protected $casts = [
    'id' => 'integer',
    'course_auth_id' => 'integer',    // ✅ Links to enrollment
    'course_unit_id' => 'integer',    // ✅ Template reference
    'course_date_id' => 'integer',    // ✅ Class instance (ALWAYS PRESENT)
    'inst_unit_id' => 'integer',      // ✅ Instructor session
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp',
    // ... more fields
];

// SelfStudyLesson.php - OFFLINE self-study progress
protected $table = 'self_study_lessons';
protected $casts = [
    'id' => 'integer',
    'course_auth_id' => 'integer',    // ✅ Direct enrollment link
    'lesson_id' => 'integer',         // ✅ Direct lesson link
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp',
    'completed_at' => 'timestamp',
    'seconds_viewed' => 'integer',    // ✅ Time tracking
];

// StudentLesson.php - ONLINE lesson progress (requires StudentUnit)
protected $table = 'student_lesson';
protected $casts = [
    'id' => 'integer',
    'lesson_id' => 'integer',         // ✅ Links to Lesson
    'student_unit_id' => 'integer',   // ✅ Links to StudentUnit (ONLINE)
    'inst_lesson_id' => 'integer',    // ✅ Links to instructor's lesson
    // ... timestamps and progress fields
];
```

### Enrollment Model (Student Access)
```php
// CourseAuth.php - Student enrollment/purchase
protected $table = 'course_auths';
// Links User to Course with access permissions
// Contains agreed_at, start_date, expire_date, etc.

// RELATIONSHIPS:
public function SelfStudyLessons()    // OFFLINE progress
{
    return $this->hasMany(SelfStudyLesson::class, 'course_auth_id');
}

public function StudentUnits()        // ONLINE enrollments
{
    return $this->hasMany(StudentUnit::class, 'course_auth_id');
}
```

**🔍 KEY FINDINGS:**

1. **Naming is Correct:** Model names match the documented structure
2. **Relationships Verified:** Foreign keys align with expected relationships  
3. **Online/Offline Split Confirmed:**
   - Templates: Course, CourseUnit, CourseUnitLesson, Lesson
   - Online Instances: CourseDate, InstUnit, StudentUnit, StudentLesson
   - **Offline Progress: SelfStudyLesson** (directly links CourseAuth → Lesson)
4. **CourseAuth is Central:** Enrollment model with two progress paths:
   - `SelfStudyLessons()` - for offline self-study 
   - `StudentUnits()` - for online classes
5. **Table Names:** Some use singular (`student_unit`, `student_lesson`, `self_study_lessons`)

**📋 DATA COLLECTION STRATEGY:**

For Student Dashboard, we need to query:

**Offline Classes (Self-Study):**
- CourseAuth → Course → CourseUnit → Lessons
- **SelfStudyLesson** progress (seconds_viewed, completed_at)
- No CourseDate, StudentUnit, InstUnit, or live data

**Online Classes (Scheduled):**  
- CourseAuth → StudentUnit → CourseDate → CourseUnit → Lessons
- InstUnit (instructor session) 
- StudentLesson (live progress linked to StudentUnit)
- Real-time data (chat, zoom, attendance)

**Mixed Data Always Needed:**
- User profile and preferences
- Course completion certificates
- Assignments and submissions
- Messages and notificationss · Priority: High · ETA: 8 hrs

1) Core Concepts (clean)

Course — product you sell (commerce).

Lesson — static content objects.

CourseUnit (Template) — day-template: structure/sequence of lessons, durations, requirements. No calendar.

CourseUnitLesson (Template) — lessons inside the unit template.

CourseDate (Instance) — scheduled occurrence (date/time/zone/capacity) generated from a CourseUnit. This is the actual class on the calendar.

Opinionated push: rename to CourseUnitTemplate and CourseSession (instead of CourseDate). Your devs will thank you.

2) Offline vs Online

Offline data = Templates: Course, Lesson, CourseUnit, CourseUnitLesson. Versioned, reusable, not time-bound.

Online data = Live Instances: CourseDate (calendar), attendance, chat, recordings, submissions.

3) Instructor Flow

Instructor clicks “Teach” on a CourseDate → create InstUnit (ties instructor ↔ CourseDate).

InstUnitLesson = snapshot of the template lessons mapped to this CourseDate (allow reorder/overrides w/ audit trail).

4) Student Flow

Student joins a CourseDate → create StudentUnit (student ↔ CourseDate, status: RSVPed | Active | Completed | No-Show).

StudentUnitLesson tracks per-lesson progress, scores, timestamps, artifacts.

5) Relationships (decisive)

Course 1-N CourseUnit (Template)

CourseUnit (Template) 1-N CourseUnitLesson (Template)

CourseUnit (Template) 1-N CourseDate (Instance)

CourseDate 1-N InstUnit, 1-N StudentUnit

InstUnit 1-N InstUnitLesson (snapshot of template)

StudentUnit 1-N StudentUnitLesson (progress)

6) Minimal Fields (practical)

CourseUnit (Template): id, course_id, title, objectives, duration_total, version, is_active

CourseUnitLesson (Template): id, unit_id, lesson_id, seq, planned_duration

CourseDate (Instance): id, unit_template_id, starts_at, ends_at, timezone, capacity, room/zoom_link, status (Scheduled|Live|Done|Canceled), template_version_snapshot

InstUnit: id, course_date_id, instructor_id, confirmed_at, notes

InstUnitLesson: id, inst_unit_id, lesson_id, seq, actual_duration, overrides_json

StudentUnit: id, course_date_id, student_id, enroll_status, attendance (Present|Late|No-Show)

StudentUnitLesson: id, student_unit_id, lesson_id, status, score, started_at, completed_at, artifacts_json

7) Lifecycle (don’t overcomplicate)

Template Draft → Template Published → CourseDate Scheduled → Live → Done/Archived (retain template version snapshot for audit).


## 🔄 ONLINE vs OFFLINE DATA STRATEGY

### **📊 KEY PRINCIPLE: CourseDate Determines Data Flow**

**IF CourseDate EXISTS → Online Class Data**
**IF CourseDate MISSING → Offline Self-Study Data**

---

### **🌐 ONLINE DATA (CourseDate-Based Classes)**

**Data Source:** Live scheduled classes with instructor and real-time elements

**Query Path:**
```
CourseAuth → StudentUnit → CourseDate → CourseUnit → Lessons
                ↓
         InstUnit (instructor session)
                ↓  
         StudentLesson (live progress)
```

**Required Models:**
- ✅ CourseDate (scheduled class instance)
- ✅ StudentUnit (student enrollment in SPECIFIC class)
- ✅ InstUnit (instructor's live session)  
- ✅ StudentLesson (real-time lesson progress)

**Data Elements:**
- 📅 **Schedule:** starts_at, ends_at, timezone
- 👨‍🏫 **Instructor:** Live session tracking, notes, overrides
- 🎯 **Progress:** Real-time lesson completion, scores, timestamps
- 💬 **Interactive:** Chat logs, zoom recordings, attendance
- 📊 **Status:** Present/Late/No-Show, live completion tracking

---

### **📚 OFFLINE DATA (Self-Study Mode)**  

**Data Source:** Self-paced learning without scheduled classes

**Query Path:**
```
CourseAuth → Course → CourseUnit → CourseUnitLesson → Lesson
     ↓
SelfStudyLesson (direct progress tracking)
```

**Required Models:**
- ❌ NO CourseDate (no scheduled class)
- ❌ NO StudentUnit (no class-specific enrollment)
- ❌ NO InstUnit (no instructor session)  
- ✅ SelfStudyLesson (self-paced progress with time tracking)

**Data Elements:**
- 📖 **Content:** Static lessons, objectives, planned duration
- ⏰ **Self-Paced:** No fixed schedule, student controls timing
- ⏱️ **Time Tracking:** seconds_viewed, completed_at timestamps
- 🎯 **Goals:** Course objectives, skill targets, assessments
- 📋 **Structure:** Unit sequence, lesson order, prerequisites

---

### **🔍 DATA DETECTION LOGIC**

```php
// In StudentPortalController or new StudentDashboardService

public function getStudentClassroomData($courseAuthId) {
    $courseAuth = CourseAuth::with(['course.courseUnits', 'studentUnits.courseDate'])
                            ->find($courseAuthId);
    
    // Check for active StudentUnit (indicates online class enrollment)
    $activeStudentUnits = $courseAuth->studentUnits()
        ->whereHas('courseDate', function($q) {
            $q->where('starts_at', '>=', now()->subHours(2))    // Include recent classes
              ->where('ends_at', '<=', now()->addWeeks(4));     // Upcoming window
        })
        ->with('courseDate')
        ->get();
    
    if ($activeStudentUnits->count() > 0) {
        // 🌐 ONLINE DATA PATH - has StudentUnit with CourseDate
        return $this->getOnlineClassroomData($courseAuth, $activeStudentUnits);
    } else {
        // 📚 OFFLINE DATA PATH - use SelfStudyLesson tracking
        return $this->getOfflineClassroomData($courseAuth);
    }
}

private function getOnlineClassroomData($courseAuth, $studentUnits) {
    // Fetch live class data with instructor, students, real-time progress
    // Query: StudentUnit → CourseDate → InstUnit
    // Query: StudentUnit → StudentLesson (live progress)
    // Include chat, zoom links, attendance, live lesson tracking
}

private function getOfflineClassroomData($courseAuth) {
    // Fetch self-study data using SelfStudyLesson
    // Query: CourseAuth → Course → CourseUnit → Lessons
    // Query: CourseAuth → SelfStudyLesson (progress with seconds_viewed)
    // Include course structure, self-study progress
}
```

---

### **⚡ PERFORMANCE CONSIDERATIONS**

**Online Data:**
- 🔴 **Higher Cost:** Multiple joins, real-time data, instructor tracking
- 🔴 **Complex Caching:** Live data changes frequently
- ✅ **Rich Features:** Chat, zoom, instructor notes, attendance

**Offline Data:**  
- ✅ **Lower Cost:** Template-based, static content
- ✅ **Easy Caching:** Course structure rarely changes
- ✅ **Fast Loading:** Fewer database joins, simpler queries

---

### **🎯 DASHBOARD IMPLICATIONS**

**Online Classes Show:**
- 📅 Live schedule with countdown timers
- 👨‍🏫 Instructor info and session status  
- 👥 Classmate list and activity
- 💬 Live chat and recordings access
- 📊 Real-time progress with instructor feedback

**Offline Classes Show:**
- 📖 Self-study course outline
- ⏰ Personal progress tracking
- 🎯 Learning objectives and goals  
- 📋 Static lesson content and resources
- 📈 Basic completion percentages

---

## **🏗️ STUDENTDATALAYER IMPLEMENTATION PLAN**

### **📁 File Structure (Following Existing Pattern)**

Based on existing `app/Classes/ClassroomQueries/` structure:

```
app/Classes/
├── StudentDataLayer.php                 # Main class
├── StudentDataLayer/
│   ├── StudentOnlineDataTrait.php       # Online class data methods
│   ├── StudentOfflineDataTrait.php      # Offline self-study data methods  
│   ├── StudentProgressTrait.php         # Progress tracking utilities
│   ├── StudentDashboardTrait.php        # Dashboard-specific data
│   └── StudentCachingTrait.php          # Caching strategies
```

### **🎯 Main Class Structure**

```php
<?php
// app/Classes/StudentDataLayer.php

namespace App\Classes;

use App\Classes\StudentDataLayer\StudentOnlineDataTrait;
use App\Classes\StudentDataLayer\StudentOfflineDataTrait;
use App\Classes\StudentDataLayer\StudentProgressTrait;
use App\Classes\StudentDataLayer\StudentDashboardTrait;
use App\Classes\StudentDataLayer\StudentCachingTrait;

class StudentDataLayer
{
    use StudentOnlineDataTrait;
    use StudentOfflineDataTrait;
    use StudentProgressTrait;
    use StudentDashboardTrait;
    use StudentCachingTrait;

    protected $userId;
    protected $courseAuthId;
    
    public function __construct($userId = null, $courseAuthId = null)
    {
        $this->userId = $userId ?? auth()->id();
        $this->courseAuthId = $courseAuthId;
    }

    /**
     * Main method - determines online vs offline and returns appropriate data
     */
    public function getStudentDashboardData()
    {
        $courseAuth = $this->getCourseAuth();
        
        if ($this->hasActiveCourseDate($courseAuth)) {
            return $this->getOnlineClassData($courseAuth);
        } else {
            return $this->getOfflineStudyData($courseAuth);
        }
    }
}
```

### **🌐 StudentOnlineDataTrait Structure**

```php
<?php
// app/Classes/StudentDataLayer/StudentOnlineDataTrait.php

namespace App\Classes\StudentDataLayer;

use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Models\InstUnit;

trait StudentOnlineDataTrait
{
    /**
     * Get live class data with instructor, schedule, and real-time progress
     */
    public function getOnlineClassData($courseAuth)
    {
        $activeCourseDate = $this->getActiveCourseDate($courseAuth);
        $studentUnit = $this->getStudentUnit($activeCourseDate->id);
        $instUnit = $this->getInstructorUnit($activeCourseDate->id);
        
        return [
            'type' => 'online',
            'courseAuth' => $courseAuth,
            'courseDate' => $activeCourseDate,
            'studentUnit' => $studentUnit,
            'instUnit' => $instUnit,
            'schedule' => $this->getClassSchedule($activeCourseDate),
            'instructor' => $this->getInstructorInfo($instUnit),
            'classmates' => $this->getClassmates($activeCourseDate),
            'progress' => $this->getLiveProgress($studentUnit),
            'interactive' => $this->getInteractiveData($activeCourseDate),
        ];
    }
    
    protected function hasActiveCourseDate($courseAuth)
    {
        return CourseDate::whereHas('courseUnit', function($q) use ($courseAuth) {
            $q->where('course_id', $courseAuth->course_id);
        })
        ->where('starts_at', '>=', now()->subHours(2))
        ->where('ends_at', '<=', now()->addWeeks(4))
        ->exists();
    }
}
```

### **📚 StudentOfflineDataTrait Structure**

```php
<?php
// app/Classes/StudentDataLayer/StudentOfflineDataTrait.php

namespace App\Classes\StudentDataLayer;

trait StudentOfflineDataTrait
{
    /**
     * Get self-study data from course templates
     */
    public function getOfflineStudyData($courseAuth)
    {
        $course = $courseAuth->course()->with([
            'courseUnits' => function($q) {
                $q->orderBy('ordering');
            },
            'courseUnits.courseUnitLessons.lesson'
        ])->first();
        
        return [
            'type' => 'offline',
            'courseAuth' => $courseAuth,
            'course' => $course,
            'units' => $this->getOfflineUnits($course),
            'progress' => $this->getSelfStudyProgress($courseAuth),
            'structure' => $this->getCourseStructure($course),
            'objectives' => $this->getLearningObjectives($course),
            'resources' => $this->getStaticResources($course),
        ];
    }
    
    protected function getOfflineUnits($course)
    {
        return $course->courseUnits->map(function($unit) {
            return [
                'id' => $unit->id,
                'title' => $unit->title,
                'admin_title' => $unit->admin_title,
                'ordering' => $unit->ordering,
                'lessons' => $unit->courseUnitLessons->map(function($unitLesson) {
                    return [
                        'id' => $unitLesson->lesson->id,
                        'title' => $unitLesson->lesson->title,
                        'sequence' => $unitLesson->seq,
                        'duration' => $unitLesson->planned_duration,
                        'content' => $unitLesson->lesson->content,
                    ];
                }),
            ];
        });
    }
}
```

### **📊 StudentProgressTrait Structure**

```php
<?php
// app/Classes/StudentDataLayer/StudentProgressTrait.php

namespace App\Classes\StudentDataLayer;

use App\Models\StudentLesson;

trait StudentProgressTrait
{
    /**
     * Get comprehensive progress data for both online and offline
     */
    public function getProgressData($courseAuth, $type = 'both')
    {
        return [
            'overall' => $this->getOverallProgress($courseAuth),
            'units' => $this->getUnitProgress($courseAuth),
            'lessons' => $this->getLessonProgress($courseAuth),
            'scores' => $this->getScoreData($courseAuth),
            'timeline' => $this->getProgressTimeline($courseAuth),
        ];
    }
    
    protected function getLiveProgress($studentUnit)
    {
        // Real-time progress for online classes
        return StudentLesson::where('student_unit_id', $studentUnit->id)
            ->with(['lesson', 'instLesson'])
            ->get()
            ->map(function($sl) {
                return [
                    'lesson_id' => $sl->lesson_id,
                    'status' => $sl->status,
                    'score' => $sl->score,
                    'started_at' => $sl->started_at,
                    'completed_at' => $sl->completed_at,
                    'instructor_feedback' => $sl->instLesson->notes ?? null,
                ];
            });
    }
    
    protected function getSelfStudyProgress($courseAuth)
    {
        // Self-paced progress for offline study
        return StudentLesson::whereHas('studentUnit', function($q) use ($courseAuth) {
            $q->where('course_auth_id', $courseAuth->id)
              ->whereNull('course_date_id'); // No scheduled class
        })
        ->with('lesson')
        ->get()
        ->map(function($sl) {
            return [
                'lesson_id' => $sl->lesson_id,
                'status' => $sl->status,
                'score' => $sl->score,
                'self_completion_at' => $sl->completed_at,
                'time_spent' => $sl->time_spent ?? 0,
            ];
        });
    }
}
```

### **🎯 Integration with Controllers**

**Update StudentPortalController:**

```php
// app/Http/Controllers/StudentPortalController.php

use App\Classes\StudentDataLayer;

public function getClassRoomData(Request $request)
{
    $studentDataLayer = new StudentDataLayer(
        auth()->id(), 
        $request->input('course_auth_id')
    );
    
    $dashboardData = $studentDataLayer->getStudentDashboardData();
    
    return response()->json([
        'status' => 'success',
        'data' => $dashboardData,
        'cache_key' => $studentDataLayer->getCacheKey(),
        'generated_at' => now(),
    ]);
}
```

### **⚡ Caching Strategy**

```php
<?php
// app/Classes/StudentDataLayer/StudentCachingTrait.php

trait StudentCachingTrait
{
    protected function getCacheKey($suffix = '')
    {
        return "student_data.{$this->userId}.{$this->courseAuthId}.{$suffix}";
    }
    
    protected function cacheOnlineData($data, $minutes = 5)
    {
        // Short cache for live data
        Cache::put($this->getCacheKey('online'), $data, now()->addMinutes($minutes));
    }
    
    protected function cacheOfflineData($data, $hours = 24)
    {
        // Long cache for template data
        Cache::put($this->getCacheKey('offline'), $data, now()->addHours($hours));
    }
}
```

---

## **📊 STUDENT TRACKING & AUDIT SYSTEM**

### **🎯 OBJECTIVE: Complete Student Activity Audit Trail**

Record all student actions categorized by date and class for both online and offline progress tracking.

---

### **📁 NEW MODELS NEEDED**

#### **1. StudentActivity Model**

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
        'course_date_id' => 'integer',        // NULL for offline activities
        'student_unit_id' => 'integer',       // NULL for offline activities
        'lesson_id' => 'integer',             // NULL for non-lesson activities
        'activity_type' => 'string',          // Enum: lesson_start, lesson_complete, login, logout, etc.
        'category' => 'string',               // Enum: online, offline, system, interaction
        'data' => 'json',                     // Additional activity data
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

#### **2. StudentSession Model**

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
        'course_date_id' => 'integer',        // NULL for offline sessions
        'session_type' => 'string',           // online, offline
        'started_at' => 'timestamp',
        'ended_at' => 'timestamp',
        'duration_minutes' => 'integer',
        'lessons_accessed' => 'json',         // Array of lesson IDs
        'activities_count' => 'integer',
        'completion_rate' => 'decimal:2',     // Percentage 0-100
        'ip_address' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class, 'user_id', 'user_id')
                   ->whereBetween('created_at', [$this->started_at, $this->ended_at ?? now()]);
    }
}
```

---

### **🗄️ DATABASE MIGRATIONS**

#### **student_activities Migration**

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
            $table->string('activity_type', 50)->notNull(); // lesson_start, lesson_complete, etc.
            $table->string('category', 20)->notNull();      // online, offline, system, interaction
            $table->json('data')->nullable();               // Additional context data
            
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

#### **student_sessions Migration**

```php
<?php
// database/migrations/create_student_sessions_table.php

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
};
```

---

### **📝 ACTIVITY TYPES & CATEGORIES**

#### **Activity Types (Enum)**
```php
// Online Activities
'online_session_start'    // Student joins live class
'online_session_end'      // Student leaves live class
'online_lesson_start'     // Begins lesson in live class
'online_lesson_complete'  // Completes lesson in live class
'online_chat_message'     // Sends chat message
'online_breakout_join'    // Joins breakout room
'online_hand_raise'       // Raises hand in class

// Offline Activities  
'offline_session_start'   // Student starts self-study
'offline_session_end'     // Student ends self-study
'offline_lesson_start'    // Begins self-study lesson
'offline_lesson_complete' // Completes self-study lesson
'offline_resource_view'   // Views course resource/material
'offline_assignment_submit' // Submits assignment

// System Activities
'login'                   // Student logs in
'logout'                  // Student logs out
'course_access'           // Accesses course dashboard
'progress_sync'           // Progress synchronization

// Interaction Activities
'instructor_message'      // Message from instructor
'peer_interaction'        // Interaction with classmate
'support_ticket'          // Creates support ticket
```

#### **Categories**
- **online**: Live class activities with CourseDate
- **offline**: Self-study activities without CourseDate  
- **system**: Platform/authentication activities
- **interaction**: Communication and collaboration

---

### **🔧 TRACKING TRAITS FOR STUDENTDATALAYER**

#### **StudentActivityTrackingTrait**

```php
<?php
// app/Classes/StudentDataLayer/StudentActivityTrackingTrait.php

namespace App\Classes\StudentDataLayer;

use App\Models\StudentActivity;
use App\Models\StudentSession;

trait StudentActivityTrackingTrait
{
    /**
     * Log student activity with context
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
        return StudentSession::create([
            'user_id' => $this->userId,
            'course_auth_id' => $this->courseAuthId,
            'course_date_id' => $courseDateId,
            'session_type' => $sessionType,
            'started_at' => now(),
            'ip_address' => request()->ip(),
        ]);
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
}
```

---

### **🎯 INTEGRATION WITH EXISTING SYSTEM**

#### **Update StudentDataLayer Main Class**

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

#### **Update Controllers with Activity Logging**

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
```

---

## **🚀 NEXT PHASE: API DEVELOPMENT**
