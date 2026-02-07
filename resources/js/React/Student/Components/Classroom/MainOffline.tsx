import React, { useState } from "react";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonSideBar from "../Common/LessonSideBar";
import { useLessonSidebar } from "../../hooks/useLessonSidebar";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";
import TabDetails from "../OfflineTabSystem/TabDetails";
import TabSelfStudy from "../OfflineTabSystem/TabSelfStudy";
import TabDocumentation from "../OfflineTabSystem/TabDocumentation";

const OFFLINE_ACTIVE_TAB_STORAGE_KEY = "offline_active_tab";
const OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY = "offline_self_study_session";

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

    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(
        null,
    );

    const [selfStudyLessons, setSelfStudyLessons] = useState<any[]>([]);
    const [isLoadingSelfStudyLessons, setIsLoadingSelfStudyLessons] =
        useState(false);

    // Tab state management
    const [activeTab, setActiveTab] = useState<
        "details" | "self-study" | "documentation"
    >("details");

    // Restore last active tab (and active self-study session) on refresh.
    const didRestoreRef = React.useRef(false);
    React.useEffect(() => {
        if (didRestoreRef.current) return;
        didRestoreRef.current = true;

        const isValidTab = (
            value: any,
        ): value is "details" | "self-study" | "documentation" => {
            return (
                value === "details" ||
                value === "self-study" ||
                value === "documentation"
            );
        };

        try {
            const storedTab = localStorage.getItem(
                OFFLINE_ACTIVE_TAB_STORAGE_KEY,
            );
            if (isValidTab(storedTab)) {
                setActiveTab(storedTab);
            }
        } catch {
            // ignore
        }

        try {
            const raw = localStorage.getItem(
                OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY,
            );
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (!parsed?.sessionId || !parsed?.lessonId) return;
            if (Number(parsed.courseAuthId) !== Number(courseAuthId)) return;

            if (parsed.expiresAt) {
                const expiresAt = new Date(parsed.expiresAt);
                if (
                    Number.isFinite(expiresAt.getTime()) &&
                    expiresAt <= new Date()
                ) {
                    localStorage.removeItem(
                        OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY,
                    );
                    return;
                }
            }

            // If a session exists, prioritize jumping back into Self Study.
            setActiveTab("self-study");
            setSelectedLessonId(Number(parsed.lessonId));
        } catch {
            // ignore
        }
    }, [courseAuthId]);

    // Persist last active tab selection.
    React.useEffect(() => {
        try {
            localStorage.setItem(OFFLINE_ACTIVE_TAB_STORAGE_KEY, activeTab);
        } catch {
            // ignore
        }
    }, [activeTab]);

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

    const lessons =
        selfStudyLessons && selfStudyLessons.length > 0
            ? selfStudyLessons
            : mockLessons;
    const studentLessons = studentContext?.studentLessons || [];
    const activeLesson = null; // No active lesson in offline mode
    const isLoadingLessons = isLoadingSelfStudyLessons;

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

    React.useEffect(() => {
        let isCancelled = false;

        const load = async () => {
            if (!courseAuthId) return;

            setIsLoadingSelfStudyLessons(true);
            try {
                const response = await fetch(
                    `/classroom/self-study/lessons?course_auth_id=${courseAuthId}`,
                    {
                        method: "GET",
                        headers: { Accept: "application/json" },
                    },
                );
                const payload = await response.json();

                if (isCancelled) return;

                if (response.ok && payload?.success && payload?.data?.lessons) {
                    setSelfStudyLessons(payload.data.lessons);
                }
            } catch {
                // non-fatal: fall back to context/mock lessons
            } finally {
                if (!isCancelled) setIsLoadingSelfStudyLessons(false);
            }
        };

        load();

        return () => {
            isCancelled = true;
        };
    }, [courseAuthId]);

    React.useEffect(() => {
        if (selectedLessonId !== null) return;
        if (!lessons || lessons.length === 0) return;

        const firstIncomplete = lessons.find((l: any) => {
            return !(
                l?.is_completed === true ||
                l?.status === "completed" ||
                l?.completed_at != null
            );
        });

        setSelectedLessonId(Number(firstIncomplete?.id ?? lessons[0]?.id));
    }, [lessons, selectedLessonId]);

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
                                    selectedLessonId={selectedLessonId}
                                    onSelectLesson={setSelectedLessonId}
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
                                        <TabDetails
                                            courseAuthId={courseAuthId}
                                            lessons={lessons}
                                        />
                                    )}

                                    {/* Self Study Tab Content */}
                                    {activeTab === "self-study" && (
                                        <TabSelfStudy
                                            courseAuthId={courseAuthId}
                                            lessons={lessons}
                                            selectedLessonId={selectedLessonId}
                                            onSelectLesson={setSelectedLessonId}
                                            onLessonsUpdated={
                                                setSelfStudyLessons
                                            }
                                        />
                                    )}

                                    {/* Documentation Tab Content */}
                                    {activeTab === "documentation" && (
                                        <TabDocumentation
                                            courseAuthId={courseAuthId}
                                        />
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
