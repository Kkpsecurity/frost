import React from "react";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonSideBar from "../Common/LessonSideBar";
import { useLessonSidebar } from "../../hooks/useLessonSidebar";
import { useClassroom } from "../../context/ClassroomContext";

interface MainOfflineProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
    devModeToggle?: React.ReactNode;
}

/**
 * MainOffline - Self-study classroom mode
 *
 * Layout:
 * - Title Bar: Student tools and information (SchoolDashboardTitleBar component)
 * - Sidebar: All lessons for selected course
 * - Content Area: Tabbed interface (Details, Self Study, Documentation)
 */
const MainOffline: React.FC<MainOfflineProps> = ({
    courseAuthId,
    student,
    onBackToDashboard,
    devModeToggle,
}) => {
    const classroomContext = useClassroom();

    // OFFLINE MODE: ALL lessons for the entire course (not just today)
    // Backend returns all lessons across all course units when no courseDate exists
    // Students can study any lesson in self-paced mode
    // Access data property if context is wrapped
    const classroomData = classroomContext?.data || classroomContext;
    const backendLessons = classroomData?.lessons || [];
    
    // 🎨 TEMP: For testing layout with all 18 lessons
    // TODO: Remove this when backend returns all course lessons in offline mode
    const mockLessons = backendLessons.length < 18 ? [
        ...backendLessons,
        ...Array.from({ length: 18 - backendLessons.length }, (_, i) => ({
            id: 100 + i,
            title: `Lesson ${backendLessons.length + i + 1}`,
            description: `Course lesson ${backendLessons.length + i + 1}`,
            duration_minutes: 60,
            order: backendLessons.length + i + 1,
            status: 'incomplete',
            is_completed: false,
            is_active: false,
            is_paused: false,
        }))
    ] : backendLessons;
    
    const lessons = mockLessons;
    const studentLessons = classroomData?.studentLessons || [];
    const activeLesson = null; // No active lesson in offline mode
    const isLoadingLessons = false; // Replace with real loading state

    // 🔍 DEBUG: Log lessons data
    console.log("📚 MainOffline Lessons:", {
        lessonsCount: lessons.length,
        backendLessonsCount: backendLessons.length,
        lessons: lessons,
        studentLessons: studentLessons,
        classroomContext: classroomContext,
        classroomData: classroomData,
    });

    // Use lesson sidebar hook for helper functions
    const {
        isLessonCompletedByStudent,
        isLessonInProgress,
        getLessonStatusColor,
        getLessonTextColor,
        getLessonStatusIcon,
    } = useLessonSidebar({
        lessons,
        studentLessons,
        activeLesson,
    });

    return (
        <FrostDashboardWrapper>
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title="Self-Study Mode"
                subtitle="Complete lessons at your own pace"
                icon={<i className="fas fa-book-open"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="OFFLINE"
                devModeToggle={devModeToggle}
            />
            <div className="container-fluid px-0">
                <div className="row g-0">
                    <div className="col-12 px-0">
                        {/* Main Classroom Layout */}
                        <div className="row g-0">
                            {/* Left Sidebar - Lessons */}
                            <div className="col-md-2">
                                <LessonSideBar
                                    lessons={lessons}
                                    isLoadingLessons={isLoadingLessons}
                                    isLessonCompletedByStudent={
                                        isLessonCompletedByStudent
                                    }
                                    isLessonInProgress={isLessonInProgress}
                                    getLessonStatusColor={getLessonStatusColor}
                                    getLessonTextColor={getLessonTextColor}
                                    getLessonStatusIcon={getLessonStatusIcon}
                                />
                            </div>
                            {/* Main Content Area */}
                            <div className="col-md-10">
                                <div style={{ padding: "2rem" }}>
                                    <h3 style={{ color: "white" }}>
                                        Self-Study Content Area
                                    </h3>
                                    <p style={{ color: "#95a5a6" }}>
                                        Lesson details, study materials, and
                                        documentation will appear here.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </FrostDashboardWrapper>
    );
};

export default MainOffline;
