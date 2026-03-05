/**
 * Classroom Domain Types & Props
 *
 * Following React Types & Interfaces Rules:
 * - type = single-dimension props or atomic aliases
 * - interface = multi-field shapes (domain models)
 * - Domain entities end with Type
 * - Props end with Props
 * - Shapes end with Shape
 */

// =============================================================================
// ATOMIC ALIASES (type)
// =============================================================================

export type ClassroomId = number;
export type InstructorId = number;
export type SessionStatus = "active" | "scheduled" | "completed" | "cancelled";
export type TimeZone = string;
export type MeetingUrl = string;
export type LessonStatus =
    | "incomplete"
    | "completed"
    | "active_live"
    | "active_fstb";
export type LessonId = number;

// =============================================================================
// POLL PAYLOAD TYPES (compat)
// =============================================================================

// The classroom poll payload is currently flexible and may vary by endpoint.
// Keep this broad to avoid blocking builds during incremental refactors.
export type ClassroomPollDataType = any;

export type ClassroomPollRequestParams = any;

// =============================================================================
// DOMAIN MODELS (interface)
// =============================================================================

/**
 * Lesson Domain Model
 * References: App\Models\Lesson
 * Database: lessons table
 */
export interface LessonType {
    id: LessonId;
    title: string;
    description: string;
    duration_minutes: number;
    order: number;
    status: LessonStatus;
    is_completed: boolean;
    is_active: boolean;
    is_paused?: boolean;
    paused_at?: string | null;
    video_url?: string | null; // S3 signed URL for video (null if no video)
}

/**
 * CourseDate Domain Model
 * References: App\Models\CourseDate
 * Database: course_dates table
 */
export interface CourseDateType {
    id: ClassroomId;
    course_id: number;
    instructor_id: InstructorId;
    start_date: string;
    end_date: string;
    start_time: string;
    end_time: string;
    timezone: TimeZone;
    location: string;
    status: SessionStatus;
    max_students: number;
    current_enrollment: number;
    meeting_link: MeetingUrl | null;
    course_title: string;
    created_at: string;
    updated_at: string;
}

// =============================================================================
// VIEW MODEL SHAPES (interface)
// =============================================================================

/**
 * Classroom Session Shape - Multi-field view model
 */
export interface ClassroomSessionShape {
    course_date: CourseDateType;
    instructor: import("./students.types").InstructorType;
    enrolled_students: import("./students.types").StudentType[];
    current_session?: {
        id: number;
        status: SessionStatus;
        started_at?: string;
        participants_count: number;
    };
}

/**
 * Classroom Dashboard Shape - Multi-field view model
 */
export interface ClassroomDashboardShape {
    sessions: ClassroomSessionShape[];
    upcoming_sessions: CourseDateType[];
    active_session?: ClassroomSessionShape;
}

// =============================================================================
// PROPS TYPES (type)
// =============================================================================

export type ClassroomSessionProps = {
    session: ClassroomSessionShape;
    onJoin?: () => void;
    onLeave?: () => void;
};

export type ClassroomListProps = {
    sessions: ClassroomSessionShape[];
    filter?: SessionStatus;
};

// =============================================================================
// HOOK TYPES (type)
// =============================================================================

export type UseClassroomDataOptions = {
    date?: string;
    isLive?: boolean;
    instructor_id?: InstructorId;
};

export type UseClassroomQueryResult<T> = {
    data: T | undefined;
    isLoading: boolean;
    error: Error | null;
    refetch: () => void;
};
export interface ClassroomPollOkShape<T = any> {
    success: true;
    data: T;
    timestamp?: string;
    message?: string;
}

export interface ClassroomPollErrShape {
    success: false;
    message: string;
    error_code?: string;
    timestamp?: string;
    error?: string;
}

export type ClassroomPollResponseShape<T = any> =
    | ClassroomPollOkShape<T>
    | ClassroomPollErrShape;

    export interface ClassroomPollActiveLessonShape {
    id: number; // inst_lesson id (based on current frontend logs)
    lesson_id: LessonId;
    is_paused: boolean;
    paused_at?: string | null;
    [key: string]: any;
}

export interface ClassroomPollBreaksShape {
    breaks_remaining?: number;
    break_duration_minutes?: number;
    [key: string]: any;
}

// Lessons in the classroom poll `lessons[]` array always have `id` = Lesson model id.
// The backend also emits `lesson_id` as an alias so consumers don't need a ternary.
// InstLesson / StudentLesson records use `lesson_id` as a FK; they do NOT have `id`
// referring to the lesson — their own PK is `id` (InstLesson id / StudentLesson id).
export interface ClassroomPollLessonShape {
    id: LessonId;        // Lesson model PK
    lesson_id: LessonId; // alias — always equals id
    title: string;
    duration_minutes?: number | null;
    [key: string]: any;
}

/**
 * Classroom poll payload — mirrors the `data` object from StudentDashboardController@getClassData.
 *
 * Two response variants exist (historic divergence):
 *
 * Variant A (getClassData — primary, used by useClassroomPoll):
 *   success: true, data: { courseDate, courseUnit, instUnit, instructor, lessons, activeLesson, zoom, challenge }
 *
 * Variant B (legacy classroom poll — some older code paths):
 *   success: true (top-level), courseDate, lessons, instUnit, studentUnit, studentLessons, challenge, config
 *
 * StudentDataLayer exposes `classroomPoll` = the `data` sub-object for Variant A.
 * For Variant B fields hoisted to top level, the context builder accesses them directly.
 */
export interface ClassroomPollPayloadShape {
    // --- CourseDate summary ---
    courseDate: {
        id: number;
        class_date: string | null;
        class_time: string | null;
        duration_minutes: number | null;
        starts_at?: string | null; // ISO, present on active_classroom entries
    } | null;

    // --- Course unit for today ---
    courseUnit: {
        id: number;
        name: string;
        day_number: number | null;
        course_id: number;
    } | null;

    // --- Instructor unit (null = instructor not started yet) ---
    instUnit: {
        id: number;
        status: 'active' | 'ended';
        started_at: string | null;
        completed_at: string | null;
        /** Raw inst_lessons array — each item has its own PK `id` and FK `lesson_id`. */
        inst_lessons?: Array<{
            id: number;         // InstLesson PK
            lesson_id: number;  // Lesson FK
            status?: string;
            is_paused: boolean;
            completed_at: string | null;
            [key: string]: any;
        }>;
    } | null;

    instructor: any | null;

    /**
     * Classroom-scoped lessons for today.
     * Each item has both `id` (Lesson PK) and `lesson_id` (alias).
     */
    lessons: ClassroomPollLessonShape[];

    /** Currently active (in-progress) instructor lesson, or null. */
    activeLesson: ClassroomPollActiveLessonShape | null;

    /** Break state for the active lesson. */
    breaks?: ClassroomPollBreaksShape;

    zoom?: any | null;

    /** Active participation challenge for this student, or null. */
    challenge: {
        challenge_id: number;
        student_lesson_id: number;
        is_final: boolean;
        is_eol: boolean;
        expires_at: string;
        time_remaining: number;
        created_at: string;
    } | null;

    // Flexible for back-compat with Variant B and future additions
    [key: string]: any;
}
