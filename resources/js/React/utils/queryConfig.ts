/**
 * TanStack Query Configuration & Utilities
 * Centralized query keys and utilities for consistent caching
 */

// Query key factory for organized cache management
export const queryKeys = {
  // Laravel core
  laravel: {
    config: () => ['laravel', 'config'] as const,
    settings: () => ['laravel', 'settings'] as const,
  },

  // Admin queries
  admin: {
    dashboard: () => ['admin', 'dashboard'] as const,
    users: () => ['admin', 'users'] as const,
    user: (id: string | number) => ['admin', 'users', id] as const,
    config: () => ['admin', 'config'] as const,
  },

  // Instructor queries
  instructor: {
    dashboard: () => ['instructor', 'dashboard'] as const,
    validation: () => ['instructor', 'validation'] as const,
    classroom: () => ['instructor', 'classroom'] as const,
    students: () => ['instructor', 'students'] as const,
    classes: () => ['instructor', 'classes'] as const,
    student: (id: string | number) => ['instructor', 'students', id] as const,
  },

  // Student queries
  student: {
    dashboard: () => ['student', 'dashboard'] as const,
    stats: () => ['student', 'stats'] as const,
    profile: () => ['student', 'profile'] as const,
    lessons: () => ['student', 'lessons'] as const,
    lesson: (id: string | number) => ['student', 'lessons', id] as const,
    recentLessons: () => ['student', 'lessons', 'recent'] as const,
    classroom: () => ['student', 'classroom'] as const,
    assignments: () => ['student', 'assignments'] as const,
    upcomingAssignments: () => ['student', 'assignments', 'upcoming'] as const,
  },

  // Support queries
  support: {
    dashboard: () => ['support', 'dashboard'] as const,
    stats: () => ['support', 'stats'] as const,
    tickets: () => ['support', 'tickets'] as const,
    ticket: (id: string | number) => ['support', 'tickets', id] as const,
    recentTickets: () => ['support', 'tickets', 'recent'] as const,
    students: () => ['support', 'students'] as const,
    student: (id: string | number) => ['support', 'students', id] as const,
  },

  // Course management
  courses: {
    list: () => ['courses'] as const,
    course: (id: string | number) => ['courses', id] as const,
    dates: () => ['courses', 'dates'] as const,
    units: (courseId: string | number) => ['courses', courseId, 'units'] as const,
  },

  // Messaging
  messaging: {
    notifications: () => ['messaging', 'notifications'] as const,
    threads: () => ['messaging', 'threads'] as const,
    thread: (id: string | number) => ['messaging', 'threads', id] as const,
  },

  // Shared/Global queries
  shared: {
    profile: () => ['profile'] as const,
    notifications: () => ['notifications'] as const,
    settings: () => ['settings'] as const,
  },
} as const;

// Query utilities for common operations
export const queryUtils = {
  /**
   * Prefetch a query using the query client
   */
  prefetchQuery: async (
    queryClient: any,
    queryKey: readonly unknown[],
    queryFn: () => Promise<any>,
    staleTime = 5 * 60 * 1000 // 5 minutes default
  ) => {
    return queryClient.prefetchQuery({
      queryKey,
      queryFn,
      staleTime,
    });
  },

  /**
   * Invalidate queries by pattern
   */
  invalidateQueries: (queryClient: any, pattern: readonly unknown[]) => {
    return queryClient.invalidateQueries({
      queryKey: pattern,
    });
  },

  /**
   * Remove queries from cache
   */
  removeQueries: (queryClient: any, pattern: readonly unknown[]) => {
    return queryClient.removeQueries({
      queryKey: pattern,
    });
  },

  /**
   * Set query data programmatically
   */
  setQueryData: (queryClient: any, queryKey: readonly unknown[], data: any) => {
    return queryClient.setQueryData(queryKey, data);
  },

  /**
   * Get query data from cache
   */
  getQueryData: (queryClient: any, queryKey: readonly unknown[]) => {
    return queryClient.getQueryData(queryKey);
  },
};

// Common query options
export const queryOptions = {
  // For frequently changing data (real-time updates)
  realtime: {
    staleTime: 0,
    gcTime: 1000 * 60, // 1 minute
    refetchInterval: 30 * 1000, // 30 seconds
    refetchOnWindowFocus: true,
  },

  // For moderately changing data (user profiles, course data)
  moderate: {
    staleTime: 2 * 60 * 1000, // 2 minutes
    gcTime: 10 * 60 * 1000, // 10 minutes
    refetchOnWindowFocus: false,
  },

  // For rarely changing data (config, settings)
  stable: {
    staleTime: 10 * 60 * 1000, // 10 minutes
    gcTime: 30 * 60 * 1000, // 30 minutes
    refetchOnWindowFocus: false,
  },

  // For static content (rarely/never changes)
  static: {
    staleTime: 60 * 60 * 1000, // 1 hour
    gcTime: 24 * 60 * 60 * 1000, // 24 hours
    refetchOnWindowFocus: false,
    refetchOnMount: false,
  },
};

// Error handling utilities
export const queryErrorHandlers = {
  /**
   * Standard retry function for queries
   */
  defaultRetry: (failureCount: number, error: any) => {
    // Don't retry on auth errors
    if (error?.status >= 400 && error?.status < 500) {
      return false;
    }
    // Retry up to 3 times for other errors
    return failureCount < 3;
  },

  /**
   * Exponential backoff for retry delays
   */
  retryDelay: (attemptIndex: number) => {
    return Math.min(1000 * 2 ** attemptIndex, 30000);
  },
};

export default queryKeys;
