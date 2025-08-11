/**
 * Shared TanStack Query configuration for all React apps
 * Provides consistent query client setup across Admin, Student, Instructor, and Support apps
 */

import { QueryClient } from '@tanstack/react-query';

// Default query client configuration
export const defaultQueryClientOptions = {
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5, // 5 minutes - data stays fresh for 5 minutes
            gcTime: 1000 * 60 * 10, // 10 minutes - cache garbage collection time (formerly cacheTime)
            retry: (failureCount: number, error: any) => {
                // Don't retry on 4xx client errors (authentication, authorization, validation, etc.)
                if (error?.status >= 400 && error?.status < 500) {
                    return false;
                }
                // Retry up to 3 times for other errors (network, 5xx server errors)
                return failureCount < 3;
            },
            refetchOnWindowFocus: false, // Don't refetch when window regains focus
            refetchOnReconnect: true,    // Refetch when network reconnects
            refetchOnMount: true,        // Refetch when component mounts
        },
        mutations: {
            retry: 1, // Retry mutations only once
            onError: (error: any) => {
                // Global error handling for mutations
                console.error('Mutation error:', error);

                // You can add global error notifications here
                if (typeof window !== 'undefined' && (window as any).showNotification) {
                    (window as any).showNotification({
                        type: 'error',
                        message: error?.message || 'An error occurred while processing your request'
                    });
                }
            },
        },
    },
};

// Factory function to create a new QueryClient instance
export function createQueryClient(): QueryClient {
    return new QueryClient(defaultQueryClientOptions);
}

// Query keys factory for consistent key naming across apps
export const queryKeys = {
    // Student queries
    student: {
        dashboard: () => ['student', 'dashboard'] as const,
        stats: () => ['student', 'stats'] as const,
        lessons: () => ['student', 'lessons'] as const,
        lesson: (id: string | number) => ['student', 'lessons', id] as const,
        recentLessons: () => ['student', 'recentLessons'] as const,
        assignments: () => ['student', 'assignments'] as const,
        assignment: (id: string | number) => ['student', 'assignments', id] as const,
        upcomingAssignments: () => ['student', 'upcomingAssignments'] as const,
        progress: () => ['student', 'progress'] as const,
    },

    // Admin queries
    admin: {
        dashboard: () => ['admin', 'dashboard'] as const,
        users: () => ['admin', 'users'] as const,
        user: (id: string | number) => ['admin', 'users', id] as const,
        courses: () => ['admin', 'courses'] as const,
        course: (id: string | number) => ['admin', 'courses', id] as const,
        media: () => ['admin', 'media'] as const,
        reports: () => ['admin', 'reports'] as const,
    },

    // Instructor queries
    instructor: {
        dashboard: () => ['instructor', 'dashboard'] as const,
        stats: () => ['instructor', 'stats'] as const,
        classes: () => ['instructor', 'classes'] as const,
        class: (id: string | number) => ['instructor', 'classes', id] as const,
        upcomingClasses: () => ['instructor', 'upcomingClasses'] as const,
        students: () => ['instructor', 'students'] as const,
        student: (id: string | number) => ['instructor', 'students', id] as const,
        lessons: () => ['instructor', 'lessons'] as const,
        lesson: (id: string | number) => ['instructor', 'lessons', id] as const,
    },

    // Support queries
    support: {
        dashboard: () => ['support', 'dashboard'] as const,
        stats: () => ['support', 'stats'] as const,
        tickets: () => ['support', 'tickets'] as const,
        ticket: (id: string | number) => ['support', 'tickets', id] as const,
        recentTickets: () => ['support', 'recentTickets'] as const,
        students: () => ['support', 'students'] as const,
        student: (id: string | number) => ['support', 'students', id] as const,
    },

    // Shared queries (used across multiple apps)
    shared: {
        notifications: () => ['notifications'] as const,
        profile: () => ['profile'] as const,
        settings: () => ['settings'] as const,
    },
};

// Mutation keys for consistent naming
export const mutationKeys = {
    // Student mutations
    student: {
        updateProfile: () => ['student', 'updateProfile'] as const,
        submitAssignment: () => ['student', 'submitAssignment'] as const,
        enrollCourse: () => ['student', 'enrollCourse'] as const,
    },

    // Admin mutations
    admin: {
        createUser: () => ['admin', 'createUser'] as const,
        updateUser: () => ['admin', 'updateUser'] as const,
        deleteUser: () => ['admin', 'deleteUser'] as const,
        createCourse: () => ['admin', 'createCourse'] as const,
        updateCourse: () => ['admin', 'updateCourse'] as const,
        uploadMedia: () => ['admin', 'uploadMedia'] as const,
    },

    // Instructor mutations
    instructor: {
        createLesson: () => ['instructor', 'createLesson'] as const,
        updateLesson: () => ['instructor', 'updateLesson'] as const,
        gradeAssignment: () => ['instructor', 'gradeAssignment'] as const,
    },

    // Support mutations
    support: {
        createTicket: () => ['support', 'createTicket'] as const,
        updateTicket: () => ['support', 'updateTicket'] as const,
        resolveTicket: () => ['support', 'resolveTicket'] as const,
    },
};

// Utility functions for query invalidation
export const queryUtils = {
    // Invalidate all queries for a specific app
    invalidateApp: (queryClient: QueryClient, app: 'student' | 'admin' | 'instructor' | 'support') => {
        queryClient.invalidateQueries({ queryKey: [app] });
    },

    // Invalidate specific query
    invalidateQuery: (queryClient: QueryClient, queryKey: readonly unknown[]) => {
        queryClient.invalidateQueries({ queryKey });
    },

    // Prefetch query
    prefetchQuery: async (queryClient: QueryClient, queryKey: readonly unknown[], queryFn: () => Promise<any>) => {
        await queryClient.prefetchQuery({
            queryKey,
            queryFn,
            staleTime: 1000 * 60 * 5, // 5 minutes
        });
    },
};

// Development helpers
export const devTools = {
    // Log all queries and their status
    logQueries: (queryClient: QueryClient) => {
        if (process.env.NODE_ENV === 'development') {
            const cache = queryClient.getQueryCache();
            const queries = cache.getAll();

            console.log('📊 TanStack Query Cache Status:');
            console.table(queries.map(query => ({
                key: JSON.stringify(query.queryKey),
                status: query.state.status,
                stale: query.isStale(),
                fetchStatus: query.state.fetchStatus,
                dataUpdatedAt: new Date(query.state.dataUpdatedAt).toLocaleTimeString(),
            })));
        }
    },

    // Clear all caches (useful for development)
    clearAllCaches: (queryClient: QueryClient) => {
        if (process.env.NODE_ENV === 'development') {
            queryClient.clear();
            console.log('🧹 All TanStack Query caches cleared');
        }
    },
};

// Make dev tools available globally in development
if (process.env.NODE_ENV === 'development' && typeof window !== 'undefined') {
    (window as any).tanstackDevTools = devTools;
    console.log('🛠️ TanStack Query dev tools available at window.tanstackDevTools');
}
