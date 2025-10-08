import { useState, useCallback } from 'react';
import { CourseDate } from '../Components/Offline/types';
import { classroomSessionAPI } from '../Components/api/classroomSessionAPI';

export interface UseClassroomActionsResult {
    isLoading: boolean;
    error: string | null;
    joinAsInstructor: (courseDate: CourseDate, assistantId?: number) => Promise<boolean>;
    joinAsAssistant: (courseDate: CourseDate) => Promise<boolean>;
    leaveClassroom: (instUnitId: number) => Promise<boolean>;
    clearError: () => void;
}

/**
 * Custom hook for classroom joining actions
 *
 * Handles:
 * - Starting a class as instructor (creates InstUnit)
 * - Joining a class as assistant (updates InstUnit)
 * - Leaving classroom (updates InstUnit if needed)
 * - Loading states and error handling
 */
export const useClassroomActions = (): UseClassroomActionsResult => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const clearError = useCallback(() => {
        setError(null);
    }, []);

    /**
     * Join a class as the instructor (start class)
     */
    const joinAsInstructor = useCallback(async (
        courseDate: CourseDate,
        assistantId?: number
    ): Promise<boolean> => {
        setIsLoading(true);
        setError(null);

        try {
            const response = await classroomSessionAPI.startSession(courseDate.id, assistantId);

            if (!response.success) {
                setError(response.message || 'Failed to start class');
                return false;
            }

            console.log("✅ Successfully joined as instructor:", {
                courseId: courseDate.id,
                courseName: courseDate.course_name,
                instUnitId: response.data?.inst_unit_id,
                assistant: response.data?.assistant?.name || 'None'
            });

            return true;
        } catch (err: any) {
            const errorMessage = err?.message || 'Error starting class';
            setError(errorMessage);
            console.error("❌ Error joining as instructor:", err);
            return false;
        } finally {
            setIsLoading(false);
        }
    }, []);

    /**
     * Join a class as an assistant
     */
    const joinAsAssistant = useCallback(async (courseDate: CourseDate): Promise<boolean> => {
        setIsLoading(true);
        setError(null);

        try {
            const response = await classroomSessionAPI.assistClass(
                courseDate.id,
                courseDate.inst_unit?.id
            );

            if (!response.success) {
                setError(response.message || 'Failed to join as assistant');
                return false;
            }

            console.log("✅ Successfully joined as assistant:", {
                courseId: courseDate.id,
                courseName: courseDate.course_name,
                instUnitId: courseDate.inst_unit?.id,
                assistantName: response.data?.assistant?.name
            });

            return true;
        } catch (err: any) {
            const errorMessage = err?.message || 'Error joining as assistant';
            setError(errorMessage);
            console.error("❌ Error joining as assistant:", err);
            return false;
        } finally {
            setIsLoading(false);
        }
    }, []);

    /**
     * Leave the classroom (complete session or remove assignment)
     */
    const leaveClassroom = useCallback(async (instUnitId: number): Promise<boolean> => {
        setIsLoading(true);
        setError(null);

        try {
            // TODO: Implement leave classroom API call
            // This could be completing the InstUnit or removing assistant assignment
            console.log("🚪 Leaving classroom for InstUnit:", instUnitId);

            // For now, just return success
            // await classroomSessionAPI.completeSession(instUnitId);

            return true;
        } catch (err: any) {
            const errorMessage = err?.message || 'Error leaving classroom';
            setError(errorMessage);
            console.error("❌ Error leaving classroom:", err);
            return false;
        } finally {
            setIsLoading(false);
        }
    }, []);

    return {
        isLoading,
        error,
        joinAsInstructor,
        joinAsAssistant,
        leaveClassroom,
        clearError
    };
};

export default useClassroomActions;
