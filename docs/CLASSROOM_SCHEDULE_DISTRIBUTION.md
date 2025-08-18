# Classroom Schedule Data Distribution

This document explains how the `useClassroomSchedule` hook is integrated at the data layer level and distributed throughout the app.

## 🏗️ **Architecture Overview**

```
InstructorDataLayer (Top Level)
├── ClassroomScheduleProvider (Data Distribution)
│   ├── useClassroomSchedule Hook (Data Source)
│   └── ClassroomScheduleContext (Global Access)
├── InstructorDataProvider (Instructor Progress)
└── SettingsProvider (App Configuration)
```

## ✅ **Implementation Complete**

### **1. Data Layer Integration**
The `useClassroomSchedule` hook is now integrated at the `InstructorDataLayer` level through the `ClassroomScheduleProvider`:

```tsx
<ClassroomScheduleProvider instructorId={instructorId}>
  <InstructorDataProvider instructorId={instructorId}>
    {/* Rest of app */}
  </InstructorDataProvider>
</ClassroomScheduleProvider>
```

### **2. Global Context Distribution**
Any component in the app can now access classroom schedule data using specialized hooks:

```tsx
import { 
  useClassroomSessions, 
  useWeeklySchedule, 
  useSessionActions 
} from './Context/ClassroomScheduleContext';

// In any component
const MyComponent = () => {
  const { classroomSessions, hasSessionsThisWeek } = useClassroomSessions();
  const { weeklySchedule } = useWeeklySchedule();
  const { claimSession } = useSessionActions();
  
  // Use the data...
};
```

## 🎯 **Available Data & Functions**

### **useClassroomSessions()**
```tsx
const {
  classroomSessions,        // Full session array
  isLoadingSessions,        // Loading state
  getSessionsForToday,      // Today's sessions
  getUpcomingSessions,      // Future sessions
  getActiveSessions,        // Live sessions
  hasSessionsThisWeek,      // Boolean check
  refetchSessions           // Refresh data
} = useClassroomSessions();
```

### **useWeeklySchedule()**
```tsx
const {
  weeklySchedule,          // Week data with stats
  isLoadingSchedule,       // Loading state
  refetchSchedule          // Refresh data
} = useWeeklySchedule();
```

### **useSessionActions()**
```tsx
const {
  claimSession,            // Take control function
  generateWeek,            // Generate sessions
  updateSession,           // Update session details
  isClaimingSession,       // Action loading states
  isGeneratingWeek,
  isUpdatingSession,
  canClaimSession,         // Permission checks
  canEnterSession
} = useSessionActions();
```

## 🔄 **Data Flow**

1. **InstructorDataLayer** loads with `instructorId`
2. **ClassroomScheduleProvider** initializes `useClassroomSchedule(instructorId)`
3. **ClassroomScheduleContext** distributes data to all child components
4. **Any component** can access classroom data via specialized hooks

## 📝 **Usage Examples**

### **Dashboard Panel Implementation**
```tsx
const DashboardPanel = () => {
  const { hasSessionsThisWeek, classroomSessions } = useClassroomSessions();
  
  if (hasSessionsThisWeek()) {
    return <WeeklyScheduleWidget sessions={classroomSessions} />;
  } else {
    return <BulletinBoardWidget />;
  }
};
```

### **Session Card Widget**
```tsx
const SessionCardWidget = ({ session }) => {
  const { claimSession, canClaimSession } = useSessionActions();
  
  const handleTakeControl = () => {
    if (canClaimSession(session)) {
      claimSession({ 
        course_date_id: session.id, 
        instructor_id: instructorId 
      });
    }
  };
  
  return (
    <div className="session-card">
      {/* Session details */}
      <button onClick={handleTakeControl}>
        Take Control
      </button>
    </div>
  );
};
```

### **Weekly Stats Component**
```tsx
const WeeklyStats = () => {
  const { weeklySchedule } = useWeeklySchedule();
  
  return (
    <div>
      <span>Total Sessions: {weeklySchedule?.total_sessions}</span>
      <span>Active: {weeklySchedule?.active_sessions}</span>
    </div>
  );
};
```

## 🎉 **Benefits**

### **✅ Single Source of Truth**
- Classroom data is fetched once at the data layer
- All components access the same cached data
- Automatic updates propagate everywhere

### **✅ Optimized Performance**
- TanStack Query caching and background updates
- No duplicate API calls
- Smart refetching strategies

### **✅ Easy to Use**
- Specialized hooks for different use cases
- Clean separation of concerns
- TypeScript support throughout

### **✅ Scalable Architecture**
- Easy to add new components that need classroom data
- Centralized data management
- Consistent error handling

## 🚀 **Ready for Dashboard Implementation**

The classroom schedule data is now distributed throughout the app and ready for:

1. **Dashboard Panel** - Main classroom status logic
2. **Session Widgets** - Individual session cards
3. **Weekly Stats** - Summary components
4. **Navigation Components** - Session-aware routing

All components can access real-time classroom schedule data without additional setup!
