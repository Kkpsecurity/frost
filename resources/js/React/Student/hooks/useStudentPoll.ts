/**
 * Student Poll Hook
 * Polls student-owned data every 5 seconds
 * Returns student info, courses, progress, validations, active classroom, exam status, etc.
 */

import { useQuery, keepPreviousData } from '@tanstack/react-query';

interface StudentPollData {
    data: {
        student: any;
        courses: any[];
        progress: any;
        validations_by_course_auth: any;
        active_classroom: any;
        studentExam: any;
        studentExamsByCourseAuth: Record<number, any>;
        lessons_by_course_auth: any;
        studentUnit: any;
        studentLessons: any[];
        notifications: any[];
        assignments: any[];
    };
}

const fetchStudentPoll = async (): Promise<StudentPollData> => {
    const response = await fetch('/classroom/student/poll');

    if (!response.ok) {
        throw new Error(`Failed to fetch student data: ${response.status}`);
    }

    return response.json();
};

export const useStudentPoll = () => {
    return useQuery({
        queryKey: ['student-poll'],
        queryFn: fetchStudentPoll,
        placeholderData: keepPreviousData,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });
};
