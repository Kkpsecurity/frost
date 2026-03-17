import React, { useState, useEffect, useRef } from "react";
import { t } from "@/i18n";
import PauseModal from "./PauseModal";

interface SecureVideoPlayerProps {
    activeSession: {
        session_id?: string;
        sessionId?: string;
        lesson_id?: number;
        lessonId?: number;
        time_remaining_minutes: number;
        pause_remaining_minutes: number;
        completion_percentage?: number;
        completionPercentage?: number;
        playbackProgressSeconds?: number;
        playback_progress_seconds?: number;
        pause_allocation?: {
            total_minutes: number;
            pauses: Array<{
                duration_minutes: number;
                label: string;
            }>;
            current_pause_index: number;
        };
    };
    lesson: {
        id: number;
        title: string;
        description?: string;
        duration_minutes: number;
    };
    videoUrl: string;
    completionThreshold?: number;
    simulationMode?: boolean;
    simulationSpeed?: number;
    pauseWarningSeconds?: number;
    pauseAlertSound?: string;
    requireUserPlay?: boolean;
    useViewportHeight?: boolean;
    onComplete: () => void;
    onProgress: (data: { playedSeconds: number; percentage: number }) => void;
    onError: (error: string) => void;
}

const SecureVideoPlayer: React.FC<SecureVideoPlayerProps> = ({
    activeSession,
    lesson,
    videoUrl,
    completionThreshold = 80,
    simulationMode = true,
    simulationSpeed = 10,
    pauseWarningSeconds = 30,
    pauseAlertSound = "/sounds/pause-warning.mp3",
    requireUserPlay = false,
    useViewportHeight = false,
    onComplete,
    onProgress,
    onError,
}) => {
    const videoRef = useRef<HTMLVideoElement | null>(null);
    const initialSessionIdRef = useRef<string | null>(null);

    const resolvedSessionId =
        (activeSession as any)?.session_id ?? (activeSession as any)?.sessionId;
    const resolvedCompletionPercentage =
        (activeSession as any)?.completion_percentage ??
        (activeSession as any)?.completionPercentage ??
        0;
    const resolvedPlaybackProgressSeconds =
        (activeSession as any)?.playbackProgressSeconds ??
        (activeSession as any)?.playback_progress_seconds ??
        0;

    // Playback state
    const [playing, setPlaying] = useState(false);
    const [hasUserStarted, setHasUserStarted] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);
    const [furthestPointReached, setFurthestPointReached] = useState(0);
    const [volume, setVolume] = useState(1.0); // Volume from 0.0 to 1.0
    const [isMuted, setIsMuted] = useState(false);

    // Pause tracking
    const [pauseStartTime, setPauseStartTime] = useState<number | null>(null);
    const [totalPauseTime, setTotalPauseTime] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const [pauseRemainingSeconds, setPauseRemainingSeconds] = useState(0);
    const [currentPauseIndex, setCurrentPauseIndex] = useState(0);

    // Progress tracking
    const [lastSavedProgress, setLastSavedProgress] = useState(0);
    const progressSaveInterval = 30; // Save progress every 30 seconds

    // Settings - Convert threshold from percentage (0-100) to decimal (0-1)
    const completionThresholdDecimal = completionThreshold / 100;
    const rewindSeconds = 10; // Rewind button skips back 10 seconds

    // Get pause allocation data
    const pauseAllocation = activeSession.pause_allocation || {
        total_minutes: 10,
        pauses: [{ duration_minutes: 10, label: "Break" }],
        current_pause_index: 0,
    };

    const currentPause =
        pauseAllocation.pauses[currentPauseIndex] || pauseAllocation.pauses[0];

    // Pause limits (display only for now)
    const MAX_PAUSES = 3;
    const configuredPauseCount = Array.isArray(pauseAllocation.pauses)
        ? pauseAllocation.pauses.length
        : 0;
    const pausesAllowed = Math.min(configuredPauseCount, MAX_PAUSES);
    const pausesUsed = Math.min(currentPauseIndex, pausesAllowed);
    const pausesRemaining = Math.max(0, pausesAllowed - pausesUsed);

    const isPlaybackLocked = requireUserPlay && !hasUserStarted;

    // Keep the real video element in sync with React state.
    useEffect(() => {
        if (simulationMode) return;
        const video = videoRef.current;
        if (!video) return;

        if (playing && !isPlaybackLocked) {
            const playPromise = video.play();
            // Ignore autoplay-related errors; the UI is user-driven.
            if (
                playPromise &&
                typeof (playPromise as any).catch === "function"
            ) {
                (playPromise as any).catch(() => undefined);
            }
        } else {
            video.pause();
        }
    }, [playing, isPlaybackLocked, simulationMode]);

    // Initialize duration from lesson duration_minutes in simulation mode
    useEffect(() => {
        if (simulationMode && lesson.duration_minutes > 0) {
            const durationInSeconds = lesson.duration_minutes * 60;
            setDuration(durationInSeconds);
        }

        // Restore volume from localStorage
        try {
            const savedVolume = localStorage.getItem("video_player_volume");
            if (savedVolume) {
                const vol = parseFloat(savedVolume);
                if (!isNaN(vol) && vol >= 0 && vol <= 1) {
                    setVolume(vol);
                }
            }
        } catch {
            // ignore
        }
    }, [simulationMode, lesson.duration_minutes]);

    // Persist volume to localStorage
    useEffect(() => {
        try {
            localStorage.setItem("video_player_volume", volume.toString());
        } catch {
            // ignore
        }

        // Apply volume to video element if it exists
        if (videoRef.current) {
            videoRef.current.volume = volume;
        }
    }, [volume]);

    // Apply mute state to video element
    useEffect(() => {
        if (videoRef.current) {
            videoRef.current.muted = isMuted;
        }
    }, [isMuted]);

    // Always start paused when a NEW session is loaded/mounted.
    // Playback should only begin after an explicit user click on the Play button.
    // Only reset if the session ID actually changed (new session started)
    useEffect(() => {
        if (!resolvedSessionId) return;

        // Track initial session ID on mount
        if (!initialSessionIdRef.current) {
            initialSessionIdRef.current = resolvedSessionId;
            setPlaying(false);
            setHasUserStarted(false);
            return;
        }

        // Only reset if session ID changed (different session)
        if (initialSessionIdRef.current !== resolvedSessionId) {
            initialSessionIdRef.current = resolvedSessionId;
            setPlaying(false);
            setHasUserStarted(false);
        }
    }, [resolvedSessionId]);

    // Safety: if something tries to autoplay before the student presses Play,
    // immediately force playback off.
    useEffect(() => {
        if (!requireUserPlay) return;
        if (playing && !hasUserStarted) {
            setPlaying(false);
        }
    }, [requireUserPlay, playing, hasUserStarted]);

    // Simulation playback timer
    useEffect(() => {
        if (!simulationMode || !playing || duration === 0) {
            return;
        }

        const interval = setInterval(() => {
            setCurrentTime((prev) => {
                const newTime = prev + simulationSpeed; // Advance by simulationSpeed seconds per second

                // Check if reached end
                if (newTime >= duration) {
                    setPlaying(false);
                    return duration;
                }

                // Update furthest point if moving forward
                if (newTime > furthestPointReached) {
                    setFurthestPointReached(newTime);
                }

                return newTime;
            });
        }, 1000); // Update every second

        return () => clearInterval(interval);
    }, [
        simulationMode,
        playing,
        duration,
        simulationSpeed,
        furthestPointReached,
    ]);

    // Auto-save progress and check completion in simulation mode
    useEffect(() => {
        if (!simulationMode || !playing) {
            return;
        }

        // Save progress every 30 seconds of real time
        const progressInterval = setInterval(() => {
            if (currentTime > lastSavedProgress + progressSaveInterval) {
                const percentage =
                    duration > 0 ? (currentTime / duration) * 100 : 0;
                onProgress({ playedSeconds: currentTime, percentage });
                updateProgress(currentTime, percentage);
                setLastSavedProgress(currentTime);
            }

            // Check for completion
            if (duration > 0) {
                const completionPercentage = currentTime / duration;
                if (completionPercentage >= completionThresholdDecimal) {
                    handleCompletion();
                }
            }
        }, 5000); // Check every 5 seconds

        return () => clearInterval(progressInterval);
    }, [simulationMode, playing, currentTime, duration, lastSavedProgress]);

    // Initialize furthest point from session completion percentage
    useEffect(() => {
        if (
            duration > 0 &&
            (resolvedCompletionPercentage > 0 ||
                resolvedPlaybackProgressSeconds > 0)
        ) {
            // Prefer exact seconds over percentage
            const savedPosition =
                resolvedPlaybackProgressSeconds > 0
                    ? resolvedPlaybackProgressSeconds
                    : (resolvedCompletionPercentage / 100) * duration;

            setFurthestPointReached(savedPosition);
            setCurrentTime(savedPosition);

            // Seek to saved position (only in real video mode)
            if (!simulationMode && videoRef.current) {
                // If metadata isn't loaded yet, the browser may ignore this.
                // We'll also set it again on metadata load.
                videoRef.current.currentTime = savedPosition;
            }
        }
    }, [
        duration,
        resolvedCompletionPercentage,
        resolvedPlaybackProgressSeconds,
        simulationMode,
    ]);

    // Handle play/pause
    const handlePlayPause = () => {
        if (playing) {
            // Pausing - show pause modal
            setPlaying(false);
            setIsPaused(true);

            // Set pause duration from current pause allocation
            const pauseDurationSeconds = currentPause.duration_minutes * 60;
            setPauseRemainingSeconds(pauseDurationSeconds);
            setPauseStartTime(Date.now());
        } else {
            // Can only resume if not in pause modal
            if (!isPaused) {
                setHasUserStarted(true);
                setPlaying(true);
            }
        }
    };

    // Handle resume from pause modal
    const handleResume = () => {
        if (pauseStartTime) {
            const pauseDuration = (Date.now() - pauseStartTime) / 1000; // Convert to seconds
            setTotalPauseTime((prev) => prev + pauseDuration);
            setPauseStartTime(null);

            // Track pause time in backend
            trackPauseTime(Math.floor(pauseDuration));
        }

        setIsPaused(false);
        setPauseRemainingSeconds(0);
        setHasUserStarted(true);
        setPlaying(true);

        // Move to next pause if available
        if (currentPauseIndex < pauseAllocation.pauses.length - 1) {
            setCurrentPauseIndex((prev) => prev + 1);
        }
    };

    // Handle pause time expired
    const handlePauseTimeExpired = () => {
        // Automatically resume
        handleResume();
    };

    // Countdown timer for pause modal
    useEffect(() => {
        if (!isPaused || pauseRemainingSeconds <= 0) {
            return;
        }

        const interval = setInterval(() => {
            setPauseRemainingSeconds((prev) => {
                if (prev <= 1) {
                    clearInterval(interval);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [isPaused, pauseRemainingSeconds]);

    // Handle rewind button
    const handleRewind = () => {
        const newTime = Math.max(0, currentTime - rewindSeconds);
        setCurrentTime(newTime);

        // In real video mode, seek the player
        if (!simulationMode && videoRef.current) {
            videoRef.current.currentTime = newTime;
        }
    };

    const handleVideoTimeUpdate = (
        event: React.SyntheticEvent<HTMLVideoElement>,
    ) => {
        const video = event.currentTarget;
        const playedSeconds = video.currentTime;
        const dur = Number.isFinite(video.duration) ? video.duration : 0;

        if (dur > 0 && dur !== duration) {
            setDuration(dur);
        }

        setCurrentTime(playedSeconds);

        // Only update furthest point if moving forward
        if (playedSeconds > furthestPointReached) {
            setFurthestPointReached(playedSeconds);
        }

        // Save progress every 30 seconds
        if (playedSeconds - lastSavedProgress >= progressSaveInterval) {
            const percentage = dur > 0 ? (playedSeconds / dur) * 100 : 0;
            onProgress({ playedSeconds, percentage });
            setLastSavedProgress(playedSeconds);

            // Update backend
            updateProgress(playedSeconds, percentage);
        }

        // Check for completion
        if (dur > 0) {
            const completionPercentage = playedSeconds / dur;
            if (completionPercentage >= completionThresholdDecimal) {
                handleCompletion();
            }
        }
    };

    const handleVideoSeeking = (
        event: React.SyntheticEvent<HTMLVideoElement>,
    ) => {
        const video = event.currentTarget;
        const attemptedSeconds = video.currentTime;

        // Only allow seeking to positions <= furthestPointReached
        // (small tolerance prevents jitter on some browsers)
        if (attemptedSeconds <= furthestPointReached + 0.25) {
            return;
        }

        video.currentTime = furthestPointReached;
        setCurrentTime(furthestPointReached);

        // Show warning
        onError(
            t("secureVideoPlayer.errorForwardSkip"),
        );
    };

    // Handle completion
    const handleCompletion = async () => {
        setPlaying(false);

        // Await the final progress flush so the cache has the correct
        // completion_percentage before completeSession reads it.
        const finalPercentage = (furthestPointReached / duration) * 100;
        await updateProgress(furthestPointReached, finalPercentage);

        // Notify parent (triggers completeSession on the backend)
        onComplete();
    };

    // API call to update progress
    const updateProgress = async (
        playedSeconds: number,
        percentage: number,
    ) => {
        try {
            // In simulation mode without session, just log locally
            if (!resolvedSessionId) {
                console.warn("No session ID - progress not saved to server", {
                    playedSeconds,
                    percentage,
                });
                return;
            }

            const response = await fetch("/classroom/lesson/update-progress", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    session_id: resolvedSessionId,
                    playback_seconds: Math.floor(playedSeconds),
                    completion_percentage: Math.min(
                        100,
                        Math.floor(percentage),
                    ),
                }),
            });

            if (!response.ok) {
                console.error(
                    "Failed to update progress:",
                    await response.text(),
                );
            }
        } catch (error) {
            console.error("Error updating progress:", error);
        }
    };

    // API call to track pause time
    const trackPauseTime = async (pauseSeconds: number) => {
        try {
            if (!resolvedSessionId) return;
            const pauseMinutes = pauseSeconds / 60; // Convert to minutes
            const response = await fetch("/classroom/lesson/track-pause", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    session_id: resolvedSessionId,
                    pause_minutes: pauseMinutes,
                }),
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error("Failed to track pause time:", errorText);
            } else {
                const data = await response.json();
                console.log("✅ Pause time tracked:", data);
            }
        } catch (error) {
            console.error("❌ Error tracking pause time:", error);
        }
    };

    // Format time for display
    const formatTime = (seconds: number): string => {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);

        if (h > 0) {
            return `${h}:${m.toString().padStart(2, "0")}:${s.toString().padStart(2, "0")}`;
        }
        return `${m}:${s.toString().padStart(2, "0")}`;
    };

    // Calculate progress percentage
    const progressPercentage =
        duration > 0 ? (currentTime / duration) * 100 : 0;
    const furthestProgressPercentage =
        duration > 0 ? (furthestPointReached / duration) * 100 : 0;

    return (
        <div
            className="secure-video-player"
            style={{
                maxWidth: "100%",
                marginLeft: 0,
                marginRight: 0,
                height: useViewportHeight ? "100vh" : undefined,
                display: useViewportHeight ? "flex" : undefined,
                flexDirection: useViewportHeight ? "column" : undefined,
            }}
        >
            {/* Video Player Container */}
            <div
                className={`video-container${useViewportHeight ? "" : " ratio ratio-16x9"} ${useViewportHeight ? "mb-2" : "mb-3"}`}
                style={{
                    backgroundColor: "#000",
                    borderRadius: "8px",
                    overflow: "hidden",
                    position: "relative",
                    flex: useViewportHeight ? 1 : undefined,
                    minHeight: useViewportHeight ? 0 : undefined,
                }}
            >
                {simulationMode ? (
                    // Simulation Mode Display
                    <div
                        style={{
                            position: "absolute",
                            top: 0,
                            left: 0,
                            width: "100%",
                            height: "100%",
                            display: "flex",
                            flexDirection: "column",
                            alignItems: "center",
                            justifyContent: "center",
                            background:
                                "linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)",
                        }}
                    >
                        {isPlaybackLocked ? (
                            <button
                                type="button"
                                onClick={() => {
                                    setHasUserStarted(true);
                                    setPlaying(true);
                                }}
                                style={{
                                    background: "transparent",
                                    border: "none",
                                    color: "white",
                                    display: "flex",
                                    flexDirection: "column",
                                    alignItems: "center",
                                    justifyContent: "center",
                                    cursor: "pointer",
                                    padding: 0,
                                }}
                            >
                                <i
                                    className="fas fa-play-circle fa-6x mb-3"
                                    style={{ color: "white", opacity: 0.9 }}
                                ></i>
                                <h3
                                    style={{
                                        color: "white",
                                        marginBottom: "8px",
                                    }}
                                >
                                    {t("secureVideoPlayer.readyToStart")}
                                </h3>
                                <p
                                    style={{
                                        color: "rgba(255,255,255,0.8)",
                                        marginBottom: 0,
                                    }}
                                >
                                    {t("secureVideoPlayer.clickWhenReady")}
                                </p>
                            </button>
                        ) : (
                            <>
                                <i
                                    className={`fas ${playing ? "fa-pause-circle" : "fa-play-circle"} fa-5x mb-3`}
                                    style={{ color: "white", opacity: 0.8 }}
                                ></i>
                                <h3
                                    style={{
                                        color: "white",
                                        marginBottom: "10px",
                                    }}
                                >
                                    {t("secureVideoPlayer.simulationMode")}
                                </h3>
                                <p
                                    style={{
                                        color: "rgba(255,255,255,0.8)",
                                        marginBottom: "5px",
                                    }}
                                >
                                    {t("secureVideoPlayer.lessonLabel", { title: lesson.title })}
                                </p>
                                <p
                                    style={{
                                        color: "rgba(255,255,255,0.6)",
                                        fontSize: "0.9rem",
                                    }}
                                >
                                    {t("secureVideoPlayer.testingSpeed", { speed: simulationSpeed })}
                                </p>
                                <div
                                    style={{
                                        marginTop: "20px",
                                        padding: "10px 20px",
                                        backgroundColor: "rgba(0,0,0,0.3)",
                                        borderRadius: "5px",
                                    }}
                                >
                                    <p
                                        style={{
                                            color: "white",
                                            fontSize: "2rem",
                                            margin: 0,
                                        }}
                                    >
                                        {formatTime(currentTime)} /{" "}
                                        {formatTime(duration)}
                                    </p>
                                </div>
                            </>
                        )}
                    </div>
                ) : (
                    // Real Video Player
                    <div
                        style={{
                            position: "absolute",
                            top: 0,
                            left: 0,
                            width: "100%",
                            height: "100%",
                        }}
                    >
                        <video
                            ref={videoRef}
                            src={videoUrl}
                            playsInline
                            preload="metadata"
                            controls={false}
                            muted={isMuted}
                            disablePictureInPicture
                            controlsList="nodownload noplaybackrate"
                            onTimeUpdate={handleVideoTimeUpdate}
                            onSeeking={handleVideoSeeking}
                            onLoadedMetadata={(e) => {
                                const video = e.currentTarget;

                                // Apply volume immediately on metadata load.
                                try {
                                    video.volume = volume;
                                } catch {
                                    // ignore
                                }

                                if (
                                    Number.isFinite(video.duration) &&
                                    video.duration > 0
                                ) {
                                    setDuration(video.duration);
                                }

                                if (
                                    (resolvedCompletionPercentage > 0 ||
                                        resolvedPlaybackProgressSeconds > 0) &&
                                    Number.isFinite(video.duration) &&
                                    video.duration > 0
                                ) {
                                    const savedPosition =
                                        resolvedPlaybackProgressSeconds > 0
                                            ? resolvedPlaybackProgressSeconds
                                            : (resolvedCompletionPercentage /
                                                100) *
                                            video.duration;
                                    setFurthestPointReached(savedPosition);
                                    setCurrentTime(savedPosition);
                                    video.currentTime = savedPosition;
                                }

                                // Ensure we stay paused when locked.
                                if (isPlaybackLocked) {
                                    video.pause();
                                }
                            }}
                            onPlay={() => {
                                // If something tries to autoplay while locked, immediately stop it.
                                if (isPlaybackLocked) {
                                    try {
                                        videoRef.current?.pause();
                                    } catch {
                                        // ignore
                                    }
                                }
                            }}
                            style={{
                                width: "100%",
                                height: "100%",
                                objectFit: "contain",
                                backgroundColor: "black",
                                pointerEvents: isPlaybackLocked
                                    ? "none"
                                    : "auto",
                            }}
                        />

                        {/* Playback lock overlay (real video) */}
                        {isPlaybackLocked && (
                            <div
                                style={{
                                    position: "absolute",
                                    inset: 0,
                                    display: "flex",
                                    flexDirection: "column",
                                    alignItems: "center",
                                    justifyContent: "center",
                                    background:
                                        "linear-gradient(135deg, rgba(30,60,114,0.85) 0%, rgba(42,82,152,0.85) 100%)",
                                    color: "white",
                                    textAlign: "center",
                                    padding: "24px",
                                }}
                            >
                                <button
                                    type="button"
                                    onClick={() => {
                                        setHasUserStarted(true);
                                        setPlaying(true);
                                    }}
                                    style={{
                                        background: "transparent",
                                        border: "none",
                                        color: "white",
                                        display: "flex",
                                        flexDirection: "column",
                                        alignItems: "center",
                                        justifyContent: "center",
                                        cursor: "pointer",
                                        padding: 0,
                                    }}
                                >
                                    <i
                                        className="fas fa-play-circle fa-6x mb-3"
                                        style={{
                                            color: "white",
                                            opacity: 0.9,
                                        }}
                                    ></i>
                                    <h3
                                        style={{
                                            color: "white",
                                            marginBottom: "8px",
                                        }}
                                    >
                                        {t("secureVideoPlayer.readyToStart")}
                                    </h3>
                                    <p
                                        style={{
                                            color: "rgba(255,255,255,0.85)",
                                            marginBottom: 0,
                                        }}
                                    >
                                        {t("secureVideoPlayer.pressWhenReady")}
                                    </p>
                                </button>
                            </div>
                        )}
                    </div>
                )}
            </div>

            {/* Custom Controls */}
            <div
                className="video-controls-panel"
                style={{
                    backgroundColor: "#2c3e50",
                    borderRadius: "8px",
                    padding: "15px",
                }}
            >
                {/* Progress Bar */}
                <div
                    style={{
                        height: "5px",
                        backgroundColor: "#34495e",
                        position: "relative",
                        overflow: "hidden",
                        marginBottom: "15px",
                        borderRadius: "3px",
                    }}
                >
                    {/* Furthest point reached (light overlay) */}
                    <div
                        style={{
                            width: `${furthestProgressPercentage}%`,
                            height: "5px",
                            backgroundColor: "rgba(52, 152, 219, 0.3)",
                            position: "absolute",
                            top: 0,
                            left: 0,
                        }}
                    />
                    {/* Current playback position */}
                    <div
                        style={{
                            width: `${progressPercentage}%`,
                            height: "5px",
                            backgroundColor: "#3498db",
                            position: "absolute",
                            top: 0,
                            left: 0,
                        }}
                    />
                </div>

                {/* Time Display */}
                <div className="d-flex justify-content-between mb-2">
                    <small style={{ color: "#95a5a6" }}>
                        {formatTime(currentTime)} / {formatTime(duration)}
                    </small>
                    <small style={{ color: "#95a5a6" }}>
                        {t("secureVideoPlayer.progress")} {Math.floor(furthestProgressPercentage)}%
                        {furthestProgressPercentage >= completionThreshold && (
                            <span className="text-success ms-2">
                                <i className="fas fa-check-circle"></i> {t("secureVideoPlayer.readyToComplete")}
                            </span>
                        )}
                    </small>
                </div>

                {/* Pause Limits */}
                <div className="d-flex justify-content-between mb-3">
                    <small style={{ color: "#95a5a6" }}>
                        <i className="fas fa-pause-circle me-2"></i>
                        {t("secureVideoPlayer.pauseLimit", { allowed: pausesAllowed, max: MAX_PAUSES })}
                    </small>
                    <small style={{ color: "#95a5a6" }}>
                        {t("secureVideoPlayer.pausesRemaining", { count: pausesRemaining })}
                    </small>
                </div>

                {/* Control Buttons */}
                <div className="d-flex gap-2 align-items-center">
                    <button
                        className="btn btn-warning"
                        onClick={handleRewind}
                        disabled={currentTime === 0}
                    >
                        <i className="fas fa-backward me-2"></i>
                        {t("secureVideoPlayer.rewindButton", { seconds: rewindSeconds })}
                    </button>

                    <button
                        className={`btn ${playing ? "btn-secondary" : "btn-primary"} flex-grow-1`}
                        onClick={handlePlayPause}
                    >
                        <i
                            className={`fas ${playing ? "fa-pause" : "fa-play"} me-2`}
                        ></i>
                        {playing ? t("secureVideoPlayer.pause") : t("secureVideoPlayer.play")}
                    </button>

                    {/* Volume Controls */}
                    <div className="d-flex align-items-center gap-2 ms-2">
                        <button
                            className="btn btn-sm btn-secondary"
                            onClick={() => setIsMuted(!isMuted)}
                            title={isMuted ? t("secureVideoPlayer.unmute") : t("secureVideoPlayer.mute")}
                        >
                            <i
                                className={`fas ${isMuted ? "fa-volume-mute" : volume > 0.5 ? "fa-volume-up" : "fa-volume-down"}`}
                            ></i>
                        </button>
                        <input
                            type="range"
                            min="0"
                            max="1"
                            step="0.05"
                            value={isMuted ? 0 : volume}
                            onChange={(e) => {
                                const newVolume = parseFloat(e.target.value);
                                setVolume(newVolume);
                                if (newVolume > 0 && isMuted) {
                                    setIsMuted(false);
                                }
                            }}
                            className="form-range"
                            style={{
                                width: "100px",
                                cursor: "pointer",
                            }}
                            title={`Volume: ${Math.round((isMuted ? 0 : volume) * 100)}%`}
                        />
                        <small style={{ color: "#95a5a6", minWidth: "35px" }}>
                            {Math.round((isMuted ? 0 : volume) * 100)}%
                        </small>
                    </div>

                    {furthestProgressPercentage >= completionThreshold && (
                        <button
                            className="btn btn-success"
                            onClick={handleCompletion}
                        >
                            <i className="fas fa-check me-2"></i>
                            {t("secureVideoPlayer.completeLesson")}
                        </button>
                    )}
                </div>

                {/* Info Text */}
                <div className="mt-3">
                    <small style={{ color: "#95a5a6" }}>
                        <i className="fas fa-info-circle me-2"></i>
                        {t("secureVideoPlayer.infoRewind", { threshold: Math.floor(completionThreshold) })}
                    </small>
                </div>
            </div>

            {/* Pause Modal */}
            <PauseModal
                isVisible={isPaused}
                pauseDurationMinutes={currentPause.duration_minutes}
                pauseLabel={currentPause.label}
                remainingSeconds={pauseRemainingSeconds}
                warningSeconds={pauseWarningSeconds}
                alertSoundPath={pauseAlertSound}
                onTimeExpired={handlePauseTimeExpired}
            />
        </div>
    );
};

export default SecureVideoPlayer;
