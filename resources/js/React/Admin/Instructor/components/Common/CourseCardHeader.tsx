import React from "react";

interface CourseCardHeaderProps {
    courseName: string;
    unitName?: string;
    courseCode?: string;
    statusMeta: {
        chip: string;
        label: string;
    };
    handleButtonClick: (action: string, course: any) => void;
    course: any;
    isLoading: boolean;
}

const CourseCardHeader: React.FC<CourseCardHeaderProps> = ({
    courseName,
    unitName,
    courseCode,
    statusMeta,
    handleButtonClick,
    course,
    isLoading,
}) => {
    return (
        <div
            className="card-header border-bottom pb-3"
            style={{ marginLeft: "4px" }}
        >
            <div className="d-flex align-items-start justify-content-between">
                <div className="pe-2">
                    <div className="d-flex align-items-center gap-2">
                        <i
                            className="fas fa-graduation-cap text-light mr-2"
                            aria-hidden="true"
                        />
                        <h5 className="mb-0 text-light fw-normal">
                            {courseName}
                        </h5>
                    </div>
                    {unitName && (
                        <div className="mt-1 small text-info fw-semibold">
                            <i
                                className="fas fa-calendar-day mr-2"
                                aria-hidden="true"
                            />
                            <span>{unitName}</span>
                        </div>
                    )}
                    <div className="mt-1 small text-light-50">
                        <i
                            className="fas fa-bookmark mr-2 text-secondary"
                            aria-hidden="true"
                        />
                        <span className="text-secondary">
                            {courseCode || "N/A"}
                        </span>
                    </div>
                </div>
                <div className="d-flex align-items-center gap-2">
                    <span
                        className={`${statusMeta.chip} small`}
                        style={{
                            borderRadius: 4,
                            fontSize: "0.75rem",
                        }}
                    >
                        {statusMeta.label}
                    </span>
                    <button
                        className="btn btn-sm btn-outline-danger m-2"
                        onClick={(e) => {
                            e.stopPropagation();
                            handleButtonClick("delete", course);
                        }}
                        disabled={isLoading}
                        title="Delete Course"
                        style={{
                            padding: "0.25rem 0.5rem",
                            fontSize: "0.7rem",
                            borderRadius: "4px",
                        }}
                    >
                        <i
                            className={`fas ${
                                isLoading ? "fa-spinner fa-spin" : "fa-trash"
                            }`}
                        />
                    </button>
                </div>
            </div>
        </div>
    );
};

export default CourseCardHeader;
