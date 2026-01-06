import React from "react";
import { useEffect, useRef, useState, useCallback } from "react";
import { Button } from "react-bootstrap";
import CanvasComponent from "./CanvasComponent";
import VideoComponent from "./VideoComponent";
import { Spinner } from "react-bootstrap";
import { StudentType } from "@/React/Student/types/students.types";
import useFileHandler from '@/React/Hooks/useFileHandler';

// Global type declarations for OpenCV
declare global {
    interface Window {
        cv: any;
        Module: any;
    }
}

/**
 * ImageIDCapture Lets the user capture an image from the webcam
 * features:
 * - Capture image from webcam
 * - Reset the Webcam to capture another image
 * - Upload an image to the local file system
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

    // Initialize file handler
    const { uploadFile, showIdCard, downloadFile, generateFileName } = useFileHandler();

    // Basic state - no complex logic yet
    const [captured, setCaptured] = useState<boolean>(false);
    const [cameraStream, setCameraStream] = useState<MediaStream | null>(null);
    const [filesentTotserver, setFileSentToServer] = useState<boolean>(false);
    const [cameraError, setCameraError] = useState<string | null>(null);
    const [cameraLoading, setCameraLoading] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [isAutoDetectionEnabled, setIsAutoDetectionEnabled] = useState<boolean>(true);
    const [detectionStatus, setDetectionStatus] = useState<string>("Searching for ID card...");
    const [autoCapturing, setAutoCapturing] = useState<boolean>(false);
    const [isLoading, setIsLoading] = useState(false);
    const [isError, setIsError] = useState(false);
    const [cvLoaded, setCvLoaded] = useState<boolean>(false);
    const [cardDetected, setCardDetected] = useState<boolean>(false);
    const [isBlurry, setIsBlurry] = useState<boolean>(false);
    const [imageSaved, setImageSaved] = useState<boolean>(false);
    const [capturedImageData, setCapturedImageData] = useState<{ blob: Blob; dataUrl: string } | null>(null);
    const [isAnalyzing, setIsAnalyzing] = useState<boolean>(false);
    const [objectSizeCoverage, setObjectSizeCoverage] = useState<number>(0);
    const [steadinessCount, setSteadinessCount] = useState<number>(0);

    // Refs
    const videoRef = useRef<HTMLVideoElement>(null);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const detectionCanvasRef = useRef<HTMLCanvasElement>(null);

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

    // Blur detection function
    const detectBlur = useCallback((canvas: HTMLCanvasElement): number => {
        if (!window.cv) {
            // Fallback: Simple edge detection using canvas
            const ctx = canvas.getContext('2d');
            if (!ctx) return 0;

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            let edgeSum = 0;

            // Simple sobel edge detection
            for (let y = 1; y < canvas.height - 1; y++) {
                for (let x = 1; x < canvas.width - 1; x++) {
                    const idx = (y * canvas.width + x) * 4;
                    const gray = data[idx] * 0.299 + data[idx + 1] * 0.587 + data[idx + 2] * 0.114;

                    // Get neighboring pixels
                    const right = (data[idx + 4] * 0.299 + data[idx + 5] * 0.587 + data[idx + 6] * 0.114);
                    const bottom = (data[idx + canvas.width * 4] * 0.299 + data[idx + canvas.width * 4 + 1] * 0.587 + data[idx + canvas.width * 4 + 2] * 0.114);

                    edgeSum += Math.abs(gray - right) + Math.abs(gray - bottom);
                }
            }

            return edgeSum / (canvas.width * canvas.height);
        }

        try {
            const ctx = canvas.getContext('2d');
            if (!ctx) return 0;

            // Get image data from canvas
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            // Create OpenCV Mat from image data
            const src = window.cv.matFromImageData(imageData);
            const gray = new window.cv.Mat();
            const laplacian = new window.cv.Mat();

            // Convert to grayscale
            window.cv.cvtColor(src, gray, window.cv.COLOR_RGBA2GRAY);

            // Apply Laplacian filter to detect edges (blur detection)
            window.cv.Laplacian(gray, laplacian, window.cv.CV_64F);

            // Calculate variance of Laplacian (higher = sharper)
            const mean = new window.cv.Mat();
            const stddev = new window.cv.Mat();
            window.cv.meanStdDev(laplacian, mean, stddev);

            const variance = Math.pow(stddev.doubleAt(0, 0), 2);

            // Cleanup
            src.delete();
            gray.delete();
            laplacian.delete();
            mean.delete();
            stddev.delete();

            return variance;
        } catch (error) {
            console.error('Blur detection error:', error);
            return 0;
        }
    }, []);

    // Detail level detection function
    const calculateDetailLevel = useCallback((canvas: HTMLCanvasElement): number => {
        const ctx = canvas.getContext('2d');
        if (!ctx) return 0;

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        let contrastSum = 0;
        let pixelCount = 0;

        // Calculate local contrast using neighboring pixels
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

    // Load OpenCV.js
    useEffect(() => {
        const loadOpenCV = () => {
            if (window.cv && window.cv.Mat) {
                setCvLoaded(true);
                return;
            }

            // Declare global Module for OpenCV
            if (!window.Module) {
                window.Module = {
                    onRuntimeInitialized: () => {
                        console.log('OpenCV.js runtime initialized');
                        setCvLoaded(true);
                    }
                };
            }

            // Create script element for OpenCV.js
            const script = document.createElement('script');
            script.src = '/assets/js/opencv.js';
            script.async = true;
            script.onload = () => {
                console.log('OpenCV.js script loaded');
            };
            script.onerror = (error) => {
                console.error('Failed to load OpenCV.js:', error);
                // Fallback to simple detection
                setCvLoaded(false);
            };

            document.head.appendChild(script);
        };

        loadOpenCV();
    }, []);

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

    // Simple capture function with useCallback
    const captureImage = useCallback(() => {
        console.log('🎬 captureImage called! Current state:');
        console.log('  - captured:', captured);
        console.log('  - canvasRef.current:', !!canvasRef.current);
        console.log('  - videoRef.current:', !!videoRef.current);

        if (captured) {
            console.log('❌ Capture skipped - already captured');
            return;
        }

        if (canvasRef.current && videoRef.current) {
            console.log('✅ Starting capture process...');
            const ctx = canvasRef.current.getContext("2d");
            if (ctx) {
                console.log('✅ Got canvas context, drawing image...');
                // Draw the image to canvas
                ctx.drawImage(
                    videoRef.current,
                    0,
                    0,
                    dimensions.width,
                    dimensions.height
                );

                // Check for blur
                const blurVariance = detectBlur(canvasRef.current);
                // Dynamic threshold based on detection method
                const blurThreshold = window.cv ? 100 : 10; // Higher threshold for OpenCV, lower for fallback
                const isImageBlurry = blurVariance < blurThreshold;
                setIsBlurry(isImageBlurry);

                console.log(`✅ Image drawn to canvas, blur check: ${blurVariance.toFixed(1)} (threshold: ${blurThreshold}, blurry: ${isImageBlurry})`);

                if (debug) {
                    console.log('Blur detection - Variance:', blurVariance, 'Threshold:', blurThreshold, 'IsBlurry:', isImageBlurry);
                }

                // Convert canvas to blob and data URL for saving
                canvasRef.current.toBlob((blob) => {
                    if (blob) {
                        console.log('✅ Canvas converted to blob successfully');
                        const dataUrl = canvasRef.current!.toDataURL();
                        const imageData = { blob, dataUrl };
                        setCapturedImageData(imageData);

                        // Notify parent component about the captured image
                        if (onImageSaved && !isImageBlurry) {
                            console.log('✅ Calling onImageSaved callback');
                            onImageSaved(imageData);
                        } else if (isImageBlurry) {
                            console.log('⚠️ Image is blurry, not calling onImageSaved callback');
                        } else {
                            console.log('⚠️ No onImageSaved callback provided');
                        }
                    } else {
                        console.error('❌ Failed to convert canvas to blob');
                    }
                }, 'image/jpeg', 0.8);

                setCaptured(true);
                console.log("✅ Capture completed successfully - state updated to captured: true");
                if (debug === true) console.log("Image captured successfully");
            } else {
                console.error('❌ Canvas context not available');
            }
        } else {
            console.error('❌ Canvas or video ref not available:', {
                hasCanvas: !!canvasRef.current,
                hasVideo: !!videoRef.current
            });
        }
    }, [dimensions.width, dimensions.height, debug, captured, detectBlur, onImageSaved]);

    // Save image function
    const handleSaveImage = useCallback(async () => {
        console.log('🔄 handleSaveImage called');
        console.log('  - capturedImageData:', !!capturedImageData);
        console.log('  - isBlurry:', isBlurry);
        console.log('  - student:', student);
        console.log('  - data:', data);

        // Enhanced validation
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

        if (isBlurry) {
            console.log('⚠️ Image is blurry, asking user to retake');
            alert('Image is too blurry. Please try capturing again with better lighting and focus.');
            return;
        }

        try {
            // Convert blob to File object for the file handler
            const filename = generateFileName(student, 'id_card');
            console.log('📁 Generated filename:', filename);

            if (!capturedImageData.blob) {
                throw new Error('No blob data available for file creation');
            }

            const file = new File([capturedImageData.blob], filename, {
                type: 'image/jpeg'
            });

            console.log('📁 Created file object:', {
                name: file.name,
                size: file.size,
                type: file.type
            });

            // Upload using file handler
            const uploadedFile = await uploadFile(file, 'id_card');

            if (!uploadedFile) {
                throw new Error('File handler returned null - upload failed');
            }

            console.log('✅ File uploaded to handler:', uploadedFile.id);

            // Download the file automatically
            downloadFile(uploadedFile);

            // Update states
            setImageSaved(true);
            setFileSentToServer(true);

            // Call parent callback if provided
            if (onImageSaved) {
                console.log('📤 Calling onImageSaved callback');
                onImageSaved(capturedImageData);
            }

            // Trigger step completion after successful save
            setTimeout(() => {
                if (onStepComplete) {
                    console.log('✅ Calling onStepComplete');
                    onStepComplete();
                }
            }, 1000);

            if (debug) {
                console.log('✅ Image saved successfully using file handler', {
                    filename,
                    uploadedFile,
                    capturedImageData
                });
            }

        } catch (error) {
            console.error('❌ Error saving image with file handler:', error);
            console.error('Student data at time of error:', student);
            console.error('Data prop at time of error:', data);
            console.error('Captured image data at time of error:', {
                hasBlob: !!capturedImageData?.blob,
                hasDataUrl: !!capturedImageData?.dataUrl
            });
            alert(`Error saving image: ${error.message || error}. Please try again.`);
        }
    }, [capturedImageData, isBlurry, student, data, uploadFile, downloadFile, generateFileName, onImageSaved, onStepComplete, debug]);
// OpenCV.js detection functions
    const detectIDCardWithOpenCV = useCallback(() => {
        if (!cvLoaded || !window.cv || !videoRef.current || !detectionCanvasRef.current ||
            !isAutoDetectionEnabled || capturedRef.current || autoCapturingRef.current) {
            console.log('OpenCV detection skipped:', { cvLoaded, hasCV: !!window.cv, hasVideo: !!videoRef.current,
                hasCanvas: !!detectionCanvasRef.current, autoDetectionEnabled: isAutoDetectionEnabled,
                captured: capturedRef.current, autoCapturing: autoCapturingRef.current });
            return;
        }

        console.log('Running OpenCV detection...');
        console.log('=== DETECTION FRAME START ===');

        const video = videoRef.current;
        const canvas = detectionCanvasRef.current;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        canvas.width = video.videoWidth || dimensions.width;
        canvas.height = video.videoHeight || dimensions.height;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        console.log(`📐 Canvas size: ${canvas.width}x${canvas.height}`);

        try {
            const cv = window.cv;

            // Get image data from canvas
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            // Create OpenCV Mat from image data
            const src = cv.matFromImageData(imageData);
            const gray = new cv.Mat();
            const edges = new cv.Mat();
            const contours = new cv.MatVector();
            const hierarchy = new cv.Mat();

            // Convert to grayscale
            cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);

            // Apply Gaussian blur
            cv.GaussianBlur(gray, gray, new cv.Size(5, 5), 0);

            // Edge detection with Canny (more sensitive settings)
            cv.Canny(gray, edges, 25, 75); // Lowered from 50,150 to be more sensitive

            // Find contours
            cv.findContours(edges, contours, hierarchy, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);

            // REAL-TIME VALUES DISPLAY
            const totalContours = contours.size();
            const currentBlurVariance = detectBlur(canvas);
            const currentDetailLevel = calculateDetailLevel(canvas);

            console.log('📊 REAL-TIME VALUES:');
            console.log(`   Contours found: ${totalContours}`);
            console.log(`   Blur Variance: ${currentBlurVariance.toFixed(2)} (threshold: 50)`);
            console.log(`   Detail Level: ${currentDetailLevel.toFixed(2)} (threshold: 10)`);
            console.log(`   Is Sharp: ${currentBlurVariance > 50}`);
            console.log(`   Has Detail: ${currentDetailLevel > 10}`);

            let bestRect = null;
            let bestArea = 0;
            let rectangleCount = 0;

            // Process contours to find rectangular shapes
            console.log('🔍 ANALYZING RECTANGLES:');
            for (let i = 0; i < contours.size(); i++) {
                const contour = contours.get(i);
                const area = cv.contourArea(contour);

                // Filter by area - should be significant portion of image (very low threshold)
                const minArea = canvas.width * canvas.height * 0.001; // 0.1% - very permissive
                console.log(`   Contour ${i+1}: area=${area.toFixed(0)}, minRequired=${minArea.toFixed(0)}`);

                if (area < minArea) {
                    console.log(`     ❌ Too small (${area.toFixed(0)} < ${minArea.toFixed(0)})`);
                    continue;
                }

                // Approximate contour to polygon
                const approx = new cv.Mat();
                const peri = cv.arcLength(contour, true);
                cv.approxPolyDP(contour, approx, 0.02 * peri, true);

                console.log(`     ⚪ Corners: ${approx.rows}`);

                // Check if it's roughly rectangular (4 corners)
                if (approx.rows >= 4) {
                    rectangleCount++;
                    const rect = cv.boundingRect(contour);
                    const aspectRatio = rect.width / rect.height;

                    console.log(`     📐 Rectangle ${rectangleCount}: area=${area.toFixed(0)}, aspectRatio=${aspectRatio.toFixed(2)}, size=${rect.width}x${rect.height}`);

                    // ID card aspect ratio check (relaxed from 1.2-2.2 to 1.0-3.0)
                    if (aspectRatio > 1.0 && aspectRatio < 3.0 && area > bestArea) {
                        bestRect = rect;
                        bestArea = area;
                        console.log('     ✅ NEW BEST RECTANGLE!');
                    } else {
                        console.log(`     ❌ Rejected: aspectRatio=${aspectRatio.toFixed(2)} (need 1.0-3.0) or smaller area`);
                    }
                } else {
                    console.log(`     ❌ Not rectangular (${approx.rows} corners, need 4+)`);
                }

                approx.delete();
                contour.delete();
            }

            // Clean up OpenCV objects
            src.delete();
            gray.delete();
            edges.delete();
            contours.delete();
            hierarchy.delete();

            // Check if we found a valid ID card (very low threshold for testing)
            const minFinalArea = canvas.width * canvas.height * 0.005; // 0.5% - very permissive
            console.log(`Best rectangle - area: ${bestArea}, minRequired: ${minFinalArea.toFixed(0)}, hasRect: ${!!bestRect}`);

            if (bestRect && bestArea > minFinalArea) {
                // Check if rectangle is reasonably centered
                const centerX = canvas.width / 2;
                const centerY = canvas.height / 2;
                const rectCenterX = bestRect.x + bestRect.width / 2;
                const rectCenterY = bestRect.y + bestRect.height / 2;
                const distance = Math.sqrt(
                    Math.pow(rectCenterX - centerX, 2) + Math.pow(rectCenterY - centerY, 2)
                );

                const maxDistance = Math.min(canvas.width, canvas.height) * 0.5; // Relaxed from 0.4
                console.log(`Rectangle position - distance from center: ${distance.toFixed(1)}, maxAllowed: ${maxDistance.toFixed(1)}`);

                if (distance < maxDistance) {
                    // Check image quality before auto-capturing
                    const blurVariance = detectBlur(canvas);
                    const blurThreshold = 100; // Threshold for blur (higher = sharper)
                    const isSharp = blurVariance > blurThreshold;

                    // Check detail/contrast level
                    const detailLevel = calculateDetailLevel(canvas);
                    const detailThreshold = 20; // Threshold for detail (higher = more detail)
                    const hasGoodDetail = detailLevel > detailThreshold;

                    if (debug) {
                        console.log('Quality check - Blur:', blurVariance, 'Detail:', detailLevel, 'Sharp:', isSharp, 'GoodDetail:', hasGoodDetail);
                    }

                    if (isSharp && hasGoodDetail) {
                        console.log('🎉 ✅ HIGH QUALITY ID CARD DETECTED! AUTO-CAPTURING IN 1.5 SECONDS...');
                        setCardDetected(true);
                        setDetectionStatus("✓ High Quality ID Detected! Hold steady...");
                        setAutoCapturing(true);

                        // Slightly longer delay for better positioning
                        setTimeout(() => {
                            if (!capturedRef.current && autoCapturingRef.current) {
                                console.log('Triggering quality-based auto-capture!');
                                captureImage();
                            }
                            setAutoCapturing(false);
                            setCardDetected(false);
                        }, 1500); // Increased from 500ms to 1500ms
                    } else {
                        // Card detected but quality not good enough
                        setCardDetected(true);
                        if (!isSharp) {
                            setDetectionStatus("📸 ID detected - hold steady for sharper focus");
                        } else {
                            setDetectionStatus("📸 ID detected - improve lighting for more detail");
                        }

                        // Reset detection status after 2 seconds
                        setTimeout(() => {
                            setCardDetected(false);
                            setDetectionStatus("Position your ID card in the frame");
                        }, 2000);
                    }
                    return;
                }
            } else {
                console.log('Rectangle found but not centered properly or too small');
            }

            if (bestRect) {
                setCardDetected(false);
                setDetectionStatus("Adjust position - center your ID in the frame");
                console.log('⚠️ Rectangle detected but position/quality not good enough');
            } else {
                setCardDetected(false);
                setDetectionStatus("Position your ID card in the frame");
                console.log('❌ No suitable rectangles found - continue detection');
            }

            // FALLBACK: Two-phase detection system
            if (!bestRect) {
                console.log('🔄 Starting two-phase detection system...');
                const centerArea = ctx.getImageData(
                    canvas.width * 0.25,
                    canvas.height * 0.25,
                    canvas.width * 0.5,
                    canvas.height * 0.5
                );

                // Calculate average brightness in center area
                let totalBrightness = 0;
                const data = centerArea.data;
                for (let i = 0; i < data.length; i += 4) {
                    totalBrightness += (data[i] + data[i + 1] + data[i + 2]) / 3;
                }
                const avgBrightness = totalBrightness / (data.length / 4);

                console.log(`📱 Phase 1 - Motion detection: Center brightness: ${avgBrightness.toFixed(1)}`);

                // PHASE 1: Motion Detection - Start analyzing when object detected
                if (avgBrightness > 30 && avgBrightness < 220) {
                    console.log('✅ Phase 1: Object detected - Starting analysis...');
                    setIsAnalyzing(true);
                    setDetectionStatus("📱 Object detected - analyzing quality...");

                    // PHASE 2: Capture Criteria - Check size, blur, and steadiness
                    console.log('🎯 Phase 2 - Capture criteria check:');

                    // 1. Calculate object size coverage
                    let objectPixels = 0;
                    const totalPixels = data.length / 4;
                    for (let i = 0; i < data.length; i += 4) {
                        const pixelBrightness = (data[i] + data[i + 1] + data[i + 2]) / 3;
                        // Consider pixels that are significantly different from background
                        if (pixelBrightness > 50 && pixelBrightness < 200) {
                            objectPixels++;
                        }
                    }
                    const sizeCoverage = (objectPixels / totalPixels) * 100;
                    setObjectSizeCoverage(sizeCoverage);
                    console.log(`   📏 Size coverage: ${sizeCoverage.toFixed(1)}% (target: >50%)`);

                    // 2. Check blur ratio (sharpness) - more permissive
                    const blurVariance = detectBlur(canvas);
                    const isSharp = blurVariance > 25; // Lowered from 35 for easier capture
                    console.log(`   🔍 Blur variance: ${blurVariance.toFixed(1)} (sharp: ${isSharp})`);

                    // 3. Check detail level (hand steadiness indicator) - more permissive
                    const detailLevel = calculateDetailLevel(canvas);
                    const isSteady = detailLevel > 6; // Lowered from 8 for easier capture
                    console.log(`   🤚 Detail/steadiness: ${detailLevel.toFixed(1)} (steady: ${isSteady})`);

                    // CAPTURE DECISION: All criteria must be met
                    const sizeOK = sizeCoverage > 50; // Lowered from 65% for easier capture
                    const qualityOK = isSharp && isSteady;

                    console.log(`\n🎯 CAPTURE CRITERIA:`);
                    console.log(`   Size OK (>50%): ${sizeOK} - ${sizeCoverage.toFixed(1)}%`);
                    console.log(`   Sharp (blur>25): ${isSharp} - ${blurVariance.toFixed(1)}`);
                    console.log(`   Steady (detail>6): ${isSteady} - ${detailLevel.toFixed(1)}`);

                    if (sizeOK && qualityOK) {
                        // Increment steadiness count for final validation
                        setSteadinessCount(prev => {
                            const newCount = prev + 1;
                            console.log(`   📊 Steadiness count: ${newCount}/2 (was ${prev})`);

                            if (newCount >= 2) { // Reduced from 3 to 2 consecutive good readings
                                console.log('🎉 ✅ ALL CRITERIA MET! AUTO-CAPTURING IN 1.5 SECONDS...');
                                setCardDetected(true);
                                setDetectionStatus(`✓ Perfect! Size: ${sizeCoverage.toFixed(0)}%, Quality: Good - Hold steady...`);
                                setAutoCapturing(true);

                                setTimeout(() => {
                                    console.log('🚀 CAPTURE TIMEOUT FIRED! Checking conditions...');
                                    console.log('  - capturedRef.current:', capturedRef.current);
                                    console.log('  - autoCapturingRef.current:', autoCapturingRef.current);

                                    if (!capturedRef.current && autoCapturingRef.current) {
                                        console.log('✅ Conditions met - calling captureImage()');
                                        captureImage();
                                    } else {
                                        console.log('❌ Conditions not met for capture');
                                        if (capturedRef.current) console.log('   - Already captured');
                                        if (!autoCapturingRef.current) console.log('   - Not in auto-capturing state');
                                    }
                                    setAutoCapturing(false);
                                    setCardDetected(false);
                                    setIsAnalyzing(false);
                                    setSteadinessCount(0);
                                    console.log('🔄 Capture process completed, states reset');
                                }, 1500);

                                return 0; // Reset count after capture
                            }
                            return newCount;
                        });
                    } else {
                        // Reset steadiness count if criteria not met
                        setSteadinessCount(prev => {
                            if (prev > 0) {
                                console.log(`⚠️  STEADINESS RESET! Was ${prev}, now 0. Reason:`);
                                if (!sizeOK) console.log(`   - Size: ${sizeCoverage.toFixed(1)}% < 50%`);
                                if (!isSharp) console.log(`   - Blur: ${blurVariance.toFixed(1)} < 25`);
                                if (!isSteady) console.log(`   - Detail: ${detailLevel.toFixed(1)} < 6`);
                            }
                            return 0;
                        });

                        // Provide specific feedback
                        if (!sizeOK) {
                            setDetectionStatus(`📏 Move closer - ID coverage: ${sizeCoverage.toFixed(0)}% (need >50%)`);
                        } else if (!isSharp) {
                            setDetectionStatus(`🔍 Hold steady for sharper focus - blur: ${blurVariance.toFixed(0)}`);
                        } else if (!isSteady) {
                            setDetectionStatus(`🤚 Keep hand steady - detail: ${detailLevel.toFixed(0)}`);
                        }
                    }
                } else {
                    console.log(`❌ Phase 1: No object detected - brightness: ${avgBrightness.toFixed(1)}`);
                    setIsAnalyzing(false);
                    setDetectionStatus("Position your ID card in the frame");
                    setSteadinessCount(0);
                    setObjectSizeCoverage(0);
                }
            }

            console.log('=== DETECTION FRAME END ===\n');

        } catch (error) {
            console.error("OpenCV detection error:", error);
            setDetectionStatus("Detection error - please try manual capture");
        }
    }, [cvLoaded, isAutoDetectionEnabled, dimensions.width, dimensions.height, captureImage]);

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

    // Refs for avoiding circular dependencies in OpenCV detection
    const autoCapturingRef = useRef<boolean>(false);
    const capturedRef = useRef<boolean>(false);

    // Sync refs with state
    useEffect(() => { autoCapturingRef.current = autoCapturing; }, [autoCapturing]);
    useEffect(() => { capturedRef.current = captured; }, [captured]);

    // Simple fallback detection (basic motion/change detection)
    const simpleDetection = useCallback(() => {
        if (!videoRef.current || !detectionCanvasRef.current || !isAutoDetectionEnabled ||
            capturedRef.current || autoCapturingRef.current) {
            console.log('Simple detection skipped:', { hasVideo: !!videoRef.current,
                hasCanvas: !!detectionCanvasRef.current, autoDetectionEnabled: isAutoDetectionEnabled,
                captured: capturedRef.current, autoCapturing: autoCapturingRef.current });
            return;
        }

        console.log('Running simple detection...');

        // Simple detection: just look for any significant content in the center area
        const video = videoRef.current;
        const canvas = detectionCanvasRef.current;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        canvas.width = video.videoWidth || dimensions.width;
        canvas.height = video.videoHeight || dimensions.height;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Sample the center area for content
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const sampleWidth = canvas.width * 0.6;
        const sampleHeight = canvas.height * 0.4;

        const imageData = ctx.getImageData(
            centerX - sampleWidth / 2,
            centerY - sampleHeight / 2,
            sampleWidth,
            sampleHeight
        );

        // Simple brightness analysis to detect if something is in the frame
        let totalBrightness = 0;
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const brightness = (data[i] + data[i + 1] + data[i + 2]) / 3;
            totalBrightness += brightness;
        }

        const avgBrightness = totalBrightness / (data.length / 4);

        // If there's reasonable contrast/content, check quality before capturing
        if (avgBrightness > 60 && avgBrightness < 180) {
            // Check image quality
            const blurVariance = detectBlur(canvas);
            const blurThreshold = 50; // Lower threshold for simple detection
            const isSharp = blurVariance > blurThreshold;

            const detailLevel = calculateDetailLevel(canvas);
            const detailThreshold = 15; // Lower threshold for simple detection
            const hasGoodDetail = detailLevel > detailThreshold;

            if (debug) {
                console.log('Simple quality check - Blur:', blurVariance, 'Detail:', detailLevel, 'Sharp:', isSharp, 'GoodDetail:', hasGoodDetail);
            }

            if (isSharp && hasGoodDetail) {
                console.log('High quality content detected! Auto-capturing immediately...');
                setCardDetected(true);
                setDetectionStatus("✓ Good Quality Detected! Capturing...");
                setAutoCapturing(true);

                setTimeout(() => {
                    if (!capturedRef.current && autoCapturingRef.current) {
                        console.log('Triggering quality-based simple auto-capture!');
                        captureImage();
                    }
                    setAutoCapturing(false);
                    setCardDetected(false);
                }, 500);
            } else {
                // Content detected but quality not good enough
                setCardDetected(true);
                if (!isSharp) {
                    setDetectionStatus("📸 Content detected - hold steady for better focus");
                } else {
                    setDetectionStatus("📸 Content detected - improve lighting");
                }

                setTimeout(() => {
                    setCardDetected(false);
                    setDetectionStatus("Position your ID card in the frame");
                }, 2000);
            }
        } else {
            setCardDetected(false);
            setDetectionStatus("Position your ID card in the frame");
            setAutoCapturing(false);
        }
    }, [isAutoDetectionEnabled, dimensions.width, dimensions.height, captureImage]);

    // Auto-detection loop - use OpenCV if available, otherwise simple detection
    useEffect(() => {
        if (!isAutoDetectionEnabled || cameraLoading || captured) return;

        let intervalId: NodeJS.Timeout;

        const runDetection = () => {
            if (cvLoaded) {
                detectIDCardWithOpenCV();
            } else {
                simpleDetection();
            }
        };

        // Start detection after a short delay
        const timeoutId = setTimeout(() => {
            intervalId = setInterval(runDetection, 1000);
        }, 1000);

        return () => {
            clearTimeout(timeoutId);
            if (intervalId) {
                clearInterval(intervalId);
            }
        };
    }, [isAutoDetectionEnabled, cameraLoading, captured, cvLoaded]);

    return (
        <div style={{ maxWidth: '800px', margin: '0 auto', padding: '20px', fontFamily: 'system-ui, -apple-system, sans-serif' }}>
            {/* Main Header */}
            <div style={{ marginBottom: '24px' }}>
                <h2 style={{ fontSize: '24px', fontWeight: '600', color: '#1a1a1a', marginBottom: '12px' }}>
                    Capture ID Card Verification
                </h2>
                <p style={{ fontSize: '16px', color: '#6b7280', lineHeight: '1.5', margin: '0' }}>
                    We need this to verify your identity before proceeding with your course enrollment.
                    Please follow the steps below to capture a clear image of your ID card.
                </p>
            </div>

            {/* Instructions Section */}
            <div style={{
                background: '#f8fafc',
                borderRadius: '12px',
                padding: '24px',
                marginBottom: '32px',
                border: '1px solid #e2e8f0'
            }}>
                <div style={{ marginBottom: '24px' }}>
                    <h3 style={{ fontSize: '18px', fontWeight: '600', color: '#374151', marginBottom: '16px' }}>
                        Instructions:
                    </h3>

                    <div style={{ marginBottom: '16px', display: 'flex', alignItems: 'flex-start' }}>
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
                            Make sure your face is well-lit and clearly visible
                        </span>
                    </div>

                    <div style={{ marginBottom: '16px', display: 'flex', alignItems: 'flex-start' }}>
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
                            Hold your ID card to the camera and position it within the frame
                        </span>
                    </div>

                    <div style={{ marginBottom: '0', display: 'flex', alignItems: 'flex-start' }}>
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
                            Wait for automatic capture when your ID card is detected
                        </span>
                    </div>
                </div>
            </div>

            {/* Camera Section */}
            <div style={{
                background: '#ffffff',
                borderRadius: '12px',
                padding: '24px',
                border: '1px solid #e2e8f0',
                marginBottom: '24px'
            }}>
                {/* Status Display */}
                <div style={{ marginBottom: '20px', textAlign: 'center' }}>
                    <div style={{
                        display: 'inline-block',
                        padding: '8px 16px',
                        borderRadius: '8px',
                        background: captured
                            ? (isBlurry ? '#fef2f2' : imageSaved ? '#dcfce7' : '#e0f2fe')
                            : (cardDetected ? '#dcfce7' : '#fef3c7'),
                        color: captured
                            ? (isBlurry ? '#dc2626' : imageSaved ? '#166534' : '#0369a1')
                            : (cardDetected ? '#166534' : '#92400e'),
                        fontSize: '14px',
                        fontWeight: '500'
                    }}>
                        {captured
                            ? (isBlurry
                                ? '⚠️ Image is blurry - try again with better focus'
                                : (imageSaved
                                    ? '✅ Image saved successfully!'
                                    : '📷 Image captured - click Save to continue'))
                            : detectionStatus}
                    </div>
                </div>

                {/* Hidden detection canvas */}
                <canvas ref={detectionCanvasRef} style={{ display: "none" }} />

                {!cvLoaded && (
                    <div style={{ margin: '20px 0', textAlign: 'center', color: '#6b7280' }}>
                        <Spinner animation="border" size="sm" style={{ marginRight: '8px' }} />
                        Loading detection system...
                    </div>
                )}

                {cameraError ? (
                    <div style={{
                        textAlign: 'center',
                        padding: '40px 20px',
                        background: '#fef2f2',
                        borderRadius: '8px',
                        border: '1px solid #fecaca'
                    }}>
                        <p style={{ color: '#dc2626', marginBottom: '16px', fontSize: '16px' }}>
                            {cameraError}
                        </p>
                        <Button
                            onClick={() => {
                                setCameraError(null);
                                setCameraLoading(true);
                                enableCamera();
                            }}
                            style={{
                                background: '#3b82f6',
                                border: 'none',
                                borderRadius: '8px',
                                padding: '10px 20px',
                                fontWeight: '500'
                            }}
                        >
                            Try Again
                        </Button>
                    </div>
                ) : cameraLoading ? (
                    <div style={{
                        textAlign: 'center',
                        padding: '40px 20px',
                        background: '#f8fafc',
                        borderRadius: '8px',
                        border: '1px solid #e2e8f0'
                    }}>
                        <Spinner animation="border" style={{ marginBottom: '16px', color: '#3b82f6' }} />
                        <p style={{ color: '#6b7280', margin: '0', fontSize: '16px' }}>
                            Starting camera...
                        </p>
                    </div>
                ) : (
                    <div style={{ textAlign: 'center' }}>
                        {/* Canvas Component - Always rendered for ref availability */}
                        <div style={{
                            marginBottom: '20px',
                            display: captured ? 'block' : 'none'
                        }}>
                            <CanvasComponent
                                width={dimensions.width}
                                height={dimensions.height}
                                captured={captured}
                                canvasRef={canvasRef}
                                handleSaveImage={handleSaveImage}
                                handleWebcamReset={handleWebcamReset}
                                debug={debug}
                            />
                        </div>

                        {/* Video Component */}
                        {!captured && (
                            <div style={{ position: "relative", display: "inline-block" }}>
                                <VideoComponent
                                    width={dimensions.width}
                                    height={dimensions.height}
                                    videoRef={videoRef}
                                    captureImage={captureImage}
                                    handleWebcamReset={handleWebcamReset}
                                    debug={debug}
                                />

                                {/* ID Card Guide Overlay with Professional Brackets */}
                                {isAutoDetectionEnabled && (
                                    <div
                                        style={{
                                            position: "absolute",
                                            top: "40%",
                                            left: "50%",
                                            transform: "translate(-50%, -50%)",
                                            width: `${Math.min(dimensions.width - 80, 250)}px`,
                                            height: `${Math.min(dimensions.height - 80, 160)}px`,
                                            pointerEvents: "none",
                                            border: "none"
                                        }}
                                    >
                                        {/* Corner Brackets */}
                                        <div style={{
                                            position: "absolute",
                                            top: "0",
                                            left: "0",
                                            width: "60px",
                                            height: "60px",
                                            borderTop: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            borderLeft: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            transition: "border-color 0.3s ease"
                                        }}></div>

                                        <div style={{
                                            position: "absolute",
                                            top: "0",
                                            right: "0",
                                            width: "60px",
                                            height: "60px",
                                            borderTop: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            borderRight: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            transition: "border-color 0.3s ease"
                                        }}></div>

                                        <div style={{
                                            position: "absolute",
                                            bottom: "0",
                                            left: "0",
                                            width: "60px",
                                            height: "60px",
                                            borderBottom: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            borderLeft: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            transition: "border-color 0.3s ease"
                                        }}></div>

                                        <div style={{
                                            position: "absolute",
                                            bottom: "0",
                                            right: "0",
                                            width: "60px",
                                            height: "60px",
                                            borderBottom: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            borderRight: cardDetected ? "4px solid #10b981" : "3px solid #3b82f6",
                                            transition: "border-color 0.3s ease"
                                        }}></div>

                                        {/* Center Instruction Text */}
                                        <div style={{
                                            position: "absolute",
                                            top: "50%",
                                            left: "50%",
                                            transform: "translate(-50%, -50%)",
                                            background: cardDetected ? "rgba(16, 185, 129, 0.9)" : "rgba(59, 130, 246, 0.9)",
                                            color: "white",
                                            padding: "12px 20px",
                                            borderRadius: "24px",
                                            fontSize: "14px",
                                            fontWeight: "600",
                                            textAlign: "center",
                                            whiteSpace: "nowrap",
                                            transition: "background-color 0.3s ease",
                                            backdropFilter: "blur(4px)"
                                        }}>
                                            {autoCapturing ? "📸 CAPTURING..." : cardDetected ? "✅ ID DETECTED" : "🆔 POSITION ID CARD HERE"}
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

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
                                    color: '#6b7280',
                                    background: 'white',
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

            {/* Settings Panel */}
            <div style={{
                background: '#f8fafc',
                borderRadius: '8px',
                padding: '16px',
                border: '1px solid #e2e8f0'
            }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px', flexWrap: 'wrap' }}>
                    <Button
                        onClick={() => setIsAutoDetectionEnabled(!isAutoDetectionEnabled)}
                        variant={isAutoDetectionEnabled ? 'primary' : 'outline-secondary'}
                        size="sm"
                        style={{
                            borderRadius: '6px',
                            fontWeight: '500',
                            fontSize: '14px'
                        }}
                    >
                        {isAutoDetectionEnabled ? '🤖 Auto-Detection ON' : '👁️ Auto-Detection OFF'}
                    </Button>

                    <span style={{ fontSize: '14px', color: '#6b7280' }}>
                        OpenCV: {cvLoaded ? '✅ Ready' : '⏳ Loading'}
                    </span>

                    <span style={{ fontSize: '14px', color: '#6b7280' }}>
                        Camera: {cameraError ? '❌ Error' : cameraLoading ? '⏳ Loading' : '✅ Ready'}
                    </span>

                    {captured && (
                        <span style={{ fontSize: '14px', color: '#10b981', fontWeight: '500' }}>
                            ✅ Image Captured Successfully
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ImageIDCapture;
