# Instructor Dashboard Foundation Setup ✅

This document outlines the foundation setup for the Classroom Dashboard MVP, ready for next development steps.

## Current Status: Data Hooks Ready ✅

The Instructor Dashboard foundation is now complete with specialized data management hooks:

### ✅ **Data Architecture**
- **SettingsContext**: 15-minute cached Laravel configuration
- **useInstructorProgress**: InstUnit and InstLesson progress tracking
- **useClassroomSchedule**: CourseDate scheduling and session management
- **InstructorDataContext**: Backwards-compatible context layer

### ✅ **Hook Structure**

#### **useInstructorProgress Hook** (InstUnit & InstLesson)
```typescript
// Tracks instructor progress through units and lessons
const {
  // Data
  progressData,
  activeUnits,
  completedUnits,
  currentLessons,
  progressStats,
  
  // Actions
  startUnit,
  completeUnit,
  startLesson,
  completeLesson,
  
  // Helpers
  hasActiveUnit,
  getUnitForCourseDate,
  getLessonsForUnit,
  isUnitComplete,
  isLessonComplete,
} = useInstructorProgress(instructorId);
```

#### **useClassroomSchedule Hook** (CourseDate & Sessions)
```typescript
// Manages classroom scheduling and session data
const {
  // Data
  weeklySchedule,
  classroomSessions,
  currentWeek,
  weekStats,
  
  // Actions
  claimSession,
  generateWeek,
  updateSession,
  
  // Helpers
  getSessionsForToday,
  getSessionsForDay,
  getUpcomingSessions,
  getActiveSessions,
  hasSessionsThisWeek,
  canClaimSession,
  canEnterSession,
} = useClassroomSchedule(instructorId);
```

### ✅ **Data Models Mapped**

#### **Laravel → TypeScript Mapping**
- **InstUnit** → Instructor progress units
- **InstLesson** → Individual lesson completion tracking
- **CourseDate** → Scheduled classroom sessions
- **CourseUnit** → Course sections/modules
- **StudentUnit** → Student enrollment tracking

#### **Key Relationships**
```
CourseDate (session) 
├── has InstUnit (instructor progress)
│   └── has InstLessons[] (lesson tracking)
└── has StudentUnits[] (enrollment)
```

### ✅ **API Endpoints Ready**

#### **Instructor Progress APIs**
- `GET /api/instructor/{id}/progress` - Get instructor progress data
- `POST /api/instructor/units/start` - Start new instructional unit
- `POST /api/instructor/units/complete` - Complete instructional unit
- `POST /api/instructor/lessons/start` - Start lesson within unit
- `POST /api/instructor/lessons/complete` - Complete lesson

#### **Classroom Schedule APIs**
- `GET /api/classroom/schedule/week` - Get weekly schedule
- `GET /api/classroom/sessions` - Get detailed session info
- `POST /api/classroom/sessions/claim` - Claim/Take Control of session
- `POST /api/classroom/sessions/generate-week` - Generate sessions for week
- `POST /api/classroom/sessions/update` - Update session details

### ✅ **Component Structure**
- **InstructorDataLayer**: Main page component with basic error handling
- **Clean Layout**: AdminLTE-compatible structure ready for Panel implementation
- **Type Safety**: All TypeScript errors resolved, proper type definitions

## Next Steps Implementation Guide

### 🎯 **Dashboard Panel Implementation**

1. **Create Dashboard Panel** (`dashboard.panel.tsx`)
```typescript
import { useClassroomSchedule, useInstructorProgress } from '../Hooks';

const DashboardPanel = ({ instructorId }: { instructorId: number }) => {
  const { hasSessionsThisWeek, classroomSessions } = useClassroomSchedule(instructorId);
  const { progressStats } = useInstructorProgress(instructorId);
  
  return (
    <div>
      {hasSessionsThisWeek() ? (
        <WeeklyScheduleWidget sessions={classroomSessions} />
      ) : (
        <BulletinBoardWidget stats={progressStats} />
      )}
    </div>
  );
};
```

2. **Create Session Widgets**
```typescript
// SessionCardWidget with Take Control
const SessionCardWidget = ({ session }: { session: ClassroomSession }) => {
  const { claimSession, canClaimSession } = useClassroomSchedule();
  
  const handleTakeControl = () => {
    claimSession({ course_date_id: session.id, instructor_id: instructorId });
  };
  
  return (
    <div className="card">
      <h5>{session.course_title}</h5>
      <p>{session.starts_at} - {session.ends_at}</p>
      <p>Roster: {session.roster_count}/{session.max_capacity}</p>
      {canClaimSession(session) && (
        <button onClick={handleTakeControl}>Take Control</button>
      )}
    </div>
  );
};
```

### 🔧 **Database Integration**

The hooks are designed to work with your existing Laravel models:

```sql
-- InstUnit tracks instructor progress
SELECT * FROM inst_unit WHERE course_date_id = ?;

-- InstLesson tracks lesson completion
SELECT * FROM inst_lesson WHERE inst_unit_id = ?;

-- CourseDate represents classroom sessions
SELECT cd.*, cu.title as unit_title, c.title as course_title
FROM course_dates cd
JOIN course_units cu ON cd.course_unit_id = cu.id
JOIN courses c ON cu.course_id = c.id
WHERE cd.starts_at BETWEEN ? AND ?;
```

### 📁 **File Structure**

```
resources/js/React/Instructor/
├── Hooks/
│   ├── useInstructorData.tsx        # ✅ InstUnit/InstLesson progress
│   ├── useClassroomData.tsx         # ✅ CourseDate/session management
│   └── index.ts                     # ✅ Clean exports
├── Context/
│   ├── SettingsContext.tsx          # ✅ 15-min cache
│   └── InstructorDataContext.tsx    # ✅ Backwards compatibility
├── InstructorDataLayer.tsx          # ✅ Foundation ready
└── CLASSROOM_DASHBOARD.md           # ✅ This documentation
```

### 🚀 **Usage Examples**

#### **In Dashboard Panel**
```typescript
const DashboardPanel = ({ instructorId }: { instructorId: number }) => {
  // Get classroom sessions
  const { 
    classroomSessions, 
    hasSessionsThisWeek, 
    claimSession 
  } = useClassroomSchedule(instructorId);
  
  // Get instructor progress
  const { 
    progressStats, 
    startUnit 
  } = useInstructorProgress(instructorId);
  
  // Handle Take Control action
  const handleTakeControl = (sessionId: number) => {
    claimSession({ 
      course_date_id: sessionId, 
      instructor_id: instructorId 
    });
  };
  
  return (
    <div className="dashboard-panel">
      {hasSessionsThisWeek() ? (
        <SessionsView 
          sessions={classroomSessions} 
          onTakeControl={handleTakeControl} 
        />
      ) : (
        <BulletinBoard stats={progressStats} />
      )}
    </div>
  );
};
```

#### **In Session Card Widget**
```typescript
const SessionCard = ({ session }: { session: ClassroomSession }) => {
  const { claimSession, canClaimSession } = useClassroomSchedule();
  const { startUnit } = useInstructorProgress();
  
  const handleTakeControl = async () => {
    // 1. Claim the session (marks as Live)
    await claimSession({ 
      course_date_id: session.id, 
      instructor_id: instructorId 
    });
    
    // 2. Start instructor unit if needed
    if (!session.inst_unit) {
      await startUnit({ 
        course_date_id: session.id, 
        instructor_id: instructorId 
      });
    }
    
    // 3. Navigate to classroom
    window.location.href = `/admin/classroom/${session.id}`;
  };
  
  return (
    <div className="session-card">
      <h5>{session.course_title}</h5>
      <p>{session.unit_title}</p>
      <p>{new Date(session.starts_at).toLocaleString()}</p>
      <div className="roster-info">
        {session.roster_count}/{session.max_capacity || 'Unlimited'}
      </div>
      <div className="session-actions">
        {canClaimSession(session) ? (
          <button 
            className="btn btn-primary" 
            onClick={handleTakeControl}
          >
            Take Control
          </button>
        ) : (
          <span className="badge badge-info">
            {session.status}
          </span>
        )}
      </div>
    </div>
  );
};
```

### 💡 **Key Benefits**

1. **Separation of Concerns**: Clear distinction between instructor progress and classroom scheduling
2. **Type Safety**: Full TypeScript support with Laravel model mapping
3. **Real-time Updates**: TanStack Query provides caching and background updates
4. **Backwards Compatibility**: Existing InstructorDataContext still works
5. **API Ready**: All endpoints defined and ready for Laravel implementation

The foundation is complete and ready for the next development phase when you're ready to implement the classroom dashboard functionality with real data integration.
