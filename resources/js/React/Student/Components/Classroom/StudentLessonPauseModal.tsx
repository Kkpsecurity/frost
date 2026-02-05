import React, { useEffect, useState, useRef } from "react";

interface StudentLessonPauseModalProps {
    isVisible: boolean;
    lessonTitle?: string;
    breaksRemaining?: number;
    breakDurationMinutes?: number;
    breakStartedAt?: string;
}

/**
 * StudentLessonPauseModal
 *
 * Read-only modal shown to students when instructor pauses the lesson.
 * Shows countdown timer and plays alert sounds.
 * Students CANNOT control the pause - they just see the status.
 */
const StudentLessonPauseModal: React.FC<StudentLessonPauseModalProps> = ({
    isVisible,
    lessonTitle = "Lesson",
    breaksRemaining,
    breakDurationMinutes = 15,
    breakStartedAt,
}) => {
    const [remainingSeconds, setRemainingSeconds] = useState<number>(0);
    const audioContextRef = useRef<AudioContext | null>(null);
    const hasPlayedStartSound = useRef(false);
    const hasPlayedWarningSound = useRef(false);

    // Initialize audio context
    useEffect(() => {
        audioContextRef.current = new (
            window.AudioContext || (window as any).webkitAudioContext
        )();
        return () => {
            if (audioContextRef.current) {
                audioContextRef.current.close();
            }
        };
    }, []);

    // Function to play beep sound using Web Audio API
    const playBeep = (
        frequency: number = 800,
        duration: number = 200,
        repeat: number = 1,
    ) => {
        if (!audioContextRef.current) return;

        const playOnce = (delay: number = 0) => {
            setTimeout(() => {
                const oscillator = audioContextRef.current!.createOscillator();
                const gainNode = audioContextRef.current!.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContextRef.current!.destination);

                oscillator.frequency.value = frequency;
                oscillator.type = "sine";

                gainNode.gain.setValueAtTime(
                    0.3,
                    audioContextRef.current!.currentTime,
                );
                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    audioContextRef.current!.currentTime + duration / 1000,
                );

                oscillator.start(audioContextRef.current!.currentTime);
                oscillator.stop(
                    audioContextRef.current!.currentTime + duration / 1000,
                );
            }, delay);
        };

        for (let i = 0; i < repeat; i++) {
            playOnce(i * (duration + 100));
        }
    };

    // Play sound when pause starts
    useEffect(() => {
        if (isVisible && !hasPlayedStartSound.current) {
            console.log("🔊 Playing pause start sound");
            playBeep(600, 150, 2); // Two beeps when pause starts
            hasPlayedStartSound.current = true;
        }

        if (!isVisible) {
            hasPlayedStartSound.current = false;
            hasPlayedWarningSound.current = false;
            setRemainingSeconds(0);
        }
    }, [isVisible, breakStartedAt]);

    // Calculate remaining time and play warning sound
    useEffect(() => {
        if (!isVisible || !breakStartedAt) {
            setRemainingSeconds(0);
            return;
        }

        // Match instructor timer behavior: always compute from server break start time.
        hasPlayedWarningSound.current = false;

        const interval = setInterval(() => {
            const startTime = new Date(breakStartedAt).getTime();
            const now = Date.now();
            const elapsedSeconds = Math.floor((now - startTime) / 1000);
            const totalSeconds = breakDurationMinutes * 60;
            const remaining = Math.max(0, totalSeconds - elapsedSeconds);

            setRemainingSeconds(remaining);

            // Play warning sound at 1 minute before end
            if (remaining === 60 && !hasPlayedWarningSound.current) {
                console.log("🔊 Playing 1-minute warning sound");
                playBeep(1000, 300, 3); // Three urgent beeps at 1 minute remaining
                hasPlayedWarningSound.current = true;
            }
        }, 1000);

        return () => clearInterval(interval);
    }, [isVisible, breakStartedAt, breakDurationMinutes]);

    if (!isVisible) return null;

    // Format time as MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, "0")}`;
    };

    // Calculate progress percentage
    const totalSeconds = breakDurationMinutes * 60;
    const elapsedSeconds = totalSeconds - remainingSeconds;
    const isSynced = Boolean(breakStartedAt);
    const progressPercentage = isSynced
        ? (elapsedSeconds / totalSeconds) * 100
        : 0;
    const isNearingEnd =
        isSynced && remainingSeconds <= 60 && remainingSeconds > 0;
    const displayTime = isSynced ? formatTime(remainingSeconds) : "--:--";

    return (
        <>
            {/* Full-Screen Pause Modal - Red Backdrop */}
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
                                color: isNearingEnd ? "#fbbf24" : "#dc2626",
                                marginBottom: "20px",
                                animation: isNearingEnd
                                    ? "fastPulse 1s infinite"
                                    : "pulse 2s infinite",
                            }}
                        />
                        <h2 style={{ color: "white", marginBottom: "10px" }}>
                            Lesson Paused
                        </h2>
                        <p
                            style={{
                                color: "rgba(255, 255, 255, 0.7)",
                                fontSize: "16px",
                            }}
                        >
                            Your instructor has paused the lesson. Please wait.
                        </p>

                        {/* Countdown Timer */}
                        <div
                            style={{
                                backgroundColor: isNearingEnd
                                    ? "rgba(251, 191, 36, 0.2)"
                                    : "rgba(220, 38, 38, 0.2)",
                                border: `2px solid ${isNearingEnd ? "#fbbf24" : "#dc2626"}`,
                                borderRadius: "12px",
                                padding: "20px",
                                marginTop: "20px",
                                marginBottom: "20px",
                            }}
                        >
                            <div
                                style={{
                                    fontSize: "48px",
                                    fontWeight: "bold",
                                    color: isNearingEnd ? "#fbbf24" : "#ef4444",
                                    marginBottom: "8px",
                                }}
                            >
                                {displayTime}
                            </div>
                            <div
                                style={{
                                    fontSize: "14px",
                                    color: "rgba(255, 255, 255, 0.6)",
                                }}
                            >
                                {isSynced
                                    ? `${breakDurationMinutes}-minute break`
                                    : "Syncing break timer..."}
                            </div>
                            {/* Progress Bar */}
                            <div
                                style={{
                                    marginTop: "12px",
                                    backgroundColor: "rgba(0, 0, 0, 0.3)",
                                    borderRadius: "4px",
                                    height: "6px",
                                    overflow: "hidden",
                                }}
                            >
                                <div
                                    style={{
                                        width: `${progressPercentage}%`,
                                        height: "100%",
                                        backgroundColor: isNearingEnd
                                            ? "#fbbf24"
                                            : "#ef4444",
                                        transition: "width 1s linear",
                                    }}
                                />
                            </div>
                        </div>

                        {isNearingEnd && (
                            <div
                                style={{
                                    backgroundColor: "rgba(251, 191, 36, 0.2)",
                                    border: "1px solid rgba(251, 191, 36, 0.5)",
                                    borderRadius: "8px",
                                    padding: "12px",
                                    marginBottom: "16px",
                                    color: "#fbbf24",
                                    fontSize: "14px",
                                }}
                            >
                                <i className="fas fa-exclamation-triangle me-2" />
                                Break ending soon - get ready to resume!
                            </div>
                        )}
                        {lessonTitle && (
                            <p
                                style={{
                                    color: "#3b82f6",
                                    fontSize: "14px",
                                    marginTop: "10px",
                                    fontWeight: "600",
                                }}
                            >
                                <i className="fas fa-book-open me-2" />
                                {lessonTitle}
                            </p>
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

                        {/* Info Box */}
                        <div
                            style={{
                                backgroundColor: "rgba(59, 130, 246, 0.2)",
                                border: "1px solid rgba(59, 130, 246, 0.5)",
                                borderRadius: "8px",
                                padding: "16px",
                                marginTop: "20px",
                                color: "#93c5fd",
                                textAlign: "left",
                            }}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    alignItems: "start",
                                    gap: "12px",
                                }}
                            >
                                <i
                                    className="fas fa-clock"
                                    style={{
                                        fontSize: "20px",
                                        marginTop: "2px",
                                        flexShrink: 0,
                                    }}
                                />
                                <div>
                                    <h6
                                        style={{
                                            color: "#60a5fa",
                                            marginBottom: "8px",
                                            fontWeight: "600",
                                        }}
                                    >
                                        Break Time
                                    </h6>
                                    <p
                                        style={{
                                            fontSize: "14px",
                                            lineHeight: "1.5",
                                            margin: 0,
                                        }}
                                    >
                                        Your instructor is taking a short break.
                                        The lesson will resume shortly. Stay on
                                        this page to continue when ready.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Pulse animation */}
            <style>{`
                @keyframes pulse {
                    0%, 100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: 0.5;
                    }
                }
                @keyframes fastPulse {
                    0%, 100% {
                        opacity: 1;
                        transform: scale(1);
                    }
                    50% {
                        opacity: 0.7;
                        transform: scale(1.05);
                    }
                }
            `}</style>
        </>
    );
};

export default StudentLessonPauseModal;
