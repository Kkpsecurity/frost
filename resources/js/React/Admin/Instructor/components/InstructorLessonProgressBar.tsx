import React, { useState, useEffect } from "react";

interface Lesson {
    id: number;
    lesson_name: string;
    lesson_description?: string;
    duration_minutes: number;
    status: 'not_started' | 'in_progress' | 'completed';
    start_time?: string;
    end_time?: string;
    progress_minutes?: number;
}

interface InstructorLessonProgressBarProps {
    currentLesson: Lesson | null;
    lessons: Lesson[];
}

/**
 * InstructorLessonProgressBar - Shows lesson progress and elapsed time for instructors
 *
 * Features:
 * - Current lesson title and description
 * - Elapsed time since lesson started
 * - Progress percentage and time remaining
 * - Overall lesson progress (completed/total)
 * - Instructor controls (Start/End lesson)
 */
const InstructorLessonProgressBar: React.FC<InstructorLessonProgressBarProps> = ({
    currentLesson,
    lessons
}) => {
    const [elapsedSeconds, setElapsedSeconds] = useState(0);

    useEffect(() => {
        if (!currentLesson?.start_time || currentLesson.status !== 'in_progress') {
            setElapsedSeconds(0);
            return;
        }

        // Calculate initial elapsed time
        const start = new Date(currentLesson.start_time).getTime();
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
    }, [currentLesson?.start_time, currentLesson?.status]);

    // Format seconds to MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    // Calculate overall progress
    const completedLessons = lessons.filter(l => l.status === 'completed').length;
    const totalLessons = lessons.length;
    const overallProgress = totalLessons > 0 ? (completedLessons / totalLessons) * 100 : 0;

    if (!currentLesson) {
        return (
            <div
                className="card"
                style={{
                    backgroundColor: "#34495e",
                    border: "none",
                    borderRadius: "8px",
                    marginBottom: "20px"
                }}
            >
                <div
                    className="card-header"
                    style={{
                        backgroundColor: "#2c3e50",
                        borderBottom: "1px solid rgba(255,255,255,0.1)",
                        borderRadius: "8px 8px 0 0"
                    }}
                >
                    <div className="d-flex justify-content-between align-items-center">
                        <h6 className="mb-0" style={{ color: "white", fontSize: "0.875rem" }}>
                            <i className="fas fa-chalkboard-teacher me-2"></i>
                            Lesson Progress
                        </h6>
                        <span
                            className="badge bg-secondary"
                            style={{ fontSize: '0.75rem' }}
                        >
                            {completedLessons} / {totalLessons} Completed
                        </span>
                    </div>
                </div>
                <div
                    className="card-body text-center py-4"
                    style={{ color: "#95a5a6" }}
                >
                    <i className="fas fa-play-circle fa-3x mb-3" style={{ color: "#3498db" }}></i>
                    <h6 style={{ color: "white", marginBottom: "0.5rem" }}>Ready to Start Teaching</h6>
                    <p className="mb-0" style={{ fontSize: "0.875rem" }}>
                        Click "Start Lesson" from the lessons panel to begin
                    </p>
                </div>
            </div>
        );
    }

    const totalSeconds = currentLesson.duration_minutes * 60;
    const progressPercentage = currentLesson.status === 'in_progress' && currentLesson.start_time
        ? Math.min((elapsedSeconds / totalSeconds) * 100, 100)
        : 0;
    const remainingSeconds = Math.max(totalSeconds - elapsedSeconds, 0);
    const isOvertime = elapsedSeconds > totalSeconds;

    return (
        <div
            className="card"
            style={{
                backgroundColor: "#34495e",
                border: "none",
                borderRadius: "8px",
                marginBottom: "20px"
            }}
        >
            <div
                className="card-header"
                style={{
                    backgroundColor: "#2c3e50",
                    borderBottom: "1px solid rgba(255,255,255,0.1)",
                    borderRadius: "8px 8px 0 0"
                }}
            >
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 className="mb-0" style={{ color: "white", fontSize: "0.875rem" }}>
                            <i className="fas fa-chalkboard-teacher me-2"></i>
                            Current Lesson
                        </h6>
                        <small style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                            {completedLessons} / {totalLessons} lessons completed
                        </small>
                    </div>
                    <div className="d-flex gap-2">
                        {currentLesson.status === 'in_progress' && (
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
                        <span
                            className="badge bg-info"
                            style={{ fontSize: '0.75rem' }}
                        >
                            Lesson {lessons.findIndex(l => l.id === currentLesson.id) + 1}
                        </span>
                    </div>
                </div>
            </div>
            <div className="card-body" style={{ padding: "1rem" }}>
                {/* Lesson Info */}
                <div className="mb-3">
                    <div style={{ color: "white", fontSize: "0.875rem", fontWeight: "600", marginBottom: "0.25rem" }}>
                        {currentLesson.lesson_name}
                    </div>
                    {currentLesson.lesson_description && (
                        <small style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                            {currentLesson.lesson_description}
                        </small>
                    )}
                </div>

                {/* Time Display */}
                <div className="row g-2 mb-3">
                    <div className="col-4">
                        <div style={{ color: "#95a5a6", fontSize: "0.7rem", marginBottom: "0.25rem" }}>
                            Elapsed
                        </div>
                        <div style={{
                            color: "#3498db",
                            fontSize: "1rem",
                            fontWeight: "600",
                            fontFamily: "monospace"
                        }}>
                            {formatTime(elapsedSeconds)}
                        </div>
                    </div>
                    <div className="col-4">
                        <div style={{ color: "#95a5a6", fontSize: "0.7rem", marginBottom: "0.25rem" }}>
                            Duration
                        </div>
                        <div style={{
                            color: "white",
                            fontSize: "1rem",
                            fontWeight: "600",
                            fontFamily: "monospace"
                        }}>
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
                            Lesson Progress
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

                {/* Overall Class Progress */}
                <div className="mt-3 pt-3" style={{ borderTop: "1px solid rgba(255,255,255,0.1)" }}>
                    <div className="d-flex justify-content-between align-items-center mb-1">
                        <small style={{ color: "#95a5a6", fontSize: "0.7rem" }}>
                            Class Progress
                        </small>
                        <small style={{ color: "white", fontSize: "0.75rem", fontWeight: "600" }}>
                            {Math.round(overallProgress)}%
                        </small>
                    </div>
                    <div
                        style={{
                            width: "100%",
                            height: "4px",
                            backgroundColor: "#2c3e50",
                            borderRadius: "2px",
                            overflow: "hidden",
                        }}
                    >
                        <div
                            style={{
                                width: `${overallProgress}%`,
                                height: "100%",
                                backgroundColor: "#2ecc71",
                                transition: "width 0.5s ease",
                                borderRadius: "2px",
                            }}
                        />
                    </div>
                </div>

                {/* Status Message */}
                {currentLesson.status === 'not_started' && (
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
                        Ready to start this lesson
                    </div>
                )}

                {currentLesson.status === 'completed' && (
                    <div
                        className="alert mt-3 mb-0"
                        style={{
                            backgroundColor: "rgba(46, 204, 113, 0.1)",
                            border: "1px solid rgba(46, 204, 113, 0.3)",
                            color: "#2ecc71",
                            padding: "0.5rem",
                            fontSize: "0.75rem",
                            borderRadius: "0.25rem"
                        }}
                    >
                        <i className="fas fa-check-circle me-1"></i>
                        Lesson completed successfully
                    </div>
                )}
            </div>
        </div>
    );
};

export default InstructorLessonProgressBar;
