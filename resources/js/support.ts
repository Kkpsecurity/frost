/**
 * Support application entry point
 * Implements route-based component loading for support panel components
 */

import { RouteCheckers, logRouteInfo } from './utils/routeUtils';

// Log route info for debugging
logRouteInfo();

/**
 * Load the React components for the Support Dashboard
 * Only when the url is /support/dashboard
 */
if (RouteCheckers.isSupportDashboard()) {
    require("./React/Support/app");
}

/**
 * Load the React components for Ticket Manager
 * Only when the url is /support/tickets
 */
if (RouteCheckers.isSupportTickets()) {
    require("./React/Support/Components/TicketManager");
}

/**
 * Load the React components for Student Search
 * Only when the url is /support/students
 */
if (RouteCheckers.isSupportStudents()) {
    require("./React/Support/Components/StudentSearch");
}

console.log("Support.ts loaded for route:", window.location.pathname);
