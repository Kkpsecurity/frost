import React, { useState, useEffect } from "react";
import { LessonType } from "../../types/classroom";

interface LessonProgressBarProps {
    selectedLesson: LessonType | null;
    startTime?: string | null; // ISO timestamp when lesson started
}

/**
 * LessonProgressBar - Shows elapsed time and progress for current lesson
 *
 * Displays:
 * - Selected lesson title and duration
 * - Elapsed time since lesson started
 * - Progress bar showing time completion percentage
 * - Remaining time estimate
 */
const LessonProgressBar: React.FC<LessonProgressBarProps> = ({
    selectedLesson,
    startTime
}) => {
    const [elapsedSeconds, setElapsedSeconds] = useState(0);

    useEffect(() => {
        if (!startTime || !selectedLesson) {
            setElapsedSeconds(0);
            return;
        }

        // Calculate initial elapsed time
        const start = new Date(startTime).getTime();
        const now = Date.now();
        const initialElapsed = Math.floor((now - start) / 1000);
        setElapsedSeconds(initialElapsed);

        // Update every second
        const interval = setInterval(() => {
            const currentNow = Date.now();
            const currentElapsed = Math.floor((currentNow - start) / 1000);
            setElapsedSeconds(currentElapsed);
        }, 1000);

        return () => clearInterval(interval);
    }, [startTime, selectedLesson]);

    // Format seconds to MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    if (!selectedLesson) {
        return (
            <div
                className="card mt-3"
                style={{
                    backgroundColor: "#34495e",
                    border: "none",
                    borderRadius: "0",
                }}
            >
                <div
                    className="card-body text-center py-3"
                    style={{ color: "#95a5a6" }}
                >
                    <i className="fas fa-clock fa-2x mb-2"></i>
                    <p className="mb-0" style={{ fontSize: "0.875rem" }}>
                        Select a lesson to track progress
                    </p>
                </div>
            </div>
        );
    }

    const totalSeconds = selectedLesson.duration_minutes * 60;
    const progressPercentage = startTime
        ? Math.min((elapsedSeconds / totalSeconds) * 100, 100)
        : 0;
    const remainingSeconds = Math.max(totalSeconds - elapsedSeconds, 0);
    const isOvertime = elapsedSeconds > totalSeconds;

    return (
        <div
            className="card mt-3"
            style={{
                backgroundColor: "#34495e",
                border: "none",
                borderRadius: "0",
            }}
        >
            <div
                className="card-header"
                style={{
                    backgroundColor: "#2c3e50",
                    borderBottom: "1px solid rgba(255,255,255,0.1)",
                }}
            >
                <div className="d-flex justify-content-between align-items-center">
                    <h6 className="mb-0" style={{ color: "white", fontSize: "0.875rem" }}>
                        <i className="fas fa-clock me-2"></i>
                        Lesson Progress
                    </h6>
                    {startTime && (
                        <span
                            className="badge"
                            style={{
                                backgroundColor: isOvertime ? '#e74c3c' : '#3498db',
                                fontSize: '0.75rem'
                            }}
                        >
                            {isOvertime ? 'Overtime' : 'In Progress'}
                        </span>
                    )}
                </div>
            </div>
            <div className="card-body" style={{ padding: "1rem" }}>
                {/* Lesson Title */}
                <div className="mb-3">
                    <div style={{ color: "white", fontSize: "0.875rem", fontWeight: "600" }}>
                        {selectedLesson.title}
                    </div>
                    {selectedLesson.description && (
                        <small style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                            {selectedLesson.description}
                        </small>
                    )}
                </div>

                {/* Time Display */}
                <div className="row g-2 mb-3">
                    <div className="col-4">
                        <div style={{ color: "#95a5a6", fontSize: "0.7rem", marginBottom: "0.25rem" }}>
                            Elapsed
                        </div>
                        <div style={{ color: "#3498db", fontSize: "1rem", fontWeight: "600", fontFamily: "monospace" }}>
                            {formatTime(elapsedSeconds)}
                        </div>
                    </div>
                    <div className="col-4">
                        <div style={{ color: "#95a5a6", fontSize: "0.7rem", marginBottom: "0.25rem" }}>
                            Duration
                        </div>
                        <div style={{ color: "white", fontSize: "1rem", fontWeight: "600", fontFamily: "monospace" }}>
                            {formatTime(totalSeconds)}
                        </div>
                    </div>
                    <div className="col-4">
                        <div style={{ color: "#95a5a6", fontSize: "0.7rem", marginBottom: "0.25rem" }}>
                            Remaining
                        </div>
                        <div style={{
                            color: isOvertime ? "#e74c3c" : "#2ecc71",
                            fontSize: "1rem",
                            fontWeight: "600",
                            fontFamily: "monospace"
                        }}>
                            {isOvertime ? '+' + formatTime(elapsedSeconds - totalSeconds) : formatTime(remainingSeconds)}
                        </div>
                    </div>
                </div>

                {/* Progress Bar */}
                <div>
                    <div className="d-flex justify-content-between align-items-center mb-1">
                        <small style={{ color: "#95a5a6", fontSize: "0.7rem" }}>
                            Progress
                        </small>
                        <small style={{ color: "white", fontSize: "0.75rem", fontWeight: "600" }}>
                            {Math.round(progressPercentage)}%
                        </small>
                    </div>
                    <div
                        style={{
                            width: "100%",
                            height: "8px",
                            backgroundColor: "#2c3e50",
                            borderRadius: "4px",
                            overflow: "hidden",
                        }}
                    >
                        <div
                            style={{
                                width: `${progressPercentage}%`,
                                height: "100%",
                                backgroundColor: isOvertime ? "#e74c3c" : "#3498db",
                                transition: "width 0.5s ease",
                                borderRadius: "4px",
                            }}
                        />
                    </div>
                </div>

                {/* Status Message */}
                {!startTime && (
                    <div
                        className="alert mt-3 mb-0"
                        style={{
                            backgroundColor: "rgba(52, 152, 219, 0.1)",
                            border: "1px solid rgba(52, 152, 219, 0.3)",
                            color: "#3498db",
                            padding: "0.5rem",
                            fontSize: "0.75rem",
                            borderRadius: "0.25rem"
                        }}
                    >
                        <i className="fas fa-info-circle me-1"></i>
                        Waiting for instructor to start this lesson
                    </div>
                )}
            </div>
        </div>
    );
};

export default LessonProgressBar;
