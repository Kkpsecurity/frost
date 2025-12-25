import React, { useEffect, useState, useRef } from 'react';

interface PauseModalProps {
    isVisible: boolean;
    pauseDurationMinutes: number;
    pauseLabel: string;
    remainingSeconds: number;
    warningSeconds: number;
    alertSoundPath: string;
    onResume: () => void;
    onTimeExpired: () => void;
}

const PauseModal: React.FC<PauseModalProps> = ({
    isVisible,
    pauseDurationMinutes,
    pauseLabel,
    remainingSeconds,
    warningSeconds,
    alertSoundPath,
    onResume,
    onTimeExpired
}) => {
    const [showWarning, setShowWarning] = useState(false);
    const audioRef = useRef<HTMLAudioElement | null>(null);
    const hasPlayedSound = useRef(false);

    // Initialize audio element
    useEffect(() => {
        audioRef.current = new Audio(alertSoundPath);
        return () => {
            if (audioRef.current) {
                audioRef.current.pause();
                audioRef.current = null;
            }
        };
    }, [alertSoundPath]);

    // Check for warning threshold
    useEffect(() => {
        if (remainingSeconds <= warningSeconds && remainingSeconds > 0) {
            setShowWarning(true);

            // Play sound only once when warning starts
            if (!hasPlayedSound.current && audioRef.current) {
                audioRef.current.play().catch(err => {
                    console.error('Failed to play pause warning sound:', err);
                });
                hasPlayedSound.current = true;
            }
        } else {
            setShowWarning(false);
            if (remainingSeconds > warningSeconds) {
                hasPlayedSound.current = false; // Reset for next warning
            }
        }

        // Auto-resume when time expires
        if (remainingSeconds <= 0) {
            onTimeExpired();
        }
    }, [remainingSeconds, warningSeconds, onTimeExpired]);

    if (!isVisible) return null;

    // Format time as MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    // Calculate progress percentage
    const totalSeconds = pauseDurationMinutes * 60;
    const elapsedSeconds = totalSeconds - remainingSeconds;
    const progressPercentage = (elapsedSeconds / totalSeconds) * 100;

    return (
        <>
            {/* Backdrop */}
            <div
                style={{
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    backdropFilter: 'blur(8px)',
                    zIndex: 9999,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                }}
            >
                {/* Modal */}
                <div
                    style={{
                        backgroundColor: '#2c3e50',
                        borderRadius: '16px',
                        padding: '40px',
                        maxWidth: '500px',
                        width: '90%',
                        boxShadow: '0 20px 60px rgba(0, 0, 0, 0.5)',
                        border: showWarning ? '3px solid #e74c3c' : '3px solid #3498db',
                        animation: showWarning ? 'pulse 1s infinite' : 'none',
                    }}
                >
                    {/* Pause Icon */}
                    <div className="text-center mb-4">
                        <i
                            className="fas fa-pause-circle"
                            style={{
                                fontSize: '80px',
                                color: showWarning ? '#e74c3c' : '#3498db',
                            }}
                        ></i>
                    </div>

                    {/* Title */}
                    <h3 className="text-center mb-2" style={{ color: 'white', fontWeight: '600' }}>
                        Video Paused
                    </h3>

                    {/* Pause Label */}
                    <p className="text-center mb-4" style={{ color: '#95a5a6', fontSize: '1.1rem' }}>
                        {pauseLabel} - {pauseDurationMinutes} minutes
                    </p>

                    {/* Countdown Timer */}
                    <div
                        className="text-center mb-4"
                        style={{
                            backgroundColor: showWarning ? 'rgba(231, 76, 60, 0.2)' : 'rgba(52, 152, 219, 0.2)',
                            borderRadius: '12px',
                            padding: '30px',
                        }}
                    >
                        <div
                            style={{
                                fontSize: '4rem',
                                fontWeight: '700',
                                color: showWarning ? '#e74c3c' : '#3498db',
                                fontFamily: 'monospace',
                            }}
                        >
                            {formatTime(remainingSeconds)}
                        </div>
                        <small style={{ color: '#95a5a6' }}>
                            {showWarning ? 'Pause time ending soon!' : 'Time remaining'}
                        </small>
                    </div>

                    {/* Progress Bar */}
                    <div
                        style={{
                            height: '8px',
                            backgroundColor: '#34495e',
                            borderRadius: '4px',
                            overflow: 'hidden',
                            marginBottom: '20px',
                        }}
                    >
                        <div
                            style={{
                                height: '100%',
                                width: `${progressPercentage}%`,
                                backgroundColor: showWarning ? '#e74c3c' : '#3498db',
                                transition: 'width 1s linear',
                            }}
                        />
                    </div>

                    {/* Warning Message */}
                    {showWarning && (
                        <div
                            className="alert mb-3"
                            style={{
                                backgroundColor: 'rgba(231, 76, 60, 0.2)',
                                border: '1px solid #e74c3c',
                                color: '#e74c3c',
                                borderRadius: '8px',
                                padding: '12px',
                                textAlign: 'center',
                            }}
                        >
                            <i className="fas fa-exclamation-triangle me-2"></i>
                            <strong>Pause time ending in {remainingSeconds} seconds!</strong>
                        </div>
                    )}

                    {/* Info Message */}
                    <div
                        className="text-center mb-4"
                        style={{
                            color: '#95a5a6',
                            fontSize: '0.9rem',
                        }}
                    >
                        <i className="fas fa-info-circle me-2"></i>
                        Video will automatically resume when pause time expires
                    </div>

                    {/* Resume Button */}
                    <button
                        className="btn btn-primary btn-lg w-100"
                        onClick={onResume}
                        style={{
                            padding: '15px',
                            fontSize: '1.1rem',
                            fontWeight: '600',
                        }}
                    >
                        <i className="fas fa-play me-2"></i>
                        Resume Now
                    </button>
                </div>
            </div>

            {/* Warning Animation Styles */}
            <style>{`
                @keyframes pulse {
                    0%, 100% {
                        border-color: #e74c3c;
                    }
                    50% {
                        border-color: #c0392b;
                    }
                }
            `}</style>
        </>
    );
};

export default PauseModal;
