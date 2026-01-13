/**
 * Route Checking Utilities
 * Provides functions to check the current route for conditional component loading
 */

export const RouteCheckers = {
    /**
     * Check if currently on /admin/instructors route
     */
    isAdminInstructors(): boolean {
        return window.location.pathname.includes("/admin/instructors");
    },

    /**
     * Check if currently on /admin/assistant/classroom route
     */
    isAdminAssistantClassroom(): boolean {
        return window.location.pathname.includes("/admin/assistant/classroom");
    },

    /**
     * Check if currently on /classroom route (student area)
     */
    isStudentClassroom(): boolean {
        return (
            window.location.pathname.includes("/classroom") &&
            !window.location.pathname.includes("/classroom/portal/")
        );
    },

    /**
     * Check if currently on /admin route
     */
    isAdmin(): boolean {
        return window.location.pathname.includes("/admin");
    },

    /**
     * Check if currently on student dashboard
     */
    isStudentDashboard(): boolean {
        return window.location.pathname.includes("/dashboard");
    },

    /**
     * Check if currently on /admin/frost-support route
     */
    isAdminFrostSupport(): boolean {
        return window.location.pathname.includes("/admin/frost-support");
    },
};

/**
 * Log current route info for debugging
 */
export const logRouteInfo = (): void => {
    if (process.env.NODE_ENV === "development") {
        console.log("🔀 Route Info:", {
            pathname: window.location.pathname,
            isAdminInstructors: RouteCheckers.isAdminInstructors(),
            isAdminAssistantClassroom:
                RouteCheckers.isAdminAssistantClassroom(),
            isStudentClassroom: RouteCheckers.isStudentClassroom(),
            isAdmin: RouteCheckers.isAdmin(),
            isStudentDashboard: RouteCheckers.isStudentDashboard(),
            isAdminFrostSupport: RouteCheckers.isAdminFrostSupport(),
        });
    }
};
