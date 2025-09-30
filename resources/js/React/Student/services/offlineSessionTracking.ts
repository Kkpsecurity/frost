/**
 * Student Offline Session Tracking Service
 *
 * Handles communication with backend for offline session tracking
 */

export interface OfflineSessionData {
    timezone?: string;
    screen_resolution?: string;
    browser_info?: {
        name: string;
        version: string;
        platform: string;
    };
    study_plan?: {
        planned_lessons: number[];
        estimated_duration: number;
        goals: string[];
    };
}

export interface LessonActivityData {
    title?: string;
    duration?: number;
    progress?: number;
    time_spent?: number;
    completed_sections?: string[];
}

export interface SessionStepData {
    step_duration?: number;
    user_input?: any;
    verification_data?: any;
}

export interface SessionStatus {
    has_active_session: boolean;
    session_type: string | null;
    session_id: number | null;
    started_at?: string;
    duration_minutes: number;
    activities_count: number;
    lessons_accessed: number;
    recent_activities: Array<{
        type: string;
        timestamp: string;
    }>;
}

export interface SessionSummary {
    total_sessions: number;
    total_time_minutes: number;
    average_session_duration: number;
    average_completion_rate: number;
    total_lessons_accessed: number;
    sessions_by_date: Record<string, {
        count: number;
        total_minutes: number;
        avg_completion: number;
    }>;
    date_range: {
        from: string;
        to: string;
    };
}

export interface ActivityRecord {
    id: number;
    type: string;
    lesson: string | null;
    session_id: number | null;
    timestamp: string;
    data: any;
}

class OfflineSessionTrackingService {
    private baseUrl = '/student/offline';

    /**
     * Get CSRF token from meta tag
     */
    private getCsrfToken(): string {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            throw new Error('CSRF token not found');
        }
        return token;
    }

    /**
     * Make authenticated request to backend
     */
    private async makeRequest(endpoint: string, options: RequestInit = {}): Promise<any> {
        const url = `${this.baseUrl}${endpoint}`;

        const defaultOptions: RequestInit = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        };

        try {
            const response = await fetch(url, mergedOptions);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error(`Request failed for ${endpoint}:`, error);
            throw error;
        }
    }

    /**
     * Start a new offline study session
     */
    async startSession(courseAuthId: number, sessionData?: OfflineSessionData): Promise<{
        session_id: number;
        session_type: string;
        started_at: string;
        course_auth_id: number;
        status: string;
    }> {
        const response = await this.makeRequest(`/session/start/${courseAuthId}`, {
            method: 'POST',
            body: JSON.stringify(sessionData || {}),
        });

        return response.data;
    }

    /**
     * End current offline session
     */
    async endSession(courseAuthId: number): Promise<{
        session_id: number;
        duration_minutes: number;
        activities_count: number;
        lessons_accessed: number;
        completion_rate: number;
        ended_at: string;
    }> {
        const response = await this.makeRequest(`/session/end/${courseAuthId}`, {
            method: 'POST',
        });

        return response.data;
    }

    /**
     * Track lesson activity (start, complete, pause, resume)
     */
    async trackLessonActivity(
        courseAuthId: number,
        activityType: 'offline_lesson_start' | 'offline_lesson_complete' | 'offline_lesson_pause' | 'offline_lesson_resume',
        lessonId: number,
        lessonData?: LessonActivityData
    ): Promise<{
        activity_id: number;
        activity_type: string;
        lesson_id: number;
        session_id: number | null;
        timestamp: string;
    }> {
        const response = await this.makeRequest(`/track/lesson/${courseAuthId}`, {
            method: 'POST',
            body: JSON.stringify({
                activity_type: activityType,
                lesson_id: lessonId,
                lesson_data: lessonData,
            }),
        });

        return response.data;
    }

    /**
     * Track session step progression
     */
    async trackSessionStep(
        courseAuthId: number,
        stepKey: 'view_lessons' | 'begin_session' | 'student_agreement' | 'student_rules' | 'student_verification' | 'active_session' | 'completed_session',
        stepLabel: string,
        stepData?: SessionStepData
    ): Promise<{
        activity_id: number;
        activity_type: string;
        step_key: string;
        step_label: string;
        session_id: number | null;
        timestamp: string;
    }> {
        const response = await this.makeRequest(`/track/step/${courseAuthId}`, {
            method: 'POST',
            body: JSON.stringify({
                step_key: stepKey,
                step_label: stepLabel,
                step_data: stepData,
            }),
        });

        return response.data;
    }

    /**
     * Get current session status
     */
    async getSessionStatus(courseAuthId: number): Promise<SessionStatus> {
        const response = await this.makeRequest(`/session/status/${courseAuthId}`);
        return response.data;
    }

    /**
     * Get session summary for date range
     */
    async getSessionSummary(
        courseAuthId: number,
        dateFrom?: string,
        dateTo?: string
    ): Promise<SessionSummary> {
        const params = new URLSearchParams();
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        const queryString = params.toString();
        const endpoint = `/summary/${courseAuthId}${queryString ? `?${queryString}` : ''}`;

        const response = await this.makeRequest(endpoint);
        return response.data;
    }

    /**
     * Get recent offline activities
     */
    async getRecentActivities(courseAuthId: number, limit = 20): Promise<{
        activities: ActivityRecord[];
        count: number;
    }> {
        const response = await this.makeRequest(`/activities/${courseAuthId}?limit=${limit}`);
        return response.data;
    }

    /**
     * Force end all sessions (cleanup)
     */
    async forceEndSessions(courseAuthId: number): Promise<{
        sessions_ended: number;
    }> {
        const response = await this.makeRequest(`/session/force-end/${courseAuthId}`, {
            method: 'POST',
        });

        return response.data;
    }

    // =============================================================================
    // CONVENIENCE METHODS
    // =============================================================================

    /**
     * Auto-start session with browser detection
     */
    async autoStartSession(courseAuthId: number): Promise<any> {
        const sessionData: OfflineSessionData = {
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            screen_resolution: `${screen.width}x${screen.height}`,
            browser_info: {
                name: this.getBrowserName(),
                version: this.getBrowserVersion(),
                platform: navigator.platform,
            },
        };

        return this.startSession(courseAuthId, sessionData);
    }

    /**
     * Track lesson start with auto-detection
     */
    async startLesson(courseAuthId: number, lessonId: number, lessonTitle?: string): Promise<any> {
        return this.trackLessonActivity(
            courseAuthId,
            'offline_lesson_start',
            lessonId,
            {
                title: lessonTitle,
                duration: 0,
                progress: 0,
            }
        );
    }

    /**
     * Track lesson completion with progress data
     */
    async completeLesson(
        courseAuthId: number,
        lessonId: number,
        timeSpent: number,
        completedSections?: string[]
    ): Promise<any> {
        return this.trackLessonActivity(
            courseAuthId,
            'offline_lesson_complete',
            lessonId,
            {
                time_spent: timeSpent,
                progress: 100,
                completed_sections: completedSections,
            }
        );
    }

    /**
     * Check if user has active session
     */
    async hasActiveSession(courseAuthId: number): Promise<boolean> {
        try {
            const status = await this.getSessionStatus(courseAuthId);
            return status.has_active_session;
        } catch (error) {
            console.error('Failed to check session status:', error);
            return false;
        }
    }

    // =============================================================================
    // PRIVATE HELPER METHODS
    // =============================================================================

    private getBrowserName(): string {
        const userAgent = navigator.userAgent;
        if (userAgent.includes('Chrome')) return 'Chrome';
        if (userAgent.includes('Firefox')) return 'Firefox';
        if (userAgent.includes('Safari')) return 'Safari';
        if (userAgent.includes('Edge')) return 'Edge';
        return 'Unknown';
    }

    private getBrowserVersion(): string {
        const userAgent = navigator.userAgent;
        const match = userAgent.match(/(Chrome|Firefox|Safari|Edge)\/([0-9.]+)/);
        return match ? match[2] : 'Unknown';
    }
}

// Export singleton instance
export const offlineSessionTracking = new OfflineSessionTrackingService();
export default offlineSessionTracking;
