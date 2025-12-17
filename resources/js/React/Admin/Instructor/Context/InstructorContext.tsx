import React, { createContext, useContext, ReactNode } from "react";

export interface InstructorContextType {
    instructor: any | null;
    todayLessons: any[];
    upcomingLessons: any[];
    previousLessons: any[];
    stats: any | null;
    bulletinBoardData: any | null;
    loading: boolean;
    error: string | null;
    lastUpdated: Date | null;
}

const InstructorContext = createContext<InstructorContextType | undefined>(undefined);

interface InstructorContextProviderProps {
    children: ReactNode;
    value: InstructorContextType;
}

export const InstructorContextProvider: React.FC<InstructorContextProviderProps> = ({
    children,
    value,
}) => (
    <InstructorContext.Provider value={value}>
        {children}
    </InstructorContext.Provider>
);

export const useInstructorContext = (): InstructorContextType => {
    const context = useContext(InstructorContext);
    if (!context) {
        throw new Error("useInstructorContext must be used within InstructorContextProvider");
    }
    return context;
};
