import React, { useState, useEffect, useRef } from 'react';
import ReactPlayer from 'react-player';
import PauseModal from './PauseModal';

interface SecureVideoPlayerProps {
    activeSession: {
        session_id: string;
        lesson_id: number;
        time_remaining_minutes: number;
        pause_remaining_minutes: number;
        completion_percentage: number;
        pause_allocation?: {
            total_minutes: number;
            pauses: Array<{
                duration_minutes: number;
                label: string;
            }>;
            current_pause_index: number;
        };
    };
    lesson: {
        id: number;
        title: string;
        description?: string;
        duration_minutes: number;
    };
    videoUrl: string;
    completionThreshold?: number;
    simulationMode?: boolean;
    simulationSpeed?: number;
    pauseWarningSeconds?: number;
    pauseAlertSound?: string;
    onComplete: () => void;
    onProgress: (data: { playedSeconds: number; percentage: number }) => void;
    onError: (error: string) => void;
}

const SecureVideoPlayer: React.FC<SecureVideoPlayerProps> = ({
    activeSession,
    lesson,
    videoUrl,
    completionThreshold = 80,
    simulationMode = true,
    simulationSpeed = 10,
    pauseWarningSeconds = 30,
    pauseAlertSound = '/sounds/pause-warning.mp3',
    onComplete,
    onProgress,
    onError
}) => {
    const playerRef = useRef<ReactPlayer>(null);

    // Playback state
    const [playing, setPlaying] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);
    const [furthestPointReached, setFurthestPointReached] = useState(0);

    // Pause tracking
    const [pauseStartTime, setPauseStartTime] = useState<number | null>(null);
    const [totalPauseTime, setTotalPauseTime] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const [pauseRemainingSeconds, setPauseRemainingSeconds] = useState(0);
    const [currentPauseIndex, setCurrentPauseIndex] = useState(0);

    // Progress tracking
    const [lastSavedProgress, setLastSavedProgress] = useState(0);
    const progressSaveInterval = 30; // Save progress every 30 seconds

    // Settings - Convert threshold from percentage (0-100) to decimal (0-1)
    const completionThresholdDecimal = completionThreshold / 100;
    const rewindSeconds = 10; // Rewind button skips back 10 seconds

    // Get pause allocation data
    const pauseAllocation = activeSession.pause_allocation || {
        total_minutes: 10,
        pauses: [{ duration_minutes: 10, label: 'Break' }],
        current_pause_index: 0,
    };

    const currentPause = pauseAllocation.pauses[currentPauseIndex] || pauseAllocation.pauses[0];

    // Initialize duration from lesson duration_minutes in simulation mode
    useEffect(() => {
        if (simulationMode && lesson.duration_minutes > 0) {
            const durationInSeconds = lesson.duration_minutes * 60;
            setDuration(durationInSeconds);
        }
    }, [simulationMode, lesson.duration_minutes]);

    // Simulation playback timer
    useEffect(() => {
        if (!simulationMode || !playing || duration === 0) {
            return;
        }

        const interval = setInterval(() => {
            setCurrentTime(prev => {
                const newTime = prev + simulationSpeed; // Advance by simulationSpeed seconds per second

                // Check if reached end
                if (newTime >= duration) {
                    setPlaying(false);
                    return duration;
                }

                // Update furthest point if moving forward
                if (newTime > furthestPointReached) {
                    setFurthestPointReached(newTime);
                }

                return newTime;
            });
        }, 1000); // Update every second

        return () => clearInterval(interval);
    }, [simulationMode, playing, duration, simulationSpeed, furthestPointReached]);

    // Auto-save progress and check completion in simulation mode
    useEffect(() => {
        if (!simulationMode || !playing) {
            return;
        }

        // Save progress every 30 seconds of real time
        const progressInterval = setInterval(() => {
            if (currentTime > lastSavedProgress + progressSaveInterval) {
                const percentage = duration > 0 ? (currentTime / duration) * 100 : 0;
                onProgress({ playedSeconds: currentTime, percentage });
                updateProgress(currentTime, percentage);
                setLastSavedProgress(currentTime);
            }

            // Check for completion
            if (duration > 0) {
                const completionPercentage = currentTime / duration;
                if (completionPercentage >= completionThresholdDecimal) {
                    handleCompletion();
                }
            }
        }, 5000); // Check every 5 seconds

        return () => clearInterval(progressInterval);
    }, [simulationMode, playing, currentTime, duration, lastSavedProgress]);

    // Initialize furthest point from session completion percentage
    useEffect(() => {
        if (duration > 0 && activeSession.completion_percentage > 0) {
            const savedPosition = (activeSession.completion_percentage / 100) * duration;
            setFurthestPointReached(savedPosition);
            setCurrentTime(savedPosition);

            // Seek to saved position (only in real video mode)
            if (!simulationMode && playerRef.current) {
                playerRef.current.seekTo(savedPosition, 'seconds');
            }
        }
    }, [duration, activeSession.completion_percentage, simulationMode]);

    // Handle play/pause
    const handlePlayPause = () => {
        if (playing) {
            // Pausing - show pause modal
            setPlaying(false);
            setIsPaused(true);

            // Set pause duration from current pause allocation
            const pauseDurationSeconds = currentPause.duration_minutes * 60;
            setPauseRemainingSeconds(pauseDurationSeconds);
            setPauseStartTime(Date.now());
        } else {
            // Can only resume if not in pause modal
            if (!isPaused) {
                setPlaying(true);
            }
        }
    };

    // Handle resume from pause modal
    const handleResume = () => {
        if (pauseStartTime) {
            const pauseDuration = (Date.now() - pauseStartTime) / 1000; // Convert to seconds
            setTotalPauseTime(prev => prev + pauseDuration);
            setPauseStartTime(null);

            // Track pause time in backend
            trackPauseTime(Math.floor(pauseDuration));
        }

        setIsPaused(false);
        setPauseRemainingSeconds(0);
        setPlaying(true);

        // Move to next pause if available
        if (currentPauseIndex < pauseAllocation.pauses.length - 1) {
            setCurrentPauseIndex(prev => prev + 1);
        }
    };

    // Handle pause time expired
    const handlePauseTimeExpired = () => {
        // Automatically resume
        handleResume();
    };

    // Countdown timer for pause modal
    useEffect(() => {
        if (!isPaused || pauseRemainingSeconds <= 0) {
            return;
        }

        const interval = setInterval(() => {
            setPauseRemainingSeconds(prev => {
                if (prev <= 1) {
                    clearInterval(interval);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [isPaused, pauseRemainingSeconds]);

    // Handle rewind button
    const handleRewind = () => {
        const newTime = Math.max(0, currentTime - rewindSeconds);
        setCurrentTime(newTime);

        // In real video mode, seek the player
        if (!simulationMode && playerRef.current) {
            playerRef.current.seekTo(newTime, 'seconds');
        }
    };

    // Handle progress updates (called continuously during playback)
    const handleProgress = (state: { played: number; playedSeconds: number; loaded: number; loadedSeconds: number }) => {
        const { playedSeconds } = state;
        setCurrentTime(playedSeconds);

        // Only update furthest point if moving forward
        if (playedSeconds > furthestPointReached) {
            setFurthestPointReached(playedSeconds);
        }

        // Save progress every 30 seconds
        if (playedSeconds - lastSavedProgress >= progressSaveInterval) {
            const percentage = duration > 0 ? (playedSeconds / duration) * 100 : 0;
            onProgress({ playedSeconds, percentage });
            setLastSavedProgress(playedSeconds);

            // Update backend
            updateProgress(playedSeconds, percentage);
        }

        // Check for completion
        if (duration > 0) {
            const completionPercentage = playedSeconds / duration;
            if (completionPercentage >= completionThresholdDecimal) {
                handleCompletion();
            }
        }
    };

    // Handle seek attempts - restrict to rewind only
    const handleSeek = (seconds: number) => {
        // Only allow seeking to positions <= furthestPointReached
        if (seconds <= furthestPointReached) {
            setCurrentTime(seconds);
        } else {
            // Attempted to skip forward - reset to furthest point
            if (playerRef.current) {
                playerRef.current.seekTo(furthestPointReached, 'seconds');
            }
            setCurrentTime(furthestPointReached);

            // Show warning
            onError('You can only rewind to previously watched content. Forward skipping is not allowed.');
        }
    };

    // Handle duration ready
    const handleDuration = (dur: number) => {
        setDuration(dur);
    };

    // Handle completion
    const handleCompletion = () => {
        setPlaying(false);

        // Final progress update
        const finalPercentage = (furthestPointReached / duration) * 100;
        updateProgress(furthestPointReached, finalPercentage);

        // Notify parent
        onComplete();
    };

    // API call to update progress
    const updateProgress = async (playedSeconds: number, percentage: number) => {
        try {
            const response = await fetch('/api/student/lesson-session/progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    session_id: activeSession.session_id,
                    progress_seconds: Math.floor(playedSeconds),
                    completion_percentage: Math.min(100, Math.floor(percentage)),
                }),
            });

            if (!response.ok) {
                console.error('Failed to update progress:', await response.text());
            }
        } catch (error) {
            console.error('Error updating progress:', error);
        }
    };

    // API call to track pause time
    const trackPauseTime = async (pauseSeconds: number) => {
        try {
            const pauseMinutes = pauseSeconds / 60; // Convert to minutes
            const response = await fetch('/classroom/lesson/track-pause', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    session_id: activeSession.session_id,
                    pause_minutes: pauseMinutes,
                }),
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to track pause time:', errorText);
            } else {
                const data = await response.json();
                console.log('✅ Pause time tracked:', data);
            }
        } catch (error) {
            console.error('❌ Error tracking pause time:', error);
        }
    };

    // Format time for display
    const formatTime = (seconds: number): string => {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);

        if (h > 0) {
            return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }
        return `${m}:${s.toString().padStart(2, '0')}`;
    };

    // Calculate progress percentage
    const progressPercentage = duration > 0 ? (currentTime / duration) * 100 : 0;
    const furthestProgressPercentage = duration > 0 ? (furthestPointReached / duration) * 100 : 0;

    return (
        <div className="secure-video-player">
            {/* Video Player Container */}
            <div
                className="video-container mb-3"
                style={{
                    backgroundColor: '#000',
                    borderRadius: '8px',
                    overflow: 'hidden',
                    position: 'relative',
                    paddingTop: '56.25%', // 16:9 aspect ratio
                }}
            >
                {simulationMode ? (
                    // Simulation Mode Display
                    <div
                        style={{
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '100%',
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            justifyContent: 'center',
                            background: 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
                        }}
                    >
                        <i className={`fas ${playing ? 'fa-play-circle' : 'fa-pause-circle'} fa-5x mb-3`} style={{ color: 'white', opacity: 0.8 }}></i>
                        <h3 style={{ color: 'white', marginBottom: '10px' }}>Simulation Mode</h3>
                        <p style={{ color: 'rgba(255,255,255,0.8)', marginBottom: '5px' }}>Lesson: {lesson.title}</p>
                        <p style={{ color: 'rgba(255,255,255,0.6)', fontSize: '0.9rem' }}>
                            Testing at {simulationSpeed}x speed
                        </p>
                        <div style={{
                            marginTop: '20px',
                            padding: '10px 20px',
                            backgroundColor: 'rgba(0,0,0,0.3)',
                            borderRadius: '5px'
                        }}>
                            <p style={{ color: 'white', fontSize: '2rem', margin: 0 }}>
                                {formatTime(currentTime)} / {formatTime(duration)}
                            </p>
                        </div>
                    </div>
                ) : (
                    // Real Video Player
                    <div
                        style={{
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '100%',
                        }}
                    >
                        <ReactPlayer
                            ref={playerRef}
                            url={videoUrl}
                            playing={playing}
                            controls={false} // Use custom controls
                            width="100%"
                            height="100%"
                            onProgress={handleProgress}
                            onDuration={handleDuration}
                            onSeek={handleSeek}
                            progressInterval={1000} // Update every second
                            config={{
                                file: {
                                    attributes: {
                                        controlsList: 'nodownload noplaybackrate',
                                        disablePictureInPicture: true,
                                    }
                                }
                            }}
                        />
                    </div>
                )}
            </div>

            {/* Custom Controls */}
            <div className="video-controls-panel" style={{ backgroundColor: '#2c3e50', borderRadius: '8px', padding: '15px' }}>
                {/* Progress Bar */}
                <div style={{ height: '5px', backgroundColor: '#34495e', position: 'relative', overflow: 'hidden', marginBottom: '15px', borderRadius: '3px' }}>
                    {/* Furthest point reached (light overlay) */}
                    <div
                        style={{
                            width: `${furthestProgressPercentage}%`,
                            height: '5px',
                            backgroundColor: 'rgba(52, 152, 219, 0.3)',
                            position: 'absolute',
                            top: 0,
                            left: 0,
                        }}
                    />
                    {/* Current playback position */}
                    <div
                        style={{
                            width: `${progressPercentage}%`,
                            height: '5px',
                            backgroundColor: '#3498db',
                            position: 'absolute',
                            top: 0,
                            left: 0,
                        }}
                    />
                </div>

                {/* Time Display */}
                <div className="d-flex justify-content-between mb-2">
                    <small style={{ color: '#95a5a6' }}>
                        {formatTime(currentTime)} / {formatTime(duration)}
                    </small>
                    <small style={{ color: '#95a5a6' }}>
                        Progress: {Math.floor(furthestProgressPercentage)}%
                        {furthestProgressPercentage >= completionThreshold && (
                            <span className="text-success ms-2">
                                <i className="fas fa-check-circle"></i> Ready to Complete
                            </span>
                        )}
                    </small>
                </div>

                {/* Control Buttons */}
                <div className="d-flex gap-2 align-items-center">
                    <button
                        className="btn btn-warning"
                        onClick={handleRewind}
                        disabled={currentTime === 0}
                    >
                        <i className="fas fa-backward me-2"></i>
                        Rewind {rewindSeconds}s
                    </button>

                    <button
                        className={`btn ${playing ? 'btn-secondary' : 'btn-primary'} flex-grow-1`}
                        onClick={handlePlayPause}
                    >
                        <i className={`fas ${playing ? 'fa-pause' : 'fa-play'} me-2`}></i>
                        {playing ? 'Pause' : 'Play'}
                    </button>

                    {furthestProgressPercentage >= completionThreshold && (
                        <button
                            className="btn btn-success"
                            onClick={handleCompletion}
                        >
                            <i className="fas fa-check me-2"></i>
                            Complete Lesson
                        </button>
                    )}
                </div>

                {/* Info Text */}
                <div className="mt-3">
                    <small style={{ color: '#95a5a6' }}>
                        <i className="fas fa-info-circle me-2"></i>
                        You can only rewind to previously watched content. Forward skipping is disabled.
                        Complete {Math.floor(completionThreshold)}% to finish this lesson.
                    </small>
                </div>
            </div>

            {/* Pause Modal */}
            <PauseModal
                isVisible={isPaused}
                pauseDurationMinutes={currentPause.duration_minutes}
                pauseLabel={currentPause.label}
                remainingSeconds={pauseRemainingSeconds}
                warningSeconds={pauseWarningSeconds}
                alertSoundPath={pauseAlertSound}
                onResume={handleResume}
                onTimeExpired={handlePauseTimeExpired}
            />
        </div>
    );
};

export default SecureVideoPlayer;
