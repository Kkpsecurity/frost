import { useState, useEffect, useCallback } from "react";

export interface BulletinBoardData {
    announcements: Announcement[];
    available_courses: AvailableCourse[];
    instructor_resources: InstructorResource[];
    quick_stats: {
        total_instructors: number;
        active_courses_today: number;
        students_in_system: number;
    };
}

export interface Announcement {
    id: number;
    title: string;
    content: string;
    type: string;
    author: string;
    created_at: string;
    expires_at?: string;
}

export interface AvailableCourse {
    id: number;
    title: string;
    description?: string;
    total_minutes: number;
    price: number;
    is_active: boolean;
}

export interface InstructorResource {
    id: number;
    title: string;
    description: string;
    type: string;
    category: string;
    url: string;
}

interface UseInstructorBulletinBoardResult {
    data: BulletinBoardData | null;
    loading: boolean;
    error: string | null;
    refetch: () => void;
}

export const useInstructorBulletinBoard = (): UseInstructorBulletinBoardResult => {
    const [data, setData] = useState<BulletinBoardData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchBulletinBoardData = useCallback(async () => {
        try {
            setLoading(true);
            const response = await fetch("/admin/instructors/data/bulletin-board", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
            });

            if (!response.ok) {
                if (response.status === 401) {
                    // For development, provide fallback data if not authenticated
                    console.warn("Not authenticated, using fallback bulletin board data");
                    const fallbackData: BulletinBoardData = {
                        announcements: [
                            {
                                id: 1,
                                title: "Welcome to Instructor Dashboard",
                                content: "You are viewing the instructor dashboard. Login to see live data.",
                                type: "info",
                                author: "System",
                                created_at: new Date().toISOString(),
                            }
                        ],
                        available_courses: [
                            {
                                id: 1,
                                title: "Sample Course",
                                description: "This is a sample course for demonstration",
                                total_minutes: 120,
                                price: 299,
                                is_active: true
                            }
                        ],
                        instructor_resources: [
                            {
                                id: 1,
                                title: "Getting Started Guide",
                                description: "Learn how to use the instructor dashboard",
                                type: "document",
                                category: "Getting Started",
                                url: "#"
                            }
                        ],
                        quick_stats: {
                            total_instructors: 15,
                            active_courses_today: 3,
                            students_in_system: 125
                        }
                    };
                    setData(fallbackData);
                    setError(null);
                    return;
                } else if (response.status === 500) {
                    throw new Error(
                        "Server error loading bulletin board. Please try again later."
                    );
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }

            const bulletinData = await response.json();
            setData(bulletinData);
            setError(null);
        } catch (err) {
            console.error("Failed to fetch instructor bulletin board data:", err);
            setError(
                err instanceof Error ? err.message : "Failed to load bulletin board"
            );
        } finally {
            setLoading(false);
        }
    }, []);

    const refetch = useCallback(() => {
        fetchBulletinBoardData();
    }, [fetchBulletinBoardData]);

    useEffect(() => {
        fetchBulletinBoardData();
    }, [fetchBulletinBoardData]);

    return {
        data,
        loading,
        error,
        refetch,
    };
};
