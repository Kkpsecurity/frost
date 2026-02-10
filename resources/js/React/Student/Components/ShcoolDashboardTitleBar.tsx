import React from "react";
import { SchoolDashboardTitleBarProps } from "../types/props/classroom.props";
import { useStudent } from "../context/StudentContext";

const SchoolDashboardTitleBar = ({
    title,
    subtitle,
    icon,
    onBackToDashboard,
    onExamClick,
    classroomStatus = null,
    devModeToggle = null,
}: SchoolDashboardTitleBarProps) => {
    const { studentExam, studentExamsByCourseAuth, selectedCourseAuthId } =
        useStudent();

    const effectiveStudentExam =
        (selectedCourseAuthId
            ? studentExamsByCourseAuth?.[selectedCourseAuthId]
            : null) ?? studentExam;

    const showExamButton = Boolean(
        effectiveStudentExam?.has_active_attempt ||
        effectiveStudentExam?.is_ready,
    );

    const handleExamClick = () => {
        // If onExamClick handler is provided, use it
        if (onExamClick) {
            onExamClick();
            return;
        }

        // Fallback behavior if no handler provided
        const nextAttemptAt = effectiveStudentExam?.next_attempt_at;

        if (nextAttemptAt) {
            window.alert(`Exam not ready yet. Next attempt: ${nextAttemptAt}`);
            return;
        }

        window.alert(
            "Exam is available, but the Exam Room UI is not wired into the React app yet.",
        );
    };

    return (
        <div
            className="section-title"
            style={{
                background:
                    "linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color))",
                color: "white",
                padding: "12px 30px",
                boxShadow: "0 4px 15px rgba(0,0,0,0.1)",
                position: "relative",
                margin: 0,
            }}
        >
            <div className="d-flex align-items-center justify-content-between">
                <div>
                    <h4 className="mb-0 fw-semibold text-white">
                        {icon && <span className="me-2">{icon}</span>}
                        {title}
                        {classroomStatus && (
                            <span
                                className={`badge ms-2 ${
                                    classroomStatus === "ONLINE"
                                        ? "bg-success"
                                        : classroomStatus === "WAITING"
                                          ? "bg-warning"
                                          : "bg-secondary"
                                }`}
                                style={{
                                    fontSize: "0.9rem",
                                }}
                            >
                                <i
                                    className={`fas ${
                                        classroomStatus === "ONLINE"
                                            ? "fa-wifi"
                                            : classroomStatus === "WAITING"
                                              ? "fa-clock"
                                              : "fa-wifi-slash"
                                    } me-1`}
                                ></i>
                                {classroomStatus}
                            </span>
                        )}
                    </h4>

                    {subtitle && (
                        <p
                            className="mb-0 mt-2 text-white-50"
                            style={{ fontSize: "1rem" }}
                        >
                            {subtitle}
                        </p>
                    )}
                </div>

                <div className="d-flex align-items-center gap-3">
                    {showExamButton && (
                        <button
                            type="button"
                            className="btn btn-light btn-sm d-flex align-items-center gap-2"
                            onClick={handleExamClick}
                            title="Exam"
                            aria-label="Exam"
                            style={{
                                backgroundColor: "rgba(255, 255, 255, 0.15)",
                                color: "white",
                                fontWeight: "600",
                                border: "2px solid rgba(255, 255, 255, 0.35)",
                                padding: "8px 16px",
                            }}
                        >
                            <i
                                className="fas fa-clipboard-check"
                                aria-hidden="true"
                            />
                            <span>Exam</span>
                        </button>
                    )}
                    {devModeToggle}
                    <button
                        type="button"
                        className="btn btn-light btn-sm d-flex align-items-center gap-2"
                        onClick={onBackToDashboard}
                        title="Back to Dashboard"
                        aria-label="Back to Dashboard"
                        style={{
                            backgroundColor: "white",
                            color: "var(--frost-primary-color)",
                            fontWeight: "600",
                            border: "2px solid white",
                            padding: "8px 16px",
                        }}
                    >
                        <i
                            className="fas fa-arrow-left mr-2"
                            aria-hidden="true"
                        />
                        <span>Dashboard</span>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboardTitleBar;
