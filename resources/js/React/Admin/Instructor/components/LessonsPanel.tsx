import React, { useEffect, useState } from "react";
import axios from "axios";

interface Lesson {
  id: number;
  title: string;
  sort_order: number;
  lesson_type: string;
  is_completed: boolean;
  duration_minutes: number;
  description: string;
  content_url: string | null;
  objectives: string | null;
}

interface InstLesson {
  id: number;
  lesson_id: number;
  created_at: string;
  completed_at: string | null;
  is_paused: boolean;
}

interface LessonsPanelProps {
  courseDateId?: number;
  collapsed: boolean;
  onToggle: () => void;
  instUnit?: any; // Contains instLessons
  zoomReady?: boolean;
}

/**
 * LessonsPanel - Left sidebar showing today's lessons
 *
 * Rules:
 * 1. All lessons DISABLED until zoom_setup complete (zoom_started_at set)
 * 2. After zoom setup, only FIRST incomplete lesson is enabled
 * 3. Lessons enable progressively as previous lessons complete
 * 4. Prevents starting lessons out of order
 */
const LessonsPanel: React.FC<LessonsPanelProps> = ({
  courseDateId,
  collapsed,
  onToggle,
  instUnit,
  zoomReady,
}) => {
    const [lessons, setLessons] = useState<Lesson[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [actionLoading, setActionLoading] = useState(false);
    const [lessonState, setLessonState] = useState<any>(null);
    const [actionMessage, setActionMessage] = useState<string | null>(null);
    const [showPauseModal, setShowPauseModal] = useState(false);
    const [pausedLessonId, setPausedLessonId] = useState<number | null>(null);
    const [breakStartedAt, setBreakStartedAt] = useState<string | null>(null);
    const [breakDurationMinutes, setBreakDurationMinutes] =
        useState<number>(15);
    const [breakTimeRemaining, setBreakTimeRemaining] = useState<number>(0);

    // Check if Zoom is setup (zoom_creds.zoom_status === enabled)
    const isZoomReady = !!zoomReady;

    // Get current active lesson from instUnitLesson (instructor polling data)
    // This is the CURRENT in-progress lesson (not completed)
    const instUnitLesson =
        lessonState?.instUnitLesson || instUnit?.instUnitLesson || null;
    const completedLessons =
        lessonState?.completedInstLessons ||
        instUnit?.completedInstLessons ||
        [];

    console.log(
        "📊 RENDER - instUnitLesson:",
        instUnitLesson,
        "completedLessons:",
        completedLessons.length,
        "lessonState:",
        !!lessonState
    );

    const activeLessonId = instUnitLesson?.lesson_id;
    const isPaused = !!instUnitLesson?.is_paused;
    const lessonStartedAt = instUnitLesson?.started_at;
    const breaksRemaining = lessonState?.breaks?.breaks_remaining;
    const breaksAllowed = lessonState?.breaks?.breaks_allowed;
    const breaksTaken = lessonState?.breaks?.breaks_taken;
    const currentBreakStartedAt = lessonState?.breaks?.current_break_started_at;

    // Countdown timer for break duration
    useEffect(() => {
        if (!isPaused || !currentBreakStartedAt) {
            setBreakTimeRemaining(0);
            return;
        }

        const updateTimer = () => {
            const startTime = new Date(currentBreakStartedAt).getTime();
            const now = Date.now();
            const elapsed = Math.floor((now - startTime) / 1000); // seconds
            const total = breakDurationMinutes * 60; // seconds
            const remaining = Math.max(0, total - elapsed);
            setBreakTimeRemaining(remaining);
        };

        updateTimer(); // Update immediately
        const interval = setInterval(updateTimer, 1000); // Update every second

        return () => clearInterval(interval);
    }, [isPaused, currentBreakStartedAt, breakDurationMinutes]);

    // Auto-open modal when lesson becomes paused
    useEffect(() => {
        if (isPaused && activeLessonId) {
            setShowPauseModal(true);
            setPausedLessonId(activeLessonId);
            if (currentBreakStartedAt) {
                setBreakStartedAt(currentBreakStartedAt);
            }
        } else if (!isPaused) {
            // Auto-close modal when lesson is no longer paused
            setShowPauseModal(false);
            setPausedLessonId(null);
            setBreakStartedAt(null);
        }
    }, [isPaused, activeLessonId, currentBreakStartedAt]);

    useEffect(() => {
        if (!courseDateId) {
            setLessons([]);
            return;
        }

        const fetchLessons = async () => {
            setLoading(true);
            setError(null);

            try {
                console.log(
                    `📚 Fetching lessons for courseDate: ${courseDateId}`
                );
                const response = await axios.get(
                    `/admin/instructors/data/lessons/${courseDateId}`
                );

                console.log("✅ Lessons received:", response.data);
                setLessons(response.data.lessons || []);
            } catch (err: any) {
                console.error("❌ Error fetching lessons:", err);
                setError(err.message || "Failed to load lessons");
            } finally {
                setLoading(false);
            }
        };

        fetchLessons();
    }, [courseDateId]);

    useEffect(() => {
        if (!courseDateId) {
            setLessonState(null);
            return;
        }

        const fetchLessonState = async () => {
            try {
                const response = await axios.get(
                    `/admin/instructors/lessons/state/${courseDateId}`
                );
                setLessonState(response.data);
            } catch (err) {
                // non-fatal - UI still works with polling data
            }
        };

        fetchLessonState();
        const interval = window.setInterval(fetchLessonState, 5000);
        return () => window.clearInterval(interval);
    }, [courseDateId]);

    const postLessonAction = async (path: string, lessonId: number) => {
        if (!courseDateId) return;
        setActionLoading(true);
        setActionMessage(null);

        console.log(`🎯 Posting lesson action:`, {
            path,
            lessonId,
            courseDateId,
        });

        try {
            const res = await axios.post(path, {
                course_date_id: courseDateId,
                lesson_id: lessonId,
            });

            console.log(`✅ Lesson action success:`, res.data);
            setActionMessage(res?.data?.message || "Success");

            // Capture break duration from pause response
            if (
                path.includes("/pause") &&
                res.data?.data?.break_duration_minutes
            ) {
                setBreakDurationMinutes(res.data.data.break_duration_minutes);
                console.log(
                    "⏱️ Break duration set to:",
                    res.data.data.break_duration_minutes,
                    "minutes"
                );
            }

            // Force refresh state quickly
            try {
                const stateRes = await axios.get(
                    `/admin/instructors/lessons/state/${courseDateId}`
                );
                console.log("🔄 Lesson state refreshed:", stateRes.data);
                console.log(
                    "🔄 inst_lessons from state:",
                    stateRes.data?.data?.inst_lessons
                );
                setLessonState(stateRes.data);
            } catch (e) {
                console.warn("Failed to refresh lesson state:", e);
            }
        } catch (err: any) {
            console.error(`❌ Lesson action failed:`, err);

            // Handle CSRF token mismatch
            if (err?.response?.status === 419) {
                const message = "Session expired. Please refresh the page.";
                setActionMessage(message);
                alert(message); // Show alert for critical auth errors
                return;
            }

            const message =
                err?.response?.data?.message || err?.message || "Action failed";
            setActionMessage(message);

            // Refresh state even on error (e.g., "lesson already started" means it exists!)
            try {
                const stateRes = await axios.get(
                    `/admin/instructors/lessons/state/${courseDateId}`
                );
                console.log("🔄 State refreshed after error:", stateRes.data);
                setLessonState(stateRes.data);
            } catch (e) {
                console.warn("Failed to refresh lesson state after error:", e);
            }
        } finally {
            setActionLoading(false);
        }
    };

    /**
     * Check if a lesson is completed by looking at completedInstLessons
     */
    const isLessonCompleted = (lessonId: number): boolean => {
        return completedLessons.some((cl: any) => cl.lesson_id === lessonId);
    };

    /**
     * Check if a lesson is currently active (started but not completed)
     */
    const isLessonActive = (lessonId: number): boolean => {
        return instUnitLesson?.lesson_id === lessonId;
    };

    /**
     * Determine if a lesson button should be enabled
     * Rules:
     * 1. If Zoom not ready, ALL disabled
     * 2. If lesson already started/completed, disabled
     * 3. Only first incomplete lesson is enabled
     * 4. All subsequent lessons disabled until previous completes
     */
    const isLessonEnabled = (lesson: Lesson, index: number): boolean => {
        // Rule 1: Zoom must be setup first
        if (!isZoomReady) {
            return false;
        }

        // Check if this lesson is already completed or active
        const completed = isLessonCompleted(lesson.id);
        const active = isLessonActive(lesson.id);

        // If already active or completed, disable button
        if (active || completed) {
            return false;
        }

        // Find the first incomplete lesson
        for (let i = 0; i < lessons.length; i++) {
            const currentLesson = lessons[i];
            if (!isLessonCompleted(currentLesson.id)) {
                // This is the first incomplete lesson - enable only if it's THIS lesson
                return currentLesson.id === lesson.id;
            }
        }

        return false;
    };

    const getLessonIcon = (lessonType: string) => {
        switch (lessonType) {
            case "video":
                return "fa-video";
            case "reading":
                return "fa-book";
            case "quiz":
                return "fa-clipboard-question";
            case "assignment":
                return "fa-file-pen";
            default:
                return "fa-book-open";
        }
    };

    const formatDuration = (minutes: number) => {
        if (minutes < 60) {
            return `${minutes}m`;
        }
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
    };

    return (
        <>
            <aside
                className={`sidebar sidebar-left ${
                    collapsed ? "collapsed" : ""
                }`}
            >
                <div className="sidebar-header">
                    <div className="sidebar-title">
                        {!collapsed && (
                            <>
                                <i className="fas fa-book" />
                                <span>Today's Lessons</span>
                            </>
                        )}
                        {collapsed && <i className="fas fa-book" />}
                    </div>
                    <button
                        className="btn-collapse"
                        onClick={onToggle}
                        title={collapsed ? "Expand" : "Collapse"}
                    >
                        <i
                            className={`fas ${
                                collapsed
                                    ? "fa-chevron-right"
                                    : "fa-chevron-left"
                            }`}
                        />
                    </button>
                </div>

                <div className="sidebar-content">
                    {collapsed && (
                        <div className="collapsed-icons">
                            <i className="fas fa-book-open" title="Lessons" />
                            {lessons.length > 0 && (
                                <div className="lesson-count-badge">
                                    {lessons.length}
                                </div>
                            )}
                        </div>
                    )}

                    {!collapsed && (
                        <>
                            {loading && (
                                <div className="text-center p-4">
                                    <i className="fas fa-spinner fa-spin fa-2x text-white-50 mb-2" />
                                    <p className="text-white-50 small mb-0">
                                        Loading lessons...
                                    </p>
                                </div>
                            )}

                            {error && !loading && (
                                <div className="alert alert-danger m-3">
                                    <i className="fas fa-exclamation-triangle mr-2" />
                                    {error}
                                </div>
                            )}

                            {!loading && !error && lessons.length === 0 && (
                                <div className="placeholder-content">
                                    <i className="fas fa-inbox fa-3x mb-3 text-muted" />
                                    <p className="text-muted small">
                                        No lessons scheduled for today
                                    </p>
                                </div>
                            )}

                            {!loading && !error && lessons.length > 0 && (
                                <>
                                    {!isZoomReady && (
                                        <div className="alert alert-warning m-3">
                                            <i className="fas fa-exclamation-triangle mr-2" />
                                            <strong>Zoom Setup Required</strong>
                                            <p className="mb-0 small">
                                                Complete Zoom setup before
                                                starting lessons
                                            </p>
                                        </div>
                                    )}

                                    {activeLessonId && (
                                        <div className="m-3">
                                            <button
                                                className="btn btn-sm btn-warning w-100"
                                                title="Pause class (take a break)"
                                                disabled={
                                                    !isZoomReady ||
                                                    actionLoading ||
                                                    isPaused ||
                                                    (breaksRemaining !==
                                                        undefined &&
                                                        breaksRemaining <= 0)
                                                }
                                                onClick={() => {
                                                    setPausedLessonId(
                                                        activeLessonId
                                                    );
                                                    setShowPauseModal(true);
                                                }}
                                            >
                                                <i className="fas fa-pause me-1" />
                                                Pause Class
                                                {breaksRemaining !==
                                                    undefined &&
                                                    breaksRemaining > 0 && (
                                                        <span className="ms-1">
                                                            ({breaksRemaining}{" "}
                                                            left)
                                                        </span>
                                                    )}
                                            </button>
                                            {typeof breaksAllowed ===
                                                "number" && (
                                                <div className="mt-1">
                                                    <small className="text-white-50">
                                                        Breaks:{" "}
                                                        {breaksTaken ?? 0}/
                                                        {breaksAllowed}
                                                        {typeof breaksRemaining ===
                                                        "number"
                                                            ? ` (${breaksRemaining} remaining)`
                                                            : ""}
                                                    </small>
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    <div className="lessons-list">
                                        {lessons.map((lesson, index) => {
                                            const completed = isLessonCompleted(
                                                lesson.id
                                            );
                                            const active = isLessonActive(
                                                lesson.id
                                            );
                                            const enabled = isLessonEnabled(
                                                lesson,
                                                index
                                            );

                                            return (
                                                <div
                                                    key={lesson.id}
                                                    className={`lesson-item ${
                                                        completed
                                                            ? "completed"
                                                            : ""
                                                    } ${
                                                        active ? "active" : ""
                                                    }`}
                                                >
                                                    <div className="lesson-number">
                                                        {index + 1}
                                                    </div>
                                                    <div className="lesson-content">
                                                        <div className="lesson-header">
                                                            <i
                                                                className={`fas ${getLessonIcon(
                                                                    lesson.lesson_type
                                                                )} mr-2`}
                                                            />
                                                            <h6 className="lesson-title mb-0">
                                                                {lesson.title}
                                                            </h6>
                                                        </div>
                                                        <div className="lesson-meta">
                                                            <span className="lesson-duration">
                                                                <i className="far fa-clock me-1" />
                                                                {formatDuration(
                                                                    lesson.duration_minutes
                                                                )}
                                                            </span>
                                                            {completed && (
                                                                <span className="lesson-status text-success">
                                                                    <i className="fas fa-check-circle me-1" />
                                                                    Completed
                                                                </span>
                                                            )}
                                                            {active &&
                                                                !completed && (
                                                                    <span className="lesson-status text-primary">
                                                                        <i className="fas fa-play-circle me-1" />
                                                                        In
                                                                        Progress
                                                                    </span>
                                                                )}
                                                            {!active &&
                                                                !completed &&
                                                                !enabled && (
                                                                    <span className="lesson-status text-muted">
                                                                        <i className="fas fa-lock me-1" />
                                                                        Locked
                                                                    </span>
                                                                )}
                                                        </div>
                                                        {!completed &&
                                                            !active && (
                                                                <button
                                                                    className="btn btn-sm btn-primary btn-start-lesson mt-2"
                                                                    disabled={
                                                                        !enabled
                                                                    }
                                                                    title={
                                                                        !isZoomReady
                                                                            ? "Setup Zoom first"
                                                                            : !enabled
                                                                            ? "Complete previous lesson first"
                                                                            : "Start this lesson"
                                                                    }
                                                                    onClick={() =>
                                                                        postLessonAction(
                                                                            "/admin/instructors/lessons/start",
                                                                            lesson.id
                                                                        )
                                                                    }
                                                                >
                                                                    <i className="fas fa-play me-1" />
                                                                    Start Lesson
                                                                </button>
                                                            )}
                                                        {active &&
                                                            !completed && (
                                                                <div className="w-100 mt-2">
                                                                    <button
                                                                        className="btn btn-sm btn-success w-100"
                                                                        title="Mark lesson as complete"
                                                                        disabled={
                                                                            actionLoading
                                                                        }
                                                                        onClick={() =>
                                                                            postLessonAction(
                                                                                "/admin/instructors/lessons/complete",
                                                                                lesson.id
                                                                            )
                                                                        }
                                                                    >
                                                                        <i className="fas fa-check me-1" />
                                                                        Complete
                                                                    </button>
                                                                </div>
                                                            )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {actionMessage && (
                                        <div className="alert alert-info m-3">
                                            <i className="fas fa-info-circle mr-2" />
                                            {actionMessage}
                                        </div>
                                    )}
                                </>
                            )}
                        </>
                    )}
                </div>

                {/* Lesson Panel Specific Styles */}
                <style>{`
        .lessons-list {
          padding: 0;
        }

        .lesson-item {
          display: flex;
          gap: 12px;
          padding: 15px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
          transition: background 0.2s;
        }

        .lesson-item:hover {
          background: rgba(255, 255, 255, 0.05);
        }

        .lesson-item.completed {
          opacity: 0.7;
        }

        .lesson-item.active {
          background: rgba(0, 123, 255, 0.1);
          border-left: 3px solid #007bff;
        }

        .lesson-item.active .lesson-number {
          background: #007bff;
          color: white;
        }

        .lesson-number {
          flex-shrink: 0;
          width: 32px;
          height: 32px;
          display: flex;
          align-items: center;
          justify-content: center;
          background: rgba(255, 255, 255, 0.1);
          border-radius: 50%;
          font-weight: 600;
          font-size: 14px;
          color: white;
        }

        .lesson-content {
          flex: 1;
          min-width: 0;
        }

        .lesson-header {
          display: flex;
          align-items: center;
          margin-bottom: 6px;
        }

        .lesson-title {
          font-size: 14px;
          font-weight: 500;
          color: white;
          line-height: 1.4;
        }

        .lesson-meta {
          display: flex;
          align-items: center;
          gap: 12px;
          font-size: 12px;
          color: rgba(255, 255, 255, 0.7);
          margin-bottom: 8px;
        }

        .lesson-duration {
          display: flex;
          align-items: center;
        }

        .lesson-status {
          display: flex;
          align-items: center;
        }

        .btn-start-lesson {
          width: 100%;
          font-size: 12px;
          padding: 6px 12px;
        }

        .lesson-count-badge {
          position: absolute;
          top: 8px;
          right: 8px;
          background: #007bff;
          color: white;
          border-radius: 50%;
          width: 24px;
          height: 24px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
          font-weight: 600;
        }
      `}</style>
            </aside>

            {/* Full-Screen Pause Modal - Red Backdrop */}
            {showPauseModal && (
                <div
                    style={{
                        position: "fixed",
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        backgroundColor: "rgba(220, 38, 38, 0.95)", // Red backdrop
                        zIndex: 9999,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        backdropFilter: "blur(4px)",
                    }}
                >
                    <div
                        style={{
                            backgroundColor: "#1f2937",
                            borderRadius: "12px",
                            padding: "40px",
                            maxWidth: "500px",
                            width: "90%",
                            boxShadow: "0 25px 50px -12px rgba(0, 0, 0, 0.5)",
                            border: "2px solid rgba(220, 38, 38, 0.5)",
                        }}
                    >
                        <div
                            style={{
                                textAlign: "center",
                                marginBottom: "30px",
                            }}
                        >
                            <i
                                className="fas fa-pause-circle"
                                style={{
                                    fontSize: "64px",
                                    color: "#dc2626",
                                    marginBottom: "20px",
                                }}
                            />
                            <h2
                                style={{ color: "white", marginBottom: "10px" }}
                            >
                                Lesson Paused
                            </h2>
                            <p
                                style={{
                                    color: "rgba(255, 255, 255, 0.7)",
                                    fontSize: "16px",
                                }}
                            >
                                Take a break. Students will see the lesson is
                                paused.
                            </p>
                            {isPaused && breakTimeRemaining > 0 && (
                                <div
                                    style={{
                                        backgroundColor:
                                            "rgba(59, 130, 246, 0.2)",
                                        border: "2px solid rgba(59, 130, 246, 0.5)",
                                        borderRadius: "12px",
                                        padding: "20px",
                                        marginTop: "20px",
                                        marginBottom: "10px",
                                    }}
                                >
                                    <div
                                        style={{
                                            fontSize: "14px",
                                            color: "#93c5fd",
                                            marginBottom: "8px",
                                        }}
                                    >
                                        Break Time Remaining
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "48px",
                                            fontWeight: "bold",
                                            color:
                                                breakTimeRemaining <= 60
                                                    ? "#fbbf24"
                                                    : "white",
                                            fontFamily: "monospace",
                                        }}
                                    >
                                        {Math.floor(breakTimeRemaining / 60)}:
                                        {String(
                                            breakTimeRemaining % 60
                                        ).padStart(2, "0")}
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "12px",
                                            color: "#93c5fd",
                                            marginTop: "8px",
                                        }}
                                    >
                                        Break Duration: {breakDurationMinutes}{" "}
                                        minutes
                                    </div>
                                </div>
                            )}
                            {breaksRemaining !== undefined && (
                                <p
                                    style={{
                                        color: "#fbbf24",
                                        fontSize: "14px",
                                        marginTop: "10px",
                                    }}
                                >
                                    <i className="fas fa-info-circle me-2" />
                                    {breaksRemaining} break
                                    {breaksRemaining !== 1 ? "s" : ""} remaining
                                </p>
                            )}
                            {actionMessage && (
                                <div
                                    style={{
                                        backgroundColor:
                                            "rgba(220, 38, 38, 0.2)",
                                        border: "1px solid rgba(220, 38, 38, 0.5)",
                                        borderRadius: "8px",
                                        padding: "12px",
                                        marginTop: "15px",
                                        color: "#fca5a5",
                                    }}
                                >
                                    <i className="fas fa-exclamation-triangle me-2" />
                                    {actionMessage}
                                </div>
                            )}
                        </div>

                        <div style={{ display: "flex", gap: "12px" }}>
                            {!isPaused && (
                                <>
                                    <button
                                        className="btn btn-success btn-lg"
                                        style={{
                                            flex: 1,
                                            fontSize: "18px",
                                            padding: "12px",
                                        }}
                                        disabled={actionLoading}
                                        onClick={async () => {
                                            if (pausedLessonId) {
                                                await postLessonAction(
                                                    "/admin/instructors/lessons/pause",
                                                    pausedLessonId
                                                );
                                                // Don't close modal - stays open while paused
                                            }
                                        }}
                                    >
                                        <i className="fas fa-pause me-2" />
                                        {actionLoading
                                            ? "Pausing..."
                                            : "Start Break"}
                                    </button>
                                    <button
                                        className="btn btn-secondary btn-lg"
                                        style={{
                                            flex: 1,
                                            fontSize: "18px",
                                            padding: "12px",
                                        }}
                                        disabled={actionLoading}
                                        onClick={() => {
                                            setShowPauseModal(false);
                                            setPausedLessonId(null);
                                        }}
                                    >
                                        <i className="fas fa-times me-2" />
                                        Cancel
                                    </button>
                                </>
                            )}
                            {isPaused && (
                                <button
                                    className="btn btn-success btn-lg"
                                    style={{
                                        width: "100%",
                                        fontSize: "18px",
                                        padding: "12px",
                                    }}
                                    disabled={actionLoading}
                                    onClick={async () => {
                                        if (pausedLessonId) {
                                            await postLessonAction(
                                                "/admin/instructors/lessons/resume",
                                                pausedLessonId
                                            );
                                            // Close modal after resuming
                                            setShowPauseModal(false);
                                            setPausedLessonId(null);
                                        }
                                    }}
                                >
                                    <i className="fas fa-play me-2" />
                                    {actionLoading
                                        ? "Resuming..."
                                        : "Resume Lesson"}
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default LessonsPanel;
