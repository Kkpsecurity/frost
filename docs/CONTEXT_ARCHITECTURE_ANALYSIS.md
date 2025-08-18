# InstructorDataLayer Architecture: Single vs Separate Contexts

## Current Situation Analysis

Based on our polling intervals:
- **Settings**: 15 minutes (SettingsContext)
- **Instructor Progress**: 5 minutes (InstUnit, InstLesson)
- **Classroom Schedule**: 2 minutes (CourseDate, sessions)

## Option 1: Single Unified Context ⚡

```tsx
<InstructorContext value={{
  settings,     // 15-min cache
  instructor,   // 5-min polling 
  classroom     // 2-min polling
}}>
  {children}
</InstructorContext>
```

### Pros:
✅ **Simple API** - One context, one hook
✅ **Single source of truth** - All data in one place
✅ **Easy to use** - `const { settings, instructor, classroom } = useInstructorContext()`

### Cons:
❌ **Unnecessary re-renders** - Classroom updates every 2 minutes trigger re-renders for components only using settings
❌ **Coupling** - All data bundled together even if components only need one piece
❌ **Memory usage** - All data loaded even if not needed

## Option 2: Separate Contexts (Current) 🎯

```tsx
<SettingsContext>        {/* 15-min */}
  <InstructorContext>    {/* 5-min */}
    <ClassroomContext>   {/* 2-min */}
      {children}
```

### Pros:
✅ **Optimized polling** - Each context updates on its own schedule
✅ **Selective re-renders** - Components only re-render when their data changes
✅ **Flexible usage** - Components can subscribe to only what they need
✅ **Better performance** - Reduced unnecessary updates

### Cons:
❌ **More complex API** - Multiple hooks to remember
❌ **Context nesting** - Can get deep with many providers

## Recommendation: Hybrid Approach! 🚀

Best of both worlds - **unified data loading** with **optimized distribution**:

```tsx
// InstructorDataLayer - Loads all data
const InstructorDataLayer = ({ children, instructorId }) => {
  // Load all data with their optimal polling intervals
  const settings = useSettings();        // 15-min
  const instructor = useInstructor(id);  // 5-min  
  const classroom = useClassroom(id);    // 2-min

  // Single context with all data
  return (
    <InstructorContext value={{ settings, instructor, classroom }}>
      {children}
    </InstructorContext>
  );
};

// Usage - Components can access all or specific data
const MyComponent = () => {
  const { classroom } = useInstructorContext();        // Full access
  const sessions = useClassroomSessions();             // Specialized hook
  const progress = useInstructorProgress();            // Specialized hook
};
```

## Implementation Benefits:

### ✅ **Clean Data Layer**
- Loads all data in one place
- Manages different polling intervals internally
- Single point of truth

### ✅ **Flexible Access**
- One main context with all data
- Specialized hooks for common use cases
- Components choose what they need

### ✅ **Optimized Performance**
- TanStack Query manages polling intervals
- Components only re-render when their subscribed data changes
- Smart caching and background updates

## Usage Examples:

```tsx
// Full access
const DashboardPanel = () => {
  const { instructor, classroom } = useInstructorContext();
  // Use both datasets
};

// Specialized access
const SessionCard = () => {
  const { claimSession } = useSessionActions();
  // Only session actions
};

const WeeklyStats = () => {
  const { weeklySchedule } = useWeeklySchedule();
  // Only schedule data
};
```

This approach gives us the simplicity of a single context while maintaining the performance benefits of separate polling intervals!
