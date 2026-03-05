import React, { createContext, ReactNode } from 'react';
import type { ClassroomPollPayloadShape, ClassroomPollLessonShape } from "../types/classroom";

/**
 * Classroom Context — holds classroom-specific data from the classroom poll.
 *
 * Data source: StudentDashboardController@getClassData via useClassroomPoll.
 * Value is null when no classroom poll is active (no class today / OFFLINE route).
 */

export interface ClassroomContextType {
    /** Raw poll data (the `data` sub-object from the API response). */
    data: ClassroomPollPayloadShape | null;

    // Convenience accessors derived from `data`
    course: any | null;
    courseDate: ClassroomPollPayloadShape['courseDate'];
    instructor: any | null;
    instUnit: ClassroomPollPayloadShape['instUnit'];
    studentUnit?: any | null;
    courseUnits: any[];
    /** Lessons for today from `data.lessons`. Each item: { id, lesson_id, title, … } */
    courseLessons: ClassroomPollLessonShape[];
    /** Raw inst_lessons from `data.instUnit.inst_lessons`. Each item: { id (InstLesson PK), lesson_id, … } */
    instLessons: any[];
    config: any | null;

    // Status indicators
    isClassroomActive: boolean;
    isInstructorOnline: boolean;
    classroomStatus: 'waiting' | 'starting' | 'active' | 'ended' | 'not_started';

    // Loading state
    loading: boolean;
    error: string | null;
}

export const ClassroomContext = createContext<
    ClassroomContextType | null | undefined
>(undefined);

export const ClassroomContextProvider: React.FC<{
    value: ClassroomContextType | null;
    children: ReactNode;
}> = ({ value, children }) => {
    return (
        <ClassroomContext.Provider value={value}>
            {children}
        </ClassroomContext.Provider>
    );
};

/**
 * Hook to use Classroom Context
 * Usage: const classroom = useClassroom();
 */
export const useClassroom = () => {
    const context = React.useContext(ClassroomContext);
    // undefined means the hook is being used outside the provider.
    // null is a valid value meaning "classroom data not loaded/available".
    if (context === undefined) {
        throw new Error(
            "useClassroom must be used within ClassroomContextProvider"
        );
    }
    return context;
};
