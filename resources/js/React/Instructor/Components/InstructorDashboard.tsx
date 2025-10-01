import React, { useState, useEffect } from "react";
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
import ClassroomManager from "./ClassroomManager";

const InstructorDashboard: React.FC = () => {
    const [currentView, setCurrentView] = useState<"dashboard" | "classroom">(
        "dashboard"
    );
    const [selectedCourse, setSelectedCourse] = useState<CourseDate | null>(
        null
    );
    const [manualDashboard, setManualDashboard] = useState(false); // Track if user manually went back to dashboard

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

    // Auto-redirect to classroom when instructor is assigned to an active class
    useEffect(() => {
        // Don't auto-redirect if user manually chose to stay on dashboard
        if (manualDashboard) return;

        // Find any "in_progress" class that the instructor is assigned to
        const activeClass = courses?.find(
            (course) =>
                course.class_status === "in_progress" &&
                course.inst_unit &&
                course.instructor_name
        );

        if (activeClass && currentView === "dashboard") {
            console.log(
                "🎯 Auto-redirecting to classroom for active class:",
                activeClass
            );
            setSelectedCourse(activeClass);
            setCurrentView("classroom");
        }
    }, [courses, currentView, manualDashboard]);

    const handleCourseSelect = (courseDate: CourseDate) => {
        console.log("Selected course:", courseDate);
        // TODO: Navigate to course management interface
    };

    const handleStartClass = (courseDate: CourseDate) => {
        console.log("Starting class:", courseDate);
        setSelectedCourse(courseDate);
        setCurrentView("classroom");
    };

    const handleExitClassroom = () => {
        setCurrentView("dashboard");
        setSelectedCourse(null);
        setManualDashboard(true); // Mark that user manually returned to dashboard

        // Clear manual dashboard flag after 5 minutes to allow auto-redirect again
        setTimeout(() => {
            setManualDashboard(false);
        }, 5 * 60 * 1000); // 5 minutes
    };

    const handleAdminAction = () => {
        console.log("Admin action: Create new course schedule");
        // TODO: Open modal or navigate to course creation interface
        alert(
            "Course Schedule Creation - Coming Soon!\n\nThis will open a dialog to create new course schedules automatically or manually."
        );
    };

    // Show classroom view when instructor starts a class
    if (currentView === "classroom" && selectedCourse) {
        return (
            <ClassroomManager
                initialCourse={selectedCourse}
                onExitClassroom={handleExitClassroom}
            />
        );
    }

    // Show main dashboard
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

                        {/* Auto-redirect indicator */}
                        {!manualDashboard &&
                            courses?.some(
                                (c) =>
                                    c.class_status === "in_progress" &&
                                    c.inst_unit
                            ) && (
                                <span
                                    className="badge bg-info me-2"
                                    style={{ fontSize: "0.7rem" }}
                                >
                                    <i className="fas fa-rocket me-1"></i>
                                    Auto-Classroom
                                </span>
                            )}

                        {manualDashboard && (
                            <span
                                className="badge bg-warning me-2"
                                style={{ fontSize: "0.7rem" }}
                            >
                                <i className="fas fa-pause me-1"></i>
                                Manual Mode
                            </span>
                        )}

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
                    <div className="d-flex gap-2">
                        {manualDashboard && (
                            <button
                                className="btn btn-sm btn-outline-success"
                                onClick={() => setManualDashboard(false)}
                                title="Re-enable automatic classroom redirect"
                            >
                                <i className="fas fa-play me-1"></i>
                                Enable Auto-Redirect
                            </button>
                        )}
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
            </div>

            <div className="p-4">
                {courseCount === 0 ? (
                    <EmptyState />
                ) : (
                    <CoursesGrid
                        courses={courses}
                        onCourseSelect={handleCourseSelect}
                        onStartClass={handleStartClass}
                        onRefreshData={refetch}
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
