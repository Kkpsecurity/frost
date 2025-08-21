/**
 * TanStack Query Configuration
 * Centralized query keys and configuration for React Query
 */

// Query Keys Factory
export const queryKeys = {
  // Student Queries
  student: {
    all: ['student'] as const,
    dashboard: () => [...queryKeys.student.all, 'dashboard'] as const,
    courses: () => [...queryKeys.student.all, 'courses'] as const,
    stats: () => [...queryKeys.student.all, 'stats'] as const,
    recentLessons: () => [...queryKeys.student.all, 'recentLessons'] as const,
    upcomingAssignments: () => [...queryKeys.student.all, 'upcomingAssignments'] as const,
    profile: (id: number) => [...queryKeys.student.all, 'profile', id] as const,
  },
  
  // Instructor Queries
  instructor: {
    all: ['instructor'] as const,
    dashboard: () => [...queryKeys.instructor.all, 'dashboard'] as const,
    courses: () => [...queryKeys.instructor.all, 'courses'] as const,
    bulletin: () => [...queryKeys.instructor.all, 'bulletin'] as const,
    profile: (id: number) => [...queryKeys.instructor.all, 'profile', id] as const,
  },
  
  // Support Queries
  support: {
    all: ['support'] as const,
    dashboard: () => [...queryKeys.support.all, 'dashboard'] as const,
    tickets: () => [...queryKeys.support.all, 'tickets'] as const,
    knowledge: () => [...queryKeys.support.all, 'knowledge'] as const,
  },
  
  // Course Queries
  courses: {
    all: ['courses'] as const,
    list: (filters?: any) => [...queryKeys.courses.all, 'list', filters] as const,
    detail: (id: number) => [...queryKeys.courses.all, 'detail', id] as const,
    enrollment: (id: number) => [...queryKeys.courses.all, 'enrollment', id] as const,
  },
  
  // Common Queries
  announcements: ['announcements'] as const,
  resources: ['resources'] as const,
  notifications: ['notifications'] as const,
} as const;

// Default Query Options
export const defaultQueryOptions = {
  staleTime: 5 * 60 * 1000, // 5 minutes
  cacheTime: 10 * 60 * 1000, // 10 minutes
  retry: 3,
  retryDelay: (attemptIndex: number) => Math.min(1000 * 2 ** attemptIndex, 30000),
  refetchOnWindowFocus: false,
  refetchOnReconnect: true,
};

// Mutation Options
export const defaultMutationOptions = {
  retry: 1,
  onError: (error: any) => {
    console.error('Mutation error:', error);
  },
};

export default {
  queryKeys,
  defaultQueryOptions,
  defaultMutationOptions,
};
