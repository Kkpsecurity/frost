/**
 * Instructor application entry point
 * Implements route-based component loading for instructor panel components
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Instructor Dashboard
 * Only when the url is /instructor/dashboard
 */
if (RouteCheckers.isInstructorDashboard()) {
    require("./React/Instructor/app");
}

/**
 * Load the React components for Classroom Management
 * Only when the url is /instructor/classroom
 */
if (RouteCheckers.isInstructorClassroom()) {
    require("./React/Instructor/Classroom/ClassroomManager");
    require("./React/Instructor/Classroom/InstructorDashboard");
}

/**
 * Load the React components for Student Management
 * Only when the url is /instructor/students
 */
if (RouteCheckers.isInstructorStudents()) {
    require("./React/Instructor/Classroom/StudentManagement");
}

/**
 * Load the React components for Live Class Controls
 * Only when the url contains /live-class/
 */
if (RouteCheckers.isLiveClass()) {
    require("./React/Instructor/Classroom/LiveClassControls");
}

console.log("Instructor.ts loaded for route:", window.location.pathname);
