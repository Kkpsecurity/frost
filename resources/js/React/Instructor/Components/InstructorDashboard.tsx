import React from "react";
import {
    DashboardHeader,
    LoadingState,
    ErrorState,
    EmptyState,
    ContentHeader,
    CoursesGrid,
    CompletedCoursesList,
    useBulletinBoard,
    useCompletedCourses,
    type InstructorDashboardProps,
    type CourseDate,
} from "./Offline";
import AssignmentHistoryTable from "./Offline/AssignmentHistoryTable";

const InstructorDashboard: React.FC = () => {
    const {
        courseDates: courses,
        assignmentHistory,
        loading,
        error,
        refetch,
        isPolling,
        lastUpdated,
    } = useBulletinBoard();
    const {
        completedCourses,
        loading: completedLoading,
        error: completedError,
    } = useCompletedCourses();

    const handleCourseSelect = (courseDate: CourseDate) => {
        console.log("Selected course:", courseDate);
        // TODO: Navigate to course management interface
    };

    const handleAdminAction = () => {
        console.log("Admin action: Create new course schedule");
        // TODO: Open modal or navigate to course creation interface
        alert(
            "Course Schedule Creation - Coming Soon!\n\nThis will open a dialog to create new course schedules automatically or manually."
        );
    };

    if (loading) {
        return <LoadingState />;
    }

    if (error) {
        return <ErrorState error={error} />;
    }

    const courseCount = courses?.length || 0;

    return (
        <div className="container-fluid p-0">
            <DashboardHeader onAdminAction={handleAdminAction} />

            {/* Polling Status Bar */}
            <div
                className="px-4 py-2"
                style={{
                    backgroundColor: "var(--frost-light-bg-color, #f8fafc)",
                    borderBottom:
                        "1px solid var(--frost-light-primary-color, #e2e8f0)",
                }}
            >
                <div className="d-flex justify-content-between align-items-center">
                    <div className="d-flex align-items-center">
                        <span
                            className={`badge me-2 ${
                                isPolling ? "bg-success" : "bg-secondary"
                            }`}
                            style={{ fontSize: "0.7rem" }}
                        >
                            <i
                                className={`fas ${
                                    isPolling ? "fa-sync fa-spin" : "fa-pause"
                                } me-1`}
                            ></i>
                            {isPolling ? "Live Updates" : "Paused"}
                        </span>
                        <small
                            style={{
                                color: "var(--frost-base-color, #6b7280)",
                            }}
                        >
                            {lastUpdated
                                ? `Last updated: ${lastUpdated.toLocaleTimeString()}`
                                : "Loading..."}
                        </small>
                    </div>
                    <button
                        className="btn btn-sm btn-outline-primary"
                        onClick={refetch}
                        disabled={loading}
                    >
                        <i className="fas fa-sync me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <div className="p-4">
                {courseCount === 0 ? (
                    <EmptyState />
                ) : (
                    <CoursesGrid
                        courses={courses}
                        onCourseSelect={handleCourseSelect}
                    />
                )}
            </div>

            {/* Assignment History Section */}
            <div className="px-4 mb-4">
                <AssignmentHistoryTable data={assignmentHistory} />
            </div>
        </div>
    );
};

export default InstructorDashboard;
