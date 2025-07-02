import React from "react";
import { useEffect, useRef, useState } from "react";
import { Button } from "react-bootstrap";
import { ClassDataShape, StudentType } from "../../../../../Config/types";
import CanvasComponent from "./CanvasCompoent";
import VideoComponent from "./VideoComponent";

import { HandelFileUpload } from "../../../../../Hooks/Web/useClassRoomDataHooks";
import { Logger } from "agora-rtc-sdk";
import Loader from "../../../../../Components/Widgets/Loader";

/**
 * ImageIDCapture Lets the user capture an image from the webcam
 * features:
 * - Capture image from webcam
 * - Reset the Webcam to capture another image
 * - Upload an image to the local file system
 */

interface ImageIDCaptureProps {
    data: ClassDataShape | null; 
    student: StudentType;
    photoType: string | null;
    headshot: string | null;
    idcard: string | null;
    debug?: boolean;
}

const ImageIDCapture: React.FC<ImageIDCaptureProps> = ({
    data,
    student,
    photoType,
    headshot,
    idcard,
    debug = false,
}) => {
    console.log("ImageIDCapture", photoType, headshot, idcard, student);

    const [captured, setCaptured] = useState<boolean>(false);
    const [cameraStream, setCameraStream] = useState<MediaStream | null>(null);
    const [filesentTotserver, setFileSentToServer] = useState<boolean>(false);
    const videoRef = useRef(null);
    const canvasRef = useRef(null);

    const [loading, setLoading] = useState<boolean>(false);

    const [dimensions, setDimensions] = useState(
        window.innerWidth > 768 
            ? { width: 400, height: 300 }
            : { width: 320, height: 280 }
    );

    useEffect(() => {
        const handleResize = () => {
            setDimensions(
                window.innerWidth > 768 
                    ? { width: 400, height: 300 }
                    : { width: 320, height: 280 }
            );
        };

        // Attach the event listener
        window.addEventListener('resize', handleResize);

        // Cleanup the event listener on component unmount
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    /**
     * Reset the webcam
     */
    const handleWebcamReset = () => {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => track.stop());
        }

        setCameraStream(null);
        setCaptured(false);
        enableCamera();

        if (canvasRef.current) {
            const canvas = canvasRef.current as HTMLCanvasElement;
            const context = canvas.getContext("2d");
            if (context) {
                context.clearRect(0, 0, canvas.width, canvas.height);
            }
        }
    };

    /**
     * Prepare the mutation to upload the image to the server
     */
    const { mutate: saveImage, isLoading, isError } = HandelFileUpload();

    /**
     * Send File to server
     */
    const handleSaveImage = async () => {
        setLoading(true);
        try {
            // Convert canvas to blob
            const originalImageBlob = await new Promise<Blob>((resolve, reject) => {
                if (!canvasRef.current) {
                    reject(new Error("Canvas reference is null"));
                    return;
                }
                canvasRef.current.toBlob((blob) => {
                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error("Failed to convert canvas to Blob"));
                    }
                }, "image/png");
            });
    
            // Resize the image blob using a canvas
            const img = new Image();
            img.src = URL.createObjectURL(originalImageBlob);
            await new Promise((resolve, reject) => { // Add reject to handle image loading errors
                img.onload = () => resolve(null); // Resolve with null because onload doesn't return a value
                img.onerror = () => reject(new Error("Failed to load image for resizing")); // Handle image load errors
            });
    
            const elem = document.createElement('canvas');
            const scaleFactor = 0.5; // Adjust this value to change the size
            elem.width = img.width * scaleFactor;
            elem.height = img.height * scaleFactor;
            const ctx = elem.getContext('2d');
            if (!ctx) {
                throw new Error("Failed to get canvas context");
            }
            ctx.drawImage(img, 0, 0, elem.width, elem.height);
            
            const resizedImageBlob = await new Promise<Blob>((resolve) => {
                ctx.canvas.toBlob((blob) => {
                    if (blob) {
                        resolve(blob);
                    } else {
                        throw new Error("Failed to convert resized canvas to Blob");
                    }
                }, "image/png");
            });
    
            const formData = new FormData();
            formData.append("student_unit_id", data.student_unit_id.toString());
            formData.append("course_auth_id", student.course_auth_id.toString());
            formData.append("_token", document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ?? "");
            formData.append("photoType", photoType ?? "");
            formData.append("file", resizedImageBlob); // Use the resized blob
    
            await saveImage(formData); // Assuming saveImage is async, wait for it to complete
            setFileSentToServer(true);

            setTimeout(() => { 
                handleWebcamReset();
                setLoading(false);
            }, 3000); 
        } catch (error) {
            console.error("Error saving image:", error);
            setLoading(false); // Ensure loading state is reset on error
            // Adjust error handling as necessary. The original throw might not be appropriate depending on your error handling strategy.
        }
    };
    
    

    /**
     * Enable the webcam
     */
    async function enableCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false,
            });

            setCameraStream(stream);

            if (videoRef.current) {
                videoRef.current.srcObject = stream;
            }
        } catch (error) {
            console.log("Error enabling camera:", error);
        }
    }

    useEffect(() => {
        enableCamera();
        return () => handleWebcamReset();
    }, []);

    const captureImage = () => {
        if (canvasRef.current && videoRef.current) {
            canvasRef.current
                .getContext("2d")
                .drawImage(
                    videoRef.current,
                    0,
                    0,
                    dimensions.width,
                    dimensions.height
                );

            /**
             * Convert the canvas to a data url
             */
            const imageURL = canvasRef.current.toDataURL("image/png");
            setCaptured(true);

            if (debug === true)
                console.log("Image Captured Successfullt: ", imageURL);
        } else {
            if (debug === true) console.log("No canvas or video ref found");
        }
    };

    if(loading) {
        return <Loader />
    }

    return (
        <>
            {isLoading && (
                <div className="alert alert-info">Uploading image...</div>
            )}
            
            {isError && (
                <div className="alert alert-danger">Error uploading image</div>
            )}

            {filesentTotserver && !isError && (
                <div className="alert alert-success">
                    Image uploaded successfully
                </div>
            )}

            <CanvasComponent
                width={dimensions.width}
                height={dimensions.height}
                captured={captured}
                canvasRef={canvasRef}
                handleSaveImage={handleSaveImage}
                handleWebcamReset={handleWebcamReset}
                debug={debug}
            />

            {!captured ? (
                <>
                    <VideoComponent
                        width={dimensions.width}
                        height={dimensions.height}
                        videoRef={videoRef}
                        captureImage={captureImage}
                        handleWebcamReset={handleWebcamReset}
                        debug={debug}
                    />
                </>
            ) : (
                <></>
            )}
        </>
    );
};

export default ImageIDCapture;
