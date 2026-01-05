import React, { useState, useEffect } from "react";
import { LessonsData, LessonProgressData } from "../types/LaravelProps";
import { useLessonSession } from "../hooks/useLessonSession";
import type { StudentAttendanceSummary } from "../types/props/classroom.props";

type StudentSidebarProps = {
    instructor: { fname: string; lname: string } | null;
    classroomStatus: string;
    lessons?: LessonsData;
    hasLessons?: boolean;
    isOnline?: boolean;
    selectedCourseAuthId?: number | null;
    studentAttendance?: StudentAttendanceSummary | null;
    studentUnit?: any | null; // StudentUnit data with classroom_created_at (classroom entry time)
    activeTab?: "home" | "videos" | "documents"; // Track which tab is active
    onLessonClick?: (lesson: LessonProgressData) => void; // Optional callback for lesson clicks
    selectedLesson?: LessonProgressData | null; // Track which lesson is currently selected/active
    validations?: any; // Verification data (ID card and headshot images)
    activeLesson?: any | null; // Active InstLesson from instructor (live classroom)
};

const StudentSidebar: React.FC<StudentSidebarProps> = ({
    instructor,
    classroomStatus,
    lessons,
    hasLessons = false,
    isOnline = false,
    selectedCourseAuthId = null,
    studentAttendance = null,
    studentUnit = null, // Destructure studentUnit prop
    activeTab = "home", // Default to home tab
    onLessonClick, // Destructure the callback prop
    selectedLesson = null, // Destructure selected lesson prop
    validations = null, // Destructure validations prop
    activeLesson = null, // Destructure active lesson from instructor
}) => {
    // Use shared lesson session hook - syncs with VideoLessonsTab
    const {
        session,
        isActive: hasActiveFSTBSession,
        refreshSession,
    } = useLessonSession();

    // Debug: Log session state for troubleshooting
    useEffect(() => {
        console.log("🔍 SIDEBAR SESSION STATE:", {
            isActive: session?.isActive,
            lessonId: session?.lessonId,
            lessonTitle: session?.lessonTitle,
            fullSession: session,
        });
    }, [session]);

    // Force sidebar to always start expanded (not collapsed)
    // LocalStorage removed to prevent width inconsistencies
    const [isCollapsed, setIsCollapsed] = useState(false);

    const toggleCollapse = () => {
        setIsCollapsed(!isCollapsed);
    };

    // Lesson click handler - uses parent's callback if provided, otherwise defaults to console log
    const handleLessonClick = (lesson: LessonProgressData) => {
        if (!lesson) {
            console.error("❌ handleLessonClick called with undefined lesson");
            return;
        }

        if (onLessonClick) {
            // Use parent's handler if provided (preferred path)
            onLessonClick(lesson);
        } else {
            // Fallback behavior for testing/development
            console.log(
                "⚠️ No onLessonClick handler provided. Lesson:",
                lesson.title || "Unknown",
                "ID:",
                lesson.id
            );
        }
    };

    const filteredLessons = React.useMemo(() => {
        if (!lessons) return {} as LessonsData;

        if (!selectedCourseAuthId) {
            console.log(
                "🔍 StudentSidebar: No selectedCourseAuthId, returning all lessons",
                { lessonsKeys: Object.keys(lessons) }
            );
            return lessons;
        }

        // Lessons are already shaped/filtered by the backend poll response.
        return lessons;
    }, [lessons, hasLessons, selectedCourseAuthId]);

    // Derived flags (no extra polling/hooks here; status comes from parent poll-driven props)
    const isClassActive = classroomStatus === "active";
    const isInstructorPresent = Boolean(instructor);
    const hasCourseDate = classroomStatus !== "offline";
    const isWaitingForInstructor = hasCourseDate && !isClassActive;
    const lastUpdated: string | null = null;

    // For FSTB (offline self-study), activeTab === "videos" means student is in self-study mode
    // StudentUnit gets created ONLY when they start a lesson (actual class activity)
    const isFSTBMode = activeTab === "videos";

    // Refresh session data when tab becomes active or component mounts
    useEffect(() => {
        refreshSession();
    }, [activeTab, refreshSession]);

    // Active session data from hook (replaces localStorage check)
    // hasActiveFSTBSession already destructured from hook above
    const activeFSTBSessionData = session?.isActive
        ? {
              lessonId: session?.lessonId,
              lessonTitle: session?.lessonTitle,
              sessionToken: null, // Not needed for display
              startedAt: session?.startedAt,
          }
        : null;

    console.log("🎓 StudentSidebar: Session state from hook:", {
        isActive: hasActiveFSTBSession,
        lessonTitle: session?.lessonTitle,
    });

    // 🐛 DEBUG: Check localStorage directly vs hook state
    useEffect(() => {
        const rawSession = localStorage.getItem("offlineLessonSession");
        const rawOnboarding = localStorage.getItem(
            "fstb_offline_onboarding_progress"
        );

        console.log("🔍 SESSION DEBUG - StudentSidebar Mount/Update:", {
            timestamp: new Date().toISOString(),
            activeTab,
            "localStorage.offlineLessonSession": rawSession
                ? JSON.parse(rawSession)
                : null,
            "localStorage.onboarding": rawOnboarding
                ? JSON.parse(rawOnboarding)
                : null,
            "hook.session": session,
            "hook.isActive": hasActiveFSTBSession,
            "computed.hasStudentUnit":
                studentUnit && studentUnit.classroom_created_at,
        });
    }, [activeTab, session, hasActiveFSTBSession, studentUnit]);

    // Check if student has attendance (StudentUnit created from lesson start)
    const hasStudentUnit = studentUnit && studentUnit.classroom_created_at;

    // Determine student status label - BASED ON DATA, NOT TAB
    // Priority order:
    // 1. Active offline lesson (localStorage session) → "Lesson Active"
    // 2. Has StudentUnit but no active lesson → "Session Active"
    // 3. In live online session → "In Session"
    // 4. Default → "No Session"
    let studentStatusLabel = "No Session";

    if (hasActiveFSTBSession) {
        studentStatusLabel = "Lesson Active"; // Offline lesson active
    } else if (hasStudentUnit) {
        studentStatusLabel = "Session Active"; // Has attendance, no active lesson
    } else if (studentAttendance?.is_present) {
        studentStatusLabel = "In Session"; // Live online class
    } else if (studentAttendance?.attendance_status === "left") {
        studentStatusLabel = "Left Session"; // Left live class
    }

    const studentStatusBadgeClass = hasActiveFSTBSession
        ? "bg-info" // Lesson Active - blue
        : hasStudentUnit
        ? "bg-secondary" // Session Active - gray
        : studentAttendance?.is_present
        ? "bg-success" // In Session - green
        : studentAttendance?.attendance_status === "left"
        ? "bg-warning" // Left Session - yellow
        : "bg-secondary"; // No Session - gray

    // Status message based on actual state, not tab
    let studentStatusMessage = "No active class session detected.";

    if (hasActiveFSTBSession) {
        studentStatusMessage =
            "You are engaged in offline self-study. Your session time is being tracked.";
    } else if (hasStudentUnit) {
        studentStatusMessage =
            "You have completed attendance. Start a new lesson to continue studying.";
    } else if (studentAttendance?.is_present) {
        studentStatusMessage =
            "You are currently checked in. Stay engaged with the class.";
    } else if (studentAttendance?.attendance_status === "left") {
        studentStatusMessage =
            "You previously left the classroom. Rejoin if your instructor requests it.";
    } else if (isClassActive) {
        studentStatusMessage =
            "Class is live. Join now so your attendance is recorded.";
    } else if (hasCourseDate) {
        studentStatusMessage =
            "Class is scheduled for today. We will alert you when it begins.";
    }

    const arrivalTimeDisplay = studentAttendance?.entry_time ?? null;
    const arrivalRelativeDisplay =
        studentAttendance?.entry_time_relative ?? null;
    const sessionDurationDisplay =
        studentAttendance?.session_duration?.formatted ?? null;

    const resolvedClassroomLive = isClassActive || classroomStatus === "active";
    const resolvedClassroomWaiting =
        isWaitingForInstructor && !resolvedClassroomLive;
    const resolvedClassroomScheduled =
        !resolvedClassroomLive &&
        !resolvedClassroomWaiting &&
        (hasCourseDate ||
            classroomStatus === "active" ||
            classroomStatus === "scheduled");

    const classroomStatusLabel = resolvedClassroomLive
        ? "Live"
        : resolvedClassroomWaiting
        ? "Waiting"
        : resolvedClassroomScheduled
        ? "Scheduled"
        : "Offline";

    const classroomStatusBadgeClass = resolvedClassroomLive
        ? "bg-light-success"
        : resolvedClassroomWaiting
        ? "bg-info"
        : resolvedClassroomScheduled
        ? "bg-primary"
        : "bg-secondary";

    const classroomStatusMessage = resolvedClassroomLive
        ? "Your instructor has started the class."
        : resolvedClassroomWaiting
        ? "Your instructor is preparing to begin."
        : resolvedClassroomScheduled
        ? "This class is scheduled for today."
        : "No class is scheduled right now.";

    const shouldShowJoinReminder =
        resolvedClassroomLive && !studentAttendance?.is_present;

    const formattedLastChecked = React.useMemo(() => {
        if (!lastUpdated) {
            return null;
        }

        try {
            return new Date(lastUpdated).toLocaleTimeString();
        } catch (error) {
            console.warn("Unable to format lastUpdated timestamp", {
                lastUpdated,
                error,
            });
            return lastUpdated;
        }
    }, [lastUpdated]);

    console.log("🎓 StudentSidebar: Rendering status overview", studentUnit);
    console.log("🔐 StudentSidebar: Validations data:", validations);
    return (
        <div
            className="dashboard-side ultra-thin-scrollbar"
            style={{
                width: isCollapsed ? "60px" : "300px",
                minHeight: "100%",
                overflowY: "auto",
                overflowX: "hidden", // Prevent horizontal scroll
                margin: 0,
                padding: 0,
                transition: "width 0.3s ease",
                backgroundColor: "#0f172a", // Very dark slate - much darker than content area
            }}
        >
            {/* Toggle Button */}
            <div
                className="p-2 text-white d-flex justify-content-between align-items-center"
                style={{
                    borderBottom: "1px solid #1e293b", // Slightly lighter border
                }}
            >
                {!isCollapsed && (
                    <h5 className="mb-0">
                        <i className="fas fa-user-check me-2"></i>
                        Session Status
                    </h5>
                )}
                <button
                    type="button"
                    className="btn btn-sm btn-outline-light d-flex align-items-center justify-content-center"
                    onClick={toggleCollapse}
                    style={{ width: "32px", height: "32px" }}
                    title={isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"}
                    aria-label={
                        isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"
                    }
                >
                    <i
                        className={`fas ${
                            isCollapsed ? "fa-chevron-right" : "fa-chevron-left"
                        }`}
                        aria-hidden="true"
                    />
                </button>
            </div>

            {!isCollapsed && (
                <div className="px-3 py-3 text-white bg-light-success bg-opacity-10">
                    <div className="d-flex align-items-center justify-content-between mb-3">
                        <small className="text-muted text-uppercase fw-bold">
                            Attendance:
                        </small>
                        {studentUnit && studentUnit.classroom_created_at && (
                            <span className="text-muted small">
                                Arrived:{" "}
                                {new Date(
                                    studentUnit.classroom_created_at
                                ).toLocaleTimeString()}
                            </span>
                        )}
                    </div>

                    {/* Compact Session Strip - StudentUnit created time & InstUnit (class started) time */}
                    <div className="mb-3">
                        <div
                            className="bg-dark border border-secondary rounded-3 px-2 py-2"
                            style={{ minHeight: "40px" }}
                        >
                            <div className="d-flex flex-column gap-2 small">
                                {/* Top row: Status and arrival time */}
                                <div className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span
                                        className={`badge ${studentStatusBadgeClass}`}
                                        style={{
                                            fontSize: "0.7rem",
                                            color: "#eee",
                                            whiteSpace: "nowrap",
                                        }}
                                    >
                                        {studentStatusLabel}
                                    </span>

                                    {/* For FSTB mode with active session, show lesson start time */}
                                    {isFSTBMode &&
                                    hasActiveFSTBSession &&
                                    activeFSTBSessionData ? (
                                        <span
                                            className="text-muted"
                                            style={{ fontSize: "0.75rem" }}
                                        >
                                            <i className="fas fa-play-circle me-1"></i>
                                            {new Date(
                                                activeFSTBSessionData.startedAt
                                            ).toLocaleTimeString()}
                                        </span>
                                    ) : isFSTBMode ? null : (
                                        arrivalTimeDisplay && (
                                            <span
                                                className="text-muted"
                                                style={{ fontSize: "0.75rem" }}
                                            >
                                                <i className="fas fa-user-clock me-1"></i>
                                                {arrivalTimeDisplay}
                                            </span>
                                        )
                                    )}
                                </div>
                                {/* Bottom row: Instructor and duration */}
                                {(isInstructorPresent ||
                                    sessionDurationDisplay) && (
                                    <div className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        {isInstructorPresent && (
                                            <span
                                                className="text-info"
                                                style={{ fontSize: "0.75rem" }}
                                            >
                                                <i className="fas fa-chalkboard-teacher me-1"></i>
                                                Live
                                            </span>
                                        )}
                                        {sessionDurationDisplay && (
                                            <span
                                                className="text-muted"
                                                style={{ fontSize: "0.75rem" }}
                                            >
                                                <i className="fas fa-stopwatch me-1"></i>
                                                {sessionDurationDisplay}
                                            </span>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Lesson Status Indicator - Shows if student has active StudentLesson */}
                    {activeLesson && (
                        <div className="mt-2">
                            <div
                                className={`border rounded-2 px-2 py-1 ${(() => {
                                    // Check if student has StudentLesson for active lesson
                                    const allLessons = lessons
                                        ? Object.values(lessons).flatMap(
                                              (c) => c.lessons
                                          )
                                        : [];
                                    // Check has_student_lesson from activeLesson polling data (not lesson list)
                                    const hasStudentLesson =
                                        activeLesson?.has_student_lesson ===
                                        true;

                                    return hasStudentLesson
                                        ? "bg-success bg-opacity-10 border-success"
                                        : "bg-warning bg-opacity-10 border-warning";
                                })()}`}
                                style={{ fontSize: "0.7rem" }}
                            >
                                {(() => {
                                    // Check has_student_lesson from activeLesson polling data (not lesson list)
                                    const hasStudentLesson =
                                        activeLesson?.has_student_lesson ===
                                        true;

                                    if (hasStudentLesson) {
                                        return (
                                            <span
                                                className="fw-bold"
                                                style={{ color: "#059669" }}
                                            >
                                                <i className="fas fa-check-circle me-1"></i>
                                                Lesson Active - Credit Tracking
                                            </span>
                                        );
                                    } else {
                                        return (
                                            <span
                                                className="fw-bold"
                                                style={{ color: "#1e293b" }}
                                            >
                                                <i className="fas fa-exclamation-triangle me-1"></i>
                                                Late Arrival - No Credit
                                            </span>
                                        );
                                    }
                                })()}
                            </div>
                        </div>
                    )}

                    {shouldShowJoinReminder && !sessionDurationDisplay && (
                        <small className="text-muted">
                            Need help? Check your onboarding steps in the main
                            panel.
                        </small>
                    )}
                </div>
            )}

            {/* Course Lessons Section - Dynamic Data */}
            {!isCollapsed &&
                hasLessons &&
                Object.keys(filteredLessons).length > 0 && (
                    <div className="border-top border-secondary">
                        <div className="p-0">
                            {/* Dynamic lessons from real course data */}
                            {Object.entries(filteredLessons).map(
                                ([courseAuthId, courseData]) => {
                                    // Backend already filtered lessons by day for D course
                                    // Show ALL lessons that backend provided (already filtered by unit_id)
                                    const allLessons = (courseData?.lessons || []).filter(
                                        (lesson) => lesson && lesson.title
                                    );

                                    // If an active live lesson exists (from classroom poll), show ONLY that lesson.
                                    // Active lesson can come from:
                                    // - `activeLesson.lesson_id` (InstLesson from instructor)
                                    // - `lesson.is_active === true` (flag on lesson objects)
                                    const activeLiveLessonId: number | null =
                                        (activeLesson?.lesson_id
                                            ? Number(activeLesson.lesson_id)
                                            : null) ??
                                        (allLessons.find((l: any) => l?.is_active === true)
                                            ?.id
                                            ? Number(
                                                  allLessons.find(
                                                      (l: any) => l?.is_active === true
                                                  )?.id
                                              )
                                            : null);

                                    const lessonsToShow = activeLiveLessonId
                                        ? allLessons.filter(
                                              (l: any) =>
                                                  Number(l?.id) ===
                                                  Number(activeLiveLessonId)
                                          )
                                        : allLessons;

                                    console.log(
                                        "🎯 StudentSidebar: Rendering lessons for course",
                                        {
                                            courseAuthId,
                                            totalBackendLessons:
                                                courseData.lessons?.length,
                                            filteredLessonsToShow:
                                                lessonsToShow.length,
                                            activeLiveLessonId,
                                            current_day_only:
                                                courseData.current_day_only,
                                            isOnline,
                                            lessonTitles: lessonsToShow.map(
                                                (l) => l.title
                                            ),
                                        }
                                    );

                                    return (
                                        <div key={courseAuthId}>
                                            {/* Course Title Header */}
                                            {Object.keys(filteredLessons)
                                                .length > 1 && (
                                                <div className="bg-dark p-2 border-bottom border-secondary">
                                                    <small className="text-info fw-bold">
                                                        <i className="fas fa-book me-1"></i>
                                                        {
                                                            courseData.course_title
                                                        }
                                                    </small>
                                                </div>
                                            )}

                                            {/* Lessons List */}
                                            {lessonsToShow.map(
                                                (lesson, index) => {
                                                    const isSelected =
                                                        selectedLesson?.id ===
                                                        lesson.id;

                                                    // Debug: Check if this lesson matches active session
                                                    // Check if this is the active FSTB lesson
                                                    const isActiveFSTBLesson =
                                                        session?.isActive &&
                                                        session?.lessonId ===
                                                            lesson.id;

                                                    // Check if this is the active live instructor lesson (from backend is_active flag)
                                                    const isActiveLiveLesson =
                                                        lesson.is_active ===
                                                            true ||
                                                        (activeLesson &&
                                                            activeLesson.lesson_id ===
                                                                lesson.id);

                                                    // DON'T automatically mark first lesson as active - only backend determines this
                                                    const shouldShowAsActive =
                                                        false;

                                                    if (
                                                        isActiveFSTBLesson ||
                                                        isActiveLiveLesson
                                                    ) {
                                                        console.log(
                                                            "🎯 ACTIVE LESSON MATCHED:",
                                                            lesson.title,
                                                            {
                                                                lessonId:
                                                                    lesson.id,
                                                                isActiveFSTBLesson,
                                                                isActiveLiveLesson,
                                                            }
                                                        );
                                                    }

                                                    return (
                                                        <div
                                                            key={lesson.id}
                                                            className={`lesson-item ${
                                                                lesson.is_completed
                                                                    ? ""
                                                                    : ""
                                                            } ${
                                                                index <
                                                                lessonsToShow.length -
                                                                    1
                                                                    ? "border-bottom border-secondary"
                                                                    : ""
                                                            } ${
                                                                isSelected
                                                                    ? "lesson-item-selected"
                                                                    : ""
                                                            }`}
                                                            style={{
                                                                margin: "0",
                                                                borderRadius:
                                                                    "0",
                                                                backgroundColor:
                                                                    isActiveLiveLesson
                                                                        ? "#0ea5e9" // Bright cyan/blue for live instructor lesson (like screenshot)
                                                                        : isActiveFSTBLesson
                                                                        ? "#3b82f6" // Bright blue for active FSTB lesson
                                                                        : shouldShowAsActive
                                                                        ? "#0ea5e9" // Bright cyan/blue for first lesson (active)
                                                                        : lesson.is_completed
                                                                        ? "#059669" // Bright green for completed (like screenshot)
                                                                        : isSelected
                                                                        ? "#2563eb" // Darker blue for selected
                                                                        : "#1e293b", // Dark slate for incomplete
                                                                borderLeft:
                                                                    isActiveLiveLesson ||
                                                                    isActiveFSTBLesson
                                                                        ? "none" // No border for active lessons
                                                                        : isSelected
                                                                        ? "4px solid #60a5fa"
                                                                        : "none",
                                                                transition:
                                                                    "all 0.2s ease",
                                                                cursor: "pointer",
                                                            }}
                                                        >
                                                            <div className="p-3">
                                                                <div className="d-flex justify-content-between align-items-start">
                                                                    <div className="flex-grow-1">
                                                                        <h6 className="mb-1 fw-bold text-white">
                                                                            {
                                                                                lesson.title
                                                                            }
                                                                        </h6>
                                                                        <div className="d-flex flex-wrap gap-2">
                                                                            <small className="text-muted">
                                                                                Credit
                                                                                Minutes:{" "}
                                                                                {
                                                                                    lesson.credit_minutes
                                                                                }
                                                                            </small>
                                                                            {lesson.video_seconds >
                                                                                0 && (
                                                                                <small className="text-muted">
                                                                                    •
                                                                                    Video:{" "}
                                                                                    {Math.round(
                                                                                        lesson.video_seconds /
                                                                                            60
                                                                                    )}
                                                                                    min
                                                                                </small>
                                                                            )}
                                                                            <small className="text-muted">
                                                                                •
                                                                                Unit:{" "}
                                                                                {
                                                                                    lesson.unit_title
                                                                                }
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                    <div className="d-flex align-items-center gap-2">
                                                                        {lesson.is_completed && (
                                                                            <i className="fas fa-check-circle text-success"></i>
                                                                        )}

                                                                        {/* Show status badge on Home/Documents tab */}
                                                                        {activeTab !==
                                                                            "videos" && (
                                                                            <span
                                                                                className={`badge ${
                                                                                    lesson.status ===
                                                                                    "passed"
                                                                                        ? "bg-success"
                                                                                        : lesson.status ===
                                                                                          "failed"
                                                                                        ? "bg-danger"
                                                                                        : lesson.status ===
                                                                                          "in-progress"
                                                                                        ? "bg-info"
                                                                                        : shouldShowAsActive
                                                                                        ? "bg-info"
                                                                                        : "bg-warning text-dark"
                                                                                }`}
                                                                                style={{
                                                                                    minWidth:
                                                                                        "80px",
                                                                                    fontSize:
                                                                                        "0.75rem",
                                                                                }}
                                                                            >
                                                                                {lesson.status ===
                                                                                "passed"
                                                                                    ? "Completed"
                                                                                    : lesson.status ===
                                                                                      "failed"
                                                                                    ? "Failed"
                                                                                    : lesson.status ===
                                                                                      "in-progress"
                                                                                    ? "In Progress"
                                                                                    : shouldShowAsActive
                                                                                    ? "Active"
                                                                                    : lesson.status ===
                                                                                      "credit-available"
                                                                                    ? "Available"
                                                                                    : "Pending"}
                                                                            </span>
                                                                        )}

                                                                        {/* Show Start/Resume/Review button only on Video Lessons tab */}
                                                                        {activeTab ===
                                                                            "videos" && (
                                                                            <button
                                                                                className={`btn btn-sm ms-2 ${
                                                                                    session?.isActive &&
                                                                                    session?.lessonId ===
                                                                                        lesson.id
                                                                                        ? "btn-info" // Active lesson
                                                                                        : lesson.is_completed
                                                                                        ? "btn-outline-success"
                                                                                        : "btn-outline-info"
                                                                                }`}
                                                                                style={{
                                                                                    minWidth:
                                                                                        "70px",
                                                                                    cursor:
                                                                                        session?.isActive &&
                                                                                        session?.lessonId !==
                                                                                            lesson.id
                                                                                            ? "not-allowed"
                                                                                            : "pointer",
                                                                                    opacity:
                                                                                        session?.isActive &&
                                                                                        session?.lessonId !==
                                                                                            lesson.id
                                                                                            ? 0.5
                                                                                            : 1,
                                                                                }}
                                                                                onClick={() =>
                                                                                    handleLessonClick(
                                                                                        lesson
                                                                                    )
                                                                                }
                                                                                disabled={
                                                                                    session?.isActive &&
                                                                                    session?.lessonId !==
                                                                                        lesson.id
                                                                                }
                                                                            >
                                                                                {session?.isActive &&
                                                                                session?.lessonId !==
                                                                                    lesson.id ? (
                                                                                    <>
                                                                                        <i className="fas fa-lock me-1"></i>
                                                                                        Locked
                                                                                    </>
                                                                                ) : session?.isActive &&
                                                                                  session?.lessonId ===
                                                                                      lesson.id ? (
                                                                                    <>
                                                                                        <i className="fas fa-play-circle me-1"></i>
                                                                                        Resume
                                                                                    </>
                                                                                ) : lesson.is_completed ? (
                                                                                    "Review"
                                                                                ) : (
                                                                                    "Start"
                                                                                )}
                                                                            </button>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    );
                                                }
                                            )}
                                        </div>
                                    );
                                }
                            )}
                        </div>
                    </div>
                )}

            {/* Fallback: No Lessons Available */}
            {!isCollapsed && !hasLessons && (
                <div className="border-top border-secondary">
                    <div className="p-4 text-center">
                        <div className="text-muted">
                            <i
                                className="fas fa-book-open mb-2"
                                style={{ fontSize: "2rem" }}
                            ></i>
                            <p className="mb-0">No lessons available</p>
                            <small>
                                Course content will appear here when available
                            </small>
                        </div>
                    </div>
                </div>
            )}

            {/* Collapsed state - show lesson initials from dynamic data */}
            {isCollapsed &&
                hasLessons &&
                Object.keys(filteredLessons).length > 0 && (
                    <div className="d-flex flex-column align-items-center py-2">
                        {/* Dynamic lesson initials */}
                        {Object.entries(filteredLessons).flatMap(
                            ([courseAuthId, courseData]) => {
                                // Backend already filtered lessons by day for D course
                                // Show ALL lessons that backend provided
                                const lessonsToShow = courseData.lessons;

                                return lessonsToShow
                                    .filter((lesson) => lesson && lesson.title)
                                    .map((lesson, index) => {
                                        // First lesson should be active (blue) if not completed
                                        const isFirstLesson = index === 0;
                                        const shouldShowAsActive =
                                            isFirstLesson &&
                                            !lesson.is_completed &&
                                            lesson.status ===
                                                "credit-available";

                                        return (
                                            <div
                                                key={`${courseAuthId}-${lesson.id}`}
                                                className={`lesson-initial ${
                                                    lesson.is_completed
                                                        ? "bg-success"
                                                        : shouldShowAsActive
                                                        ? "bg-info"
                                                        : "bg-secondary"
                                                } text-white text-center ${
                                                    index <
                                                    lessonsToShow.length - 1
                                                        ? "mb-1"
                                                        : ""
                                                }`}
                                                style={{
                                                    width: "32px",
                                                    height: "32px",
                                                    lineHeight: "32px",
                                                    fontSize: "14px",
                                                    fontWeight: "bold",
                                                    cursor: "pointer",
                                                }}
                                                title={`${
                                                    lesson.title || "Unknown"
                                                } (${
                                                    lesson.status === "passed"
                                                        ? "Completed"
                                                        : lesson.status ===
                                                          "failed"
                                                        ? "Failed"
                                                        : lesson.status ===
                                                          "in-progress"
                                                        ? "In Progress"
                                                        : shouldShowAsActive
                                                        ? "Active"
                                                        : lesson.status ===
                                                          "credit-available"
                                                        ? "Available"
                                                        : "Pending"
                                                })`}
                                                onClick={() =>
                                                    handleLessonClick(lesson)
                                                }
                                            >
                                                {(lesson.title || "?")
                                                    .charAt(0)
                                                    .toUpperCase()}
                                            </div>
                                        );
                                    });
                            }
                        )}
                    </div>
                )}

            {/** Collapsed state - no lessons fallback */}
            {isCollapsed && !hasLessons && (
                <div className="d-flex flex-column align-items-center py-3">
                    <div className="text-muted text-center">
                        <i className="fas fa-book-open mb-2"></i>
                        <small className="d-block">No lessons available</small>
                    </div>
                </div>
            )}

            {/** ID Verification moved to ClassroomInfoSidebar (right sidebar) */}
        </div>
    );
};

export default StudentSidebar;
