import React, { useState, useEffect } from "react";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import PauseOverlay from "../Common/PauseOverlay";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonSideBar from "../Common/LessonSideBar";
import LessonProgressBar from "./LessonProgressBar";
import { useClassroomSessionMode } from "@/React/Hooks/ClassroomAskInstructorHooks";
import { useLessonSidebar } from "../../hooks/useLessonSidebar";

interface MainOnlineProps {
    classroom: any;
    student: any;
    validations?: any;
    onBackToDashboard: () => void;
    devModeToggle?: React.ReactNode;
}

/**
 * MainOnline - Live classroom mode
 *
 * Shown when:
 * - CourseDate exists (class scheduled)
 * - InstUnit exists (instructor has started class)
 *
 * Features:
 * - Live video/audio
 * - Screen sharing
 * - Real-time chat
 * - Live lesson presentation
 * - Student interactions
 * - Attendance tracking
 */
const MainOnline: React.FC<MainOnlineProps> = ({
    classroom,
    student,
    validations,
    onBackToDashboard,
    devModeToggle,
}) => {
    // Extract classroom data (handle if classroom is wrapped in context)
    const classroomData = classroom?.data || classroom;
    const { courseDate, instructor, instUnit } = classroomData || {};
    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(
        null,
    );
    const [pauseRemainingSeconds, setPauseRemainingSeconds] =
        useState<number>(0);
    const [pauseStartTime, setPauseStartTime] = useState<number | null>(null);
    const PAUSE_DURATION_MINUTES = 5;
    const PAUSE_DURATION_SECONDS = PAUSE_DURATION_MINUTES * 60;

    const courseDateId: number | null = courseDate?.id ?? null;
    const sessionModeQuery = useClassroomSessionMode(courseDateId);
    const sessionMode = sessionModeQuery.data?.mode ?? "TEACHING";

    // ONLINE MODE: Lessons for TODAY only (based on courseUnit/day_number)
    // Backend returns lessons for current CourseUnit (e.g., Wednesday = Day 3 lessons)
    const lessons = classroomData?.lessons || [];
    const studentLessons = classroomData?.studentLessons || [];
    const activeLesson = classroomData?.activeLesson || null;
    const isLoadingLessons = false; // Replace with real loading state

    // 🔍 DEBUG: Log lessons data
    console.log("📚 MainOnline Lessons:", {
        lessonsCount: lessons.length,
        lessons: lessons,
        studentLessons: studentLessons,
        classroom: classroom,
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

    // Zoom screen share data from backend
    const zoomData = classroomData?.zoom || {};
    const isZoomReady = zoomData?.is_ready ?? false;
    const screenShareUrl = zoomData?.screen_share_url ?? null;

    const instructorName =
        instructor?.name || instructor?.fname || "Instructor";
    const instructorEmail = instructor?.email || null;
    const instructorAvatar = instructor?.avatar || "/images/default-avatar.png";

    // Get today's day name for headshot lookup
    const getTodayKey = () => {
        try {
            return new Date()
                .toLocaleString("en-US", { weekday: "long" })
                .toLowerCase();
        } catch {
            return "monday";
        }
    };

    // Extract ID card URL from validations
    const idCardUrl: string | null =
        typeof validations?.idcard === "string" && validations.idcard.length > 0
            ? validations.idcard
            : null;

    // Extract today's headshot URL from validations
    const getTodayHeadshotUrl = (): string | null => {
        const headshot = validations?.headshot;
        if (!headshot) return null;

        if (typeof headshot === "string")
            return headshot.length > 0 ? headshot : null;
        if (Array.isArray(headshot)) {
            const found = headshot.find(
                (v: any) => typeof v === "string" && v.length > 0,
            );
            return found || null;
        }
        if (typeof headshot === "object") {
            const todayKey = getTodayKey();
            const todayUrl = headshot?.[todayKey];
            if (typeof todayUrl === "string" && todayUrl.length > 0)
                return todayUrl;
            const firstUrl = Object.values(headshot).find(
                (v: any) => typeof v === "string" && v.length > 0,
            );
            return (firstUrl as string) || null;
        }

        return null;
    };

    const todayHeadshotUrl: string | null = getTodayHeadshotUrl();

    // 🚨 PAUSE DETECTION: Monitor activeLesson for pause state changes
    useEffect(() => {
        if (
            activeLesson &&
            activeLesson.is_paused &&
            !activeLesson.completed_at
        ) {
            console.log("🔍 PAUSE DETECTED FROM CLASSROOM POLL:", activeLesson);
            setPauseRemainingSeconds(PAUSE_DURATION_SECONDS);
            setPauseStartTime(Date.now());
        } else {
            // No pause detected - instructor has unpaused or lesson completed
            setPauseRemainingSeconds(0);
            setPauseStartTime(null);
            sessionStorage.removeItem("pauseStartTime");
        }
    }, [JSON.stringify(activeLesson)]);

    // Pause countdown timer - updates every second based on elapsed time
    useEffect(() => {
        if (!pauseStartTime) {
            setPauseRemainingSeconds(0);
            return;
        }

        const updateCountdown = () => {
            const elapsedMs = Date.now() - pauseStartTime;
            const elapsedSeconds = Math.floor(elapsedMs / 1000);
            const remaining = Math.max(
                0,
                PAUSE_DURATION_SECONDS - elapsedSeconds,
            );
            setPauseRemainingSeconds(remaining);

            if (remaining === 0) {
                setPauseStartTime(null);
                sessionStorage.removeItem("pauseStartTime");
            }
        };

        updateCountdown(); // Update immediately
        const interval = setInterval(updateCountdown, 1000);

        return () => clearInterval(interval);
    }, [pauseStartTime, PAUSE_DURATION_SECONDS]);

    return (
        <FrostDashboardWrapper>
            <PauseOverlay pauseRemainingSeconds={pauseRemainingSeconds} />
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title="Live Classroom"
                subtitle={`Instructor: ${instructorName}`}
                icon={<i className="fas fa-video"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="ONLINE"
                devModeToggle={devModeToggle}
            />
            <div
                className="container-fluid px-0"
                style={{
                    pointerEvents: pauseRemainingSeconds > 0 ? "none" : "auto",
                }}
            >
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

                            {/* Center - Screen Share & Progress */}
                            <div className="col-md-7">
                                {/* Screen Share Card */}
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        borderRadius: "0",
                                        overflow: "hidden",
                                        marginBottom: "1rem",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-desktop me-2"></i>
                                            Screen Share / Presentation
                                        </h6>
                                    </div>
                                    <div
                                        className="card-body p-0"
                                        style={{
                                            backgroundColor: "transparent",
                                        }}
                                    >
                                        <div
                                            className="ratio ratio-16x9"
                                            style={{
                                                backgroundColor: "#000",
                                                borderRadius: "0",
                                                overflow: "hidden",
                                            }}
                                        >
                                            {isZoomReady && screenShareUrl ? (
                                                <iframe
                                                    title="Zoom Screen Share"
                                                    src={screenShareUrl}
                                                    style={{
                                                        width: "100%",
                                                        height: "100%",
                                                        border: "none",
                                                    }}
                                                    allow="camera; microphone; fullscreen; display-capture"
                                                />
                                            ) : (
                                                <div className="d-flex align-items-center justify-content-center">
                                                    <div className="text-center">
                                                        <i
                                                            className="fas fa-tv fa-4x mb-3"
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        ></i>
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                                marginBottom:
                                                                    "0.5rem",
                                                            }}
                                                        >
                                                            Wait for instructor
                                                            to start screen
                                                            share
                                                        </p>
                                                        <small
                                                            style={{
                                                                color: "#95a5a6",
                                                                opacity: 0.8,
                                                            }}
                                                        >
                                                            This panel will
                                                            auto-load when
                                                            ready.
                                                        </small>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Lesson Progress Bar - Shows elapsed time and progress */}
                                <LessonProgressBar
                                    selectedLesson={
                                        // Only show lesson if instructor marked it as active (even if paused)
                                        lessons.find(
                                            (l) => l.is_active === true,
                                        ) ||
                                        // If user manually selected a lesson, show that
                                        lessons.find(
                                            (l) => l.id === selectedLessonId,
                                        ) ||
                                        null
                                    }
                                    startTime={
                                        lessons.find(
                                            (l) => l.is_active === true,
                                        )?.started_at ||
                                        lessons.find(
                                            (l) => l.id === selectedLessonId,
                                        )?.started_at ||
                                        null
                                    }
                                    isPaused={
                                        lessons.find(
                                            (l) => l.is_active === true,
                                        )?.is_paused || false
                                    }
                                />
                            </div>

                            {/* Right Sidebar - Tools */}
                            <div className="col-md-3">
                                {/* Instructor Panel */}
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        marginBottom: "1rem",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-chalkboard-teacher me-2"></i>
                                            Instructor
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="d-flex align-items-center gap-3">
                                            <img
                                                src={instructorAvatar}
                                                alt={instructorName}
                                                style={{
                                                    width: "44px",
                                                    height: "44px",
                                                    borderRadius: "50%",
                                                    objectFit: "cover",
                                                    backgroundColor:
                                                        "rgba(0,0,0,0.25)",
                                                }}
                                            />
                                            <div style={{ minWidth: 0 }}>
                                                <div
                                                    style={{
                                                        color: "#ecf0f1",
                                                        fontWeight: 600,
                                                        lineHeight: 1.2,
                                                    }}
                                                >
                                                    {instructorName}
                                                </div>
                                                <div
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                        overflow: "hidden",
                                                        textOverflow:
                                                            "ellipsis",
                                                        whiteSpace: "nowrap",
                                                    }}
                                                    title={
                                                        instructorEmail ||
                                                        undefined
                                                    }
                                                >
                                                    {instructorEmail ||
                                                        "No email"}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* ID Card Preview Panel */}
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        marginBottom: "1rem",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-id-badge me-2"></i>
                                            ID Verification
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div
                                            style={{
                                                display: "flex",
                                                gap: "0.75rem",
                                            }}
                                        >
                                            <div
                                                style={{ flex: 1, minWidth: 0 }}
                                            >
                                                <div
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.8rem",
                                                        marginBottom: "0.35rem",
                                                    }}
                                                >
                                                    ID Card
                                                </div>
                                                {idCardUrl ? (
                                                    <img
                                                        src={idCardUrl}
                                                        alt="ID Card"
                                                        style={{
                                                            width: "100%",
                                                            height: "90px",
                                                            objectFit: "cover",
                                                            borderRadius:
                                                                "0.5rem",
                                                            backgroundColor:
                                                                "rgba(0,0,0,0.25)",
                                                        }}
                                                    />
                                                ) : (
                                                    <div
                                                        style={{
                                                            height: "90px",
                                                            borderRadius:
                                                                "0.5rem",
                                                            backgroundColor:
                                                                "rgba(0,0,0,0.15)",
                                                            color: "#95a5a6",
                                                            display: "flex",
                                                            alignItems:
                                                                "center",
                                                            justifyContent:
                                                                "center",
                                                            fontSize: "0.8rem",
                                                        }}
                                                    >
                                                        Missing
                                                    </div>
                                                )}
                                            </div>
                                            <div
                                                style={{ flex: 1, minWidth: 0 }}
                                            >
                                                <div
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.8rem",
                                                        marginBottom: "0.35rem",
                                                    }}
                                                >
                                                    Headshot (Today)
                                                </div>
                                                {todayHeadshotUrl ? (
                                                    <img
                                                        src={todayHeadshotUrl}
                                                        alt="Headshot"
                                                        style={{
                                                            width: "100%",
                                                            height: "90px",
                                                            objectFit: "cover",
                                                            borderRadius:
                                                                "0.5rem",
                                                            backgroundColor:
                                                                "rgba(0,0,0,0.25)",
                                                        }}
                                                    />
                                                ) : (
                                                    <div
                                                        style={{
                                                            height: "90px",
                                                            borderRadius:
                                                                "0.5rem",
                                                            backgroundColor:
                                                                "rgba(0,0,0,0.15)",
                                                            color: "#95a5a6",
                                                            display: "flex",
                                                            alignItems:
                                                                "center",
                                                            justifyContent:
                                                                "center",
                                                            fontSize: "0.8rem",
                                                        }}
                                                    >
                                                        Missing
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Chat System Panel */}
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        marginBottom: "1rem",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-comments me-2"></i>
                                            Classroom Chat
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div
                                            style={{
                                                height: "200px",
                                                backgroundColor:
                                                    "rgba(0,0,0,0.15)",
                                                borderRadius: "0.375rem",
                                                display: "flex",
                                                alignItems: "center",
                                                justifyContent: "center",
                                                color: "#95a5a6",
                                            }}
                                        >
                                            <div className="text-center">
                                                <i className="fas fa-comment-dots fa-2x mb-2"></i>
                                                <p
                                                    className="mb-0"
                                                    style={{
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    Chat will appear here
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Course Documents Panel */}
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-folder-open me-2"></i>
                                            Course Documents
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div
                                            style={{
                                                color: "#95a5a6",
                                                fontSize: "0.875rem",
                                                textAlign: "center",
                                                padding: "1rem 0",
                                            }}
                                        >
                                            <i className="fas fa-file-pdf fa-2x mb-2"></i>
                                            <p className="mb-0">
                                                Course resources will appear
                                                here
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </FrostDashboardWrapper>
    );
};

export default MainOnline;
