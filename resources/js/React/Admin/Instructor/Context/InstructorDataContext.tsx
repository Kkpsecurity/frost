import React, { createContext, useContext, ReactNode } from "react";
import { InstructorDataContextType } from "../types";

const InstructorDataContext = createContext<InstructorDataContextType | undefined>(undefined);

interface InstructorDataContextProviderProps {
    children: ReactNode;
    value: InstructorDataContextType;
}

export const InstructorDataContextProvider: React.FC<InstructorDataContextProviderProps> = ({
    children,
    value,
}) => (
    <InstructorDataContext.Provider value={value}>
        {children}
    </InstructorDataContext.Provider>
);

export const useInstructorDataContext = (): InstructorDataContextType => {
    const context = useContext(InstructorDataContext);
    if (!context) {
        throw new Error("useInstructorDataContext must be used within InstructorDataContextProvider");
    }
    return context;
};
