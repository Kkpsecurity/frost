import { useEffect, useState, useRef } from 'react';
import axios from 'axios';

interface SupportPollData {
    student: any;
    courses: any[];
    courseActivity: any;
    lessons: any[];
    classHistory: any[];
    photos: any[];
    examResults: any[];
    studentDetails: any;
}

interface UseSupportPollOptions {
    studentId: number | null;
    courseId: string | null;
    enabled: boolean;
    pollInterval?: number;
}

export const useSupportPoll = ({
    studentId,
    courseId,
    enabled,
    pollInterval = 5000
}: UseSupportPollOptions) => {
    const [data, setData] = useState<SupportPollData | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const pollTimerRef = useRef<NodeJS.Timeout | null>(null);
    const abortControllerRef = useRef<AbortController | null>(null);

    const fetchPollData = async () => {
        if (!studentId || !enabled) return;

        try {
            // Cancel previous request if still pending
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }

            abortControllerRef.current = new AbortController();

            const params: any = { student_id: studentId };
            if (courseId) {
                params.course_id = courseId;
            }

            const response = await axios.get('/admin/api/support/poll-data', {
                params,
                signal: abortControllerRef.current.signal
            });

            // Backend returns {success: true, data: {...}}
            // We want the nested data object
            setData(response.data.data);
            setError(null);
        } catch (err: any) {
            if (err.name !== 'AbortError' && err.name !== 'CanceledError') {
                console.error('Support poll error:', err);
                setError(err.response?.data?.message || 'Failed to fetch support data');
            }
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (!enabled || !studentId) {
            // Clear timer when disabled
            if (pollTimerRef.current) {
                clearInterval(pollTimerRef.current);
                pollTimerRef.current = null;
            }
            return;
        }

        // Initial fetch
        setIsLoading(true);
        fetchPollData();

        // Set up polling interval
        pollTimerRef.current = setInterval(() => {
            fetchPollData();
        }, pollInterval);

        // Cleanup function
        return () => {
            if (pollTimerRef.current) {
                clearInterval(pollTimerRef.current);
                pollTimerRef.current = null;
            }
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
        };
    }, [studentId, courseId, enabled, pollInterval]);

    return { data, isLoading, error, refetch: fetchPollData };
};
