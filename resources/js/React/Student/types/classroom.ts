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
