import React from "react";
import { t } from "@/i18n";

interface LessonSideBarProps {
    lessons: any[];
    isLoadingLessons: boolean;
    title?: string;
    isLessonCompletedByStudent: (lessonId: number) => boolean;
    isLessonFailedByStudent: (lessonId: number) => boolean;
    isLessonInProgress: (lessonId: number, index: number) => boolean;
    getLessonStatusColor: (lesson: any, index: number) => string;
    getLessonTextColor: (lesson: any, index: number) => string;
    getLessonStatusIcon: (lesson: any, index: number) => React.ReactNode;
    onSelectLesson?: (lessonId: number) => void;
    selectedLessonId?: number | null;
    activeSessionLessonId?: number | null; // Lesson with active video session
    disableNavigation?: boolean; // Disable all navigation during active session
}

const LessonSideBar: React.FC<LessonSideBarProps> = ({
    lessons,
    isLoadingLessons,
    title = t("lessonSideBar.todaysLessons"),
    isLessonCompletedByStudent,
    isLessonFailedByStudent,
    isLessonInProgress,
    getLessonStatusColor,
    getLessonTextColor,
    getLessonStatusIcon,
    onSelectLesson,
    selectedLessonId,
    activeSessionLessonId,
    disableNavigation = false,
}) => {
    return (
        <div
            style={{
                width: "100%",
                backgroundColor: "#34495e",
                borderRight: "2px solid #2c3e50",
                overflowY: "auto",
                height: "100vh",
            }}
        >
            <div className="p-3">
                <div className="d-flex justify-content-between align-items-center mb-3">
                    <h6
                        className="mb-0"
                        style={{ color: "white", fontWeight: "600" }}
                    >
                        <i className="fas fa-list me-2"></i>
                        {title}
                    </h6>
                    <span
                        className="badge"
                        style={{ backgroundColor: "#3498db" }}
                    >
                        {
                            lessons.filter((l) =>
                                isLessonCompletedByStudent(l.lesson_id || l.id),
                            ).length
                        }{" "}
                        / {lessons.length}
                    </span>
                </div>
                {/* Real lesson data from API */}
                <div className="lesson-list">
                    {isLoadingLessons ? (
                        <div className="text-center py-4">
                            <div
                                className="spinner-border text-light"
                                role="status"
                            >
                                <span className="visually-hidden">
                                    {t("lessonSideBar.loadingLessons")}
                                </span>
                            </div>
                        </div>
                    ) : lessons.length === 0 ? (
                        <div
                            className="text-center py-4"
                            style={{ color: "#95a5a6" }}
                        >
                            <i className="fas fa-inbox fa-2x mb-2"></i>
                            <p className="mb-0">{t("lessonSideBar.noLessons")}</p>
                        </div>
                    ) : (
                        lessons.map((lesson, index) => {
                            const baseColor = getLessonStatusColor(
                                lesson,
                                index,
                            );
                            const textColor = getLessonTextColor(lesson, index);
                            const lessonId = lesson.lesson_id || lesson.id;
                            const isCompleted =
                                isLessonCompletedByStudent(lessonId);
                            const isFailed = isLessonFailedByStudent(lessonId);
                            const inProgress = isLessonInProgress(
                                lessonId,
                                index,
                            );
                            const isSelected =
                                typeof selectedLessonId === "number"
                                    ? Number(selectedLessonId) ===
                                    Number(lessonId)
                                    : false;
                            const hasActiveSession =
                                typeof activeSessionLessonId === "number" &&
                                Number(activeSessionLessonId) ===
                                Number(lessonId);
                            const isDisabled =
                                disableNavigation &&
                                !hasActiveSession &&
                                !isSelected;

                            return (
                                <div
                                    key={lesson.id}
                                    className="lesson-item mb-2 p-3"
                                    style={{
                                        backgroundColor: isSelected
                                            ? "#3498db"
                                            : isDisabled
                                                ? "#2c3e50"
                                                : baseColor,
                                        borderRadius: "0.25rem",
                                        border: isSelected
                                            ? "3px solid #2ecc71"
                                            : hasActiveSession
                                                ? "2px solid #e74c3c"
                                                : "1px solid rgba(255,255,255,0.1)",
                                        boxShadow: isSelected
                                            ? "0 4px 8px rgba(52,152,219,0.4)"
                                            : "0 1px 3px rgba(0,0,0,0.1)",
                                        cursor:
                                            isDisabled || !onSelectLesson
                                                ? "not-allowed"
                                                : "pointer",
                                        opacity: isDisabled ? 0.5 : 1,
                                        transition: "all 0.2s ease",
                                        position: "relative",
                                    }}
                                    onClick={() => {
                                        if (!isDisabled && onSelectLesson) {
                                            onSelectLesson(Number(lessonId));
                                        }
                                    }}
                                >
                                    {hasActiveSession && (
                                        <div
                                            className="badge position-absolute top-0 end-0 m-2"
                                            style={{
                                                backgroundColor: "#e74c3c",
                                                fontSize: "0.65rem",
                                            }}
                                        >
                                            <i className="fas fa-play me-1"></i>
                                            {t("lessonSideBar.playing")}
                                        </div>
                                    )}
                                    <div className="d-flex justify-content-between align-items-start mb-2">
                                        <div
                                            style={{
                                                color: isSelected
                                                    ? "white"
                                                    : textColor,
                                                fontSize: "0.95rem",
                                                fontWeight: isSelected
                                                    ? "700"
                                                    : "600",
                                                flex: 1,
                                            }}
                                        >
                                            {lesson.title}
                                            {isSelected && (
                                                <i className="fas fa-check-circle ms-2"></i>
                                            )}
                                        </div>
                                        {getLessonStatusIcon(lesson, index)}
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center">
                                        <small
                                            style={{
                                                color: isSelected
                                                    ? "rgba(255,255,255,0.9)"
                                                    : textColor,
                                                fontSize: "0.8rem",
                                                opacity: 0.9,
                                            }}
                                        >
                                            {t("lessonSideBar.creditMinutes")}{" "}
                                            <strong>
                                                {lesson.duration_minutes}
                                            </strong>
                                        </small>
                                        <small
                                            style={{
                                                color: isSelected
                                                    ? "rgba(255,255,255,0.95)"
                                                    : textColor,
                                                fontSize: "0.8rem",
                                                fontWeight: "600",
                                            }}
                                        >
                                            {isDisabled ? (
                                                <span
                                                    style={{ color: "#95a5a6" }}
                                                >
                                                    <i className="fas fa-lock me-1"></i>
                                                    {t("lessonSideBar.locked")}
                                                </span>
                                            ) : isCompleted ? (
                                                t("lessonSideBar.completed")
                                            ) : isFailed ? (
                                                t("lessonSideBar.failed")
                                            ) : inProgress ? (
                                                t("lessonSideBar.inProgress")
                                            ) : (
                                                t("lessonSideBar.pending")
                                            )}
                                        </small>
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>
            </div>
        </div>
    );
};

export default LessonSideBar;
