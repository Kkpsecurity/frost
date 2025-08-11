/**
 * API Helper Functions
 * Provides centralized API call functions for dashboard components
 */

// Base API URL
const API_BASE = '/api';

/**
 * Generic API fetch wrapper with error handling
 */
async function apiFetch<T>(endpoint: string): Promise<T> {
    const response = await fetch(`${API_BASE}${endpoint}`, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // Add CSRF token if needed
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
        },
    });

    if (!response.ok) {
        throw new Error(`API Error: ${response.status} ${response.statusText}`);
    }

    return response.json();
}

/**
 * Instructor API calls
 */
export const instructorAPI = {
    getStats: () => apiFetch<{
        totalClasses: number;
        activeStudents: number;
        completedLessons: number;
        upcomingClasses: number;
    }>('/admin/instructors/stats'),

    getUpcomingClasses: () => apiFetch<Array<{
        id: number;
        title: string;
        time: string;
        students: number;
        duration: string;
    }>>('/admin/instructors/upcoming-classes'),
};

/**
 * Support API calls
 */
export const supportAPI = {
    getStats: () => apiFetch<{
        openTickets: number;
        resolvedToday: number;
        avgResponseTime: string;
        pendingEscalation: number;
    }>('/admin/support/stats'),

    getRecentTickets: () => apiFetch<Array<{
        id: number;
        subject: string;
        student: string;
        priority: 'low' | 'medium' | 'high' | 'urgent';
        status: 'open' | 'in-progress' | 'resolved';
        created: string;
    }>>('/admin/support/recent-tickets'),
};

/**
 * Student API calls
 */
export const studentAPI = {
    getStats: () => apiFetch<{
        enrolledCourses: number;
        completedLessons: number;
        assignmentsDue: number;
        hoursLearned: number;
    }>('/student/stats'),

    getRecentLessons: () => apiFetch<Array<{
        id: number;
        title: string;
        course: string;
        progress: number;
        duration: string;
        lastAccessed: string;
    }>>('/student/recent-lessons'),

    getUpcomingAssignments: () => apiFetch<Array<{
        id: number;
        title: string;
        course: string;
        dueDate: string;
        type: 'quiz' | 'assignment' | 'project';
    }>>('/student/upcoming-assignments'),
};
