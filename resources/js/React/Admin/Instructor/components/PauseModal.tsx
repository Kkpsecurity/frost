import React from "react";

interface PauseModalProps {
    isPaused: boolean;
    breakTimeRemaining: number;
    breakDurationMinutes: number;
    breaksRemaining: number | undefined;
    actionMessage: string | null;
    pausedLessonId: number | null;
    actionLoading: boolean;
    onAction: (path: string, lessonId: number) => Promise<void>;
    onClose: () => void;
}

const PauseModal: React.FC<PauseModalProps> = ({
    isPaused,
    breakTimeRemaining,
    breakDurationMinutes,
    breaksRemaining,
    actionMessage,
    pausedLessonId,
    actionLoading,
    onAction,
    onClose,
}) => {
    return (
        <div
            style={{
                position: "fixed",
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                backgroundColor: "rgba(220, 38, 38, 0.95)",
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
                <div style={{ textAlign: "center", marginBottom: "30px" }}>
                    <i
                        className="fas fa-pause-circle"
                        style={{ fontSize: "64px", color: "#dc2626", marginBottom: "20px" }}
                    />
                    <h2 style={{ color: "white", marginBottom: "10px" }}>
                        Lesson Paused
                    </h2>
                    <p style={{ color: "rgba(255, 255, 255, 0.7)", fontSize: "16px" }}>
                        Take a break. Students will see the lesson is paused.
                    </p>

                    {isPaused && breakTimeRemaining > 0 && (
                        <div
                            style={{
                                backgroundColor: "rgba(59, 130, 246, 0.2)",
                                border: "2px solid rgba(59, 130, 246, 0.5)",
                                borderRadius: "12px",
                                padding: "20px",
                                marginTop: "20px",
                                marginBottom: "10px",
                            }}
                        >
                            <div style={{ fontSize: "14px", color: "#93c5fd", marginBottom: "8px" }}>
                                Break Time Remaining
                            </div>
                            <div
                                style={{
                                    fontSize: "48px",
                                    fontWeight: "bold",
                                    color: breakTimeRemaining <= 60 ? "#fbbf24" : "white",
                                    fontFamily: "monospace",
                                }}
                            >
                                {Math.floor(breakTimeRemaining / 60)}:
                                {String(breakTimeRemaining % 60).padStart(2, "0")}
                            </div>
                            <div style={{ fontSize: "12px", color: "#93c5fd", marginTop: "8px" }}>
                                Break Duration: {breakDurationMinutes} minutes
                            </div>
                        </div>
                    )}

                    {breaksRemaining !== undefined && (
                        <p style={{ color: "#fbbf24", fontSize: "14px", marginTop: "10px" }}>
                            <i className="fas fa-info-circle me-2" />
                            {breaksRemaining} break{breaksRemaining !== 1 ? "s" : ""} remaining
                        </p>
                    )}

                    {actionMessage && (
                        <div
                            style={{
                                backgroundColor: "rgba(220, 38, 38, 0.2)",
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
                                style={{ flex: 1, fontSize: "18px", padding: "12px" }}
                                disabled={actionLoading}
                                onClick={async () => {
                                    if (pausedLessonId) {
                                        await onAction(
                                            "/admin/instructors/lessons/pause",
                                            pausedLessonId,
                                        );
                                    }
                                }}
                            >
                                <i className="fas fa-pause me-2" />
                                {actionLoading ? "Pausing..." : "Start Break"}
                            </button>
                            <button
                                className="btn btn-secondary btn-lg"
                                style={{ flex: 1, fontSize: "18px", padding: "12px" }}
                                disabled={actionLoading}
                                onClick={onClose}
                            >
                                <i className="fas fa-times me-2" />
                                Cancel
                            </button>
                        </>
                    )}
                    {isPaused && (
                        <button
                            className="btn btn-success btn-lg"
                            style={{ width: "100%", fontSize: "18px", padding: "12px" }}
                            disabled={actionLoading}
                            onClick={async () => {
                                if (pausedLessonId) {
                                    await onAction(
                                        "/admin/instructors/lessons/resume",
                                        pausedLessonId,
                                    );
                                    onClose();
                                }
                            }}
                        >
                            <i className="fas fa-play me-2" />
                            {actionLoading ? "Resuming..." : "Resume Lesson"}
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
};

export default PauseModal;
