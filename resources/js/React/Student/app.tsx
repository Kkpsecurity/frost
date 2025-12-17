import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import StudentDataLayer from "./StudentDataLayer";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5,
            gcTime: 1000 * 60 * 10,
            retry: (failureCount, error) => {
                if (!error) return failureCount < 3;
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
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
);

// Main Student App Entry - loads StudentDataLayer
export const StudentEntry: React.FC = () => {
    console.log("🎓 StudentEntry: Mounting with StudentDataLayer...");
    return (
        <StudentAppWrapper>
            <StudentErrorBoundary>
                <StudentDataLayer />
            </StudentErrorBoundary>
        </StudentAppWrapper>
    );
};

// Mount student dashboard when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    console.log("🎓 StudentEntry: DOM loaded, mounting dashboard...");

    const container = document.getElementById("student-dashboard-container");
    if (container) {
        console.log("✅ Found student container, mounting StudentEntry...");
        const root = createRoot(container);
        root.render(<StudentEntry />);
    } else {
        console.log("❌ Could not find student-dashboard-container");
    }
});
