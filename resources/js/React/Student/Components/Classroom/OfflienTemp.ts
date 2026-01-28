import React, { useState, useEffect } from "react";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";
import { useVideoQuota } from "../../hooks/useVideoQuota";
import { useLessonSession } from "../../hooks/useLessonSession";
import SessionInfoPanel from "./SessionInfoPanel";
import SecureVideoPlayer from "./SecureVideoPlayer";
import usePhotoUploaded from "../../../Hooks/Web/usePhotoUploaded";
import { LessonType } from "../../types/classroom";
import StudentLessonBar from "./StudentLessonBar";
import LessonListSB from "./LessonListSB";
import OfflineTabSystem from "../OfflineTabSystem";

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
        null,
    );
    const [isLoadingLessons, setIsLoadingLessons] = useState(true);
    const [courseName, setCourseName] = useState<string>("Loading...");
    const [courseAuth, setCourseAuth] = useState<any>(null);

    // View mode: 'list' (default), 'preview' (lesson details), 'player' (video player)
    const [viewMode, setViewMode] = useState<"list" | "preview" | "player">(
        "list",
    );
    const [previewLessonId, setPreviewLessonId] = useState<number | null>(null);

    // Active session data from classroom poll
    const [activeSession, setActiveSession] = useState<any>(null);

    // Settings from API
    const [completionThreshold, setCompletionThreshold] = useState<number>(80);
    const [pauseWarningSeconds, setPauseWarningSeconds] = useState<number>(30);
    const [pauseAlertSound, setPauseAlertSound] = useState<string>(
        "/sounds/pause-warning.mp3",
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
            const validations =
                studentContext.validationsByCourseAuth[courseAuthId];
            console.log(
                "📋 [useEffect] Validations from student context:",
                validations,
            );

            if (validations?.idcard) {
                const idcard = validations.idcard;
                const status = validations.idcard_status || "uploaded";

                console.log("🆔 [useEffect] ID Card URL:", idcard);
                console.log("📊 [useEffect] ID Card Status:", status);

                // ID card is a direct URL string from buildStudentValidationsForCourseAuth
                if (
                    typeof idcard === "string" &&
                    !idcard.includes("no-image")
                ) {
                    setIdCardUrl(idcard);
                    setIdCardStatus(
                        status as
                            | "missing"
                            | "uploaded"
                            | "approved"
                            | "rejected",
                    );
                    console.log(
                        "✅ [useEffect] ID Card loaded from student poll context",
                    );
                } else {
                    console.log("ℹ️ [useEffect] No valid ID Card URL");
                }
            } else {
                console.log("ℹ️ [useEffect] No ID card in validations");
            }
        } else {
            console.log(
                "⚠️ [useEffect] Student context or validationsByCourseAuth not available",
            );
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
                    },
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
                            data.data.settings.completion_threshold,
                        );
                    }

                    // Set pause settings from API
                    if (data.data.settings?.pause_warning_seconds) {
                        setPauseWarningSeconds(
                            data.data.settings.pause_warning_seconds,
                        );
                    }
                    if (data.data.settings?.pause_alert_sound) {
                        setPauseAlertSound(
                            data.data.settings.pause_alert_sound,
                        );
                    }

                    // Set lessons if available
                    if (data.data.lessons) {
                        setLessons(data.data.lessons);
                        // Auto-select first incomplete or first lesson
                        const firstIncomplete = data.data.lessons.find(
                            (l: LessonType) => !l.is_completed,
                        );
                        setSelectedLessonId(
                            firstIncomplete?.id ||
                                data.data.lessons[0]?.id ||
                                null,
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
                activeSession.lesson_id,
            );

            // Check if the lesson exists in our lessons list
            const activeLesson = lessons.find(
                (l) => l.id === activeSession.lesson_id,
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
        if (lesson.status === "completed") {
            return (
                <i
                    className="fas fa-check-circle"
                    style={{ color: "#10b981" }}
                ></i>
            );
        }
        // No 'failed' status in LessonStatus; skip or use another property if needed
            return (
                <i
                    className="fas fa-times-circle"
                    style={{ color: "#ef4444" }}
                ></i>
            );
        }
        if (lesson.status === "incomplete") {
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
                subtitle={`Self-Study Mode | Student: ${student?.name || "N/A"}`}
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
                        <StudentLessonBar lessons={lessons} />
                        {/* Session Info Panel - Shows when session is active */}
                        {/* Real lesson data from API */}
                        <div className="lesson-list mt-4">
                            <LessonListSB
                                lessons={lessons}
                                isLoadingLessons={isLoadingLessons}
                                selectedLessonId={selectedLessonId}
                                handleLessonClick={handleLessonClick}
                                getLessonStatusColor={getLessonStatusColor}
                                getLessonStatusIcon={getLessonStatusIcon}
                                activeTab={activeTab}
                                areLessonsLocked={areLessonsLocked}
                                session={session}
                                hasActiveSession={hasActiveSession}
                                setSelectedLessonId={setSelectedLessonId}
                                setPreviewLessonId={setPreviewLessonId}
                                setViewMode={setViewMode}
                            />
                        </div>
                    </div>
                </div>

                {/* Content Area */}
                <div
                    className="content-area flex-grow-1 d-flex flex-column"
                    style={{ overflow: "hidden" }}
                >
                    {/* Tabs Navigation */}
                    <OfflineTabSystem
                        activeTab={activeTab}
                        setActiveTab={setActiveTab}
                        lessons={lessons}
                    />
                </div>
            </div>
        </div>
    );
}

export default MainOffline;
