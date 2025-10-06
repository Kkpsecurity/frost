import React, { useState, useEffect } from "react";
import {
    DashboardHeader,
    LoadingState,
    ErrorState,
    EmptyState,
    CoursesGrid,
    useBulletinBoard,
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
    // Note: No tabs needed - show all courses by default

    const {
        courseDates: courses,
        assignmentHistory,
        loading,
        error,
        refetch,
        isPolling,
        lastUpdated,
    } = useBulletinBoard();

    // Auto-determine view based on active InstUnit
    useEffect(() => {
        // Find any course with active InstUnit (CourseDate + InstUnit = classroom mode)
        const activeClass = courses?.find(
            (course) =>
                course.inst_unit &&
                course.class_status === "in_progress" &&
                course.inst_unit.completed_at === null
        );

        if (activeClass) {
            // CourseDate + InstUnit = Classroom Mode
            console.log(
                "🎯 Active InstUnit found, entering classroom mode:",
                activeClass
            );
            setSelectedCourse(activeClass);
            setCurrentView("classroom");
        } else {
            // CourseDate only OR No CourseDate = Bulletin Board Mode
            console.log("📋 No active InstUnit, showing bulletin board");
            setCurrentView("dashboard");
            setSelectedCourse(null);
        }
    }, [courses]);

    const handleCourseSelect = (courseDate: CourseDate) => {
        console.log("Selected course:", courseDate);
        // TODO: Navigate to course management interface
    };

    const handleStartClass = async (courseDate: CourseDate) => {
        console.log("Starting class:", courseDate);
        // TODO: Create InstUnit via API call
        // For now, simulate starting the class
        setSelectedCourse(courseDate);
        setCurrentView("classroom");
    };

    const handleExitClassroom = async () => {
        console.log("Ending class:", selectedCourse);
        // TODO: End InstUnit via API call
        // For now, simulate ending the class
        setCurrentView("dashboard");
        setSelectedCourse(null);
        // Refresh data to reflect InstUnit is ended
        refetch();
    };

    const handleAdminAction = () => {
        console.log("Admin action: Course management");
        // TODO: Navigate to course management interface
        alert("Course Management - Coming Soon!");
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

    // Show all courses (no filtering - bulletin board shows everything)
    const filteredCourses = courses || [];
    const courseCount = filteredCourses.length;

    return (
        <div className="container-fluid p-0">
            <DashboardHeader onAdminAction={handleAdminAction} />
            <div className="p-4">
                {courseCount === 0 ? (
                    <EmptyState
                        title="No Classes Scheduled"
                        message="There are no courses scheduled for today."
                        icon="fas fa-calendar-times"
                    />
                ) : (
                    <CoursesGrid
                        courses={filteredCourses}
                        onCourseSelect={handleCourseSelect}
                        onStartClass={handleStartClass}
                        onRefreshData={refetch}
                    />
                )}
            </div>
        </div>
    );
};

export default InstructorDashboard;
