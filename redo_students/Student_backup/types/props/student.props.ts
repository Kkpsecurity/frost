/**
 * Student Component Props
 * 
 * Following React Types & Interfaces Rules:
 * - type = single-dimension props or atomic aliases  
 * - interface = multi-field shapes (domain models)
 * - Props end with Props
 */

import type { 
    StudentId,
    UserId,
    CourseId,
    StudentType,
    UserType,
    CourseAuthType,
    StudentDashboardShape
} from "../students.types";

import type {
    AuthUserType,
    CourseAuthType as DashboardCourseAuthType
} from "../dashboard";

// =============================================================================
// COMPONENT PROPS (type)
// =============================================================================

export type StudentDashboardProps = {
    student: StudentType;
    courseAuths: CourseAuthType[];
    dashboardData: StudentDashboardShape;
};

export type StudentProfileProps = {
    student: StudentType;
    isEditable?: boolean;
    onUpdate?: (updates: Partial<StudentType>) => void;
};

export type StudentProgressProps = {
    studentId: StudentId;
    courseId: CourseId;
    progress: number;
    completedTasks?: number;
    totalTasks?: number;
    lastActivity?: string;
};

export type StudentCoursesProps = {
    studentId: StudentId;
    courseAuths: CourseAuthType[];
    showInactive?: boolean;
    onCourseSelect?: (courseId: CourseId) => void;
};

export type WelcomeCardProps = {
    student: StudentType;
    totalCourses: number;
    completedCourses: number;
    overallProgress: number;
};

export type CourseCardProps = {
    courseAuth: CourseAuthType;
    student: StudentType;
    showProgress?: boolean;
    showActions?: boolean;
    compact?: boolean;
    onEnter?: () => void;
    onViewDetails?: () => void;
};

export type StatusIndicatorProps = {
    status: ViewMode;
    lastUpdate?: string;
    showLabel?: boolean;
    animate?: boolean;
};

// =============================================================================
// EVENT HANDLER TYPES
// =============================================================================

export type StudentUpdateHandler = (updates: Partial<StudentType>) => void;
export type CourseSelectHandler = (courseId: CourseId) => void;

// =============================================================================
// UTILITY TYPES
// =============================================================================

export type ViewMode = 'ONLINE' | 'OFFLINE';
export type CourseFilter = 'all' | 'active' | 'completed' | 'expired';
export type CourseSort = 'date' | 'progress' | 'title' | 'status';
