import React, { createContext, ReactNode } from "react";

/**
 * Student Context - Holds all student-related data from polling
 * Contains: user info, courses, progress, assignments, etc.
 */

export interface LessonPayloadEntry {
    id: number;
    lesson_id: number;
    is_completed?: boolean;
    status?: string;
    completed_at?: string | null;
    [key: string]: any;
}

export interface CourseAuthLessonPayload {
    course_auth_id: number;
    lessons: LessonPayloadEntry[];
}

export interface StudentExam {
    is_ready: boolean;
    next_attempt_at: string | null;
    missing_id_file: boolean;
    has_active_attempt: boolean;
    active_exam_auth_id: number | null;
    exam_id: number | null;
    num_questions: number | null;
    num_to_pass: number | null;
    policy_expire_seconds: number | null;
    has_previous_attempt: boolean;
    previous_exam_passed: boolean;
    previous_exam_score: string | null;
    previous_exam_completed_at: string | null;
}

export interface StudentContextType {
    student: {
        id: number;
        name: string;
        email: string;
        avatar?: string;
        role: string;
    } | null;
    courses: any[];
    progress: {
        total_courses: number;
        completed: number;
        in_progress: number;
    } | null;
    // Student progress (not classroom progress): validation/upload state per courseAuth.
    validationsByCourseAuth?: Record<number, any> | null;
    // Student-owned classroom participation + per-lesson completion (from student poll)
    studentUnit?: any | null;
    studentLessons?: any[];
    activeClassroom?: any | null;
    // Student-owned exam readiness/attempt (from student poll)
    studentExam?: StudentExam | null;
    // Student-owned exam readiness/attempt for all enrollments
    studentExamsByCourseAuth?: Record<number, StudentExam> | null;
    lessonsByCourseAuth?: Record<number, CourseAuthLessonPayload> | null;
    notifications: any[];
    assignments: any[];
    selectedCourseAuthId: number | null;
    setSelectedCourseAuthId: (id: number | null) => void;
    loading: boolean;
    error: string | null;
}

export const StudentContext = createContext<StudentContextType | undefined>(
    undefined,
);

export const StudentContextProvider: React.FC<{
    value: StudentContextType;
    children: ReactNode;
}> = ({ value, children }) => {
    return (
        <StudentContext.Provider value={value}>
            {children}
        </StudentContext.Provider>
    );
};

/**
 * Hook to use Student Context
 */
export const useStudent = () => {
    const context = React.useContext(StudentContext);
    if (!context) {
        throw new Error(
            "useStudent must be used within StudentContextProvider",
        );
    }
    return context;
};
