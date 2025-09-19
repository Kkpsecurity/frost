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
};

export type SchoolDashboardTitleBarProps = {
    title: string;
    subtitle?: string;
    icon?: React.ReactNode;
    className?: string;
    onBackToDashboard?: () => void;
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
