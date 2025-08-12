import React from "react";

/**
 * IInstructor Entry
 * Purpose to load and setup the Tanstack Query
 * Load the InstrcutorDataLayer which will load all api coming from Laravel
 */

import { createRoot } from "react-dom/client";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";

// Instructor Components will go here
import InstructorDashboard from "./Components/InstructorDashboard";

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
}

// Wrapper component with QueryClient provider
const InstructorAppWrapper: React.FC<{ children: React.ReactNode }> = ({
    children,
}) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === "development" && (
            <ReactQueryDevtools initialIsOpen={false} />
        )}
    </QueryClientProvider>
);

// Function to render instructor components into HTML containers
window.renderInstructorComponent = (
    componentName: string,
    containerId: string,
    props = {}
) => {
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
};

console.log("Instructor React components loaded");

// Export queryClient for access in other parts of the app
export { queryClient };
