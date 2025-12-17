import React from "react";
import { useClassroom } from "../../hooks/useClassroom";
import OnlineDashboard from "./OnlineDashboard";
import OfflineDashboard from "./OfflineDashboard";

interface MainDashboardProps {
    courseAuthId?: number | null;
}

/**
 * MainDashboard - Orchestrator component
 *
 * Responsibilities:
 * - Determine if classroom session exists (courseDate)
 * - Route to appropriate dashboard
 * - If courseDate exists → OnlineDashboard (live session)
 * - If no courseDate → OfflineDashboard (waiting/materials)
 *
 * Does NOT handle:
 * - Data fetching (handled by StudentDataLayer)
 * - Polling logic (handled by StudentDataLayer)
 */
const MainDashboard: React.FC<MainDashboardProps> = ({ courseAuthId }) => {
    const classroom = useClassroom();

    // Determine based on whether we have a scheduled course date
    const hasScheduledClass = !!classroom?.courseDate;

    // Render appropriate dashboard based on course date existence
    return (
        <>
            {hasScheduledClass ? (
                <OnlineDashboard courseAuthId={courseAuthId} />
            ) : (
                <OfflineDashboard courseAuthId={courseAuthId} />
            )}
        </>
    );
};

export default MainDashboard;
