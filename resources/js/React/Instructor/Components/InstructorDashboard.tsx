import React, { useState, useEffect } from "react";

interface InstructorDashboardProps {
    mode?: "offline" | "online";
    courseData?: any;
    studentData?: any[];
    lessonsData?: any[];
}

interface Lesson {
    id: number;
    title: string;
    duration: string;
    status: "completed" | "current" | "pending";
    progress?: number;
    module: string;
}

interface Student {
    id: number;
    name: string;
    email: string;
    avatar: string;
    status: "online" | "away" | "offline";
    progress: number;
}

interface ChatMessage {
    id: number;
    name: string;
    message: string;
    time: string;
    avatar: string;
}

const InstructorDashboard: React.FC<InstructorDashboardProps> = ({
    mode = "offline",
    courseData,
    studentData = [],
    lessonsData = [],
}) => {
    const [currentMode, setCurrentMode] = useState<"offline" | "online">(mode);
    const [lessons, setLessons] = useState<Lesson[]>(lessonsData);
    const [students, setStudents] = useState<Student[]>(studentData);
    const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
    const [newMessage, setNewMessage] = useState("");
    const [currentLesson, setCurrentLesson] = useState<Lesson | null>(null);
    const [zoomActive, setZoomActive] = useState(false);

    useEffect(() => {
        // Initialize with sample data if none provided
        if (lessons.length === 0) {
            setLessons([
                {
                    id: 1,
                    title: "Introduction to Security",
                    duration: "45 min",
                    status: "completed",
                    progress: 100,
                    module: "Module 1",
                },
                {
                    id: 2,
                    title: "Risk Assessment Fundamentals",
                    duration: "60 min",
                    status: "current",
                    progress: 65,
                    module: "Module 1",
                },
                {
                    id: 3,
                    title: "Threat Analysis",
                    duration: "50 min",
                    status: "pending",
                    progress: 0,
                    module: "Module 2",
                },
            ]);
        }

        if (students.length === 0) {
            setStudents([
                {
                    id: 1,
                    name: "John Doe",
                    email: "john@example.com",
                    avatar: "/images/default-avatar.png",
                    status: "online",
                    progress: 75,
                },
                {
                    id: 2,
                    name: "Jane Smith",
                    email: "jane@example.com",
                    avatar: "/images/default-avatar.png",
                    status: "online",
                    progress: 82,
                },
                {
                    id: 3,
                    name: "Mike Johnson",
                    email: "mike@example.com",
                    avatar: "/images/default-avatar.png",
                    status: "away",
                    progress: 60,
                },
            ]);
        }

        // Set current lesson
        const current = lessons.find((l) => l.status === "current");
        setCurrentLesson(current || null);
    }, [lessons, students]);

    const handleSendMessage = () => {
        if (newMessage.trim()) {
            const message: ChatMessage = {
                id: Date.now(),
                name: "Instructor",
                message: newMessage,
                time: "now",
                avatar: "/images/instructor-avatar.png",
            };
            setChatMessages((prev) => [...prev, message]);
            setNewMessage("");
        }
    };

    const handleStartZoom = () => {
        setZoomActive(true);
        // Initialize Zoom SDK here
    };

    const handleLessonStart = (lessonId: number) => {
        setLessons((prev) =>
            prev.map((lesson) =>
                lesson.id === lessonId
                    ? { ...lesson, status: "current" as const }
                    : lesson.status === "current"
                    ? { ...lesson, status: "completed" as const }
                    : lesson
            )
        );
    };

    if (currentMode === "offline") {
        return (
            <div className="instructor-dashboard offline-mode">
                {/* Offline Mode Dashboard */}
                <div className="dashboard-header mb-4">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 className="h4 mb-1">
                                <i className="fas fa-chalkboard-teacher text-primary"></i>
                                Instructor Dashboard - Offline Mode
                            </h2>
                            <p className="text-muted mb-0">
                                Today's lesson board and class overview
                            </p>
                        </div>
                        <div>
                            <span className="badge badge-secondary badge-lg">
                                <i className="fas fa-wifi-slash"></i> Offline
                                Mode
                            </span>
                            <button
                                className="btn btn-success btn-sm ml-2"
                                onClick={() => setCurrentMode("online")}
                            >
                                <i className="fas fa-video"></i> Go Live
                            </button>
                        </div>
                    </div>
                </div>

                {/* Today's Lessons Board */}
                <div className="row mb-4">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header">
                                <h5 className="card-title mb-0">
                                    <i className="fas fa-calendar-day"></i>
                                    Today's Lessons -{" "}
                                    {new Date().toLocaleDateString("en-US", {
                                        weekday: "long",
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    })}
                                </h5>
                            </div>
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-striped table-hover mb-0">
                                        <thead className="thead-light">
                                            <tr>
                                                <th>Time</th>
                                                <th>Course</th>
                                                <th>Lesson</th>
                                                <th>Students</th>
                                                <th>Status</th>
                                                <th className="text-right">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {lessons.map((lesson, index) => (
                                                <tr key={lesson.id}>
                                                    <td>
                                                        <strong>
                                                            {9 + index}:00 AM
                                                        </strong>
                                                        <br />
                                                        <small className="text-muted">
                                                            {lesson.duration}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>
                                                                Security
                                                                Fundamentals
                                                            </strong>
                                                            <br />
                                                            <small className="text-muted">
                                                                SEC-101
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>
                                                                {lesson.title}
                                                            </strong>
                                                            <br />
                                                            <small className="text-muted">
                                                                {lesson.module}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span className="badge badge-info">
                                                            {15 - index}{" "}
                                                            students
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            className={`badge badge-${
                                                                lesson.status ===
                                                                "completed"
                                                                    ? "success"
                                                                    : lesson.status ===
                                                                      "current"
                                                                    ? "warning"
                                                                    : "secondary"
                                                            }`}
                                                        >
                                                            {lesson.status ===
                                                            "completed"
                                                                ? "Completed"
                                                                : lesson.status ===
                                                                  "current"
                                                                ? "In Progress"
                                                                : "Scheduled"}
                                                        </span>
                                                    </td>
                                                    <td className="text-right">
                                                        <div className="btn-group">
                                                            <button className="btn btn-sm btn-outline-primary">
                                                                <i className="fas fa-eye"></i>
                                                            </button>
                                                            <button className="btn btn-sm btn-outline-info">
                                                                <i className="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Stats and Quick Actions */}
                <div className="row">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="card-title mb-0">
                                    <i className="fas fa-chart-line"></i>
                                    Class Overview
                                </h6>
                            </div>
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-6 mb-3">
                                        <div className="text-center">
                                            <h4 className="text-info">45</h4>
                                            <small className="text-muted">
                                                Total Students
                                            </small>
                                        </div>
                                    </div>
                                    <div className="col-6 mb-3">
                                        <div className="text-center">
                                            <h4 className="text-success">6</h4>
                                            <small className="text-muted">
                                                Active Courses
                                            </small>
                                        </div>
                                    </div>
                                    <div className="col-6">
                                        <div className="text-center">
                                            <h4 className="text-warning">
                                                87%
                                            </h4>
                                            <small className="text-muted">
                                                Completion Rate
                                            </small>
                                        </div>
                                    </div>
                                    <div className="col-6">
                                        <div className="text-center">
                                            <h4 className="text-danger">12</h4>
                                            <small className="text-muted">
                                                Pending Grades
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="card-title mb-0">
                                    <i className="fas fa-bolt"></i>
                                    Quick Actions
                                </h6>
                            </div>
                            <div className="card-body">
                                <div className="list-group list-group-flush">
                                    <button className="list-group-item list-group-item-action border-0 d-flex align-items-center">
                                        <i className="fas fa-plus text-success mr-3"></i>
                                        <div className="text-left">
                                            <strong>Create New Lesson</strong>
                                            <br />
                                            <small className="text-muted">
                                                Add a new lesson to your course
                                            </small>
                                        </div>
                                    </button>
                                    <button className="list-group-item list-group-item-action border-0 d-flex align-items-center">
                                        <i className="fas fa-upload text-info mr-3"></i>
                                        <div className="text-left">
                                            <strong>Upload Resources</strong>
                                            <br />
                                            <small className="text-muted">
                                                Add materials and documents
                                            </small>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // Online Mode Dashboard
    return (
        <div className="instructor-dashboard online-mode">
            <div className="dashboard-header mb-4">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="h4 mb-1">
                            <i className="fas fa-video text-success"></i>
                            Live Class Dashboard
                        </h2>
                        <p className="text-muted mb-0">
                            Managing live online class session
                        </p>
                    </div>
                    <div>
                        <span className="badge badge-success badge-lg pulse-badge">
                            <i className="fas fa-broadcast-tower"></i> LIVE
                        </span>
                        <button
                            className="btn btn-secondary btn-sm ml-2"
                            onClick={() => setCurrentMode("offline")}
                        >
                            <i className="fas fa-stop"></i> Go Offline
                        </button>
                    </div>
                </div>
            </div>

            <div className="row">
                {/* Left Sidebar - Lessons and Resources */}
                <div className="col-lg-3">
                    <div className="card">
                        <div className="card-header bg-primary text-white">
                            <h6 className="mb-0">
                                <i className="fas fa-list"></i>
                                Lessons & Resources
                            </h6>
                        </div>
                        <div
                            className="card-body p-0"
                            style={{ maxHeight: "600px", overflowY: "auto" }}
                        >
                            {/* Current Lesson */}
                            {currentLesson && (
                                <div className="p-3 bg-light border-bottom">
                                    <h6 className="text-muted mb-2">
                                        CURRENT LESSON
                                    </h6>
                                    <div className="current-lesson">
                                        <div className="d-flex align-items-center">
                                            <div className="lesson-indicator bg-success mr-2"></div>
                                            <div>
                                                <strong>
                                                    {currentLesson.title}
                                                </strong>
                                                <br />
                                                <small className="text-muted">
                                                    {currentLesson.module} •{" "}
                                                    {currentLesson.duration}
                                                </small>
                                            </div>
                                        </div>
                                        <div className="mt-2">
                                            <div
                                                className="progress"
                                                style={{ height: "4px" }}
                                            >
                                                <div
                                                    className="progress-bar bg-success"
                                                    style={{
                                                        width: `${
                                                            currentLesson.progress ||
                                                            0
                                                        }%`,
                                                    }}
                                                ></div>
                                            </div>
                                            <small className="text-muted">
                                                {currentLesson.progress || 0}%
                                                complete
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Lesson List */}
                            <div className="lesson-list">
                                {lessons.map((lesson, index) => (
                                    <div
                                        key={lesson.id}
                                        className={`lesson-item p-3 border-bottom ${
                                            lesson.status === "current"
                                                ? "bg-light"
                                                : ""
                                        }`}
                                    >
                                        <div className="d-flex align-items-center justify-content-between">
                                            <div className="d-flex align-items-center">
                                                <div className="lesson-number mr-2">
                                                    {lesson.status ===
                                                    "completed" ? (
                                                        <i className="fas fa-check-circle text-success"></i>
                                                    ) : lesson.status ===
                                                      "current" ? (
                                                        <i className="fas fa-play-circle text-primary"></i>
                                                    ) : (
                                                        <span className="badge badge-secondary">
                                                            {index + 1}
                                                        </span>
                                                    )}
                                                </div>
                                                <div>
                                                    <div className="lesson-title">
                                                        {lesson.title}
                                                    </div>
                                                    <small className="text-muted">
                                                        {lesson.duration}
                                                    </small>
                                                </div>
                                            </div>
                                            {lesson.status !== "completed" && (
                                                <button
                                                    className="btn btn-sm btn-outline-primary"
                                                    onClick={() =>
                                                        handleLessonStart(
                                                            lesson.id
                                                        )
                                                    }
                                                >
                                                    <i className="fas fa-play"></i>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Center Area - Zoom Player and Chat */}
                <div className="col-lg-6">
                    {/* Zoom Player */}
                    <div className="card mb-3">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <h6 className="mb-0">
                                <i className="fas fa-video text-primary"></i>
                                Live Class Session
                            </h6>
                            <div className="class-controls">
                                <button
                                    className={`btn btn-sm ${
                                        zoomActive
                                            ? "btn-warning"
                                            : "btn-success"
                                    }`}
                                    onClick={handleStartZoom}
                                >
                                    <i className="fas fa-video"></i>{" "}
                                    {zoomActive ? "Zoom Active" : "Start Zoom"}
                                </button>
                                <button className="btn btn-sm btn-warning ml-1">
                                    <i className="fas fa-microphone-slash"></i>
                                </button>
                                <button className="btn btn-sm btn-info ml-1">
                                    <i className="fas fa-desktop"></i>
                                </button>
                            </div>
                        </div>
                        <div className="card-body p-0">
                            <div
                                id="zoom-container"
                                style={{
                                    height: "400px",
                                    background: "#000",
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "center",
                                }}
                            >
                                <div className="text-white text-center">
                                    <i className="fas fa-video fa-3x mb-3"></i>
                                    <h5>Zoom Meeting Container</h5>
                                    <p>
                                        {zoomActive
                                            ? "Zoom session is active"
                                            : 'Click "Start Zoom" to begin the live session'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Class Chat */}
                    <div className="card">
                        <div className="card-header">
                            <h6 className="mb-0">
                                <i className="fas fa-comments text-info"></i>
                                Class Chat
                                <span className="badge badge-info ml-2">
                                    {chatMessages.length}
                                </span>
                            </h6>
                        </div>
                        <div className="card-body p-0">
                            <div
                                style={{
                                    height: "250px",
                                    overflowY: "auto",
                                    padding: "15px",
                                }}
                            >
                                {chatMessages.length === 0 ? (
                                    <div className="text-center text-muted py-4">
                                        <i className="fas fa-comment fa-2x mb-2"></i>
                                        <p>No messages yet</p>
                                    </div>
                                ) : (
                                    chatMessages.map((message) => (
                                        <div
                                            key={message.id}
                                            className="chat-message mb-2"
                                        >
                                            <div className="d-flex">
                                                <div className="chat-avatar mr-2">
                                                    <img
                                                        src={message.avatar}
                                                        className="rounded-circle"
                                                        width="32"
                                                        height="32"
                                                        alt="Avatar"
                                                    />
                                                </div>
                                                <div className="chat-content">
                                                    <div className="chat-header">
                                                        <strong>
                                                            {message.name}
                                                        </strong>
                                                        <small className="text-muted ml-2">
                                                            {message.time}
                                                        </small>
                                                    </div>
                                                    <div className="chat-text">
                                                        {message.message}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                            <div className="chat-input border-top p-3">
                                <div className="input-group">
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Type a message..."
                                        value={newMessage}
                                        onChange={(e) =>
                                            setNewMessage(e.target.value)
                                        }
                                        onKeyPress={(e) =>
                                            e.key === "Enter" &&
                                            handleSendMessage()
                                        }
                                    />
                                    <div className="input-group-append">
                                        <button
                                            className="btn btn-primary"
                                            type="button"
                                            onClick={handleSendMessage}
                                        >
                                            <i className="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Sidebar - Students */}
                <div className="col-lg-3">
                    <div className="card">
                        <div className="card-header bg-success text-white">
                            <h6 className="mb-0">
                                <i className="fas fa-users"></i>
                                Students in Class
                                <span className="badge badge-light ml-2">
                                    {students.length}
                                </span>
                            </h6>
                        </div>
                        <div
                            className="card-body p-0"
                            style={{ maxHeight: "600px", overflowY: "auto" }}
                        >
                            {students.map((student) => (
                                <div
                                    key={student.id}
                                    className="student-item p-3 border-bottom"
                                >
                                    <div className="d-flex align-items-center justify-content-between">
                                        <div className="d-flex align-items-center">
                                            <div className="student-avatar mr-3 position-relative">
                                                <img
                                                    src={student.avatar}
                                                    className="rounded-circle"
                                                    width="40"
                                                    height="40"
                                                    alt="Avatar"
                                                />
                                                <div
                                                    className={`student-status ${student.status}`}
                                                ></div>
                                            </div>
                                            <div className="student-info">
                                                <div className="student-name">
                                                    {student.name}
                                                </div>
                                                <small className="text-muted">
                                                    {student.email}
                                                </small>
                                                <div className="student-progress">
                                                    <div
                                                        className="progress"
                                                        style={{
                                                            height: "3px",
                                                        }}
                                                    >
                                                        <div
                                                            className="progress-bar bg-info"
                                                            style={{
                                                                width: `${student.progress}%`,
                                                            }}
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="dropdown">
                                            <button
                                                className="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                            >
                                                <i className="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div className="dropdown-menu">
                                                <a
                                                    className="dropdown-item"
                                                    href="#"
                                                >
                                                    <i className="fas fa-comment"></i>{" "}
                                                    Message
                                                </a>
                                                <a
                                                    className="dropdown-item"
                                                    href="#"
                                                >
                                                    <i className="fas fa-microphone-slash"></i>{" "}
                                                    Mute
                                                </a>
                                                <a
                                                    className="dropdown-item"
                                                    href="#"
                                                >
                                                    <i className="fas fa-eye"></i>{" "}
                                                    View Progress
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className="card-footer">
                            <div className="row">
                                <div className="col-6">
                                    <button className="btn btn-sm btn-outline-info btn-block">
                                        <i className="fas fa-hand-paper"></i>{" "}
                                        Raise Hands
                                    </button>
                                </div>
                                <div className="col-6">
                                    <button className="btn btn-sm btn-outline-warning btn-block">
                                        <i className="fas fa-poll"></i> Quick
                                        Poll
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style jsx>{`
                .pulse-badge {
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.05);
                    }
                    100% {
                        transform: scale(1);
                    }
                }

                .lesson-indicator {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                }

                .lesson-item:hover {
                    background-color: #f8f9fa !important;
                }

                .student-status {
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    border: 2px solid white;
                }

                .student-status.online {
                    background-color: #28a745;
                }

                .student-status.away {
                    background-color: #ffc107;
                }

                .student-status.offline {
                    background-color: #6c757d;
                }

                .chat-message {
                    animation: fadeIn 0.3s ease-in;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .current-lesson {
                    background: white;
                    border-radius: 8px;
                    padding: 15px;
                    border: 2px solid #28a745;
                }
            `}</style>
        </div>
    );
};

export default InstructorDashboard;
