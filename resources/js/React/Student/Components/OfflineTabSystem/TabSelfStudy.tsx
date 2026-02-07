import React from "react";
import SecureVideoPlayer from "../Classroom/SecureVideoPlayer";
import { useVideoQuota } from "../../hooks/useVideoQuota";

type SelfStudyLesson = {
    id: number;
    lesson_id?: number;
    title: string;
    description?: string;
    duration_minutes: number;
    video_seconds?: number;
    effective_video_seconds?: number;
    video_minutes?: number;
    buffer_minutes?: number;
    pause_minutes?: number;
    required_minutes?: number;
    is_completed?: boolean;
};

type ActiveSession = {
    sessionId: string;
    lessonId: number;
    courseAuthId: number;
    startedAt?: string;
    expiresAt: string;
    completionPercentage: number;
    playbackProgressSeconds: number;
    totalPauseAllowed: number;
    pauseUsed: number;
    videoDurationSeconds: number;
};

interface TabSelfStudyProps {
    courseAuthId: number;
    lessons: SelfStudyLesson[];
    selectedLessonId: number | null;
    onSelectLesson: (lessonId: number) => void;
    onLessonsUpdated?: (lessons: SelfStudyLesson[]) => void;
}

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

const mutedText: React.CSSProperties = { color: "#95a5a6" };

const STORAGE_KEY = "offline_self_study_session";

const TabSelfStudy: React.FC<TabSelfStudyProps> = ({
    courseAuthId,
    lessons,
    selectedLessonId,
    onSelectLesson,
    onLessonsUpdated,
}) => {
    const {
        quota,
        isLoading: isLoadingQuota,
        error: quotaError,
    } = useVideoQuota();

    const [isStarting, setIsStarting] = React.useState(false);
    const [error, setError] = React.useState<string | null>(null);
    const [activeSession, setActiveSession] =
        React.useState<ActiveSession | null>(null);
    const [isCompleting, setIsCompleting] = React.useState(false);
    const [isPlayerUnlocked, setIsPlayerUnlocked] = React.useState(false);
    const [nowTick, setNowTick] = React.useState<number>(() => Date.now());

    const selectedLesson = React.useMemo(() => {
        if (!selectedLessonId) return null;
        return (
            lessons.find((l) => Number(l.id) === Number(selectedLessonId)) ??
            null
        );
    }, [lessons, selectedLessonId]);

    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    const refreshLessons = React.useCallback(async () => {
        try {
            const response = await fetch(
                `/classroom/self-study/lessons?course_auth_id=${courseAuthId}`,
                {
                    method: "GET",
                    headers: { Accept: "application/json" },
                },
            );
            const payload = await response.json();
            if (!response.ok || !payload?.success) {
                return;
            }
            const updated: SelfStudyLesson[] = payload?.data?.lessons || [];
            onLessonsUpdated?.(updated);
        } catch {
            // non-fatal
        }
    }, [courseAuthId, onLessonsUpdated]);

    // Restore an active session (if any) from localStorage
    React.useEffect(() => {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (!parsed?.sessionId || !parsed?.lessonId) return;
            if (Number(parsed.courseAuthId) !== Number(courseAuthId)) return;

            // If already expired, ignore.
            if (parsed.expiresAt) {
                const expiresAt = new Date(parsed.expiresAt);
                if (
                    Number.isFinite(expiresAt.getTime()) &&
                    expiresAt <= new Date()
                ) {
                    localStorage.removeItem(STORAGE_KEY);
                    return;
                }
            }

            setActiveSession(parsed as ActiveSession);
            setIsPlayerUnlocked(false);
            onSelectLesson(Number(parsed.lessonId));
        } catch {
            // ignore
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [courseAuthId]);

    // Keep a lightweight "now" tick while a session is active
    React.useEffect(() => {
        if (!activeSession?.sessionId) return;
        const interval = window.setInterval(() => {
            setNowTick(Date.now());
        }, 10_000);
        return () => window.clearInterval(interval);
    }, [activeSession?.sessionId]);

    // Hydrate session status from the backend so refresh doesn't reset pause/progress/time.
    React.useEffect(() => {
        if (!activeSession?.sessionId) return;

        let cancelled = false;

        const fetchStatus = async () => {
            try {
                const response = await fetch(
                    `/classroom/lesson/session-status/${activeSession.sessionId}`,
                    {
                        method: "GET",
                        headers: { Accept: "application/json" },
                    },
                );
                const payload = await response.json();
                if (cancelled) return;
                if (!response.ok || !payload?.success || !payload?.session) {
                    return;
                }

                const s = payload.session;

                // If backend says expired/inactive, clear local session.
                if (s.isExpired || s.isActive === false) {
                    localStorage.removeItem(STORAGE_KEY);
                    setActiveSession(null);
                    setIsPlayerUnlocked(false);
                    return;
                }

                const hydrated: ActiveSession = {
                    sessionId: String(s.sessionId),
                    lessonId: Number(s.lessonId),
                    courseAuthId: Number(s.courseAuthId),
                    startedAt: s.startedAt ? String(s.startedAt) : undefined,
                    expiresAt: String(s.expiresAt || activeSession.expiresAt),
                    completionPercentage: Number(s.completionPercentage || 0),
                    playbackProgressSeconds: Number(
                        s.playbackProgressSeconds || 0,
                    ),
                    totalPauseAllowed: Number(s.totalPauseAllowed || 0),
                    pauseUsed: Number(s.pauseUsed || 0),
                    videoDurationSeconds: Number(s.videoDurationSeconds || 0),
                };

                setActiveSession(hydrated);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(hydrated));

                // Ensure lesson selection stays aligned
                if (Number(s.lessonId) > 0) {
                    onSelectLesson(Number(s.lessonId));
                }
            } catch {
                // non-fatal
            }
        };

        // Fetch once immediately, then periodically to keep pause/progress accurate.
        fetchStatus();
        const interval = window.setInterval(fetchStatus, 20_000);

        return () => {
            cancelled = true;
            window.clearInterval(interval);
        };
        // Intentionally exclude expiresAt etc; sessionId is the driver.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [activeSession?.sessionId]);

    const handleStartSession = async () => {
        if (!selectedLesson) {
            setError("Select a lesson first.");
            return;
        }

        setIsStarting(true);
        setError(null);

        try {
            const videoDurationSeconds =
                Number(selectedLesson.effective_video_seconds) > 0
                    ? Number(selectedLesson.effective_video_seconds)
                    : Number(selectedLesson.video_seconds) > 0
                      ? Number(selectedLesson.video_seconds)
                      : Math.max(
                            60,
                            Number(selectedLesson.duration_minutes || 0) * 60,
                        );

            const response = await fetch("/classroom/lesson/start-session", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    lesson_id: selectedLesson.id,
                    course_auth_id: courseAuthId,
                    video_duration_seconds: videoDurationSeconds,
                    lesson_title: selectedLesson.title,
                }),
            });

            const payload = await response.json();

            if (!response.ok || !payload?.success) {
                const message = payload?.error || "Failed to start session";
                throw new Error(message);
            }

            const session = payload.session;

            const normalized: ActiveSession = {
                sessionId: String(session.sessionId),
                lessonId: Number(session.lessonId),
                courseAuthId: Number(session.courseAuthId),
                startedAt: session.startedAt
                    ? String(session.startedAt)
                    : undefined,
                expiresAt: String(session.expiresAt),
                completionPercentage: Number(session.completionPercentage || 0),
                playbackProgressSeconds: Number(
                    session.playbackProgressSeconds || 0,
                ),
                totalPauseAllowed: Number(session.totalPauseAllowed || 0),
                pauseUsed: Number(session.pauseUsed || 0),
                videoDurationSeconds: Number(
                    session.videoDurationSeconds || videoDurationSeconds,
                ),
            };

            setActiveSession(normalized);
            setIsPlayerUnlocked(false);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(normalized));

            await refreshLessons();
        } catch (e: any) {
            setError(e?.message || "Failed to start session");
        } finally {
            setIsStarting(false);
        }
    };

    const handleCompleteSession = async () => {
        if (!activeSession?.sessionId) return;

        setIsCompleting(true);
        setError(null);

        try {
            const response = await fetch("/classroom/lesson/complete-session", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ session_id: activeSession.sessionId }),
            });

            const payload = await response.json();
            if (!response.ok || !payload?.success) {
                throw new Error(payload?.error || "Failed to complete session");
            }

            localStorage.removeItem(STORAGE_KEY);
            setActiveSession(null);
            await refreshLessons();
        } catch (e: any) {
            setError(e?.message || "Failed to complete session");
        } finally {
            setIsCompleting(false);
        }
    };

    const isSessionForSelectedLesson =
        Boolean(activeSession?.sessionId) &&
        Boolean(selectedLesson?.id) &&
        Number(activeSession?.lessonId) === Number(selectedLesson?.id);

    const allottedMinutes = selectedLesson?.duration_minutes || 0;
    const videoMinutes =
        typeof selectedLesson?.video_minutes === "number"
            ? Number(selectedLesson.video_minutes)
            : selectedLesson?.video_seconds
              ? Math.ceil(Number(selectedLesson.video_seconds) / 60)
              : null;
    const remainingMinutes = quota
        ? Math.floor(Number(quota.remaining_hours || 0) * 60)
        : null;

    const requiredMinutes =
        typeof selectedLesson?.required_minutes === "number"
            ? Number(selectedLesson.required_minutes)
            : (videoMinutes ?? allottedMinutes);

    const hasSufficientQuotaForSession =
        remainingMinutes === null ? true : requiredMinutes <= remainingMinutes;
    const pauseRemainingMinutes = activeSession
        ? Math.max(
              0,
              Number(activeSession.totalPauseAllowed) -
                  Number(activeSession.pauseUsed),
          )
        : 0;

    const sessionTimeRemainingMinutes = React.useMemo(() => {
        if (!activeSession?.expiresAt) return null;
        const expiresMs = new Date(activeSession.expiresAt).getTime();
        if (!Number.isFinite(expiresMs)) return null;
        const remainingMs = expiresMs - nowTick;
        return Math.max(0, Math.ceil(remainingMs / 60000));
    }, [activeSession?.expiresAt, nowTick]);

    return (
        <div className="self-study-tab">
            <h3 style={{ color: "white", marginBottom: "1.5rem" }}>
                <i className="fas fa-play-circle me-2"></i>
                Self Study Mode
            </h3>

            <div className="row g-3">
                <div className="col-12">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-hourglass-half me-2"
                                    style={{ color: "#f39c12" }}
                                ></i>
                                Video Quota
                            </h6>

                            {isLoadingQuota ? (
                                <div className="mt-2" style={mutedText}>
                                    Loading quota…
                                </div>
                            ) : quotaError ? (
                                <div className="mt-2" style={mutedText}>
                                    Quota unavailable.
                                </div>
                            ) : quota ? (
                                <div
                                    className="mt-2"
                                    style={{ color: "#ecf0f1" }}
                                >
                                    <div className="d-flex flex-wrap gap-3">
                                        <div>
                                            <div style={mutedText}>Total</div>
                                            <div style={{ fontWeight: 600 }}>
                                                {Number(
                                                    quota.total_hours,
                                                ).toFixed(2)}
                                                h
                                            </div>
                                        </div>
                                        <div>
                                            <div style={mutedText}>Used</div>
                                            <div style={{ fontWeight: 600 }}>
                                                {Number(
                                                    quota.used_hours,
                                                ).toFixed(2)}
                                                h
                                            </div>
                                        </div>
                                        <div>
                                            <div style={mutedText}>
                                                Remaining
                                            </div>
                                            <div style={{ fontWeight: 600 }}>
                                                {Number(
                                                    quota.remaining_hours,
                                                ).toFixed(2)}
                                                h
                                            </div>
                                        </div>
                                        <div>
                                            <div style={mutedText}>
                                                Refunded
                                            </div>
                                            <div style={{ fontWeight: 600 }}>
                                                {Number(
                                                    quota.refunded_hours,
                                                ).toFixed(2)}
                                                h
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="mt-2" style={mutedText}>
                                    Quota unavailable.
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-12">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-film me-2"
                                    style={{ color: "#3498db" }}
                                ></i>
                                Lesson Player
                            </h6>

                            {!selectedLesson && (
                                <div className="mt-2" style={mutedText}>
                                    Select a lesson from the left sidebar to
                                    start.
                                </div>
                            )}

                            {error && (
                                <div className="mt-3 text-danger">{error}</div>
                            )}

                            {selectedLesson && (
                                <div className="mt-3">
                                    <div className="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div style={{ color: "#ecf0f1" }}>
                                            <div style={{ fontWeight: 600 }}>
                                                {selectedLesson.title}
                                            </div>
                                            <div style={mutedText}>
                                                Allotted Minutes:{" "}
                                                {allottedMinutes}
                                                {videoMinutes
                                                    ? ` • Video: ${videoMinutes} min`
                                                    : ""}
                                                {typeof selectedLesson.required_minutes ===
                                                "number"
                                                    ? ` • Required: ${requiredMinutes} min`
                                                    : ""}
                                                {selectedLesson.is_completed
                                                    ? " • Status: Completed"
                                                    : " • Status: Pending"}
                                            </div>
                                            {isSessionForSelectedLesson &&
                                            activeSession ? (
                                                <div
                                                    className="mt-2"
                                                    style={mutedText}
                                                >
                                                    Progress:{" "}
                                                    {Math.round(
                                                        Number(
                                                            activeSession.completionPercentage,
                                                        ) || 0,
                                                    )}
                                                    %{" • "}Pause Remaining:{" "}
                                                    {pauseRemainingMinutes} min
                                                    {typeof sessionTimeRemainingMinutes ===
                                                    "number"
                                                        ? ` • Time Remaining: ${sessionTimeRemainingMinutes} min`
                                                        : ""}
                                                </div>
                                            ) : null}
                                        </div>

                                        <div className="d-flex align-items-center gap-2">
                                            {!isSessionForSelectedLesson ? (
                                                <button
                                                    className="btn btn-sm btn-info"
                                                    disabled={
                                                        isStarting ||
                                                        !hasSufficientQuotaForSession
                                                    }
                                                    onClick={handleStartSession}
                                                >
                                                    {isStarting
                                                        ? "Starting…"
                                                        : "Start Session"}
                                                </button>
                                            ) : (
                                                <button
                                                    className="btn btn-sm btn-outline-success"
                                                    disabled={isCompleting}
                                                    onClick={
                                                        handleCompleteSession
                                                    }
                                                >
                                                    {isCompleting
                                                        ? "Completing…"
                                                        : "Complete Session"}
                                                </button>
                                            )}
                                        </div>
                                    </div>

                                    {isSessionForSelectedLesson &&
                                    activeSession ? (
                                        <div className="mt-3">
                                            {!isPlayerUnlocked ? (
                                                <div
                                                    className="p-3"
                                                    style={cardStyle}
                                                >
                                                    <div
                                                        style={{
                                                            color: "#ecf0f1",
                                                            fontWeight: 600,
                                                        }}
                                                    >
                                                        Open player (paused)
                                                        when ready
                                                    </div>
                                                    <div
                                                        className="mt-2"
                                                        style={mutedText}
                                                    >
                                                        Player opens paused.
                                                        Press Play when you’re
                                                        ready.
                                                    </div>
                                                    <div className="mt-3">
                                                        <button
                                                            className="btn btn-sm btn-primary"
                                                            onClick={() =>
                                                                setIsPlayerUnlocked(
                                                                    true,
                                                                )
                                                            }
                                                        >
                                                            Open Player
                                                        </button>
                                                    </div>
                                                </div>
                                            ) : (
                                                <SecureVideoPlayer
                                                    activeSession={{
                                                        session_id:
                                                            activeSession.sessionId,
                                                        lesson_id:
                                                            activeSession.lessonId,
                                                        time_remaining_minutes:
                                                            typeof sessionTimeRemainingMinutes ===
                                                            "number"
                                                                ? sessionTimeRemainingMinutes
                                                                : allottedMinutes,
                                                        pause_remaining_minutes:
                                                            pauseRemainingMinutes,
                                                        completion_percentage:
                                                            Number(
                                                                activeSession.completionPercentage,
                                                            ) || 0,
                                                    }}
                                                    lesson={{
                                                        id: selectedLesson.id,
                                                        title: selectedLesson.title,
                                                        description:
                                                            selectedLesson.description ||
                                                            "",
                                                        duration_minutes:
                                                            selectedLesson.duration_minutes,
                                                    }}
                                                    videoUrl={""}
                                                    simulationMode={true}
                                                    simulationSpeed={10}
                                                    requireUserPlay={true}
                                                    onComplete={
                                                        handleCompleteSession
                                                    }
                                                    onProgress={(data) => {
                                                        // Keep local session (and localStorage) aligned for refresh resume UX.
                                                        setActiveSession(
                                                            (prev) => {
                                                                if (!prev)
                                                                    return prev;
                                                                const next: ActiveSession =
                                                                    {
                                                                        ...prev,
                                                                        playbackProgressSeconds:
                                                                            Math.floor(
                                                                                Number(
                                                                                    data.playedSeconds ||
                                                                                        0,
                                                                                ),
                                                                            ),
                                                                        completionPercentage:
                                                                            Number(
                                                                                data.percentage ||
                                                                                    0,
                                                                            ),
                                                                    };
                                                                try {
                                                                    localStorage.setItem(
                                                                        STORAGE_KEY,
                                                                        JSON.stringify(
                                                                            next,
                                                                        ),
                                                                    );
                                                                } catch {
                                                                    // ignore
                                                                }
                                                                return next;
                                                            },
                                                        );
                                                    }}
                                                    onError={(message) => {
                                                        setError(message);
                                                    }}
                                                />
                                            )}
                                        </div>
                                    ) : null}

                                    {!hasSufficientQuotaForSession && (
                                        <div className="mt-3 text-warning">
                                            Not enough quota for this session.
                                            {typeof remainingMinutes ===
                                            "number" ? (
                                                <>
                                                    {" "}
                                                    Required: {
                                                        requiredMinutes
                                                    }{" "}
                                                    min • Remaining:{" "}
                                                    {remainingMinutes} min
                                                </>
                                            ) : null}
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TabSelfStudy;
