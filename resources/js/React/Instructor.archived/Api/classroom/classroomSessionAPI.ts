/**
 * Classroom Session API
 * Handles classroom session operations for instructors
 */

interface SessionResponse {
    success: boolean;
    message: string;
    session_id?: string;
    [key: string]: any;
}

export const classroomSessionAPI = {
    /**
     * Start a classroom session
     */
    startSession: async (courseDateId: string | number): Promise<SessionResponse> => {
        try {
            const response = await fetch(`/admin/instructors/classroom/start/${courseDateId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to start session: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error("Error starting session:", error);
            throw error;
        }
    },

    /**
     * Assist a class session
     */
    assistClass: async (courseDateId: string | number): Promise<SessionResponse> => {
        try {
            const response = await fetch(`/admin/instructors/classroom/assist/${courseDateId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to assist class: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error("Error assisting class:", error);
            throw error;
        }
    },

    /**
     * End a classroom session
     */
    endSession: async (sessionId: string | number): Promise<SessionResponse> => {
        try {
            const response = await fetch(`/admin/instructors/classroom/end/${sessionId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to end session: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error("Error ending session:", error);
            throw error;
        }
    },

    /**
     * Get session status
     */
    getSessionStatus: async (sessionId: string | number): Promise<SessionResponse> => {
        try {
            const response = await fetch(`/admin/instructors/classroom/status/${sessionId}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to get session status: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error("Error getting session status:", error);
            throw error;
        }
    },
};
