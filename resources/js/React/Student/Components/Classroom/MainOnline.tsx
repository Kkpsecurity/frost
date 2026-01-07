import React, { useState, useEffect } from "react";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonProgressBar from "./LessonProgressBar";
import { LessonType } from "../../types/classroom";
import ClassroomChatCard from "./ClassroomChatCard";
import AskInstructorCard from "./AskInstructorCard";
import { useClassroomSessionMode } from "../../../Hooks/ClassroomAskInstructorHooks";

interface MainOnlineProps {
    classroom: any;
    student: any;
    validations?: any;
    onBackToDashboard: () => void;
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
const MainOnline: React.FC<MainOnlineProps> = ({ classroom, student, validations, onBackToDashboard }) => {
    const { courseDate, instructor, instUnit } = classroom;
    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(null);

    const courseDateId: number | null = courseDate?.id ?? null;
    const sessionModeQuery = useClassroomSessionMode(courseDateId);
    const sessionMode = sessionModeQuery.data?.mode ?? "TEACHING";

    const instructorName = instructor?.name || instructor?.fname || 'Instructor';
    const instructorEmail = instructor?.email || null;
    const instructorAvatar = instructor?.avatar || '/images/default-avatar.png';

    const getTodayKey = () => {
        try {
            return new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
        } catch {
            return 'monday';
        }
    };

    const idCardUrl: string | null = typeof validations?.idcard === 'string' && validations.idcard.length > 0
        ? validations.idcard
        : null;

    const getTodayHeadshotUrl = (): string | null => {
        const headshot = validations?.headshot;
        if (!headshot) return null;

        if (typeof headshot === 'string') return headshot.length > 0 ? headshot : null;
        if (Array.isArray(headshot)) {
            const found = headshot.find((v: any) => typeof v === 'string' && v.length > 0);
            return found || null;
        }
        if (typeof headshot === 'object') {
            const todayKey = getTodayKey();
            const todayUrl = headshot?.[todayKey];
            if (typeof todayUrl === 'string' && todayUrl.length > 0) return todayUrl;
            const firstUrl = Object.values(headshot).find((v: any) => typeof v === 'string' && v.length > 0);
            return (firstUrl as string) || null;
        }

        return null;
    };

    const todayHeadshotUrl: string | null = getTodayHeadshotUrl();

    // Get lessons from classroom data (already loaded from poll)
    const lessons = (classroom?.lessons || classroom?.data?.lessons || []) as LessonType[];
    const isLoadingLessons = false; // Lessons come from classroom poll data

    const zoom = classroom?.data?.zoom;
    const isZoomReady = !!zoom?.is_ready;
    const screenShareUrl = zoom?.screen_share_url as string | undefined;

    // Get lesson status color
    const getLessonStatusColor = (status: string) => {
        switch (status) {
            case 'passed': return '#1a472a';
            case 'failed': return '#7f1d1d';
            case 'in-progress': return '#1e3a8a';
            default: return '#2c3e50';
        }
    };

    // Get lesson status icon
    const getLessonStatusIcon = (lesson: LessonType) => {
        if (lesson.status === 'passed') {
            return <i className="fas fa-check-circle" style={{ color: '#10b981' }}></i>;
        }
        if (lesson.status === 'failed') {
            return <i className="fas fa-times-circle" style={{ color: '#ef4444' }}></i>;
        }
        if (lesson.status === 'in-progress') {
            return <i className="fas fa-play-circle" style={{ color: '#3b82f6' }}></i>;
        }
        return <i className="far fa-circle" style={{ color: '#6b7280' }}></i>;
    };

    // Handle lesson click
    const handleLessonClick = (lessonId: number) => {
        setSelectedLessonId(lessonId);
    };

    return (
        <div
            className="online-classroom"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                paddingTop: "60px", // Space for main site header
                paddingBottom: "3rem",
            }}
        >
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title="Live Classroom"
                subtitle={`Instructor: ${instructor?.name || "N/A"}`}
                icon={<i className="fas fa-video"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="ONLINE"
            />

            <div className="container-fluid px-0">
                <div className="row g-0">
                    <div className="col-12 px-0">
                        {/* Main Classroom Layout */}
                        <div className="row g-0">
                            {/* Left Sidebar - Lessons */}
                            <div className="col-md-2">
                                <div
                                    style={{
                                        width: "100%",
                                        backgroundColor: "#34495e",
                                        borderRight: "2px solid #2c3e50",
                                        overflowY: "auto",
                                        height: "calc(100vh - 250px)",
                                    }}
                                >
                                    <div className="p-3">
                                        <div className="d-flex justify-content-between align-items-center mb-3">
                                            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                <i className="fas fa-list me-2"></i>
                                                Today's Lessons
                                            </h6>
                                            <span className="badge" style={{ backgroundColor: '#3498db' }}>
                                                {lessons.filter(l => l.status === 'passed').length} / {lessons.length}
                                            </span>
                                        </div>

                                        {/* Real lesson data from API */}
                                        <div className="lesson-list">
                                            {isLoadingLessons ? (
                                                <div className="text-center py-4">
                                                    <div className="spinner-border text-light" role="status">
                                                        <span className="visually-hidden">Loading lessons...</span>
                                                    </div>
                                                </div>
                                            ) : lessons.length === 0 ? (
                                                <div className="text-center py-4" style={{ color: '#95a5a6' }}>
                                                    <i className="fas fa-inbox fa-2x mb-2"></i>
                                                    <p className="mb-0">No lessons available</p>
                                                </div>
                                            ) : (
                                                lessons.map((lesson) => {
                                                    const isSelected = selectedLessonId === lesson.id;
                                                    const baseColor = getLessonStatusColor(lesson.status);
                                                    const selectedColor = '#2563eb';

                                                    return (
                                                        <div
                                                            key={lesson.id}
                                                            className="lesson-item mb-2 p-2"
                                                            onClick={() => handleLessonClick(lesson.id)}
                                                            style={{
                                                                backgroundColor: isSelected ? selectedColor : baseColor,
                                                                borderRadius: "0.25rem",
                                                                cursor: "pointer",
                                                                transition: "all 0.2s",
                                                                border: isSelected ? '2px solid #3b82f6' : '2px solid transparent',
                                                                opacity: isSelected ? 1 : 0.85,
                                                            }}
                                                            onMouseEnter={(e) => {
                                                                if (!isSelected) {
                                                                    e.currentTarget.style.opacity = '1';
                                                                    e.currentTarget.style.transform = 'translateX(4px)';
                                                                }
                                                            }}
                                                            onMouseLeave={(e) => {
                                                                if (!isSelected) {
                                                                    e.currentTarget.style.opacity = '0.85';
                                                                    e.currentTarget.style.transform = 'translateX(0)';
                                                                }
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-start">
                                                                <div className="me-2 mt-1">
                                                                    {getLessonStatusIcon(lesson)}
                                                                </div>
                                                                <div className="flex-grow-1">
                                                                    <div style={{ color: "white", fontSize: "0.875rem", fontWeight: "500" }}>
                                                                        {lesson.title}
                                                                    </div>
                                                                    {lesson.description && (
                                                                        <small style={{ color: "rgba(255,255,255,0.7)", fontSize: "0.7rem", display: 'block', marginTop: '0.25rem' }}>
                                                                            {lesson.description.length > 60
                                                                                ? lesson.description.substring(0, 60) + '...'
                                                                                : lesson.description}
                                                                        </small>
                                                                    )}
                                                                    <div className="d-flex align-items-center mt-1">
                                                                        <small style={{ color: "rgba(255,255,255,0.6)", fontSize: "0.7rem" }}>
                                                                            <i className="far fa-clock me-1"></i>
                                                                            {lesson.duration_minutes} min
                                                                        </small>
                                                                        {lesson.status === 'passed' && (
                                                                            <small className="ms-2" style={{ color: '#10b981', fontSize: "0.7rem" }}>
                                                                                <i className="fas fa-check me-1"></i>
                                                                                Completed
                                                                            </small>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    );
                                                })
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Center - Main Content */}
                            <div className="col-md-7">
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        borderRadius: "0",
                                        overflow: "hidden",
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
                                    selectedLesson={lessons.find(l => l.id === selectedLessonId) || null}
                                    startTime={lessons.find(l => l.id === selectedLessonId)?.started_at || null}
                                />
                            </div>

                            {/* Right Sidebar - Students */}
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
                                        <h6 className="mb-0" style={{ color: "white" }}>
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
                                                    backgroundColor: "rgba(0,0,0,0.25)",
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
                                                        textOverflow: "ellipsis",
                                                        whiteSpace: "nowrap",
                                                    }}
                                                    title={instructorEmail || undefined}
                                                >
                                                    {instructorEmail || "No email"}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Validation Preview (ID + Today's Headshot) */}
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
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-id-badge me-2"></i>
                                            Today's Verification
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div
                                            style={{
                                                display: "flex",
                                                gap: "0.75rem",
                                            }}
                                        >
                                            <div style={{ flex: 1, minWidth: 0 }}>
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
                                                            borderRadius: "0.5rem",
                                                            backgroundColor: "rgba(0,0,0,0.25)",
                                                        }}
                                                    />
                                                ) : (
                                                    <div
                                                        style={{
                                                            height: "90px",
                                                            borderRadius: "0.5rem",
                                                            backgroundColor: "rgba(0,0,0,0.15)",
                                                            color: "#95a5a6",
                                                            display: "flex",
                                                            alignItems: "center",
                                                            justifyContent: "center",
                                                            fontSize: "0.8rem",
                                                        }}
                                                    >
                                                        Missing
                                                    </div>
                                                )}
                                            </div>

                                            <div style={{ flex: 1, minWidth: 0 }}>
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
                                                            borderRadius: "0.5rem",
                                                            backgroundColor: "rgba(0,0,0,0.25)",
                                                        }}
                                                    />
                                                ) : (
                                                    <div
                                                        style={{
                                                            height: "90px",
                                                            borderRadius: "0.5rem",
                                                            backgroundColor: "rgba(0,0,0,0.15)",
                                                            color: "#95a5a6",
                                                            display: "flex",
                                                            alignItems: "center",
                                                            justifyContent: "center",
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

                                {sessionMode === "Q&A" ? (
                                    <ClassroomChatCard courseDateId={courseDateId} />
                                ) : (
                                    <AskInstructorCard courseDateId={courseDateId} mode={sessionMode} />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MainOnline;
