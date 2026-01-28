import React from "react";

interface LessonListSBProps {
    lessons: any[];
    isLoadingLessons: boolean;
    selectedLessonId: number | null;
    handleLessonClick: (id: number) => void;
    getLessonStatusColor: (status: string) => string;
    getLessonStatusIcon: (lesson: any) => React.ReactNode;
    activeTab: string;
    areLessonsLocked: boolean;
    session: any;
    hasActiveSession: boolean;
    setSelectedLessonId: (id: number) => void;
    setPreviewLessonId: (id: number) => void;
    setViewMode: (mode: string) => void;
}

const LessonListSB: React.FC<LessonListSBProps> = ({
    lessons,
    isLoadingLessons,
    selectedLessonId,
    handleLessonClick,
    getLessonStatusColor,
    getLessonStatusIcon,
    activeTab,
    areLessonsLocked,
    session,
    hasActiveSession,
    setSelectedLessonId,
    setPreviewLessonId,
    setViewMode,
}) => {
    return (
        <div className="lesson-list">
            {isLoadingLessons ? (
                <div className="text-center py-4">
                    <div className="spinner-border text-light" role="status">
                        <span className="visually-hidden">
                            Loading lessons...
                        </span>
                    </div>
                </div>
            ) : lessons.length === 0 ? (
                <div className="text-center py-4" style={{ color: "#95a5a6" }}>
                    <i className="fas fa-inbox fa-2x mb-2"></i>
                    <p className="mb-0">No lessons available</p>
                </div>
            ) : (
                lessons.map((lesson) => {
                    const isSelected = selectedLessonId === lesson.id;
                    const baseColor = getLessonStatusColor(lesson.status);
                    const selectedColor = "#2563eb";

                    return (
                        <div
                            key={lesson.id}
                            className="lesson-item mb-2 p-2"
                            onClick={() => handleLessonClick(lesson.id)}
                            style={{
                                backgroundColor: isSelected
                                    ? selectedColor
                                    : baseColor,
                                borderRadius: "0.25rem",
                                cursor: "pointer",
                                transition: "all 0.2s",
                                border: isSelected
                                    ? "2px solid #3b82f6"
                                    : "2px solid transparent",
                                opacity: isSelected ? 1 : 0.85,
                            }}
                            onMouseEnter={(e) => {
                                if (!isSelected) {
                                    e.currentTarget.style.opacity = "1";
                                    e.currentTarget.style.transform =
                                        "translateX(4px)";
                                }
                            }}
                            onMouseLeave={(e) => {
                                if (!isSelected) {
                                    e.currentTarget.style.opacity = "0.85";
                                    e.currentTarget.style.transform =
                                        "translateX(0)";
                                }
                            }}
                        >
                            <div className="d-flex align-items-start">
                                <div className="me-2 mt-1">
                                    {getLessonStatusIcon(lesson)}
                                </div>
                                <div className="flex-grow-1">
                                    <div
                                        style={{
                                            color: "white",
                                            fontSize: "0.875rem",
                                            fontWeight: "500",
                                        }}
                                    >
                                        {lesson.title}
                                    </div>
                                    {lesson.description && (
                                        <small
                                            style={{
                                                color: "rgba(255,255,255,0.7)",
                                                fontSize: "0.7rem",
                                                display: "block",
                                                marginTop: "0.25rem",
                                            }}
                                        >
                                            {lesson.description.length > 60
                                                ? lesson.description.substring(
                                                      0,
                                                      60,
                                                  ) + "..."
                                                : lesson.description}
                                        </small>
                                    )}
                                    <div className="d-flex align-items-center mt-1">
                                        <small
                                            style={{
                                                color: "rgba(255,255,255,0.6)",
                                                fontSize: "0.7rem",
                                            }}
                                        >
                                            <i className="far fa-clock me-1"></i>
                                            {lesson.duration_minutes} min
                                        </small>
                                        {lesson.status === "completed" && (
                                            <small
                                                className="ms-2"
                                                style={{
                                                    color: "#10b981",
                                                    fontSize: "0.7rem",
                                                }}
                                            >
                                                <i className="fas fa-check me-1"></i>
                                                Completed
                                            </small>
                                        )}
                                        {lesson.status === "incomplete" && (
                                            <small
                                                className="ms-2"
                                                style={{
                                                    color: "#3b82f6",
                                                    fontSize: "0.7rem",
                                                }}
                                            >
                                                <i className="fas fa-spinner me-1"></i>
                                                In Progress
                                            </small>
                                        )}
                                        {isSelected && (
                                            <small
                                                className="ms-auto"
                                                style={{
                                                    color: "white",
                                                    fontSize: "0.7rem",
                                                }}
                                            >
                                                <i className="fas fa-arrow-right"></i>
                                            </small>
                                        )}
                                    </div>

                                    {/* Start/Resume/Locked Button - Only visible on Self Study tab */}
                                    {activeTab === "self-study" && (
                                        <button
                                            className={`btn btn-sm mt-2 w-100 ${
                                                lesson.status === "completed"
                                                    ? "btn-outline-success" // Completed - Review
                                                    : "btn-outline-info" // Available - Start
                                            }`}
                                            style={{
                                                padding: "0.375rem 0.75rem",
                                                fontSize: "0.75rem",
                                                fontWeight: "600",
                                                borderRadius: "0.25rem",
                                                cursor:
                                                    areLessonsLocked &&
                                                    session?.lessonId !==
                                                        lesson.id
                                                        ? "not-allowed"
                                                        : "pointer",
                                                opacity:
                                                    areLessonsLocked &&
                                                    session?.lessonId !==
                                                        lesson.id
                                                        ? 0.5
                                                        : 1,
                                                transition: "all 0.2s",
                                            }}
                                            // No lesson locking logic; always enabled
                                            disabled={false}
                                            onClick={(e) => {
                                                e.stopPropagation();

                                                if (
                                                    lesson.status ===
                                                    "completed"
                                                ) {
                                                    // Review completed lesson
                                                    setSelectedLessonId(
                                                        lesson.id,
                                                    );
                                                    setPreviewLessonId(
                                                        lesson.id,
                                                    );
                                                    setViewMode("preview");
                                                } else {
                                                    // Start new lesson - show preview first
                                                    setSelectedLessonId(
                                                        lesson.id,
                                                    );
                                                    setPreviewLessonId(
                                                        lesson.id,
                                                    );
                                                    setViewMode("preview");
                                                }
                                            }}
                                        >
                                            {areLessonsLocked &&
                                            session?.lessonId !== lesson.id ? (
                                                <>
                                                    <i className="fas fa-lock me-1"></i>
                                                    Locked
                                                </>
                                            ) : hasActiveSession &&
                                              session?.lessonId ===
                                                  lesson.id ? (
                                                <>
                                                    <i className="fas fa-play-circle me-1"></i>
                                                    Resume
                                                </>
                                            ) : lesson.status === "completed" ||
                                              lesson.status === false ? (
                                                <>
                                                    <i className="fas fa-eye me-1"></i>
                                                    Review
                                                </>
                                            ) : (
                                                <>
                                                    <i className="fas fa-play me-1"></i>
                                                    Start Lesson
                                                </>
                                            )}
                                        </button>
                                    )}

                                    {/* Review Button - Only visible on Self Study tab for completed lessons */}
                                    {activeTab === "self-study" &&
                                        lesson.status === "completed" && (
                                            <button
                                                className="btn btn-sm mt-2 w-100"
                                                style={{
                                                    backgroundColor:
                                                        lesson.status ===
                                                        "passed"
                                                            ? "#10b981"
                                                            : "#ef4444",
                                                    color: "white",
                                                    border: "none",
                                                    padding: "0.375rem 0.75rem",
                                                    fontSize: "0.75rem",
                                                    fontWeight: "600",
                                                    borderRadius: "0.25rem",
                                                    transition: "all 0.2s",
                                                }}
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    console.log(
                                                        "Review lesson:",
                                                        lesson.id,
                                                        lesson.title,
                                                    );
                                                    // Update selected lesson and show preview screen
                                                    setSelectedLessonId(
                                                        lesson.id,
                                                    );
                                                    setPreviewLessonId(
                                                        lesson.id,
                                                    );
                                                    setViewMode("preview");
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.backgroundColor =
                                                        "#229954";
                                                    e.currentTarget.style.transform =
                                                        "translateY(-2px)";
                                                    e.currentTarget.style.boxShadow =
                                                        "0 4px 8px rgba(0,0,0,0.2)";
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.backgroundColor =
                                                        "#27ae60";
                                                    e.currentTarget.style.transform =
                                                        "translateY(0)";
                                                    e.currentTarget.style.boxShadow =
                                                        "none";
                                                }}
                                            >
                                                <i className="fas fa-redo me-1"></i>
                                                Review Lesson
                                            </button>
                                        )}
                                </div>
                            </div>
                        </div>
                    );
                })
            )}
        </div>
    );
};

export default LessonListSB;
