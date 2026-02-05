# Student Poll API Documentation

## Endpoint

```
GET /student/poll
```

## Purpose

Returns **student-specific data** for a single course enrollment including validation status, active classroom participation, and challenge history. This poll shows what the student is doing in their current enrollment.

**Data Scope:** Personal student data for ONE enrollment (scoped by course_auth_id).

**Important:** This poll is scoped to a specific enrollment. The student's course list/dashboard is handled separately. For lesson content and classroom structure, use the Classroom Poll.

## Authentication

Requires authenticated user session (Laravel Sanctum/Session).

## Request Parameters

| Parameter        | Type    | Required | Description                                       |
| ---------------- | ------- | -------- | ------------------------------------------------- |
| `course_auth_id` | Integer | Yes      | The student's enrollment ID for a specific course |

## Response Structure

### Success Response (200 OK)

**⚠️ CURRENT STATE vs TARGET STATE:**

- ✅ **Currently Implemented:** `student`, `validations`, `active_classroom`, `challenges`
- 🚧 **Target Structure (NOT YET IMPLEMENTED):** `courseAuth`, `studentUnit`, `studentLessons`
- ⚠️ **DEPRECATED (will be removed):** `courses`, `progress`, `validations_by_course_auth`, `notifications`, `assignments`

```json
{
    "success": true,
    "data": {
        "student": { ... },                      // ✅ IMPLEMENTED
        "courseAuth": { ... },                   // 🚧 NOT YET IMPLEMENTED
        "studentUnit": { ... },                  // 🚧 NOT YET IMPLEMENTED
        "studentLessons": [ ... ],               // 🚧 NOT YET IMPLEMENTED
        "validations": { ... },                  // 🚧 NOT YET IMPLEMENTED
        "active_classroom": { ... },             // ✅ IMPLEMENTED
        "challenges": [ ... ],                   // ✅ IMPLEMENTED

        // ⚠️ DEPRECATED - Currently returned but will be removed:
        "courses": [ ... ],                      // Use courseAuth instead
        "progress": { ... },                     // Use courseAuth.progress_percentage instead
        "validations_by_course_auth": { ... },   // Use validations instead
        "notifications": [ ... ],                // Empty array, will be removed
        "assignments": [ ... ]                   // Empty array, will be removed
    }
}
```

## Payload Details

### `student` (Object)

**Purpose:** Authenticated user's basic information

```json
{
    "id": 2,
    "fname": "Richard",
    "lname": "Clark",
    "email": "richievc@gmail.com",
    "avatar": "https://frost.test/storage/avatars/richard_clark.jpg",
    "role_id": 5,
    "is_active": true,
    "student_info": {
        "phone": "+1-555-0123",
        "emergency_contact": "Jane Clark"
    }
}
```

| Field          | Type         | Description                                          |
| -------------- | ------------ | ---------------------------------------------------- |
| `id`           | Integer      | User's unique ID                                     |
| `fname`        | String       | User's first name                                    |
| `lname`        | String       | User's last name                                     |
| `email`        | String       | User's email address                                 |
| `avatar`       | String\|null | Profile picture URL (null if not uploaded)           |
| `role_id`      | Integer      | User role: 5 = student, 4 = instructor, 1 = admin    |
| `is_active`    | Boolean      | Account active status                                |
| `student_info` | Object\|null | Additional student metadata (phone, emergency, etc.) |

---

### `courseAuth` (Object)

**🚧 STATUS: NOT YET IMPLEMENTED** - This field is not currently returned by the API. Use `courses` array for now (see deprecated section).

**Purpose:** Student's enrollment information for this course

```json
{
    "id": 456,
    "user_id": 123,
    "course_id": 789,
    "status": "active",
    "enrolled_at": "2026-01-15T10:30:00Z",
    "completed_at": null,
    "agreed_at": "2026-01-15T10:35:00Z",
    "rules_accepted": true,
    "progress_percentage": 45
}
```

| Field                 | Type           | Description                                         |
| --------------------- | -------------- | --------------------------------------------------- |
| `id`                  | Integer        | CourseAuth ID (enrollment record)                   |
| `user_id`             | Integer        | Student's user ID                                   |
| `course_id`           | Integer        | Course being taken                                  |
| `status`              | String         | Enrollment status: `active`, `completed`, `expired` |
| `enrolled_at`         | ISO 8601       | When student enrolled                               |
| `completed_at`        | ISO 8601\|null | When student completed course (null if in progress) |
| `agreed_at`           | ISO 8601\|null | When student accepted terms                         |
| `rules_accepted`      | Boolean        | Whether student accepted classroom rules            |
| `progress_percentage` | Integer        | Completion percentage (0-100)                       |

---

### `studentUnit` (Object | null)

**🚧 STATUS: NOT YET IMPLEMENTED** - This field is not currently returned by the API in the student poll endpoint.
**Purpose:** Student's participation record for the current classroom session

```json
{
    "id": 100782,
    "course_auth_id": 456,
    "inst_unit_id": 67890,
    "course_date_id": 12345,
    "rules_accepted": true,
    "onboarding_completed": true,
    "joined_at": "2026-02-04T10:05:00Z"
}
```

```json
null // No active classroom session or student hasn't joined yet
```

| Field                  | Type           | Description                             |
| ---------------------- | -------------- | --------------------------------------- |
| `id`                   | Integer        | StudentUnit ID                          |
| `course_auth_id`       | Integer        | Links to student's enrollment           |
| `inst_unit_id`         | Integer        | Links to instructor's classroom session |
| `course_date_id`       | Integer        | Links to scheduled class date           |
| `rules_accepted`       | Boolean        | Classroom rules acknowledged            |
| `onboarding_completed` | Boolean        | Classroom onboarding finished           |
| `joined_at`            | ISO 8601\|null | When student joined this classroom      |

---

### `studentLessons` (Array of Objects)

**🚧 STATUS: NOT YET IMPLEMENTED** - This field is not currently returned by the API in the student poll endpoint.

**Purpose:** Lessons the student is taking or has completed in this course

```json
[
    {
        "id": 5432,
        "student_unit_id": 100782,
        "lesson_id": 101,
        "lesson_title": "Introduction to Security Concepts",
        "status": "active",
        "started_at": "2026-02-04T10:30:00Z",
        "completed_at": null,
        "paused_at": null,
        "progress_percentage": 35
    },
    {
        "id": 5431,
        "student_unit_id": 100782,
        "lesson_id": 100,
        "lesson_title": "Course Overview",
        "status": "completed",
        "started_at": "2026-02-04T10:05:00Z",
        "completed_at": "2026-02-04T10:25:00Z",
        "paused_at": null,
        "progress_percentage": 100
    }
]
```

| Field                 | Type           | Description                                    |
| --------------------- | -------------- | ---------------------------------------------- |
| `id`                  | Integer        | StudentLesson ID                               |
| `student_unit_id`     | Integer        | Links to student's classroom participation     |
| `lesson_id`           | Integer        | Which lesson this attempt is for               |
| `lesson_title`        | String         | Lesson name for display                        |
| `status`              | String         | `not_started`, `active`, `paused`, `completed` |
| `started_at`          | ISO 8601\|null | When student started this lesson               |
| `completed_at`        | ISO 8601\|null | When student completed this lesson             |
| `paused_at`           | ISO 8601\|null | When lesson was paused (if applicable)         |
| `progress_percentage` | Integer        | Student's progress on this lesson (0-100)      |

**Status Values:**

- `not_started`: Student hasn't started this lesson yet
- `active`: Student currently working on this lesson
- `paused`: Lesson in progress but temporarily paused
- `completed`: Student finished this lesson

---

### `validations` (Object)

**✅ STATUS: IMPLEMENTED** - This field is returned by the API and manages onboarding status.

**Purpose:** Identity verification and onboarding status for this course enrollment (ID card, headshot photos, and onboarding completion)

```json
{
    "idcard": "https://frost.test/storage/media/validations/idcards/2_richard_clark.png",
    "headshot": {
        "thursday": "https://frost.test/storage/validations/headshots/673cb5b7-65df-4012-9ab5-1404045387ca.jpg"
    },
    "idcard_status": "approved",
    "headshot_status": "approved",
    "message": null,
    "terms_accepted": true,
    "rules_accepted": true,
    "identity_verified": true,
    "onboarding_completed": true
}
```

| Field                  | Type         | Description                                             |
| ---------------------- | ------------ | ------------------------------------------------------- |
| `idcard`               | String\|null | URL to uploaded ID card image                           |
| `headshot`             | Object       | Headshot photos by day of week                          |
| `idcard_status`        | String       | `missing`, `pending`, `approved`, `rejected`            |
| `headshot_status`      | String       | `missing`, `pending`, `approved`, `rejected`, `partial` |
| `message`              | String\|null | Admin feedback or rejection reason                      |
| `terms_accepted`       | Boolean      | Student accepted course terms                           |
| `rules_accepted`       | Boolean      | Student accepted classroom rules                        |
| `identity_verified`    | Boolean      | Both ID card and headshot uploaded                      |
| `onboarding_completed` | Boolean      | All onboarding requirements met                         |

**Note:** Headshot object contains day-of-week keys (monday, tuesday, wednesday, thursday, friday, saturday, sunday) with photo URLs or null values.

**Onboarding Management:** The validations object is the single source of truth for onboarding status. All onboarding requirements (terms, rules, identity) are tracked here.

---

### `active_classroom` (Object | null)

**Purpose:** Current classroom session the student is participating in

```json
{
    "status": "active",
    "course_id": 1,
    "course_date_id": 10773,
    "inst_unit_id": 10686
}
```

```json
null // Student not currently in an active classroom
```

| Field            | Type    | Description                                  |
| ---------------- | ------- | -------------------------------------------- |
| `status`         | String  | `active` (classroom is live with instructor) |
| `course_id`      | Integer | Course template ID                           |
| `course_date_id` | Integer | Scheduled class session ID                   |
| `inst_unit_id`   | Integer | Instructor's classroom session ID            |

**Note:** This indicates which enrollment (matched by `course_id` and `course_date_id`) is currently active in a classroom.

---

### `challenges` (Array)

**Purpose:** Active participation challenges (attention checks) for the student

```json
[
    {
        "id": 9876,
        "student_lesson_id": 5432,
        "lesson_id": 101,
        "created_at": "2026-02-04T14:30:00Z",
        "expires_at": "2026-02-04T14:45:00Z",
        "completed_at": "2026-02-04T14:32:15Z",
        "failed_at": null,
        "is_final": false,
        "response_time_seconds": 135
    },
    {
        "id": 9875,
        "student_lesson_id": 5431,
        "lesson_id": 100,
        "created_at": "2026-02-04T13:15:00Z",
        "expires_at": "2026-02-04T13:30:00Z",
        "completed_at": null,
        "failed_at": "2026-02-04T13:30:00Z",
        "is_final": true,
        "response_time_seconds": null
    }
]
```

| Field               | Type           | Description                                |
| ------------------- | -------------- | ------------------------------------------ |
| `id`                | Integer        | Challenge ID                               |
| `student_lesson_id` | Integer        | Which lesson attempt this challenge is for |
| `lesson_id`         | Integer        | Lesson being challenged                    |
| `created_at`        | ISO 8601       | When challenge was issued                  |
| `expires_at`        | ISO 8601       | Challenge deadline                         |
| `completed_at`      | ISO 8601\|null | When student responded successfully        |

```json
[]
```

**Note:** Currently returns empty array. When the Challenger class creates challenges during lessons, they appear here for the student to respond to.

---

## Error Response (500 Internal Server Error)

```json
{
    "success": false,
    "error": "Error message description"
}
```

## Polling Behavior

**Recommended Interval:** 10 seconds

**Use Case:**

- Track single enrollment's classroom participation
- Monitor identity verification status
- Real-time notification of challenges
- Display current enrollment activity

## Data Ownership

All data in this poll is **student-specific** and **scoped to one enrollment**:

- ✅ Different for each student
- ✅ Scoped by course_auth_id parameter
- ✅ Shows validation status for this enrollment
- ✅ Shows if this enrollment is in an active classroom
- ✅ Safe to display in student's personal UI

## Data Separation

**Student Poll (this endpoint):**

- Student's validation status for ONE enrollment
- Active classroom participation for this enrollment
- Challenge history for this enrollment
- Personal student-specific data

**Classroom Poll:**

- Course offerings and lesson content
- Instructor actions and state
- Classroom configuration
- Shared data for all students in that classroom

## Related Endpoints

- **Classroom Poll:** `GET /classroom/class/data?course_date_id={id}` - Get shared classroom data
- **Challenge Response:** `POST /classroom/challenge/respond` - Respond to active challenge

## Examples

### Example 1: Student in active classroom with approved validations

```json
{
    "success": true,
    "data": {
        "student": {
            "id": 2,
            "fname": "Richard",
            "lname": "Clark",
            "email": "richievc@gmail.com",
            "avatar": "https://frost.test/storage/avatars/richard_clark.jpg",
            "role_id": 5,
            "is_active": true,
            "student_info": {
                "fname": "Richard",
                "middle_initial": "J",
                "lname": "Clark",
                "email": "richievc@gmail.com",
                "suffix": null,
                "dob": "1990-05-15",
                "phone": "+1-555-0123"
            }
        },
        "courseAuth": {
            "id": 456,
            "user_id": 2,
            "course_id": 1,
            "status": "active",
            "enrolled_at": "2026-01-15T10:30:00Z",
            "completed_at": null,
            "agreed_at": "2023-10-11T22:38:12.424725Z",
            "rules_accepted": true,
            "progress_percentage": 45
        },
        "studentUnit": {
            "id": 100782,
            "course_auth_id": 456,
            "inst_unit_id": 10686,
            "course_date_id": 10773,
            "rules_accepted": true,
            "onboarding_completed": true,
            "joined_at": "2026-02-04T10:05:00Z"
        },
        "studentLessons": [
            {
                "id": 5432,
                "student_unit_id": 100782,
                "lesson_id": 101,
                "lesson_title": "Introduction to Security Concepts",
                "status": "active",
                "started_at": "2026-02-04T10:30:00Z",
                "completed_at": null,
                "paused_at": null,
                "progress_percentage": 35
            },
            {
                "id": 5431,
                "student_unit_id": 100782,
                "lesson_id": 100,
                "lesson_title": "Course Overview",
                "status": "completed",
                "started_at": "2026-02-04T10:05:00Z",
                "completed_at": "2026-02-04T10:25:00Z",
                "paused_at": null,
                "progress_percentage": 100
            }
        ],
        "validations": {
            "idcard": "https://frost.test/storage/media/validations/idcards/2_richard_clark.png",
            "headshot": {
                "thursday": "https://frost.test/storage/validations/headshots/673cb5b7.jpg"
            },
            "idcard_status": "approved",
            "headshot_status": "approved",
            "message": null
        },
        "active_classroom": {
            "status": "active",
            "course_id": 1,
            "course_date_id": 10773,
            "inst_unit_id": 10686
        },
        "challenges": []
    }
}
```

### Example 2: Student with no active classroom and missing validations

```json
{
    "success": true,
    "data": {
        "student": {
            "id": 5,
            "fname": "Jane",
            "lname": "Smith",
            "email": "jane@example.com",
            "avatar": null,
            "role_id": 5,
            "is_active": true,
            "student_info": {
                "fname": "Jane",
                "middle_initial": null,
                "lname": "Smith",
                "email": "jane@example.com",
                "suffix": null,
                "dob": "1995-03-20",
                "phone": "+1-555-9876"
            }
        },
        "courseAuth": {
            "id": 1001,
            "user_id": 5,
            "course_id": 2,
            "status": "active",
            "enrolled_at": "2026-01-20T09:00:00Z",
            "completed_at": null,
            "agreed_at": "2026-01-20T09:05:00Z",
            "rules_accepted": true,
            "progress_percentage": 0
        },
        "studentUnit": null,
        "studentLessons": [],
        "validations": {
            "idcard": null,
            "headshot": {
                "thursday": null
            },
            "idcard_status": "missing",
            "headshot_status": "missing",
            "message": null
        },
        "active_classroom": null,
        "challenges": []
    }
}
```

## Implementation Notes

1. **Scoped by Enrollment:** This endpoint requires `course_auth_id` parameter - returns data only for that specific enrollment
2. **Active Classroom:** The `active_classroom` field indicates if this enrollment is currently in a live classroom session
3. **Validation Display:** Single `validations` object shows ID card and headshot status for this enrollment
4. **Challenges:** When active, Challenger class creates participation checks that appear in the `challenges` array
5. **Course List:** Student's full enrollment list/dashboard is handled by a separate endpoint - this poll is for one active enrollment only

---

## ⚠️ DEPRECATED FIELDS (Currently Returned, Will Be Removed)

The following fields are **currently returned by the API** but are **deprecated** and will be removed in the future. Do not rely on these in new code.

### `courses` (Array) - DEPRECATED

**Currently returned as:** Array of all student enrollments

**Replace with:** `courseAuth` object (when implemented) - will provide single enrollment data

**Example current structure:**

```json
"courses": [
    {
        "id": 2,
        "course_auth_id": 2,
        "course_date_id": 10773,
        "course_id": 1,
        "course_name": "Florida D40 (Dy)",
        "start_date": "2026-02-05",
        "agreed_at": "2023-10-11T22:38:12.424725Z",
        "status": "In Progress",
        "completion_status": "In Progress"
    }
]
```

**Why deprecated:** Poll should be scoped to ONE enrollment (via course_auth_id parameter), not return all enrollments.

---

### `progress` (Object) - DEPRECATED

**Currently returned as:**

```json
"progress": {
    "total_courses": 2,
    "completed": 0,
    "in_progress": 1
}
```

**Replace with:** `courseAuth.progress_percentage` (when implemented)

**Why deprecated:** Multi-course progress doesn't belong in a single-enrollment poll. Use courseAuth object for enrollment-specific progress.

---

### `validations_by_course_auth` (Object) - DEPRECATED

**Currently returned as:** Keyed by course_auth_id with nested validation data

**Replace with:** `validations` object (when implemented)

**Example current structure:**

```json
"validations_by_course_auth": {
    "2": {
        "idcard": "https://frost.test/storage/media/validations/idcards/2_richard_clark.png",
        "headshot": {
            "thursday": "https://frost.test/storage/validations/headshots/706b1fbf.jpg"
        },
        "idcard_status": "approved",
        "headshot_status": "uploaded",
        "message": null
    }
}
```

**Why deprecated:** Keyed object structure is unnecessary for single-enrollment poll. Use flat `validations` object instead.

---

### `notifications` (Array) - DEPRECATED

**Currently returned as:** Empty array `[]`

**Replace with:** Remove entirely - not used

**Why deprecated:** Always empty, no implementation, clutters response.

---

### `assignments` (Array) - DEPRECATED

**Currently returned as:** Empty array `[]`

**Replace with:** Remove entirely - not used

**Why deprecated:** Always empty, no implementation, clutters response.
