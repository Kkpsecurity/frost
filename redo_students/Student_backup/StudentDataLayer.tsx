import React, { useEffect, useState } from "react";
import StudentDashboard from "./Components/Dashboard/StudentDashboard";
import PageLoader from "../Shared/Components/Widgets/PageLoader";
import { Alert } from "react-bootstrap";

interface StudentDataLayerProps {
    courseAuthId?: number | null;
}

/**
 * StudentDataLayer - ONLY handles data polling via API
 *
 * Responsibilities:
 * - Read course_auth_id from props
 * - Poll API for student data
 * - Pass polled data to StudentDashboard
 *
 * Does NOT handle:
 * - Face verification logic
 * - Validation checks
 * - Conditional rendering (pass to Dashboard instead)
 * - Any business logic
 */
const StudentDataLayer: React.FC<StudentDataLayerProps> = ({
    courseAuthId,
}) => {
    const [mounted, setMounted] = useState(false);
    const [polledData, setPolledData] = useState<any>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        console.log("🎓 StudentDataLayer: Mounting with courseAuthId:", {
            courseAuthId,
        });

        setMounted(true);

        // Poll for data via API endpoint
        const pollData = async () => {
            try {
                const response = await fetch("/classroom/student/poll");
                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }

                const data = await response.json();
                console.log("🎓 StudentDataLayer: Polled data:", data);

                setPolledData(data);
                setError(null);
            } catch (err) {
                const errorMsg =
                    err instanceof Error ? err.message : "Unknown error";
                console.error("🎓 StudentDataLayer: Poll error:", errorMsg);
                setError(errorMsg);
            } finally {
                setIsLoading(false);
            }
        };

        // Initial poll
        pollData();

        // Set up interval polling (every 5 seconds)
        const pollInterval = setInterval(pollData, 5000);

        return () => clearInterval(pollInterval);
    }, [courseAuthId]);

    if (!mounted || isLoading) {
        return <PageLoader />;
    }

    // API error occurred
    if (error) {
        return (
            <Alert variant="warning" className="m-4">
                <Alert.Heading>Data Loading Issue</Alert.Heading>
                <p>Unable to load student data: {error}</p>
                <p>Please refresh the page or contact support.</p>
            </Alert>
        );
    }

    // No data returned
    if (!polledData) {
        return (
            <Alert variant="warning" className="m-4">
                <Alert.Heading>No Data</Alert.Heading>
                <p>Unable to load student dashboard data.</p>
            </Alert>
        );
    }

    console.log(
        "🎓 StudentDataLayer: Rendering StudentDashboard with polled data"
    );

    // Pass all polled data to Dashboard component
    // Dashboard handles all conditional rendering and business logic
    return <StudentDashboard {...polledData} courseAuthId={courseAuthId} />;
};

export default StudentDataLayer;
