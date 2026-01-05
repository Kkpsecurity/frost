import { useContext } from 'react';
import { ClassroomContext, ClassroomContextType } from '../context/ClassroomContext';

/**
 * useClassroom Hook
 *
 * Access classroom data and status from ClassroomContext
 *
 * Usage:
 * const classroom = useClassroom();
 * console.log(classroom.isClassroomActive);
 *
 * @returns ClassroomContextType
 * @throws Error if used outside ClassroomContextProvider
 */
export const useClassroom = (): ClassroomContextType | null => {
    const context = useContext(ClassroomContext);
    if (context === undefined) {
        throw new Error(
            "useClassroom must be used within ClassroomContextProvider"
        );
    }
    return context;
};

export default useClassroom;
