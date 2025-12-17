import React, { createContext, ReactNode } from 'react';

/**
 * Student Context - Holds all student-related data from polling
 * Contains: user info, courses, progress, assignments, etc.
 */

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
    notifications: any[];
    assignments: any[];
    loading: boolean;
    error: string | null;
}

export const StudentContext = createContext<StudentContextType | undefined>(undefined);

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
        throw new Error('useStudent must be used within StudentContextProvider');
    }
    return context;
};
