import React, { useState, useEffect } from "react";

interface StudentDashboardProps {
    studentName?: string;
    currentCourse?: any;
    lessons?: any[];
    resources?: any[];
    assignments?: any[];
}

interface Lesson {
    id: number;
    title: string;
    duration: string;
    status: "completed" | "current" | "available" | "locked";
    type?: string;
    url?: string;
    progress?: number;
}

interface Resource {
    id: number;
    title: string;
    type: string;
    icon: string;
    url: string;
}

interface Assignment {
    id: number;
    title: string;
    dueDate: string;
    priority: "high" | "medium" | "low";
    status: "pending" | "submitted" | "graded";
}

interface Activity {
    id: number;
    title: string;
    description: string;
    type: "video" | "reading" | "interactive";
    progress: number;
    content?: string;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    studentName = "Student",
    currentCourse,
    lessons = [],
    resources = [],
    assignments = [],
}) => {
    const [studentLessons, setStudentLessons] = useState<Lesson[]>(lessons);
    const [studentResources, setStudentResources] =
        useState<Resource[]>(resources);
    const [studentAssignments, setStudentAssignments] =
        useState<Assignment[]>(assignments);
    const [currentActivity, setCurrentActivity] = useState<Activity | null>(
        null
    );
    const [overallProgress, setOverallProgress] = useState(0);
    const [courseData, setCourseData] = useState(currentCourse);

    useEffect(() => {
        // Initialize with sample data if none provided
        if (studentLessons.length === 0) {
            setStudentLessons([
                {
                    id: 1,
                    title: "Introduction to Cybersecurity",
                    duration: "45 min",
                    status: "completed",
                    type: "video",
                    url: "/lessons/1",
                },
                {
                    id: 2,
                    title: "Risk Assessment Fundamentals",
                    duration: "60 min",
                    status: "current",
                    type: "interactive",
                    url: "/lessons/2",
                    progress: 65,
                },
                {
                    id: 3,
                    title: "Threat Analysis",
                    duration: "50 min",
                    status: "available",
                    type: "reading",
                    url: "/lessons/3",
                },
                {
                    id: 4,
                    title: "Security Controls",
                    duration: "40 min",
                    status: "locked",
                    type: "video",
                },
            ]);
        }

        if (studentResources.length === 0) {
            setStudentResources([
                {
                    id: 1,
                    title: "Course Handbook",
                    type: "PDF",
                    icon: "fa-file-pdf",
                    url: "/resources/handbook.pdf",
                },
                {
                    id: 2,
                    title: "Security Glossary",
                    type: "Document",
                    icon: "fa-file-alt",
                    url: "/resources/glossary.pdf",
                },
                {
                    id: 3,
                    title: "Practice Exercises",
                    type: "Interactive",
                    icon: "fa-laptop-code",
                    url: "/resources/exercises",
                },
            ]);
        }

        if (studentAssignments.length === 0) {
            setStudentAssignments([
                {
                    id: 1,
                    title: "Security Assessment Report",
                    dueDate: "Tomorrow",
                    priority: "high",
                    status: "pending",
                },
                {
                    id: 2,
                    title: "Risk Analysis Case Study",
                    dueDate: "Next Week",
                    priority: "medium",
                    status: "pending",
                },
            ]);
        }

        if (!courseData) {
            setCourseData({
                title: "Security Fundamentals",
                code: "SEC-101",
                progress: 45,
                lessons_completed: 3,
                total_lessons: 8,
            });
        }

        // Set current activity based on current lesson
        const currentLesson = studentLessons.find(
            (l) => l.status === "current"
        );
        if (currentLesson) {
            setCurrentActivity({
                id: currentLesson.id,
                title: currentLesson.title,
                description:
                    "Learn the fundamentals of cybersecurity and best practices.",
                type:
                    (currentLesson.type as
                        | "video"
                        | "reading"
                        | "interactive") || "video",
                progress: currentLesson.progress || 0,
            });
        }

        // Calculate overall progress
        const completedLessons = studentLessons.filter(
            (l) => l.status === "completed"
        ).length;
        const totalLessons = studentLessons.length;
        const progress =
            totalLessons > 0
                ? Math.round((completedLessons / totalLessons) * 100)
                : 0;
        setOverallProgress(progress);
    }, [studentLessons, studentResources, studentAssignments, courseData]);

    const handleLessonClick = (lesson: Lesson) => {
        if (lesson.status !== "locked" && lesson.url) {
            // In a real app, this would navigate to the lesson
            console.log(`Navigating to lesson: ${lesson.title}`);

            // Update lesson status to current if it was available
            if (lesson.status === "available") {
                setStudentLessons((prev) =>
                    prev.map((l) =>
                        l.id === lesson.id
                            ? { ...l, status: "current" as const, progress: 10 }
                            : l.status === "current" && l.id !== lesson.id
                            ? {
                                  ...l,
                                  status: "completed" as const,
                                  progress: 100,
                              }
                            : l
                    )
                );

                // Update current activity
                setCurrentActivity({
                    id: lesson.id,
                    title: lesson.title,
                    description:
                        "Continue your learning journey with this lesson.",
                    type:
                        (lesson.type as "video" | "reading" | "interactive") ||
                        "video",
                    progress: 10,
                });
            }
        }
    };

    const handleActivityAction = (actionType: string) => {
        if (currentActivity) {
            // Simulate progress update
            const newProgress = Math.min(currentActivity.progress + 25, 100);
            setCurrentActivity({
                ...currentActivity,
                progress: newProgress,
            });

            // Update lesson progress
            setStudentLessons((prev) =>
                prev.map((l) =>
                    l.id === currentActivity.id
                        ? {
                              ...l,
                              progress: newProgress,
                              status:
                                  newProgress === 100 ? "completed" : "current",
                          }
                        : l
                )
            );
        }
    };

    const renderLessonIcon = (lesson: Lesson) => {
        switch (lesson.status) {
            case "completed":
                return <i className="fas fa-check-circle text-success"></i>;
            case "current":
                return <i className="fas fa-play-circle text-primary"></i>;
            case "available":
                return <i className="far fa-circle text-info"></i>;
            default:
                return <i className="fas fa-lock text-muted"></i>;
        }
    };

    const renderActivityContent = () => {
        if (!currentActivity) {
            return (
                <div className="text-center py-5">
                    <i className="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5>No Active Activity</h5>
                    <p className="text-muted">
                        Select a lesson from the sidebar to get started.
                    </p>
                </div>
            );
        }

        switch (currentActivity.type) {
            case "video":
                return (
                    <div className="video-container">
                        <div className="embed-responsive embed-responsive-16by9">
                            <div className="embed-responsive-item bg-dark d-flex align-items-center justify-content-center rounded">
                                <div className="text-white text-center">
                                    <i className="fas fa-play fa-3x mb-3"></i>
                                    <h5>Video Lesson</h5>
                                    <button
                                        className="btn btn-success btn-lg"
                                        onClick={() =>
                                            handleActivityAction("play")
                                        }
                                    >
                                        <i className="fas fa-play"></i> Play
                                        Video
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case "reading":
                return (
                    <div className="reading-content text-center">
                        <i className="fas fa-book fa-2x text-info mb-3"></i>
                        <h5>Reading Material</h5>
                        <p className="text-muted mb-4">
                            Comprehensive study material to enhance your
                            understanding.
                        </p>
                        <button
                            className="btn btn-info btn-lg"
                            onClick={() => handleActivityAction("read")}
                        >
                            <i className="fas fa-book-open"></i> Open Reading
                        </button>
                    </div>
                );
            default:
                return (
                    <div className="text-center py-5">
                        <i className="fas fa-tasks fa-2x text-primary mb-3"></i>
                        <h5>Interactive Activity</h5>
                        <p className="text-muted mb-4">
                            Complete the interactive exercises to progress.
                        </p>
                        <button
                            className="btn btn-primary btn-lg"
                            onClick={() => handleActivityAction("interact")}
                        >
                            <i className="fas fa-arrow-right"></i> Start
                            Activity
                        </button>
                    </div>
                );
        }
    };

    return (
        <div className="student-dashboard">
            {/* Title Bar */}
            <div className="title-bar bg-primary text-white mb-4">
                <div className="container">
                    <div className="row align-items-center py-3">
                        <div className="col-md-6">
                            <h4 className="mb-0">
                                <i className="fas fa-graduation-cap"></i>
                                Student Dashboard
                            </h4>
                            <small className="opacity-75">
                                Welcome back, {studentName}
                            </small>
                        </div>
                        <div className="col-md-6 text-md-right">
                            <div className="d-flex justify-content-md-end align-items-center">
                                <div className="mr-3">
                                    <small className="opacity-75">
                                        Progress:
                                    </small>
                                    <br />
                                    <small>{overallProgress}% Complete</small>
                                </div>
                                <div className="dropdown">
                                    <button
                                        className="btn btn-outline-light btn-sm dropdown-toggle"
                                        data-toggle="dropdown"
                                    >
                                        <i className="fas fa-user-circle"></i>
                                    </button>
                                    <div className="dropdown-menu dropdown-menu-right">
                                        <a className="dropdown-item" href="#">
                                            <i className="fas fa-edit"></i> Edit
                                            Profile
                                        </a>
                                        <a className="dropdown-item" href="#">
                                            <i className="fas fa-cog"></i>{" "}
                                            Settings
                                        </a>
                                        <div className="dropdown-divider"></div>
                                        <a className="dropdown-item" href="#">
                                            <i className="fas fa-sign-out-alt"></i>{" "}
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container-fluid">
                <div className="row">
                    {/* Left Sidebar - Lessons and Resources */}
                    <div className="col-lg-3">
                        <div
                            className="card sticky-top"
                            style={{ top: "20px" }}
                        >
                            <div className="card-header bg-info text-white">
                                <h6 className="mb-0">
                                    <i className="fas fa-book"></i>
                                    My Courses & Lessons
                                </h6>
                            </div>
                            <div
                                className="card-body p-0"
                                style={{
                                    maxHeight: "600px",
                                    overflowY: "auto",
                                }}
                            >
                                {/* Current Course Progress */}
                                {courseData && (
                                    <div className="p-3 border-bottom bg-light">
                                        <h6 className="text-muted mb-2">
                                            CURRENT COURSE
                                        </h6>
                                        <div className="current-course">
                                            <strong>{courseData.title}</strong>
                                            <br />
                                            <small className="text-muted">
                                                {courseData.code}
                                            </small>
                                            <div className="mt-2">
                                                <div
                                                    className="progress"
                                                    style={{ height: "6px" }}
                                                >
                                                    <div
                                                        className="progress-bar bg-success"
                                                        style={{
                                                            width: `${courseData.progress}%`,
                                                        }}
                                                    ></div>
                                                </div>
                                                <small className="text-muted">
                                                    {courseData.progress}%
                                                    complete •{" "}
                                                    {
                                                        courseData.lessons_completed
                                                    }
                                                    /{courseData.total_lessons}{" "}
                                                    lessons
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Available Lessons */}
                                <div className="lessons-list">
                                    <div className="p-3 border-bottom">
                                        <h6 className="text-muted mb-2">
                                            AVAILABLE LESSONS
                                        </h6>
                                    </div>
                                    {studentLessons.map((lesson) => (
                                        <div
                                            key={lesson.id}
                                            className={`lesson-item p-3 border-bottom ${
                                                lesson.status === "current"
                                                    ? "bg-light"
                                                    : ""
                                            } ${
                                                lesson.status !== "locked"
                                                    ? "clickable"
                                                    : ""
                                            }`}
                                            onClick={() =>
                                                handleLessonClick(lesson)
                                            }
                                        >
                                            <div className="d-flex align-items-center">
                                                <div className="lesson-status mr-2">
                                                    {renderLessonIcon(lesson)}
                                                </div>
                                                <div className="flex-grow-1">
                                                    <div className="lesson-title">
                                                        <strong>
                                                            {lesson.title}
                                                        </strong>
                                                    </div>
                                                    <div className="lesson-meta">
                                                        <small className="text-muted">
                                                            {lesson.duration}
                                                            {lesson.type &&
                                                                ` • ${lesson.type}`}
                                                        </small>
                                                    </div>
                                                    {(lesson.status ===
                                                        "current" ||
                                                        lesson.status ===
                                                            "available") && (
                                                        <div className="mt-1">
                                                            <button className="btn btn-sm btn-outline-primary">
                                                                {lesson.status ===
                                                                "current"
                                                                    ? "Continue"
                                                                    : "Start"}
                                                            </button>
                                                        </div>
                                                    )}
                                                    {lesson.progress !==
                                                        undefined &&
                                                        lesson.progress > 0 && (
                                                            <div className="mt-2">
                                                                <div
                                                                    className="progress"
                                                                    style={{
                                                                        height: "3px",
                                                                    }}
                                                                >
                                                                    <div
                                                                        className="progress-bar bg-primary"
                                                                        style={{
                                                                            width: `${lesson.progress}%`,
                                                                        }}
                                                                    ></div>
                                                                </div>
                                                            </div>
                                                        )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                {/* Resources Section */}
                                <div className="p-3 border-top">
                                    <h6 className="text-muted mb-2">
                                        RESOURCES
                                    </h6>
                                    <div className="list-group list-group-flush">
                                        {studentResources.map((resource) => (
                                            <a
                                                key={resource.id}
                                                href={resource.url}
                                                className="list-group-item list-group-item-action py-2 border-0"
                                            >
                                                <i
                                                    className={`fas ${resource.icon} text-info mr-2`}
                                                ></i>
                                                {resource.title}
                                                <small className="text-muted d-block">
                                                    {resource.type}
                                                </small>
                                            </a>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Content Area */}
                    <div className="col-lg-9">
                        {/* Welcome Message & Quick Stats */}
                        <div className="row mb-4">
                            <div className="col-md-8">
                                <div className="card">
                                    <div className="card-body">
                                        <h5 className="card-title">
                                            <i className="fas fa-home text-primary"></i>
                                            Welcome to Your Learning Journey
                                        </h5>
                                        <p className="card-text">
                                            You're doing great! Keep up the
                                            momentum and continue your progress.
                                        </p>
                                        {currentActivity && (
                                            <div className="alert alert-info">
                                                <i className="fas fa-info-circle"></i>
                                                <strong>Next:</strong>{" "}
                                                {currentActivity.title}
                                                <button
                                                    className="btn btn-sm btn-info ml-2"
                                                    onClick={() =>
                                                        handleActivityAction(
                                                            "start"
                                                        )
                                                    }
                                                >
                                                    Continue Now
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="card bg-gradient-primary text-white">
                                    <div className="card-body text-center">
                                        <h3>{overallProgress}%</h3>
                                        <p className="mb-0">Overall Progress</p>
                                        <div
                                            className="progress mt-2"
                                            style={{
                                                backgroundColor:
                                                    "rgba(255,255,255,0.2)",
                                            }}
                                        >
                                            <div
                                                className="progress-bar bg-white"
                                                style={{
                                                    width: `${overallProgress}%`,
                                                }}
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Class Materials and Activities */}
                        <div className="row">
                            {/* Current Activity/Lesson Content */}
                            <div className="col-lg-8">
                                <div className="card">
                                    <div className="card-header d-flex justify-content-between align-items-center">
                                        <h5 className="mb-0">
                                            <i className="fas fa-play-circle text-success"></i>
                                            Current Activity
                                        </h5>
                                        {currentActivity && (
                                            <span className="badge badge-success">
                                                Active
                                            </span>
                                        )}
                                    </div>
                                    <div className="card-body">
                                        <div className="current-activity">
                                            {currentActivity && (
                                                <>
                                                    <h6>
                                                        {currentActivity.title}
                                                    </h6>
                                                    <p className="text-muted mb-3">
                                                        {
                                                            currentActivity.description
                                                        }
                                                    </p>

                                                    <div className="activity-content bg-light p-4 rounded">
                                                        {renderActivityContent()}
                                                    </div>

                                                    <div className="activity-progress mt-3">
                                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                                            <small className="text-muted">
                                                                Activity
                                                                Progress
                                                            </small>
                                                            <small className="text-muted">
                                                                {
                                                                    currentActivity.progress
                                                                }
                                                                %
                                                            </small>
                                                        </div>
                                                        <div className="progress">
                                                            <div
                                                                className="progress-bar bg-success"
                                                                style={{
                                                                    width: `${currentActivity.progress}%`,
                                                                }}
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </>
                                            )}
                                            {!currentActivity &&
                                                renderActivityContent()}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Sidebar with Additional Info */}
                            <div className="col-lg-4">
                                {/* Upcoming Assignments */}
                                <div className="card mb-3">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-calendar-check text-warning"></i>
                                            Upcoming Assignments
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        {studentAssignments.length === 0 ? (
                                            <div className="text-center py-3">
                                                <i className="fas fa-check-circle text-success fa-2x mb-2"></i>
                                                <p className="text-muted mb-0">
                                                    All caught up!
                                                </p>
                                            </div>
                                        ) : (
                                            studentAssignments.map(
                                                (assignment) => (
                                                    <div
                                                        key={assignment.id}
                                                        className="assignment-item mb-3 p-2 border rounded"
                                                    >
                                                        <div className="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>
                                                                    {
                                                                        assignment.title
                                                                    }
                                                                </strong>
                                                                <br />
                                                                <small className="text-muted">
                                                                    Due:{" "}
                                                                    {
                                                                        assignment.dueDate
                                                                    }
                                                                </small>
                                                            </div>
                                                            <span
                                                                className={`badge badge-${
                                                                    assignment.priority ===
                                                                    "high"
                                                                        ? "danger"
                                                                        : "info"
                                                                }`}
                                                            >
                                                                {
                                                                    assignment.priority
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                )
                                            )
                                        )}
                                    </div>
                                </div>

                                {/* Quick Actions */}
                                <div className="card">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-bolt text-primary"></i>
                                            Quick Actions
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="list-group list-group-flush">
                                            <a
                                                href="#"
                                                className="list-group-item list-group-item-action border-0 px-0"
                                            >
                                                <i className="fas fa-download text-info mr-2"></i>
                                                Download Certificate
                                            </a>
                                            <a
                                                href="#"
                                                className="list-group-item list-group-item-action border-0 px-0"
                                            >
                                                <i className="fas fa-chart-line text-success mr-2"></i>
                                                View Progress Report
                                            </a>
                                            <a
                                                href="#"
                                                className="list-group-item list-group-item-action border-0 px-0"
                                            >
                                                <i className="fas fa-question-circle text-warning mr-2"></i>
                                                Get Help
                                            </a>
                                            <a
                                                href="#"
                                                className="list-group-item list-group-item-action border-0 px-0"
                                            >
                                                <i className="fas fa-comments text-primary mr-2"></i>
                                                Message Instructor
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
