# 🔵 ACTIVE: Poll API Documentation Revision

**Date Started:** February 5, 2026  
**Status:** 🔄 IN PROGRESS  
**Priority:** HIGH - Architecture Documentation  
**Branch:** main

---

## Objective

**Correctly document Student Poll and Classroom Poll API payload structures** to reflect proper architectural separation between student-specific data and classroom-shared data.

### Business Requirements

- Student Poll: Returns what the **student IS DOING** (scoped to `course_auth_id`)
- Classroom Poll: Returns what **IS AVAILABLE** in the classroom (scoped to `course_date_id`)
- Clear separation prevents data leakage with 100+ students in same classroom

---

## Problem Identified

**Original Issue:** Student Poll API documentation showed incorrect multi-course payload structure.

### What Was Wrong

**Student Poll (`GET /student/poll`)** was documented with:

```json
{
  "student": {...},
  "courses": [...],  // ❌ Multiple courses
  "progress": {...},  // ❌ Aggregates
  "validations_by_course_auth": {...}  // ❌ Keyed by course_auth_id
}
```

**Issue:** This structure made it unclear that the endpoint is scoped to a **single enrollment**.

---

## Solution Implemented

### Phase 1: Student Poll API Documentation ✅

**File:** `docs/architecture/students/STUDENT_POLL_API.md`

**Changes Made:**

1. **Added `course_auth_id` parameter** (required)
    - Identifies which student enrollment to query
    - Scopes entire response to one `CourseAuth`

2. **Restructured payload** to single-enrollment scope:

```json
{
  "course_auth_id": 456,  // ✅ Single enrollment ID
  "student": {...},
  "courseAuth": {...},     // ✅ Single enrollment object
  "studentUnit": {...},    // ✅ Student's classroom participation
  "studentLessons": [...], // ✅ Student's lesson attempts
  "validations": {...},    // ✅ Single validations object
  "challenges": {...}      // ✅ Student's active challenge
}
```

3. **Removed incorrect fields:**
    - ❌ `courses` array
    - ❌ `progress` aggregates
    - ❌ `validations_by_course_auth` (keyed object)
    - ❌ `active_classroom` (moved to separate section)
    - ❌ `notifications` (not yet implemented)
    - ❌ `assignments` (not yet implemented)

4. **Added proper sections:**
    - Data Separation explanation
    - Implementation Notes for `course_auth_id` scoping
    - Two complete examples (active classroom vs no classroom)

**Result:** Student Poll now clearly documents student-specific actions and progress.

---

### Phase 2: Classroom Poll API Documentation 🔄

**File:** `docs/architecture/students/CLASSROOM_POLL_API.md`

**Current Status:** Payload structure restored but needs proper documentation sections.

**Payload Structure:**

```json
{
  "courseDate": {...},     // Scheduled class session
  "courses": {...},        // ⚠️ Needs documentation - what course info?
  "courseUnit": {...},     // Unit being taught today
  "instUnit": {...},       // Instructor session status
  "instLessons": {...},    // ⚠️ Needs documentation - all InstLesson records?
  "instructor": {...},     // Teacher info
  "lessons": [...],        // Available lessons with instructor status
  "modality": "online",    // Delivery mode
  "activeLesson": {...},   // Current lesson instructor is teaching
  "challenges": {...},     // ⚠️ Needs documentation - classroom challenge management
  "zoom": {...}            // Screen sharing info
}
```

**Tasks Remaining:**

1. ✅ Restore `courses`, `instLessons`, `challenges` fields to payload structure
2. ⏳ Document `courses` section
    - What course data is included?
    - Is this course catalog info or enrollment info?
3. ⏳ Document `instLessons` section
    - Array of all InstLesson records?
    - Relationship to `activeLesson`?
4. ⏳ Document `challenges` section
    - How instructor triggers challenges for classroom
    - Relationship to student-specific `challenge` in Student Poll
5. ⏳ Update examples to include new fields
6. ⏳ Update "What This Poll Does NOT Contain" to clarify challenges split

---

## Architecture Clarifications Needed

### Challenge Management Architecture

**Question:** How do challenges work between the two polls?

**User Clarification (Feb 5, 2026):**

> "The Classroom class manages the challenges that are popping up during the lesson, the student challenges, and the student history of the challenges they completed."

**Interpretation:**

- **Classroom Poll** (`challenges`):
    - Instructor triggers challenges for entire classroom
    - Shows which challenges are active/available
    - Classroom-level management
- **Student Poll** (`challenge`):
    - Specific challenge assigned to THIS student
    - Student's response status
    - Student's challenge history

**Backend Implementation Found:**

- `App\Classes\Challenger` - Challenge timing system
- `Challenge` model - Database records
- Automatic challenge creation based on:
    - `lesson_start_min` - Minimum time before first challenge
    - `lesson_random_min` / `lesson_random_max` - Random intervals
    - Failed challenge → Final challenge window
- Challenge types:
    - First challenge (beginning of lesson)
    - Random challenges (during lesson)
    - Final challenge (after failure)
    - EOL challenge (end of lesson)

**Still Needs Clarification:**

1. What exactly is in Classroom Poll `challenges` object?
2. Format/structure of the challenges data?
3. How does instructor trigger relate to student assignment?

### Classroom Ready State (Waiting Room)

**Key Discovery:** Classroom has 3 states based on poll data:

1. **OFFLINE** (No CourseDate):
    - No class scheduled
    - Student sees self-study mode
    - No classroom available

2. **WAITING/READY** (CourseDate exists, NO InstUnit):
    - Class scheduled for today
    - Instructor has NOT started class yet
    - Student sees "Waiting Room" with:
        - Class details (date, time, course name)
        - "Instructor is preparing to begin" message
        - Auto-refresh notification
        - Preparation checklist
    - **This is the "Ready State" - class is ready for instructor to claim**

3. **LIVE/ACTIVE** (CourseDate + InstUnit exist):
    - Instructor has started class (created InstUnit)
    - Student enters live classroom
    - Screen share, lessons, challenges active

**Key Code Patterns:**

```typescript
// Waiting state detection
if (courseDate && !instUnit) {
    return <WaitingRoom />
}

// Active state detection
if (courseDate && instUnit) {
    return <LiveClassroom />
}
```

**Status Messages:**

- Waiting: "Your instructor is preparing to begin."
- Live: "Your instructor has started the class."
- Scheduled: "This class is scheduled for today."

### Other Fields Needing Clarification

1. **`courses`** in Classroom Poll:
    - Course catalog information?
    - Just the one course for this classroom?
    - Full course details or summary?

2. **`instLessons`** in Classroom Poll:
    - All InstLesson records for this InstUnit?
    - Just completed ones?
    - How does it differ from `activeLesson`?

---

## Files Modified

### Completed ✅

1. `docs/architecture/students/STUDENT_POLL_API.md` - Complete rewrite
    - 11 sections updated
    - 5 replace operations
    - ~400 lines revised

### In Progress 🔄

2. `docs/architecture/students/CLASSROOM_POLL_API.md` - Partial update
    - Payload structure restored
    - Needs 3 new documentation sections
    - Needs example updates

---

## Testing Requirements

### Backend API Validation

- [ ] Verify Student Poll endpoint matches documented structure
- [ ] Verify Classroom Poll endpoint matches documented structure
- [ ] Test with 100+ students in same classroom
- [ ] Confirm no data leakage between students

### Frontend Integration

- [ ] Verify React components use correct poll for each data type
- [ ] Check student-specific UI uses Student Poll data
- [ ] Check classroom-shared UI uses Classroom Poll data
- [ ] Test challenge popup uses correct data source

---

## Architecture Principles

### Data Ownership Rules

**Student Poll (`GET /student/poll`):**

- ✅ Student enrollment (`courseAuth`)
- ✅ Student classroom participation (`studentUnit`)
- ✅ Student lesson attempts (`studentLessons`)
- ✅ Student validations
- ✅ Student's assigned challenge
- ✅ Student progress tracking

**Classroom Poll (`GET /classroom/class/data`):**

- ✅ Course schedule (`courseDate`)
- ✅ Unit being taught (`courseUnit`)
- ✅ Instructor session (`instUnit`)
- ✅ Available lessons with status
- ✅ Current active lesson
- ✅ Classroom challenge management
- ✅ Zoom meeting details

**Key Rule:** If 100 students in same classroom would see DIFFERENT values → belongs in Student Poll. If ALL students see SAME value → belongs in Classroom Poll.

---

## Next Steps

### Immediate (Today)

1. ✅ Get user clarification on `courses`, `instLessons`, `challenges` structure
2. ⏳ Document the 3 missing sections in Classroom Poll API
3. ⏳ Update examples to include new fields
4. ⏳ Review both documents for consistency

### Follow-up

1. Verify backend implementation matches documentation
2. Update any frontend components using incorrect poll
3. Create migration guide if API structure changed
4. Update role summaries to reference corrected APIs

---

## Contributors

**Developer:** GitHub Copilot + User  
**Documentation Review:** User  
**Date Range:** February 5, 2026 - In Progress  
**Status:** 🔄 **ACTIVE - Awaiting Clarifications**

---

## Related Documentation

- `docs/architecture/students/STUDENT_POLL_API.md` - Student-specific data
- `docs/architecture/students/CLASSROOM_POLL_API.md` - Classroom-shared data
- `docs/architecture/instructors/CLASSROOM_POLL_API.md` - Duplicate file? (needs review)
- `docs/students/STUDENTS_SUMMARY.md` - May need updates to reflect new payload structure
- `docs/instructors/INSTRUCTORS_SUMMARY.md` - May reference old structure

---

## Notes

- User moving from single-student testing to production with 100+ students per classroom
- Architecture bug would have caused each student to see different "shared" classroom data
- Proper scoping prevents data leakage and security issues
- Documentation now matches intended architecture
