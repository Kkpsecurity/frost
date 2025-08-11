import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

// Student Components will go here
import StudentDashboard from './Components/StudentDashboard';
import LessonViewer from './Components/LessonViewer';
import VideoPlayer from './Components/VideoPlayer';
import AssignmentSubmission from './Offline/AssignmentSubmission';

// Create a client for TanStack Query
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5, // 5 minutes
            gcTime: 1000 * 60 * 10, // 10 minutes
            retry: (failureCount, error: any) => {
                if (error?.status >= 400 && error?.status < 500) {
                    return false;
                }
                return failureCount < 3;
            },
        },
        mutations: {
            retry: 1,
        },
    },
});

// Global interface for Student components
interface StudentComponents {
    StudentDashboard: typeof StudentDashboard;
    LessonViewer: typeof LessonViewer;
    VideoPlayer: typeof VideoPlayer;
    AssignmentSubmission: typeof AssignmentSubmission;
}

// Wrapper component with QueryClient provider
const StudentAppWrapper: React.FC<{ children: React.ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === 'development' && <ReactQueryDevtools initialIsOpen={false} />}
    </QueryClientProvider>
);

// Make components available globally for insertion into HTML pages
declare global {
    interface Window {
        StudentComponents: StudentComponents;
        renderStudentComponent: (componentName: keyof StudentComponents, containerId: string, props?: any) => void;
    }
}

// Function to render student components into HTML containers
window.renderStudentComponent = (componentName: keyof StudentComponents, containerId: string, props = {}) => {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container with ID "${containerId}" not found`);
        return;
    }

    const Component = window.StudentComponents[componentName];
    if (!Component) {
        console.error(`Student component "${componentName}" not found`);
        return;
    }

    const root = createRoot(container);
    root.render(
        <StudentAppWrapper>
            {React.createElement(Component as React.ComponentType<any>, props)}
        </StudentAppWrapper>
    );
};

// Export components globally
window.StudentComponents = {
    StudentDashboard,
    LessonViewer,
    VideoPlayer,
    AssignmentSubmission,
};

console.log('Student React components loaded');

// Export queryClient for access in other parts of the app
export { queryClient };
