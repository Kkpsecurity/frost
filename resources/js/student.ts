/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";

/**
 * Student application entry point
 * FOR STUDENTS ONLY - handles student portal and classroom functionality
 */

// IMMEDIATE DEBUG OUTPUT
console.log("🚀 STUDENT.TS LOADING...");
console.log("🚀 Current URL:", window.location.href);
console.log("🚀 Current pathname:", window.location.pathname);

import { RouteCheckers, logRouteInfo } from "./React/utils/routeUtils";

// Log route info for debugging
logRouteInfo();

// Debug individual route checkers
console.log('🔍 Debugging route checkers:');
console.log('  - isClassroomRoute():', RouteCheckers.isClassroomRoute());
console.log('  - isStudentOffline():', RouteCheckers.isStudentOffline());
console.log('  - isLessonViewer():', RouteCheckers.isLessonViewer());

// Check if we're on any route that should load student components
const isStudentRoute = RouteCheckers.isClassroomRoute() || RouteCheckers.isStudentOffline() || RouteCheckers.isLessonViewer();

// Force load for debugging - remove this later
const forceLoad =
    window.location.pathname === "/classroom" ||
    window.location.pathname === "/classroom/" ||
    window.location.pathname.startsWith("/classroom/");

if (window.location.pathname === "/classroom") {
    console.log("🎓 Detected /classroom route - will load student components");
}

// Dynamic import to avoid loading student components on non-student pages
import("./React/Student/app")
    .then((module) => {
        console.log("✅ Student React app loaded successfully");
    })
    .catch((error) => {
        console.error("❌ Failed to load Student React app:", error);
    });

console.log("Student.ts loaded for route:", window.location.pathname);
