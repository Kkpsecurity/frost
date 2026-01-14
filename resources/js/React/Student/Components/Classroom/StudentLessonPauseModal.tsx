import React from 'react';

interface StudentLessonPauseModalProps {
    isVisible: boolean;
    lessonTitle?: string;
    breaksRemaining?: number;
}

/**
 * StudentLessonPauseModal
 *
 * Read-only modal shown to students when instructor pauses the lesson.
 * Students CANNOT control the pause - they just see the status.
 */
const StudentLessonPauseModal: React.FC<StudentLessonPauseModalProps> = ({
    isVisible,
    lessonTitle = 'Lesson',
    breaksRemaining,
}) => {
    if (!isVisible) return null;

    return (
        <>
            {/* Full-Screen Pause Modal - Red Backdrop */}
            <div
                style={{
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                    backgroundColor: 'rgba(220, 38, 38, 0.95)', // Red backdrop
                    zIndex: 9999,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(4px)',
                }}
            >
                <div
                    style={{
                        backgroundColor: '#1f2937',
                        borderRadius: '12px',
                        padding: '40px',
                        maxWidth: '500px',
                        width: '90%',
                        boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
                        border: '2px solid rgba(220, 38, 38, 0.5)',
                    }}
                >
                    <div
                        style={{
                            textAlign: 'center',
                            marginBottom: '30px',
                        }}
                    >
                        <i
                            className="fas fa-pause-circle"
                            style={{
                                fontSize: '64px',
                                color: '#dc2626',
                                marginBottom: '20px',
                                animation: 'pulse 2s infinite',
                            }}
                        />
                        <h2
                            style={{ color: 'white', marginBottom: '10px' }}
                        >
                            Lesson Paused
                        </h2>
                        <p
                            style={{
                                color: 'rgba(255, 255, 255, 0.7)',
                                fontSize: '16px',
                            }}
                        >
                            Your instructor has paused the lesson. Please wait.
                        </p>
                        {lessonTitle && (
                            <p
                                style={{
                                    color: '#3b82f6',
                                    fontSize: '14px',
                                    marginTop: '10px',
                                    fontWeight: '600',
                                }}
                            >
                                <i className="fas fa-book-open me-2" />
                                {lessonTitle}
                            </p>
                        )}
                        {breaksRemaining !== undefined && (
                            <p
                                style={{
                                    color: '#fbbf24',
                                    fontSize: '14px',
                                    marginTop: '10px',
                                }}
                            >
                                <i className="fas fa-info-circle me-2" />
                                {breaksRemaining} break
                                {breaksRemaining !== 1 ? 's' : ''} remaining
                            </p>
                        )}

                        {/* Info Box */}
                        <div
                            style={{
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                border: '1px solid rgba(59, 130, 246, 0.5)',
                                borderRadius: '8px',
                                padding: '16px',
                                marginTop: '20px',
                                color: '#93c5fd',
                                textAlign: 'left',
                            }}
                        >
                            <div style={{ display: 'flex', alignItems: 'start', gap: '12px' }}>
                                <i
                                    className="fas fa-clock"
                                    style={{
                                        fontSize: '20px',
                                        marginTop: '2px',
                                        flexShrink: 0,
                                    }}
                                />
                                <div>
                                    <h6 style={{ color: '#60a5fa', marginBottom: '8px', fontWeight: '600' }}>
                                        Break Time
                                    </h6>
                                    <p style={{ fontSize: '14px', lineHeight: '1.5', margin: 0 }}>
                                        Your instructor is taking a short break. The lesson will resume shortly.
                                        Stay on this page to continue when ready.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Pulse animation */}
            <style>{`
                @keyframes pulse {
                    0%, 100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: 0.5;
                    }
                }
            `}</style>
        </>
    );
};

export default StudentLessonPauseModal;
