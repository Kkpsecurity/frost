import { useState, useEffect } from "react";
import { CourseDate } from "./types";

interface UseBulletinBoardResult {
    courseDates: CourseDate[];
    loading: boolean;
    error: string | null;
    refetch: () => void;
}

export const useBulletinBoard = (): UseBulletinBoardResult => {
    const [courseDates, setCourseDates] = useState<CourseDate[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchTodaysCourses = async () => {
        try {
            setLoading(true);
            const response = await fetch("/admin/instructors/data/bulletin-board");

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            setCourseDates(data.courses || []);
            setError(null);
        } catch (err) {
            console.error("Failed to fetch bulletin board data:", err);
            setError(
                err instanceof Error ? err.message : "Failed to load courses"
            );
            setCourseDates([]);
        } finally {
            setLoading(false);
        }
    };

    const refetch = () => {
        fetchTodaysCourses();
    };

    useEffect(() => {
        fetchTodaysCourses();
    }, []);

    return {
        courseDates,
        loading,
        error,
        refetch
    };
};
