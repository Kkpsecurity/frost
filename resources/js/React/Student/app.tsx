import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import StudentDataLayer from "./StudentDataLayer";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";

// /** ---- TanStack Query Setup ---- */
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5,
            gcTime: 1000 * 60 * 10,
            retry: (failureCount, error) => {
                // Handle case where error might be undefined
                if (!error) return failureCount < 3;

                // Check if error has status property
                const errorStatus =
                    (error as any)?.status || (error as any)?.response?.status;
                if (errorStatus >= 400 && errorStatus < 500) return false;

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

// Main Student App Entry - handles providers and error boundary only
export const StudentEntry: React.FC = () => {
    console.log("🎓 StudentEntry: Initializing React Query providers...");

    return (
        <StudentAppWrapper>
            <StudentErrorBoundary>
                <StudentDataLayer />
            </StudentErrorBoundary>
        </StudentAppWrapper>
    );
};

// DOM mounting logic
document.addEventListener("DOMContentLoaded", () => {
    console.log("🎓 StudentEntry: DOM loaded, looking for containers...");

    const studentContainer = document.getElementById(
        "student-dashboard-container"
    );
    console.log("🔍 Student container found:", !!studentContainer);

    if (studentContainer) {
        console.log("✅ Found student container, mounting StudentEntry...");
        const root = createRoot(studentContainer);
        root.render(<StudentEntry />);
        console.log("✅ StudentEntry mounted successfully");
    } else {
        console.log("❌ Could not find student container");
    }
});
