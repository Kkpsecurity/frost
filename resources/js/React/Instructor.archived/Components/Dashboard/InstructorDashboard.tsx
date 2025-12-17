import React from "react";
import { CoursesGrid, DashboardHeader, useBulletinBoard, LoadingState, ErrorState, EmptyState } from "../Offline";

/**
 * InstructorDashboard - Main Component
 * Displays the Instructor BulletinBoard with next available and upcoming classes
 */
const InstructorDashboard: React.FC = () => {
    const { courseDates, loading, error } = useBulletinBoard();

    if (loading) return <LoadingState />;
    if (error) return <ErrorState message={error} />;
    if (courseDates.length === 0) return <EmptyState />;

    return (
        <div className="instructor-dashboard p-4">
            <DashboardHeader />
            <CoursesGrid courses={courseDates} />
        </div>
    );
};

export default InstructorDashboard;
