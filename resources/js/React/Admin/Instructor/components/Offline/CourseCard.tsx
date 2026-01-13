import React, { useState } from "react";
import { CourseDate } from "../../models";
import DeleteCourseScheduleModal from "../Common/DeleteCourseScheduleModal";
import CourseCardStats from "../Common/CourseCardStats";
import CourseCardActions from "../Common/CourseCardActions";
import CourseCardHeader from "../Common/CourseCardHeader";
import CourseCardInstructorList from "../Common/CourseCardInstructorList";
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

    // Debug: Log the course object to see what we're working with
    console.log("🎴 CourseCard received course object:", course);

    // Extract data directly from payload - matches actual database columns
    const courseName = course.course_name || "Unknown Course";
    const courseCode = course.unit_admin_title || ""; // From course_units.admin_title
    const unitName = course.unit_title || "Unknown Unit"; // From course_units.title
    const instructorAvatar = null;
    const assistantAvatar = null;

    // Use counts directly from payload - computed by service
    const lessonCount = course.lesson_count || 0;
    const studentCount = course.student_count || 0;

    const instructorName =
        course.instructor_name ||
        course.inst_unit?.instructor ||
        "Not Assigned";
    const assistantName =
        course.assistant_name || course.inst_unit?.assistant || "TBD";

    console.log("🎴 CourseCard extracted values:", {
        courseName,
        unitName,
        courseCode,
        lessonCount,
        studentCount,
        instructorName,
        assistantName,
    });

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
                alert(result.message || `Course deleted: ${courseName}`);
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
            console.log(
                "🎯 CourseCard: ASSIST action triggered for course:",
                c.id
            );
            console.log("🎯 onAssistClass callback exists:", !!onAssistClass);

            // Use the provided onAssistClass callback if available, otherwise fallback to direct API call
            if (onAssistClass) {
                console.log("🎯 Using parent onAssistClass callback");
                onAssistClass(c);
            } else {
                console.log("🎯 No parent callback - using direct API call");
                setIsLoading(true);
                try {
                    console.log(
                        "📡 Calling assistClass API for course:",
                        c.id,
                        "instUnit:",
                        c.inst_unit?.id
                    );
                    const response = await classroomSessionAPI.assistClass(
                        c.id,
                        c.inst_unit?.id
                    );
                    console.log("📡 API Response:", response);

                    if (!response.success) {
                        console.error("❌ Assist failed:", response.message);
                        setIsLoading(false);
                        return alert(`Failed to assist: ${response.message}`);
                    }

                    console.log("✅ Assist successful, updating course data");
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

                    // Redirect to instructor classroom - React will detect assistant role
                    // Don't set loading to false - let the redirect happen
                    const redirectUrl = `/admin/instructors`;
                    console.log("🚀 REDIRECTING TO:", redirectUrl);
                    console.log(
                        "🚀 User will see instructor SPA with assistant permissions"
                    );
                    window.location.href = redirectUrl;
                    console.log(
                        "🚀 window.location.href SET - redirect should happen now"
                    );
                } catch (e: any) {
                    console.error("❌ Exception during assist:", e);
                    setIsLoading(false);
                    alert(`Error assisting class: ${e?.message || e}`);
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
                <DeleteCourseScheduleModal
                    courseName={{ course_name: courseName }}
                    confirmDelete={confirmDelete}
                    cancelDelete={cancelDelete}
                />
            )}

            <div
                className="card h-100 border"
                onClick={handleCardClick}
                style={{
                    cursor: onCourseSelect ? "pointer" : "default",
                    borderRadius: 8,
                    background: "#1f2933", // Dark slate for better contrast
                    borderColor: "#34404a",
                }}
                aria-label={`${courseName} card`}
            >
                {/* Status rail (minimal) */}
                <div
                    className={`position-absolute ${statusMeta.rail}`}
                    style={{ width: 4, height: "100%" }}
                />

                {/* Header */}
                <CourseCardHeader
                    courseName={courseName}
                    unitName={unitName}
                    courseCode={courseCode}
                    statusMeta={statusMeta}
                    isLoading={isLoading}
                    handleButtonClick={handleButtonClick}
                    course={course}
                />

                {/* Body */}
                <div className="card-body py-3" style={{ marginLeft: "4px" }}>
                    {/* Stats: lessons / students / start */}
                    <CourseCardStats
                        lessonCount={lessonCount}
                        studentCount={studentCount}
                        time={course.time}
                    />

                    {/* Instructor / Assistant */}
                    <CourseCardInstructorList
                        course={course}
                        instructorName={instructorName}
                        instructorAvatar={instructorAvatar}
                        assistantName={assistantName}
                        assistantAvatar={assistantAvatar}
                    />
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
                <CourseCardActions
                    course={course}
                    isLoading={isLoading}
                    handleButtonClick={handleButtonClick}
                />
            </div>
        </>
    );
};

export default CourseCard;
