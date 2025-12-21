/**
 * Classroom API Service
 * Handles all classroom-related API calls
 */

import { ClassroomPollDataType, ClassroomPollRequestParams } from '../types/classroom';

/**
 * Fetch classroom polling data from /classroom/classroom/poll endpoint
 * Returns complete classroom structure: course, lessons, instructor, config, etc.
 *
 * @param courseAuthId - Student's course authorization ID
 * @param courseDateId - Optional specific course date ID
 * @returns Complete classroom poll data
 */
export const fetchClassroomPollData = async (
    courseAuthId: number,
    courseDateId?: number
): Promise<ClassroomPollDataType> => {
    // Use the existing /classroom/class/data endpoint
    const response = await fetch(
        `/classroom/class/data?course_auth_id=${courseAuthId}`
    );

    if (!response.ok) {
        throw new Error(
            `Failed to fetch classroom data: ${response.status} ${response.statusText}`
        );
    }

    const data = await response.json();

    if (!data.success) {
        throw new Error(data.message || "Failed to fetch classroom data");
    }

    return data.data;
};

/**
 * Get instructor's current session status
 * Checks if instructor has an active InstUnit session
 *
 * @param courseAuthId - Student's course authorization ID
 * @returns true if instructor has an active session
 */
export const isInstructorTeaching = (classroomData: ClassroomPollDataType): boolean => {
    return !!classroomData.instUnit && classroomData.instUnit.status === 'active';
};

/**
 * Get classroom status from poll data
 * Determines if classroom is active, waiting, etc.
 *
 * @param classroomData - Complete classroom poll data
 * @returns Classroom status string
 */
export const getClassroomStatus = (classroomData: ClassroomPollDataType): string => {
    if (!classroomData.instUnit) {
        return 'not_started';
    }
    return classroomData.instUnit.status || 'unknown';
};

/**
 * Get current lesson being taught
 * Finds the lesson with 'in_progress' status in instructor's lessons
 *
 * @param classroomData - Complete classroom poll data
 * @returns Current lesson or null
 */
export const getCurrentLesson = (classroomData: ClassroomPollDataType) => {
    if (!classroomData.instLessons || classroomData.instLessons.length === 0) {
        return null;
    }

    const currentLesson = classroomData.instLessons.find(
        (lesson) => lesson.status === 'in_progress'
    );

    if (!currentLesson) {
        return null;
    }

    // Find lesson details from courseLessons
    const lessonData = classroomData.courseLessons.find(
        (cl) => cl.lesson_id === currentLesson.lesson_id
    );

    return {
        ...currentLesson,
        lesson_data: lessonData?.lesson_data,
    };
};

/**
 * Get lesson progress percentage
 * Calculates how many lessons have been completed
 *
 * @param classroomData - Complete classroom poll data
 * @returns Progress object with current, total, percentage
 */
export const getLessonProgress = (classroomData: ClassroomPollDataType) => {
    if (!classroomData.instLessons) {
        return {
            current: 0,
            total: 0,
            percentage: 0,
        };
    }

    const completed = classroomData.instLessons.filter(
        (lesson) => lesson.status === 'completed'
    ).length;

    const total = classroomData.instLessons.length;
    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

    return {
        current: completed,
        total,
        percentage,
    };
};

/**
 * Check if classroom requires specific features
 * Useful for showing/hiding features based on config
 *
 * @param classroomData - Complete classroom poll data
 * @param feature - Feature name to check
 * @returns true if feature is enabled
 */
export const isFeatureEnabled = (
    classroomData: ClassroomPollDataType,
    feature: keyof typeof classroomData.config
): boolean => {
    if (!classroomData.config) {
        return false;
    }

    const value = classroomData.config[feature];
    return value === true;
};

/**
 * Get all course units for the day
 * Returns the "template" of what lessons should happen today
 *
 * @param classroomData - Complete classroom poll data
 * @returns Array of course units
 */
export const getCourseUnitsForDay = (classroomData: ClassroomPollDataType) => {
    return classroomData.courseUnits || [];
};

/**
 * Get all lessons for the day
 * Returns the complete list of lessons scheduled
 *
 * @param classroomData - Complete classroom poll data
 * @returns Array of course unit lessons
 */
export const getCourseLessonsForDay = (classroomData: ClassroomPollDataType) => {
    return classroomData.courseLessons || [];
};

/**
 * Get lesson details by lesson ID
 * Finds lesson information from course lessons
 *
 * @param classroomData - Complete classroom poll data
 * @param lessonId - Lesson ID to find
 * @returns Lesson details or null
 */
export const getLessonById = (classroomData: ClassroomPollDataType, lessonId: number) => {
    return classroomData.courseLessons.find((cl) => cl.lesson_id === lessonId);
};

/**
 * Check if face verification is required for this classroom
 *
 * @param classroomData - Complete classroom poll data
 * @returns true if face verification is required
 */
export const isFaceVerificationRequired = (classroomData: ClassroomPollDataType): boolean => {
    return isFeatureEnabled(classroomData, 'require_face_verification' as any);
};

/**
 * Check if attendance tracking is enabled
 *
 * @param classroomData - Complete classroom poll data
 * @returns true if attendance tracking is enabled
 */
export const isAttendanceTrackingEnabled = (classroomData: ClassroomPollDataType): boolean => {
    return isFeatureEnabled(classroomData, 'require_attendance_tracking' as any);
};
