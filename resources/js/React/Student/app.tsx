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

// Face Verification Component Import
import FaceVerificationPanel from "./Components/Onboarding/FaceVerification/FaceVerificationPanel";

// DOM mounting logic
document.addEventListener("DOMContentLoaded", () => {
    console.log("🎓 StudentEntry: DOM loaded, looking for containers...");

    // Mount main student dashboard
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

    // Mount Face Verification Panel
    const faceVerificationContainer = document.getElementById(
        "face-verification-container"
    );
    console.log(
        "🔍 Face verification container found:",
        !!faceVerificationContainer
    );

    if (faceVerificationContainer) {
        console.log(
            "✅ Found face verification container, mounting FaceVerificationPanel..."
        );

        // Get data attributes
        const studentId = parseInt(
            faceVerificationContainer.dataset.studentId || "0"
        );
        const studentName = faceVerificationContainer.dataset.studentName || "";
        const verificationData = faceVerificationContainer.dataset
            .verificationData
            ? JSON.parse(faceVerificationContainer.dataset.verificationData)
            : null;

        console.log("📋 Face Verification Props:", {
            studentId,
            studentName,
            verificationData,
        });

        const root = createRoot(faceVerificationContainer);
        root.render(
            <StudentAppWrapper>
                <StudentErrorBoundary>
                    <FaceVerificationPanel
                        studentId={studentId}
                        existingVerification={verificationData}
                    />
                </StudentErrorBoundary>
            </StudentAppWrapper>
        );
        console.log("✅ FaceVerificationPanel mounted successfully");
    } else {
        console.log("❌ Could not find face verification container");
    }
});
