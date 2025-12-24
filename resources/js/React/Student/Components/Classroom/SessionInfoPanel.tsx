import React from 'react';
import { LessonSession } from '../../hooks/useLessonSession';

interface SessionInfoPanelProps {
    session: LessonSession;
    timeRemaining: number;  // minutes
    pauseRemaining: number;  // minutes
    onEndSession?: () => void;  // Optional callback for early termination
}

/**
 * SessionInfoPanel Component
 * 
 * Displays active lesson session information including:
 * - Time remaining until expiration (countdown)
 * - Pause time remaining
 * - Completion progress
 * - Warning alerts when thresholds are crossed
 * 
 * Visual indicators:
 * - Green: Healthy (>50% remaining)
 * - Orange/Yellow: Warning (20-50% remaining)
 * - Red: Critical (<20% remaining)
 * 
 * @example
 * <SessionInfoPanel 
 *   session={currentSession}
 *   timeRemaining={45}
 *   pauseRemaining={15}
 *   onEndSession={() => handleEndSession()}
 * />
 */
export const SessionInfoPanel: React.FC<SessionInfoPanelProps> = ({
    session,
    timeRemaining,
    pauseRemaining,
    onEndSession,
}) => {
    // Calculate percentages for visual indicators
    const pausePercentage = (pauseRemaining / session.totalPauseAllowed) * 100;
    
    // Warning thresholds
    const timeWarning = timeRemaining <= 5;
    const timeCritical = timeRemaining <= 2;
    const pauseWarning = pausePercentage <= 20;
    const pauseLow = pausePercentage <= 50;

    // Progress bar color classes
    const getPauseBarColor = () => {
        if (pausePercentage > 50) return 'bg-success';
        if (pausePercentage > 20) return 'bg-warning';
        return 'bg-danger';
    };

    const getCompletionBarColor = () => {
        if (session.completionPercentage >= 80) return 'bg-success';
        if (session.completionPercentage >= 50) return 'bg-primary';
        return 'bg-info';
    };

    // Format time display
    const formatTime = (minutes: number): string => {
        if (minutes >= 60) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${hours}h ${mins}m`;
        }
        return `${minutes} min`;
    };

    return (
        <div className="card border-info shadow-sm mb-3" style={{ position: 'sticky', top: '10px' }}>
            {/* Header */}
            <div className="card-header bg-info text-white">
                <div className="d-flex align-items-center">
                    <i className="fas fa-video me-2"></i>
                    <div className="flex-grow-1">
                        <strong>Active Session</strong>
                        <div className="small" style={{ opacity: 0.9 }}>
                            {session.lessonTitle}
                        </div>
                    </div>
                    {session.isActive && (
                        <span className="badge bg-success">
                            <i className="fas fa-circle me-1" style={{ fontSize: '0.5rem' }}></i>
                            Live
                        </span>
                    )}
                </div>
            </div>

            <div className="card-body">
                {/* Critical Warning Banner */}
                {(timeCritical || pauseWarning) && (
                    <div className={`alert ${timeCritical ? 'alert-danger' : 'alert-warning'} py-2 px-3 mb-3`}>
                        <i className="fas fa-exclamation-triangle me-2"></i>
                        <strong>
                            {timeCritical && 'Session expiring soon! '}
                            {pauseWarning && 'Pause time almost depleted!'}
                        </strong>
                    </div>
                )}

                {/* Time Remaining */}
                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-dark">
                            <i className="fas fa-clock me-2 text-primary"></i>
                            Time Remaining
                        </span>
                        <span className={`fw-bold ${timeWarning ? 'text-danger' : 'text-primary'}`}>
                            {formatTime(timeRemaining)}
                        </span>
                    </div>
                    {timeWarning && (
                        <div className="small text-muted mb-2">
                            <i className="fas fa-info-circle me-1"></i>
                            Complete your session before time expires
                        </div>
                    )}
                    <div className="progress" style={{ height: '8px' }}>
                        <div 
                            className={`progress-bar ${timeWarning ? 'bg-danger' : 'bg-primary'}`}
                            role="progressbar"
                            style={{ 
                                width: `${Math.max(0, Math.min(100, (timeRemaining / 120) * 100))}%`,
                                transition: 'width 0.3s ease'
                            }}
                            aria-valuenow={timeRemaining}
                            aria-valuemin={0}
                            aria-valuemax={120}
                        ></div>
                    </div>
                </div>

                {/* Pause Time Remaining */}
                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-dark">
                            <i className="fas fa-pause-circle me-2 text-warning"></i>
                            Pause Time
                        </span>
                        <span className={`fw-bold ${pauseWarning ? 'text-danger' : pauseLow ? 'text-warning' : 'text-success'}`}>
                            {pauseRemaining} / {session.totalPauseAllowed} min
                        </span>
                    </div>
                    <div className="progress" style={{ height: '8px' }}>
                        <div 
                            className={`progress-bar ${getPauseBarColor()}`}
                            role="progressbar"
                            style={{ 
                                width: `${pausePercentage}%`,
                                transition: 'width 0.3s ease'
                            }}
                            aria-valuenow={pauseRemaining}
                            aria-valuemin={0}
                            aria-valuemax={session.totalPauseAllowed}
                        ></div>
                    </div>
                    {pauseWarning && (
                        <div className="small text-danger mt-1">
                            <i className="fas fa-exclamation-circle me-1"></i>
                            Low pause time - use sparingly
                        </div>
                    )}
                </div>

                {/* Completion Progress */}
                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-dark">
                            <i className="fas fa-chart-line me-2 text-success"></i>
                            Progress
                        </span>
                        <span className={`fw-bold ${session.completionPercentage >= 80 ? 'text-success' : 'text-primary'}`}>
                            {session.completionPercentage.toFixed(1)}%
                        </span>
                    </div>
                    <div className="progress" style={{ height: '8px' }}>
                        <div 
                            className={`progress-bar ${getCompletionBarColor()}`}
                            role="progressbar"
                            style={{ 
                                width: `${session.completionPercentage}%`,
                                transition: 'width 0.3s ease'
                            }}
                            aria-valuenow={session.completionPercentage}
                            aria-valuemin={0}
                            aria-valuemax={100}
                        ></div>
                    </div>
                    {session.completionPercentage >= 80 && (
                        <div className="small text-success mt-1">
                            <i className="fas fa-check-circle me-1"></i>
                            80% threshold met - lesson credit available
                        </div>
                    )}
                </div>

                {/* Session Stats Grid */}
                <div className="row g-2 mb-3">
                    <div className="col-6">
                        <div className="bg-light rounded p-2 text-center">
                            <div className="small text-muted">Video Length</div>
                            <div className="fw-bold">
                                {formatTime(Math.ceil(session.videoDurationSeconds / 60))}
                            </div>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="bg-light rounded p-2 text-center">
                            <div className="small text-muted">Pause Used</div>
                            <div className="fw-bold">
                                {session.pauseUsed} min
                            </div>
                        </div>
                    </div>
                </div>

                {/* End Session Button (if callback provided) */}
                {onEndSession && (
                    <button 
                        className="btn btn-sm btn-outline-danger w-100"
                        onClick={onEndSession}
                        title="End session early (progress will be lost)"
                    >
                        <i className="fas fa-stop me-2"></i>
                        End Session Early
                    </button>
                )}

                {/* Study Tips */}
                <div className="mt-3 pt-3 border-top">
                    <div className="small text-muted">
                        <i className="fas fa-lightbulb me-2 text-warning"></i>
                        <strong>Tip:</strong> Complete at least 80% to earn credit
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SessionInfoPanel;
