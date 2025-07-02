import { faLock } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import React, { useState } from "react";
import { Alert, OverlayTrigger, Tooltip } from "react-bootstrap";

interface FrostInstructorScreenShareProps {
    laravel: any; // You can replace this with an appropriate type
    data: any; // You can replace this with an appropriate type
    courseDateId: number;
}

const FrostInstructorScreenShare: React.FC<FrostInstructorScreenShareProps> = ({
    laravel,
    data,
    courseDateId,
}) => {
    const [isSharing, setIsSharing] = useState(false);

    const localShare = React.useRef(null);

    const handleScreenShare = async (e) => {
        e.preventDefault();

        if (!isSharing) {
            // initiate the screen share
            try {
                const localStream =
                    await navigator.mediaDevices.getDisplayMedia({
                        video: true,
                        audio: {
                            echoCancellation: true,
                            noiseSuppression: true,
                            sampleRate: 44100,
                        },
                    });

                if (localShare.current) {
                    localShare.current.srcObject = localStream;
                }
            } catch (error) {
                console.error("Error starting screen sharing:", error);
            }

            setIsSharing(true);
        } else {
            // Stop screen sharing logic
            if (localShare.current && localShare.current.srcObject) {
                const tracks = (
                    localShare.current.srcObject as MediaStream
                ).getTracks();
                tracks.forEach((track) => track.stop());
            }

            setIsSharing(false);
        }
    };

    const handleMuteAllVideos = (e) => {};

    const handleMuteAllMics = (e) => {};

    const handleSwitchScreens = (e) => {};

    const handlePauseScreenShare = (e) => {};

    const renderTooltip = (props) => (
        <Tooltip id="button-tooltip" {...props}>
            Activating the screen sharing feature will enable your screen view
            in the student's classroom. For optimal performance, it's
            recommended that you configure your Screensharing settings before
            engaging the screen sharing feature.
        </Tooltip>
    );

    if (!isSharing) {
        return (
            <Alert variant="success" className="d-flex justify-content-between">
                <OverlayTrigger
                    placement="bottom"
                    delay={{ show: 250, hide: 400 }}
                    overlay={renderTooltip}
                >
                    <span>
                        <div
                            className="title"
                            style={{
                                fontSize: "1.2rem",
                            }}
                        >
                            Frost Screen Sharing{" "}
                            <sup>
                                <i className="fas fa-question-circle"></i>
                            </sup>
                        </div>
                    </span>
                </OverlayTrigger>

                <button
                    className="btn btn-primary-outline"
                    onClick={(e) => handleScreenShare(e)}
                >
                    <FontAwesomeIcon icon={faLock} /> Initate Screen Sharing
                </button>
            </Alert>
        );
    }

    if (isSharing) {
        return (
            <div className="preview">
                <div className="row">
                    <div className="col-md-2">
                        <video
                            ref={localShare}
                            style={{
                                width: "100px",
                                height: "100px",
                                backgroundColor: "black",
                            }}
                            autoPlay
                            playsInline
                        ></video>
                    </div>
                    <div className="col-md-8">
                        <div contextMenu=""></div>                       
                    </div>
                </div>
            </div>
        );
    }
};

export default FrostInstructorScreenShare;
