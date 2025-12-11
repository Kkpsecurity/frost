/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";

/**
 * Main application entry point for general site functionality
 * This file handles common functionality across the entire site
 */

import { RouteCheckers, logRouteInfo } from "./React/Shared/Utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load student-specific React components based on current route
 * This prevents loading unnecessary components on non-student pages
 */
console.log('🎓 Checking route for Student Portal...');
console.log('📍 Current pathname:', window.location.pathname);
console.log('📍 Route segments:', window.location.pathname.split("/").filter(segment => segment !== ""));

const isStudentRoute = RouteCheckers.isClassroomRoute() || RouteCheckers.isStudentOffline() || RouteCheckers.isLessonViewer();
console.log('✅ isStudentRoute() result:', isStudentRoute);

if (isStudentRoute) {
    console.log('🎓 Student route detected, loading React components...');

    // Dynamic import to avoid loading student components on non-student pages
    import('./React/Student/app').then((module) => {
        console.log('✅ Student React app loaded successfully');
    }).catch(error => {
        console.error('❌ Failed to load Student React app:', error);
    });
} else {
    console.log('⚠️ Not a student route, skipping React component loading');
}

console.log("Main app.ts loaded for route:", window.location.pathname);

