import React, { useState } from "react";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonSideBar from "../Common/LessonSideBar";
import { useLessonSidebar } from "../../hooks/useLessonSidebar";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";

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
    const studentContext = useStudent();

    // Tab state management
    const [activeTab, setActiveTab] = useState<
        "details" | "self-study" | "documentation"
    >("details");

    // OFFLINE MODE: ALL lessons for the entire course (not just today)
    // Backend returns all lessons across all course units when no courseDate exists
    // Students can study any lesson in self-paced mode
    // Access data property if context is wrapped
    const classroomData = classroomContext?.data || classroomContext;
    const backendLessons = classroomData?.lessons || [];

    // 🎨 TEMP: For testing layout with all 18 lessons
    // TODO: Remove this when backend returns all course lessons in offline mode
    const mockLessons =
        backendLessons.length < 18
            ? [
                  ...backendLessons,
                  ...Array.from(
                      { length: 18 - backendLessons.length },
                      (_, i) => ({
                          id: 100 + i,
                          title: `Lesson ${backendLessons.length + i + 1}`,
                          description: `Course lesson ${backendLessons.length + i + 1}`,
                          duration_minutes: 60,
                          order: backendLessons.length + i + 1,
                          status: "incomplete",
                          is_completed: false,
                          is_active: false,
                          is_paused: false,
                      }),
                  ),
              ]
            : backendLessons;

    const lessons = mockLessons;
    const studentLessons = studentContext?.studentLessons || [];
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
                                {/* Tab Navigation */}
                                <div
                                    className="tabs-navigation"
                                    style={{
                                        backgroundColor: "#2c3e50",
                                        borderBottom: "2px solid #34495e",
                                        padding: "0 1.5rem",
                                    }}
                                >
                                    <div className="d-flex">
                                        {/* Details Tab */}
                                        <button
                                            className={`tab-button ${
                                                activeTab === "details"
                                                    ? "active"
                                                    : ""
                                            }`}
                                            onClick={() =>
                                                setActiveTab("details")
                                            }
                                            style={{
                                                backgroundColor:
                                                    activeTab === "details"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color:
                                                    activeTab === "details"
                                                        ? "white"
                                                        : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab === "details"
                                                        ? "600"
                                                        : "400",
                                                borderBottom:
                                                    activeTab === "details"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-info-circle me-2"></i>
                                            Details
                                        </button>

                                        {/* Self Study Tab */}
                                        <button
                                            className={`tab-button ${
                                                activeTab === "self-study"
                                                    ? "active"
                                                    : ""
                                            }`}
                                            onClick={() =>
                                                setActiveTab("self-study")
                                            }
                                            style={{
                                                backgroundColor:
                                                    activeTab === "self-study"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color:
                                                    activeTab === "self-study"
                                                        ? "white"
                                                        : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab === "self-study"
                                                        ? "600"
                                                        : "400",
                                                borderBottom:
                                                    activeTab === "self-study"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-graduation-cap me-2"></i>
                                            Self Study
                                        </button>

                                        {/* Documentation Tab */}
                                        <button
                                            className={`tab-button ${
                                                activeTab === "documentation"
                                                    ? "active"
                                                    : ""
                                            }`}
                                            onClick={() =>
                                                setActiveTab("documentation")
                                            }
                                            style={{
                                                backgroundColor:
                                                    activeTab ===
                                                    "documentation"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color:
                                                    activeTab ===
                                                    "documentation"
                                                        ? "white"
                                                        : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab ===
                                                    "documentation"
                                                        ? "600"
                                                        : "400",
                                                borderBottom:
                                                    activeTab ===
                                                    "documentation"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-file-alt me-2"></i>
                                            Documentation
                                        </button>
                                    </div>
                                </div>

                                {/* Tab Content */}
                                <div
                                    className="tab-content"
                                    style={{
                                        padding: "2rem",
                                        overflowY: "auto",
                                        height: "calc(100vh - 250px)",
                                    }}
                                >
                                    {/* Details Tab Content */}
                                    {activeTab === "details" && (
                                        <div className="details-tab">
                                            <h3
                                                style={{
                                                    color: "white",
                                                    marginBottom: "1.5rem",
                                                }}
                                            >
                                                <i className="fas fa-tachometer-alt me-2"></i>
                                                Learning Dashboard
                                            </h3>
                                            <p style={{ color: "#95a5a6" }}>
                                                Course overview, progress stats,
                                                and learning materials.
                                            </p>
                                        </div>
                                    )}

                                    {/* Self Study Tab Content */}
                                    {activeTab === "self-study" && (
                                        <div className="self-study-tab">
                                            <h3
                                                style={{
                                                    color: "white",
                                                    marginBottom: "1.5rem",
                                                }}
                                            >
                                                <i className="fas fa-play-circle me-2"></i>
                                                Self Study Mode
                                            </h3>
                                            <p style={{ color: "#95a5a6" }}>
                                                Video lessons, practice
                                                exercises, and interactive
                                                content.
                                            </p>
                                        </div>
                                    )}

                                    {/* Documentation Tab Content */}
                                    {activeTab === "documentation" && (
                                        <div className="documentation-tab">
                                            <h3
                                                style={{
                                                    color: "white",
                                                    marginBottom: "1.5rem",
                                                }}
                                            >
                                                <i className="fas fa-folder-open me-2"></i>
                                                Course Documentation
                                            </h3>
                                            <p style={{ color: "#95a5a6" }}>
                                                PDF resources, handbooks, and
                                                reference materials.
                                            </p>
                                        </div>
                                    )}
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
