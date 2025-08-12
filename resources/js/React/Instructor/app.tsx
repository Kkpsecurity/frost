import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

// Instructor Components will go here
import InstructorDashboard from './Components/InstructorDashboard';
import ClassroomManager from './Classroom/ClassroomManager';
import StudentManagement from './Classroom/StudentManagement';
import LiveClassControls from './Classroom/LiveClassControls';

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

// Global interface for Instructor components
interface InstructorComponents {
    InstructorDashboard: typeof InstructorDashboard;
    ClassroomManager: typeof ClassroomManager;
    StudentManagement: typeof StudentManagement;
    LiveClassControls: typeof LiveClassControls;
}

// Wrapper component with QueryClient provider
const InstructorAppWrapper: React.FC<{ children: React.ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === 'development' && <ReactQueryDevtools initialIsOpen={false} />}
    </QueryClientProvider>
);

// Make components available globally for insertion into HTML pages
declare global {
    interface Window {
        InstructorComponents: InstructorComponents;
        renderInstructorComponent: (componentName: keyof InstructorComponents, containerId: string, props?: any) => void;
    }
}

// Function to render instructor components into HTML containers
window.renderInstructorComponent = (componentName: keyof InstructorComponents, containerId: string, props = {}) => {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container with ID "${containerId}" not found`);
        return;
    }

    const Component = window.InstructorComponents[componentName];
    if (!Component) {
        console.error(`Instructor component "${componentName}" not found`);
        return;
    }

    const root = createRoot(container);
    root.render(
        <InstructorAppWrapper>
            {React.createElement(Component as React.ComponentType<any>, props)}
        </InstructorAppWrapper>
    );
};

// Export components globally
window.InstructorComponents = {
    InstructorDashboard,
    ClassroomManager,
    StudentManagement,
    LiveClassControls,
};

console.log('Instructor React components loaded');

// Export queryClient for access in other parts of the app
export { queryClient };
