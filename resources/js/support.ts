/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import "./core/bootstrap";

/**
 * Support application entry point
 * FOR SUPPORT STAFF ONLY - handles support dashboard and functionality
 */

import { RouteCheckers, logRouteInfo } from "./React/Shared/Utils/routeUtils";

// Log route info for debugging
logRouteInfo();

// Load Support SPA only on support routes
if (RouteCheckers.isAdminFrostSupport()) {
    console.log(
        "Loading Support components for /admin/frost-support route"
    );
    import("./React/Support/app").catch((err) =>
        console.error("Failed to load Support app:", err)
    );
}

console.log("Support.ts loaded for route:", window.location.pathname);
