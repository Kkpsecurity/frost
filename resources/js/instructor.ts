/**
 * Instructor application entry point
 * Implements route-based component loading for instructor panel components
 */

import { RouteCheckers, logRouteInfo } from "./React/utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Instructor Dashboard
 * When on /instructor/dashboard OR /admin/instructors
 */
if (
    RouteCheckers.isInstructorDashboard() ||
    RouteCheckers.isAdminInstructors()
) {
    import("./React/Instructor/app").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}

/**
 * Load the React components for Classroom Management
 * Only when the url is /instructor/classroom
 */
if (RouteCheckers.isInstructorClassroom()) {
    import("./React/Instructor/Classroom/ClassroomManager").catch((err) =>
        console.error("Failed to load Classroom Manager:", err)
    );
    import("./React/Instructor/Components/InstructorDashboard").catch((err) =>
        console.error("Failed to load Instructor Dashboard:", err)
    );
}

/**
 * Load the React components for Student Management
 * Only when the url is /instructor/students
 */
if (RouteCheckers.isInstructorStudents()) {
    import("./React/Instructor/Classroom/StudentManagement").catch((err) =>
        console.error("Failed to load Student Management:", err)
    );
}

/**
 * Load the React components for Live Class Controls
 * Only when the url contains /live-class/
 */
if (RouteCheckers.isLiveClass()) {
    import("./React/Instructor/Classroom/LiveClassControls").catch((err) =>
        console.error("Failed to load Live Class Controls:", err)
    );
}

console.log("Instructor.ts loaded for route:", window.location.pathname);
