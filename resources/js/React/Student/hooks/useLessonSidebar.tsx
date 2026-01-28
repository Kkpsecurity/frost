import { useMemo } from 'react';
import { LessonType } from '../types/classroom';

interface UseLessonSidebarProps {
    lessons: LessonType[];
    studentLessons?: any[];
    activeLesson?: any;
}

interface UseLessonSidebarReturn {
    isLessonCompletedByStudent: (lessonId: number) => boolean;
    isLessonInProgress: (lessonId: number, index: number) => boolean;
    getLessonStatusColor: (lesson: any, index: number) => string;
    getLessonTextColor: (lesson: any, index: number) => string;
    getLessonStatusIcon: (lesson: any, index: number) => React.ReactNode;
}

/**
 * useLessonSidebar Hook
 *
 * Provides helper functions for rendering lesson sidebar UI
 * Reduces code duplication between MainOnline and MainOffline
 *
 * @param lessons - Array of lessons from classroom data
 * @param studentLessons - Array of student-specific lesson progress
 * @param activeLesson - Currently active lesson (if any)
 *
 * @example
 * const { isLessonCompletedByStudent, getLessonStatusColor } = useLessonSidebar({
 *   lessons: classroom.lessons,
 *   studentLessons: classroom.studentLessons,
 *   activeLesson: classroom.activeLesson
 * });
 */
export const useLessonSidebar = ({
    lessons,
    studentLessons = [],
    activeLesson = null,
}: UseLessonSidebarProps): UseLessonSidebarReturn => {

    /**
     * Check if a lesson is completed by THIS student
     */
    const isLessonCompletedByStudent = useMemo(() => {
        return (lessonId: number): boolean => {
            if (!studentLessons || studentLessons.length === 0) return false;

            const studentLesson = studentLessons.find(
                (sl: any) => (sl.lesson_id || sl.id) === lessonId
            );

            return studentLesson?.completed_at != null || studentLesson?.is_completed === true;
        };
    }, [studentLessons]);

    /**
     * Check if a lesson is in progress (active but not completed)
     */
    const isLessonInProgress = useMemo(() => {
        return (lessonId: number, index: number): boolean => {
            if (!lessons || lessons.length === 0) return false;

            const lesson = lessons.find((l: any) => (l.lesson_id || l.id) === lessonId);

            // Active lesson is in progress if not completed
            if (activeLesson && (activeLesson.lesson_id || activeLesson.id) === lessonId) {
                return !isLessonCompletedByStudent(lessonId);
            }

            // Check if lesson is active but not completed
            if (lesson?.is_active === true && !lesson.completed_at) {
                return true;
            }

            return false;
        };
    }, [lessons, activeLesson, isLessonCompletedByStudent]);

    /**
     * Get background color for lesson status
     */
    const getLessonStatusColor = useMemo(() => {
        return (lesson: any, index: number): string => {
            const lessonId = lesson.lesson_id || lesson.id;

            // Completed - Green
            if (isLessonCompletedByStudent(lessonId)) {
                return "#27ae60"; // Success green
            }

            // In Progress - Blue
            if (isLessonInProgress(lessonId, index)) {
                return "#3498db"; // Info blue
            }

            // Paused - Orange
            if (lesson.is_paused === true) {
                return "#e67e22"; // Warning orange
            }

            // Not started - Gray
            return "#34495e"; // Dark gray
        };
    }, [isLessonCompletedByStudent, isLessonInProgress]);

    /**
     * Get text color for lesson (contrast with background)
     */
    const getLessonTextColor = useMemo(() => {
        return (lesson: any, index: number): string => {
            // All backgrounds are dark enough for white text
            return "#ffffff";
        };
    }, []);

    /**
     * Get status icon for lesson
     */
    const getLessonStatusIcon = useMemo(() => {
        return (lesson: any, index: number): React.ReactNode => {
            const lessonId = lesson.lesson_id || lesson.id;

            // Completed - Check circle
            if (isLessonCompletedByStudent(lessonId)) {
                return <i className="fas fa-check-circle" style={{ color: "#fff" }}></i>;
            }

            // In Progress - Spinner
            if (isLessonInProgress(lessonId, index)) {
                return <i className="fas fa-spinner fa-pulse" style={{ color: "#fff" }}></i>;
            }

            // Paused - Pause icon
            if (lesson.is_paused === true) {
                return <i className="fas fa-pause-circle" style={{ color: "#fff" }}></i>;
            }

            // Not started - Book icon
            return <i className="fas fa-book" style={{ color: "#95a5a6" }}></i>;
        };
    }, [isLessonCompletedByStudent, isLessonInProgress]);

    return {
        isLessonCompletedByStudent,
        isLessonInProgress,
        getLessonStatusColor,
        getLessonTextColor,
        getLessonStatusIcon,
    };
};
