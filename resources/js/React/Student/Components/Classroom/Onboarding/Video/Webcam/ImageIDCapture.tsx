import React from "react";
import { useEffect, useRef, useState, useCallback } from "react";
import { Button } from "react-bootstrap";
import CanvasComponent from "./CanvasComponent";
import VideoComponent from "./VideoComponent";
import { Spinner } from "react-bootstrap";
import { StudentType } from "@/React/Student/types/students.types";
import usePhotoUploaded from './usePhotoUploaded';

/**
 * ImageIDCapture - Simplified webcam capture for ID verification
 * Features:
 * - Manual capture with positioning guide
 * - Image quality validation (blur detection)
 * - File upload integration
 * - Auto-detection features REMOVED for simplicity
 */

interface ImageIDCaptureProps {
    data: any | null;
    student: StudentType;
    photoType: string | null;
    headshot: string | null;
    idcard: string | null;
    debug?: boolean;
    onImageSaved?: (imageData: { blob: Blob; dataUrl: string }) => void;
    onStepComplete?: () => void;
}

const ImageIDCapture: React.FC<ImageIDCaptureProps> = ({
    data,
    student,
    photoType,
    headshot,
    idcard,
    debug = false,
    onImageSaved,
    onStepComplete,
}) => {
    if (debug === true)
        console.log("ImageIDCapture", photoType, headshot, idcard, student);

    // Null safety checks
    if (!student) {
        console.error('❌ ImageIDCapture: student prop is required but was null/undefined');
        return (
            <div style={{ padding: '20px', color: 'red', textAlign: 'center' }}>
                <h3>Error: Student data not available</h3>
                <p>Please ensure student information is loaded before accessing this component.</p>
            </div>
        );
    }

    if (!student.id || !student.name) {
        console.error('❌ ImageIDCapture: student missing required fields (id, name)', student);
        return (
            <div style={{ padding: '20px', color: 'orange', textAlign: 'center' }}>
                <h3>Error: Incomplete student data</h3>
                <p>Student ID and name are required. Current data: {JSON.stringify(student)}</p>
            </div>
        );
    }

    // Initialize photo upload handler
    const {
        handleUploadCapturedImage,
        isUploading,
        isLoading: uploadLoading,
        isError: uploadError,
        errorMessage: uploadErrorMessage
    } = usePhotoUploaded({
        data,
        student,
        photoType: photoType || 'id_card',
        debug
    });

    // Simplified state - manual capture only
    const [captured, setCaptured] = useState<boolean>(false);
    const [cameraStream, setCameraStream] = useState<MediaStream | null>(null);
    const [filesentTotserver, setFileSentToServer] = useState<boolean>(false);
    const [cameraError, setCameraError] = useState<string | null>(null);
    const [cameraLoading, setCameraLoading] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [isBlurry, setIsBlurry] = useState<boolean>(false);
    const [imageSaved, setImageSaved] = useState<boolean>(false);
    const [capturedImageData, setCapturedImageData] = useState<{ blob: Blob; dataUrl: string } | null>(null);

    // Refs - simplified
    const videoRef = useRef<HTMLVideoElement>(null);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const captureCanvasRef = useRef<HTMLCanvasElement>(null); // Hidden canvas for capture process

    // Dynamic dimensions with useEffect
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

        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    // Blur detection function (kept for quality validation)
    const detectBlur = useCallback((canvas) => {
        const ctx = canvas.getContext('2d');
        if (!ctx) return 0;

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        let contrastSum = 0;
        let pixelCount = 0;

        // Calculate local contrast for edge detection
        for (let y = 1; y < canvas.height - 1; y++) {
            for (let x = 1; x < canvas.width - 1; x++) {
                const idx = (y * canvas.width + x) * 4;

                // Convert to grayscale
                const center = data[idx] * 0.299 + data[idx + 1] * 0.587 + data[idx + 2] * 0.114;

                // Get surrounding pixels
                const neighbors = [
                    data[idx - 4] * 0.299 + data[idx - 3] * 0.587 + data[idx - 2] * 0.114, // left
                    data[idx + 4] * 0.299 + data[idx + 5] * 0.587 + data[idx + 6] * 0.114, // right
                    data[idx - canvas.width * 4] * 0.299 + data[idx - canvas.width * 4 + 1] * 0.587 + data[idx - canvas.width * 4 + 2] * 0.114, // up
                    data[idx + canvas.width * 4] * 0.299 + data[idx + canvas.width * 4 + 1] * 0.587 + data[idx + canvas.width * 4 + 2] * 0.114  // down
                ];

                // Calculate local contrast
                let maxContrast = 0;
                neighbors.forEach(neighbor => {
                    maxContrast = Math.max(maxContrast, Math.abs(center - neighbor));
                });

                contrastSum += maxContrast;
                pixelCount++;
            }
        }

        return contrastSum / pixelCount;
    }, []);

    // Camera functionality
    async function enableCamera() {
        try {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                setCameraError("Camera access requires a secure connection (HTTPS). Please use the upload option instead.");
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setCameraError("Camera access not supported in this browser. Please use the upload option instead.");
                return;
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false,
            });

            setCameraStream(stream);
            setCameraLoading(false);
            setCameraError(null);
        } catch (error) {
            setCameraLoading(false);
            if (error.name === 'NotAllowedError') {
                setCameraError("Camera permission denied. Please allow camera access and try again.");
            } else if (error.name === 'NotFoundError') {
                setCameraError("No camera found. Please ensure you have a camera connected.");
            } else {
                setCameraError("Unable to access camera. Please try using the upload option instead.");
            }
        }
    }

    useEffect(() => {
        const timer = setTimeout(() => {
            enableCamera();
        }, 100);

        return () => {
            clearTimeout(timer);
            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
            }
        };
    }, []);

    // Manual capture function
    const captureImage = useCallback(() => {
        console.log('🎬 Manual capture initiated!');

        if (captured) {
            console.log('❌ Capture skipped - already captured');
            return;
        }

        if (captureCanvasRef.current && videoRef.current) {
            console.log('✅ Starting capture process...');
            const ctx = captureCanvasRef.current.getContext("2d");
            if (ctx) {
                console.log('✅ Got capture canvas context, drawing image...');

                // Set canvas dimensions to match video dimensions
                captureCanvasRef.current.width = dimensions.width;
                captureCanvasRef.current.height = dimensions.height;

                // Draw the image to capture canvas
                ctx.drawImage(
                    videoRef.current,
                    0,
                    0,
                    dimensions.width,
                    dimensions.height
                );

                // Check for blur - make it less strict and more informative
                const blurVariance = detectBlur(captureCanvasRef.current);
                const blurThreshold = 5; // Lowered from 10 to be less strict
                const isImageBlurry = blurVariance < blurThreshold;
                setIsBlurry(isImageBlurry);

                console.log(`✅ Image captured, blur analysis: ${blurVariance.toFixed(1)} (threshold: ${blurThreshold}) - ${isImageBlurry ? 'May need retaking' : 'Quality looks good'}`);

                // Note: Blur detection is now advisory only, not blocking

                // Copy the captured image to the display canvas
                if (canvasRef.current) {
                    const displayCtx = canvasRef.current.getContext("2d");
                    if (displayCtx) {
                        canvasRef.current.width = dimensions.width;
                        canvasRef.current.height = dimensions.height;
                        displayCtx.drawImage(captureCanvasRef.current, 0, 0);
                        console.log('✅ Image copied to display canvas');
                    }
                }

                // Convert capture canvas to blob and data URL for saving
                captureCanvasRef.current.toBlob((blob) => {
                    if (blob) {
                        console.log('✅ Canvas converted to blob successfully');
                        const dataUrl = captureCanvasRef.current!.toDataURL();
                        const imageData = { blob, dataUrl };
                        setCapturedImageData(imageData);

                        // Notify parent component about the captured image (always, regardless of blur)
                        if (onImageSaved) {
                            console.log('✅ Calling onImageSaved callback');
                            onImageSaved(imageData);
                            if (isImageBlurry) {
                                console.log('⚠️ Note: Image may be blurry but proceeding anyway');
                            }
                        } else {
                            console.log('⚠️ No onImageSaved callback provided');
                        }
                    } else {
                        console.error('❌ Failed to convert capture canvas to blob');
                    }
                }, 'image/jpeg', 0.8);

                setCaptured(true);
                console.log("✅ Manual capture completed successfully");
            } else {
                console.error('❌ Capture canvas context not available');
            }
        } else {
            console.error('❌ Capture canvas or video ref not available:', {
                hasCaptureCanvas: !!captureCanvasRef.current,
                hasVideo: !!videoRef.current
            });
        }
    }, [dimensions.width, dimensions.height, debug, captured, detectBlur, onImageSaved]);

    // Save image function using proper upload
    const handleSaveImage = useCallback(async () => {
        console.log('🔄 handleSaveImage called - uploading to server');

        if (!student || !student.id || !student.name) {
            console.error('❌ Invalid student data for save operation', student);
            alert('Error: Student information not available. Please refresh and try again.');
            return;
        }

        if (!capturedImageData) {
            console.error('❌ No captured image data available');
            alert('No image captured. Please capture an image first.');
            return;
        }

        // Note: Blur detection is advisory only - not blocking
        if (isBlurry) {
            console.log('⚠️ Proceeding with potentially blurry image as requested by user');
        }

        try {
            // Generate filename for proper naming structure
            const filename = `${student.name.replace(/[^a-zA-Z0-9]/g, '_')}_${student.id}_id_card_${Date.now()}.jpg`;
            console.log('📁 Generated filename:', filename);

            if (!capturedImageData.blob) {
                throw new Error('No blob data available for upload');
            }

            // Upload using the photo upload hook with proper server integration
            await handleUploadCapturedImage(capturedImageData.blob, filename);

            console.log('✅ File upload initiated successfully');

            // Update states
            setImageSaved(true);
            setFileSentToServer(true);

            // Call parent callback if provided
            if (onImageSaved) {
                console.log('📤 Calling onImageSaved callback');
                onImageSaved(capturedImageData);
            }

            // Trigger step completion after upload initiation
            setTimeout(() => {
                if (onStepComplete) {
                    console.log('✅ Calling onStepComplete');
                    onStepComplete();
                }
            }, 2000); // Slightly longer delay for upload processing

            if (debug) {
                console.log('✅ Image upload initiated successfully', {
                    filename,
                    capturedImageData
                });
            }

        } catch (error) {
            console.error('❌ Error uploading image:', error);
            alert(`Error uploading image: ${error.message || error}. Please try again.`);
        }
    }, [capturedImageData, isBlurry, student, data, handleUploadCapturedImage, onImageSaved, onStepComplete, debug]);

    // Simple reset function
    const handleWebcamReset = () => {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => track.stop());
        }
        if (videoRef.current) {
            videoRef.current.srcObject = null;
        }
        setCameraStream(null);
        setCaptured(false);
        setCameraError(null);
        setCameraLoading(true);
        setTimeout(() => enableCamera(), 100);
    };

    // Handle video stream assignment when camera stream is available
    useEffect(() => {
        if (cameraStream && !cameraError) {
            const assignStreamToVideo = () => {
                if (videoRef.current && !videoRef.current.srcObject) {
                    videoRef.current.srcObject = cameraStream;
                    videoRef.current.play().catch((error) => {
                        console.error("Error playing video:", error);
                    });
                }
            };
            assignStreamToVideo();
            const timer = setTimeout(assignStreamToVideo, 100);
            return () => clearTimeout(timer);
        }
    }, [cameraStream, cameraError]);

    // Ensure canvas is properly drawn when switching to captured state
    useEffect(() => {
        if (captured && canvasRef.current && captureCanvasRef.current) {
            const displayCtx = canvasRef.current.getContext("2d");
            const captureCtx = captureCanvasRef.current.getContext("2d");
            if (displayCtx && captureCtx) {
                console.log('✅ Refreshing display canvas from capture canvas');
                canvasRef.current.width = dimensions.width;
                canvasRef.current.height = dimensions.height;
                displayCtx.drawImage(captureCanvasRef.current, 0, 0);
            }
        }
    }, [captured, dimensions.width, dimensions.height]);

    const isHeadshot = (photoType ?? '').toLowerCase() === 'headshot';
    const headerTitle = isHeadshot ? 'Capture Headshot Photo' : 'Capture ID Card Verification';
    const headerDescription = isHeadshot
        ? 'Center your face in the frame and click "Capture" when ready.'
        : 'Position your ID card in the frame and click "Capture" when ready.';
    const instructions: string[] = isHeadshot
        ? [
              'Center your face within the green guide frame',
              'Ensure good lighting and that your face is clearly visible',
              'Click "Capture Manually" when you are ready',
          ]
        : [
              'Position your ID card within the green guide frame',
              'Ensure good lighting and that all text is clearly visible',
              'Click "Capture Manually" when your ID is properly positioned',
          ];

    return (
        <div style={{ maxWidth: '800px', margin: '0 auto', padding: '20px', fontFamily: 'system-ui, -apple-system, sans-serif' }}>
            {/* Hidden capture canvas - always available for image capture */}
            <canvas
                ref={captureCanvasRef}
                style={{ display: 'none' }}
                width={dimensions.width}
                height={dimensions.height}
            />

            {/* Main Header */}
            <div style={{ marginBottom: '24px' }}>
                <h2 style={{ fontSize: '24px', fontWeight: '600', color: '#1a1a1a', marginBottom: '12px' }}>
                    {headerTitle}
                </h2>
                <p style={{ fontSize: '16px', color: '#6b7280', lineHeight: '1.5', margin: '0' }}>
                    {headerDescription}
                </p>
            </div>

            {/* Instructions Section - Simplified */}
            <div style={{
                background: '#f3f4f6',
                borderRadius: '12px',
                padding: '20px',
                marginBottom: '24px',
                border: '1px solid #e5e7eb'
            }}>
                <h3 style={{ fontSize: '18px', fontWeight: '600', color: '#1f2937', marginBottom: '16px' }}>
                    📋 Instructions
                </h3>

                <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                    <div style={{ display: 'flex', alignItems: 'flex-start' }}>
                        <span style={{
                            background: '#3b82f6',
                            color: 'white',
                            borderRadius: '50%',
                            width: '24px',
                            height: '24px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '14px',
                            fontWeight: '600',
                            marginRight: '12px',
                            flexShrink: 0,
                            marginTop: '2px'
                        }}>
                            1
                        </span>
                        <span style={{ fontSize: '16px', color: '#374151', lineHeight: '1.5' }}>
                            {instructions[0]}
                        </span>
                    </div>

                    <div style={{ display: 'flex', alignItems: 'flex-start' }}>
                        <span style={{
                            background: '#3b82f6',
                            color: 'white',
                            borderRadius: '50%',
                            width: '24px',
                            height: '24px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '14px',
                            fontWeight: '600',
                            marginRight: '12px',
                            flexShrink: 0,
                            marginTop: '2px'
                        }}>
                            2
                        </span>
                        <span style={{ fontSize: '16px', color: '#374151', lineHeight: '1.5' }}>
                            {instructions[1]}
                        </span>
                    </div>

                    <div style={{ display: 'flex', alignItems: 'flex-start' }}>
                        <span style={{
                            background: '#3b82f6',
                            color: 'white',
                            borderRadius: '50%',
                            width: '24px',
                            height: '24px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '14px',
                            fontWeight: '600',
                            marginRight: '12px',
                            flexShrink: 0,
                            marginTop: '2px'
                        }}>
                            3
                        </span>
                        <span style={{ fontSize: '16px', color: '#374151', lineHeight: '1.5' }}>
                            {instructions[2]}
                        </span>
                    </div>
                </div>
            </div>

            {/* Camera Section */}
            <div style={{
                background: '#ffffff',
                borderRadius: '12px',
                padding: '20px',
                border: '1px solid #e5e7eb',
                marginBottom: '24px'
            }}>
                {cameraError ? (
                    <div style={{
                        textAlign: 'center',
                        color: '#ef4444',
                        fontSize: '16px',
                        padding: '40px 20px',
                        background: '#fef2f2',
                        borderRadius: '8px',
                        border: '1px solid #fecaca'
                    }}>
                        <h4 style={{ fontSize: '18px', fontWeight: '600', marginBottom: '12px' }}>
                            Camera Error
                        </h4>
                        <p style={{ margin: '0' }}>{cameraError}</p>
                    </div>
                ) : cameraLoading ? (
                    <div style={{
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '40px',
                        color: '#6b7280'
                    }}>
                        <Spinner animation="border" style={{ marginBottom: '16px' }} />
                        <p style={{ margin: '0', fontSize: '16px' }}>Loading camera...</p>
                    </div>
                ) : (
                    <div style={{ position: 'relative' }}>
                        {/* Hidden canvas for capture process */}
                        <canvas
                            ref={captureCanvasRef}
                            style={{ display: 'none' }}
                            width={dimensions.width}
                            height={dimensions.height}
                        />

                        {/* Video and Canvas Components */}
                        <div style={{ position: 'relative', display: 'inline-block' }}>
                            {!captured ? (
                                <VideoComponent
                                    width={dimensions.width}
                                    height={dimensions.height}
                                    videoRef={videoRef}
                                    captureImage={captureImage}
                                    handleWebcamReset={handleWebcamReset}
                                    debug={debug}
                                />
                            ) : (
                                <CanvasComponent
                                    width={dimensions.width}
                                    height={dimensions.height}
                                    captured={captured}
                                    canvasRef={canvasRef}
                                    handleSaveImage={handleSaveImage}
                                    handleWebcamReset={handleWebcamReset}
                                    debug={debug}
                                />
                            )}

                            {/* ID Card Guide Overlay - Simple version */}
                            {!captured && (
                                <div
                                    style={{
                                        position: "absolute",
                                        top: "40%",
                                        left: "50%",
                                        transform: "translate(-50%, -50%)",
                                        width: `${Math.min(dimensions.width - 80, isHeadshot ? 200 : 250)}px`,
                                        height: `${Math.min(dimensions.height - 80, isHeadshot ? 200 : 160)}px`,
                                        pointerEvents: "none",
                                        border: "2px solid #10b981",
                                        borderRadius: isHeadshot ? "9999px" : "8px"
                                    }}
                                >
                                    {/* Simple green frame indicator */}
                                    <div style={{
                                        position: "absolute",
                                        top: "50%",
                                        left: "50%",
                                        transform: "translate(-50%, -50%)",
                                        background: "rgba(16, 185, 129, 0.9)",
                                        color: "white",
                                        padding: "8px 16px",
                                        borderRadius: "20px",
                                        fontSize: "14px",
                                        fontWeight: "600",
                                        textAlign: "center",
                                        whiteSpace: "nowrap",
                                        backdropFilter: "blur(4px)"
                                    }}>
                                        {isHeadshot ? '👤 Position face here' : '🆔 Position ID card here'}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Action Buttons */}
                        <div style={{ marginTop: '24px', display: 'flex', gap: '12px', justifyContent: 'center', flexWrap: 'wrap' }}>
                            <Button
                                onClick={captureImage}
                                disabled={captured}
                                style={{
                                    background: captured ? '#9ca3af' : '#3b82f6',
                                    border: 'none',
                                    borderRadius: '8px',
                                    padding: '12px 24px',
                                    fontWeight: '500',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '8px',
                                    cursor: captured ? 'not-allowed' : 'pointer'
                                }}
                            >
                                📸 {captured ? 'Image Captured' : 'Capture Manually'}
                            </Button>

                            <Button
                                onClick={handleWebcamReset}
                                variant="outline-secondary"
                                style={{
                                    border: '1px solid #d1d5db',
                                    borderRadius: '8px',
                                    padding: '12px 24px',
                                    fontWeight: '500',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '8px'
                                }}
                            >
                                🔄 Reset Camera
                            </Button>
                        </div>
                    </div>
                )}
            </div>

            {/* Status Section */}
            <div style={{
                display: 'flex',
                flexWrap: 'wrap',
                justifyContent: 'center',
                gap: '16px',
                padding: '16px',
                background: '#f9fafb',
                borderRadius: '8px',
                fontSize: '14px'
            }}>
                <span style={{ color: '#6b7280' }}>
                    Camera: {cameraError ? '❌ Error' : cameraLoading ? '⏳ Loading' : '✅ Ready'}
                </span>

                {captured && (
                    <span style={{ color: '#10b981', fontWeight: '500' }}>
                        ✅ Image Captured Successfully
                    </span>
                )}

                {isBlurry && (
                    <span style={{ color: '#f59e0b', fontWeight: '500' }}>
                        ⚠️ Image may need better focus - you can still proceed or retake
                    </span>
                )}

                {isUploading && (
                    <span style={{ color: '#3b82f6', fontWeight: '500' }}>
                        ⏳ Uploading to server...
                    </span>
                )}

                {uploadLoading && (
                    <span style={{ color: '#3b82f6', fontWeight: '500' }}>
                        📤 Processing upload...
                    </span>
                )}

                {uploadError && (
                    <span style={{ color: '#ef4444', fontWeight: '500' }}>
                        ❌ Upload error - please try again
                    </span>
                )}

                {uploadErrorMessage && (
                    <span style={{ color: '#ef4444', fontWeight: '500' }}>
                        ❌ {uploadErrorMessage}
                    </span>
                )}

                {filesentTotserver && (
                    <span style={{ color: '#10b981', fontWeight: '500' }}>
                        ✅ Upload successful!
                    </span>
                )}
            </div>
        </div>
    );
};

export default ImageIDCapture;
