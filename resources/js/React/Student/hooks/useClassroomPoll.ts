/**
 * Classroom Poll Hook
 * Polls shared classroom data every 5 seconds
 * Returns classroom state, lessons, instructor, course date, etc.
 */

import { useQuery, keepPreviousData } from '@tanstack/react-query';

interface ClassroomPollData {
    success: boolean;
    data: {
        courseDate: any;
        courseUnit: any;
        instUnit: any;
        instructor: any;
        lessons: any[];
        modality: string;
        activeLesson: any;
        zoom: any;
        challenge?: any;
        breaks?: any;
    };
}

const fetchClassroomPoll = async (courseDateId?: number): Promise<ClassroomPollData> => {
    const url = courseDateId
        ? `/classroom/class/data?course_date_id=${courseDateId}`
        : '/classroom/class/data';

    const response = await fetch(url);

    if (!response.ok) {
        // Return empty classroom data structure on 404 (no classroom today)
        if (response.status === 404) {
            return {
                success: true,
                data: {
                    courseDate: null,
                    courseUnit: null,
                    instUnit: null,
                    instructor: null,
                    lessons: [],
                    modality: 'offline',
                    activeLesson: null,
                    zoom: null,
                },
            };
        }
        throw new Error(`Failed to fetch classroom data: ${response.status}`);
    }

    return response.json();
};

export const useClassroomPoll = (courseDateId?: number) => {
    return useQuery({
        queryKey: ['classroom-poll', courseDateId],
        queryFn: () => fetchClassroomPoll(courseDateId),
        enabled: true, // Always enabled, handle no courseDateId in backend
        placeholderData: keepPreviousData,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });
};
