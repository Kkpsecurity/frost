import { useState } from "react";
import { StudentType } from "../../../../../Config/types";


/**
 * This hook nanages the data for the Course Console
 */
export const useCourseConsoleData = () => {
   
    /**
     * The Students array has both types of student are
     * separated by the type key. This is the array that we use to
     * to have all the students in one array without the type key
     */
    const [allStudents, setAllStudents] = useState<StudentType[]>([]);

    /**
     * Sets the Selected student to view in the Student Card
     */
    const [selectStudent, setSelectStudent] = useState<number | null>(null);

    return {
        allStudents,
        setAllStudents,
        selectStudent,
        setSelectStudent,
    };
};
