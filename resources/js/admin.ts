/**
 * Admin application entry point
 * Implements route-based component loading for admin panel components
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Admin Dashboard
 * Only when the url is /admin/dashboard
 */
if (RouteCheckers.isAdminDashboard()) {
    require("./React/Admin/app");
}

/**
 * Load the React components for Media Manager
 * Only when the url is /admin/media
 */
if (RouteCheckers.isAdminMedia()) {
    require("./React/Admin/MediaManager/AdvancedUploadModal");
}

/**
 * Load the React components for Support Dashboard
 * Only when the url is /admin/support
 */
if (RouteCheckers.isAdminSupport()) {
    require("./React/Support/app");
}

/**
 * Load the React components for Instructor Dashboard
 * Only when the url is /admin/instructors
 */
if (RouteCheckers.isAdminInstructors()) {
    require("./React/Instructor/app");
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
