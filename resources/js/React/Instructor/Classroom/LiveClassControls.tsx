import React, { useState } from 'react';

interface LiveClassControlsProps {
    isLive?: boolean;
    studentCount?: number;
}

const LiveClassControls: React.FC<LiveClassControlsProps> = ({
    isLive = false,
    studentCount = 0
}) => {
    const [liveStatus, setLiveStatus] = useState(isLive);
    const [micEnabled, setMicEnabled] = useState(true);
    const [cameraEnabled, setCameraEnabled] = useState(true);

    const toggleLive = () => {
        setLiveStatus(!liveStatus);
    };

    return (
        <div className="live-class-controls">
            <div className="card">
                <div className="card-header">
                    <h6>Live Class Controls</h6>
                    <span className={`badge bg-${liveStatus ? 'success' : 'secondary'}`}>
                        {liveStatus ? 'LIVE' : 'OFFLINE'}
                    </span>
                </div>
                <div className="card-body">
                    <div className="row mb-3">
                        <div className="col-md-6">
                            <p><strong>Students Online:</strong> {studentCount}</p>
                        </div>
                        <div className="col-md-6">
                            <button
                                className={`btn btn-${liveStatus ? 'danger' : 'success'}`}
                                onClick={toggleLive}
                            >
                                {liveStatus ? 'End Class' : 'Start Class'}
                            </button>
                        </div>
                    </div>

                    <div className="controls-row">
                        <button
                            className={`btn btn-${micEnabled ? 'success' : 'danger'} me-2`}
                            onClick={() => setMicEnabled(!micEnabled)}
                        >
                            <i className={`fas fa-microphone${micEnabled ? '' : '-slash'}`}></i>
                            {micEnabled ? ' Mute' : ' Unmute'}
                        </button>

                        <button
                            className={`btn btn-${cameraEnabled ? 'success' : 'danger'} me-2`}
                            onClick={() => setCameraEnabled(!cameraEnabled)}
                        >
                            <i className={`fas fa-video${cameraEnabled ? '' : '-slash'}`}></i>
                            {cameraEnabled ? ' Stop Video' : ' Start Video'}
                        </button>

                        <button className="btn btn-info me-2">
                            <i className="fas fa-desktop"></i> Share Screen
                        </button>

                        <button className="btn btn-warning">
                            <i className="fas fa-comments"></i> Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default LiveClassControls;
