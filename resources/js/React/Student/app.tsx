// studentEntry.tsx
import React, { ReactNode } from "react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import StudentDataLayer from "./StudentDataLayer";

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

export const StudentAppWrapper: React.FC<{ children: ReactNode }> = ({
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
export const StudentEntry: React.FC = () => (
    <StudentAppWrapper>
        <StudentErrorBoundary>
            <StudentDataLayer />
        </StudentErrorBoundary>
    </StudentAppWrapper>
);

export { queryClient, StudentErrorBoundary };

// DOM mounting logic for student components
document.addEventListener("DOMContentLoaded", () => {
    console.log("🎓 StudentEntry: DOM loaded, looking for container...");
    console.log("🔍 Current URL:", window.location.href);
    console.log("🔍 Current pathname:", window.location.pathname);

    const container = document.getElementById("student-dashboard-container");
    console.log("🔍 Container found:", !!container);
    console.log("🔍 Container element:", container);
    
    if (container) {
        console.log("✅ Found student container, mounting StudentEntry...");
        const root = createRoot(container);
        root.render(<StudentEntry />);
        console.log("✅ StudentEntry mounted successfully");
    } else {
        console.log("⚠️ No student container found");
        console.log("🔍 Available elements with 'container' in id:");
        const allContainers = document.querySelectorAll('[id*="container"]');
        allContainers.forEach((el) => console.log("  - Found:", el.id, el));
        
        // Try again after a short delay in case the DOM isn't fully ready
        setTimeout(() => {
            const delayedContainer = document.getElementById(
                "student-dashboard-container"
            );
            if (delayedContainer) {
                console.log("✅ Found student container (delayed), mounting StudentEntry...");
                const root = createRoot(delayedContainer);
                root.render(<StudentEntry />);
                console.log("✅ StudentEntry mounted successfully (delayed)");
            } else {
                console.error("❌ Could not find student-dashboard-container");
                console.log("🔍 All elements with id attribute:");
                const allElements = document.querySelectorAll("[id]");
                allElements.forEach((el) => console.log("  - ID:", el.id));
            }
        }, 1000);
    }
});

// Also try mounting immediately if DOM is already loaded
if (document.readyState === "loading") {
    // DOM hasn't finished loading yet
} else {
    // DOM has already loaded
    console.log("🎓 StudentEntry: DOM already loaded, looking for container...");
    const container = document.getElementById("student-dashboard-container");
    if (container) {
        console.log("✅ Found student container (immediate), mounting StudentEntry...");
        const root = createRoot(container);
        root.render(<StudentEntry />);
        console.log("✅ StudentEntry mounted successfully (immediate)");
    }
}
