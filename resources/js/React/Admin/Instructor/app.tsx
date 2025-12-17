import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

import EntryErrorBoundary from "./ErrorBoundry/EntryErrorBoundry";
import InstructorDataLayer from "./InstructorDataLayer";

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

export const InstructorEntry: React.FC = () => {
    console.log("🚀 InstructorEntry: Rendering component...");
    return (
        <InstructorAppWrapper>
            <EntryErrorBoundary>
                <InstructorDataLayer />
            </EntryErrorBoundary>
        </InstructorAppWrapper>
    );
};

// FIX: Mount immediately if DOM is loaded, or wait if still loading
function mountInstructorDashboard() {
    console.log("🚀 Attempting to mount instructor dashboard...");

    const container = document.getElementById("instructor-dashboard-container");
    if (!container) {
        console.error("❌ Could not find instructor-dashboard-container");
        return;
    }

    console.log("✅ Found instructor container, mounting InstructorEntry...");
    const root = createRoot(container);
    root.render(<InstructorEntry />);
    console.log("✅ InstructorEntry mounted successfully");
}

// Check if DOM is already loaded (most common case when dynamically imported)
if (document.readyState === "loading") {
    console.log("📌 DOM still loading, waiting for DOMContentLoaded...");
    document.addEventListener("DOMContentLoaded", mountInstructorDashboard);
} else {
    console.log("📌 DOM already loaded, mounting immediately...");
    // Use setTimeout to ensure React is fully initialized
    setTimeout(mountInstructorDashboard, 0);
}

// Export default for module loading
export default InstructorEntry;
