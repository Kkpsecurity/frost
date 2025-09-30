Understood. Here’s th### Smart Buttons (enhanced with self-study precedence)

Statuses resolved per `(student, lesson)`:

* `passed_official` → **hide all** (Headstart/Retake/Start) - highest precedence
* `passed_self_study` → **show "Resume" or "Review"** - lesson completed via video
* `failed_official` → **show** "Retake Failed" list and **Start**
* `in_progress_self_study` → **show "Continue"** button 
* `eligible` (---

## Implementation Phases

### Phase 1: Data Foundation
1. **Database migrations**
   - Add `delivery_type` enum to `student_units` table
   - Create `self_study_lessons` table
   - Create `video_sessions` table with pool tracking
   - Add indexes for performance

2. **Model updates** 
   - StudentUnit.delivery_type enum support
   - SelfStudyLesson model with relationships
   - VideoSession model with pool calculations
   - Enhanced relationships and scopes

3. **Enhanced status resolver** 
   - Update getStudentDayStatus() to handle self-study precedence
   - Add self-study states: `passed_self_study`, `in_progress_self_study`
   - Implement precedence rules: self-study holds until official

### Phase 2: Backend Routes (Laravel Web Routes)
1. **Video Lesson Management** 
   - `/classroom/video-lessons` - Get available lessons with status
   - `/classroom/video-lessons/pool-status` - Current pool minutes
   - `/classroom/video-lessons/{id}` - Lesson details and eligibility

2. **Session tracking** 
   - `/classroom/video-lessons/{id}/start` - Begin lesson (create StudentUnit)
   - `/classroom/video-lessons/session/{id}/update` - Update progress and pool
   - `/classroom/video-lessons/session/{id}/complete` - Mark lesson complete

3. **Pool management** 
   - Pool consumption during active sessions
   - Reclaim logic when official class passes
   - Pool status updates and notifications

4. **Onboarding endpoints**
   - `/classroom/video-lessons/terms/agree` - Record terms agreement
   - `/classroom/video-lessons/headshot/submit` - Process identity verification

### Phase 3: Frontend Integration
1. **Video Tab UI** 
   - Lesson grid with smart status badges
   - Pool status meter with real-time updates
   - Onboarding modal flows (terms + headshot)

2. **Video player** 
   - Integrated session tracking with play/pause events
   - Pool consumption display during playback
   - Auto-pause when pool depleted

3. **Smart interactions**
   - Begin button → onboarding flow → session creation
   - Continue button for in-progress sessions
   - Review mode for completed lessons

### Phase 4: Testing & Refinement
1. **Unit tests** 
   - Pool math calculations and edge cases
   - Precedence rules and status resolution
   - Reclaim logic with multiple scenarios

2. **Integration tests** 
   - Complete lesson flow from eligible → passed
   - Onboarding flow with terms and headshot
   - Session tracking and pool updates

3. **Performance optimization** 
   - Efficient queries for lesson status
   - Cached pool calculations
   - Optimized video delivery

## Laravel Route Implementation

### Required Routes (web.php)
```php
// Video Lesson Management Routes
Route::middleware(['auth'])->prefix('classroom/video-lessons')->group(function () {
    // Overview and lesson listing
    Route::get('/', [VideoLessonController::class, 'index'])->name('video-lessons.index');
    Route::get('/overview', [VideoLessonController::class, 'overview'])->name('video-lessons.overview');
    Route::get('/pool-status', [VideoLessonController::class, 'poolStatus'])->name('video-lessons.pool-status');
    Route::get('/{lesson}', [VideoLessonController::class, 'show'])->name('video-lessons.show');

    // Session Management
    Route::post('/start', [VideoLessonController::class, 'startSession'])->name('video-lessons.start');
    Route::post('/session/{session}/update', [VideoLessonController::class, 'updateSession'])->name('video-lessons.session.update');
    Route::post('/session/{session}/complete', [VideoLessonController::class, 'completeSession'])->name('video-lessons.session.complete');

    // Onboarding Flow
    Route::post('/start-onboarding', [VideoLessonController::class, 'startOnboarding'])->name('video-lessons.start-onboarding');
    Route::post('/agree-terms', [VideoLessonController::class, 'agreeTerms'])->name('video-lessons.agree-terms');
    Route::post('/submit-headshot', [VideoLessonController::class, 'submitHeadshot'])->name('video-lessons.submit-headshot');
});
```

### Controller Structure
```php
// app/Http/Controllers/VideoLessonController.php
class VideoLessonController extends Controller
{
    public function index(Request $request) {
        // Return video lessons for current student with status
    }
    
    public function overview(Request $request) {
        // Return pool status, failed lessons, headstart suggestions
    }
    
    public function startSession(Request $request) {
        // Create StudentUnit with delivery_type='self_study'
        // Return session_id, pool_minutes, estimated_minutes
    }
    
    // ... other methods
}
```

## Implementation Priority

**HIGH PRIORITY:** 
- Database migrations for delivery_type and self-study tracking
- Enhanced status resolver with precedence logic
- Video lesson controller with basic routes

**MEDIUM PRIORITY:**
- Session tracking and pool management
- Onboarding flow with terms and headshot
- Video player integration

**LOW PRIORITY:**
- Advanced UI enhancements
- Performance optimizations
- Comprehensive test coverage

## Success Metrics

- Self-study lessons properly create StudentUnits with `delivery_type='self_study'`
- Precedence rules work: self-study status holds until official class
- Pool consumption accurately tracked during video sessions
- Reclaim logic properly restores pool minutes when official class passes
- UI clearly communicates lesson status and available actions

---

## Opinion (blunt)

* Don't let Video tab "unit passes" reclaim—**only** verified live passes. Otherwise it's farmable.
* Charge per **minute** (ceil). Seconds tracking is just for accuracy on pause/resume.empts, lesson active) → **show** in **Headstart** and **Start**
* `inactive` → **hide all**le task**—clean, complete, and ready to drop in your tracker. No invented config files.

# TASK: Build Video Tab — Make-Up & Retake (10h Pool, Precedence, Reclaim, Smart Buttons)

## Outcome (Done)

Students can:

* Use **Video** tab to take **Headstart** (random eligible) or **Retake Failed** lessons.
* Spend from a **configurable 10-hour pool** for self-study video time.
* **Pass a live class** for the same lesson later and **reclaim** previously spent self-study minutes.
* UI hides/shows **Start** buttons intelligently. **Official pass > self-study** (precedence).

---

## Scope & Rules (authoritative)

### Precedence

* If an **official/live** attempt for `(student, lesson)` is **PASSED**, that status **wins**. All Video tab Start CTAs for that lesson must be hidden (unless an admin override flag is on).

### Smart Buttons (only render when valid)

Statuses resolved per `(student, lesson)`:

* `passed_official` → **hide all** (Headstart/Retake/Start).
* `failed_official` → **show** “Retake Failed” list and **Start**.
* `eligible` (no official pass, lesson active) → **show** in **Headstart** and **Start**.
* `inactive` → **hide all**.

If pool is **0**, Start is **disabled** with tooltip: “Pool empty. Attend live to reclaim.”

### Video Mode

* Video tab route sets a **request/UI flag** `videoMode=true`.
* Only in Video Mode:

  * Show the **Video Sidebar** (pool meter, Headstart/Retake toggles, smart list).
  * Enable **minute ticking** (pool decremented while playing).

### Pool

* Student has **POOL_CAP minutes** (default 600 = 10h), **read from existing configuration/env** (no hardcoding).
* **Self-study** consumes pool **per watched minute** (ceil).
* **Official/live class** consumes **0**.

### Reclaim

* **Trigger:** When the official/live attempt for the same `(student, lesson)` is **PASSED**.
* **Amount:** Reclaim **sum of unreclaimed self-study minutes** for that lesson, **capped** by `(POOL_CAP - current_pool)`.
* Mark reclaimed rows (so no double credit). No reclaim on fail.

---

## Data (tables/columns)

* `students`

  * `makeup_minutes_remaining` INT NOT NULL DEFAULT **POOL_CAP**.
* `lessons` (existing): `id, unit_id, duration_minutes, active`.
* `student_lessons` (official attempts)

  * `id, student_id, lesson_id, status['not_started','in_progress','failed','passed']`,
  * `delivery_type['official_class','video_tab_unit']` (use `'official_class'` for live),
  * `duration_minutes`, `completed_at`, `passed_at`.
* `self_study_lessons` (video attempts)

  * `id, student_id, lesson_id, minutes_watched, status['in_progress','failed','passed']`,
  * `started_at`, `completed_at`, `reclaimed_at NULL`, `reclaim_batch_id NULL`.
* `video_sessions` (runtime tracking)

  * `id, student_id, lesson_id, origin['headstart','retake'], status['active','ended','abandoned']`,
  * `seconds_elapsed`, `pool_minutes_consumed`, `created_at`, `ended_at`, `self_study_lesson_id NULL`.

> Keep indexes on `(student_id, lesson_id)` for both lesson tables.

---

## API (names are placeholders; use your conventions)

* `GET /classroom/video-lessons/overview` → `{ pool_minutes, headstart_suggestion, failed_lessons[] }`
* `POST /classroom/video-lessons/start` `{ lesson_id, origin }` → `{ session_id, pool_minutes, estimated_minutes }`
* `POST /classroom/video-lessons/session/tick` `{ session_id }` → minute accrual + pool decrement; returns `{ pool_minutes, seconds_elapsed, status }`
* `POST /classroom/video-lessons/session/complete` `{ session_id, result }` → persists `self_study_lessons`, returns `{ self_study_id, pool_minutes }`

> Reclaim happens in your **existing live grading/attendance** flow on **official pass**.

---

## UI Design & Flow

### Lesson Detail Page Enhancement

When a lesson is selected from any context:

1. **Lesson Detail Card** displays:
   - Lesson title, duration, objectives
   - Current status badge (not_started/in_progress/failed/passed)
   - **"Begin Video Lesson" button** (smart visibility)

2. **Begin Button Logic**:
   ```javascript
   // Only show if:
   - No official_pass exists (precedence rule)
   - Student has pool minutes > 0
   - Lesson is active
   - Student has agreed to terms (if required)
   ```

3. **Onboarding Flow** (when "Begin" clicked):
   - **Step 1**: Terms Agreement modal
   - **Step 2**: Identity verification (headshot capture)
   - **Step 3**: Create self-study + session

### Self-Study Unit Creation

**Key Fix**: When student starts Video tab lesson, create a **StudentUnit** record:

```sql
INSERT INTO student_unit (
    course_auth_id, 
    course_unit_id, 
    course_date_id, -- NULL for self-study
    created_at,
    unit_completed = false,
    -- Special marker for self-study
    delivery_type = 'self_study'
)
```

### Precedence Logic (Enhanced)

```
Priority Order (highest to lowest):
1. Official Live Class PASSED → authoritative
2. Official Live Class FAILED → can retake via video
3. Self-Study PASSED → holds precedence until live class
4. Self-Study IN_PROGRESS → shows as started
5. Not Started → eligible for headstart
```

## Working Steps (do in order)

1. **Migrations & Models**

   * Add `makeup_minutes_remaining` to `students` (default = **POOL_CAP from config/env**).
   * Ensure `student_lessons.delivery_type` exists.
   * **Add `delivery_type` to `student_unit` table** (`'official_class'|'self_study'`).
   * Create `self_study_lessons`, `video_sessions`.
   * Wire Eloquent relations.

2. **Enhanced Status Resolver (handles self-study units)**

   ```php
   resolveStatus(student, lesson) {
       // Check official live class first
       $official = StudentLesson::where('student_id', $student)
           ->where('lesson_id', $lesson)
           ->where('delivery_type', 'official_class')
           ->first();
           
       if ($official && $official->status === 'passed') {
           return 'passed_official'; // Highest precedence
       }
       
       if ($official && $official->status === 'failed') {
           return 'failed_official'; // Can retake
       }
       
       // Check self-study attempts
       $selfStudy = SelfStudyLesson::where('student_id', $student)
           ->where('lesson_id', $lesson)
           ->orderBy('created_at', 'desc')
           ->first();
           
       if ($selfStudy && $selfStudy->status === 'passed') {
           return 'passed_self_study'; // Holds precedence
       }
       
       if ($selfStudy && $selfStudy->status === 'in_progress') {
           return 'in_progress_self_study';
       }
       
       return $lesson->active ? 'eligible' : 'inactive';
   }
   ```

   * Use it **everywhere** (backend lists and UI gating).

3. **Video Tab Route (sets `videoMode=true`)**

   * Controller returns overview DTO + `pool_cap` from config/env + `videoMode`.

4. **Video Sidebar (smart list)**

   * Toggle: **Headstart** | **Retake Failed**.
   * Headstart: pick random **eligible** (exclude official passes / inactive).
   * Retake: list **failed_official** lessons.
   * Render buttons per Smart Buttons matrix.

5. **Start Session (with onboarding)**

   **Onboarding Flow**:
   ```javascript
   POST /classroom/video-lessons/start-onboarding { lesson_id, origin }
   Response: { requires_agreement: bool, requires_headshot: bool }
   
   POST /classroom/video-lessons/agree-terms { lesson_id, agreed: true }
   Response: { success: true, next_step: 'headshot' }
   
   POST /classroom/video-lessons/submit-headshot { lesson_id, photo_data }
   Response: { success: true, validation_id, next_step: 'start_session' }
   ```

   **Session Creation**:
   * Validate: not `passed_official`, lesson active, pool > 0
   * **Create StudentUnit** with `delivery_type='self_study'`
   * **Create Validation** record for headshot
   * Create `video_sessions(active)` and return tick cadence **from config/env**

6. **Tick (minute charging)**

   * Only while playing & `videoMode=true`.
   * Every N seconds (from config/env):

     * Accumulate seconds; on minute boundary:

       * If `pool == 0` → return `paused_pool_empty`.
       * Else `pool--`, `pool_minutes_consumed++`.
   * Cap total watch to `lesson.duration_minutes`.

7. **Complete Session**

   * Compute `minutes_watched` (ceil, clamp).
   * Insert `self_study_lessons` row; link from `video_sessions`.
   * **Do not** reclaim here.

8. **Official Pass Hook**

   * On official live pass set in your grading flow:

     * Sum unreclaimed `self_study_lessons.minutes_watched` for same `(student, lesson)`.
     * `credit = min(sum, POOL_CAP - current_pool)`.
     * Increment pool; mark reclaimed rows (`reclaimed_at`, batch id).
     * Emit UI toast if student online.

9. **Guards & Concurrency**

   * One active `video_session` per `(student, lesson)`.
   * All pool changes in transactions (`FOR UPDATE` on student row).
   * Resume logic: tolerate missed ticks; reconcile by wall-clock up to 2 min.

---

## Acceptance Criteria (must pass)

* Headstart excludes official passes; Retake shows only official fails.
* Start buttons **never** show for `passed_official` (unless admin override is set).
* Pool decrements **per watched minute**; auto-pauses at 0.
* Official pass **reclaims** all unreclaimed self-study minutes for that lesson, capped at pool cap.
* Precedence enforced in all displays (official pass overrides).
* Configurable values (pool cap, tick seconds, feature flags) are **read from existing config/env**, not hardcoded.
**Pause Feature**

Students are allowed up to **1 hour of total pause time** per video session, divided into three pauses:

* **First pause:** up to 15 minutes
* **Second pause:** up to 30 minutes
* **Third pause:** up to 15 minutes

Each pause is tracked by a timer. Once the allotted pause time is used, further pausing is disabled for that session.

---

## Frontend Components

### 1. Enhanced Lesson Detail Page
```html
<div class="lesson-detail-card">
    <div class="lesson-header">
        <h3>{{ lesson.title }}</h3>
        <span class="status-badge {{ statusClass }}">{{ status }}</span>
        <span class="duration-badge">{{ lesson.duration_minutes }}min</span>
    </div>
    
    <div class="lesson-content">
        <p>{{ lesson.description }}</p>
        <ul class="lesson-objectives">
            <li v-for="objective in lesson.objectives">{{ objective }}</li>
        </ul>
    </div>
    
    <div class="lesson-actions">
        <!-- Smart Button Logic -->
        <button v-if="status === 'eligible'" 
                class="btn btn-primary btn-lg"
                @click="beginLesson">
            <i class="fas fa-play"></i> Begin Video Lesson
        </button>
        
        <button v-if="status === 'in_progress_self_study'" 
                class="btn btn-warning btn-lg"
                @click="continueLesson">
            <i class="fas fa-play"></i> Continue Lesson
        </button>
        
        <button v-if="status === 'passed_self_study'" 
                class="btn btn-success btn-lg"
                @click="reviewLesson">
            <i class="fas fa-eye"></i> Review Lesson
        </button>
        
        <div v-if="status === 'passed_official'" class="alert alert-success">
            <i class="fas fa-check-circle"></i> 
            Completed in live class on {{ completedDate }}
        </div>
    </div>
</div>
```

### 2. Onboarding Modal Components

#### Terms Agreement Modal
```html
<div class="onboarding-modal" v-if="step === 'terms'">
    <h4>Video Lesson Agreement</h4>
    <div class="terms-content">
        <p>By starting this video lesson:</p>
        <ul>
            <li>You will consume makeup minutes from your 10-hour pool</li>
            <li>You must complete identity verification (headshot)</li>
            <li>Your progress will be monitored and recorded</li>
            <li>Official live class results take precedence</li>
        </ul>
    </div>
    <div class="modal-actions">
        <button @click="agreeToTerms" class="btn btn-primary">
            I Agree - Continue
        </button>
        <button @click="cancelOnboarding" class="btn btn-secondary">
            Cancel
        </button>
    </div>
</div>
```

#### Headshot Capture Modal
```html
<div class="onboarding-modal" v-if="step === 'headshot'">
    <h4>Identity Verification</h4>
    <p>Take a headshot to verify your attendance for this lesson</p>
    
    <div class="camera-container">
        <video ref="camera" autoplay></video>
        <canvas ref="canvas" style="display: none;"></canvas>
    </div>
    
    <div class="headshot-preview" v-if="capturedPhoto">
        <img :src="capturedPhoto" alt="Captured headshot">
    </div>
    
    <div class="modal-actions">
        <button v-if="!capturedPhoto" @click="capturePhoto" class="btn btn-primary">
            <i class="fas fa-camera"></i> Take Photo
        </button>
        <button v-if="capturedPhoto" @click="retakePhoto" class="btn btn-warning">
            <i class="fas fa-redo"></i> Retake
        </button>
        <button v-if="capturedPhoto" @click="submitHeadshot" class="btn btn-success">
            <i class="fas fa-check"></i> Continue to Lesson
        </button>
    </div>
</div>
```

### 3. Video Player with Pool Integration
```html
<div class="video-player-container">
    <div class="pool-status-bar">
        <div class="pool-meter">
            <div class="pool-fill" :style="{ width: poolPercentage + '%' }"></div>
        </div>
        <span class="pool-text">{{ formatTime(poolMinutes) }} remaining</span>
    </div>
    
    <video ref="videoPlayer" 
           @play="startTicking" 
           @pause="pauseTicking"
           @ended="completeSession">
        <source :src="videoUrl" type="video/mp4">
    </video>
    
    <div class="player-controls">
        <button @click="togglePlay" class="btn btn-primary">
            <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'"></i>
        </button>
        <span class="time-display">
            {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
        </span>
    </div>
</div>
```

## Test Cases

* **Visibility:** `passed_official` → no Start; `failed_official` → Retake + Start; `eligible` → Begin + Start.
* **Self-study precedence:** student passes via video → shows as "passed" until live class
* **Onboarding flow:** Begin → Terms → Headshot → Session creation
* **Pool math:** watch 12 min → pool −12.
* **Reclaim:** had 90 min self-study (pool 510) → official pass → pool 600; rows marked reclaimed.
* **No double reclaim:** second official pass → 0 credit.
* **Pool cap honored:** set cap lower via env → math respects cap.
* **Pause at zero:** tick returns `paused_pool_empty`; UI pauses.

---

## Opinion (blunt)

* Don’t let Video tab “unit passes” reclaim—**only** verified live passes. Otherwise it’s farmable.
* Charge per **minute** (ceil). Seconds tracking is just for accuracy on pause/resume.
