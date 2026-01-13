import React, { useState, useEffect } from "react";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";
import { LessonType } from "../../types/classroom";
import { useVideoQuota } from "../../hooks/useVideoQuota";
import { useLessonSession } from "../../hooks/useLessonSession";
import SessionInfoPanel from "./SessionInfoPanel";
import SecureVideoPlayer from "./SecureVideoPlayer";
import usePhotoUploaded from "../../../Hooks/Web/usePhotoUploaded";

interface MainOfflineProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
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
}) => {
    // Get student context for validations
    const studentContext = useStudent();

    const [activeTab, setActiveTab] = useState<
        "details" | "self-study" | "documentation"
    >("details");
    const [lessons, setLessons] = useState<LessonType[]>([]);
    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(
        null
    );
    const [isLoadingLessons, setIsLoadingLessons] = useState(true);
    const [courseName, setCourseName] = useState<string>("Loading...");
    const [courseAuth, setCourseAuth] = useState<any>(null);

    // View mode: 'list' (default), 'preview' (lesson details), 'player' (video player)
    const [viewMode, setViewMode] = useState<"list" | "preview" | "player">(
        "list"
    );
    const [previewLessonId, setPreviewLessonId] = useState<number | null>(null);

    // Active session data from classroom poll
    const [activeSession, setActiveSession] = useState<any>(null);

    // Settings from API
    const [completionThreshold, setCompletionThreshold] = useState<number>(80);
    const [pauseWarningSeconds, setPauseWarningSeconds] = useState<number>(30);
    const [pauseAlertSound, setPauseAlertSound] = useState<string>(
        "/sounds/pause-warning.mp3"
    );

    // Map courses to their document folders
    const courseDocumentMap: {
        [key: number]: { folder: string; documents: string[] };
    } = {
        1: {
            // Class D (course_id 1)
            folder: "florida-d40",
            documents: [
                "Questions and Answers Chapter 493.pdf",
                "Private Security FAQ.pdf",
                "Job Assistance 09.2023.pdf",
                "Florida Security Officer Handbook.pdf",
                "DOL Newsletter July 2023.pdf",
                "D Certificate Of Completion - Sample - Rev 01.2023.pdf",
                "D License Application.pdf",
            ],
        },
        3: {
            // Class G (course_id 3)
            folder: "florida-g28",
            documents: [
                "FTM Firearm Training Manual Student Rev 01.2023.pdf",
                "Certificate Firearms Proficiency Sample Rev 01.2023.pdf",
                "Temporary G License Agency Character Certification.pdf",
                "G License Application.pdf",
            ],
        },
    };

    // Get documents for current course
    const getDocumentsForCourse = () => {
        if (!courseAuth) return [];
        const courseId = courseAuth.course_id;
        const courseData = courseDocumentMap[courseId];
        if (!courseData) return [];

        return courseData.documents.map((doc) => ({
            name: doc,
            url: `/docs/${courseData.folder}/${doc}`,
        }));
    };

    // Derive selected lesson from selectedLessonId
    const selectedLesson = selectedLessonId
        ? lessons.find((l) => l.id === selectedLessonId)
        : null;

    // Video quota hook - manages student watch time
    const {
        quota,
        isLoading: isLoadingQuota,
        error: quotaError,
    } = useVideoQuota();

    // Lesson session hook - manages active session with locking
    const {
        session,
        isActive: hasActiveSession,
        isLocked: areLessonsLocked,
        timeRemaining,
        pauseRemaining,
        startSession,
        completeSession,
        terminateSession,
    } = useLessonSession();

    // ID Card upload state
    const [idCardUrl, setIdCardUrl] = useState<string | null>(null);
    const [idCardStatus, setIdCardStatus] = useState<
        "missing" | "uploaded" | "approved" | "rejected"
    >("missing");

    // Initialize usePhotoUploaded hook for ID card
    const {
        errorMessage: idErrorMessage,
        setErrorMessage: setIdErrorMessage,
        selectedFile: selectedIdFile,
        setSelectedFile: setSelectedIdFile,
        fileInputRef: idFileInputRef,
        handleFileChange: handleIdFileChange,
        handleFileReset: handleIdFileReset,
        handleUploadImage: handleIdUploadImage,
        isLoading: isIdLoading,
        isUploading: isIdUploading,
        isError: isIdError,
    } = usePhotoUploaded({
        data: courseAuth
            ? { course_date_id: null, student_unit_id: null }
            : null,
        student: student || {},
        photoType: "idcard",
        debug: false,
    });

    // Watch student context for validation changes (ID card from poll)
    useEffect(() => {
        if (studentContext?.validationsByCourseAuth && courseAuthId) {
            const validations = studentContext.validationsByCourseAuth[courseAuthId];
            console.log("📋 [useEffect] Validations from student context:", validations);

            if (validations?.idcard) {
                const idcard = validations.idcard;
                const status = validations.idcard_status || "uploaded";

                console.log("🆔 [useEffect] ID Card URL:", idcard);
                console.log("📊 [useEffect] ID Card Status:", status);

                // ID card is a direct URL string from buildStudentValidationsForCourseAuth
                if (typeof idcard === 'string' && !idcard.includes('no-image')) {
                    setIdCardUrl(idcard);
                    setIdCardStatus(status as "missing" | "uploaded" | "approved" | "rejected");
                    console.log("✅ [useEffect] ID Card loaded from student poll context");
                } else {
                    console.log("ℹ️ [useEffect] No valid ID Card URL");
                }
            } else {
                console.log("ℹ️ [useEffect] No ID card in validations");
            }
        } else {
            console.log("⚠️ [useEffect] Student context or validationsByCourseAuth not available");
        }
    }, [studentContext?.validationsByCourseAuth, courseAuthId]);

    // Load lessons from API (offline mode gets all course lessons)
    useEffect(() => {
        const fetchLessons = async () => {
            try {
                setIsLoadingLessons(true);
                // Use correct GET endpoint with query parameter
                const response = await fetch(
                    `/classroom/class/data?course_auth_id=${courseAuthId}`,
                    {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error("Failed to load lessons");
                }

                const data = await response.json();
                console.log("Lessons loaded:", data); // Debug log

                if (data.success && data.data) {
                    // Set course auth data for document mapping
                    if (data.data.courseAuth) {
                        setCourseAuth(data.data.courseAuth);
                    }

                    // Set course name from response
                    if (data.data.courseAuth?.course_name) {
                        setCourseName(data.data.courseAuth.course_name);
                    }

                    // Set active session if exists
                    if (data.data.active_self_study_session) {
                        setActiveSession(data.data.active_self_study_session);
                    }

                    // Set completion threshold from settings
                    if (data.data.settings?.completion_threshold) {
                        setCompletionThreshold(
                            data.data.settings.completion_threshold
                        );
                    }

                    // Set pause settings from API
                    if (data.data.settings?.pause_warning_seconds) {
                        setPauseWarningSeconds(
                            data.data.settings.pause_warning_seconds
                        );
                    }
                    if (data.data.settings?.pause_alert_sound) {
                        setPauseAlertSound(
                            data.data.settings.pause_alert_sound
                        );
                    }

                    // Set lessons if available
                    if (data.data.lessons) {
                        setLessons(data.data.lessons);
                        // Auto-select first incomplete or first lesson
                        const firstIncomplete = data.data.lessons.find(
                            (l: LessonType) => !l.is_completed
                        );
                        setSelectedLessonId(
                            firstIncomplete?.id ||
                                data.data.lessons[0]?.id ||
                                null
                        );
                    }

                    // Note: ID card validation loading moved to separate useEffect that watches studentContext
                    // This allows real-time updates when poll data changes
                } else {
                    console.warn("No lessons found in response:", data);
                }
            } catch (error) {
                console.error("Error loading lessons:", error);
                setCourseName("Course");
            } finally {
                setIsLoadingLessons(false);
            }
        };

        fetchLessons();
    }, [courseAuthId]);

    // Auto-navigate to active session on mount
    useEffect(() => {
        if (activeSession?.lesson_id && lessons.length > 0) {
            console.log(
                "🎯 Auto-navigating to active session:",
                activeSession.lesson_id
            );

            // Check if the lesson exists in our lessons list
            const activeLesson = lessons.find(
                (l) => l.id === activeSession.lesson_id
            );
            if (activeLesson) {
                // Automatically switch to self-study tab
                setActiveTab("self-study");

                // Select the active lesson
                setSelectedLessonId(activeSession.lesson_id);
                setPreviewLessonId(activeSession.lesson_id);

                // Go directly to player view
                setViewMode("player");
            }
        }
    }, [activeSession, lessons]);

    // Get lesson status color based on completion status
    const getLessonStatusColor = (status: string) => {
        const colors = {
            passed: "#10b981", // green - completed successfully (80%+)
            failed: "#ef4444", // red - failed (< 80%)
            "in-progress": "#3b82f6", // blue - currently in progress
            "not-started": "#6b7280", // gray - not started yet
        };
        return colors[status as keyof typeof colors] || colors["not-started"];
    };

    // Get lesson status icon based on completion status
    const getLessonStatusIcon = (lesson: LessonType) => {
        if (lesson.status === "passed") {
            return (
                <i
                    className="fas fa-check-circle"
                    style={{ color: "#10b981" }}
                ></i>
            );
        }
        if (lesson.status === "failed") {
            return (
                <i
                    className="fas fa-times-circle"
                    style={{ color: "#ef4444" }}
                ></i>
            );
        }
        if (lesson.status === "in-progress") {
            return (
                <i
                    className="fas fa-play-circle"
                    style={{ color: "#3b82f6" }}
                ></i>
            );
        }
        return <i className="far fa-circle" style={{ color: "#6b7280" }}></i>;
    };

    // Handle lesson click
    const handleLessonClick = (lessonId: number) => {
        setSelectedLessonId(lessonId);
        // TODO: Load lesson content in the content area
    };

    return (
        <div
            className="offline-classroom"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                display: "flex",
                flexDirection: "column",
                paddingTop: "60px",
                gap: 0,
            }}
        >
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title={courseName}
                subtitle={`Self-Study Mode | Student: ${
                    student?.name || "N/A"
                }`}
                icon={<i className="fas fa-book-reader"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="OFFLINE"
            />

            {/* Main Layout: Sidebar + Content */}
            <div className="d-flex flex-grow-1" style={{ overflow: "hidden" }}>
                {/* Sidebar - Lessons */}
                <div
                    className="lessons-sidebar"
                    style={{
                        width: "280px",
                        backgroundColor: "#34495e",
                        borderRight: "2px solid #2c3e50",
                        overflowY: "auto",
                        flexShrink: 0,
                    }}
                >
                    <div className="p-3">
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <h6
                                className="mb-0"
                                style={{ color: "white", fontWeight: "600" }}
                            >
                                <i className="fas fa-list me-2"></i>
                                Course Lessons
                            </h6>
                            <span
                                className="badge"
                                style={{ backgroundColor: "#3498db" }}
                            >
                                {lessons.filter((l) => l.is_completed).length} /{" "}
                                {lessons.length}
                            </span>
                        </div>

                        {/* Session Info Panel - Shows when session is active */}
                        {hasActiveSession && session && (
                            <div className="mb-3">
                                <SessionInfoPanel
                                    session={session}
                                    timeRemaining={timeRemaining}
                                    pauseRemaining={pauseRemaining}
                                    onEndSession={() => {
                                        if (
                                            confirm(
                                                "Are you sure you want to end this session? Your progress will be lost."
                                            )
                                        ) {
                                            terminateSession();
                                            setViewMode("list");
                                        }
                                    }}
                                />
                            </div>
                        )}

                        {/* Real lesson data from API */}
                        <div className="lesson-list">
                            {isLoadingLessons ? (
                                <div className="text-center py-4">
                                    <div
                                        className="spinner-border text-light"
                                        role="status"
                                    >
                                        <span className="visually-hidden">
                                            Loading lessons...
                                        </span>
                                    </div>
                                </div>
                            ) : lessons.length === 0 ? (
                                <div
                                    className="text-center py-4"
                                    style={{ color: "#95a5a6" }}
                                >
                                    <i className="fas fa-inbox fa-2x mb-2"></i>
                                    <p className="mb-0">No lessons available</p>
                                </div>
                            ) : (
                                lessons.map((lesson) => {
                                    const isSelected =
                                        selectedLessonId === lesson.id;
                                    const baseColor = getLessonStatusColor(
                                        lesson.status
                                    );
                                    const selectedColor = "#2563eb";

                                    return (
                                        <div
                                            key={lesson.id}
                                            className="lesson-item mb-2 p-2"
                                            onClick={() =>
                                                handleLessonClick(lesson.id)
                                            }
                                            style={{
                                                backgroundColor: isSelected
                                                    ? selectedColor
                                                    : baseColor,
                                                borderRadius: "0.25rem",
                                                cursor: "pointer",
                                                transition: "all 0.2s",
                                                border: isSelected
                                                    ? "2px solid #3b82f6"
                                                    : "2px solid transparent",
                                                opacity: isSelected ? 1 : 0.85,
                                            }}
                                            onMouseEnter={(e) => {
                                                if (!isSelected) {
                                                    e.currentTarget.style.opacity =
                                                        "1";
                                                    e.currentTarget.style.transform =
                                                        "translateX(4px)";
                                                }
                                            }}
                                            onMouseLeave={(e) => {
                                                if (!isSelected) {
                                                    e.currentTarget.style.opacity =
                                                        "0.85";
                                                    e.currentTarget.style.transform =
                                                        "translateX(0)";
                                                }
                                            }}
                                        >
                                            <div className="d-flex align-items-start">
                                                <div className="me-2 mt-1">
                                                    {getLessonStatusIcon(
                                                        lesson
                                                    )}
                                                </div>
                                                <div className="flex-grow-1">
                                                    <div
                                                        style={{
                                                            color: "white",
                                                            fontSize:
                                                                "0.875rem",
                                                            fontWeight: "500",
                                                        }}
                                                    >
                                                        {lesson.title}
                                                    </div>
                                                    {lesson.description && (
                                                        <small
                                                            style={{
                                                                color: "rgba(255,255,255,0.7)",
                                                                fontSize:
                                                                    "0.7rem",
                                                                display:
                                                                    "block",
                                                                marginTop:
                                                                    "0.25rem",
                                                            }}
                                                        >
                                                            {lesson.description
                                                                .length > 60
                                                                ? lesson.description.substring(
                                                                      0,
                                                                      60
                                                                  ) + "..."
                                                                : lesson.description}
                                                        </small>
                                                    )}
                                                    <div className="d-flex align-items-center mt-1">
                                                        <small
                                                            style={{
                                                                color: "rgba(255,255,255,0.6)",
                                                                fontSize:
                                                                    "0.7rem",
                                                            }}
                                                        >
                                                            <i className="far fa-clock me-1"></i>
                                                            {
                                                                lesson.duration_minutes
                                                            }{" "}
                                                            min
                                                        </small>
                                                        {lesson.status ===
                                                            "passed" && (
                                                            <small
                                                                className="ms-2"
                                                                style={{
                                                                    color: "#10b981",
                                                                    fontSize:
                                                                        "0.7rem",
                                                                }}
                                                            >
                                                                <i className="fas fa-check me-1"></i>
                                                                Passed
                                                            </small>
                                                        )}
                                                        {lesson.status ===
                                                            "failed" && (
                                                            <small
                                                                className="ms-2"
                                                                style={{
                                                                    color: "#ef4444",
                                                                    fontSize:
                                                                        "0.7rem",
                                                                }}
                                                            >
                                                                <i className="fas fa-times me-1"></i>
                                                                Failed
                                                            </small>
                                                        )}
                                                        {lesson.status ===
                                                            "in-progress" && (
                                                            <small
                                                                className="ms-2"
                                                                style={{
                                                                    color: "#3b82f6",
                                                                    fontSize:
                                                                        "0.7rem",
                                                                }}
                                                            >
                                                                <i className="fas fa-spinner me-1"></i>
                                                                In Progress
                                                            </small>
                                                        )}
                                                        {isSelected && (
                                                            <small
                                                                className="ms-auto"
                                                                style={{
                                                                    color: "white",
                                                                    fontSize:
                                                                        "0.7rem",
                                                                }}
                                                            >
                                                                <i className="fas fa-arrow-right"></i>
                                                            </small>
                                                        )}
                                                    </div>

                                                    {/* Start/Resume/Locked Button - Only visible on Self Study tab */}
                                                    {
                                                        activeTab ===
                                                            "self-study" && (
                                                            <button
                                                                className={`btn btn-sm mt-2 w-100 ${
                                                                    hasActiveSession &&
                                                                    session?.lessonId ===
                                                                        lesson.id
                                                                        ? "btn-info" // Active lesson - Resume
                                                                        : lesson.status ===
                                                                              "passed" ||
                                                                          lesson.status ===
                                                                              "failed"
                                                                        ? "btn-outline-success" // Completed - Review
                                                                        : "btn-outline-info" // Available - Start
                                                                }`}
                                                                style={{
                                                                    padding:
                                                                        "0.375rem 0.75rem",
                                                                    fontSize:
                                                                        "0.75rem",
                                                                    fontWeight:
                                                                        "600",
                                                                    borderRadius:
                                                                        "0.25rem",
                                                                    cursor:
                                                                        areLessonsLocked &&
                                                                        session?.lessonId !==
                                                                            lesson.id
                                                                            ? "not-allowed"
                                                                            : "pointer",
                                                                    opacity:
                                                                        areLessonsLocked &&
                                                                        session?.lessonId !==
                                                                            lesson.id
                                                                            ? 0.5
                                                                            : 1,
                                                                    transition:
                                                                        "all 0.2s",
                                                                }}
                                                                disabled={
                                                                    areLessonsLocked &&
                                                                    session?.lessonId !==
                                                                        lesson.id
                                                                }
                                                                onClick={(
                                                                    e
                                                                ) => {
                                                                    e.stopPropagation();

                                                                    if (
                                                                        hasActiveSession &&
                                                                        session?.lessonId ===
                                                                            lesson.id
                                                                    ) {
                                                                        // Resume active lesson - go to player
                                                                        console.log(
                                                                            "Resume lesson:",
                                                                            lesson.id
                                                                        );
                                                                        setSelectedLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setPreviewLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setViewMode(
                                                                            "player"
                                                                        );
                                                                    } else if (
                                                                        lesson.status ===
                                                                            "passed" ||
                                                                        lesson.status ===
                                                                            "failed"
                                                                    ) {
                                                                        // Review completed lesson
                                                                        console.log(
                                                                            "Review lesson:",
                                                                            lesson.id
                                                                        );
                                                                        setSelectedLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setPreviewLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setViewMode(
                                                                            "preview"
                                                                        );
                                                                    } else if (
                                                                        !areLessonsLocked
                                                                    ) {
                                                                        // Start new lesson - show preview first
                                                                        console.log(
                                                                            "Start lesson:",
                                                                            lesson.id
                                                                        );
                                                                        setSelectedLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setPreviewLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setViewMode(
                                                                            "preview"
                                                                        );
                                                                    }
                                                                }}
                                                            >
                                                                {areLessonsLocked &&
                                                                session?.lessonId !==
                                                                    lesson.id ? (
                                                                    <>
                                                                        <i className="fas fa-lock me-1"></i>
                                                                        Locked
                                                                    </>
                                                                ) : hasActiveSession &&
                                                                  session?.lessonId ===
                                                                      lesson.id ? (
                                                                    <>
                                                                        <i className="fas fa-play-circle me-1"></i>
                                                                        Resume
                                                                    </>
                                                                ) : lesson.status ===
                                                                      "passed" ||
                                                                  lesson.status ===
                                                                      "failed" ? (
                                                                    <>
                                                                        <i className="fas fa-eye me-1"></i>
                                                                        Review
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                        <i className="fas fa-play me-1"></i>
                                                                        Start
                                                                        Lesson
                                                                    </>
                                                                )}
                                                            </button>
                                                        )
                                                    }

                                                    {
                                                        /* Review Button - Only visible on Self Study tab for completed lessons */
                                                    }
                                                    {
                                                        activeTab ===
                                                            "self-study" &&
                                                            (lesson.status ===
                                                                "passed" ||
                                                                lesson.status ===
                                                                    "failed") && (
                                                                <button
                                                                    className="btn btn-sm mt-2 w-100"
                                                                    style={{
                                                                        backgroundColor:
                                                                            lesson.status ===
                                                                            "passed"
                                                                                ? "#10b981"
                                                                                : "#ef4444",
                                                                        color: "white",
                                                                        border: "none",
                                                                        padding:
                                                                            "0.375rem 0.75rem",
                                                                        fontSize:
                                                                            "0.75rem",
                                                                        fontWeight:
                                                                            "600",
                                                                        borderRadius:
                                                                            "0.25rem",
                                                                        transition:
                                                                            "all 0.2s",
                                                                    }}
                                                                    onClick={(
                                                                        e
                                                                    ) => {
                                                                        e.stopPropagation();
                                                                        console.log(
                                                                            "Review lesson:",
                                                                            lesson.id,
                                                                            lesson.title
                                                                        );
                                                                        // Update selected lesson and show preview screen
                                                                        setSelectedLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setPreviewLessonId(
                                                                            lesson.id
                                                                        );
                                                                        setViewMode(
                                                                            "preview"
                                                                        );
                                                                    }}
                                                                    onMouseEnter={(
                                                                        e
                                                                    ) => {
                                                                        e.currentTarget.style.backgroundColor =
                                                                            "#229954";
                                                                        e.currentTarget.style.transform =
                                                                            "translateY(-2px)";
                                                                        e.currentTarget.style.boxShadow =
                                                                            "0 4px 8px rgba(0,0,0,0.2)";
                                                                    }}
                                                                    onMouseLeave={(
                                                                        e
                                                                    ) => {
                                                                        e.currentTarget.style.backgroundColor =
                                                                            "#27ae60";
                                                                        e.currentTarget.style.transform =
                                                                            "translateY(0)";
                                                                        e.currentTarget.style.boxShadow =
                                                                            "none";
                                                                    }}
                                                                >
                                                                    <i className="fas fa-redo me-1"></i>
                                                                    Review
                                                                    Lesson
                                                                </button>
                                                            )
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </div>

                {/* Content Area */}
                <div
                    className="content-area flex-grow-1 d-flex flex-column"
                    style={{ overflow: "hidden" }}
                >
                    {/* Tabs Navigation */}
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
                                className={`tab-button ${
                                    activeTab === "details" ? "active" : ""
                                }`}
                                onClick={() => setActiveTab("details")}
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
                                        activeTab === "details" ? "600" : "400",
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
                            <button
                                className={`tab-button ${
                                    activeTab === "self-study" ? "active" : ""
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
                            <button
                                className={`tab-button ${
                                    activeTab === "documentation"
                                        ? "active"
                                        : ""
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
                                        activeTab === "documentation"
                                            ? "600"
                                            : "400",
                                    borderBottom:
                                        activeTab === "documentation"
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
                        className="tab-content flex-grow-1 p-4"
                        style={{ overflowY: "auto" }}
                    >
                        {activeTab === "details" && (
                            <div className="details-tab">
                                <h4
                                    className="mb-4"
                                    style={{
                                        color: "white",
                                        fontSize: "1.75rem",
                                        fontWeight: "600",
                                    }}
                                >
                                    <i
                                        className="fas fa-tachometer-alt me-2"
                                        style={{ color: "#3498db" }}
                                    ></i>
                                    Learning Dashboard
                                </h4>

                                {/* Quick Stats Row */}
                                <div className="row g-3 mb-4">
                                    <div className="col-md-3">
                                        <div
                                            className="card"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div className="card-body text-center">
                                                <div
                                                    style={{
                                                        fontSize: "2rem",
                                                        color: "#3498db",
                                                        marginBottom: "0.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-book-open"></i>
                                                </div>
                                                <h3
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontSize: "2rem",
                                                    }}
                                                >
                                                    {
                                                        lessons.filter(
                                                            (l) =>
                                                                l.is_completed
                                                        ).length
                                                    }
                                                    /{lessons.length}
                                                </h3>
                                                <p
                                                    className="mb-0"
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    Lessons Complete
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div
                                            className="card"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div className="card-body text-center">
                                                <div
                                                    style={{
                                                        fontSize: "2rem",
                                                        color: "#2ecc71",
                                                        marginBottom: "0.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-chart-line"></i>
                                                </div>
                                                <h3
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontSize: "2rem",
                                                    }}
                                                >
                                                    {lessons.length > 0
                                                        ? Math.round(
                                                              (lessons.filter(
                                                                  (l) =>
                                                                      l.is_completed
                                                              ).length /
                                                                  lessons.length) *
                                                                  100
                                                          )
                                                        : 0}
                                                    %
                                                </h3>
                                                <p
                                                    className="mb-0"
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    Progress
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div
                                            className="card"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div className="card-body text-center">
                                                <div
                                                    style={{
                                                        fontSize: "2rem",
                                                        color: "#f39c12",
                                                        marginBottom: "0.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-clock"></i>
                                                </div>
                                                <h3
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontSize: "2rem",
                                                    }}
                                                >
                                                    {lessons.reduce(
                                                        (sum, l) =>
                                                            sum +
                                                            (l.duration_minutes ||
                                                                0),
                                                        0
                                                    )}
                                                </h3>
                                                <p
                                                    className="mb-0"
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    Total Minutes
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div
                                            className="card"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div className="card-body text-center">
                                                <div
                                                    style={{
                                                        fontSize: "2rem",
                                                        color: "#e74c3c",
                                                        marginBottom: "0.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-tasks"></i>
                                                </div>
                                                <h3
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontSize: "2rem",
                                                    }}
                                                >
                                                    {
                                                        lessons.filter(
                                                            (l) =>
                                                                !l.is_completed
                                                        ).length
                                                    }
                                                </h3>
                                                <p
                                                    className="mb-0"
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    Remaining
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Main Content Row */}
                                <div className="row g-3">
                                    {/* Left Column */}
                                    <div className="col-md-8">
                                        {/* Today's Attendance */}
                                        <div
                                            className="card mb-3"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div
                                                className="card-header"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    borderBottom:
                                                        "1px solid rgba(255,255,255,0.1)",
                                                    borderRadius:
                                                        "0.5rem 0.5rem 0 0",
                                                }}
                                            >
                                                <h6
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-calendar-check me-2"
                                                        style={{
                                                            color: "#2ecc71",
                                                        }}
                                                    ></i>
                                                    Today's Attendance
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <div className="row g-2">
                                                    <div className="col-6">
                                                        <div
                                                            className="p-3"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                borderLeft:
                                                                    "4px solid #2ecc71",
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center">
                                                                <div className="me-3">
                                                                    <i
                                                                        className="fas fa-sign-in-alt"
                                                                        style={{
                                                                            fontSize:
                                                                                "2rem",
                                                                            color: "#2ecc71",
                                                                        }}
                                                                    ></i>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        style={{
                                                                            color: "#95a5a6",
                                                                            fontSize:
                                                                                "0.75rem",
                                                                            marginBottom:
                                                                                "0.25rem",
                                                                        }}
                                                                    >
                                                                        Check-In
                                                                        Time
                                                                    </div>
                                                                    <div
                                                                        style={{
                                                                            color: "white",
                                                                            fontSize:
                                                                                "1.1rem",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                    >
                                                                        {new Date().toLocaleTimeString(
                                                                            [],
                                                                            {
                                                                                hour: "2-digit",
                                                                                minute: "2-digit",
                                                                            }
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-6">
                                                        <div
                                                            className="p-3"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                borderLeft:
                                                                    "4px solid #3498db",
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center">
                                                                <div className="me-3">
                                                                    <i
                                                                        className="fas fa-clock"
                                                                        style={{
                                                                            fontSize:
                                                                                "2rem",
                                                                            color: "#3498db",
                                                                        }}
                                                                    ></i>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        style={{
                                                                            color: "#95a5a6",
                                                                            fontSize:
                                                                                "0.75rem",
                                                                            marginBottom:
                                                                                "0.25rem",
                                                                        }}
                                                                    >
                                                                        Study
                                                                        Duration
                                                                    </div>
                                                                    <div
                                                                        style={{
                                                                            color: "white",
                                                                            fontSize:
                                                                                "1.1rem",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                    >
                                                                        0h 0m
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Student Units - Recent 5 */}
                                        <div
                                            className="card mb-3"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div
                                                className="card-header d-flex justify-content-between align-items-center"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    borderBottom:
                                                        "1px solid rgba(255,255,255,0.1)",
                                                    borderRadius:
                                                        "0.5rem 0.5rem 0 0",
                                                }}
                                            >
                                                <h6
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-history me-2"
                                                        style={{
                                                            color: "#f39c12",
                                                        }}
                                                    ></i>
                                                    Recent Study Sessions
                                                </h6>
                                                <span
                                                    className="badge"
                                                    style={{
                                                        backgroundColor:
                                                            "#3498db",
                                                    }}
                                                >
                                                    Last 5
                                                </span>
                                            </div>
                                            <div className="card-body">
                                                <div className="table-responsive">
                                                    <table
                                                        className="table table-sm table-hover mb-0"
                                                        style={{
                                                            color: "#95a5a6",
                                                        }}
                                                    >
                                                        <thead>
                                                            <tr
                                                                style={{
                                                                    borderBottom:
                                                                        "1px solid rgba(255,255,255,0.1)",
                                                                }}
                                                            >
                                                                <th
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "500",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                    }}
                                                                >
                                                                    Date
                                                                </th>
                                                                <th
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "500",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                    }}
                                                                >
                                                                    Course
                                                                </th>
                                                                <th
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "500",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                    }}
                                                                >
                                                                    Lessons
                                                                </th>
                                                                <th
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "500",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                    }}
                                                                >
                                                                    Duration
                                                                </th>
                                                                <th
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "500",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                    }}
                                                                >
                                                                    Status
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr
                                                                style={{
                                                                    borderBottom:
                                                                        "1px solid rgba(255,255,255,0.05)",
                                                                }}
                                                            >
                                                                <td
                                                                    style={{
                                                                        padding:
                                                                            "0.75rem",
                                                                        color: "#95a5a6",
                                                                    }}
                                                                >
                                                                    {new Date().toLocaleDateString()}
                                                                </td>
                                                                <td
                                                                    style={{
                                                                        padding:
                                                                            "0.75rem",
                                                                        color: "#95a5a6",
                                                                    }}
                                                                >
                                                                    {courseName}
                                                                </td>
                                                                <td
                                                                    style={{
                                                                        padding:
                                                                            "0.75rem",
                                                                    }}
                                                                >
                                                                    <span
                                                                        style={{
                                                                            color: "#3498db",
                                                                            fontWeight:
                                                                                "500",
                                                                        }}
                                                                    >
                                                                        {
                                                                            lessons.filter(
                                                                                (
                                                                                    l
                                                                                ) =>
                                                                                    l.is_completed
                                                                            )
                                                                                .length
                                                                        }
                                                                        /
                                                                        {
                                                                            lessons.length
                                                                        }
                                                                    </span>
                                                                </td>
                                                                <td
                                                                    style={{
                                                                        padding:
                                                                            "0.75rem",
                                                                        color: "#95a5a6",
                                                                    }}
                                                                >
                                                                    0h 0m
                                                                </td>
                                                                <td
                                                                    style={{
                                                                        padding:
                                                                            "0.75rem",
                                                                    }}
                                                                >
                                                                    <span
                                                                        className="badge"
                                                                        style={{
                                                                            backgroundColor:
                                                                                "#2ecc71",
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className="fas fa-circle me-1"
                                                                            style={{
                                                                                fontSize:
                                                                                    "0.5rem",
                                                                            }}
                                                                        ></i>
                                                                        Active
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            {[1, 2, 3, 4].map(
                                                                (item) => (
                                                                    <tr
                                                                        key={
                                                                            item
                                                                        }
                                                                        style={{
                                                                            borderBottom:
                                                                                "1px solid rgba(255,255,255,0.05)",
                                                                        }}
                                                                    >
                                                                        <td
                                                                            style={{
                                                                                padding:
                                                                                    "0.75rem",
                                                                                color: "#7f8c8d",
                                                                            }}
                                                                        >
                                                                            {new Date(
                                                                                Date.now() -
                                                                                    item *
                                                                                        24 *
                                                                                        60 *
                                                                                        60 *
                                                                                        1000
                                                                            ).toLocaleDateString()}
                                                                        </td>
                                                                        <td
                                                                            style={{
                                                                                padding:
                                                                                    "0.75rem",
                                                                                color: "#7f8c8d",
                                                                            }}
                                                                        >
                                                                            Previous
                                                                            Session
                                                                        </td>
                                                                        <td
                                                                            style={{
                                                                                padding:
                                                                                    "0.75rem",
                                                                                color: "#7f8c8d",
                                                                            }}
                                                                        >
                                                                            -
                                                                        </td>
                                                                        <td
                                                                            style={{
                                                                                padding:
                                                                                    "0.75rem",
                                                                                color: "#7f8c8d",
                                                                            }}
                                                                        >
                                                                            -
                                                                        </td>
                                                                        <td
                                                                            style={{
                                                                                padding:
                                                                                    "0.75rem",
                                                                            }}
                                                                        >
                                                                            <span
                                                                                className="badge"
                                                                                style={{
                                                                                    backgroundColor:
                                                                                        "#95a5a6",
                                                                                }}
                                                                            >
                                                                                <i
                                                                                    className="fas fa-check me-1"
                                                                                    style={{
                                                                                        fontSize:
                                                                                            "0.6rem",
                                                                                    }}
                                                                                ></i>
                                                                                Completed
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                )
                                                            )}
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div
                                                    className="d-flex justify-content-between align-items-center mt-3 pt-3"
                                                    style={{
                                                        borderTop:
                                                            "1px solid rgba(255,255,255,0.1)",
                                                    }}
                                                >
                                                    <span
                                                        style={{
                                                            color: "#95a5a6",
                                                            fontSize:
                                                                "0.875rem",
                                                        }}
                                                    >
                                                        Showing 5 of 5 sessions
                                                    </span>
                                                    <div
                                                        className="btn-group"
                                                        role="group"
                                                    >
                                                        <button
                                                            className="btn btn-sm"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                border: "none",
                                                                color: "#95a5a6",
                                                            }}
                                                            disabled
                                                        >
                                                            <i className="fas fa-chevron-left"></i>
                                                        </button>
                                                        <button
                                                            className="btn btn-sm"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                border: "none",
                                                                color: "#95a5a6",
                                                            }}
                                                            disabled
                                                        >
                                                            <i className="fas fa-chevron-right"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* ID Card Upload Section */}
                                        <div
                                            className="card mb-3"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div
                                                className="card-header"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    borderBottom:
                                                        "1px solid rgba(255,255,255,0.1)",
                                                    borderRadius:
                                                        "0.5rem 0.5rem 0 0",
                                                }}
                                            >
                                                <h6
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-id-card me-2"
                                                        style={{
                                                            color: "#f39c12",
                                                        }}
                                                    ></i>
                                                    ID Card Verification
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                {/* Hidden file input */}
                                                <input
                                                    type="file"
                                                    ref={idFileInputRef}
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    onChange={
                                                        handleIdFileChange
                                                    }
                                                    style={{ display: "none" }}
                                                />

                                                {/* Upload/Preview Area */}
                                                <div className="text-center mb-3">
                                                    {selectedIdFile ? (
                                                        // Preview selected file
                                                        <div
                                                            style={{
                                                                width: "100%",
                                                                marginBottom:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            <img
                                                                src={URL.createObjectURL(
                                                                    selectedIdFile
                                                                )}
                                                                alt="ID Card Preview"
                                                                style={{
                                                                    width: "100%",
                                                                    maxHeight:
                                                                        "300px",
                                                                    objectFit:
                                                                        "contain",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    backgroundColor:
                                                                        "rgba(0,0,0,0.25)",
                                                                    border: "2px solid #3498db",
                                                                }}
                                                            />
                                                            <div className="mt-3 d-flex gap-2">
                                                                <button
                                                                    className="btn btn-success flex-fill"
                                                                    onClick={async () => {
                                                                        try {
                                                                            await handleIdUploadImage();
                                                                            alert(
                                                                                "ID card uploaded successfully!"
                                                                            );
                                                                            setIdCardStatus(
                                                                                "uploaded"
                                                                            );
                                                                        } catch (error) {
                                                                            console.error(
                                                                                "Upload error:",
                                                                                error
                                                                            );
                                                                        }
                                                                    }}
                                                                    disabled={
                                                                        isIdUploading
                                                                    }
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#2ecc71",
                                                                        border: "none",
                                                                        padding:
                                                                            "0.75rem",
                                                                        fontWeight:
                                                                            "600",
                                                                    }}
                                                                >
                                                                    {isIdUploading ? (
                                                                        <>
                                                                            <i className="fas fa-spinner fa-spin me-2"></i>
                                                                            Uploading...
                                                                        </>
                                                                    ) : (
                                                                        <>
                                                                            <i className="fas fa-check me-2"></i>
                                                                            Upload
                                                                            ID
                                                                            Card
                                                                        </>
                                                                    )}
                                                                </button>
                                                                <button
                                                                    className="btn btn-outline-light"
                                                                    onClick={
                                                                        handleIdFileReset
                                                                    }
                                                                    disabled={
                                                                        isIdUploading
                                                                    }
                                                                    style={{
                                                                        border: "2px solid rgba(255,255,255,0.3)",
                                                                        padding:
                                                                            "0.75rem",
                                                                    }}
                                                                >
                                                                    <i className="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    ) : idCardUrl ? (
                                                        // Show uploaded ID card
                                                        <div
                                                            style={{
                                                                width: "100%",
                                                                marginBottom:
                                                                    "1rem",
                                                            }}
                                                        >
                                                            <img
                                                                src={idCardUrl}
                                                                alt="Uploaded ID Card"
                                                                style={{
                                                                    width: "100%",
                                                                    maxHeight:
                                                                        "300px",
                                                                    objectFit:
                                                                        "contain",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    backgroundColor:
                                                                        "rgba(0,0,0,0.25)",
                                                                    border: `2px solid ${
                                                                        idCardStatus ===
                                                                        "approved"
                                                                            ? "#2ecc71"
                                                                            : idCardStatus ===
                                                                              "rejected"
                                                                            ? "#e74c3c"
                                                                            : "#3498db"
                                                                    }`,
                                                                }}
                                                            />
                                                            <div className="mt-2">
                                                                <span
                                                                    className={`badge ${
                                                                        idCardStatus ===
                                                                        "approved"
                                                                            ? "bg-success"
                                                                            : idCardStatus ===
                                                                              "rejected"
                                                                            ? "bg-danger"
                                                                            : "bg-warning"
                                                                    }`}
                                                                >
                                                                    {idCardStatus ===
                                                                        "approved" && (
                                                                        <>
                                                                            <i className="fas fa-check-circle me-1"></i>
                                                                            Approved
                                                                        </>
                                                                    )}
                                                                    {idCardStatus ===
                                                                        "rejected" && (
                                                                        <>
                                                                            <i className="fas fa-times-circle me-1"></i>
                                                                            Rejected
                                                                        </>
                                                                    )}
                                                                    {idCardStatus ===
                                                                        "uploaded" && (
                                                                        <>
                                                                            <i className="fas fa-clock me-1"></i>
                                                                            Pending
                                                                            Review
                                                                        </>
                                                                    )}
                                                                </span>
                                                            </div>
                                                            <button
                                                                className="btn btn-outline-light w-100 mt-2"
                                                                onClick={() =>
                                                                    idFileInputRef.current?.click()
                                                                }
                                                                style={{
                                                                    border: "2px solid rgba(255,255,255,0.3)",
                                                                    padding:
                                                                        "0.5rem",
                                                                }}
                                                            >
                                                                <i className="fas fa-redo me-2"></i>
                                                                Re-upload ID
                                                                Card
                                                            </button>
                                                        </div>
                                                    ) : (
                                                        // Upload prompt
                                                        <>
                                                            <div
                                                                style={{
                                                                    width: "100%",
                                                                    padding:
                                                                        "3rem 1rem",
                                                                    border: "2px dashed rgba(255,255,255,0.2)",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    backgroundColor:
                                                                        "rgba(52, 152, 219, 0.1)",
                                                                    marginBottom:
                                                                        "1rem",
                                                                    cursor: "pointer",
                                                                }}
                                                                onClick={() =>
                                                                    idFileInputRef.current?.click()
                                                                }
                                                            >
                                                                <i
                                                                    className="fas fa-cloud-upload-alt"
                                                                    style={{
                                                                        fontSize:
                                                                            "3rem",
                                                                        color: "#3498db",
                                                                        marginBottom:
                                                                            "1rem",
                                                                        display:
                                                                            "block",
                                                                    }}
                                                                ></i>
                                                                <p
                                                                    style={{
                                                                        color: "#95a5a6",
                                                                        marginBottom:
                                                                            "0.5rem",
                                                                    }}
                                                                >
                                                                    Upload your
                                                                    government-issued
                                                                    ID
                                                                </p>
                                                                <p
                                                                    style={{
                                                                        color: "#7f8c8d",
                                                                        fontSize:
                                                                            "0.85rem",
                                                                    }}
                                                                >
                                                                    Accepted
                                                                    formats:
                                                                    JPG, PNG
                                                                    (Max 5MB)
                                                                </p>
                                                            </div>
                                                            <button
                                                                className="btn btn-primary w-100"
                                                                onClick={() =>
                                                                    idFileInputRef.current?.click()
                                                                }
                                                                style={{
                                                                    backgroundColor:
                                                                        "#3498db",
                                                                    border: "none",
                                                                    padding:
                                                                        "0.75rem",
                                                                    fontWeight:
                                                                        "600",
                                                                }}
                                                            >
                                                                <i className="fas fa-upload me-2"></i>
                                                                Select ID Card
                                                            </button>
                                                        </>
                                                    )}

                                                    {/* Error message */}
                                                    {idErrorMessage && (
                                                        <div
                                                            className="alert alert-danger mt-3 mb-0"
                                                            role="alert"
                                                        >
                                                            <i className="fas fa-exclamation-triangle me-2"></i>
                                                            {idErrorMessage}
                                                        </div>
                                                    )}
                                                </div>

                                                {/* Requirement notice */}
                                                {!idCardUrl && (
                                                    <div
                                                        style={{
                                                            backgroundColor:
                                                                "rgba(231, 76, 60, 0.1)",
                                                            padding: "1rem",
                                                            borderRadius:
                                                                "0.5rem",
                                                            border: "1px solid rgba(231, 76, 60, 0.3)",
                                                        }}
                                                    >
                                                        <p
                                                            style={{
                                                                color: "#e74c3c",
                                                                fontSize:
                                                                    "0.9rem",
                                                                marginBottom:
                                                                    "0.5rem",
                                                                fontWeight:
                                                                    "600",
                                                            }}
                                                        >
                                                            <i className="fas fa-exclamation-triangle me-2"></i>
                                                            Required for Course
                                                            Access
                                                        </p>
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                                fontSize:
                                                                    "0.85rem",
                                                                marginBottom: 0,
                                                            }}
                                                        >
                                                            You must upload a
                                                            valid ID card to
                                                            complete your
                                                            enrollment and
                                                            access course
                                                            materials.
                                                        </p>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Right Column */}
                                    <div className="col-md-4">
                                        {/* Quick Actions */}
                                        <div
                                            className="card mb-3"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div
                                                className="card-header"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    borderBottom:
                                                        "1px solid rgba(255,255,255,0.1)",
                                                    borderRadius:
                                                        "0.5rem 0.5rem 0 0",
                                                }}
                                            >
                                                <h6
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-bolt me-2"
                                                        style={{
                                                            color: "#f39c12",
                                                        }}
                                                    ></i>
                                                    Quick Actions
                                                </h6>
                                            </div>
                                            <div className="card-body d-grid gap-2">
                                                {selectedLessonId &&
                                                    !lessons.find(
                                                        (l) =>
                                                            l.id ===
                                                            selectedLessonId
                                                    )?.is_completed && (
                                                        <button
                                                            className="btn btn-primary w-100"
                                                            style={{
                                                                backgroundColor:
                                                                    "#3498db",
                                                                border: "none",
                                                                padding:
                                                                    "0.75rem",
                                                                fontWeight:
                                                                    "600",
                                                            }}
                                                            onClick={() =>
                                                                setActiveTab(
                                                                    "self-study"
                                                                )
                                                            }
                                                        >
                                                            <i className="fas fa-play me-2"></i>
                                                            Resume Current
                                                            Lesson
                                                        </button>
                                                    )}
                                                <button
                                                    className="btn btn-outline-light w-100"
                                                    style={{
                                                        borderColor: "#3498db",
                                                        color: "#3498db",
                                                        padding: "0.75rem",
                                                        fontWeight: "500",
                                                    }}
                                                    onClick={() =>
                                                        setActiveTab(
                                                            "documentation"
                                                        )
                                                    }
                                                >
                                                    <i className="fas fa-file-pdf me-2"></i>
                                                    View Course Materials
                                                </button>
                                            </div>
                                        </div>

                                        {/* Student Info */}
                                        <div
                                            className="card"
                                            style={{
                                                backgroundColor: "#2c3e50",
                                                border: "none",
                                                borderRadius: "0.5rem",
                                            }}
                                        >
                                            <div
                                                className="card-header"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    borderBottom:
                                                        "1px solid rgba(255,255,255,0.1)",
                                                    borderRadius:
                                                        "0.5rem 0.5rem 0 0",
                                                }}
                                            >
                                                <h6
                                                    className="mb-0"
                                                    style={{
                                                        color: "white",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    <i
                                                        className="fas fa-user me-2"
                                                        style={{
                                                            color: "#2ecc71",
                                                        }}
                                                    ></i>
                                                    Student Profile
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <div className="text-center mb-3">
                                                    <div
                                                        style={{
                                                            width: "80px",
                                                            height: "80px",
                                                            margin: "0 auto",
                                                            borderRadius: "50%",
                                                            backgroundColor:
                                                                "#3498db",
                                                            display: "flex",
                                                            alignItems:
                                                                "center",
                                                            justifyContent:
                                                                "center",
                                                            fontSize: "2rem",
                                                            color: "white",
                                                            fontWeight: "600",
                                                        }}
                                                    >
                                                        {student?.name
                                                            ?.charAt(0)
                                                            ?.toUpperCase() ||
                                                            "S"}
                                                    </div>
                                                </div>
                                                <table
                                                    style={{
                                                        width: "100%",
                                                        color: "#95a5a6",
                                                        fontSize: "0.875rem",
                                                    }}
                                                >
                                                    <tbody>
                                                        <tr>
                                                            <td
                                                                style={{
                                                                    padding:
                                                                        "0.5rem 0",
                                                                    fontWeight:
                                                                        "500",
                                                                    color: "white",
                                                                }}
                                                            >
                                                                Name:
                                                            </td>
                                                            <td
                                                                style={{
                                                                    padding:
                                                                        "0.5rem 0",
                                                                    textAlign:
                                                                        "right",
                                                                }}
                                                            >
                                                                {student?.name ||
                                                                    "N/A"}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style={{
                                                                    padding:
                                                                        "0.5rem 0",
                                                                    fontWeight:
                                                                        "500",
                                                                    color: "white",
                                                                }}
                                                            >
                                                                Email:
                                                            </td>
                                                            <td
                                                                style={{
                                                                    padding:
                                                                        "0.5rem 0",
                                                                    textAlign:
                                                                        "right",
                                                                    fontSize:
                                                                        "0.75rem",
                                                                }}
                                                            >
                                                                {student?.email ||
                                                                    "N/A"}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Info Alert */}
                                <div
                                    className="alert mt-3 mb-0"
                                    style={{
                                        backgroundColor:
                                            "rgba(52, 152, 219, 0.1)",
                                        border: "1px solid rgba(52, 152, 219, 0.3)",
                                        borderRadius: "0.5rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-info-circle me-2"
                                        style={{ color: "#3498db" }}
                                    ></i>
                                    <span style={{ color: "#3498db" }}>
                                        You are in self-study mode. Select a
                                        lesson from the sidebar to begin
                                        learning, or switch to the Self Study
                                        tab for video content.
                                    </span>
                                </div>
                            </div>
                        )}

                        {
                            activeTab === "self-study" && (
                                <div className="self-study-tab">
                                    {viewMode === "list" && (
                                        <>
                                            <h4
                                                className="mb-4"
                                                style={{
                                                    color: "white",
                                                    fontSize: "1.75rem",
                                                    fontWeight: "600",
                                                }}
                                            >
                                                <i
                                                    className="fas fa-graduation-cap me-2"
                                                    style={{ color: "#3498db" }}
                                                ></i>
                                                Self-Study Mode
                                            </h4>

                                            {/* Welcome and Instructions */}
                                            <div className="row mb-4">
                                                <div className="col-12">
                                                    <div
                                                        className="card"
                                                        style={{
                                                            backgroundColor:
                                                                "#2c3e50",
                                                            border: "2px solid #3498db",
                                                            borderRadius:
                                                                "0.5rem",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-body"
                                                            style={{
                                                                padding: "2rem",
                                                            }}
                                                        >
                                                            <h5
                                                                style={{
                                                                    color: "white",
                                                                    marginBottom:
                                                                        "1rem",
                                                                    fontWeight:
                                                                        "600",
                                                                }}
                                                            >
                                                                <i
                                                                    className="fas fa-info-circle me-2"
                                                                    style={{
                                                                        color: "#3498db",
                                                                    }}
                                                                ></i>
                                                                What is
                                                                Self-Study Mode?
                                                            </h5>
                                                            <p
                                                                style={{
                                                                    color: "#ecf0f1",
                                                                    fontSize:
                                                                        "1rem",
                                                                    lineHeight:
                                                                        "1.6",
                                                                    marginBottom:
                                                                        "1.5rem",
                                                                }}
                                                            >
                                                                Self-study mode
                                                                allows you to
                                                                watch recorded
                                                                video lessons
                                                                independently,
                                                                outside of live
                                                                instructor-led
                                                                classes. This
                                                                feature is
                                                                designed to help
                                                                you succeed in
                                                                your coursework.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Two Main Purposes */}
                                            <div className="row mb-4">
                                                {/* Purpose 1: Make Up Failed Lessons */}
                                                <div className="col-md-6 mb-3">
                                                    <div
                                                        className="card h-100"
                                                        style={{
                                                            backgroundColor:
                                                                "#27ae60",
                                                            border: "none",
                                                            borderRadius:
                                                                "0.5rem",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-body"
                                                            style={{
                                                                padding:
                                                                    "1.5rem",
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center mb-3">
                                                                <div
                                                                    style={{
                                                                        width: "50px",
                                                                        height: "50px",
                                                                        borderRadius:
                                                                            "50%",
                                                                        backgroundColor:
                                                                            "rgba(255,255,255,0.2)",
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "center",
                                                                        justifyContent:
                                                                            "center",
                                                                        marginRight:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-redo-alt"
                                                                        style={{
                                                                            fontSize:
                                                                                "1.5rem",
                                                                            color: "white",
                                                                        }}
                                                                    ></i>
                                                                </div>
                                                                <h6
                                                                    style={{
                                                                        color: "white",
                                                                        margin: 0,
                                                                        fontWeight:
                                                                            "600",
                                                                        fontSize:
                                                                            "1.1rem",
                                                                    }}
                                                                >
                                                                    Purpose 1:
                                                                    Make Up
                                                                    Failed
                                                                    Lessons
                                                                </h6>
                                                            </div>
                                                            <p
                                                                style={{
                                                                    color: "rgba(255,255,255,0.95)",
                                                                    fontSize:
                                                                        "0.95rem",
                                                                    lineHeight:
                                                                        "1.5",
                                                                    marginBottom:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                Use self-study
                                                                to review and
                                                                master content
                                                                after failing a
                                                                live lesson.
                                                                This helps you
                                                                prepare before
                                                                retaking the
                                                                live class.
                                                            </p>
                                                            <div
                                                                style={{
                                                                    backgroundColor:
                                                                        "rgba(255,255,255,0.15)",
                                                                    padding:
                                                                        "1rem",
                                                                    borderRadius:
                                                                        "0.375rem",
                                                                    marginTop:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <div
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                        marginBottom:
                                                                            "0.5rem",
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "center",
                                                                    }}
                                                                >
                                                                    <i className="fas fa-gift me-2"></i>
                                                                    Hour Refund
                                                                    Policy
                                                                </div>
                                                                <p
                                                                    style={{
                                                                        color: "rgba(255,255,255,0.95)",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                        marginBottom:
                                                                            "0.5rem",
                                                                        lineHeight:
                                                                            "1.5",
                                                                    }}
                                                                >
                                                                    ✅{" "}
                                                                    <strong>
                                                                        Your
                                                                        hours
                                                                        are
                                                                        refunded!
                                                                    </strong>
                                                                </p>
                                                                <ol
                                                                    style={{
                                                                        color: "rgba(255,255,255,0.9)",
                                                                        fontSize:
                                                                            "0.85rem",
                                                                        paddingLeft:
                                                                            "1.25rem",
                                                                        marginBottom: 0,
                                                                        lineHeight:
                                                                            "1.6",
                                                                    }}
                                                                >
                                                                    <li>
                                                                        Fail a
                                                                        live
                                                                        lesson
                                                                    </li>
                                                                    <li>
                                                                        Complete
                                                                        it
                                                                        successfully
                                                                        in
                                                                        self-study
                                                                    </li>
                                                                    <li>
                                                                        Retake
                                                                        and pass
                                                                        the live
                                                                        class
                                                                    </li>
                                                                    <li>
                                                                        <strong>
                                                                            Result:
                                                                            Hours
                                                                            refunded
                                                                            to
                                                                            your
                                                                            quota!
                                                                        </strong>
                                                                    </li>
                                                                </ol>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Purpose 2: Get a Head Start */}
                                                <div className="col-md-6 mb-3">
                                                    <div
                                                        className="card h-100"
                                                        style={{
                                                            backgroundColor:
                                                                "#3498db",
                                                            border: "none",
                                                            borderRadius:
                                                                "0.5rem",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-body"
                                                            style={{
                                                                padding:
                                                                    "1.5rem",
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center mb-3">
                                                                <div
                                                                    style={{
                                                                        width: "50px",
                                                                        height: "50px",
                                                                        borderRadius:
                                                                            "50%",
                                                                        backgroundColor:
                                                                            "rgba(255,255,255,0.2)",
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "center",
                                                                        justifyContent:
                                                                            "center",
                                                                        marginRight:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-rocket"
                                                                        style={{
                                                                            fontSize:
                                                                                "1.5rem",
                                                                            color: "white",
                                                                        }}
                                                                    ></i>
                                                                </div>
                                                                <h6
                                                                    style={{
                                                                        color: "white",
                                                                        margin: 0,
                                                                        fontWeight:
                                                                            "600",
                                                                        fontSize:
                                                                            "1.1rem",
                                                                    }}
                                                                >
                                                                    Purpose 2:
                                                                    Get a Head
                                                                    Start
                                                                </h6>
                                                            </div>
                                                            <p
                                                                style={{
                                                                    color: "rgba(255,255,255,0.95)",
                                                                    fontSize:
                                                                        "0.95rem",
                                                                    lineHeight:
                                                                        "1.5",
                                                                    marginBottom:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                Preview lessons
                                                                before attending
                                                                the live class.
                                                                This helps you
                                                                come prepared
                                                                and get more
                                                                value from
                                                                instructor-led
                                                                sessions.
                                                            </p>
                                                            <div
                                                                style={{
                                                                    backgroundColor:
                                                                        "rgba(255,255,255,0.15)",
                                                                    padding:
                                                                        "1rem",
                                                                    borderRadius:
                                                                        "0.375rem",
                                                                    marginTop:
                                                                        "1rem",
                                                                }}
                                                            >
                                                                <div
                                                                    style={{
                                                                        color: "white",
                                                                        fontWeight:
                                                                            "600",
                                                                        marginBottom:
                                                                            "0.5rem",
                                                                        display:
                                                                            "flex",
                                                                        alignItems:
                                                                            "center",
                                                                    }}
                                                                >
                                                                    <i className="fas fa-clock me-2"></i>
                                                                    Hour Usage
                                                                    Policy
                                                                </div>
                                                                <p
                                                                    style={{
                                                                        color: "rgba(255,255,255,0.95)",
                                                                        fontSize:
                                                                            "0.875rem",
                                                                        marginBottom: 0,
                                                                        lineHeight:
                                                                            "1.5",
                                                                    }}
                                                                >
                                                                    ⚠️{" "}
                                                                    <strong>
                                                                        Video
                                                                        hours
                                                                        are
                                                                        consumed
                                                                    </strong>{" "}
                                                                    (no refund)
                                                                </p>
                                                                <p
                                                                    style={{
                                                                        color: "rgba(255,255,255,0.85)",
                                                                        fontSize:
                                                                            "0.85rem",
                                                                        marginTop:
                                                                            "0.5rem",
                                                                        marginBottom: 0,
                                                                        lineHeight:
                                                                            "1.5",
                                                                    }}
                                                                >
                                                                    Head-start
                                                                    viewing uses
                                                                    your quota
                                                                    permanently.
                                                                    Only
                                                                    remediation
                                                                    (failed →
                                                                    self-study →
                                                                    passed)
                                                                    qualifies
                                                                    for hour
                                                                    refunds.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Getting Started Instructions */}
                                            <div className="row mb-4">
                                                <div className="col-12">
                                                    <div
                                                        className="card"
                                                        style={{
                                                            backgroundColor:
                                                                "#34495e",
                                                            border: "none",
                                                            borderRadius:
                                                                "0.5rem",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-body"
                                                            style={{
                                                                padding:
                                                                    "1.5rem",
                                                            }}
                                                        >
                                                            <h6
                                                                style={{
                                                                    color: "white",
                                                                    marginBottom:
                                                                        "1rem",
                                                                    fontWeight:
                                                                        "600",
                                                                }}
                                                            >
                                                                <i
                                                                    className="fas fa-play-circle me-2"
                                                                    style={{
                                                                        color: "#3498db",
                                                                    }}
                                                                ></i>
                                                                How to Get
                                                                Started
                                                            </h6>
                                                            <div className="row">
                                                                <div className="col-md-4 mb-3">
                                                                    <div
                                                                        style={{
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "flex-start",
                                                                        }}
                                                                    >
                                                                        <div
                                                                            style={{
                                                                                width: "30px",
                                                                                height: "30px",
                                                                                borderRadius:
                                                                                    "50%",
                                                                                backgroundColor:
                                                                                    "#3498db",
                                                                                display:
                                                                                    "flex",
                                                                                alignItems:
                                                                                    "center",
                                                                                justifyContent:
                                                                                    "center",
                                                                                marginRight:
                                                                                    "0.75rem",
                                                                                flexShrink: 0,
                                                                            }}
                                                                        >
                                                                            <strong
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "0.875rem",
                                                                                }}
                                                                            >
                                                                                1
                                                                            </strong>
                                                                        </div>
                                                                        <div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "0.9rem",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                Select
                                                                                a
                                                                                Lesson
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                Browse
                                                                                lessons
                                                                                in
                                                                                the
                                                                                sidebar
                                                                                and
                                                                                click
                                                                                the
                                                                                "Start
                                                                                Lesson"
                                                                                button
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-4 mb-3">
                                                                    <div
                                                                        style={{
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "flex-start",
                                                                        }}
                                                                    >
                                                                        <div
                                                                            style={{
                                                                                width: "30px",
                                                                                height: "30px",
                                                                                borderRadius:
                                                                                    "50%",
                                                                                backgroundColor:
                                                                                    "#3498db",
                                                                                display:
                                                                                    "flex",
                                                                                alignItems:
                                                                                    "center",
                                                                                justifyContent:
                                                                                    "center",
                                                                                marginRight:
                                                                                    "0.75rem",
                                                                                flexShrink: 0,
                                                                            }}
                                                                        >
                                                                            <strong
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "0.875rem",
                                                                                }}
                                                                            >
                                                                                2
                                                                            </strong>
                                                                        </div>
                                                                        <div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "0.9rem",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                Review
                                                                                Preview
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                Check
                                                                                lesson
                                                                                details
                                                                                and
                                                                                your
                                                                                remaining
                                                                                video
                                                                                time
                                                                                quota
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-4 mb-3">
                                                                    <div
                                                                        style={{
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "flex-start",
                                                                        }}
                                                                    >
                                                                        <div
                                                                            style={{
                                                                                width: "30px",
                                                                                height: "30px",
                                                                                borderRadius:
                                                                                    "50%",
                                                                                backgroundColor:
                                                                                    "#3498db",
                                                                                display:
                                                                                    "flex",
                                                                                alignItems:
                                                                                    "center",
                                                                                justifyContent:
                                                                                    "center",
                                                                                marginRight:
                                                                                    "0.75rem",
                                                                                flexShrink: 0,
                                                                            }}
                                                                        >
                                                                            <strong
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontSize:
                                                                                        "0.875rem",
                                                                                }}
                                                                            >
                                                                                3
                                                                            </strong>
                                                                        </div>
                                                                        <div>
                                                                            <div
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "0.9rem",
                                                                                    marginBottom:
                                                                                        "0.25rem",
                                                                                }}
                                                                            >
                                                                                Begin
                                                                                Learning
                                                                            </div>
                                                                            <div
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                Click
                                                                                "Begin
                                                                                Lesson"
                                                                                to
                                                                                start
                                                                                your
                                                                                video
                                                                                session
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Video Quota Reminder */}
                                            <div
                                                className="alert"
                                                style={{
                                                    backgroundColor:
                                                        "rgba(241, 196, 15, 0.15)",
                                                    border: "1px solid rgba(241, 196, 15, 0.3)",
                                                    borderRadius: "0.5rem",
                                                    padding: "1rem",
                                                }}
                                            >
                                                <div className="d-flex align-items-start">
                                                    <i
                                                        className="fas fa-clock"
                                                        style={{
                                                            color: "#f1c40f",
                                                            fontSize: "1.25rem",
                                                            marginRight:
                                                                "0.75rem",
                                                            marginTop:
                                                                "0.125rem",
                                                        }}
                                                    ></i>
                                                    <div>
                                                        <div
                                                            style={{
                                                                color: "#f1c40f",
                                                                fontWeight:
                                                                    "600",
                                                                marginBottom:
                                                                    "0.25rem",
                                                            }}
                                                        >
                                                            Your Video Time
                                                            Quota
                                                        </div>
                                                        <div
                                                            style={{
                                                                color: "#ecf0f1",
                                                                fontSize:
                                                                    "0.9rem",
                                                            }}
                                                        >
                                                            You have a total of{" "}
                                                            <strong>
                                                                10 hours
                                                            </strong>{" "}
                                                            of video watch time.
                                                            Monitor your usage
                                                            carefully and
                                                            prioritize
                                                            remediation to earn
                                                            hour refunds. Check
                                                            your remaining time
                                                            in the sidebar
                                                            before starting each
                                                            lesson.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Call to Action */}
                                            <div className="text-center mt-4">
                                                <div
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "1.1rem",
                                                        marginBottom: "0.5rem",
                                                    }}
                                                >
                                                    <i className="fas fa-arrow-left me-2"></i>
                                                    Select a lesson from the
                                                    sidebar to begin your
                                                    self-study session
                                                </div>
                                            </div>

                                            <div className="row">
                                                <div
                                                    className="col-12 mb-4"
                                                    style={{ display: "none" }}
                                                >
                                                    <div
                                                        className="card"
                                                        style={{
                                                            backgroundColor:
                                                                "#2c3e50",
                                                            border: "none",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-header"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                borderBottom:
                                                                    "1px solid rgba(255,255,255,0.1)",
                                                            }}
                                                        >
                                                            <h6
                                                                className="mb-0"
                                                                style={{
                                                                    color: "white",
                                                                }}
                                                            >
                                                                <i className="fas fa-video me-2"></i>
                                                                Video Lessons
                                                            </h6>
                                                        </div>
                                                        <div className="card-body">
                                                            <p
                                                                style={{
                                                                    color: "#95a5a6",
                                                                }}
                                                            >
                                                                Your recorded
                                                                video lessons
                                                                will appear
                                                                here. Select a
                                                                lesson from the
                                                                sidebar to
                                                                begin.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-12 mb-4">
                                                    <div
                                                        className="card"
                                                        style={{
                                                            backgroundColor:
                                                                "#2c3e50",
                                                            border: "none",
                                                        }}
                                                    >
                                                        <div
                                                            className="card-header"
                                                            style={{
                                                                backgroundColor:
                                                                    "#34495e",
                                                                borderBottom:
                                                                    "1px solid rgba(255,255,255,0.1)",
                                                            }}
                                                        >
                                                            <h6
                                                                className="mb-0"
                                                                style={{
                                                                    color: "white",
                                                                }}
                                                            >
                                                                <i className="fas fa-tasks me-2"></i>
                                                                Practice
                                                                Exercises
                                                            </h6>
                                                        </div>
                                                        <div className="card-body">
                                                            <p
                                                                style={{
                                                                    color: "#95a5a6",
                                                                }}
                                                            >
                                                                Complete
                                                                practice
                                                                exercises to
                                                                reinforce your
                                                                learning.
                                                            </p>
                                                            {/* TODO: Add exercises component */}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </>
                                    )}

                                    {/* Lesson Preview Screen - Shows when Start Lesson button is clicked */}
                                    {viewMode === "preview" &&
                                        previewLessonId !== null &&
                                        (() => {
                                            const lesson = lessons.find(
                                                (l) => l.id === previewLessonId
                                            );
                                            if (!lesson) return null;

                                            // Get quota from hook - use mock data as fallback
                                            const quotaTotal =
                                                quota?.total_hours || 10.0;
                                            const quotaUsed =
                                                quota?.used_hours || 0.0;
                                            const quotaRemaining =
                                                quota?.remaining_hours ||
                                                quotaTotal - quotaUsed;
                                            const lessonHours =
                                                (lesson.duration_minutes || 0) /
                                                60;
                                            const quotaAfterLesson =
                                                quotaRemaining - lessonHours;
                                            const quotaPercentage =
                                                (quotaRemaining / quotaTotal) *
                                                100;

                                            // Show loading state if quota is still loading
                                            if (isLoadingQuota) {
                                                return (
                                                    <div className="text-center py-5">
                                                        <div
                                                            className="spinner-border text-light"
                                                            role="status"
                                                        >
                                                            <span className="visually-hidden">
                                                                Loading quota...
                                                            </span>
                                                        </div>
                                                        <p
                                                            className="mt-3"
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        >
                                                            Loading quota
                                                            information...
                                                        </p>
                                                    </div>
                                                );
                                            }

                                            // Color coding for quota
                                            const getQuotaColor = (
                                                percentage: number
                                            ) => {
                                                if (percentage >= 60)
                                                    return "#2ecc71";
                                                if (percentage >= 30)
                                                    return "#f39c12";
                                                return "#e74c3c";
                                            };

                                            return (
                                                <div className="lesson-preview">
                                                    {/* Back Button */}
                                                    <button
                                                        className="btn btn-outline-light mb-4"
                                                        onClick={() => {
                                                            setViewMode("list");
                                                            setPreviewLessonId(
                                                                null
                                                            );
                                                        }}
                                                        style={{
                                                            border: "2px solid rgba(255,255,255,0.3)",
                                                            padding:
                                                                "0.5rem 1.5rem",
                                                        }}
                                                    >
                                                        <i className="fas fa-arrow-left me-2"></i>
                                                        Back to Lesson List
                                                    </button>

                                                    <h4
                                                        className="mb-4"
                                                        style={{
                                                            color: "white",
                                                            fontSize: "1.75rem",
                                                            fontWeight: "600",
                                                        }}
                                                    >
                                                        <i
                                                            className="fas fa-eye me-2"
                                                            style={{
                                                                color: "#3498db",
                                                            }}
                                                        ></i>
                                                        Lesson Preview
                                                    </h4>

                                                    {/* Quota Warning Banner - Show at top if insufficient */}
                                                    {quotaAfterLesson < 0 && (
                                                        <div
                                                            className="alert mb-4"
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(231, 76, 60, 0.15)",
                                                                border: "2px solid #e74c3c",
                                                                borderRadius:
                                                                    "0.5rem",
                                                                padding:
                                                                    "1.25rem",
                                                            }}
                                                        >
                                                            <div className="d-flex align-items-center">
                                                                <i
                                                                    className="fas fa-exclamation-triangle"
                                                                    style={{
                                                                        color: "#e74c3c",
                                                                        fontSize:
                                                                            "2rem",
                                                                        marginRight:
                                                                            "1rem",
                                                                    }}
                                                                ></i>
                                                                <div>
                                                                    <h6
                                                                        style={{
                                                                            color: "#e74c3c",
                                                                            fontWeight:
                                                                                "600",
                                                                            marginBottom:
                                                                                "0.25rem",
                                                                        }}
                                                                    >
                                                                        Insufficient
                                                                        Video
                                                                        Quota
                                                                    </h6>
                                                                    <p
                                                                        style={{
                                                                            color: "#ecf0f1",
                                                                            marginBottom: 0,
                                                                            fontSize:
                                                                                "0.95rem",
                                                                        }}
                                                                    >
                                                                        You need{" "}
                                                                        <strong>
                                                                            {Math.abs(
                                                                                quotaAfterLesson
                                                                            ).toFixed(
                                                                                2
                                                                            )}{" "}
                                                                            more
                                                                            hours
                                                                        </strong>{" "}
                                                                        to
                                                                        complete
                                                                        this
                                                                        lesson.
                                                                        Consider
                                                                        completing
                                                                        remediation
                                                                        lessons
                                                                        to earn
                                                                        quota
                                                                        refunds.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )}

                                                    {/* Main Content Row */}
                                                    <div className="row g-4">
                                                        {/* Left Column - Lesson Details */}
                                                        <div className="col-md-8">
                                                            {/* Lesson Header Card */}
                                                            <div
                                                                className="card mb-4"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#2c3e50",
                                                                    border: "none",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    boxShadow:
                                                                        "0 4px 6px rgba(0,0,0,0.2)",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-header"
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#34495e",
                                                                        borderBottom:
                                                                            "2px solid rgba(52, 152, 219, 0.3)",
                                                                        borderRadius:
                                                                            "0.5rem 0.5rem 0 0",
                                                                        padding:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    <h5
                                                                        className="mb-0"
                                                                        style={{
                                                                            color: "white",
                                                                            fontWeight:
                                                                                "600",
                                                                            fontSize:
                                                                                "1.25rem",
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className="fas fa-book me-2"
                                                                            style={{
                                                                                color: "#3498db",
                                                                            }}
                                                                        ></i>
                                                                        {
                                                                            lesson.title
                                                                        }
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1.5rem",
                                                                    }}
                                                                >
                                                                    {/* Description */}
                                                                    <div className="mb-4">
                                                                        <h6
                                                                            style={{
                                                                                color: "#95a5a6",
                                                                                fontSize:
                                                                                    "0.75rem",
                                                                                fontWeight:
                                                                                    "600",
                                                                                marginBottom:
                                                                                    "0.75rem",
                                                                                letterSpacing:
                                                                                    "0.05em",
                                                                                textTransform:
                                                                                    "uppercase",
                                                                            }}
                                                                        >
                                                                            <i className="fas fa-align-left me-2"></i>
                                                                            Description
                                                                        </h6>
                                                                        <p
                                                                            style={{
                                                                                color: "#ecf0f1",
                                                                                fontSize:
                                                                                    "1rem",
                                                                                lineHeight:
                                                                                    "1.7",
                                                                                marginBottom: 0,
                                                                            }}
                                                                        >
                                                                            {lesson.description ||
                                                                                "This lesson covers important concepts and skills that will help you progress in your coursework."}
                                                                        </p>
                                                                    </div>

                                                                    {/* Lesson Stats Grid */}
                                                                    <div className="row g-3">
                                                                        <div className="col-md-4">
                                                                            <div
                                                                                style={{
                                                                                    backgroundColor:
                                                                                        "#34495e",
                                                                                    padding:
                                                                                        "1rem",
                                                                                    borderRadius:
                                                                                        "0.375rem",
                                                                                    border: "1px solid rgba(255,255,255,0.1)",
                                                                                    textAlign:
                                                                                        "center",
                                                                                }}
                                                                            >
                                                                                <div
                                                                                    style={{
                                                                                        color: "#3498db",
                                                                                        fontSize:
                                                                                            "1.75rem",
                                                                                        marginBottom:
                                                                                            "0.5rem",
                                                                                    }}
                                                                                >
                                                                                    <i className="far fa-clock"></i>
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "white",
                                                                                        fontSize:
                                                                                            "1.25rem",
                                                                                        fontWeight:
                                                                                            "600",
                                                                                        marginBottom:
                                                                                            "0.25rem",
                                                                                    }}
                                                                                >
                                                                                    {
                                                                                        lesson.duration_minutes
                                                                                    }{" "}
                                                                                    min
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "#95a5a6",
                                                                                        fontSize:
                                                                                            "0.8rem",
                                                                                    }}
                                                                                >
                                                                                    Duration
                                                                                    (
                                                                                    {lessonHours.toFixed(
                                                                                        2
                                                                                    )}{" "}
                                                                                    hours)
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div className="col-md-4">
                                                                            <div
                                                                                style={{
                                                                                    backgroundColor:
                                                                                        "#34495e",
                                                                                    padding:
                                                                                        "1rem",
                                                                                    borderRadius:
                                                                                        "0.375rem",
                                                                                    border: "1px solid rgba(255,255,255,0.1)",
                                                                                    textAlign:
                                                                                        "center",
                                                                                }}
                                                                            >
                                                                                <div
                                                                                    style={{
                                                                                        color: lesson.is_completed
                                                                                            ? "#2ecc71"
                                                                                            : "#f39c12",
                                                                                        fontSize:
                                                                                            "1.75rem",
                                                                                        marginBottom:
                                                                                            "0.5rem",
                                                                                    }}
                                                                                >
                                                                                    <i
                                                                                        className={
                                                                                            lesson.is_completed
                                                                                                ? "fas fa-check-circle"
                                                                                                : "fas fa-circle-notch"
                                                                                        }
                                                                                    ></i>
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "white",
                                                                                        fontSize:
                                                                                            "1.25rem",
                                                                                        fontWeight:
                                                                                            "600",
                                                                                        marginBottom:
                                                                                            "0.25rem",
                                                                                    }}
                                                                                >
                                                                                    {lesson.is_completed
                                                                                        ? "Completed"
                                                                                        : "Not Started"}
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "#95a5a6",
                                                                                        fontSize:
                                                                                            "0.8rem",
                                                                                    }}
                                                                                >
                                                                                    Status
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div className="col-md-4">
                                                                            <div
                                                                                style={{
                                                                                    backgroundColor:
                                                                                        "#34495e",
                                                                                    padding:
                                                                                        "1rem",
                                                                                    borderRadius:
                                                                                        "0.375rem",
                                                                                    border: "1px solid rgba(255,255,255,0.1)",
                                                                                    textAlign:
                                                                                        "center",
                                                                                }}
                                                                            >
                                                                                <div
                                                                                    style={{
                                                                                        color: "#9b59b6",
                                                                                        fontSize:
                                                                                            "1.75rem",
                                                                                        marginBottom:
                                                                                            "0.5rem",
                                                                                    }}
                                                                                >
                                                                                    <i className="fas fa-video"></i>
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "white",
                                                                                        fontSize:
                                                                                            "1.25rem",
                                                                                        fontWeight:
                                                                                            "600",
                                                                                        marginBottom:
                                                                                            "0.25rem",
                                                                                    }}
                                                                                >
                                                                                    Video
                                                                                </div>
                                                                                <div
                                                                                    style={{
                                                                                        color: "#95a5a6",
                                                                                        fontSize:
                                                                                            "0.8rem",
                                                                                    }}
                                                                                >
                                                                                    Format
                                                                                    Type
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {/* What You'll Learn */}
                                                            <div
                                                                className="card mb-4"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#2c3e50",
                                                                    border: "none",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    boxShadow:
                                                                        "0 4px 6px rgba(0,0,0,0.2)",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-header"
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#34495e",
                                                                        borderBottom:
                                                                            "1px solid rgba(255,255,255,0.1)",
                                                                        borderRadius:
                                                                            "0.5rem 0.5rem 0 0",
                                                                        padding:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <h6
                                                                        className="mb-0"
                                                                        style={{
                                                                            color: "white",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className="fas fa-graduation-cap me-2"
                                                                            style={{
                                                                                color: "#2ecc71",
                                                                            }}
                                                                        ></i>
                                                                        What
                                                                        You'll
                                                                        Learn
                                                                    </h6>
                                                                </div>
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    <ul
                                                                        style={{
                                                                            color: "#ecf0f1",
                                                                            fontSize:
                                                                                "0.95rem",
                                                                            lineHeight:
                                                                                "1.8",
                                                                            paddingLeft:
                                                                                "1.5rem",
                                                                            marginBottom: 0,
                                                                        }}
                                                                    >
                                                                        <li className="mb-2">
                                                                            <strong>
                                                                                Core
                                                                                Concepts:
                                                                            </strong>{" "}
                                                                            Understand
                                                                            fundamental
                                                                            principles
                                                                            and
                                                                            key
                                                                            terminology
                                                                        </li>
                                                                        <li className="mb-2">
                                                                            <strong>
                                                                                Practical
                                                                                Skills:
                                                                            </strong>{" "}
                                                                            Apply
                                                                            knowledge
                                                                            through
                                                                            real-world
                                                                            examples
                                                                            and
                                                                            scenarios
                                                                        </li>
                                                                        <li className="mb-2">
                                                                            <strong>
                                                                                Best
                                                                                Practices:
                                                                            </strong>{" "}
                                                                            Learn
                                                                            industry-standard
                                                                            approaches
                                                                            and
                                                                            techniques
                                                                        </li>
                                                                        <li className="mb-2">
                                                                            <strong>
                                                                                Assessment
                                                                                Prep:
                                                                            </strong>{" "}
                                                                            Prepare
                                                                            for
                                                                            quizzes
                                                                            and
                                                                            evaluations
                                                                            on
                                                                            this
                                                                            material
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            {/* Prerequisites & Requirements */}
                                                            <div
                                                                className="card"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#2c3e50",
                                                                    border: "none",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    boxShadow:
                                                                        "0 4px 6px rgba(0,0,0,0.2)",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-header"
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#34495e",
                                                                        borderBottom:
                                                                            "1px solid rgba(255,255,255,0.1)",
                                                                        borderRadius:
                                                                            "0.5rem 0.5rem 0 0",
                                                                        padding:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <h6
                                                                        className="mb-0"
                                                                        style={{
                                                                            color: "white",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className="fas fa-list-check me-2"
                                                                            style={{
                                                                                color: "#f39c12",
                                                                            }}
                                                                        ></i>
                                                                        Prerequisites
                                                                        &
                                                                        Requirements
                                                                    </h6>
                                                                </div>
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    <div className="mb-3">
                                                                        <div className="d-flex align-items-start mb-2">
                                                                            <i
                                                                                className="fas fa-check-circle me-2 mt-1"
                                                                                style={{
                                                                                    color: "#2ecc71",
                                                                                    fontSize:
                                                                                        "1rem",
                                                                                }}
                                                                            ></i>
                                                                            <span
                                                                                style={{
                                                                                    color: "#ecf0f1",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                No
                                                                                prior
                                                                                experience
                                                                                required
                                                                                -
                                                                                suitable
                                                                                for
                                                                                all
                                                                                skill
                                                                                levels
                                                                            </span>
                                                                        </div>
                                                                        <div className="d-flex align-items-start mb-2">
                                                                            <i
                                                                                className="fas fa-check-circle me-2 mt-1"
                                                                                style={{
                                                                                    color: "#2ecc71",
                                                                                    fontSize:
                                                                                        "1rem",
                                                                                }}
                                                                            ></i>
                                                                            <span
                                                                                style={{
                                                                                    color: "#ecf0f1",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                Stable
                                                                                internet
                                                                                connection
                                                                                for
                                                                                video
                                                                                streaming
                                                                            </span>
                                                                        </div>
                                                                        <div className="d-flex align-items-start mb-2">
                                                                            <i
                                                                                className="fas fa-check-circle me-2 mt-1"
                                                                                style={{
                                                                                    color: "#2ecc71",
                                                                                    fontSize:
                                                                                        "1rem",
                                                                                }}
                                                                            ></i>
                                                                            <span
                                                                                style={{
                                                                                    color: "#ecf0f1",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                Sufficient
                                                                                video
                                                                                quota
                                                                                (
                                                                                {lessonHours.toFixed(
                                                                                    2
                                                                                )}{" "}
                                                                                hours
                                                                                required)
                                                                            </span>
                                                                        </div>
                                                                        <div className="d-flex align-items-start">
                                                                            <i
                                                                                className="fas fa-check-circle me-2 mt-1"
                                                                                style={{
                                                                                    color: "#2ecc71",
                                                                                    fontSize:
                                                                                        "1rem",
                                                                                }}
                                                                            ></i>
                                                                            <span
                                                                                style={{
                                                                                    color: "#ecf0f1",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                Quiet
                                                                                environment
                                                                                for
                                                                                focused
                                                                                learning
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {/* Right Column - Quota & Actions */}
                                                        <div className="col-md-4">
                                                            {/* Quota Status Card */}
                                                            <div
                                                                className="card mb-4"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    border:
                                                                        "3px solid " +
                                                                        getQuotaColor(
                                                                            quotaPercentage
                                                                        ),
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    boxShadow:
                                                                        "0 4px 6px rgba(0,0,0,0.2)",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-header"
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#2c3e50",
                                                                        borderBottom:
                                                                            "2px solid " +
                                                                            getQuotaColor(
                                                                                quotaPercentage
                                                                            ),
                                                                        borderRadius:
                                                                            "0.5rem 0.5rem 0 0",
                                                                        padding:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <h6
                                                                        className="mb-0"
                                                                        style={{
                                                                            color: "white",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className="fas fa-hourglass-half me-2"
                                                                            style={{
                                                                                color: getQuotaColor(
                                                                                    quotaPercentage
                                                                                ),
                                                                            }}
                                                                        ></i>
                                                                        Video
                                                                        Quota
                                                                    </h6>
                                                                </div>
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    {/* Current Quota */}
                                                                    <div className="mb-3">
                                                                        <div className="d-flex justify-content-between mb-2">
                                                                            <span
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                Current
                                                                                Remaining
                                                                            </span>
                                                                            <span
                                                                                style={{
                                                                                    color: "white",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "1rem",
                                                                                }}
                                                                            >
                                                                                {quotaRemaining.toFixed(
                                                                                    1
                                                                                )}

                                                                                h
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            style={{
                                                                                width: "100%",
                                                                                height: "12px",
                                                                                backgroundColor:
                                                                                    "#1e293b",
                                                                                borderRadius:
                                                                                    "6px",
                                                                                overflow:
                                                                                    "hidden",
                                                                                border: "1px solid rgba(255,255,255,0.2)",
                                                                            }}
                                                                        >
                                                                            <div
                                                                                style={{
                                                                                    width: `${quotaPercentage}%`,
                                                                                    height: "100%",
                                                                                    backgroundColor:
                                                                                        getQuotaColor(
                                                                                            quotaPercentage
                                                                                        ),
                                                                                    transition:
                                                                                        "width 0.3s ease",
                                                                                }}
                                                                            ></div>
                                                                        </div>
                                                                        <div className="text-center mt-2">
                                                                            <small
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.75rem",
                                                                                }}
                                                                            >
                                                                                {quotaRemaining.toFixed(
                                                                                    1
                                                                                )}{" "}
                                                                                of{" "}
                                                                                {quotaTotal.toFixed(
                                                                                    1
                                                                                )}{" "}
                                                                                hours
                                                                                available
                                                                            </small>
                                                                        </div>
                                                                    </div>

                                                                    <hr
                                                                        style={{
                                                                            borderColor:
                                                                                "rgba(255,255,255,0.1)",
                                                                            margin: "1rem 0",
                                                                        }}
                                                                    />

                                                                    {/* Quota Breakdown */}
                                                                    <div className="mb-2">
                                                                        <div className="d-flex justify-content-between mb-2">
                                                                            <span
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                <i
                                                                                    className="fas fa-video me-1"
                                                                                    style={{
                                                                                        color: "#3498db",
                                                                                    }}
                                                                                ></i>
                                                                                This
                                                                                Lesson
                                                                            </span>
                                                                            <span
                                                                                style={{
                                                                                    color: "#3498db",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                {lessonHours.toFixed(
                                                                                    2
                                                                                )}

                                                                                h
                                                                            </span>
                                                                        </div>
                                                                        <div className="d-flex justify-content-between mb-2">
                                                                            <span
                                                                                style={{
                                                                                    color: "#95a5a6",
                                                                                    fontSize:
                                                                                        "0.85rem",
                                                                                }}
                                                                            >
                                                                                <i
                                                                                    className="fas fa-minus-circle me-1"
                                                                                    style={{
                                                                                        color: "#e74c3c",
                                                                                    }}
                                                                                ></i>
                                                                                After
                                                                                Completion
                                                                            </span>
                                                                            <span
                                                                                style={{
                                                                                    color:
                                                                                        quotaAfterLesson >=
                                                                                        0
                                                                                            ? "#2ecc71"
                                                                                            : "#e74c3c",
                                                                                    fontWeight:
                                                                                        "600",
                                                                                    fontSize:
                                                                                        "0.95rem",
                                                                                }}
                                                                            >
                                                                                {quotaAfterLesson.toFixed(
                                                                                    2
                                                                                )}

                                                                                h
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    {/* Quota Status Message */}
                                                                    <div
                                                                        className="mt-3 p-2"
                                                                        style={{
                                                                            backgroundColor:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "rgba(231, 76, 60, 0.15)"
                                                                                    : quotaPercentage <
                                                                                      30
                                                                                    ? "rgba(243, 156, 18, 0.15)"
                                                                                    : "rgba(46, 204, 113, 0.15)",
                                                                            borderRadius:
                                                                                "0.375rem",
                                                                            textAlign:
                                                                                "center",
                                                                            border:
                                                                                "1px solid " +
                                                                                (quotaAfterLesson <
                                                                                0
                                                                                    ? "#e74c3c"
                                                                                    : quotaPercentage <
                                                                                      30
                                                                                    ? "#f39c12"
                                                                                    : "#2ecc71"),
                                                                        }}
                                                                    >
                                                                        <i
                                                                            className={
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "fas fa-exclamation-triangle"
                                                                                    : quotaPercentage <
                                                                                      30
                                                                                    ? "fas fa-exclamation-circle"
                                                                                    : "fas fa-check-circle"
                                                                            }
                                                                            style={{
                                                                                color:
                                                                                    quotaAfterLesson <
                                                                                    0
                                                                                        ? "#e74c3c"
                                                                                        : quotaPercentage <
                                                                                          30
                                                                                        ? "#f39c12"
                                                                                        : "#2ecc71",
                                                                                fontSize:
                                                                                    "1.5rem",
                                                                                display:
                                                                                    "block",
                                                                                marginBottom:
                                                                                    "0.5rem",
                                                                            }}
                                                                        ></i>
                                                                        <div
                                                                            style={{
                                                                                color:
                                                                                    quotaAfterLesson <
                                                                                    0
                                                                                        ? "#e74c3c"
                                                                                        : quotaPercentage <
                                                                                          30
                                                                                        ? "#f39c12"
                                                                                        : "#2ecc71",
                                                                                fontWeight:
                                                                                    "600",
                                                                                fontSize:
                                                                                    "0.85rem",
                                                                            }}
                                                                        >
                                                                            {quotaAfterLesson <
                                                                            0
                                                                                ? "Insufficient Quota"
                                                                                : quotaPercentage <
                                                                                  30
                                                                                ? "Low Quota"
                                                                                : "Quota Available"}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {/* Action Buttons */}
                                                            <div
                                                                className="card"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#2c3e50",
                                                                    border: "none",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                    boxShadow:
                                                                        "0 4px 6px rgba(0,0,0,0.2)",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1.25rem",
                                                                    }}
                                                                >
                                                                    <button
                                                                        className="btn btn-lg w-100 mb-3"
                                                                        disabled={
                                                                            quotaAfterLesson <
                                                                            0
                                                                        }
                                                                        onClick={async () => {
                                                                            console.log(
                                                                                "Begin lesson:",
                                                                                lesson.id,
                                                                                lesson.title
                                                                            );

                                                                            // Convert duration_minutes to seconds
                                                                            const videoDurationSeconds =
                                                                                lesson.duration_minutes *
                                                                                60;

                                                                            // Start session via hook
                                                                            const result =
                                                                                await startSession(
                                                                                    lesson.id,
                                                                                    courseAuthId,
                                                                                    videoDurationSeconds,
                                                                                    lesson.title
                                                                                );

                                                                            if (
                                                                                result.success
                                                                            ) {
                                                                                // Session started - go to player
                                                                                setViewMode(
                                                                                    "player"
                                                                                );
                                                                            } else {
                                                                                // Show error
                                                                                alert(
                                                                                    `Failed to start session: ${result.error}`
                                                                                );
                                                                            }
                                                                        }}
                                                                        style={{
                                                                            backgroundColor:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "#7f8c8d"
                                                                                    : "#2ecc71",
                                                                            color: "white",
                                                                            border: "none",
                                                                            padding:
                                                                                "0.875rem",
                                                                            fontSize:
                                                                                "1.1rem",
                                                                            fontWeight:
                                                                                "600",
                                                                            borderRadius:
                                                                                "0.375rem",
                                                                            cursor:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? "not-allowed"
                                                                                    : "pointer",
                                                                            opacity:
                                                                                quotaAfterLesson <
                                                                                0
                                                                                    ? 0.6
                                                                                    : 1,
                                                                            transition:
                                                                                "all 0.2s",
                                                                        }}
                                                                        onMouseEnter={(
                                                                            e
                                                                        ) => {
                                                                            if (
                                                                                quotaAfterLesson >=
                                                                                0
                                                                            ) {
                                                                                e.currentTarget.style.backgroundColor =
                                                                                    "#27ae60";
                                                                                e.currentTarget.style.transform =
                                                                                    "translateY(-2px)";
                                                                                e.currentTarget.style.boxShadow =
                                                                                    "0 6px 12px rgba(46,204,113,0.4)";
                                                                            }
                                                                        }}
                                                                        onMouseLeave={(
                                                                            e
                                                                        ) => {
                                                                            if (
                                                                                quotaAfterLesson >=
                                                                                0
                                                                            ) {
                                                                                e.currentTarget.style.backgroundColor =
                                                                                    "#2ecc71";
                                                                                e.currentTarget.style.transform =
                                                                                    "translateY(0)";
                                                                                e.currentTarget.style.boxShadow =
                                                                                    "none";
                                                                            }
                                                                        }}
                                                                    >
                                                                        <i className="fas fa-play-circle me-2"></i>
                                                                        Begin
                                                                        Lesson
                                                                    </button>

                                                                    <button
                                                                        className="btn btn-lg w-100"
                                                                        onClick={() => {
                                                                            setViewMode(
                                                                                "list"
                                                                            );
                                                                            setPreviewLessonId(
                                                                                null
                                                                            );
                                                                        }}
                                                                        style={{
                                                                            backgroundColor:
                                                                                "transparent",
                                                                            color: "white",
                                                                            border: "2px solid rgba(255,255,255,0.3)",
                                                                            padding:
                                                                                "0.875rem",
                                                                            fontSize:
                                                                                "1rem",
                                                                            fontWeight:
                                                                                "600",
                                                                            borderRadius:
                                                                                "0.375rem",
                                                                            transition:
                                                                                "all 0.2s",
                                                                        }}
                                                                        onMouseEnter={(
                                                                            e
                                                                        ) => {
                                                                            e.currentTarget.style.backgroundColor =
                                                                                "rgba(255,255,255,0.1)";
                                                                            e.currentTarget.style.borderColor =
                                                                                "white";
                                                                        }}
                                                                        onMouseLeave={(
                                                                            e
                                                                        ) => {
                                                                            e.currentTarget.style.backgroundColor =
                                                                                "transparent";
                                                                            e.currentTarget.style.borderColor =
                                                                                "rgba(255,255,255,0.3)";
                                                                        }}
                                                                    >
                                                                        <i className="fas fa-times me-2"></i>
                                                                        Cancel
                                                                    </button>

                                                                    {quotaAfterLesson <
                                                                        0 && (
                                                                        <div className="mt-3 text-center">
                                                                            <small
                                                                                style={{
                                                                                    color: "#e74c3c",
                                                                                    fontSize:
                                                                                        "0.8rem",
                                                                                    fontStyle:
                                                                                        "italic",
                                                                                }}
                                                                            >
                                                                                <i className="fas fa-info-circle me-1"></i>
                                                                                Complete
                                                                                remediation
                                                                                lessons
                                                                                to
                                                                                earn
                                                                                quota
                                                                                refunds
                                                                            </small>
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            </div>

                                                            {/* Tips Card */}
                                                            <div
                                                                className="card mt-4"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    border: "1px solid rgba(52, 152, 219, 0.3)",
                                                                    borderRadius:
                                                                        "0.5rem",
                                                                }}
                                                            >
                                                                <div
                                                                    className="card-body"
                                                                    style={{
                                                                        padding:
                                                                            "1rem",
                                                                    }}
                                                                >
                                                                    <h6
                                                                        style={{
                                                                            color: "#3498db",
                                                                            fontWeight:
                                                                                "600",
                                                                            fontSize:
                                                                                "0.9rem",
                                                                            marginBottom:
                                                                                "0.75rem",
                                                                        }}
                                                                    >
                                                                        <i className="fas fa-lightbulb me-2"></i>
                                                                        Study
                                                                        Tips
                                                                    </h6>
                                                                    <ul
                                                                        style={{
                                                                            color: "#ecf0f1",
                                                                            fontSize:
                                                                                "0.8rem",
                                                                            lineHeight:
                                                                                "1.6",
                                                                            paddingLeft:
                                                                                "1.25rem",
                                                                            marginBottom: 0,
                                                                        }}
                                                                    >
                                                                        <li className="mb-2">
                                                                            Take
                                                                            notes
                                                                            during
                                                                            the
                                                                            video
                                                                        </li>
                                                                        <li className="mb-2">
                                                                            You
                                                                            can
                                                                            rewind
                                                                            if
                                                                            you
                                                                            miss
                                                                            something
                                                                        </li>
                                                                        <li className="mb-2">
                                                                            Complete
                                                                            in
                                                                            one
                                                                            sitting
                                                                            for
                                                                            best
                                                                            retention
                                                                        </li>
                                                                        <li>
                                                                            Review
                                                                            material
                                                                            before
                                                                            live
                                                                            class
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            );
                                        })()}

                                    {/* Video Player Mode */}
                                    {viewMode === "player" &&
                                        selectedLesson &&
                                        activeSession && (
                                            <div
                                                className="video-player-mode"
                                                style={{ padding: "20px" }}
                                            >
                                                {/* Video Player Header */}
                                                <div className="d-flex justify-content-between align-items-center mb-3">
                                                    <h4
                                                        style={{
                                                            color: "white",
                                                            margin: 0,
                                                        }}
                                                    >
                                                        <i className="fas fa-play-circle me-2"></i>
                                                        {selectedLesson.title}
                                                    </h4>
                                                    <button
                                                        className="btn btn-outline-light btn-sm"
                                                        onClick={() => {
                                                            setViewMode("list");
                                                            setPreviewLessonId(
                                                                null
                                                            );
                                                        }}
                                                    >
                                                        <i className="fas fa-arrow-left me-2"></i>
                                                        Back to Lessons
                                                    </button>
                                                </div>

                                                {/* Session Info Display */}
                                                <div
                                                    className="alert alert-info mb-3"
                                                    style={{
                                                        backgroundColor:
                                                            "rgba(52, 152, 219, 0.1)",
                                                        borderColor: "#3498db",
                                                    }}
                                                >
                                                    <div className="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i className="fas fa-clock me-2"></i>
                                                            <strong>
                                                                Active Session
                                                            </strong>
                                                        </div>
                                                        <div className="text-end">
                                                            <small className="d-block">
                                                                Time Remaining:{" "}
                                                                {
                                                                    activeSession.time_remaining_minutes
                                                                }{" "}
                                                                min
                                                            </small>
                                                            <small className="d-block">
                                                                Pause Time Left:{" "}
                                                                {
                                                                    activeSession.pause_remaining_minutes
                                                                }{" "}
                                                                min
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Secure Video Player */}
                                                <SecureVideoPlayer
                                                    activeSession={
                                                        activeSession
                                                    }
                                                    lesson={selectedLesson}
                                                    videoUrl={`/storage/lessons/${selectedLesson.id}/video.mp4`}
                                                    completionThreshold={
                                                        completionThreshold
                                                    }
                                                    simulationMode={true}
                                                    simulationSpeed={10}
                                                    pauseWarningSeconds={
                                                        pauseWarningSeconds
                                                    }
                                                    pauseAlertSound={
                                                        pauseAlertSound
                                                    }
                                                    onComplete={() => {
                                                        // Handle lesson completion
                                                        completeSession();
                                                        setViewMode("list");
                                                        // Refresh lessons to update status
                                                        window.location.reload();
                                                    }}
                                                    onProgress={(data) => {
                                                        console.log(
                                                            "Progress update:",
                                                            data
                                                        );
                                                    }}
                                                    onError={(error) => {
                                                        alert(error);
                                                    }}
                                                />

                                                {/* Lesson Description */}
                                                {selectedLesson.description && (
                                                    <div
                                                        className="card mt-3"
                                                        style={{
                                                            backgroundColor:
                                                                "#2c3e50",
                                                            border: "none",
                                                        }}
                                                    >
                                                        <div className="card-body">
                                                            <h6
                                                                style={{
                                                                    color: "white",
                                                                }}
                                                            >
                                                                Lesson
                                                                Description
                                                            </h6>
                                                            <p
                                                                style={{
                                                                    color: "#95a5a6",
                                                                }}
                                                            >
                                                                {
                                                                    selectedLesson.description
                                                                }
                                                            </p>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                </div>
                            )
                        }

                        {
                            activeTab === "documentation" && (
                                <div className="documentation-tab">
                                    <h4
                                        className="mb-4"
                                        style={{ color: "white" }}
                                    >
                                        Course Documentation
                                    </h4>

                                    <div
                                        className="card"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            border: "none",
                                        }}
                                    >
                                        <div
                                            className="card-header"
                                            style={{
                                                backgroundColor: "#34495e",
                                                borderBottom:
                                                    "1px solid rgba(255,255,255,0.1)",
                                            }}
                                        >
                                            <h6
                                                className="mb-0"
                                                style={{ color: "white" }}
                                            >
                                                <i className="fas fa-folder me-2"></i>
                                                Available Documents
                                            </h6>
                                        </div>
                                        <div className="card-body">
                                            {getDocumentsForCourse().length >
                                            0 ? (
                                                <div
                                                    className="list-group"
                                                    style={{
                                                        backgroundColor:
                                                            "transparent",
                                                    }}
                                                >
                                                    {getDocumentsForCourse().map(
                                                        (doc, idx) => (
                                                            <a
                                                                key={idx}
                                                                href={doc.url}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="list-group-item d-flex justify-content-between align-items-center"
                                                                style={{
                                                                    backgroundColor:
                                                                        "#34495e",
                                                                    border: "1px solid rgba(255,255,255,0.1)",
                                                                    color: "white",
                                                                    marginBottom:
                                                                        "0.5rem",
                                                                    textDecoration:
                                                                        "none",
                                                                }}
                                                            >
                                                                <div
                                                                    style={{
                                                                        flex: 1,
                                                                    }}
                                                                >
                                                                    <i
                                                                        className="fas fa-file-pdf me-2"
                                                                        style={{
                                                                            color: "#e74c3c",
                                                                        }}
                                                                    ></i>
                                                                    {doc.name}
                                                                </div>
                                                                <button
                                                                    className="btn btn-sm btn-outline-light"
                                                                    onClick={(
                                                                        e
                                                                    ) => {
                                                                        e.preventDefault();
                                                                        window.open(
                                                                            doc.url,
                                                                            "_blank"
                                                                        );
                                                                    }}
                                                                >
                                                                    <i className="fas fa-download"></i>
                                                                </button>
                                                            </a>
                                                        )
                                                    )}
                                                </div>
                                            ) : (
                                                <p style={{ color: "#95a5a6" }}>
                                                    No documents available for
                                                    this course.
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            )
                        }
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MainOffline;
