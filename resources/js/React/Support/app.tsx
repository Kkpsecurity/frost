import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

// Import Support Components
import SupportDashboard from './Components/SupportDashboard';
import StudentSearch from './Components/StudentSearch';
import TicketManager from './Components/TicketManager';

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

type SupportComponents = {
    SupportDashboard: React.ComponentType;
    StudentSearch: React.ComponentType;
    TicketManager: React.ComponentType;
};

const components: SupportComponents = {
    SupportDashboard,
    StudentSearch,
    TicketManager,
};

// Wrapper component with QueryClient provider
const SupportAppWrapper: React.FC<{ children: React.ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === 'development' && <ReactQueryDevtools initialIsOpen={false} />}
    </QueryClientProvider>
);

declare global {
    interface Window {
        renderSupportComponent: (
            componentName: keyof SupportComponents,
            containerId: string,
            props?: Record<string, unknown>
        ) => void;
    }
}

window.renderSupportComponent = (componentName, containerId, props = {}) => {
    const container = document.getElementById(containerId);
    const Component = components[componentName];

    if (!container || !Component) {
        console.error(`Invalid container or component: ${containerId} / ${componentName}`);
        return;
    }

    createRoot(container).render(
        <SupportAppWrapper>
            <Component {...props} />
        </SupportAppWrapper>
    );
};

console.log('Support React components loaded');

// Export queryClient for access in other parts of the app
export { queryClient };
export default components;
