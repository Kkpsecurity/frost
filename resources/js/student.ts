/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";

/**
 * Student application entry point
 * Implements route-based component loading for student portal components
 */

import { RouteCheckers, logRouteInfo } from "./React/Shared/Utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Student Portal
 * When on student routes (/classroom, /dashboard)
 */
if (RouteCheckers.isStudentClassroom() || RouteCheckers.isStudentDashboard()) {
    console.log("Loading Student Portal components for student route");
    import("./React/Student/app").catch((err) =>
        console.error("Failed to load Student Portal:", err)
    );
}

console.log("Student.ts loaded for route:", window.location.pathname);


console.log("Student.ts loaded for route:", window.location.pathname);
