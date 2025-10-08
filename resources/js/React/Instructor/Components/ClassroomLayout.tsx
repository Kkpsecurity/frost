import React, { useState } from 'react';
import { CourseDate } from '../Components/Offline/types';

interface ClassroomLayoutProps {
    course: CourseDate;
    onBackToOverview?: () => void;
}

const ClassroomLayout: React.FC<ClassroomLayoutProps> = ({
    course,
    onBackToOverview
}) => {
    const [selectedLesson, setSelectedLesson] = useState<number | null>(null);
    const [isFullscreen, setIsFullscreen] = useState(false);

    // Mock lesson data - replace with actual data later
    const mockLessons = [
        { id: 1, title: "Introduction to Security", duration: "45 min", status: "completed", order: 1 },
        { id: 2, title: "Risk Assessment", duration: "60 min", status: "current", order: 2 },
        { id: 3, title: "Security Protocols", duration: "30 min", status: "pending", order: 3 },
        { id: 4, title: "Emergency Procedures", duration: "45 min", status: "pending", order: 4 },
        { id: 5, title: "Final Assessment", duration: "30 min", status: "pending", order: 5 }
    ];

    // Mock student data - replace with actual data later
    const mockStudents = [
        { id: 1, name: "John Smith", status: "online", progress: 85, avatar: null },
        { id: 2, name: "Maria Garcia", status: "online", progress: 92, avatar: null },
        { id: 3, name: "David Johnson", status: "away", progress: 78, avatar: null },
        { id: 4, name: "Sarah Wilson", status: "online", progress: 88, avatar: null },
        { id: 5, name: "Michael Brown", status: "offline", progress: 65, avatar: null }
    ];

    const getStatusIcon = (status: string) => {
        switch (status) {
            case "completed":
                return "fas fa-check-circle text-success";
            case "current":
                return "fas fa-play-circle text-primary";
            case "pending":
                return "fas fa-clock text-muted";
            default:
                return "fas fa-circle text-muted";
        }
    };

    const getStudentStatusColor = (status: string) => {
        switch (status) {
            case "online":
                return "var(--frost-success-color, #22c55e)";
            case "away":
                return "var(--frost-warning-color, #f59e0b)";
            case "offline":
                return "var(--frost-secondary-color, #394867)";
            default:
                return "var(--frost-secondary-color, #394867)";
        }
    };

    return (
        <div
            className="classroom-layout"
            style={{ height: "100vh", overflow: "hidden" }}
        >
            {/* Top Header */}
            <div
                className="classroom-header d-flex justify-content-between align-items-center p-3"
                style={{
                    backgroundColor: "var(--frost-primary-color, #212a3e)",
                    color: "var(--frost-white-color, #ffffff)",
                    boxShadow:
                        "var(--frost-shadow-md, 0 4px 6px rgba(0,0,0,0.1))",
                }}
            >
                <div className="d-flex align-items-center">
                    <button
                        className="btn btn-link text-white p-0 me-3"
                        onClick={onBackToOverview}
                        style={{ textDecoration: "none" }}
                    >
                        <i className="fas fa-arrow-left fs-4"></i>
                    </button>
                    <div>
                        <h5 className="mb-0">
                            {course.course_name} - {course.module}
                        </h5>
                        <small className="text-white-50">
                            Instructor: {course.instructor_name || "You"} |
                            Time: {course.time} | Duration: {course.duration}
                        </small>
                    </div>
                </div>
                <div className="d-flex align-items-center gap-2">
                    <button
                        className="btn btn-light btn-sm"
                        onClick={() => setIsFullscreen(!isFullscreen)}
                    >
                        <i
                            className={`fas ${
                                isFullscreen ? "fa-compress" : "fa-expand"
                            }`}
                        ></i>
                    </button>
                    <div className="dropdown">
                        <button
                            className="btn btn-light btn-sm dropdown-toggle"
                            data-bs-toggle="dropdown"
                        >
                            <i className="fas fa-cog"></i>
                        </button>
                        <ul className="dropdown-menu">
                            <li>
                                <a className="dropdown-item" href="#">
                                    <i className="fas fa-volume-up me-2"></i>
                                    Audio Settings
                                </a>
                            </li>
                            <li>
                                <a className="dropdown-item" href="#">
                                    <i className="fas fa-video me-2"></i>Video
                                    Settings
                                </a>
                            </li>
                            <li>
                                <hr className="dropdown-divider" />
                            </li>
                            <li>
                                <a className="dropdown-item" href="#">
                                    <i className="fas fa-save me-2"></i>Save
                                    Session
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Main 3-Column Layout */}
            <div
                className="classroom-content d-flex"
                style={{ height: "calc(100vh - 80px)" }}
            >
                {/* Column 1: Lessons Panel */}
                <div
                    className="lessons-panel"
                    style={{
                        width: "280px",
                        backgroundColor: "var(--frost-light-color, #f8f9fa)",
                        borderRight:
                            "1px solid var(--frost-light-primary-color, #e2e8f0)",
                        overflow: "hidden",
                    }}
                >
                    <div
                        className="p-3 border-bottom"
                        style={{
                            borderColor:
                                "var(--frost-light-primary-color, #e2e8f0)",
                        }}
                    >
                        <h6
                            className="mb-0 d-flex align-items-center"
                            style={{
                                color: "var(--frost-dark-color, #374151)",
                            }}
                        >
                            <i
                                className="fas fa-list-ol me-2"
                                style={{
                                    color: "var(--frost-primary-color, #212a3e)",
                                }}
                            ></i>
                            Lesson Plan ({mockLessons.length} lessons)
                        </h6>
                    </div>
                    <div
                        style={{
                            height: "calc(100% - 60px)",
                            overflowY: "auto",
                        }}
                    >
                        {mockLessons.map((lesson) => (
                            <div
                                key={lesson.id}
                                className={`lesson-item p-3 border-bottom`}
                                style={{
                                    cursor: "pointer",
                                    transition: "background-color 0.2s",
                                    backgroundColor:
                                        selectedLesson === lesson.id
                                            ? "var(--frost-light-primary-color, #e2e8f0)"
                                            : "transparent",
                                    borderColor:
                                        "var(--frost-light-primary-color, #e2e8f0)",
                                }}
                                onClick={() => setSelectedLesson(lesson.id)}
                            >
                                <div className="d-flex align-items-start">
                                    <div className="me-3 mt-1">
                                        <i
                                            className={getStatusIcon(
                                                lesson.status
                                            )}
                                        ></i>
                                    </div>
                                    <div className="flex-grow-1">
                                        <div className="fw-medium">
                                            {lesson.title}
                                        </div>
                                        <small className="text-muted d-flex justify-content-between">
                                            <span>
                                                Duration: {lesson.duration}
                                            </span>
                                            <span
                                                className={`badge bg-${
                                                    lesson.status ===
                                                    "completed"
                                                        ? "success"
                                                        : lesson.status ===
                                                          "current"
                                                        ? "primary"
                                                        : "secondary"
                                                }`}
                                            >
                                                {lesson.status}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Column 2: Teaching Tools Panel */}
                <div
                    className="teaching-tools flex-grow-1"
                    style={{
                        backgroundColor:
                            "var(--frost-secondary-color, #394867)",
                        color: "var(--frost-light-color, #f8f9fa)",
                        display: "flex",
                        flexDirection: "column",
                    }}
                >
                    {/* Tools Header */}
                    <div
                        className="tools-header p-3 border-bottom"
                        style={{
                            borderColor:
                                "var(--frost-light-primary-color, #e2e8f0)",
                        }}
                    >
                        <div className="d-flex justify-content-between align-items-center">
                            <h6
                                className="mb-0"
                                style={{
                                    color: "var(--frost-light-color, #f8f9fa)",
                                }}
                            >
                                <i
                                    className="fas fa-tools me-2"
                                    style={{
                                        color: "var(--frost-light-color, #f8f9fa)",
                                    }}
                                ></i>
                                Teaching Tools
                            </h6>
                            <div className="btn-group btn-group-sm">
                                <button
                                    className="btn active"
                                    style={{
                                        backgroundColor:
                                            "var(--frost-primary-color, #212a3e)",
                                        borderColor:
                                            "var(--frost-primary-color, #212a3e)",
                                        color: "var(--frost-white-color, #ffffff)",
                                    }}
                                >
                                    <i className="fas fa-chalkboard-teacher me-1"></i>
                                    Present
                                </button>
                                <button
                                    className="btn"
                                    style={{
                                        backgroundColor: "transparent",
                                        borderColor:
                                            "var(--frost-light-color, #f8f9fa)",
                                        color: "var(--frost-light-color, #f8f9fa)",
                                    }}
                                >
                                    <i className="fas fa-comments me-1"></i>
                                    Chat
                                </button>
                                <button
                                    className="btn"
                                    style={{
                                        backgroundColor: "transparent",
                                        borderColor:
                                            "var(--frost-light-color, #f8f9fa)",
                                        color: "var(--frost-light-color, #f8f9fa)",
                                    }}
                                >
                                    <i className="fas fa-poll me-1"></i>
                                    Poll
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Main Content Area */}
                    <div className="content-area flex-grow-1 p-4">
                        <div className="text-center h-100 d-flex align-items-center justify-content-center">
                            <div>
                                <div className="mb-4">
                                    <i
                                        className="fas fa-chalkboard fa-4x mb-3"
                                        style={{
                                            color: "var(--frost-base-color, #9ba4b5)",
                                        }}
                                    ></i>
                                    <h4
                                        style={{
                                            color: "var(--frost-light-color, #f8f9fa)",
                                        }}
                                    >
                                        Ready to Teach
                                    </h4>
                                    <p
                                        style={{
                                            color: "var(--frost-light-primary-color, #d6d9e2)",
                                        }}
                                    >
                                        Select a lesson from the left panel to
                                        begin, or use the tools above to
                                        interact with your students.
                                    </p>
                                </div>

                                {/* Quick Action Buttons */}
                                <div className="d-flex justify-content-center gap-3 flex-wrap">
                                    <button
                                        className="btn"
                                        style={{
                                            backgroundColor:
                                                "var(--frost-primary-color, #212a3e)",
                                            borderColor:
                                                "var(--frost-primary-color, #212a3e)",
                                            color: "var(--frost-white-color, #ffffff)",
                                        }}
                                    >
                                        <i className="fas fa-play me-2"></i>
                                        Start Lesson
                                    </button>
                                    <button
                                        className="btn"
                                        style={{
                                            backgroundColor: "transparent",
                                            borderColor:
                                                "var(--frost-info-color, #17aac9)",
                                            color: "var(--frost-info-color, #17aac9)",
                                        }}
                                    >
                                        <i className="fas fa-microphone me-2"></i>
                                        Enable Audio
                                    </button>
                                    <button
                                        className="btn"
                                        style={{
                                            backgroundColor: "transparent",
                                            borderColor:
                                                "var(--frost-info-color, #17aac9)",
                                            color: "var(--frost-info-color, #17aac9)",
                                        }}
                                    >
                                        <i className="fas fa-video me-2"></i>
                                        Enable Video
                                    </button>
                                    <button
                                        className="btn"
                                        style={{
                                            backgroundColor: "transparent",
                                            borderColor:
                                                "var(--frost-light-color, #f8f9fa)",
                                            color: "var(--frost-light-color, #f8f9fa)",
                                        }}
                                    >
                                        <i className="fas fa-screen-share me-2"></i>
                                        Share Screen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Bottom Toolbar */}
                    <div
                        className="bottom-toolbar p-3 border-top"
                        style={{
                            backgroundColor:
                                "var(--frost-light-color, #f8f9fa)",
                            borderColor:
                                "var(--frost-light-primary-color, #e2e8f0)",
                        }}
                    >
                        <div className="d-flex justify-content-between align-items-center">
                            <div className="d-flex gap-2">
                                <button
                                    className="btn btn-sm"
                                    style={{
                                        backgroundColor: "transparent",
                                        borderColor:
                                            "var(--frost-danger-color, #ef4444)",
                                        color: "var(--frost-danger-color, #ef4444)",
                                    }}
                                >
                                    <i className="fas fa-stop me-1"></i>
                                    End Class
                                </button>
                                <button
                                    className="btn btn-sm"
                                    style={{
                                        backgroundColor: "transparent",
                                        borderColor:
                                            "var(--frost-warning-color, #f59e0b)",
                                        color: "var(--frost-warning-color, #f59e0b)",
                                    }}
                                >
                                    <i className="fas fa-pause me-1"></i>
                                    Break
                                </button>
                            </div>
                            <div className="d-flex align-items-center gap-3">
                                <span
                                    className="badge"
                                    style={{
                                        backgroundColor:
                                            "var(--frost-success-color, #22c55e)",
                                        color: "var(--frost-white-color, #ffffff)",
                                    }}
                                >
                                    <i className="fas fa-circle me-1"></i>
                                    Live
                                </span>
                                <small
                                    style={{
                                        color: "var(--frost-base-color, #9ba4b5)",
                                    }}
                                >
                                    Session: 45:23
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Column 3: Students Panel */}
                <div
                    className="students-panel"
                    style={{
                        width: "300px",
                        backgroundColor: "var(--frost-light-color, #f8f9fa)",
                        borderLeft:
                            "1px solid var(--frost-light-primary-color, #e2e8f0)",
                        overflow: "hidden",
                    }}
                >
                    <div
                        className="p-3 border-bottom"
                        style={{
                            borderColor:
                                "var(--frost-light-primary-color, #e2e8f0)",
                        }}
                    >
                        <h6
                            className="mb-0 d-flex align-items-center justify-content-between"
                            style={{
                                color: "var(--frost-dark-color, #374151)",
                            }}
                        >
                            <span>
                                <i
                                    className="fas fa-users me-2"
                                    style={{
                                        color: "var(--frost-primary-color, #212a3e)",
                                    }}
                                ></i>
                                Students ({mockStudents.length})
                            </span>
                            <div className="d-flex gap-1">
                                <span
                                    className="badge"
                                    style={{
                                        backgroundColor:
                                            "var(--frost-success-color, #22c55e)",
                                        color: "var(--frost-white-color, #ffffff)",
                                    }}
                                >
                                    {
                                        mockStudents.filter(
                                            (s) => s.status === "online"
                                        ).length
                                    }
                                </span>
                                <span
                                    className="badge"
                                    style={{
                                        backgroundColor:
                                            "var(--frost-warning-color, #f59e0b)",
                                        color: "var(--frost-white-color, #ffffff)",
                                    }}
                                >
                                    {
                                        mockStudents.filter(
                                            (s) => s.status === "away"
                                        ).length
                                    }
                                </span>
                                <span
                                    className="badge"
                                    style={{
                                        backgroundColor:
                                            "var(--frost-secondary-color, #394867)",
                                        color: "var(--frost-white-color, #ffffff)",
                                    }}
                                >
                                    {
                                        mockStudents.filter(
                                            (s) => s.status === "offline"
                                        ).length
                                    }
                                </span>
                            </div>
                        </h6>
                    </div>
                    <div
                        style={{
                            height: "calc(100% - 60px)",
                            overflowY: "auto",
                        }}
                    >
                        {mockStudents.map((student) => (
                            <div
                                key={student.id}
                                className="student-item p-3 border-bottom"
                                style={{
                                    cursor: "pointer",
                                    borderColor:
                                        "var(--frost-light-primary-color, #e2e8f0)",
                                }}
                            >
                                <div className="d-flex align-items-center">
                                    <div className="position-relative me-3">
                                        <div
                                            className="rounded-circle d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "40px",
                                                height: "40px",
                                                backgroundColor:
                                                    "var(--frost-primary-color, #212a3e)",
                                                color: "var(--frost-white-color, #ffffff)",
                                                fontSize: "14px",
                                                fontWeight: "bold",
                                            }}
                                        >
                                            {student.name
                                                .split(" ")
                                                .map((n) => n[0])
                                                .join("")}
                                        </div>
                                        <span
                                            className="position-absolute bottom-0 end-0 badge rounded-pill"
                                            style={{
                                                fontSize: "8px",
                                                padding: "2px 4px",
                                                backgroundColor:
                                                    getStudentStatusColor(
                                                        student.status
                                                    ),
                                                color: "var(--frost-white-color, #ffffff)",
                                            }}
                                        >
                                            ●
                                        </span>
                                    </div>
                                    <div className="flex-grow-1">
                                        <div
                                            className="fw-medium"
                                            style={{
                                                color: "var(--frost-dark-color, #374151)",
                                            }}
                                        >
                                            {student.name}
                                        </div>
                                        <div className="d-flex justify-content-between align-items-center">
                                            <small
                                                style={{
                                                    color: getStudentStatusColor(
                                                        student.status
                                                    ),
                                                }}
                                            >
                                                {student.status}
                                            </small>
                                            <small
                                                style={{
                                                    color: "var(--frost-base-color, #9ba4b5)",
                                                }}
                                            >
                                                {student.progress}%
                                            </small>
                                        </div>
                                        <div
                                            className="progress mt-1"
                                            style={{
                                                height: "3px",
                                                backgroundColor:
                                                    "var(--frost-light-primary-color, #e2e8f0)",
                                            }}
                                        >
                                            <div
                                                className="progress-bar"
                                                style={{
                                                    width: `${student.progress}%`,
                                                    backgroundColor:
                                                        "var(--frost-success-color, #22c55e)",
                                                }}
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ClassroomLayout;
