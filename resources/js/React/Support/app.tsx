// supportEntry.tsx
import React, { ReactNode } from "react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
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

export const SupportAppWrapper: React.FC<{ children: ReactNode }> = ({
    children,
}) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === "development" && (
            <ReactQueryDevtools initialIsOpen={false} />
        )}
    </QueryClientProvider>
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

// DOM mounting logic for support components
document.addEventListener("DOMContentLoaded", () => {
    console.log("🚀 SupportEntry: DOM loaded, looking for container...");
    const container = document.getElementById("support-dashboard-container");
    if (container) {
        console.log("✅ Found support container, mounting SupportEntry...");
        const root = createRoot(container);
        root.render(<SupportEntry />);
        console.log("✅ SupportEntry mounted successfully");
    } else {
        console.log("⚠️ No support container found");
        // Try again after a short delay in case the DOM isn't fully ready
        setTimeout(() => {
            const delayedContainer = document.getElementById(
                "support-dashboard-container"
            );
            if (delayedContainer) {
                console.log("✅ Found support container (delayed), mounting SupportEntry...");
                const root = createRoot(delayedContainer);
                root.render(<SupportEntry />);
                console.log("✅ SupportEntry mounted successfully (delayed)");
            } else {
                console.error("❌ Could not find support-dashboard-container");
            }
        }, 1000);
    }
});

// Also try mounting immediately if DOM is already loaded
if (document.readyState === "loading") {
    // DOM hasn't finished loading yet
} else {
    // DOM has already loaded
    console.log("🚀 SupportEntry: DOM already loaded, looking for container...");
    const container = document.getElementById("support-dashboard-container");
    if (container) {
        console.log("✅ Found support container (immediate), mounting SupportEntry...");
        const root = createRoot(container);
        root.render(<SupportEntry />);
        console.log("✅ SupportEntry mounted successfully (immediate)");
    }
}
