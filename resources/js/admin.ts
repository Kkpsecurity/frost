/**
 * Admin application entry point
 * Implements route-based component loading for admin panel components
 */

import { RouteCheckers, logRouteInfo } from "./React/utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Admin Dashboard
 * Only when the url is /admin/dashboard
 */
if (RouteCheckers.isAdminDashboard()) {
    import("./React/Admin/app").catch((err) =>
        console.error("Failed to load Admin Dashboard:", err)
    );
}

/**
 * Load the React components for the Instructor Dashboard
 * Only when the url is /admin/instructors
 */
if (RouteCheckers.isAdminInstructors()) {
    import("./React/Instructor/app").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}

/**
 * Load the React components for Media Manager
 * Only when the url is /admin/media
 */
if (RouteCheckers.isAdminMedia()) {
    import("./React/Admin/MediaManager/AdvancedUploadModal").catch((err) =>
        console.error("Failed to load Media Manager:", err)
    );
}

/**
 * Load the React components for Support Dashboard
 * Only when the url is /admin/support
 */
if (RouteCheckers.isAdminSupport()) {
    import("./React/Support/app").catch((err) =>
        console.error("Failed to load Support Dashboard:", err)
    );
}

/**
 * Load the React components for Instructor Dashboard
 * Only when the url is /admin/instructors
 */
if (RouteCheckers.isAdminInstructors()) {
    import("./React/Instructor/app").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}

/**
 * Load any admin component when accessing admin routes
 * Fallback for general admin pages
 */
if (RouteCheckers.isAdminRoute()) {
    // Load general admin utilities if not already loaded by specific routes
    console.log("Admin area accessed:", window.location.pathname);
}

console.log("Admin.ts loaded for route:", window.location.pathname);
