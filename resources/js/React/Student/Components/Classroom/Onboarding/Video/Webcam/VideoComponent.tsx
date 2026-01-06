import { Card } from "react-bootstrap";

interface VideoComponentProps {
    width: number;
    height: number;
    videoRef: React.RefObject<HTMLVideoElement>;
    captureImage: () => void;
    handleWebcamReset: () => void;
    debug?: boolean;
}

const VideoComponent: React.FC<VideoComponentProps> = ({
    width,
    height,
    videoRef,
    captureImage,
    handleWebcamReset,
    debug = false,
}) => {
    if(debug === true) console.log("VideoComponent", width, height, videoRef, captureImage, handleWebcamReset);

    return (
        <div className="container">
            <video
                width={width}
                height={height}
                ref={videoRef}
                style={{
                    objectFit: "cover",
                    width: width,
                    height: height,
                    margin: 0,
                }}
                autoPlay={true}
                muted={true}
                playsInline={true}
            />
            <div className="d-flex justify-content-between p-3">
                <button
                    className="btn btn-lg btn-primary"
                    onClick={captureImage}
                >
                   <i className="fa fa-camera"></i> Capture
                </button>
                <button
                    className="btn btn-lg btn-danger "
                    onClick={handleWebcamReset}
                >
                    <i className="fa fa-refresh"></i> Reset
                </button>
            </div>
        </div>
    );
};

export default VideoComponent;
