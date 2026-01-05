import React, { createContext, ReactNode } from 'react';
import type { ClassroomPollDataType } from "../types/classroom";

/**
 * Classroom Context - Holds all classroom-specific data from polling
 *
 * Data structure matches /classroom/classroom/poll endpoint response
 * Contains: course, lessons, instructor, sessions, configuration
 */

export interface ClassroomContextType {
    // Raw poll data from endpoint
    data: ClassroomPollDataType | null;

    // Convenience accessors
    course: ClassroomPollDataType["course"] | null;
    // Note: the classroom poll payload uses `courseDate`.
    courseDate: any | null;
    instructor: ClassroomPollDataType["instructor"] | null;
    instUnit: ClassroomPollDataType["instUnit"] | null;
    // StudentUnit is needed for onboarding gating.
    studentUnit?: any | null;
    courseUnits: ClassroomPollDataType["courseUnits"];
    courseLessons: ClassroomPollDataType["courseLessons"];
    instLessons: ClassroomPollDataType["instLessons"];
    config: ClassroomPollDataType["config"] | null;

    // Status indicators
    isClassroomActive: boolean;
    isInstructorOnline: boolean;
    classroomStatus:
        | "waiting"
        | "starting"
        | "active"
        | "ended"
        | "not_started";

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
