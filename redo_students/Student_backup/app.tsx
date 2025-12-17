import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import StudentDataLayer from "./StudentDataLayer";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";

/**
 * ReactQuery Configuration
 */
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

/**
 * App Wrapper - Provides ReactQuery Context
 */
const StudentAppWrapper: React.FC<{ children: ReactNode }> = ({ children }) => (
    <QueryClientProvider client={queryClient}>
        {children}
        {process.env.NODE_ENV === "development" && (
            <ReactQueryDevtools initialIsOpen={false} />
        )}
    </QueryClientProvider>
);

/**
 * Main App Component - Setup providers and mount StudentDataLayer
 */
const StudentApp: React.FC<{ courseAuthId?: number | null }> = ({
    courseAuthId,
}) => (
    <StudentAppWrapper>
        <StudentErrorBoundary>
            <StudentDataLayer courseAuthId={courseAuthId} />
        </StudentErrorBoundary>
    </StudentAppWrapper>
);

/**
 * DOM Mounting - Read courseAuthId from props and mount app
 */
document.addEventListener("DOMContentLoaded", () => {
    const studentContainer = document.getElementById(
        "student-dashboard-container"
    );

    if (!studentContainer) {
        console.error("❌ Student dashboard container not found");
        return;
    }

    // Read course_auth_id from student-props script tag
    let courseAuthId: number | null = null;
    const studentPropsElement = document.getElementById("student-props");

    if (studentPropsElement?.textContent) {
        try {
            const propsData = JSON.parse(studentPropsElement.textContent);
            courseAuthId = propsData.course_auth_id || null;
        } catch (e) {
            console.error("❌ Failed to parse student props:", e);
        }
    }

    // Mount the app
    const root = createRoot(studentContainer);
    root.render(<StudentApp courseAuthId={courseAuthId} />);
});
