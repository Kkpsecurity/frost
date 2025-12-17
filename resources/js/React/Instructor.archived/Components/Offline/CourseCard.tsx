import React, { useState } from "react";
import { CourseDate } from "./types";
import { classroomSessionAPI } from "../../Api/classroom/classroomSessionAPI";

interface CourseCardProps {
    course: CourseDate;
    onCourseSelect?: (course: CourseDate) => void;
    onStartClass?: (course: CourseDate) => void;
    onAssistClass?: (course: CourseDate) => void;
    onRefreshData?: () => void;
    onDeleteCourse?: (course: CourseDate) => void;
}

const STATUS_META: Record<
    NonNullable<CourseDate["class_status"]> | "unassigned",
    { label: string; rail: string; chip: string }
> = {
    unassigned: {
        label: "UNASSIGNED",
        rail: "bg-warning",
        chip: "badge bg-warning-subtle text-warning-emphasis border border-warning",
    },
    assigned: {
        label: "ASSIGNED",
        rail: "bg-info",
        chip: "badge bg-info-subtle text-info-emphasis border border-info",
    },
    in_progress: {
        label: "IN PROGRESS",
        rail: "bg-success",
        chip: "badge bg-success-subtle text-success-emphasis border border-success",
    },
    completed: {
        label: "COMPLETED",
        rail: "bg-secondary",
        chip: "badge bg-secondary-subtle text-secondary-emphasis border border-secondary",
    },
};

const CourseCard: React.FC<CourseCardProps> = ({
    course,
    onCourseSelect,
    onStartClass,
    onAssistClass,
    onRefreshData,
    onDeleteCourse,
}) => {
    const [isLoading, setIsLoading] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const statusKey =
        (course.class_status as keyof typeof STATUS_META) || "unassigned";
    const statusMeta = STATUS_META[statusKey] || STATUS_META.unassigned;

    const handleCardClick = () => onCourseSelect?.(course);

    const confirmDelete = async () => {
        setShowDeleteModal(false);
        setIsLoading(true);

        try {
            const response = await fetch(`/admin/course-dates/${course.id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alert(
                    result.message || `Course deleted: ${course.course_name}`
                );
                onRefreshData && setTimeout(onRefreshData, 300);
            } else {
                console.error("Delete failed:", result);
                alert(
                    `Delete failed: ${
                        result.message || "Unknown error"
                    }\n\nStatus: ${response.status}\nResponse: ${JSON.stringify(
                        result
                    )}`
                );
            }
        } catch (error) {
            alert("Error deleting course");
        } finally {
            setIsLoading(false);
        }
    };

    const cancelDelete = () => {
        setShowDeleteModal(false);
    };

    const handleButtonClick = async (action: string, c: CourseDate) => {
        // REMOVED: "assign_instructor" action to prevent accidental auto-assignment
        // Instructors are now only assigned when they click "Start Class"

        if (action === "start_class" || action === "take_control") {
            // Let the parent component handle the start class logic
            // This avoids duplicate API calls and ensures proper view switching
            if (onStartClass) {
                console.log(
                    "🎯 CourseCard: Calling onStartClass for course:",
                    c.id
                );
                onStartClass(c);
            } else {
                console.warn(
                    "🚨 CourseCard: No onStartClass handler provided, falling back to direct API call"
                );
                // Fallback to direct API call if no parent handler
                setIsLoading(true);
                try {
                    const response = await classroomSessionAPI.startSession(
                        c.id
                    );
                    if (!response.success)
                        return alert(`Failed: ${response.message}`);
                    if (response.data) {
                        c.inst_unit = {
                            id: response.data.inst_unit_id,
                            created_by: response.data.instructor.id,
                            created_at: response.data.created_at,
                            completed_at: null,
                            assistant_id: response.data.assistant?.id || null,
                            instructor: response.data.instructor.name,
                            assistant: response.data.assistant?.name || null,
                        };
                        c.instructor_name = response.data.instructor.name;
                        c.assistant_name =
                            response.data.assistant?.name || null;
                        c.class_status = "in_progress";
                    }
                    onRefreshData && setTimeout(onRefreshData, 300);
                } catch (e: any) {
                    alert(`Error starting class: ${e?.message || e}`);
                } finally {
                    setIsLoading(false);
                }
            }
        } else if (action === "assist") {
            // Use the provided onAssistClass callback if available, otherwise fallback to direct API call
            if (onAssistClass) {
                onAssistClass(c);
            } else {
                setIsLoading(true);
                try {
                    const response = await classroomSessionAPI.assistClass(
                        c.id,
                        c.inst_unit?.id
                    );
                    if (!response.success) {
                        return alert(`Failed to assist: ${response.message}`);
                    }

                    // Update the course data with assistant information
                    if (response.data && response.data.assistant) {
                        c.assistant_name = response.data.assistant.name;
                        if (c.inst_unit) {
                            c.inst_unit.assistant_id =
                                response.data.assistant.id;
                            c.inst_unit.assistant =
                                response.data.assistant.name;
                        }
                    }

                    alert(`Successfully joined as assistant: ${c.course_name}`);
                    onRefreshData && setTimeout(onRefreshData, 300);
                } catch (e: any) {
                    alert(`Error assisting class: ${e?.message || e}`);
                } finally {
                    setIsLoading(false);
                }
            }
        } else if (action === "complete") {
            if (!c.inst_unit?.id) return alert("No active session found.");
            // TODO: Implement course completion logic
            // For now, just update the status
            c.class_status = "completed";
            onRefreshData && setTimeout(onRefreshData, 300);
            alert(`Class completed: ${c.course_name}`);
        } else if (action === "delete") {
            setShowDeleteModal(true);
        }
    };

    return (
        <>
            {/* Delete Modal */}
            {showDeleteModal && (
                <div
                    className="modal fade show"
                    style={{ display: "block", zIndex: 9999 }}
                    tabIndex={-1}
                >
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Delete Course</h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={cancelDelete}
                                >
                                    <i
                                        className="fas fa-times"
                                        aria-hidden="true"
                                    ></i>
                                </button>
                            </div>
                            <div className="modal-body">
                                <p>
                                    Are you sure you want to delete{" "}
                                    <strong>{course.course_name}</strong>?
                                </p>
                                <p className="text-danger">
                                    This action cannot be undone.
                                </p>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={cancelDelete}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={confirmDelete}
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <div
                className="card h-100 border"
                onClick={handleCardClick}
                style={{
                    cursor: onCourseSelect ? "pointer" : "default",
                    borderRadius: 8,
                    background: "#cccccc",
                    borderColor: "#e9ecef",
                }}
                aria-label={`${course.course_name} card`}
            >
                {/* Status rail (minimal) */}
                <div
                    className={`position-absolute ${statusMeta.rail}`}
                    style={{ width: 4, height: "100%" }}
                />

                {/* Header */}
                <div
                    className="card-header bg-dark border-bottom pb-3"
                    style={{ marginLeft: "4px" }}
                >
                    <div className="d-flex align-items-start justify-content-between">
                        <div className="pe-2">
                            <div className="d-flex align-items-center gap-2">
                                <i
                                    className="fas fa-graduation-cap text-dark mr-2"
                                    aria-hidden="true"
                                />
                                <h5 className="mb-0 text-dark fw-normal">
                                    {course.course_name}
                                </h5>
                            </div>
                            {course.lesson_name && (
                                <div className="mt-1 small text-primary fw-semibold">
                                    <i
                                        className="fas fa-calendar-day mr-2"
                                        aria-hidden="true"
                                    />
                                    <span>{course.lesson_name}</span>
                                </div>
                            )}
                            <div className="mt-1 small text-muted">
                                <i
                                    className="fas fa-bookmark mr-2"
                                    aria-hidden="true"
                                />
                                <span>{course.module}</span>
                            </div>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                            <span
                                className={`${statusMeta.chip} small`}
                                style={{ borderRadius: 4, fontSize: "0.75rem" }}
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
                                        isLoading
                                            ? "fa-spinner fa-spin"
                                            : "fa-trash"
                                    }`}
                                />
                            </button>
                        </div>
                    </div>
                </div>

                {/* Body */}
                <div className="card-body py-3" style={{ marginLeft: "4px" }}>
                    {/* Stats: lessons / students / start */}
                    <div className="row text-center g-0 mb-3 bg-light rounded p-2">
                        <div className="col-4">
                            <div className="py-1">
                                <div className="fw-semibold h4 text-dark mb-0">
                                    {course.lesson_count || 0}
                                </div>
                                <div className="small text-muted">
                                    Lesson
                                    {(course.lesson_count || 0) === 1
                                        ? ""
                                        : "s"}
                                </div>
                            </div>
                        </div>
                        <div className="col-4 border-start border-end">
                            <div className="py-1">
                                <div className="fw-semibold h4 text-dark mb-0">
                                    {course.student_count || 0}
                                </div>
                                <div className="small text-muted">
                                    Student
                                    {(course.student_count || 0) === 1
                                        ? ""
                                        : "s"}
                                </div>
                            </div>
                        </div>
                        <div className="col-4">
                            <div className="py-1">
                                <div className="fw-semibold h5 text-dark mb-0">
                                    {course.time || "—"}
                                </div>
                                <div className="small text-muted">Start</div>
                            </div>
                        </div>
                    </div>

                    {/* People: Instructor / Assistant */}
                    <div className="d-flex flex-column gap-2">
                        <div className="col-12">
                            <div className="d-flex align-items-center gap-2 p-2 bg-white border rounded">
                                {course.inst_unit && course.instructor_name ? (
                                    <>
                                        <img
                                            src={`https://ui-avatars.com/api/?name=${encodeURIComponent(
                                                course.instructor_name
                                            )}&size=32&background=6c757d&color=ffffff&rounded=true`}
                                            alt=""
                                            width="28"
                                            height="28"
                                            className="rounded-circle mr-2"
                                        />
                                        <div className="d-flex flex-column flex-grow-1">
                                            <span
                                                className="small text-dark text-uppercase fw-bold"
                                                style={{
                                                    fontSize: "0.65rem",
                                                    letterSpacing: "0.5px",
                                                }}
                                            >
                                                INSTRUCTOR
                                            </span>
                                            <span
                                                className="fw-bold"
                                                style={{
                                                    fontSize: "1rem",
                                                    color: "#000000",
                                                    fontWeight: "700",
                                                }}
                                            >
                                                {course.instructor_name}
                                            </span>
                                        </div>
                                    </>
                                ) : (
                                    <>
                                        <div
                                            className="rounded-circle d-flex align-items-center justify-content-center bg-light border"
                                            style={{ width: 28, height: 28 }}
                                        >
                                            <i
                                                className="fas fa-user text-muted"
                                                aria-hidden="true"
                                                style={{ fontSize: "0.7rem" }}
                                            />
                                        </div>
                                        <div className="d-flex flex-column flex-grow-1">
                                            <span
                                                className="small text-muted text-uppercase fw-bold"
                                                style={{
                                                    fontSize: "0.65rem",
                                                    letterSpacing: "0.5px",
                                                }}
                                            >
                                                INSTRUCTOR
                                            </span>
                                            <span className="text-muted small">
                                                Not Assigned
                                            </span>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex align-items-center gap-2 p-2 bg-white border rounded">
                                {course.inst_unit && course.assistant_name ? (
                                    <>
                                        <img
                                            src={`https://ui-avatars.com/api/?name=${encodeURIComponent(
                                                course.assistant_name
                                            )}&size=32&background=6c757d&color=ffffff&rounded=true`}
                                            alt=""
                                            width="28"
                                            height="28"
                                            className="rounded-circle mr-2"
                                        />
                                        <div className="d-flex flex-column flex-grow-1">
                                            <span
                                                className="small text-dark text-uppercase fw-bold"
                                                style={{
                                                    fontSize: "0.65rem",
                                                    letterSpacing: "0.5px",
                                                }}
                                            >
                                                ASSISTANT
                                            </span>
                                            <span
                                                className="fw-bold"
                                                style={{
                                                    fontSize: "1rem",
                                                    color: "#000000",
                                                    fontWeight: "700",
                                                }}
                                            >
                                                {course.assistant_name}
                                            </span>
                                        </div>
                                    </>
                                ) : (
                                    <>
                                        <div
                                            className="rounded-circle d-flex align-items-center justify-content-center bg-light border"
                                            style={{ width: 28, height: 28 }}
                                        >
                                            <i
                                                className="fas fa-user-plus text-muted"
                                                aria-hidden="true"
                                                style={{ fontSize: "0.7rem" }}
                                            />
                                        </div>
                                        <div className="d-flex flex-column flex-grow-1">
                                            <span
                                                className="small text-muted text-uppercase fw-bold"
                                                style={{
                                                    fontSize: "0.65rem",
                                                    letterSpacing: "0.5px",
                                                }}
                                            >
                                                ASSISTANT
                                            </span>
                                            <span className="text-muted small">
                                                TBD
                                            </span>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Debug info for troubleshooting */}
                {process.env.NODE_ENV === "development" && (
                    <div
                        style={{
                            fontSize: "10px",
                            color: "red",
                            padding: "5px",
                        }}
                    >
                        Status: {course.class_status} | Buttons:{" "}
                        {JSON.stringify(course.buttons)} | InstUnit:{" "}
                        {course.inst_unit ? "Yes" : "No"}
                    </div>
                )}

                {/* Footer actions */}
                {course.buttons && Object.keys(course.buttons).length > 0 && (
                    <div
                        className="card-footer bg-white border-top pt-3"
                        style={{ marginLeft: "4px" }}
                    >
                        <div className="d-flex gap-2">
                            {Object.entries(course.buttons).map(
                                ([action, label]) => {
                                    const isStart =
                                        action === "start_class" ||
                                        action === "take_control";
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
                                                handleButtonClick(
                                                    action,
                                                    course
                                                );
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
                                                        className="fas fa-spinner fa-spin me-1"
                                                        aria-hidden="true"
                                                    />
                                                    Processing…
                                                </>
                                            ) : (
                                                <>
                                                    <i
                                                        className={`fas me-1 ${
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
                                }
                            )}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
};

export default CourseCard;
