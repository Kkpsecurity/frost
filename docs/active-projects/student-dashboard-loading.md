# Student Dashboard Loading Rules & Baseline UI

## Overview

This document defines the loading behavior and baseline UI implementation for the Student Dashboard. The dashboard operates with a clear OFFLINE/ONLINE status detection mechanism based solely on DataLayer outputs.

## DataLayer Sources

### Student Payload
- **Source**: Laravel Props via `StudentDataLayer`
- **Content**: Existing student data + course authorizations
- **Fields Used**: 
  - `student` (user profile information)
  - `course_auths` (purchased course authorizations)

### Classroom Payload  
- **Source**: Laravel Props via `StudentDataLayer`
- **Content**: Existing classroom data (may be empty)
- **Fields Used**:
  - `instructor` (instructor information, if available)
  - `course_dates` (scheduled classroom sessions)

## Loading Rules

### Data Loading Sequence
1. **Load Student Data** from DataLayer
2. **Load Classroom Data** from DataLayer  
3. **Apply Status Detection** (no additional API calls)

### Status Detection Logic
- **OFFLINE**: `courseDates` is empty, missing, or has length = 0
- **ONLINE**: `courseDates` exists and contains one or more items

**Important**: No flags or synthetic fields are created client-side. Status is derived purely from `courseDates` presence.

```typescript
// Status detection implementation
const isClassroomOnline = course_dates && course_dates.length > 0;
const classroomStatus = isClassroomOnline ? 'ONLINE' : 'OFFLINE';
```

## UI Implementation

### Layout Structure
- **Standard Dashboard Layout**: Title bar + Left sidebar + Main content
- **Responsive Design**: Bootstrap-based, mobile-friendly
- **Component Architecture**: Single component with conditional rendering

### Title Bar
- **Page Title**: "Student Dashboard" + student name (if available)
- **Status Indicator**: ONLINE/OFFLINE badge based on classroom status
- **Minimal Controls**: Refresh button only (no additional features)

### Left Sidebar  
- **Navigation Items**: Derived from existing data only
  - Dashboard (always present)
  - My Courses (if `course_auths.length > 0`)
  - Live Classroom (if `isClassroomOnline = true`)
- **No Fake Entries**: Only show items when data exists

### Main Content Areas

#### OFFLINE State
When `courseDates` is empty or missing:
- **Primary Card**: "Classroom Offline" with appropriate messaging
- **Student Summary**: Display student profile information
- **Course Authorization Summary**: Show purchased courses count and recent purchases
- **Visual Indicators**: Offline icons and secondary color scheme

#### ONLINE State  
When `courseDates` contains items:
- **Primary Card**: "Live Classroom Session" with success styling
- **Instructor Information**: Display if `instructor` data exists
- **Scheduled Sessions**: List all items from `course_dates` array
- **Student Summary**: Same as offline state
- **Course Authorization Summary**: Same as offline state
- **Action Button**: "Join Classroom" (functional implementation not required)

## Data Constraints

### No Synthetic Fields
- **Server as Source of Truth**: Only render data returned by DataLayer
- **No Client-Side Synthesis**: Do not create, modify, or enhance data fields
- **Exact Rendering**: Display data exactly as provided by Laravel Props

### Empty State Handling
- **Graceful Degradation**: Handle missing or empty data appropriately
- **No Dummy Data**: Never show placeholder or fake content
- **Clear Messaging**: Inform users when data is unavailable

### Validation Rules
- **Required Fields**: Only `student` and `course_auths` are expected
- **Optional Fields**: `instructor` and `course_dates` may be empty
- **Fallback Behavior**: Show appropriate empty states for missing data

## Debug Information

### Development Mode Only
When `process.env.NODE_ENV === 'development'`:
- **Status Detection**: Show derived classroom status
- **Data Counts**: Display count of course_dates, course_auths, etc.
- **Loading Source**: Indicate Laravel Props as data source
- **Validation Status**: Show if data passed validation

### Production Mode
- **No Debug Output**: Debug information is completely hidden
- **Clean UI**: Only functional dashboard elements visible

## Technical Implementation

### Component Structure
```
StudentDashboard
├── Title Bar (status + controls)
├── Left Sidebar (dynamic navigation)
└── Main Content
    ├── ONLINE View (conditional)
    ├── OFFLINE View (conditional)  
    ├── Student Summary (always)
    ├── Course Auth Summary (always)
    └── Debug Info (development only)
```

### Status Flow
```
DataLayer → course_dates → Status Detection → UI Rendering
```

### Data Flow
```
Laravel Props → LaravelPropsReader → StudentDataLayer → StudentDashboard
```

## Acceptance Criteria

### ✅ Functional Requirements
- [ ] Dashboard correctly shows OFFLINE when `course_dates` is empty/missing
- [ ] Dashboard correctly shows ONLINE when `course_dates` contains items  
- [ ] Only existing DataLayer fields are displayed (no synthetic data)
- [ ] Empty states are handled gracefully without errors
- [ ] Student and course authorization data displays correctly
- [ ] Sidebar navigation reflects available data only

### ✅ Technical Requirements  
- [ ] No additional API calls beyond initial DataLayer load
- [ ] No client-side data manipulation or field creation
- [ ] Responsive design works on mobile and desktop
- [ ] Debug information only appears in development mode
- [ ] Component properly handles missing instructor/classroom data

### ✅ UX Requirements
- [ ] Clear visual distinction between ONLINE/OFFLINE states
- [ ] Appropriate messaging for empty states
- [ ] Intuitive navigation based on available features
- [ ] Professional dashboard appearance with proper styling

## Future Considerations

- **API Integration**: When live classroom features are implemented, the ONLINE state can be enhanced with real-time data
- **Interactivity**: Action buttons can be connected to functional endpoints
- **Personalization**: Additional user preferences can be integrated while maintaining the core loading rules
- **Performance**: Caching strategies can be applied to the DataLayer without changing the UI logic

---

**Last Updated**: September 11, 2025  
**Version**: 1.0  
**Status**: Implemented  
**Component**: `StudentDashboard.tsx`
