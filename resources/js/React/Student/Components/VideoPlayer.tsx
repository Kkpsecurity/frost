import React, { useState, useRef } from 'react';

interface VideoPlayerProps {
    videoUrl?: string;
    title?: string;
    duration?: string;
}

const VideoPlayer: React.FC<VideoPlayerProps> = ({
    videoUrl = '',
    title = 'Video Lesson',
    duration = '0:00'
}) => {
    const videoRef = useRef<HTMLVideoElement>(null);
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [volume, setVolume] = useState(1);

    const togglePlay = () => {
        if (videoRef.current) {
            if (isPlaying) {
                videoRef.current.pause();
            } else {
                videoRef.current.play();
            }
            setIsPlaying(!isPlaying);
        }
    };

    const handleTimeUpdate = () => {
        if (videoRef.current) {
            setCurrentTime(videoRef.current.currentTime);
        }
    };

    const handleVolumeChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newVolume = parseFloat(e.target.value);
        setVolume(newVolume);
        if (videoRef.current) {
            videoRef.current.volume = newVolume;
        }
    };

    return (
        <div className="video-player">
            <div className="card">
                <div className="card-header">
                    <h6>{title}</h6>
                    <small className="text-muted">Duration: {duration}</small>
                </div>
                <div className="card-body p-0">
                    <div className="video-container">
                        {videoUrl ? (
                            <video
                                ref={videoRef}
                                className="w-100"
                                onTimeUpdate={handleTimeUpdate}
                                controls
                            >
                                <source src={videoUrl} type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        ) : (
                            <div className="video-placeholder d-flex align-items-center justify-content-center" style={{height: '300px', backgroundColor: '#f8f9fa'}}>
                                <div className="text-center">
                                    <i className="fas fa-play-circle fa-3x text-muted mb-2"></i>
                                    <p className="text-muted">No video source provided</p>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="video-controls p-3">
                        <div className="d-flex align-items-center">
                            <button
                                className="btn btn-primary btn-sm me-3"
                                onClick={togglePlay}
                            >
                                <i className={`fas fa-${isPlaying ? 'pause' : 'play'}`}></i>
                            </button>

                            <div className="volume-control d-flex align-items-center">
                                <i className="fas fa-volume-up me-2"></i>
                                <input
                                    type="range"
                                    className="form-range"
                                    min="0"
                                    max="1"
                                    step="0.1"
                                    value={volume}
                                    onChange={handleVolumeChange}
                                    style={{width: '100px'}}
                                />
                            </div>

                            <div className="ms-auto">
                                <button className="btn btn-outline-secondary btn-sm me-2">
                                    <i className="fas fa-closed-captioning"></i>
                                </button>
                                <button className="btn btn-outline-secondary btn-sm">
                                    <i className="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default VideoPlayer;
