import React, { useState } from "react";
import { LessonsData, LessonProgressData } from "../types/LaravelProps";

const StudentSidebar = ({
    instructor,
    classroomStatus,
    lessons,
    hasLessons = false,
    isOnline = false,
    selectedCourseAuthId = null,
    studentAttendance = null,
}: {
    instructor: { fname: string; lname: string } | null;
    classroomStatus: string;
    lessons?: LessonsData;
    hasLessons?: boolean;
    isOnline?: boolean;
    selectedCourseAuthId?: number | null;
    studentAttendance?: {
        is_present: boolean;
        entry_time: string | null;
        entry_time_relative: string | null;
        attendance_status: string;
        session_duration?: {
            formatted: string;
        };
    } | null;
}) => {
    const [isCollapsed, setIsCollapsed] = useState(false);

    const toggleCollapse = () => {
        setIsCollapsed(!isCollapsed);
    };

    const handleLessonClick = (lesson: LessonProgressData) => {
        // TODO: Implement lesson navigation
        console.log("Opening lesson:", lesson.title, "ID:", lesson.id);
        // This would typically navigate to the lesson page or open a modal
        // For now, we'll just log it
    };

    // Filter lessons to only show the selected course if in course dashboard
    const filteredLessons = React.useMemo(() => {
        if (!lessons || !hasLessons) return {};

        // If we have a selected course auth ID, filter to only that course
        if (selectedCourseAuthId) {
            const courseAuthKey = selectedCourseAuthId.toString();
            if (lessons[courseAuthKey]) {
                return { [courseAuthKey]: lessons[courseAuthKey] };
            }
            return {}; // Selected course not found
        }

        // If no specific course selected, show all lessons
        return lessons;
    }, [lessons, selectedCourseAuthId, hasLessons]);

    // Debug logging
    console.log("🎯 StudentSidebar props:", {
        hasLessons,
        lessons,
        lessonsKeys: lessons ? Object.keys(lessons) : [],
        selectedCourseAuthId,
        filteredLessons,
        filteredLessonsKeys: Object.keys(filteredLessons),
        isOnline,
        classroomStatus,
    });
    return (
        <div
            className="dashboard-side bg-dark thin-scrollbar"
            style={{
                width: isCollapsed ? "60px" : "300px",
                minHeight: "100%",
                overflowY: "auto",
                margin: 0,
                padding: 0,
                transition: "width 0.3s ease",
            }}
        >
            {/* Toggle Button */}
            <div className="p-2 text-white border-bottom border-secondary d-flex justify-content-between align-items-center">
                {!isCollapsed && (
                    <h5 className="mb-0">
                        <i className="fas fa-user-check me-2"></i>
                        Session Status
                    </h5>
                )}
                <button
                    type="button"
                    className="btn btn-sm btn-outline-light d-flex align-items-center justify-content-center"
                    onClick={toggleCollapse}
                    style={{ width: "32px", height: "32px" }}
                    title={isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"}
                    aria-label={
                        isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"
                    }
                >
                    <i
                        className={`fas ${
                            isCollapsed ? "fa-chevron-right" : "fa-chevron-left"
                        }`}
                        aria-hidden="true"
                    />
                </button>
            </div>

            {!isCollapsed && (
                <>
                    {/* Student Entry Time */}
                    <div className="px-3 py-2 text-white">
                        <div className="d-flex align-items-center justify-content-between mb-2">
                            <small className="text-muted text-uppercase">
                                Class Session:
                            </small>
                            <span
                                className={`badge ${
                                    studentAttendance?.is_present
                                        ? "bg-success"
                                        : studentAttendance?.attendance_status ===
                                          "left"
                                        ? "bg-warning"
                                        : "bg-secondary"
                                }`}
                            >
                                {studentAttendance?.is_present
                                    ? "In Session"
                                    : studentAttendance?.attendance_status ===
                                      "left"
                                    ? "Left Session"
                                    : "Not in Session"}
                            </span>
                        </div>
                        <div className="d-flex align-items-center">
                            <i className="fas fa-clock me-2 text-info"></i>
                            <span className="fw-bold">
                                {studentAttendance?.entry_time || "--:-- --"}
                            </span>
                        </div>
                        <small className="text-muted">
                            {studentAttendance?.entry_time_relative ||
                                "No active class session"}
                        </small>
                        {studentAttendance?.session_duration && (
                            <div className="mt-1">
                                <small className="text-info">
                                    <i className="fas fa-stopwatch me-1"></i>
                                    Session:{" "}
                                    {
                                        studentAttendance.session_duration
                                            .formatted
                                    }
                                </small>
                            </div>
                        )}
                    </div>

                    {/* System Status */}
                    <div className="px-3 py-2 text-white border-top border-secondary">
                        <small className="text-muted text-uppercase d-block mb-2">
                            System Status:
                        </small>
                        <div className="d-flex align-items-center justify-content-between">
                            <div>
                                <div className="d-flex align-items-center mb-1">
                                    <i
                                        className="fas fa-circle me-2 text-success"
                                        style={{ fontSize: "8px" }}
                                    ></i>
                                    <small>Online</small>
                                </div>
                                <div className="d-flex align-items-center">
                                    <i
                                        className={`fas fa-circle me-2 ${
                                            studentAttendance?.is_present
                                                ? "text-success"
                                                : "text-secondary"
                                        }`}
                                        style={{ fontSize: "8px" }}
                                    ></i>
                                    <small>
                                        {studentAttendance?.is_present
                                            ? "In Class Session"
                                            : "Not in Class Session"}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </>
            )}

            {!isCollapsed && (
                <div className="p-3">
                    <div className="text-white">
                        <h6 className="text-uppercase small text-muted mb-3">
                            Classroom Status
                        </h6>
                        <div className="d-flex align-items-center">
                            <span
                                className={`badge me-2 ${
                                    classroomStatus === "active"
                                        ? "bg-success"
                                        : "bg-secondary"
                                }`}
                            >
                                {classroomStatus === "active"
                                    ? "ACTIVE"
                                    : "INACTIVE"}
                            </span>
                            <small className="text-muted">
                                {classroomStatus === "active"
                                    ? "Live session available"
                                    : "No live session"}
                            </small>
                        </div>
                    </div>
                </div>
            )}

            {/* Course Lessons Section - Dynamic Data */}
            {!isCollapsed &&
                hasLessons &&
                Object.keys(filteredLessons).length > 0 && (
                    <div className="border-top border-secondary">
                        <div className="p-0">
                            {/* Dynamic lessons from real course data */}
                            {Object.entries(filteredLessons).map(
                                ([courseAuthId, courseData]) => {
                                    const lessonsToShow =
                                        isOnline && courseData.current_day_only
                                            ? courseData.lessons.filter(
                                                  (lesson) =>
                                                      lesson.is_completed ===
                                                      false // Show incomplete lessons for current day
                                              )
                                            : courseData.lessons;

                                    return (
                                        <div key={courseAuthId}>
                                            {/* Course Title Header */}
                                            {Object.keys(filteredLessons)
                                                .length > 1 && (
                                                <div className="bg-dark p-2 border-bottom border-secondary">
                                                    <small className="text-info fw-bold">
                                                        <i className="fas fa-book me-1"></i>
                                                        {
                                                            courseData.course_title
                                                        }
                                                    </small>
                                                </div>
                                            )}

                                            {/* Lessons List */}
                                            {lessonsToShow.map(
                                                (lesson, index) => (
                                                    <div
                                                        key={lesson.id}
                                                        className={`lesson-item ${
                                                            lesson.is_completed
                                                                ? "bg-success bg-opacity-20"
                                                                : "bg-secondary bg-opacity-20"
                                                        } ${
                                                            index <
                                                            lessonsToShow.length -
                                                                1
                                                                ? "border-bottom border-secondary"
                                                                : ""
                                                        }`}
                                                        style={{
                                                            margin: "0",
                                                            borderRadius: "0",
                                                        }}
                                                    >
                                                        <div className="p-3">
                                                            <div className="d-flex justify-content-between align-items-start">
                                                                <div className="flex-grow-1">
                                                                    <h6 className="mb-1 fw-bold text-white">
                                                                        {
                                                                            lesson.title
                                                                        }
                                                                    </h6>
                                                                    <div className="d-flex flex-wrap gap-2">
                                                                        <small className="text-muted">
                                                                            Credit
                                                                            Minutes:{" "}
                                                                            {
                                                                                lesson.credit_minutes
                                                                            }
                                                                        </small>
                                                                        {lesson.video_seconds >
                                                                            0 && (
                                                                            <small className="text-muted">
                                                                                •
                                                                                Video:{" "}
                                                                                {Math.round(
                                                                                    lesson.video_seconds /
                                                                                        60
                                                                                )}
                                                                                min
                                                                            </small>
                                                                        )}
                                                                        <small className="text-muted">
                                                                            •
                                                                            Unit:{" "}
                                                                            {
                                                                                lesson.unit_title
                                                                            }
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                                <div className="d-flex align-items-center gap-2">
                                                                    {lesson.is_completed && (
                                                                        <i className="fas fa-check-circle text-success"></i>
                                                                    )}
                                                                    <button
                                                                        className={`btn btn-sm ms-2 ${
                                                                            lesson.is_completed
                                                                                ? "btn-outline-success"
                                                                                : "btn-outline-info"
                                                                        }`}
                                                                        style={{
                                                                            minWidth:
                                                                                "60px",
                                                                        }}
                                                                        onClick={() =>
                                                                            handleLessonClick(
                                                                                lesson
                                                                            )
                                                                        }
                                                                    >
                                                                        {lesson.is_completed
                                                                            ? "Review"
                                                                            : "Start"}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                )
                                            )}
                                        </div>
                                    );
                                }
                            )}
                        </div>
                    </div>
                )}

            {/* Fallback: No Lessons Available */}
            {!isCollapsed && !hasLessons && (
                <div className="border-top border-secondary">
                    <div className="p-4 text-center">
                        <div className="text-muted">
                            <i
                                className="fas fa-book-open mb-2"
                                style={{ fontSize: "2rem" }}
                            ></i>
                            <p className="mb-0">No lessons available</p>
                            <small>
                                Course content will appear here when available
                            </small>
                        </div>
                    </div>
                </div>
            )}

            {/* Collapsed state - show lesson initials from dynamic data */}
            {isCollapsed &&
                hasLessons &&
                Object.keys(filteredLessons).length > 0 && (
                    <div className="d-flex flex-column align-items-center py-2">
                        {/* Dynamic lesson initials */}
                        {Object.entries(filteredLessons).flatMap(
                            ([courseAuthId, courseData]) => {
                                const lessonsToShow =
                                    isOnline && courseData.current_day_only
                                        ? courseData.lessons.filter(
                                              (lesson) =>
                                                  lesson.is_completed === false
                                          )
                                        : courseData.lessons;

                                return lessonsToShow.map((lesson, index) => (
                                    <div
                                        key={`${courseAuthId}-${lesson.id}`}
                                        className={`lesson-initial ${
                                            lesson.is_completed
                                                ? "bg-success"
                                                : "bg-secondary"
                                        } text-white text-center ${
                                            index < lessonsToShow.length - 1
                                                ? "mb-1"
                                                : ""
                                        }`}
                                        style={{
                                            width: "32px",
                                            height: "32px",
                                            lineHeight: "32px",
                                            fontSize: "14px",
                                            fontWeight: "bold",
                                            cursor: "pointer",
                                        }}
                                        title={`${lesson.title} (${
                                            lesson.is_completed
                                                ? "Completed"
                                                : "Pending"
                                        })`}
                                        onClick={() =>
                                            handleLessonClick(lesson)
                                        }
                                    >
                                        {lesson.title.charAt(0).toUpperCase()}
                                    </div>
                                ));
                            }
                        )}
                    </div>
                )}

            {/* Collapsed state - no lessons fallback */}
            {isCollapsed && !hasLessons && (
                <div className="d-flex flex-column align-items-center py-3">
                    <div className="text-muted text-center">
                        <i className="fas fa-book-open mb-2"></i>
                        <small className="d-block">No lessons available</small>
                    </div>
                </div>
            )}
        </div>
    );
};

export default StudentSidebar;
