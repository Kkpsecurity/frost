import React from "react";

interface LessonSideBarProps {
    lessons: any[];
    isLoadingLessons: boolean;
    isLessonCompletedByStudent: (lessonId: number) => boolean;
    isLessonInProgress: (lessonId: number, index: number) => boolean;
    getLessonStatusColor: (lesson: any, index: number) => string;
    getLessonTextColor: (lesson: any, index: number) => string;
    getLessonStatusIcon: (lesson: any, index: number) => React.ReactNode;
    onSelectLesson?: (lessonId: number) => void;
    selectedLessonId?: number | null;
}

const LessonSideBar: React.FC<LessonSideBarProps> = ({
    lessons,
    isLoadingLessons,
    isLessonCompletedByStudent,
    isLessonInProgress,
    getLessonStatusColor,
    getLessonTextColor,
    getLessonStatusIcon,
    onSelectLesson,
    selectedLessonId,
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
                        Today's Lessons
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
                                    Loading lessons...
                                </span>
                            </div>
                        </div>
                    ) : lessons.length === 0 ? (
                        <div
                            className="text-center py-4"
                            style={{ color: "#95a5a6" }}
                        >
                            <i className="fas fa-inbox fa-2x mb-2"></i>
                            <p className="mb-0">No lessons available</p>
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
                            const inProgress = isLessonInProgress(
                                lessonId,
                                index,
                            );
                            const isSelected =
                                typeof selectedLessonId === "number"
                                    ? Number(selectedLessonId) ===
                                      Number(lessonId)
                                    : false;

                            return (
                                <div
                                    key={lesson.id}
                                    className="lesson-item mb-2 p-3"
                                    style={{
                                        backgroundColor: baseColor,
                                        borderRadius: "0.25rem",
                                        border: isSelected
                                            ? "2px solid rgba(255,255,255,0.55)"
                                            : "none",
                                        boxShadow: "0 1px 3px rgba(0,0,0,0.1)",
                                        cursor: onSelectLesson
                                            ? "pointer"
                                            : "default",
                                    }}
                                    onClick={() => {
                                        if (onSelectLesson) {
                                            onSelectLesson(Number(lessonId));
                                        }
                                    }}
                                >
                                    <div className="d-flex justify-content-between align-items-start mb-2">
                                        <div
                                            style={{
                                                color: textColor,
                                                fontSize: "0.95rem",
                                                fontWeight: "600",
                                                flex: 1,
                                            }}
                                        >
                                            {lesson.title}
                                        </div>
                                        {getLessonStatusIcon(lesson, index)}
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center">
                                        <small
                                            style={{
                                                color: textColor,
                                                fontSize: "0.8rem",
                                                opacity: 0.9,
                                            }}
                                        >
                                            Credit Minutes:{" "}
                                            <strong>
                                                {lesson.duration_minutes}
                                            </strong>
                                        </small>
                                        <small
                                            style={{
                                                color: textColor,
                                                fontSize: "0.8rem",
                                                fontWeight: "600",
                                            }}
                                        >
                                            {isCompleted
                                                ? "Completed"
                                                : inProgress
                                                  ? "In Progress"
                                                  : "Pending"}
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
