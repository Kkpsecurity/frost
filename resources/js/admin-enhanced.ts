/**
 * Enhanced admin application entry point
 * Uses the new ComponentLoader for better performance and error handling
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';
import { componentLoader, routeBasedLoader } from './utils/componentLoader';

// Log route info for debugging
logRouteInfo();

// Initialize admin app
async function initializeAdminApp() {
    console.log('🚀 Initializing admin application...');

    try {
        // Load admin-specific components
        await routeBasedLoader.loadAdminComponents();

        // Also load support components if on admin/support route
        if (RouteCheckers.isAdminSupport()) {
            await routeBasedLoader.loadSupportComponents();
        }

        // Also load instructor components if on admin/instructors route
        if (RouteCheckers.isAdminInstructors()) {
            await routeBasedLoader.loadInstructorComponents();
        }

        // Log loading statistics
        const stats = componentLoader.getStats();
        console.log('📊 Admin component loading complete:', stats);

    } catch (error) {
        console.error('❌ Error during admin app initialization:', error);
    }
}

// Run initialization
initializeAdminApp();

console.log("✅ Enhanced Admin.ts loaded for route:", window.location.pathname);
