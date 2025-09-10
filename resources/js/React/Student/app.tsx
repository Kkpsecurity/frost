// studentEntry.tsx
import React, { ReactNode } from "react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { createRoot } from "react-dom/client";
import StudentErrorBoundary from "./ErrorBoundry/StudentErrorBoundry";
import ClassroomDataLayer from "./ClassroomDataLayer";
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
 * Classroom Entry: providers + error boundary + ClassroomDataLayer.
 * This is the main classroom dashboard interface.
 */
export const ClassroomEntry: React.FC = () => (
    <StudentAppWrapper>
        <StudentErrorBoundary>
            <ClassroomDataLayer />
        </StudentErrorBoundary>
    </StudentAppWrapper>
);

/**
 * Student Entry: providers + error boundary + StudentDataLayer (legacy).
 * This is the demo/development interface.
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
    console.log("🎓 StudentEntry: DOM loaded, looking for containers...");
    console.log("🔍 Current URL:", window.location.href);
    console.log("🔍 Current pathname:", window.location.pathname);

    // Check for classroom dashboard container first
    const classroomContainer = document.getElementById(
        "classroom-dashboard-container"
    );
    const studentContainer = document.getElementById(
        "student-dashboard-container"
    );

    console.log("🔍 Classroom container found:", !!classroomContainer);
    console.log("🔍 Student container found:", !!studentContainer);

    if (classroomContainer) {
        console.log("✅ Found classroom container, mounting ClassroomEntry...");
        const root = createRoot(classroomContainer);
        root.render(<ClassroomEntry />);
        console.log("✅ ClassroomEntry mounted successfully");
    } else if (studentContainer) {
        console.log("✅ Found student container, mounting StudentEntry...");
        const root = createRoot(studentContainer);
        root.render(<StudentEntry />);
        console.log("✅ StudentEntry mounted successfully");
    } else {
        console.log("⚠️ No containers found");
        console.log("🔍 Available elements with 'container' in id:");
        const allContainers = document.querySelectorAll('[id*="container"]');
        allContainers.forEach((el) => console.log("  - Found:", el.id, el));

        // Try again after a short delay in case the DOM isn't fully ready
        setTimeout(() => {
            const delayedClassroomContainer = document.getElementById(
                "classroom-dashboard-container"
            );
            const delayedStudentContainer = document.getElementById(
                "student-dashboard-container"
            );

            if (delayedClassroomContainer) {
                console.log(
                    "✅ Found classroom container (delayed), mounting ClassroomEntry..."
                );
                const root = createRoot(delayedClassroomContainer);
                root.render(<ClassroomEntry />);
                console.log("✅ ClassroomEntry mounted successfully (delayed)");
            } else if (delayedStudentContainer) {
                console.log(
                    "✅ Found student container (delayed), mounting StudentEntry..."
                );
                const root = createRoot(delayedStudentContainer);
                root.render(<StudentEntry />);
                console.log("✅ StudentEntry mounted successfully (delayed)");
            } else {
                console.error(
                    "❌ Could not find any student/classroom containers"
                );
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
    console.log(
        "🎓 StudentEntry: DOM already loaded, looking for containers..."
    );
    const classroomContainer = document.getElementById(
        "classroom-dashboard-container"
    );
    const studentContainer = document.getElementById(
        "student-dashboard-container"
    );

    if (classroomContainer) {
        console.log(
            "✅ Found classroom container (immediate), mounting ClassroomEntry..."
        );
        const root = createRoot(classroomContainer);
        root.render(<ClassroomEntry />);
        console.log("✅ ClassroomEntry mounted successfully (immediate)");
    } else if (studentContainer) {
        console.log(
            "✅ Found student container (immediate), mounting StudentEntry..."
        );
        const root = createRoot(studentContainer);
        root.render(<StudentEntry />);
        console.log("✅ StudentEntry mounted successfully (immediate)");
    }
}
