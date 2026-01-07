import React, { useState, useEffect } from "react";
import { Button } from "react-bootstrap";
import { toast } from "react-toastify";

import ImageIDCapture from "./Webcam/ImageIDCapture";
import ImageIDUpload from "./Upload/ImageIDUpload";

import { StudentType } from "@/React/Student/types/students.types";

interface CaptureDevicesProps {
    data: any | null;
    photoType: string; // 'headshot' or 'idcard'
    student: StudentType; // StudentType is defined in types.ts
    validations: {
        headshot: string | string[] | null;
        idcard: string | null;
    } | null;
    showCaptureType: CaptureTypes;
    setShowCaptureType: React.Dispatch<React.SetStateAction<CaptureTypes>>;
    setCurrentStep: React.Dispatch<React.SetStateAction<number>>;
    currentStep: number;
    onUploaded?: (photoType: 'idcard' | 'headshot') => void;
    debug?: boolean;
}

/**
 * The Captured Type
 */
type CaptureTypes = "upload" | "webcam" | "preview" | null;

/**
 * Component Icons
 */
const headShotIcon = "/assets/img/icon/headshot.png";
const idCardIcon = "/assets/img/icon/idcard.png";

const CaptureDevices: React.FC<CaptureDevicesProps> = ({
    data,
    photoType,
    student,
    validations,
    showCaptureType,
    setShowCaptureType,
    setCurrentStep,
    currentStep,
    onUploaded,
    debug = false,
}) => {
    if (debug === true)
        console.log("CaptureDevices: ", photoType, student, validations);

    /**
     * The captured images state for headshot and idcard
     * hold the url string for each image
     */
    const [headshot, setHeadshot] = useState<string | null>(null);
    const [idcard, setIdcard] = useState<string | null>(null);

    /**
     * Modern Take Photo Button
     */
    const TakePhoto: React.FC<{
        photoType: string;
        headshot: string | null;
        idcard: string | null;
        showCaptureType: CaptureTypes;
    }> = ({ photoType, headshot, idcard, showCaptureType }) => {
        const isActive = showCaptureType === "webcam";
        let isDisabled = false;

        // Check for the headshot case
        if (photoType === "headshot") {
            isDisabled = !!(headshot && headshot !== "" && typeof headshot === 'string' && !headshot.includes("no-image"));
        }

        // Check for the ID card case
        if (photoType === "idcard") {
            isDisabled = !!(idcard && idcard !== "" && typeof idcard === 'string' && !idcard.includes("no-image"));
        }

        return (
            <button
                onClick={!isActive ? () => setShowCaptureType("webcam") : undefined}
                disabled={isDisabled}
                style={{
                    background: isActive ? '#10b981' : 'rgba(255,255,255,0.1)',
                    border: isActive ? '1px solid #10b981' : '1px solid rgba(255,255,255,0.2)',
                    borderRadius: '8px',
                    padding: '8px 16px',
                    fontSize: '13px',
                    fontWeight: '500',
                    color: isActive ? '#ffffff' : '#374151',
                    cursor: isDisabled ? 'not-allowed' : 'pointer',
                    opacity: isDisabled ? 0.5 : 1,
                    transition: 'all 0.2s ease',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '6px',
                    minWidth: '90px',
                    justifyContent: 'center',
                    backdropFilter: 'blur(4px)'
                }}
                onMouseEnter={(e) => {
                    if (!isDisabled && !isActive) {
                        const target = e.target as HTMLButtonElement;
                        target.style.background = 'rgba(255,255,255,0.15)';
                        target.style.borderColor = 'rgba(255,255,255,0.3)';
                    }
                }}
                onMouseLeave={(e) => {
                    if (!isDisabled && !isActive) {
                        const target = e.target as HTMLButtonElement;
                        target.style.background = 'rgba(255,255,255,0.1)';
                        target.style.borderColor = 'rgba(255,255,255,0.2)';
                    }
                }}
            >
                📸 Take Photo
            </button>
        );
    };


    /**
     * Modern Upload Photo Button
     */
    const UploadPhoto: React.FC<{
        photoType: string;
        headshot: string | null;
        idcard: string | null;
        showCaptureType: CaptureTypes;
    }> = ({ photoType, headshot, idcard, showCaptureType }) => {
        const isActive = showCaptureType === "upload";
        let isDisabled = false;

        // Check for the headshot case
        if (photoType === "headshot") {
            isDisabled = !!(headshot && headshot !== "" && typeof headshot === 'string' && !headshot.includes("no-image"));
        }

        // Check for the ID card case
        if (photoType === "idcard") {
            isDisabled = !!(idcard && idcard !== "" && typeof idcard === 'string' && !idcard.includes("no-image"));
        }

        return (
            <button
                onClick={!isActive ? () => setShowCaptureType("upload") : undefined}
                disabled={isDisabled}
                style={{
                    background: isActive ? '#3b82f6' : 'rgba(255,255,255,0.1)',
                    border: isActive ? '1px solid #3b82f6' : '1px solid rgba(255,255,255,0.2)',
                    borderRadius: '8px',
                    padding: '8px 16px',
                    fontSize: '13px',
                    fontWeight: '500',
                    color: isActive ? '#ffffff' : '#374151',
                    cursor: isDisabled ? 'not-allowed' : 'pointer',
                    opacity: isDisabled ? 0.5 : 1,
                    transition: 'all 0.2s ease',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '6px',
                    minWidth: '95px',
                    justifyContent: 'center',
                    backdropFilter: 'blur(4px)'
                }}
                onMouseEnter={(e) => {
                    if (!isDisabled && !isActive) {
                        const target = e.target as HTMLButtonElement;
                        target.style.background = 'rgba(255,255,255,0.15)';
                        target.style.borderColor = 'rgba(255,255,255,0.3)';
                    }
                }}
                onMouseLeave={(e) => {
                    if (!isDisabled && !isActive) {
                        const target = e.target as HTMLButtonElement;
                        target.style.background = 'rgba(255,255,255,0.1)';
                        target.style.borderColor = 'rgba(255,255,255,0.2)';
                    }
                }}
            >
                📁 Upload Photo
            </button>
        );
    };


    useEffect(() => {
        if (!validations) {
            console.log("Validations not available.");
            return;
        }

        // Handle headshots
        const headshots = validations.headshot;
        let headshotToSet = null;

        if (headshots && typeof headshots === 'string') {
            headshotToSet = headshots;
        } else if (Array.isArray(headshots) && headshots.length > 0) {
            headshotToSet = headshots[0];
        } else if (headshots && typeof headshots === 'object') {
            // Backend poll provides { monday: url|null, ... }
            try {
                const todayKey = new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
                const headshotMap = headshots as unknown as Record<string, unknown>;
                const todayUrl = headshotMap[todayKey];
                if (typeof todayUrl === 'string' && todayUrl.length > 0) {
                    headshotToSet = todayUrl;
                } else {
                    const firstUrl = Object.values(headshotMap).find((v: any) => typeof v === 'string' && v.length > 0);
                    headshotToSet = (firstUrl as string) || null;
                }
            } catch {
                // ignore
            }
        }

        if (headshotToSet) {
            setHeadshot(headshotToSet);
        }

        // Handle ID card
        setIdcard(validations.idcard);

    }, [validations, student, setHeadshot, setIdcard]);

    return (
        <div style={{
            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            borderRadius: '12px',
            padding: '0',
            overflow: 'hidden',
            boxShadow: '0 8px 32px rgba(0,0,0,0.1)'
        }}>
            {/* Modern Header with Icon */}
            <div style={{
                background: 'rgba(255,255,255,0.95)',
                backdropFilter: 'blur(10px)',
                padding: '16px 24px',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                borderBottom: '1px solid rgba(255,255,255,0.2)'
            }}>
                {/* Compact Icon */}
                <div style={{
                    width: '32px',
                    height: '32px',
                    borderRadius: '8px',
                    background: photoType === "headshot" ? '#3b82f6' : '#10b981',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '16px'
                }}>
                    {photoType === "headshot" ? '👤' : '🆔'}
                </div>

                {/* Title and Description */}
                <div style={{ flex: 1 }}>
                    <h3 style={{
                        margin: '0',
                        fontSize: '18px',
                        fontWeight: '600',
                        color: '#1f2937',
                        lineHeight: '1.2'
                    }}>
                        {photoType === "headshot" ? "Student Head Shot" : "Student ID Card"}
                    </h3>
                    <p style={{
                        margin: '2px 0 0 0',
                        fontSize: '13px',
                        color: '#6b7280',
                        lineHeight: '1.3'
                    }}>
                        {photoType === "headshot"
                            ? "Take or upload a clear photo of yourself"
                            : "Capture or upload your government-issued ID"
                        }
                    </p>
                </div>

                {/* Compact Action Buttons */}
                <div style={{
                    display: 'flex',
                    gap: '8px'
                }}>
                    <TakePhoto
                        photoType={photoType}
                        headshot={headshot}
                        idcard={idcard}
                        showCaptureType={showCaptureType}
                    />
                    <UploadPhoto
                        photoType={photoType}
                        headshot={headshot}
                        idcard={idcard}
                        showCaptureType={showCaptureType}
                    />
                </div>
            </div>

            {/* Main Content Area */}
            <div style={{
                padding: '24px',
                background: 'rgba(255,255,255,0.02)'
            }}>
                {showCaptureType === "webcam" ? (
                    <ImageIDCapture
                        data={data}
                        student={student}
                        photoType={photoType}
                        headshot={headshot}
                        idcard={idcard}
                        onStepComplete={() => {
                            toast.success(
                                photoType === "headshot"
                                    ? "Headshot uploaded successfully"
                                    : "ID card uploaded successfully"
                            );
                            setShowCaptureType(null);
                            if (onUploaded) {
                                onUploaded(photoType === 'headshot' ? 'headshot' : 'idcard');
                                return;
                            }
                            if (photoType === "idcard") setCurrentStep(3);
                            if (photoType === "headshot") setCurrentStep(4);
                        }}
                        debug={debug}
                    />
                ) : showCaptureType === "upload" ? (
                    <ImageIDUpload
                        data={data}
                        student={student}
                        photoType={photoType}
                        headshot={headshot}
                        idcard={idcard}
                        onStepComplete={() => {
                            toast.success(
                                photoType === "headshot"
                                    ? "Headshot uploaded successfully"
                                    : "ID card uploaded successfully"
                            );
                            setShowCaptureType(null);
                            if (onUploaded) {
                                onUploaded(photoType === 'headshot' ? 'headshot' : 'idcard');
                                return;
                            }
                            if (photoType === "idcard") setCurrentStep(3);
                            if (photoType === "headshot") setCurrentStep(4);
                        }}
                        debug={debug}
                    />
                ) : showCaptureType === "preview" ? (
                    <div style={{
                        textAlign: 'center',
                        padding: '40px 20px',
                        color: 'rgba(255,255,255,0.9)'
                    }}>
                        <div style={{
                            fontSize: '48px',
                            marginBottom: '16px'
                        }}>
                            ✓
                        </div>
                        <h4 style={{
                            fontSize: '18px',
                            fontWeight: '600',
                            margin: '0 0 8px 0',
                            color: 'rgba(255,255,255,0.95)'
                        }}>
                            Image captured successfully!
                        </h4>
                    </div>
                ) : (
                    <div style={{
                        textAlign: 'center',
                        padding: '40px 20px',
                        color: 'rgba(255,255,255,0.9)'
                    }}>
                        <div style={{
                            width: '64px',
                            height: '64px',
                            borderRadius: '50%',
                            background: 'rgba(255,255,255,0.1)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            margin: '0 auto 16px',
                            fontSize: '24px'
                        }}>
                            📸
                        </div>
                        <h4 style={{
                            fontSize: '18px',
                            fontWeight: '600',
                            margin: '0 0 8px 0',
                            color: 'rgba(255,255,255,0.95)'
                        }}>
                            Ready to capture your {photoType === "headshot" ? "photo" : "ID card"}?
                        </h4>
                        <p style={{
                            fontSize: '14px',
                            margin: '0',
                            color: 'rgba(255,255,255,0.7)',
                            lineHeight: '1.4'
                        }}>
                            Choose "Take Photo" to use your camera or "Upload Photo" to select from your device
                        </p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CaptureDevices;
