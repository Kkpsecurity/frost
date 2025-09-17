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

const InstructorDashboard: React.FC = () => {
    const { courseDates: courses, loading, error } = useBulletinBoard();
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

            {/* Completed Courses Section */}
            <h4
                className="mb-3 d-flex align-items-center"
                style={{
                    color: "#efefef",
                }}
            >
                <i
                    className="fas fa-history mr-2"
                    style={{
                        color: "var(--frost-success-color, #10b981)",
                    }}
                ></i>
                Recently Completed Courses
            </h4>
            <CompletedCoursesList courses={completedCourses} />
        </div>
    );
};

export default InstructorDashboard;
