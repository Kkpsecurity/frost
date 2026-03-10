import React from "react";
import { Lesson } from "./LessonsPanelTypes";
import { getLessonIcon, formatDuration } from "./LessonsPanelUtils";

interface LessonItemProps {
    lesson: Lesson;
    index: number;
    completed: boolean;
    active: boolean;
    enabled: boolean;
    actionLoading: boolean;
    isZoomReady: boolean;
    onAction: (path: string, lessonId: number) => void;
}

const LessonItem: React.FC<LessonItemProps> = ({
    lesson,
    index,
    completed,
    active,
    enabled,
    actionLoading,
    isZoomReady,
    onAction,
}) => {
    return (
        <div
            className={`lesson-item ${completed ? "completed" : ""} ${active ? "active" : ""}`}
        >
            <div className="lesson-number">{index + 1}</div>
            <div className="lesson-content">
                <div className="lesson-header">
                    <i className={`fas ${getLessonIcon(lesson.lesson_type)} mr-2`} />
                    <h6 className="lesson-title mb-0">{lesson.title}</h6>
                </div>
                <div className="lesson-meta">
                    <span className="lesson-duration">
                        <i className="far fa-clock me-1" />
                        {formatDuration(lesson.duration_minutes)}
                    </span>
                    {completed && (
                        <span className="lesson-status text-success">
                            <i className="fas fa-check-circle me-1" />
                            Completed
                        </span>
                    )}
                    {active && !completed && (
                        <span className="lesson-status text-primary">
                            <i className="fas fa-play-circle me-1" />
                            In Progress
                        </span>
                    )}
                    {!active && !completed && !enabled && (
                        <span className="lesson-status text-muted">
                            <i className="fas fa-lock me-1" />
                            Locked
                        </span>
                    )}
                </div>
                {!completed && !active && (
                    <button
                        className="btn btn-sm btn-primary btn-start-lesson mt-2"
                        disabled={!enabled}
                        title={
                            !isZoomReady
                                ? "Setup Zoom first"
                                : !enabled
                                    ? "Complete previous lesson first"
                                    : "Start this lesson"
                        }
                        onClick={() =>
                            onAction("/admin/instructors/lessons/start", lesson.id)
                        }
                    >
                        <i className="fas fa-play me-2" />
                        Start Lesson
                    </button>
                )}
                {active && !completed && (
                    <div className="w-100 mt-2">
                        <button
                            className="btn btn-sm btn-success w-100"
                            title="Mark lesson as complete"
                            disabled={actionLoading}
                            onClick={() =>
                                onAction("/admin/instructors/lessons/complete", lesson.id)
                            }
                        >
                            <i className="fas fa-check me-1" />
                            Complete
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
};

export default LessonItem;
