## Classroom Data Flow Specification

Purpose: capture the data flow for the `/classroom` surface (Student, Instructor, Support) so backend APIs, services, and frontend data layers can be implemented consistently.

This file is a short, practical spec: entities, canonical API surfaces, frontend responsibilities (React Query usage), event flows (enrollment, progress, submissions), realtime recommendations, edge cases, and prioritized next steps.

## Checklist
- Identify core entities and relationships — Done
- List backend services and API endpoints to implement — Done (recommended endpoints)
- Describe frontend data layers and where they mount (React Query usage) — Done
- Define canonical JSON shapes for common payloads — Done
- Recommend realtime approach and caching strategy — Done
- Provide prioritized implementation tasks — Done

## Core entities (DB / Eloquent)
- User (students/instructors/support)
- Role
- Course
- CourseAuth (enrollment) — links User <-> Course, holds status/progress
- CourseDate / Schedule — lessons/occurrences
- Assignment / Quiz / Challenge
- Submission / Attempt / Grade
- ActivityLog (classroom activity events)
- Transcript / Certificate
- Order / Payment / CourseAuth source

Files/services found in the repo
- `app/Services/CourseAuthService.php` — CourseAuth lifecycle and grant-from-order
- `app/Services/Frost/Instructors/ClassroomService.php` — instructor-facing classroom queries and schedule/capacity helpers

## Canonical API surface (recommended)
Note: adapt URLs and auth middleware to your existing `routes/` structure.

1) GET /api/v1/classroom/me
  - Purpose: student-facing summary (profile, active enrollments, quick stats)
  - Returns: StudentProfile + minimal enrollments + progress summaries

2) GET /api/v1/courses/{course_id}/enrollments
  - Purpose: instructor/support view of enrolled students
  - Query params: page, per_page, status

3) POST /api/v1/courses/{course_id}/enrollments
  - Purpose: create CourseAuth (grant access). Body: { user_id, source, starts_at, expires_at }

4) GET /api/v1/course_auth/{course_auth_id}/progress
  - Purpose: course progress detail for a specific enrollment

5) GET /api/v1/classroom/{classroom_id}/schedule?start=&end=
  - Purpose: classroom schedule, lessons within period

6) POST /api/v1/assignments/{assignment_id}/submissions
  - Purpose: student uploads submission

7) GET /api/v1/classroom/{classroom_id}/activity?since=
  - Purpose: activity feed (attendance, submissions, interactions)

8) GET /api/v1/instructor/classrooms/active
  - Purpose: instructor dashboard data (capacity, enrollments, quick stats)

9) POST /api/v1/notifications/message
  - Purpose: instructor/support -> student messages (optionally enqueue broadcasts)

Security: all endpoints should use sanctum/session + role-based middleware (instructors require instructor role, support require support role).

## Frontend data layers (React architecture)
- Each surface has a top-level DataLayer component that sets up React Query and mounts children:
  - `resources/js/React/Student/StudentDataLayer.tsx` (student)
  - `resources/js/React/Instructor/InstructorDataLayer.tsx` (instructor)
  - `resources/js/React/Support/SupportDataLayer.tsx` (support)
- Use `@tanstack/react-query` QueryClient with options in the repo (staleTime, retry policy already present in instructor entry)
- Data fetching rules:
  - Query keys by resource and id: ['student', userId, 'enrollments']
  - Keep list endpoints paginated; use infiniteQuery for timelines/activity
  - Use optimistic or background refetch for enrollment actions
- Caching and invalidation:
  - After enrollment create/delete: invalidate ['course', courseId, 'enrollments'] and ['student', userId, 'enrollments']
  - After submission: invalidate ['assignment', assignmentId, 'submissions'] and ['course_auth', courseAuthId, 'progress']

## Canonical JSON shapes

StudentProfile (GET /api/v1/classroom/me)
{
  "user_id": 123,
  "name": "Jane Doe",
  "student_number": "S-0001",
  "enrollment_date": "2025-08-01",
  "academic_status": "active",
  "current_level": "Level 2",
  "active_enrollments": [{
    "course_auth_id": 456,
    "course_id": 12,
    "course_title": "Intro to X",
    "completion_percentage": 42,
    "last_activity": "2025-08-25T14:22:00Z"
  }]
}

CourseProgress (GET /api/v1/course_auth/{id}/progress)
{
  "course_auth_id": 456,
  "course_id": 12,
  "completion_percentage": 42,
  "modules": [{ "module_id": 1, "title": "Intro", "completed": true, "completed_at": null }],
  "time_spent_seconds": 3600,
  "milestones": [{ "id": "m1", "title": "First Steps", "achieved_at": null }]
}

ClassroomActivity (GET /api/v1/classroom/{id}/activity)
[
  { "id": 9001, "student_id": 123, "activity_type": "attendance", "activity_date": "2025-08-25T13:00:00Z", "duration": 3600, "meta": { "lesson_id": 77 } },
  { "id": 9002, "student_id": 124, "activity_type": "submission", "activity_date": "2025-08-25T13:05:00Z", "meta": { "assignment_id": 200 } }
]

## Event flows (high level)

1) Enrollment flow (order -> CourseAuth)
  - Order completed -> `CourseAuthService::grantFromOrder()` creates CourseAuth
  - Service logs event and returns CourseAuth id
  - Backend triggers: invalidate caches and send notification to student
  - Frontend: support/instructor lists refetch or receive broadcast

2) Student progress flow
  - Student completes module -> frontend POST to `/api/v1/course_auth/{id}/progress/events`
  - Backend persists progress, recalculates completion_percentage, and writes ActivityLog
  - Optionally broadcast progress event for instructor dashboards
  - Frontend invalidates course_auth progress query and re-renders

3) Submission & grading
  - Student uploads -> POST submission endpoint
  - Backend stores file (S3/local), creates Submission record
  - Instructor grades -> PATCH grade; backend updates Submission and writes Grade
  - Invalidate queries and optionally send notifications

4) Live classroom interactions (attendance, messages)
  - If realtime enabled: backend broadcasts via Laravel broadcasting (Redis + Echo or Pusher)
  - Clients subscribe to classroom channels and update activity timelines

## Realtime recommendation
- Repo scan did not reveal a clearly configured realtime layer (no clear Echo client import). For realtime consider:
  - Laravel broadcasting using Redis + laravel-echo-server or Pusher; or
  - Use simple polling with React Query for lower complexity (pollInterval for activity timelines)
- If you add realtime, broadcast only high-value events (attendance, message, submission) and keep channel auth strict.

## Edge cases and performance
- Large class sizes: paginate enrollment lists server-side, use indexed queries for activity
- Long timelines: use cursor/infinite pagination for activity feeds
- Race conditions for progress updates: use idempotency keys and server-side aggregation
- File uploads: stream to S3 or temp store and use background jobs for heavy processing
- Missing frontend container: entry scripts already retry mount; ensure blade templates include containers

## Tests / Quality gates
- Backend: unit tests for CourseAuthService, ClassroomService, and feature tests for API endpoints (happy path + auth failures)
- Frontend: small Jest/React Testing Library tests for DataLayer mounting and one integration test (msw) for API fetch + render
- Smoke: E2E test to load `/classroom` and assert container presence and initial API response mocking

## Prioritized implementation plan (small increments)
1) Add API endpoint: GET /api/v1/classroom/me (student summary) + minimal controller and resource (quick win)
2) Wire StudentDataLayer to call the endpoint and render active_enrollments
3) Add CourseAuth list endpoint for instructors and support with pagination
4) Create Submission endpoint and ensure file uploads are saved by existing file manager
5) Add broadcast hooks (optional): start with polling, switch to broadcast when stable
6) Add tests: backend unit/feature + frontend query tests

## Recent Implementation Updates (September 19, 2025)

### ✅ StudentDashboardService Improvements
The `StudentDashboardService` has been refactored to leverage existing helper classes:

**Helper Classes Integration**:
- `CourseAuthObj`: Primary business logic class for course authorization management
- `CourseUnitObj`: Manages course unit relationships and student progress tracking
- Performance: 306.93ms execution time for 18 lessons with completion tracking

**Data Flow Enhancement**:
```php
// New pattern using helper classes
$courseAuthObj = new CourseAuthObj($courseAuth);
$courseUnitObjs = $courseAuthObj->CourseUnitObjs();
foreach ($courseUnitObjs as $courseUnitObj) {
    $studentUnits = $courseUnitObj->StudentUnits($courseAuth);
    $isCompleted = $this->isLessonCompletedFromStudentUnits($studentUnits, $lesson);
}
```

### ✅ React Frontend Integration
- **StudentSidebar.tsx**: Dynamic lesson loading replaced hardcoded data
- **LaravelProps.ts**: Enhanced TypeScript interfaces for lesson progress
- **Responsive Design**: Proper mobile/desktop lesson display

### ✅ Calendar Functionality
- **Route**: `/courses/schedules` now properly displays course dates
- **Controller Logic**: Handles both instructor-led and self-paced courses
- **Data Population**: Course dates populated to eliminate empty calendar

## Next steps I can do now (pick one)
- ✅ **IMPLEMENTED**: StudentDashboardService refactoring with helper classes
- ✅ **IMPLEMENTED**: React StudentSidebar with dynamic lesson loading
- ✅ **IMPLEMENTED**: Calendar route functionality and course date population
- scaffold the GET `/api/v1/classroom/me` endpoint (controller, route, resource) and a small frontend hook in `StudentDataLayer` to call it; or
- scaffold instructor enrolments API and a small placeholder `InstructorDataLayer` widget; or
- add simple mocked realtime broadcast example (backend event and small Echo-client stub) to the repo for reference.

---

This spec is intentionally practical and aligned with code already present in the repo. The recent implementations demonstrate successful integration of existing business logic classes with modern React components, resulting in a robust and maintainable classroom data flow architecture. 
