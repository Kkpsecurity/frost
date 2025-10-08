import { useState, useEffect, useMemo } from 'react';
import { CourseDate } from '../Components/Offline/types';

export interface InstructorAssignment {
    assignedCourse: CourseDate | null;
    isInstructor: boolean;
    isAssistant: boolean;
    shouldEnterClassroom: boolean;
    currentUserId: number | null;
}

export interface UseInstructorAssignmentOptions {
    courses: CourseDate[] | null;
    currentUserId?: number | null;
    autoEnterClassroom?: boolean;
}

/**
 * Custom hook to detect instructor/assistant assignments and manage classroom entry
 *
 * This hook handles:
 * - Detecting if current user is assigned as instructor or assistant
 * - Determining if user should automatically enter classroom mode
 * - Providing assignment status information
 */
export const useInstructorAssignment = ({
    courses,
    currentUserId = null,
    autoEnterClassroom = true
}: UseInstructorAssignmentOptions): InstructorAssignment => {

    // Find active course where user is assigned as instructor or assistant
    const assignedCourse = useMemo(() => {
        if (!courses || !currentUserId) {
            return null;
        }

        return courses.find((course) => {
            console.log("🔍 Checking course for assignment:", {
                courseId: course.id,
                courseName: course.course_name,
                class_status: course.class_status,
                hasInstUnit: !!course.inst_unit,
                instUnitCompleted: course.inst_unit?.completed_at,
                currentUserId
            });

            // Check if course has active InstUnit
            if (!course.inst_unit || course.inst_unit.completed_at !== null) {
                console.log("❌ Skipping course - no InstUnit or completed");
                return false;
            }

            // Check if current user is assigned to this course
            const isInstructor = course.inst_unit.created_by === currentUserId;
            const isAssistant = course.inst_unit.assistant_id === currentUserId;

            console.log("🔍 InstUnit assignment check:", {
                courseId: course.id,
                courseName: course.course_name,
                currentUserId,
                instructorId: course.inst_unit.created_by,
                assistantId: course.inst_unit.assistant_id,
                isInstructor,
                isAssistant,
                classStatus: course.class_status
            });

            return isInstructor || isAssistant;
        }) || null;
    }, [courses, currentUserId]);

    // Determine user role in assigned course
    const isInstructor = useMemo(() => {
        return assignedCourse?.inst_unit?.created_by === currentUserId;
    }, [assignedCourse, currentUserId]);

    const isAssistant = useMemo(() => {
        return assignedCourse?.inst_unit?.assistant_id === currentUserId;
    }, [assignedCourse, currentUserId]);

    // Determine if user should enter classroom automatically
    const shouldEnterClassroom = useMemo(() => {
        return autoEnterClassroom && assignedCourse !== null && (isInstructor || isAssistant);
    }, [autoEnterClassroom, assignedCourse, isInstructor, isAssistant]);

    // Log assignment changes
    useEffect(() => {
        if (assignedCourse) {
            console.log("👤 User assignment detected:", {
                courseId: assignedCourse.id,
                courseName: assignedCourse.course_name,
                role: isInstructor ? 'instructor' : isAssistant ? 'assistant' : 'unknown',
                shouldEnterClassroom,
                autoEnterClassroom
            });
        } else {
            console.log("📋 No active assignment - staying on bulletin board");
        }
    }, [assignedCourse, isInstructor, isAssistant, shouldEnterClassroom, autoEnterClassroom]);

    return {
        assignedCourse,
        isInstructor,
        isAssistant,
        shouldEnterClassroom,
        currentUserId
    };
};

export default useInstructorAssignment;
