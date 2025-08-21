/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";

/**
 * Support application entry point
 * FOR SUPPORT STAFF ONLY - handles support dashboard and functionality
 */

import { RouteCheckers, logRouteInfo } from "./React/utils/routeUtils";

// Log route info for debugging
logRouteInfo();

/**
 * Load support-specific React components based on current route
 * This prevents loading unnecessary components on non-support pages
 */
console.log('🔍 Checking route for Frost Support...');
console.log('📍 Current pathname:', window.location.pathname);
console.log('📍 Route segments:', window.location.pathname.split("/").filter(segment => segment !== ""));

const isFrostSupportRoute = RouteCheckers.isAdminFrostSupport();
console.log('✅ isAdminFrostSupport() result:', isFrostSupportRoute);

if (isFrostSupportRoute) {
    console.log('🔧 Frost Support route detected, loading React components...');

    // Dynamic import to avoid loading support components on non-support pages
    import('./React/Support/app').then((module) => {
        console.log('✅ Support React app loaded successfully');
    }).catch(error => {
        console.error('❌ Failed to load Support React app:', error);
    });
} else {
    console.log('⚠️ Not a Frost Support route, skipping React component loading');
}

console.log("Support.ts loaded for route:", window.location.pathname);
