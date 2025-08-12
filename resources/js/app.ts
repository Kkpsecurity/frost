/**
 * Main application entry point for student/web components
 * Implements route-based component loading to optimize performance
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Student Classroom Dashboard
 * Only when the url is /classroom/
 */
if (RouteCheckers.isClassroomDefault()) {
    require("./React/Student/app");
}

/**
 * Load the React components for the Student Portal
 * Only when the url is /classroom/portal
 */
if (RouteCheckers.isClassroomPortal()) {
    require("./React/Student/app");
}

/**
 * Load the React components for Zoom screen sharing
 * Only when the url is /classroom/portal/zoom
 */
if (RouteCheckers.isClassroomPortalZoom()) {
    require("./React/Student/Components/VideoPlayer");
}

/**
 * Load the React components for the account profile dashboard
 * Only when the url is /account/profile
 */
if (RouteCheckers.isAccountProfile()) {
    require("./React/Student/StudentDashboard");
}

/**
 * Load the React components for offline assignment submission
 * Only when the url is /student/offline
 */
if (RouteCheckers.isStudentOffline()) {
    require("./React/Student/Offline/AssignmentSubmission");
}

/**
 * Load the React components for lesson viewer
 * Only when the url contains /lesson/
 */
if (RouteCheckers.isLessonViewer()) {
    require("./React/Student/Components/LessonViewer");
}

console.log("App.ts loaded for route:", window.location.pathname);
