import { useState, useEffect } from "react";

export interface CompletedCourse {
    id: number;
    course_date_id: number;
    course_name: string;
    course_unit_title: string;
    sequence: number;
    completed_at: string;
    completed_by_name: string;
    student_count: number;
    duration: string;
    course_date: string;
    completion_date: string;
}

interface UseCompletedCoursesResult {
    completedCourses: CompletedCourse[];
    loading: boolean;
    error: string | null;
    refetch: () => void;
}

export const useCompletedCourses = (): UseCompletedCoursesResult => {
    const [completedCourses, setCompletedCourses] = useState<CompletedCourse[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchCompletedCourses = async () => {
        try {
            setLoading(true);
            const response = await fetch("/admin/instructors/data/completed-courses", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                },
            });

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error("Authentication expired. Please refresh the page and log in again.");
                } else if (response.status === 500) {
                    throw new Error("Server error loading completed courses. Please try again later.");
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }

            const data = await response.json();
            setCompletedCourses(data.completed_courses || []);
            setError(null);
        } catch (err) {
            console.error("Failed to fetch completed courses:", err);
            setError(
                err instanceof Error ? err.message : "Failed to load completed courses"
            );
            setCompletedCourses([]);
        } finally {
            setLoading(false);
        }
    };

    const refetch = () => {
        fetchCompletedCourses();
    };

    useEffect(() => {
        fetchCompletedCourses();
    }, []);

    return {
        completedCourses,
        loading,
        error,
        refetch
    };
};
