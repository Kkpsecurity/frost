/**
 * First, we will load all of this project's JavaScript dependencies which
 * includes setting up common functionality like CSRF tokens and axios.
 */

import './bootstrap';

/**
 * Admin application entry point
 * FOR ADMINISTRATORS ONLY - handles /admin/dashboard and general admin functionality
 * Does NOT handle instructor routes (those are in instructor.ts)
 */


console.log("Admin.ts loaded for route:", window.location.pathname);
