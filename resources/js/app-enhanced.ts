/**
 * Enhanced main application entry point
 * Uses the new ComponentLoader for better performance and error handling
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';
import { componentLoader, routeBasedLoader } from './utils/componentLoader';

// Load development utilities in development mode
if (process.env.NODE_ENV === 'development') {
    import('./utils/devUtils');
}

// Log route info for debugging
logRouteInfo();

// Initialize component loading
async function initializeApp() {
    console.log('🚀 Initializing student/web application...');

    try {
        // Load components based on current route
        await routeBasedLoader.loadStudentComponents();

        // Log loading statistics
        const stats = componentLoader.getStats();
        console.log('📊 Component loading complete:', stats);

    } catch (error) {
        console.error('❌ Error during app initialization:', error);
    }
}

// Run initialization
initializeApp();

console.log("✅ Enhanced App.ts loaded for route:", window.location.pathname);
