import { useState, useEffect, useRef, useCallback } from "react";
import { CourseDate, AssignmentHistoryRecord } from "./types";

interface UseBulletinBoardResult {
    courseDates: CourseDate[];
    assignmentHistory: AssignmentHistoryRecord[];
    loading: boolean;
    error: string | null;
    refetch: () => void;
    isPolling: boolean;
    lastUpdated: Date | null;
}

export const useBulletinBoard = (): UseBulletinBoardResult => {
    const [courseDates, setCourseDates] = useState<CourseDate[]>([]);
    const [assignmentHistory, setAssignmentHistory] = useState<
        AssignmentHistoryRecord[]
    >([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [isPolling, setIsPolling] = useState(false);
    const [lastUpdated, setLastUpdated] = useState<Date | null>(null);

    const pollingIntervalRef = useRef<NodeJS.Timeout | null>(null);
    const isInitialLoad = useRef(true);

    const fetchTodaysCourses = useCallback(
        async (isBackgroundUpdate = false) => {
            try {
                // Only show loading spinner on initial load, not during polling
                if (!isBackgroundUpdate) {
                    setLoading(true);
                }
                const response = await fetch(
                    "/admin/instructors/data/lessons/today",
                    {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN":
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content") || "",
                        },
                    }
                );

                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error(
                            "Authentication expired. Please refresh the page and log in again."
                        );
                    } else if (response.status === 500) {
                        throw new Error(
                            "Server error loading today's lessons. Please try again later."
                        );
                    } else {
                        throw new Error(
                            `HTTP error! status: ${response.status}`
                        );
                    }
                }
                const data = await response.json();
                // Convert lessons to CourseDate format
                const lessons = data.lessons || [];
                const courseDates = lessons.map((lesson: any) => ({
                    id: lesson.id,
                    course_name: lesson.course_name,
                    class_day: lesson.lesson_name,
                    lesson_count: lesson.lesson_count || 1, // Use actual lesson count from API
                    calendar_title: `${lesson.course_name} - ${lesson.lesson_name}`,
                    instructor:
                        lesson.instructor_name ||
                        lesson.inst_unit?.instructor ||
                        "Not assigned",
                    instructor_name: lesson.instructor_name, // Add instructor_name for CourseCard
                    assistant_name: lesson.assistant_name, // Add assistant_name for CourseCard
                    time: lesson.time, // Add time for CourseCard
                    start_time: lesson.time,
                    end_time: lesson.ends_at,
                    student_count: lesson.student_count,
                    class_status: lesson.class_status, // Add class_status for CourseCard
                    module: lesson.module, // Add module for CourseCard
                    is_scheduled: lesson.class_status === "scheduled",
                    is_live:
                        lesson.class_status === "assigned" ||
                        lesson.class_status === "live",
                    status: lesson.class_status,
                    buttons: lesson.buttons,
                    inst_unit: lesson.inst_unit,
                }));
                setCourseDates(courseDates);

                // Set assignment history from the response
                const history = data.assignment_history || [];
                setAssignmentHistory(history);
                setError(null);
                setLastUpdated(new Date());
            } catch (err) {
                console.error("Failed to fetch bulletin board data:", err);
                setError(
                    err instanceof Error
                        ? err.message
                        : "Failed to load courses"
                );
                if (!isBackgroundUpdate) {
                    setCourseDates([]);
                }
            } finally {
                if (!isBackgroundUpdate) {
                    setLoading(false);
                }
            }
        },
        []
    );

    const startPolling = useCallback(() => {
        if (pollingIntervalRef.current) {
            clearInterval(pollingIntervalRef.current);
        }

        setIsPolling(true);
        pollingIntervalRef.current = setInterval(() => {
            console.log("🔄 Polling instructor dashboard data...");
            fetchTodaysCourses(true); // Background update
        }, 15000); // 15 seconds
    }, [fetchTodaysCourses]);

    const stopPolling = useCallback(() => {
        if (pollingIntervalRef.current) {
            clearInterval(pollingIntervalRef.current);
            pollingIntervalRef.current = null;
        }
        setIsPolling(false);
    }, []);

    const refetch = useCallback(() => {
        fetchTodaysCourses(false);
    }, [fetchTodaysCourses]);

    // Initial load and polling setup
    useEffect(() => {
        if (isInitialLoad.current) {
            fetchTodaysCourses(false);
            isInitialLoad.current = false;

            // Start polling after initial load
            setTimeout(startPolling, 2000); // Start polling 2 seconds after initial load
        }

        return () => {
            stopPolling();
        };
    }, [fetchTodaysCourses, startPolling, stopPolling]);

    // Cleanup on unmount
    useEffect(() => {
        return () => {
            if (pollingIntervalRef.current) {
                clearInterval(pollingIntervalRef.current);
            }
        };
    }, []);

    return {
        courseDates,
        assignmentHistory,
        loading,
        error,
        refetch,
        isPolling,
        lastUpdated,
    };
};
