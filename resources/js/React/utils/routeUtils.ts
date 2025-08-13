/**
 * Route utilities for conditional component loading
 * Provides helper functions to check current route segments
 */

/**
 * Get the current route segments as an array
 * @returns {string[]} Array of route segments
 */
export function getRouteSegments(): string[] {
    return window.location.pathname.split("/").filter(segment => segment !== "");
}

/**
 * Check if the current route matches a specific pattern
 * @param {string[]} expectedSegments - Expected route segments
 * @returns {boolean} True if route matches
 */
export function isRoute(expectedSegments: string[]): boolean {
    const currentSegments = getRouteSegments();

    if (currentSegments.length !== expectedSegments.length) {
        return false;
    }

    return expectedSegments.every((segment, index) =>
        segment === "*" || currentSegments[index] === segment
    );
}

/**
 * Check if the current route starts with specific segments
 * @param {string[]} expectedSegments - Expected starting segments
 * @returns {boolean} True if route starts with these segments
 */
export function routeStartsWith(expectedSegments: string[]): boolean {
    const currentSegments = getRouteSegments();

    if (currentSegments.length < expectedSegments.length) {
        return false;
    }

    return expectedSegments.every((segment, index) =>
        segment === "*" || currentSegments[index] === segment
    );
}

/**
 * Check if the current route contains a specific segment
 * @param {string} segment - Segment to look for
 * @returns {boolean} True if route contains the segment
 */
export function routeContains(segment: string): boolean {
    return getRouteSegments().includes(segment);
}

/**
 * Log current route information for debugging
 */
export function logRouteInfo(): void {
    console.log("Current route:", window.location.pathname);
    console.log("Route segments:", getRouteSegments());
}

// Export commonly used route checkers
export const RouteCheckers = {
    // Admin routes
    isAdminDashboard: () => isRoute(["admin", "dashboard"]),
    isAdminMedia: () => isRoute(["admin", "media"]),
    isAdminSupport: () => isRoute(["admin", "support"]),
    isAdminInstructors: () => isRoute(["admin", "instructors"]),
    isAdminRoute: () => routeStartsWith(["admin"]),

    // Instructor routes
    isInstructorDashboard: () => isRoute(["instructor", "dashboard"]),
    isInstructorClassroom: () => isRoute(["instructor", "classroom"]),
    isInstructorStudents: () => isRoute(["instructor", "students"]),
    isInstructorRoute: () => routeStartsWith(["instructor"]),
    isLiveClass: () => routeContains("live-class"),

    // Support routes
    isSupportDashboard: () => isRoute(["support", "dashboard"]),
    isSupportTickets: () => isRoute(["support", "tickets"]),
    isSupportStudents: () => isRoute(["support", "students"]),
    isSupportRoute: () => routeStartsWith(["support"]),

    // Student routes
    isClassroomPortal: () => isRoute(["classroom", "portal"]),
    isClassroomPortalZoom: () => isRoute(["classroom", "portal", "zoom"]),
    isClassroomDefault: () => isRoute(["classroom"]),
    isClassroomRoute: () => routeStartsWith(["classroom"]),
    isAccountProfile: () => isRoute(["account", "profile"]),
    isStudentOffline: () => isRoute(["student", "offline"]),
    isLessonViewer: () => routeContains("lesson"),
};
