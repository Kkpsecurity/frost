/**
 * Classroom Component Props
 *
 * Following React Types & Interfaces Rules:
 * - type = single-dimension props or atomic aliases
 * - interface = multi-field shapes (domain models)
 * - Props end with Props
 */

import type {
    UserId,
    StudentType,
    InstructorType,
    CourseAuthType,
} from "../students.types";

import type { CourseDateType, ClassroomSessionShape } from "../classroom";

// =============================================================================
// COMPONENT PROPS (type)
// =============================================================================

export type SchoolDashboardProps = {
    student: StudentType;
    instructor: InstructorType;
    courseAuths: CourseAuthType[];
    courseDates: CourseDateType[];
    onBackToDashboard?: () => void;
    lessons?: import("../LaravelProps").LessonsData;
    hasLessons?: boolean;
    validations?: any; // Include validation data from StudentDataArrayService
    studentAttendance?: StudentAttendanceSummary | null;
    instUnit?: any | null; // InstUnit data to determine if class is active
};

export type SchoolDashboardTitleBarProps = {
    title: string;
    subtitle?: string | React.ReactNode;
    icon?: React.ReactNode;
    className?: string;
    onBackToDashboard?: () => void;
    classroomStatus?: "ONLINE" | "WAITING" | "OFFLINE" | null;
    devModeToggle?: React.ReactNode;
};

export type SchoolNavBarProps = {
    student: StudentType;
    instructor: InstructorType;
    courseAuths: CourseAuthType[];
    courseDates: CourseDateType[];
    activeTab?: string;
    onTabChange?: (tabId: string) => void;
};

export type SchoolDashboardTabContentProps = {
    student: StudentType;
    instructor: InstructorType;
    courseAuths: CourseAuthType[];
    courseDates: CourseDateType[];
    activeTab?: string;
};

export type ClassroomControlsProps = {
    session: ClassroomSessionShape;
    isInstructor: boolean;
    onJoinSession?: () => void;
    onLeaveSession?: () => void;
    onStartRecording?: () => void;
    onStopRecording?: () => void;
};

export type StudentListProps = {
    students: StudentType[];
    currentUserId: UserId;
    showActions?: boolean;
    onStudentSelect?: (student: StudentType) => void;
};

export type CourseScheduleProps = {
    courseDates: CourseDateType[];
    selectedDate?: string;
    onDateSelect?: (date: string) => void;
    showTimeSlots?: boolean;
};

export type StudentAttendanceSummary = {
    is_present: boolean;
    entry_time: string | null;
    entry_time_relative: string | null;
    attendance_status: string;
    student_unit_id?: number;
    session_duration?: {
        formatted: string;
    };
};

// =============================================================================
// EVENT HANDLER TYPES
// =============================================================================

/**
 * Tab Navigation Handler
 */
export type TabChangeHandler = (tabId: string) => void;

/**
 * Session Action Handlers
 */
export type SessionActionHandler = () => void | Promise<void>;

/**
 * Student Selection Handler
 */
export type StudentSelectHandler = (student: StudentType) => void;

/**
 * Date Selection Handler
 */
export type DateSelectHandler = (date: string) => void;

// =============================================================================
// UTILITY TYPES
// =============================================================================

/**
 * Classroom Status Union Type
 */
export type ClassroomStatus = 'offline' | 'online' | 'scheduled' | 'maintenance';

/**
 * Session Status Union Type
 */
export type SessionStatus = 'scheduled' | 'active' | 'completed' | 'cancelled';

/**
 * Attendance Status Union Type
 */
export type AttendanceStatus = 'present' | 'absent' | 'late';

/**
 * Tab IDs Union Type
 */
export type TabId = 'overview' | 'schedule' | 'students' | 'materials' | 'settings';
