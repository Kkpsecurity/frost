// instructorEntry.tsx
import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

import EntryErrorBoundary from "./ErrorBoundry/EntryErrorBoundry";
import InstructorDashboard from "./Components/InstructorDashboard";

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

export const InstructorAppWrapper: React.FC<{ children: ReactNode }> = ({
    children,
}) => (
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
);

/**
 * Root entry: providers + error boundary + (lazy) DataLayer.
 * No globals, no direct DOM mounting here.
 */
export const InstructorEntry: React.FC = () => (
    <InstructorAppWrapper>
        <EntryErrorBoundary>
            <InstructorDashboard />
        </EntryErrorBoundary>
    </InstructorAppWrapper>
);

export { queryClient, EntryErrorBoundary };

// DOM mounting logic for instructor components
// Auto-mount when this module loads
document.addEventListener("DOMContentLoaded", () => {
    console.log("🚀 InstructorEntry: DOM loaded, looking for container...");

    const container = document.getElementById("instructor-dashboard-container");
    if (container) {
        console.log(
            "✅ Found instructor container, mounting InstructorEntry..."
        );
        const root = createRoot(container);
        root.render(<InstructorEntry />);
        console.log("✅ InstructorEntry mounted successfully");
    } else {
        console.log("⚠️ No instructor container found");
        // Try again after a short delay in case the DOM isn't fully ready
        setTimeout(() => {
            const delayedContainer = document.getElementById(
                "instructor-dashboard-container"
            );
            if (delayedContainer) {
                console.log(
                    "✅ Found instructor container (delayed), mounting InstructorEntry..."
                );
                const root = createRoot(delayedContainer);
                root.render(<InstructorEntry />);
                console.log(
                    "✅ InstructorEntry mounted successfully (delayed)"
                );
            } else {
                console.error(
                    "❌ Could not find instructor-dashboard-container"
                );
            }
        }, 1000);
    }
});

// Also try mounting immediately if DOM is already loaded
if (document.readyState === "loading") {
    // DOM hasn't finished loading yet
} else {
    // DOM has already loaded
    console.log(
        "🚀 InstructorEntry: DOM already loaded, looking for container..."
    );
    const container = document.getElementById("instructor-dashboard-container");
    if (container) {
        console.log(
            "✅ Found instructor container (immediate), mounting InstructorEntry..."
        );
        const root = createRoot(container);
        root.render(<InstructorEntry />);
        console.log("✅ InstructorEntry mounted successfully (immediate)");
    }
}

