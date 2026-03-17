import React from "react";
import { SchoolDashboardTitleBarProps } from "../types/props/classroom.props";
import { useStudent } from "../context/StudentContext";
import { t } from "@/i18n";

const SchoolDashboardTitleBar = ({
    title,
    subtitle,
    courseName,
    icon,
    onBackToDashboard,
    onExamClick,
    classroomStatus = null,
    devModeToggle = null,
    courseAuthId = null,
}: SchoolDashboardTitleBarProps) => {
    const { studentExam, studentExamsByCourseAuth, selectedCourseAuthId } =
        useStudent();

    // Use explicitly passed courseAuthId if available, otherwise fall back to selectedCourseAuthId
    const effectiveCourseAuthId = courseAuthId ?? selectedCourseAuthId;

    const effectiveStudentExam =
        (effectiveCourseAuthId
            ? studentExamsByCourseAuth?.[effectiveCourseAuthId]
            : null) ?? studentExam;

    // 🔍 DEBUG: Log exam button visibility logic
    console.group("🎓 SchoolDashboardTitleBar Exam Button Debug");
    console.log("Props courseAuthId:", courseAuthId);
    console.log("Context selectedCourseAuthId:", selectedCourseAuthId);
    console.log("Effective courseAuthId:", effectiveCourseAuthId);
    console.log("Full studentExamsByCourseAuth:", studentExamsByCourseAuth);
    console.log("Effective exam data:", effectiveStudentExam);

    if (effectiveStudentExam) {
        console.log("✅ Exam data found:");
        console.log("  - is_ready:", effectiveStudentExam.is_ready);
        console.log(
            "  - has_active_attempt:",
            effectiveStudentExam.has_active_attempt,
        );
        console.log(
            "  - next_attempt_at:",
            effectiveStudentExam.next_attempt_at,
        );
        console.log(
            "  - failure_reason:",
            (effectiveStudentExam as any).failure_reason ?? "(none — exam is ready)",
        );
        if ((effectiveStudentExam as any).failure_reason === null && effectiveStudentExam.is_ready) {
            console.log("  ℹ️  Exam is READY — all lessons complete (or admin override)");
        }
        if ((effectiveStudentExam as any).failure_reason === 'lessons') {
            console.warn("  ⚠️  Exam blocked: not all lessons completed");
        }
    } else {
        console.warn(
            "❌ No exam data found for courseAuthId:",
            effectiveCourseAuthId,
        );
        console.log(
            "Available courseAuthIds in studentExamsByCourseAuth:",
            studentExamsByCourseAuth
                ? Object.keys(studentExamsByCourseAuth)
                : "null",
        );
    }

    // 🎯 Show exam button ONLY when:
    // 1. All lessons are genuinely complete (all_lessons_completed is NEVER bypassed by exam_admin_id)
    // 2. OR student already has an active exam attempt in progress
    //
    // We intentionally do NOT use is_ready here because is_ready=true for admin-override accounts
    // even when lessons are not finished, which would surface the button prematurely.
    const showExamButton = Boolean(
        effectiveStudentExam?.has_active_attempt ||
        effectiveStudentExam?.all_lessons_completed,
    );

    console.log("Exam button visibility check:");
    console.log("  - has_active_attempt:", effectiveStudentExam?.has_active_attempt);
    console.log("  - is_ready:", effectiveStudentExam?.is_ready);
    console.log("  - Final showExamButton:", showExamButton);
    console.groupEnd();

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
                                className={`badge ms-2 ${classroomStatus === "ONLINE"
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
                                    className={`fas ${classroomStatus === "ONLINE"
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

                    {courseName && (
                        <p
                            className="mb-0 mt-1 text-white"
                            style={{ fontSize: "0.875rem", opacity: 0.85 }}
                        >
                            {courseName}
                        </p>
                    )}
                    {subtitle && (
                        <p
                            className="mb-0 mt-1 text-white-50"
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
                            title={t('titleBar.exam')}
                            aria-label={t('titleBar.exam')}
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
                            <span>{t('titleBar.exam')}</span>
                        </button>
                    )}
                    {devModeToggle}
                    <button
                        type="button"
                        className="btn btn-light btn-sm d-flex align-items-center gap-2"
                        onClick={onBackToDashboard}
                        title={t('titleBar.backToDashboard')}
                        aria-label={t('titleBar.backToDashboard')}
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
                        <span>{t('titleBar.dashboard')}</span>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboardTitleBar;
