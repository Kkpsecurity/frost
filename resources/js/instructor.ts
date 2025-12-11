/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";


/**
 * Instructor application entry point
 * Implements route-based component loading for instructor panel components
 */

import { RouteCheckers, logRouteInfo } from "./React/Shared/Utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Instructor Dashboard
 * When on /admin/instructors route
 */
if (RouteCheckers.isAdminInstructors()) {
    console.log("Loading Instructor Dashboard components for /admin/instructors route");
    import("./React/Instructor/app").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}

console.log("Instructor.ts loaded for route:", window.location.pathname);
