/**
 * Instructor application entry point
 * Implements route-based component loading for instructor panel components
 */

import { RouteCheckers, logRouteInfo } from "./React/utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Instructor Dashboard
 * When on /admin/instructors route
 */
if (RouteCheckers.isAdminInstructors()) {
    import("./React/Instructor/app").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}


console.log("Instructor.ts loaded for route:", window.location.pathname);
