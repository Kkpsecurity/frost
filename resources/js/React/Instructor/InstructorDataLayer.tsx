import React from "react";
import { useQuery } from "@tanstack/react-query";
import InstructorDashboard from "./Components/InstructorDashboard";

/**
 * Main Instructor Data Layer Component
 * This serves as the data gathering layer for the instructor dashboard
 * Fetches all necessary data and passes it down to the dashboard
 */

interface InstructorDataLayerProps {
    instructorId?: any;
    debug?: boolean;
}

interface InstructorData {
    instructor: any;
    bulletin_board: any;
    active_courses: any[];
    classroom_data: any;
    students_data: any;
}

const InstructorDataLayer: React.FC<InstructorDataLayerProps> = ({
    instructorId,
    debug = false,
}) => {
    if (debug) {
        console.log("🔧 InstructorDataLayer props:", { instructorId, debug });
    }

    // Fetch instructor session validation
    const {
        data: sessionData,
        isLoading: sessionLoading,
        error: sessionError,
    } = useQuery({
        queryKey: ["instructor", "session"],
        queryFn: async () => {
            console.log("🔄 Validating instructor session...");
            const response = await fetch("/admin/instructors/validate", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            });

            if (!response.ok) {
                throw new Error(
                    `Session validation failed: ${response.status}`
                );
            }

            const data = await response.json();
            console.log("✅ Session validated:", data);
            return data;
        },
        staleTime: 5 * 60 * 1000,
        retry: 2,
    });

    // Fetch bulletin board data
    const {
        data: bulletinData,
        isLoading: bulletinLoading,
        error: bulletinError,
    } = useQuery({
        queryKey: ["instructor", "bulletin-board"],
        queryFn: async () => {
            console.log("🔄 Fetching bulletin board data...");
            const response = await fetch(
                "/admin/instructors/api/bulletin-board",
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                }
            );

            if (!response.ok) {
                throw new Error(
                    `Bulletin board fetch failed: ${response.status}`
                );
            }

            const data = await response.json();
            console.log("✅ Bulletin board data loaded:", data);
            return data;
        },
        enabled: !!sessionData?.authenticated, // Only fetch if session is valid
        staleTime: 5 * 60 * 1000,
    });

    // Handle loading states
    if (sessionLoading) {
        return (
            <div className="instructor-data-layer">
                <div
                    className="d-flex justify-content-center align-items-center"
                    style={{ minHeight: "400px" }}
                >
                    <div className="text-center">
                        <div
                            className="spinner-border text-primary mb-3"
                            role="status"
                        >
                            <span className="visually-hidden">Loading...</span>
                        </div>
                        <h5 className="text-muted">Validating Session...</h5>
                    </div>
                </div>
            </div>
        );
    }

    // Handle session errors
    if (sessionError || !sessionData?.authenticated) {
        return (
            <div className="instructor-data-layer">
                <div className="alert alert-danger mx-3 mt-3">
                    <h5 className="alert-heading">
                        <i className="fas fa-exclamation-triangle me-2"></i>
                        Authentication Required
                    </h5>
                    <p>
                        Unable to validate instructor session. Please log in and
                        try again.
                    </p>
                    <p className="small text-muted mb-0">
                        Error:{" "}
                        {sessionError?.message || "Session validation failed"}
                    </p>
                </div>
            </div>
        );
    }

    // Prepare data for the dashboard
    const dashboardData: InstructorData = {
        instructor: sessionData?.instructor || null,
        bulletin_board: bulletinData || null,
        active_courses: [], // Will be populated when course selection is implemented
        classroom_data: null, // Will be populated when classroom is active
        students_data: null, // Will be populated when students are loaded
    };

    console.log("📊 Dashboard data prepared:", dashboardData);

    return (
        <div className="instructor-data-layer">
            <InstructorDashboard
                instructorData={dashboardData}
                isLoading={bulletinLoading}
                error={bulletinError}
                debug={debug}
            />
        </div>
    );
};

export default InstructorDataLayer;
