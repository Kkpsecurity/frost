import React, { useState, useEffect } from "react";
import {
    DashboardHeader,
    LoadingState,
    ErrorState,
    EmptyState,
    CoursesGrid,
    useBulletinBoard,
    QuickCourseModal,
    type CourseDate,
} from "./Offline";
import AssignmentHistoryTable from "./Offline/AssignmentHistoryTable";
import ClassroomManager from "./ClassroomManager";
import AssistantView from "../Views/AssistantView";
import { useInstructorAssignment } from "../Hooks/useInstructorAssignment";
import { useClassroomActions } from "../Hooks/useClassroomActions";

const InstructorDashboard: React.FC = () => {
    const [currentView, setCurrentView] = useState<"dashboard" | "classroom">(
        "dashboard"
    );
    const [selectedCourse, setSelectedCourse] = useState<CourseDate | null>(
        null
    );
    const [showQuickCourseModal, setShowQuickCourseModal] =
        useState<boolean>(false);
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

    // Hook to detect instructor/assistant assignments
    const {
        assignedCourse,
        isInstructor,
        isAssistant,
        shouldEnterClassroom,
        currentUserId,
    } = useInstructorAssignment({
        courses,
        currentUserId: 2, // TODO: Get from auth context - your user ID for testing
        autoEnterClassroom: true, // Enable auto-enter for assistant feature
    });

    // Hook for classroom joining actions
    const {
        isLoading: classroomLoading,
        error: classroomError,
        joinAsInstructor,
        joinAsAssistant,
        leaveClassroom,
        clearError,
    } = useClassroomActions();

    // Handle classroom view switching based on assignment
    useEffect(() => {
        if (shouldEnterClassroom && assignedCourse) {
            console.log("🎯 Entering classroom mode:", {
                course: assignedCourse.course_name,
                role: isInstructor ? "instructor" : "assistant",
            });
            setSelectedCourse(assignedCourse);
            setCurrentView("classroom");
        } else {
            console.log("📋 Staying on bulletin board");
            setCurrentView("dashboard");
            setSelectedCourse(null);
        }
    }, [shouldEnterClassroom, assignedCourse, isInstructor, isAssistant]);

    const handleCourseSelect = (courseDate: CourseDate) => {
        console.log("Selected course:", courseDate);
        // TODO: Navigate to course management interface
    };

    const handleStartClass = async (courseDate: CourseDate) => {
        console.log("Starting class:", courseDate);
        clearError(); // Clear any previous errors

        const success = await joinAsInstructor(courseDate);
        if (success) {
            setSelectedCourse(courseDate);
            setCurrentView("classroom");
            refetch(); // Refresh data to show updated InstUnit
        }
    };

    const handleJoinAsAssistant = async (courseDate: CourseDate) => {
        console.log("Joining as assistant:", courseDate);
        clearError(); // Clear any previous errors

        const success = await joinAsAssistant(courseDate);
        if (success) {
            setSelectedCourse(courseDate);
            setCurrentView("classroom");
            refetch(); // Refresh data to show updated InstUnit
        }
    };

    const handleExitClassroom = async () => {
        console.log("Ending class:", selectedCourse);
        clearError(); // Clear any previous errors

        if (selectedCourse?.inst_unit?.id) {
            await leaveClassroom(selectedCourse.inst_unit.id);
        }

        setCurrentView("dashboard");
        setSelectedCourse(null);
        refetch(); // Refresh data to reflect InstUnit is ended
    };

    const handleAdminAction = () => {
        console.log("Admin action: Open quick course creation modal");
        setShowQuickCourseModal(true);
    };

    const handleQuickCourseSuccess = () => {
        console.log("Quick course created successfully, refreshing data");
        refetch(); // Refresh the bulletin board data to show new course
    };

    const handleDeleteCourse = (courseDate: CourseDate) => {
        console.log("Course deleted, refreshing data:", courseDate);
        refetch(); // Refresh the bulletin board data after deletion
    };

    // Show classroom view when instructor starts a class or assistant joins
    if (currentView === "classroom" && selectedCourse) {
        // Determine if current user is assistant for this course
        const userIsAssistant =
            selectedCourse.inst_unit &&
            selectedCourse.inst_unit.assistant_id === currentUserId;

        if (userIsAssistant) {
            // Show Assistant View
            return (
                <AssistantView
                    course={selectedCourse}
                    onExitClassroom={handleExitClassroom}
                />
            );
        } else {
            // Show Instructor Classroom Manager
            return (
                <ClassroomManager
                    initialCourse={selectedCourse}
                    onExitClassroom={handleExitClassroom}
                />
            );
        }
    }

    // Show main dashboard
    if (loading || classroomLoading) {
        return <LoadingState />;
    }

    // Prioritize classroom errors over general errors
    const displayError = classroomError || error;
    if (displayError) {
        return <ErrorState error={displayError} />;
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
                        onAssistClass={handleJoinAsAssistant}
                        onRefreshData={refetch}
                        onDeleteCourse={handleDeleteCourse}
                    />
                )}
            </div>

            {/* Quick Course Creation Modal */}
            <QuickCourseModal
                isOpen={showQuickCourseModal}
                onClose={() => setShowQuickCourseModal(false)}
                onSuccess={handleQuickCourseSuccess}
            />
        </div>
    );
};

export default InstructorDashboard;
