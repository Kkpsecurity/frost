import React, { useEffect, useState } from "react";
import axios from "axios";
import { Lesson, LessonsPanelProps } from "./LessonsPanelTypes";
import { lessonPanelStyles } from "./LessonsPanelUtils";
import LessonItem from "./LessonItem";
import PauseModal from "./PauseModal";
import ActiveLessonPanel from "./ActiveLessonPanel";
import LessonListPanel from "./LessonListPanel";

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
    const [endDayLoading, setEndDayLoading] = useState(false);

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
        !!lessonState,
    );

    const activeLessonId = instUnitLesson?.lesson_id;
    const isPaused = !!instUnitLesson?.is_paused;
    const lessonStartedAt = instUnitLesson?.started_at;
    const breaksRemaining = lessonState?.breaks?.breaks_remaining;
    const breaksAllowed = lessonState?.breaks?.breaks_allowed;
    const breaksTaken = lessonState?.breaks?.breaks_taken;
    const currentBreakStartedAt = lessonState?.breaks?.current_break_started_at;

    console.log("🔍 PAUSE BUTTON CHECK:", {
        activeLessonId,
        hasActiveLessonId: !!activeLessonId,
        instUnitLesson,
        isPaused,
        isZoomReady,
        breaksRemaining,
        breaksAllowed,
        shouldShowButton: !!activeLessonId,
    });

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
                    `📚 Fetching lessons for courseDate: ${courseDateId}`,
                );
                const response = await axios.get(
                    `/admin/instructors/data/lessons/${courseDateId}`,
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
                    `/admin/instructors/lessons/state/${courseDateId}`,
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
        console.log(`🔍 postLessonAction called:`, {
            path,
            lessonId,
            courseDateId,
            hasCourseDateId: !!courseDateId,
        });

        if (!courseDateId) {
            console.error("❌ courseDateId is missing, cannot make API call");
            return;
        }

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
                    "minutes",
                );
            }

            // Force refresh state quickly
            try {
                const stateRes = await axios.get(
                    `/admin/instructors/lessons/state/${courseDateId}`,
                );
                console.log("🔄 Lesson state refreshed:", stateRes.data);
                console.log(
                    "🔄 inst_lessons from state:",
                    stateRes.data?.data?.inst_lessons,
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
                    `/admin/instructors/lessons/state/${courseDateId}`,
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

    const allLessonsCompleted =
        lessons.length > 0 &&
        lessons.every((lesson) => isLessonCompleted(lesson.id));

    const endDay = async () => {
        if (!instUnit?.id) {
            setActionMessage("Unable to end day: missing class session.");
            return;
        }

        if (!allLessonsCompleted) {
            setActionMessage("Complete all lessons before ending the day.");
            return;
        }

        if (activeLessonId) {
            setActionMessage(
                "A lesson is still marked in-progress. Complete it before ending the day.",
            );
            return;
        }

        const confirmed = window.confirm(
            "Are you sure you want to end the day? This will close the class session.",
        );
        if (!confirmed) return;

        setEndDayLoading(true);
        setActionMessage(null);

        try {
            const res = await axios.post(
                "/admin/instructors/classroom/end-class",
                {
                    inst_unit_id: instUnit.id,
                },
            );

            const success = !!res?.data?.success;
            if (success) {
                window.location.href = "/admin/instructors";
                return;
            }

            setActionMessage(res?.data?.message || "Failed to end day");
        } catch (err: any) {
            const message =
                err?.response?.data?.message ||
                err?.message ||
                "Failed to end day";
            setActionMessage(message);
        } finally {
            setEndDayLoading(false);
        }
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

    return (
        <>
            <aside
                className={`sidebar sidebar-left ${collapsed ? "collapsed" : ""
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
                            className={`fas ${collapsed
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
                                        <ActiveLessonPanel
                                            activeLessonId={activeLessonId}
                                            courseDateId={courseDateId || 0}
                                            isZoomReady={isZoomReady}
                                            isPaused={isPaused}
                                            breaksRemaining={breaksRemaining}
                                            breaksAllowed={breaksAllowed}
                                            breaksTaken={breaksTaken}
                                            postLessonAction={postLessonAction}
                                            actionLoading={actionLoading}
                                        />
                                    )}

                                    <LessonListPanel
                                        lessons={lessons}
                                        isLessonCompleted={isLessonCompleted}
                                        isLessonActive={isLessonActive}
                                        isLessonEnabled={isLessonEnabled}
                                        actionLoading={actionLoading}
                                        isZoomReady={isZoomReady}
                                        postLessonAction={postLessonAction}
                                    />

                                    {actionMessage && (
                                        <div className="alert alert-info m-3">
                                            <i className="fas fa-info-circle me-2" />
                                            {actionMessage}
                                        </div>
                                    )}

                                    {allLessonsCompleted && (
                                        <div className="m-3">
                                            <div className="alert alert-success mb-2">
                                                <i className="fas fa-check-circle mr-2" />
                                                All lessons completed.
                                            </div>
                                            <button
                                                className="btn btn-danger w-100"
                                                disabled={endDayLoading}
                                                onClick={endDay}
                                                title="End the day and close this class session"
                                            >
                                                <i className="fas fa-flag-checkered me-1" />
                                                {endDayLoading ? "Ending day..." : "End Day"}
                                            </button>
                                        </div>
                                    )}
                                </>
                            )}
                        </>
                    )}
                </div>

                {/* Lesson Panel Specific Styles */}
                <style>{lessonPanelStyles}</style>
            </aside>

            {/* Full-Screen Pause Modal */}
            {showPauseModal && (
                <PauseModal
                    isPaused={isPaused}
                    breakTimeRemaining={breakTimeRemaining}
                    breakDurationMinutes={breakDurationMinutes}
                    breaksRemaining={breaksRemaining}
                    actionMessage={actionMessage}
                    pausedLessonId={pausedLessonId}
                    actionLoading={actionLoading}
                    onAction={postLessonAction}
                    onClose={() => {
                        setShowPauseModal(false);
                        setPausedLessonId(null);
                    }}
                />
            )}
        </>
    );
};

export default LessonsPanel;
