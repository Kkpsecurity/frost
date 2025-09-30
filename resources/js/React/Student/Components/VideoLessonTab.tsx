import React, { useState } from "react";

interface VideoLessonTabProps {
    selectedLessonId?: number | null;
    poolMinutes?: number;
    maxPoolMinutes?: number;
}

const VideoLessonTab: React.FC<VideoLessonTabProps> = ({
    selectedLessonId = null,
    poolMinutes = 438, // 7.3 hours
    maxPoolMinutes = 600, // 10 hours
}) => {
    // Sample lesson data - this would come from props in real implementation
    const [currentLessonId, setCurrentLessonId] = useState<number | null>(selectedLessonId || 2);

    const sampleLessons = {
        1: {
            id: 1,
            title: "Introduction to Security",
            description: "Learn the fundamental concepts of cybersecurity and threat landscape",
            duration: 15,
            status: 'passed' as const,
            progress: 100,
            objectives: [
                "Understand basic security principles",
                "Identify common threats",
                "Learn security terminology",
                "Recognize security best practices"
            ]
        },
        2: {
            id: 2,
            title: "Network Fundamentals",
            description: "Learn the basics of network security and protocols",
            duration: 22,
            status: 'in_progress' as const,
            progress: 67,
            lastWatched: "14:45 of 22:00",
            objectives: [
                "Understand basic network topologies",
                "Learn about TCP/IP protocols",
                "Identify common network vulnerabilities",
                "Implement basic security measures"
            ]
        },
        3: {
            id: 3,
            title: "Security Protocols",
            description: "Deep dive into security protocols and encryption methods",
            duration: 18,
            status: 'not_started' as const,
            objectives: [
                "Learn encryption algorithms",
                "Understand SSL/TLS",
                "Study authentication protocols",
                "Implement secure communications"
            ]
        }
    };

    const selectedLesson = currentLessonId ? sampleLessons[currentLessonId as keyof typeof sampleLessons] : null;
    const poolPercentage = (poolMinutes / maxPoolMinutes) * 100;

    // Listen for lesson selection from sidebar (this would be implemented via props or context)
    React.useEffect(() => {
        if (selectedLessonId && selectedLessonId !== currentLessonId) {
            setCurrentLessonId(selectedLessonId);
        }
    }, [selectedLessonId, currentLessonId]);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'passed':
                return <span className="badge bg-success">Passed</span>;
            case 'in_progress':
                return <span className="badge bg-warning">Continue</span>;
            case 'not_started':
                return <span className="badge bg-secondary">Start</span>;
            case 'locked':
                return <span className="badge bg-secondary">Locked</span>;
            default:
                return <span className="badge bg-secondary">Unknown</span>;
        }
    };

    const getStatusIndicator = (status: string) => {
        let background = "var(--frost-muted-color)";
        let border = "2px solid var(--frost-border-color)";

        switch (status) {
            case 'passed':
                background = "var(--frost-success-color)";
                border = "none";
                break;
            case 'in_progress':
                background = "var(--frost-warning-color)";
                border = "none";
                break;
            case 'not_started':
            case 'locked':
                background = "var(--frost-muted-color)";
                border = "2px solid var(--frost-border-color)";
                break;
        }

        return { background, border };
    };

    const handleLessonAction = (lesson: typeof selectedLesson, action: string) => {
        if (!lesson) return;
        const baseUrl = '/classroom/video-lessons';

        switch (action) {
            case 'review':
                // Navigate to lesson review page
                window.location.href = `${baseUrl}/${lesson.id}?mode=review`;
                break;
            case 'continue':
                // Continue existing session
                window.location.href = `${baseUrl}/${lesson.id}?mode=continue`;
                break;
            case 'begin':
                // Start new lesson with onboarding
                window.location.href = `${baseUrl}/${lesson.id}?mode=start`;
                break;
            case 'restart':
                // Restart lesson from beginning
                if (confirm('Are you sure you want to restart this lesson? Your current progress will be lost.')) {
                    window.location.href = `${baseUrl}/${lesson.id}?mode=restart`;
                }
                break;
            default:
                console.log('Unknown action:', action);
        }
    };

    const getActionButton = (lesson: typeof selectedLesson) => {
        if (!lesson) return null;
        switch (lesson.status) {
            case 'passed':
                return (
                    <button
                        className="btn btn-outline-primary w-100 mb-2"
                        onClick={() => handleLessonAction(lesson, 'review')}
                    >
                        <i className="fas fa-eye me-2"></i>
                        Review Lesson
                    </button>
                );
            case 'in_progress':
                return (
                    <button
                        className="btn btn-primary btn-lg w-100 mb-2"
                        onClick={() => handleLessonAction(lesson, 'continue')}
                    >
                        <i className="fas fa-play me-2"></i>
                        Continue Lesson
                    </button>
                );
            case 'not_started':
                return (
                    <button
                        className="btn btn-primary btn-lg w-100 mb-2"
                        onClick={() => handleLessonAction(lesson, 'begin')}
                    >
                        <i className="fas fa-play me-2"></i>
                        Begin Lesson
                    </button>
                );
            case 'locked':
                return (
                    <button className="btn btn-secondary btn-lg w-100 mb-2" disabled>
                        <i className="fas fa-lock me-2"></i>
                        Locked
                    </button>
                );
            default:
                return null;
        }
    };

    return (
        <div className="h-100">
            {/* Video Lessons Overview */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 className="mb-1">
                                <i className="fas fa-play-circle me-2 text-primary"></i>
                                Video Lessons
                            </h4>
                            <p className="text-muted mb-0">
                                Self-paced video lessons using your makeup time pool
                            </p>
                        </div>
                        <div className="text-end">
                            <div className="pool-status-mini d-flex align-items-center">
                                <i className="fas fa-clock me-2 text-primary"></i>
                                <div>
                                    <small className="fw-semibold d-block">
                                        {(poolMinutes / 60).toFixed(1)} hours remaining
                                    </small>
                                    <small className="text-muted">
                                        of {maxPoolMinutes / 60} hours
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Pool Status Bar */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card border-0" style={{
                        background: "linear-gradient(135deg, rgba(var(--frost-primary-rgb), 0.1), rgba(var(--frost-secondary-rgb), 0.1))",
                        borderRadius: "var(--frost-radius-lg)",
                    }}>
                        <div className="card-body p-3">
                            <div className="d-flex justify-content-between align-items-center mb-2">
                                <small className="fw-semibold">Makeup Time Pool</small>
                                <small className="text-muted">{poolPercentage.toFixed(0)}% remaining</small>
                            </div>
                            <div className="progress" style={{ height: "8px" }}>
                                <div
                                    className="progress-bar bg-primary"
                                    role="progressbar"
                                    style={{ width: `${poolPercentage}%` }}
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content - Video Lessons Dashboard */}
            <div className="row">
                <div className="col-12">
                    <div
                        className="card shadow-sm border-0"
                        style={{
                            background: "var(--frost-white-color)",
                            borderRadius: "var(--frost-radius-lg)",
                        }}
                    >
                        <div className="card-body p-4">
                            {selectedLesson ? (
                                <>
                                    {/* Selected Lesson Header */}
                                    <div className="lesson-header mb-4">
                                        <div className="d-flex align-items-center mb-3">
                                            <div className="lesson-icon me-3" style={{
                                                width: "48px",
                                                height: "48px",
                                                borderRadius: "var(--frost-radius-md)",
                                                background: "linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color))",
                                                display: "flex",
                                                alignItems: "center",
                                                justifyContent: "center",
                                            }}>
                                                <i className="fas fa-play text-white"></i>
                                            </div>
                                            <div className="flex-grow-1">
                                                <h4 className="mb-1">{selectedLesson.title}</h4>
                                                <p className="text-muted mb-0">{selectedLesson.description}</p>
                                            </div>
                                            <div className="lesson-status-badge">
                                                {getStatusBadge(selectedLesson.status)}
                                            </div>
                                        </div>

                                        <div className="lesson-meta d-flex gap-4 mb-3">
                                            <div className="meta-item">
                                                <i className="fas fa-clock text-muted me-1"></i>
                                                <small className="text-muted">{selectedLesson.duration} minutes</small>
                                            </div>
                                            {selectedLesson.progress && (
                                                <div className="meta-item">
                                                    <i className="fas fa-chart-line text-muted me-1"></i>
                                                    <small className="text-muted">{selectedLesson.progress}% Complete</small>
                                                </div>
                                            )}
                                            <div className="meta-item">
                                                <i className="fas fa-users text-muted me-1"></i>
                                                <small className="text-muted">Self-Study</small>
                                            </div>
                                            <div className="meta-item">
                                                <i className="fas fa-minus-circle text-muted me-1"></i>
                                                <small className="text-muted">Costs {selectedLesson.duration} minutes</small>
                                            </div>
                                        </div>

                                        {selectedLesson.progress && (
                                            <div className="progress mb-3" style={{ height: "8px" }}>
                                                <div
                                                    className={`progress-bar ${selectedLesson.status === 'passed' ? 'bg-success' : 'bg-warning'}`}
                                                    role="progressbar"
                                                    style={{ width: `${selectedLesson.progress}%` }}
                                                ></div>
                                            </div>
                                        )}
                                    </div>

                                    {/* Lesson Content */}
                                    <div className="row">
                                        <div className="col-md-8">
                                            <h5 className="mb-3">Lesson Objectives</h5>
                                            <ul className="list-unstyled">
                                                {selectedLesson.objectives.map((objective, index) => (
                                                    <li key={index} className="mb-2 d-flex align-items-start">
                                                        {selectedLesson.progress && index < Math.floor((selectedLesson.progress / 100) * selectedLesson.objectives.length) ? (
                                                            <i className="fas fa-check-circle text-success me-2 mt-1"></i>
                                                        ) : (
                                                            <i className="fas fa-circle text-muted me-2 mt-1"></i>
                                                        )}
                                                        <span>{objective}</span>
                                                    </li>
                                                ))}
                                            </ul>

                                            <div className="mt-4">
                                                <h5 className="mb-3">Description</h5>
                                                <p className="text-muted">{selectedLesson.description}</p>
                                            </div>
                                        </div>

                                        <div className="col-md-4">
                                            {/* Action Panel */}
                                            <div className="action-panel">
                                                <div className="card border-0" style={{
                                                    background: "linear-gradient(135deg, rgba(var(--frost-primary-rgb), 0.05), rgba(var(--frost-secondary-rgb), 0.05))",
                                                    borderRadius: "var(--frost-radius-lg)",
                                                }}>
                                                    <div className="card-body p-4">
                                                        <h6 className="mb-3">Start Learning</h6>

                                                        {selectedLesson.lastWatched && (
                                                            <div className="mb-3">
                                                                <small className="text-muted d-block">Last Progress:</small>
                                                                <strong>{selectedLesson.lastWatched}</strong>
                                                            </div>
                                                        )}

                                                        {getActionButton(selectedLesson)}

                                                        {selectedLesson.status === 'in_progress' && (
                                                            <button
                                                                className="btn btn-outline-secondary w-100 mb-3"
                                                                onClick={() => handleLessonAction(selectedLesson, 'restart')}
                                                            >
                                                                <i className="fas fa-history me-2"></i>
                                                                Start Over
                                                            </button>
                                                        )}

                                                        <div className="requirements-check">
                                                            <small className="text-muted d-block mb-2">Requirements:</small>
                                                            <div className="d-flex align-items-center mb-1">
                                                                <i className="fas fa-check-circle text-success me-2"></i>
                                                                <small>Headshot verification</small>
                                                            </div>
                                                            <div className="d-flex align-items-center mb-1">
                                                                <i className="fas fa-check-circle text-success me-2"></i>
                                                                <small>Terms agreement</small>
                                                            </div>
                                                            <div className="d-flex align-items-center">
                                                                <i className="fas fa-clock text-warning me-2"></i>
                                                                <small>Available pool time</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-5">
                                    <i className="fas fa-play-circle fa-3x text-muted mb-3"></i>
                                    <h5 className="text-muted">Choose a Video Lesson</h5>
                                    <p className="text-muted">
                                        Select a lesson from the sidebar to view details and start learning.<br />
                                        Video lessons consume time from your makeup pool.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default VideoLessonTab;
