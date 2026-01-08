import React, { ReactNode } from "react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { createRoot } from "react-dom/client";
import SupportErrorBoundary from "./ErrorBoundry/SupportErrorBoundry";
import SupportDataLayer from "./SupportDataLayer";

// Direct import instead of lazy loading for testing

/** ---- Error Boundary ---- */
type EBProps = {
    children: ReactNode;
    onError?: (error: unknown, info?: unknown) => void;
};
type EBState = { hasError: boolean; error?: unknown };

/** ---- TanStack Query Setup ---- */
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5,
            gcTime: 1000 * 60 * 10,
            retry: (failureCount, error: any) => {
                if (error?.status >= 400 && error?.status < 500) return false;
                return failureCount < 3;
            },
            refetchOnWindowFocus: false,
        },
        mutations: { retry: 1 },
    },
});

export const SupportAppWrapper: React.FC<{ children: ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
);

/**
 * Root entry: providers + error boundary + DataLayer.
 * No globals, no direct DOM mounting here.
 */
export const SupportEntry: React.FC = () => (
    <SupportAppWrapper>
        <SupportErrorBoundary>
            <SupportDataLayer />
        </SupportErrorBoundary>
    </SupportAppWrapper>
);

export { queryClient, SupportErrorBoundary };

function mountSupportDashboard() {
    const container = document.getElementById("support-dashboard-container");
    if (!container) {
        console.error("❌ Could not find support-dashboard-container");
        return;
    }

    const root = createRoot(container);
    root.render(<SupportEntry />);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountSupportDashboard);
} else {
    setTimeout(mountSupportDashboard, 0);
}

export default SupportEntry;
