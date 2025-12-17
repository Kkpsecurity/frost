import React, { createContext, useContext, ReactNode } from "react";

export interface ClassroomActivityContextType {
    // Classroom Poll Response
    courseDate: any | null;
    courses: any[];
    lessons: any[];
    courseUnit: any | null;
    courseLessons: any[];

    // Instructor Data (when class started)
    instructor: any | null;
    instUnit: any | null;
    instLessons: any[];

    // Status
    isClassroomActive: boolean;
    classroomStatus: 'idle' | 'starting' | 'active' | 'ending';
    students: any[];

    loading: boolean;
    error: string | null;
    lastUpdated: Date | null;
}

const ClassroomActivityContext = createContext<ClassroomActivityContextType | undefined>(undefined);

interface ClassroomActivityContextProviderProps {
    children: ReactNode;
    value: ClassroomActivityContextType;
}

export const ClassroomActivityContextProvider: React.FC<ClassroomActivityContextProviderProps> = ({
    children,
    value,
}) => (
    <ClassroomActivityContext.Provider value={value}>
        {children}
    </ClassroomActivityContext.Provider>
);

export const useClassroomActivityContext = (): ClassroomActivityContextType => {
    const context = useContext(ClassroomActivityContext);
    if (!context) {
        throw new Error("useClassroomActivityContext must be used within ClassroomActivityContextProvider");
    }
    return context;
};
