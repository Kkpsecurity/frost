/**
 * Classroom Session API Client
 *
 * Handles API calls for managing classroom sessions
 */

export interface ClassroomSessionInfo {
    exists: boolean;
    course_date_id: number;
    inst_unit_id?: number;
    course_name?: string;
    instructor?: {
        id: number;
        name: string;
    };
    assistant?: {
        id: number;
        name: string;
    } | null;
    created_at?: string;
    completed_at?: string | null;
    is_active?: boolean;
    error?: string;
}

export interface StartSessionResponse {
    success: boolean;
    message: string;
    data?: {
        inst_unit_id: number;
        course_date_id: number;
        instructor: {
            id: number;
            name: string;
        };
        assistant?: {
            id: number;
            name: string;
        } | null;
        created_at: string;
        is_existing: boolean;
    };
    errors?: any;
}

export interface AssistClassResponse {
    success: boolean;
    message: string;
    data?: {
        inst_unit_id: number;
        assistant: {
            id: number;
            name: string;
        };
        session_info: ClassroomSessionInfo;
    };
    errors?: any;
}

class ClassroomSessionAPI {
    private baseURL = "/admin/instructors/classroom";

    /**
     * Get CSRF token for requests
     */
    private async getCSRFToken(): Promise<string> {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        if (!token) {
            throw new Error("CSRF token not found");
        }
        return token;
    }

    /**
     * Assign an instructor to a course (creates InstUnit without starting session)
     */
    async assignInstructor(
        courseDateId: number,
        instructorId?: number,
        assistantId?: number
    ): Promise<StartSessionResponse> {
        try {
            const csrfToken = await this.getCSRFToken();

            const response = await fetch(
                `${this.baseURL}/assign-instructor/${courseDateId}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        instructor_id: instructorId || null,
                        assistant_id: assistantId || null,
                    }),
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Failed to assign instructor");
            }

            return data;
        } catch (error) {
            console.error("Error assigning instructor:", error);
            throw error;
        }
    }

    /**
     * Start a new classroom session
     */
    async startSession(
        courseDateId: number,
        assistantId?: number
    ): Promise<StartSessionResponse> {
        try {
            const csrfToken = await this.getCSRFToken();

            const response = await fetch(
                `${this.baseURL}/start-class/${courseDateId}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        assistant_id: assistantId || null,
                    }),
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || "Failed to start classroom session"
                );
            }

            return data;
        } catch (error) {
            console.error("Error starting classroom session:", error);
            throw error;
        }
    }

    /**
     * Take over a classroom session
     */
    async takeOverClass(): Promise<{ success: boolean; message: string }> {
        try {
            const csrfToken = await this.getCSRFToken();

            const response = await fetch(`${this.baseURL}/take-over`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || "Failed to take over classroom session"
                );
            }

            return data;
        } catch (error) {
            console.error("Error taking over classroom session:", error);
            throw error;
        }
    }

    /**
     * Assist in a classroom session
     */
    async assistClass(
        courseDateId?: number,
        instUnitId?: number
    ): Promise<{ success: boolean; message: string; data?: any }> {
        try {
            const csrfToken = await this.getCSRFToken();

            // Build URL with course date ID if provided
            const url = courseDateId
                ? `${this.baseURL}/assist/${courseDateId}`
                : `${this.baseURL}/assist`;

            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    course_date_id: courseDateId,
                    inst_unit_id: instUnitId,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || "Failed to assist in classroom session"
                );
            }

            return data;
        } catch (error) {
            console.error("Error assisting in classroom session:", error);
            throw error;
        }
    }

    /**
     * Get classroom status and data
     */
    async getClassroomStatus(): Promise<any> {
        try {
            const response = await fetch(
                "/admin/instructors/data/classroom/status",
                {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error("Failed to get classroom status");
            }

            return result;
        } catch (error) {
            console.error("Error getting classroom status:", error);
            throw error;
        }
    }
}

// Export singleton instance
export const classroomSessionAPI = new ClassroomSessionAPI();
