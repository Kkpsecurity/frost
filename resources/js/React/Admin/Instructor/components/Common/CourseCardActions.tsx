import React from "react";
import { CourseDate } from "../../models";

interface CourseCardActionsProps {
    course: CourseDate;
    isLoading: boolean;
    handleButtonClick: (action: string, course: CourseDate) => void;
}

const CourseCardActions: React.FC<CourseCardActionsProps> = ({
    course,
    isLoading,
    handleButtonClick,
}) => {
    if (!course.buttons || Object.keys(course.buttons).length === 0) {
        return null;
    }

    return (
        <div
            className="card-footer bg-white border-top pt-3"
            style={{ marginLeft: "4px" }}
        >
            <div className="d-flex gap-2">
                {Object.entries(course.buttons).map(([action, label]) => {
                    const isStart =
                        action === "start_class" || action === "take_control";
                    const isComplete = action === "complete";
                    const isAssist = action === "assist";
                    const btnClass = isStart
                        ? "btn btn-dark"
                        : isComplete
                        ? "btn btn-success"
                        : isAssist
                        ? "btn btn-outline-secondary"
                        : "btn btn-secondary";

                    return (
                        <button
                            key={action}
                            className={`${btnClass} btn-sm flex-fill`}
                            onClick={(e) => {
                                e.stopPropagation();
                                handleButtonClick(action, course);
                            }}
                            disabled={isLoading}
                            aria-label={`${label} for ${course.course_name}`}
                            style={{
                                borderRadius: "4px",
                                fontWeight: "500",
                            }}
                        >
                            {isLoading ? (
                                <>
                                    <i
                                        className="fas fa-spinner fa-spin mr-1"
                                        aria-hidden="true"
                                    />
                                    Processing…
                                </>
                            ) : (
                                <>
                                    <i
                                        className={`fas mr-1 ${
                                            isStart
                                                ? "fa-play"
                                                : isComplete
                                                ? "fa-check"
                                                : isAssist
                                                ? "fa-hands-helping"
                                                : "fa-info-circle"
                                        }`}
                                        aria-hidden="true"
                                    />
                                    {label}
                                </>
                            )}
                        </button>
                    );
                })}
            </div>
        </div>
    );
};

export default CourseCardActions;
