import React, { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import StudentDataLayer from "./Components/StudentDataLayer";
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
    console.log("🚀 StudentEntry: Rendering component...");

    // Read student props from DOM
    const propsElement = document.getElementById("student-props");
    let courseAuthId: number | null = null;

    if (propsElement) {
        try {
            const props = JSON.parse(propsElement.textContent || "{}");
            courseAuthId =
                props.course_auth_id || props.selected_course_auth_id || null;
            console.log(
                "🎓 StudentEntry: Read courseAuthId from props:",
                courseAuthId
            );
        } catch (error) {
            console.error("❌ Failed to parse student props:", error);
        }
    }

    return (
        <StudentAppWrapper>
            <StudentErrorBoundary>
                <StudentDataLayer courseAuthId={courseAuthId} />
            </StudentErrorBoundary>
        </StudentAppWrapper>
    );
};

// FIX: Mount immediately if DOM is loaded, or wait if still loading
function mountStudentDashboard() {
    console.log("🚀 Attempting to mount student dashboard...");

    const container = document.getElementById("student-dashboard-container");
    if (!container) {
        console.error("❌ Could not find student-dashboard-container");
        return;
    }

    console.log("✅ Found student container, mounting StudentEntry...");
    const root = createRoot(container);
    root.render(<StudentEntry />);
    console.log("✅ StudentEntry mounted successfully");
}

// Check if DOM is already loaded (most common case when dynamically imported)
if (document.readyState === "loading") {
    console.log("📌 DOM still loading, waiting for DOMContentLoaded...");
    document.addEventListener("DOMContentLoaded", mountStudentDashboard);
} else {
    console.log("📌 DOM already loaded, mounting immediately...");
    // Use setTimeout to ensure React is fully initialized
    setTimeout(mountStudentDashboard, 0);
}

// Export default for module loading
export default StudentEntry;

