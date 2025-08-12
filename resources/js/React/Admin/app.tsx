import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

// Import Admin Components
import AdminDashboard from './AdminDashboard';

// Create a client for TanStack Query
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5, // 5 minutes
            gcTime: 1000 * 60 * 10, // 10 minutes (formerly cacheTime)
            retry: (failureCount, error: any) => {
                // Don't retry on 4xx errors
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

type AdminComponents = {
    AdminDashboard: React.ComponentType;
};

const components: AdminComponents = {
    AdminDashboard,
};

// Wrapper component with QueryClient provider
const AdminAppWrapper: React.FC<{ children: React.ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === 'development' && <ReactQueryDevtools initialIsOpen={false} />}
    </QueryClientProvider>
);

declare global {
    interface Window {
        renderAdminComponent: (
            componentName: keyof AdminComponents,
            containerId: string,
            props?: any
        ) => void;
    }
}

window.renderAdminComponent = (componentName, containerId, props = {}) => {
    const container = document.getElementById(containerId);
    if (container) {
        const Component = components[componentName];
        if (Component) {
            const root = createRoot(container);
            root.render(
                <AdminAppWrapper>
                    <Component {...props} />
                </AdminAppWrapper>
            );
        } else {
            console.error(`Admin component ${componentName} not found`);
        }
    } else {
        console.error(`Container ${containerId} not found`);
    }
};

export { queryClient };
export default components;
