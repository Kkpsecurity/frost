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
import TabLicenseHistory from "../OfflineTabSystem/TabLicenseHistory";
import { t } from "@/i18n";

const OFFLINE_ACTIVE_TAB_STORAGE_KEY = "offline_active_tab";
const OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY = "offline_self_study_session";

interface MainOfflineProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
    onExamClick?: () => void;
    devModeToggle?: React.ReactNode;
}

const MainOffline: React.FC<MainOfflineProps> = ({
    courseAuthId,
    student, // kept for contract stability (even if not used yet)
    onBackToDashboard,
    onExamClick,
    devModeToggle,
}) => {
    const classroomContext = useClassroom();
    const studentContext = useStudent();

    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(null);

    const [selfStudyLessons, setSelfStudyLessons] = useState<any[]>([]);
    const [isLoadingSelfStudyLessons, setIsLoadingSelfStudyLessons] = useState(true);

    const [activeSelfStudySessionLessonId, setActiveSelfStudySessionLessonId] =
        useState<number | null>(null);

    const [activeTab, setActiveTab] = useState<"details" | "self-study" | "documentation" | "license-history">(
        "details",
    );

    // Restore last active tab + self-study session
    const didRestoreRef = React.useRef(false);
    React.useEffect(() => {
        if (didRestoreRef.current) return;
        didRestoreRef.current = true;

        const isValidTab = (
            value: any,
        ): value is "details" | "self-study" | "documentation" | "license-history" =>
            value === "details" || value === "self-study" || value === "documentation" || value === "license-history";

        try {
            const storedTab = localStorage.getItem(OFFLINE_ACTIVE_TAB_STORAGE_KEY);
            if (isValidTab(storedTab)) setActiveTab(storedTab);
        } catch {
            // ignore
        }

        try {
            const raw = localStorage.getItem(OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY);
            if (!raw) return;

            const parsed = JSON.parse(raw);
            if (!parsed?.sessionId || !parsed?.lessonId) return;
            if (Number(parsed.courseAuthId) !== Number(courseAuthId)) return;

            if (parsed.expiresAt) {
                const expiresAt = new Date(parsed.expiresAt);
                if (Number.isFinite(expiresAt.getTime()) && expiresAt <= new Date()) {
                    localStorage.removeItem(OFFLINE_SELF_STUDY_SESSION_STORAGE_KEY);
                    return;
                }
            }

            setActiveTab("self-study");
            setSelectedLessonId(Number(parsed.lessonId));
        } catch {
            // ignore
        }
    }, [courseAuthId]);

    // Persist last active tab
    React.useEffect(() => {
        try {
            localStorage.setItem(OFFLINE_ACTIVE_TAB_STORAGE_KEY, activeTab);
        } catch {
            // ignore
        }
    }, [activeTab]);

    // OFFLINE MODE: use self-study endpoint lessons only (no course date → all course lessons).
    // Never fall back to classroom-context lessons which only contain today's unit.
    const lessons = selfStudyLessons;

    const studentLessons = (studentContext as any)?.studentLessons || [];
    const activeLesson = null; // no "active lesson" in offline mode
    const isLoadingLessons = isLoadingSelfStudyLessons;

    // Sidebar helpers
    const {
        isLessonCompletedByStudent,
        isLessonFailedByStudent,
        isLessonInProgress,
        getLessonStatusColor,
        getLessonTextColor,
        getLessonStatusIcon,
    } = useLessonSidebar({
        lessons,
        studentLessons,
        activeLesson,
    });

    // Load offline/self-study lesson list from backend
    React.useEffect(() => {
        let isCancelled = false;

        const load = async () => {
            if (!courseAuthId) {
                setIsLoadingSelfStudyLessons(false);
                return;
            }

            setIsLoadingSelfStudyLessons(true);
            try {
                const response = await fetch(
                    `/classroom/self-study/lessons?course_auth_id=${courseAuthId}`,
                    { method: "GET", headers: { Accept: "application/json" } },
                );

                const payload = await response.json();

                if (!isCancelled) {
                    if (response.ok && payload?.success && Array.isArray(payload?.data?.lessons)) {
                        setSelfStudyLessons(payload.data.lessons);
                    } else {
                        console.error('[MainOffline] self-study/lessons failed', response.status, payload);
                    }
                }
            } catch (err) {
                console.error('[MainOffline] self-study/lessons fetch error', err);
            } finally {
                if (!isCancelled) setIsLoadingSelfStudyLessons(false);
            }
        };

        load();
        return () => {
            isCancelled = true;
        };
    }, [courseAuthId]);

    // Auto-select first incomplete lesson on first load
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

    const courseTitle = studentContext.courses?.find((c: any) => {
        const candidateCourseAuthId = c?.course_auth_id ?? c?.courseAuthId ?? c?.id;
        return Number(candidateCourseAuthId) === Number(courseAuthId);
    })?.course_name ?? null;

    return (
        <FrostDashboardWrapper>
            <SchoolDashboardTitleBar
                title={courseTitle ?? t("classroom.selfStudyFallbackTitle")}
                subtitle={t("classroom.selfStudySubtitle")}
                icon={<i className="fas fa-book-open"></i>}
                onBackToDashboard={onBackToDashboard}
                onExamClick={onExamClick}
                classroomStatus="OFFLINE"
                devModeToggle={devModeToggle}
                courseAuthId={courseAuthId}
            />

            <div className="container-fluid px-0">
                <div className="row g-0">
                    <div className="col-12 px-0">
                        <div className="row g-0">
                            <div className="col-md-2">
                                <LessonSideBar
                                    lessons={lessons}
                                    isLoadingLessons={isLoadingLessons}
                                    title={t("classroom.courseLessons")}
                                    isLessonCompletedByStudent={isLessonCompletedByStudent}
                                    isLessonFailedByStudent={isLessonFailedByStudent}
                                    isLessonInProgress={isLessonInProgress}
                                    getLessonStatusColor={getLessonStatusColor}
                                    getLessonTextColor={getLessonTextColor}
                                    getLessonStatusIcon={getLessonStatusIcon}
                                    selectedLessonId={selectedLessonId}
                                    onSelectLesson={(lessonId) => {
                                        // Prevent switching during active session
                                        if (
                                            activeSelfStudySessionLessonId &&
                                            lessonId !== activeSelfStudySessionLessonId
                                        ) {
                                            return;
                                        }
                                        setSelectedLessonId(lessonId);
                                    }}
                                    activeSessionLessonId={activeSelfStudySessionLessonId}
                                    disableNavigation={activeSelfStudySessionLessonId !== null}
                                />
                            </div>

                            <div className="col-md-10">
                                {/* Tabs */}
                                <div
                                    className="tabs-navigation"
                                    style={{
                                        backgroundColor: "#2c3e50",
                                        borderBottom: "2px solid #34495e",
                                        padding: "0 1.5rem",
                                    }}
                                >
                                    <div className="d-flex">
                                        <button
                                            className={`tab-button ${activeTab === "details" ? "active" : ""
                                                }`}
                                            onClick={() => setActiveTab("details")}
                                            style={{
                                                backgroundColor:
                                                    activeTab === "details"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color: activeTab === "details" ? "white" : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab === "details" ? "600" : "400",
                                                borderBottom:
                                                    activeTab === "details"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-info-circle me-2"></i>
                                            {t("classroom.tabDetails")}
                                        </button>

                                        <button
                                            className={`tab-button ${activeTab === "self-study" ? "active" : ""
                                                }`}
                                            onClick={() => setActiveTab("self-study")}
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
                                                    activeTab === "self-study" ? "600" : "400",
                                                borderBottom:
                                                    activeTab === "self-study"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-graduation-cap me-2"></i>
                                            {t("classroom.tabSelfStudy")}
                                        </button>

                                        <button
                                            className={`tab-button ${activeTab === "documentation" ? "active" : ""
                                                }`}
                                            onClick={() => setActiveTab("documentation")}
                                            style={{
                                                backgroundColor:
                                                    activeTab === "documentation"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color:
                                                    activeTab === "documentation"
                                                        ? "white"
                                                        : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab === "documentation" ? "600" : "400",
                                                borderBottom:
                                                    activeTab === "documentation"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-file-alt me-2"></i>
                                            {t("classroom.tabDocumentation")}
                                        </button>

                                        <button
                                            className={`tab-button ${activeTab === "license-history" ? "active" : ""}`}
                                            onClick={() => setActiveTab("license-history")}
                                            style={{
                                                backgroundColor:
                                                    activeTab === "license-history"
                                                        ? "#34495e"
                                                        : "transparent",
                                                color:
                                                    activeTab === "license-history"
                                                        ? "white"
                                                        : "#95a5a6",
                                                border: "none",
                                                padding: "1rem 1.5rem",
                                                cursor: "pointer",
                                                fontWeight:
                                                    activeTab === "license-history" ? "600" : "400",
                                                borderBottom:
                                                    activeTab === "license-history"
                                                        ? "3px solid #3498db"
                                                        : "none",
                                                transition: "all 0.2s",
                                            }}
                                        >
                                            <i className="fas fa-id-card me-2"></i>
                                            {t("classroom.tabLicenseHistory")}
                                        </button>
                                    </div>
                                </div>

                                <div
                                    className="tab-content"
                                    style={{
                                        padding: "2rem",
                                        overflowY: "auto",
                                        height: "calc(100vh - 250px)",
                                    }}
                                >
                                    {activeTab === "details" && (
                                        <TabDetails courseAuthId={courseAuthId} lessons={lessons} />
                                    )}

                                    {activeTab === "self-study" && (
                                        <TabSelfStudy
                                            courseAuthId={courseAuthId}
                                            lessons={lessons}
                                            selectedLessonId={selectedLessonId}
                                            onSelectLesson={setSelectedLessonId}
                                            onLessonsUpdated={setSelfStudyLessons}
                                            onActiveSessionChange={
                                                setActiveSelfStudySessionLessonId
                                            }
                                        />
                                    )}

                                    {activeTab === "documentation" && (
                                        <TabDocumentation courseAuthId={courseAuthId} />
                                    )}

                                    {activeTab === "license-history" && (
                                        <TabLicenseHistory courseAuthId={courseAuthId} />
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
