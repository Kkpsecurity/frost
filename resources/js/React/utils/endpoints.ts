/**
 * Centralized API Endpoints Configuration
 * Global endpoint management for all application modules
 */

// Base URL detection with fallbacks
const getBaseUrl = (): string => {
  if (typeof window !== 'undefined') {
    return `${window.location.protocol}//${window.location.host}`;
  }
  return process.env.REACT_APP_API_BASE_URL || '';
};

// API endpoint configurations organized by module
export const endpoints = {
  laravel: {
    config: `${getBaseUrl()}/api/laravel/config`,
    settings: `${getBaseUrl()}/api/laravel/settings`,
    test: `${getBaseUrl()}/api/laravel/test`,
  },
  instructor: {
    validate: `${getBaseUrl()}/api/instructor/validate`,
    data: {
      classroom: `${getBaseUrl()}/api/instructor/data/classroom`,
      students: `${getBaseUrl()}/api/instructor/data/students`,
    },
  },
  messaging: {
    admin: {
      notifications: `${getBaseUrl()}/api/messaging/admin/notifications`,
      threads: `${getBaseUrl()}/api/messaging/admin/threads`,
    },
  },
  // Add more endpoint categories as needed
  admin: {
    dashboard: `${getBaseUrl()}/api/admin/dashboard`,
    users: `${getBaseUrl()}/api/admin/users`,
  },
  student: {
    profile: `${getBaseUrl()}/api/student/profile`,
    courses: `${getBaseUrl()}/api/student/courses`,
  },
};

export default endpoints;
