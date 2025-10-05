# Instructor Dashboard CourseDate-Focused Redesign

**Date Created:** October 5, 2025  
**Objective:** Redesign instructor dashboard to follow CourseDate-first flow with simplified teaching workflow  
**Priority:** High  
**Status:** Planning Phase

## Core Concept

The dashboard starts with **CourseDate** as the primary entity. If there are scheduled CourseDates for the instructor, show them so the instructor can actually teach. The flow depends on whether there's an active teaching session (InstUnit) or not.

## Entities (Simplified)

- **CourseDate** = one scheduled class instance (time, mode, location/join)
- **InstUnit** = the instructor's active teaching session for a specific CourseDate (created when they click "Teach Class"; ends when they click "End Class")

## High-Level Flow

1. **Load Instructor's CourseDates** (assigned to them, today + upcoming by default; include "Live" if within window)

2. **For each CourseDate:**
   - **IF NO InstUnit exists** for this CourseDate and instructor:
     - Show **Bulletin Board state**:
       - Class summary (title, time, mode, capacity)
       - CTA: "Teach Class"
   - **IF InstUnit EXISTS & is ACTIVE** for this CourseDate and instructor:
     - Skip Bulletin Board; open **Classroom view** directly

3. **Business Rule:** Only one ACTIVE InstUnit per instructor at a time. If another is active, prompt to end it before starting a new one.

## Two Main Views

### Bulletin Board (Pre-Teach)
**Purpose:** Quick prep + start teaching

**Show:**
- Course title
- Start/end time (local to class timezone)
- Online/Offline badge
- Location or join info
- Roster count

**Actions:**
- **Teach Class** → creates InstUnit and routes into Classroom
- **Preview Roster** (read-only)
- **Cancel Class** (if allowed) → marks CourseDate cancelled

### Classroom (Teaching Mode)
**Purpose:** Run the live session

**Auto-lands here** if an active InstUnit exists

**Must include:**
- Roster panel
- Attendance controls
- Join/share (if online)
- End-session control

**Actions:**
- **End Class** → closes InstUnit, returns to Bulletin Board list

## Online vs Offline Handling

### Online Classes:
- Show platform + join CTA on both Bulletin Board and Classroom
- Classroom exposes share/copy functionality

### Offline Classes:
- Show venue/room information
- Classroom focuses on attendance tools

## State Entry Rules

### "Teach Class" is enabled when:
- Now is within the allowed start window for that CourseDate (or policy permits early start)
- No other active InstUnit for this instructor

### Auto-route Logic:
- If instructor has one active InstUnit → go straight to that Classroom
- If multiple (shouldn't happen, but if it does) → force resolve by ending extra sessions

## Edge Cases (Minimal Handling)

1. **No CourseDates** → show empty-state message: "No classes to teach."
2. **CourseDate missing join/location info** → show "Not Ready" on Bulletin Board (Teach disabled)
3. **CourseDate cancelled** → show as "Cancelled" (no actions)

## Filters (Basic)

**Tabs:**
- **Live** (classes currently in session or within start window)
- **Today** (all today's classes)
- **Upcoming** (future classes)

**Default tab:** Live (if any), else Today

## Audit (Lightweight)

**Log when:**
- InstUnit is created (Teach Class clicked)
- InstUnit is ended (End Class clicked)

## Current Implementation Status

### ✅ What's Already Working:
- InstructorDashboard.tsx has bulletin board functionality restored
- CourseDate loading via useBulletinBoard() hook
- Auto-redirect logic for active classes
- ClassroomManager component for teaching mode

### 🔄 What Needs Updating:

#### 1. Data Structure Alignment
- Current: Uses complex bulletin board data with course acceptance workflow
- **Need:** Simple CourseDate list with InstUnit status

#### 2. Tab System Implementation
- **Add:** Live/Today/Upcoming tab filters
- **Update:** Default to Live tab logic

#### 3. Simplified Card Layout
- **Current:** Complex course cards with multiple actions
- **Need:** Simple cards showing:
  - Course title, time, mode badge
  - Single primary action: "Teach Class" or "Enter Classroom"
  - Status indicators: Ready/Not Ready/Cancelled

#### 4. InstUnit Management
- **Add:** Check for existing active InstUnit
- **Add:** Create InstUnit on "Teach Class"
- **Add:** Single active session enforcement

#### 5. Empty States
- **Update:** "No classes to teach" message
- **Add:** Tab-specific empty states

## Implementation Plan

### Phase 1: Data Layer Updates
1. **Update CourseDate API endpoint** to include:
   - InstUnit status (active/inactive)
   - Ready status (has required info)
   - Time window status (can start now?)

2. **Add InstUnit management endpoints:**
   - `POST /admin/instructors/instunit/create/{courseDateId}`
   - `POST /admin/instructors/instunit/end/{instUnitId}`
   - `GET /admin/instructors/instunit/active` (check for existing)

### Phase 2: Component Redesign
1. **Simplify InstructorDashboard.tsx:**
   - Remove complex bulletin board logic
   - Add tab system (Live/Today/Upcoming)
   - Implement auto-route for active InstUnit

2. **Update CourseCard component:**
   - Simplified layout focused on teaching action
   - Status badges (Online/Offline, Ready/Not Ready)
   - Single primary CTA

3. **Update state management:**
   - Remove course acceptance workflow
   - Add InstUnit creation/management
   - Add single active session enforcement

### Phase 3: UI/UX Polish
1. **Empty states for each tab**
2. **Loading states during InstUnit operations**
3. **Error handling for InstUnit conflicts**
4. **Time-based UI updates (auto-refresh for Live tab)**

## Success Criteria

1. ✅ Instructor sees their assigned CourseDates in simple list
2. ✅ Can click "Teach Class" to start teaching (creates InstUnit)
3. ✅ Auto-redirects to Classroom if active InstUnit exists
4. ✅ Only one active InstUnit per instructor enforced
5. ✅ Live/Today/Upcoming tabs work correctly
6. ✅ Online vs Offline classes handled appropriately
7. ✅ Empty states show helpful messages
8. ✅ InstUnit creation/ending logged for audit

## Files to Modify

### Frontend:
- `resources/js/React/Instructor/Components/InstructorDashboard.tsx` (main redesign)
- `resources/js/React/Instructor/Components/Offline/CourseCard.tsx` (simplify)
- `resources/js/React/Instructor/Components/Offline/useBulletinBoard.ts` (update data structure)

### Backend:
- `app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php` (add InstUnit methods)
- `app/Services/Frost/Instructors/CourseDatesService.php` (update CourseDate queries)
- `routes/admin/instructors.php` (add InstUnit routes)

### Database:
- Ensure InstUnit model and relationships are properly set up
- Add any missing indexes for performance

## Risk Assessment

### Low Risk:
- Tab system implementation
- UI simplification
- Empty state handling

### Medium Risk:
- InstUnit creation/management logic
- Single active session enforcement
- Auto-redirect logic updates

### High Risk:
- Data structure changes breaking existing functionality
- Time zone handling for class windows
- Race conditions in InstUnit creation

---

**Next Steps:**
1. Review and approve this task plan
2. Update data layer to support InstUnit status checking
3. Implement simplified CourseDate-first UI
4. Test teaching workflow end-to-end

**Last Updated:** October 5, 2025  
**Estimated Effort:** 2-3 days
