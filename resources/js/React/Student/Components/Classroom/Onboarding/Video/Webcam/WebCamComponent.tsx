import React from "react";
import { Button } from "react-bootstrap";

import CanvasComponent from "./CanvasCompoent";
import VideoComponent from "./VideoComponent";

interface WebCamComponentProps {
    photoType: string;
    showCaptureType: string | null;
    headshot: string | null;
    idcard: string | null;
    width: number;
    height: number;
    captured: boolean;
    videoRef: React.RefObject<HTMLVideoElement>;
    canvasRef: React.RefObject<HTMLCanvasElement>;
    captureImage: () => void;
    handleSaveImage: () => void;
    handleWebcamReset: () => void;
    handelRemoveImageFromServer: () => void;
}


const WebCamComponent: React.FC<WebCamComponentProps> = ({
    photoType,
    showCaptureType,
    headshot,
    idcard,
    width,
    height,
    captured,
    videoRef,
    canvasRef,
    captureImage,
    handleSaveImage,
    handleWebcamReset,
    handelRemoveImageFromServer,
}) => {

    return (
        <div>
            {showCaptureType === "webcam" && photoType === "headshot" && headshot?.length > 0 ? (
                <>
                    <img
                        src={headshot}
                        alt="Headshot"
                        style={{
                            objectFit: "cover",
                            width: "100%",
                            height: "100%",
                            margin: 0,
                        }}
                    />
                    <footer className="alert text-right"><Button className="btn btn-danger btn-sm" onClick={() => handelRemoveImageFromServer()}>Delete</Button></footer>
                </>
            ) : showCaptureType === "webcam" && photoType === "idcard" && idcard?.length > 0 ? (
                <>
                    <img
                        src={idcard}
                        alt="ID Card"
                        style={{
                            objectFit: "cover",
                            width: "100%",
                            height: "100%",
                            margin: 0,
                        }}
                    />
                    <footer className="alert text-right"><Button className="btn btn-danger btn-sm" onClick={() => handelRemoveImageFromServer()}>Delete</Button></footer>
                </>
            ) : !captured ? (
                <VideoComponent
                    width={width}
                    height={height}
                    videoRef={videoRef}
                    captureImage={captureImage}
                    handleWebcamReset={handleWebcamReset}
                />
            ) : (
                <CanvasComponent
                    width={width}
                    height={height}
                    captured={captured}
                    canvasRef={canvasRef}
                    handleSaveImage={handleSaveImage}
                    handleWebcamReset={handleWebcamReset}
                />
            )}
        </div>
    );
};

export default WebCamComponent;
