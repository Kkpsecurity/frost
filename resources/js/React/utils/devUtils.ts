/**
 * Development utilities for testing route-based component loading
 * Only available in development mode
 */

import { componentLoader } from './componentLoader';
import { RouteCheckers, getRouteSegments } from './routeUtils';

// Development-only utilities
if (process.env.NODE_ENV === 'development') {

    // Add global debugging functions
    (window as any).routeDebug = {
        // Check current route
        currentRoute: () => {
            console.log('Current route:', window.location.pathname);
            console.log('Route segments:', getRouteSegments());

            // Check all route conditions
            const checks = {
                // Admin routes
                isAdminDashboard: RouteCheckers.isAdminDashboard(),
                isAdminMedia: RouteCheckers.isAdminMedia(),
                isAdminSupport: RouteCheckers.isAdminSupport(),
                isAdminInstructors: RouteCheckers.isAdminInstructors(),
                isAdminRoute: RouteCheckers.isAdminRoute(),

                // Instructor routes
                isInstructorDashboard: RouteCheckers.isInstructorDashboard(),
                isInstructorClassroom: RouteCheckers.isInstructorClassroom(),
                isInstructorStudents: RouteCheckers.isInstructorStudents(),
                isInstructorRoute: RouteCheckers.isInstructorRoute(),
                isLiveClass: RouteCheckers.isLiveClass(),

                // Support routes
                isSupportDashboard: RouteCheckers.isSupportDashboard(),
                isSupportTickets: RouteCheckers.isSupportTickets(),
                isSupportStudents: RouteCheckers.isSupportStudents(),
                isSupportRoute: RouteCheckers.isSupportRoute(),

                // Student routes
                isClassroomPortal: RouteCheckers.isClassroomPortal(),
                isClassroomPortalZoom: RouteCheckers.isClassroomPortalZoom(),
                isAccountProfile: RouteCheckers.isAccountProfile(),
                isStudentOffline: RouteCheckers.isStudentOffline(),
                isLessonViewer: RouteCheckers.isLessonViewer(),
            };

            console.table(checks);
            return checks;
        },

        // Check component loading status
        componentStats: () => {
            const stats = componentLoader.getStats();
            console.log('📊 Component Loading Statistics:');
            console.log('Loaded components:', stats.loaded);
            console.log('Currently loading:', stats.loading);
            console.log('Total registered:', stats.total);
            return stats;
        },

        // Simulate route change for testing
        testRoute: (path: string) => {
            console.log(`🧪 Testing route: ${path}`);

            // Store original path
            const originalPath = window.location.pathname;

            // Temporarily change the pathname for testing
            Object.defineProperty(window.location, 'pathname', {
                value: path,
                writable: true
            });

            // Check what would be loaded
            const routeChecks = (window as any).routeDebug.currentRoute();

            // Restore original path
            Object.defineProperty(window.location, 'pathname', {
                value: originalPath,
                writable: true
            });

            return routeChecks;
        },

        // Force load a component for testing
        forceLoadComponent: async (componentKey: string) => {
            console.log(`🔧 Force loading component: ${componentKey}`);
            return await componentLoader.loadComponent(componentKey);
        },

        // Get all available route checkers
        availableCheckers: () => {
            return Object.keys(RouteCheckers);
        }
    };

    // Log available debug functions
    console.log('🛠️ Development utilities loaded:');
    console.log('- window.routeDebug.currentRoute() - Check current route');
    console.log('- window.routeDebug.componentStats() - Component loading stats');
    console.log('- window.routeDebug.testRoute(path) - Test a route path');
    console.log('- window.routeDebug.forceLoadComponent(key) - Force load component');
    console.log('- window.routeDebug.availableCheckers() - List route checkers');
}
