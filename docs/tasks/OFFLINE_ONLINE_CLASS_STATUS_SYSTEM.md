# Offline/Online Class Status System Implementation

**Date Created:** September 23, 2025  
**Priority:** High - Core functionality for live classroom management  
**Goal:** Enable real-time class status detection across student and instructor interfaces  

## 🎯 Tomorrow's Objective

**Primary Goal:** Get the offline view working for both student and instructor dashboards, then implement the live class detection system so all sections can tell when a class is live.

## 📋 Current State Analysis

### What We Have (Working)
✅ **Student Dashboard**: Basic classroom interface with React components  
✅ **Instructor Dashboard**: Offline view with real course data from database  
✅ **Database Structure**: CourseDate, CourseUnit, Course tables properly set up  
✅ **Course Data Service**: CourseDatesService with getTodaysLessons() method  
✅ **Student Account Dashboard**: Sidebar navigation with profile, settings, orders, payments  

### What Needs Fixing (Immediate)
❌ **Header Settings Link**: Currently points to `/account/settings` (POST route) instead of `/account?section=settings`  
❌ **Offline/Online Status**: No system to detect when instructor starts a live class  
❌ **Real-time Updates**: Student dashboard doesn't know when class goes live  
❌ **Status Synchronization**: No communication between instructor and student interfaces  

## 🔧 Technical Implementation Plan

### Phase 1: Fix Header Settings Link (30 minutes)
**File to find and fix:** Header template with settings link
**Current broken URL:** `/account/settings` 
**Correct URL:** `/account?section=settings`

**Action Items:**
1. Search for settings link in header templates
2. Update URL to use query parameter approach
3. Test that clicking header settings goes to correct account section

### Phase 2: Define Class Status System (1 hour)

#### Status Definitions
```php
// Class Status Enum
const CLASS_STATUS = [
    'OFFLINE' => 'offline',      // No active class, students see self-study
    'STARTING' => 'starting',    // Instructor preparing, students see "Class starting soon"
    'LIVE' => 'live',           // Active class, students join live session
    'ENDING' => 'ending',       // Class wrapping up, final minutes
    'COMPLETED' => 'completed'   // Class finished, students see completion status
];
```

#### Database Schema Changes
```sql
-- Add status tracking to course_dates table
ALTER TABLE course_dates ADD COLUMN class_status VARCHAR(20) DEFAULT 'offline';
ALTER TABLE course_dates ADD COLUMN live_started_at TIMESTAMP NULL;
ALTER TABLE course_dates ADD COLUMN live_ended_at TIMESTAMP NULL;
ALTER TABLE course_dates ADD COLUMN instructor_id INTEGER NULL;

-- Index for performance
CREATE INDEX idx_course_dates_status ON course_dates(class_status, starts_at);
```

### Phase 3: Instructor Class Control (2 hours)

#### Instructor Dashboard Updates
**File:** `resources/views/dashboards/instructor/offline.blade.php`

**Add Class Control Buttons:**
```html
<!-- For each today's lesson -->
<div class="lesson-controls">
    @if($lesson['class_status'] === 'offline')
        <button class="btn btn-success start-class-btn" data-course-date-id="{{ $lesson['id'] }}">
            <i class="fas fa-play"></i> Start Class
        </button>
    @elseif($lesson['class_status'] === 'live')
        <button class="btn btn-danger end-class-btn" data-course-date-id="{{ $lesson['id'] }}">
            <i class="fas fa-stop"></i> End Class
        </button>
        <span class="live-indicator">
            <i class="fas fa-circle text-danger"></i> LIVE
        </span>
    @endif
</div>
```

#### Backend API Endpoints
**File:** `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php`

```php
public function startClass(Request $request, $courseDateId)
{
    $courseDate = CourseDate::findOrFail($courseDateId);
    
    $courseDate->update([
        'class_status' => 'live',
        'live_started_at' => now(),
        'instructor_id' => auth()->id()
    ]);
    
    // Broadcast to students
    broadcast(new ClassStatusChanged($courseDate))->toOthers();
    
    return response()->json(['status' => 'live']);
}

public function endClass(Request $request, $courseDateId)
{
    $courseDate = CourseDate::findOrFail($courseDateId);
    
    $courseDate->update([
        'class_status' => 'completed',
        'live_ended_at' => now()
    ]);
    
    // Broadcast to students
    broadcast(new ClassStatusChanged($courseDate))->toOthers();
    
    return response()->json(['status' => 'completed']);
}
```

### Phase 4: Student Status Detection (2 hours)

#### Student Dashboard Status Display
**File:** `resources/js/React/Student/Components/StudentDataLayer.tsx`

```typescript
interface ClassStatus {
    status: 'offline' | 'starting' | 'live' | 'ending' | 'completed';
    courseDateId: number;
    courseTitle: string;
    instructorName?: string;
    liveStartedAt?: string;
    joinUrl?: string;
}

const StudentDataLayer: React.FC = () => {
    const [classStatus, setClassStatus] = useState<ClassStatus | null>(null);
    
    useEffect(() => {
        // Check for live classes on mount
        checkClassStatus();
        
        // Listen for real-time updates
        window.Echo?.channel('class-status')
            .listen('ClassStatusChanged', (event: ClassStatus) => {
                setClassStatus(event);
            });
    }, []);
};
```

#### Status Banner Component
```typescript
const LiveClassBanner: React.FC<{ status: ClassStatus }> = ({ status }) => {
    if (status.status === 'offline') return null;
    
    return (
        <div className={`live-class-banner status-${status.status}`}>
            {status.status === 'live' && (
                <>
                    <i className="fas fa-circle text-danger"></i>
                    <strong>LIVE CLASS: {status.courseTitle}</strong>
                    <button className="btn btn-primary btn-sm">Join Class</button>
                </>
            )}
            {status.status === 'starting' && (
                <>
                    <i className="fas fa-clock text-warning"></i>
                    <strong>Class Starting Soon: {status.courseTitle}</strong>
                </>
            )}
        </div>
    );
};
```

### Phase 5: Real-time Communication (1 hour)

#### Laravel Echo Setup
**File:** `resources/js/bootstrap.js`

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

#### Broadcast Event
**File:** `app/Events/ClassStatusChanged.php`

```php
class ClassStatusChanged implements ShouldBroadcast
{
    public function __construct(
        public CourseDate $courseDate
    ) {}

    public function broadcastOn()
    {
        return new Channel('class-status');
    }

    public function broadcastWith()
    {
        return [
            'status' => $this->courseDate->class_status,
            'courseDateId' => $this->courseDate->id,
            'courseTitle' => $this->courseDate->courseUnit->course->title,
            'liveStartedAt' => $this->courseDate->live_started_at,
        ];
    }
}
```

## 🗂️ Files to Modify

### Backend Files
1. **Migration:** `database/migrations/add_class_status_to_course_dates.php`
2. **Controller:** `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php`
3. **Event:** `app/Events/ClassStatusChanged.php`
4. **Service:** `app/Services/Frost/Instructors/CourseDatesService.php`

### Frontend Files
1. **Instructor View:** `resources/views/dashboards/instructor/offline.blade.php`
2. **Student Components:** `resources/js/React/Student/Components/`
   - `StudentDataLayer.tsx`
   - `LiveClassBanner.tsx`
   - `StudentDashboard.tsx`

### Routes
1. **API Routes:** `routes/api.php` - Add class control endpoints
2. **Header Template:** Find and fix settings link

## 🧪 Testing Scenarios

### Scenario 1: Instructor Starts Class
1. **Given:** Instructor is on dashboard, class is scheduled for today
2. **When:** Instructor clicks "Start Class" button
3. **Then:** 
   - Button changes to "End Class" with live indicator
   - Database updates course_dates.class_status = 'live'
   - Students see live class banner

### Scenario 2: Student Sees Live Class
1. **Given:** Student is on their dashboard
2. **When:** Instructor starts a class they're enrolled in
3. **Then:**
   - Live class banner appears at top
   - "Join Class" button is visible
   - Page updates without refresh

### Scenario 3: Class Ends
1. **Given:** Class is currently live
2. **When:** Instructor clicks "End Class"
3. **Then:**
   - Status changes to 'completed'
   - Students see completion message
   - Instructor dashboard updates

## 📊 Success Metrics

### Functional Requirements
- [ ] Header settings link works correctly
- [ ] Instructor can start/end classes with button clicks
- [ ] Students see real-time status updates
- [ ] Database accurately tracks class status
- [ ] No page refreshes required for status updates

### Performance Requirements
- [ ] Status changes propagate to students within 2 seconds
- [ ] Dashboard loads remain under 3 seconds
- [ ] Real-time listeners don't cause memory leaks

### User Experience
- [ ] Clear visual indicators for live/offline status
- [ ] Intuitive button states for instructors
- [ ] Responsive design works on mobile
- [ ] Error states handled gracefully

## 🚨 Risk Assessment

### High Risk
- **Real-time Broadcasting**: WebSocket connections can be unstable
- **Database Performance**: Class status queries must be optimized
- **State Synchronization**: Race conditions between instructor and student updates

### Medium Risk
- **Browser Compatibility**: Echo/Pusher support across browsers
- **Mobile Experience**: Real-time updates on mobile devices

### Low Risk
- **Database Migration**: Simple column additions
- **Button UI Changes**: Standard Bootstrap components

## 🔄 Rollback Plan

If real-time features fail:
1. **Fallback:** Implement polling every 30 seconds
2. **Graceful Degradation:** Show "Refresh to check class status" message
3. **Manual Refresh:** Add refresh button for status updates

## 📝 Tomorrow's Task Breakdown

### Morning (9:00 AM - 12:00 PM)
1. **Fix Header Settings Link** (30 min)
2. **Create Database Migration** (30 min)
3. **Add Class Control Buttons to Instructor Dashboard** (2 hours)

### Afternoon (1:00 PM - 5:00 PM)
1. **Implement Backend API Endpoints** (1.5 hours)
2. **Create Student Status Detection** (2 hours)
3. **Test Real-time Updates** (30 min)

### Testing & Polish (5:00 PM - 6:00 PM)
1. **End-to-end Testing** (45 min)
2. **Documentation Updates** (15 min)

## 🎯 Definition of Done

Tomorrow is successful when:
- ✅ Students can see when their class goes live
- ✅ Instructors can start/end classes with simple button clicks
- ✅ Status updates happen in real-time without page refresh
- ✅ Header settings link works correctly
- ✅ System handles edge cases gracefully

## 📚 Related Documentation

- **Current Status:** [`INSTRUCTOR_DASHBOARD_REAL_DATA.md`](./instrcutor/INSTRUCTOR_DASHBOARD_REAL_DATA.md)
- **Student System:** [`student-sidebar-dynamic-lessons.md`](./student-sidebar-dynamic-lessons.md)
- **Video Tab:** [`video-tab-self-study-implementation.md`](./video-tab-self-study-implementation.md)

---

**Ready to implement tomorrow!** 🚀  
**Estimated Time:** 6-7 hours  
**Key Focus:** Real-time class status across instructor and student interfaces
