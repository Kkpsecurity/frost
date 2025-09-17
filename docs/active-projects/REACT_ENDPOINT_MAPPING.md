# React API Endpoint Mapping

## Overview
This document shows how the debug endpoints are matched to React API endpoints to provide real data to the React frontend instead of mock data.

## Debug Endpoints vs React API Endpoints

### 1. Debug Endpoints (Development/Testing)

| Endpoint | Purpose | Data Format |
|----------|---------|-------------|
| `/classroom/debug` | Full dashboard data (both student + classroom) | Complete debug object |
| `/classroom/debug/student` | Student data only | `{ student: {...}, courseAuth: [...] }` |
| `/classroom/debug/class` | Classroom data only | `{ instructors: [...], courseDates: [...] }` |

### 2. React API Endpoints (Production)

| Endpoint | Purpose | React Interface | Description |
|----------|---------|----------------|-------------|
| `/classroom/api/stats` | Student statistics | `StudentStats` | Enrollment count, progress metrics |
| `/classroom/api/recent-lessons` | Recent lessons | `RecentLesson[]` | Recently accessed lessons with progress |
| `/classroom/api/upcoming-assignments` | Assignments due | `UpcomingAssignment[]` | Assignments with due dates |

## React Interfaces

### StudentStats
```typescript
interface StudentStats {
    enrolledCourses: number;
    completedLessons: number;
    assignmentsDue: number;
    hoursLearned: number;
}
```

### RecentLesson
```typescript
interface RecentLesson {
    id: number;
    title: string;
    course: string;
    progress: number;
    duration: string;
    lastAccessed: string;
}
```

### UpcomingAssignment
```typescript
interface UpcomingAssignment {
    id: number;
    title: string;
    course: string;
    dueDate: string;
    type: 'quiz' | 'assignment' | 'project';
}
```

## Data Transformation

### Debug Data → React API Data

1. **Student Stats Transformation:**
   ```php
   // From debug courseAuth data
   $courseAuths = $service->getCourseAuths();
   
   // To React stats format
   $stats = [
       'enrolledCourses' => count($courseAuths),
       'completedLessons' => 0, // TODO: Calculate from lessons
       'assignmentsDue' => 0,   // TODO: Calculate from assignments  
       'hoursLearned' => 0      // TODO: Calculate from progress
   ];
   ```

2. **Recent Lessons Transformation:**
   ```php
   // TODO: Use actual lesson data from debug classroom data
   // Current: Mock data matching course titles from debug
   $recentLessons = [
       [
           'title' => 'Network Security Fundamentals',
           'course' => 'Advanced Network Security', // From debug course data
           'progress' => 85,
           // ...
       ]
   ];
   ```

3. **Upcoming Assignments Transformation:**
   ```php
   // TODO: Use actual assignment data from services
   // Current: Mock data matching course structure
   $upcomingAssignments = [
       [
           'title' => 'Network Security Assessment',
           'course' => 'Advanced Network Security', // From debug course data
           'dueDate' => '2025-09-15',
           'type' => 'assignment'
       ]
   ];
   ```

## Implementation Status

### ✅ Completed
- [x] Created React API endpoints in `StudentDashboardController`
- [x] Added routes in `routes/frontend/student.php`
- [x] Updated React component to use real API calls
- [x] Removed mock data from React component
- [x] Interface matching between backend and frontend

### 🚧 In Progress 
- [ ] Transform debug data into proper React format
- [ ] Calculate real metrics from debug data

### 📋 TODO
- [ ] Connect lesson data from classroom debug endpoint
- [ ] Calculate completed lessons from actual course progress  
- [ ] Calculate assignments due from actual assignment data
- [ ] Calculate hours learned from actual time tracking
- [ ] Add error handling for failed API calls in React
- [ ] Add loading states for better UX

## Testing

### Debug Endpoints (Current)
- ✅ `/classroom/debug/student` - Returns raw student + courseAuth data
- ✅ `/classroom/debug/class` - Returns instructors + courseDates
- ✅ `/classroom/debug` - Returns complete debug data

### React API Endpoints (New)
- ✅ `/classroom/api/stats` - Returns StudentStats format
- ✅ `/classroom/api/recent-lessons` - Returns RecentLesson[] format  
- ✅ `/classroom/api/upcoming-assignments` - Returns UpcomingAssignment[] format

### React Component
- ✅ Uses real API calls instead of mock data
- ✅ Proper error handling for fetch failures
- ✅ Loading states maintained with React Query

## Next Steps

1. **Data Integration**: Connect React API endpoints to actual debug data
2. **Metrics Calculation**: Implement real calculation logic for stats
3. **Service Enhancement**: Expand services to provide lesson/assignment data
4. **Testing**: Test full data flow from services → API → React UI
5. **Performance**: Add caching for frequently accessed data

## Notes

- Debug endpoints remain unchanged for development/testing
- React API endpoints provide production-ready data format
- All endpoints require authentication (`auth` middleware)
- React Query handles caching and error states automatically
- API responses follow REST conventions with proper HTTP status codes
