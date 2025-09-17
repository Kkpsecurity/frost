/**
 * Enhanced API Client for Instructor Module
 * Real implementations for instructor dashboard endpoints
 */

import { getCsrfToken } from '../../utils/LaravelHelper';

// Instructor-specific API client with enhanced features
class InstructorApiClient {
    private baseUrl: string;
    private csrfToken: string | null;

    constructor() {
        this.baseUrl = '/admin/instructors';
        this.csrfToken = null;
        this.initializeCsrfToken();
    }

    private async initializeCsrfToken() {
        try {
            this.csrfToken = await getCsrfToken();
        } catch (error) {
            console.warn('Could not initialize CSRF token:', error);
            // Try to get from meta tag as fallback
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = metaTag?.getAttribute('content') || null;
        }
    }

    private async makeRequest(endpoint: string, options: RequestInit = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(this.csrfToken && { 'X-CSRF-TOKEN': this.csrfToken }),
        };

        const response = await fetch(url, {
            ...options,
            headers: {
                ...defaultHeaders,
                ...options.headers,
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    }

    // Real implementations for instructor dashboard
    async fetchLaravelConfig() {
        // This is a placeholder - implement when Laravel config endpoint is available
        return Promise.resolve({ 
            status: "active",
            app_name: "Frost LMS",
            version: "1.0"
        });
    }

    async validateInstructor() {
        return this.makeRequest('/validate');
    }

    async fetchClassroomData() {
        return this.makeRequest('/data/classroom/status');
    }

    async fetchStudentsData() {
        return this.makeRequest('/data/students/enrolled');
    }

    async fetchTodaysLessons() {
        return this.makeRequest('/data/lessons/today');
    }

    async fetchUpcomingLessons() {
        return this.makeRequest('/data/lessons/upcoming');
    }

    async fetchPreviousLessons() {
        return this.makeRequest('/data/lessons/previous');
    }

    async fetchOverviewStats() {
        return this.makeRequest('/data/stats/overview');
    }

    async fetchNotifications() {
        return this.makeRequest('/data/notifications/unread');
    }

    async fetchBulletinBoard() {
        return this.makeRequest('/data/bulletin-board');
    }

    async fetchCompletedCourses() {
        return this.makeRequest('/data/completed-courses');
    }

    async fetchRecentActivity() {
        return this.makeRequest('/data/activity/recent');
    }

    async fetchMessageThreads() {
        // TODO: Implement when messaging system is ready
        return Promise.resolve([]);
    }

    async testConnection() {
        try {
            await this.validateInstructor();
            return { connected: true };
        } catch (error) {
            return { connected: false, error: error instanceof Error ? error.message : 'Unknown error' };
        }
    }
}

// Singleton instance
export const instructorApi = new InstructorApiClient();

// Helper functions for direct fetch usage (for TanStack Query)
export const fetchHelpers = {
  laravelConfig: () => instructorApi.fetchLaravelConfig(),
  instructorValidation: () => instructorApi.validateInstructor(),
  classroomData: () => instructorApi.fetchClassroomData(),
  studentsData: () => instructorApi.fetchStudentsData(),
  todaysLessons: () => instructorApi.fetchTodaysLessons(),
  upcomingLessons: () => instructorApi.fetchUpcomingLessons(),
  previousLessons: () => instructorApi.fetchPreviousLessons(),
  overviewStats: () => instructorApi.fetchOverviewStats(),
  notifications: () => instructorApi.fetchNotifications(),
  bulletinBoard: () => instructorApi.fetchBulletinBoard(),
  completedCourses: () => instructorApi.fetchCompletedCourses(),
  recentActivity: () => instructorApi.fetchRecentActivity(),
  messageThreads: () => instructorApi.fetchMessageThreads(),
  testConnection: () => instructorApi.testConnection(),
};

export default instructorApi;
