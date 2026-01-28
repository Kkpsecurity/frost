import React, { useEffect, useState } from "react";
import { Alert, Spinner } from "react-bootstrap";
import { ClassDataShape, StudentType } from "../../../../../Config/types";
import CaptureDevices from "./CaptureDevices";
import { Icon } from "../../../../../Styles.ts";
import UploadInstructions from "../UploadInstructions";
import ValidationErrorBoundary from "../../../../../Shared/Components/Boundries/ValidationErrorBoundary";
import {
    StyledAlert,
    StyledButton,
    StyledContainer,
    PhotoTitle,
} from "../../../../../Styles.ts";
import { toast } from "react-toastify";
import { set } from "lodash";
import InstructionView from "../Views/InstructionView";
import UploadHeadshotView from "../Views/UploadHeadshotView";
import UploadIDcardView from "../Views/UploadIDcardView";

/**
 * The Captured Type
 */
type CaptureTypes = "upload" | "webcam" | "preview" | null;

interface CIDFVTYPE {
    data: ClassDataShape | null;
    student: StudentType;
    validations: {
        headshot: any;
        idcard: string | null;
    } | null;
    debug?: boolean;
    onComplete?: () => void;
}

const FILE_DEFAULT = "no-image";

// ValidationConfirmationView component for the final step
const ValidationConfirmationView: React.FC<{
    validations: any;
    student: StudentType;
    setCurrentStep: React.Dispatch<React.SetStateAction<number>>;
    onComplete?: () => void;
}> = ({ validations, student, setCurrentStep, onComplete }) => {
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleConfirmValidation = async () => {
        setIsSubmitting(true);
        try {
            // Advance to the parent onboarding Step 4 confirm screen.
            if (typeof onComplete === 'function') {
                onComplete();
                return;
            }

            toast.success("Validation completed successfully!");
        } catch (error) {
            toast.error("Failed to complete validation");
        } finally {
            setIsSubmitting(false);
        }
    };

    const getTodayKey = () => {
        try {
            return new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
        } catch {
            return 'monday';
        }
    };

    const getImageUrl = (imageData: any) => {
        if (!imageData) return null;
        if (typeof imageData === 'object' && !Array.isArray(imageData)) {
            const todayKey = getTodayKey();
            const todayUrl = imageData?.[todayKey];
            if (typeof todayUrl === 'string' && todayUrl && !todayUrl.includes(FILE_DEFAULT)) {
                return todayUrl;
            }
            const firstUrl = Object.values(imageData).find((v: any) => typeof v === 'string' && v && !v.includes(FILE_DEFAULT));
            return (firstUrl as string) || null;
        }
        if (Array.isArray(imageData)) {
            return imageData.find(url => url && !url.includes(FILE_DEFAULT));
        }
        return imageData && !imageData.includes(FILE_DEFAULT) ? imageData : null;
    };

    const headshotUrl = getImageUrl(validations?.headshot);
    const idCardUrl = getImageUrl(validations?.idcard);

    const hasMissingImages = !headshotUrl || !idCardUrl;

    return (
        <div className="container-fluid">
            <div className="row justify-content-center">
                <div className="col-lg-10">
                    {/* Show alert if images are missing */}
                    {hasMissingImages && (
                        <div
                            className="alert alert-warning mb-3"
                            style={{
                                background: "rgba(255, 193, 7, 0.1)",
                                border: "1px solid #ffc107",
                                color: "#ffc107",
                                borderRadius: "0.5rem"
                            }}
                        >
                            <strong>⚠️ Missing Images</strong>
                            <p className="mb-0 mt-1">
                                {!idCardUrl && !headshotUrl
                                    ? "Both ID card and headshot images are missing. Please upload them to continue."
                                    : !idCardUrl
                                    ? "ID card image is missing. Please upload it to continue."
                                    : "Headshot image is missing. Please upload it to continue."}
                            </p>
                        </div>
                    )}

                    {/* Compact Header */}
                    <div className="text-center mb-2">
                        <PhotoTitle style={{ fontSize: "1rem", margin: "0 0 0.25rem" }}>
                            Review Your Validation Images
                        </PhotoTitle>
                        <p style={{ color: "#95a5a6", fontSize: "0.85rem", margin: "0" }}>
                            Please review both images below and confirm they are clear and valid.
                        </p>
                    </div>

                    {/* Compact Image Review */}
                    <div
                        className="d-flex flex-column flex-md-row gap-2 p-2 mb-2"
                        style={{
                            background: "#34495e",
                            borderRadius: "0.5rem",
                            minHeight: "160px"
                        }}
                    >
                        {/* ID Card Section */}
                        <div className="flex-fill">
                            <h6 style={{ color: "#3498db", margin: "0 0 0.5rem", fontSize: "0.9rem" }}>ID Card</h6>
                            <div className="d-flex justify-content-center">
                                {idCardUrl ? (
                                    <img
                                        src={idCardUrl}
                                        alt="ID Card"
                                        style={{
                                            maxWidth: "100%",
                                            maxHeight: "120px",
                                            borderRadius: "0.375rem",
                                            border: "2px solid #3498db"
                                        }}
                                    />
                                ) : (
                                    <div
                                        className="d-flex flex-column align-items-center justify-content-center"
                                        style={{
                                            color: "#e74c3c",
                                            minHeight: "120px",
                                            background: "rgba(231, 76, 60, 0.1)",
                                            borderRadius: "0.375rem",
                                            width: "100%",
                                            padding: "1rem"
                                        }}
                                    >
                                        <div style={{ fontSize: "2rem", marginBottom: "0.5rem" }}>📷</div>
                                        <div style={{ fontSize: "0.9rem", fontWeight: 600, marginBottom: "0.25rem" }}>
                                            No ID card image
                                        </div>
                                        <div style={{ fontSize: "0.85rem", color: "#95a5a6" }}>
                                            Click "Back to ID Card" to upload
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Headshot Section */}
                        <div className="flex-fill">
                            <h6 style={{ color: "#3498db", margin: "0 0 0.5rem", fontSize: "0.9rem" }}>Headshot</h6>
                            <div className="d-flex justify-content-center">
                                {headshotUrl ? (
                                    <img
                                        src={headshotUrl}
                                        alt="Headshot"
                                        style={{
                                            maxWidth: "100%",
                                            maxHeight: "120px",
                                            borderRadius: "0.375rem",
                                            border: "2px solid #3498db"
                                        }}
                                    />
                                ) : (
                                    <div
                                        className="d-flex flex-column align-items-center justify-content-center"
                                        style={{
                                            color: "#e74c3c",
                                            minHeight: "120px",
                                            background: "rgba(231, 76, 60, 0.1)",
                                            borderRadius: "0.375rem",
                                            width: "100%",
                                            padding: "1rem"
                                        }}
                                    >
                                        <div style={{ fontSize: "2rem", marginBottom: "0.5rem" }}>🤳</div>
                                        <div style={{ fontSize: "0.9rem", fontWeight: 600, marginBottom: "0.25rem" }}>
                                            No headshot image
                                        </div>
                                        <div style={{ fontSize: "0.85rem", color: "#95a5a6" }}>
                                            {!idCardUrl ? "Upload ID card first" : "Click button to go back and upload"}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Compact Navigation */}
                    <div className="d-flex justify-content-between gap-2">
                        <StyledButton
                            onClick={() => setCurrentStep(idCardUrl ? 3 : 2)}
                            style={{ padding: "0.5rem 1rem" }}
                        >
                            {idCardUrl ? "← Back to Headshot" : "← Back to ID Card"}
                        </StyledButton>
                        <StyledButton
                            onClick={handleConfirmValidation}
                            disabled={isSubmitting || !headshotUrl || !idCardUrl}
                            title={
                                !headshotUrl || !idCardUrl
                                    ? "Please upload both images before confirming"
                                    : "Confirm and continue"
                            }
                            style={{
                                background: !headshotUrl || !idCardUrl ? "#6c757d" : "#2ecc71",
                                color: "#ffffff",
                                padding: "0.5rem 1rem",
                                cursor: !headshotUrl || !idCardUrl ? "not-allowed" : "pointer"
                            }}
                        >
                            {isSubmitting ? "Submitting..." : "Confirm Validation"}
                        </StyledButton>
                    </div>
                </div>
            </div>
        </div>
    );
};

const CaptureIDForValidation: React.FC<CIDFVTYPE> = ({
    data,
    student,
    validations,
    debug = false,
    onComplete,
}) => {
    const [currentStep, setCurrentStep] = useState(1);
    const [validationMessage, setValidationMessage] = useState<string | null>(null);

    const [autoAdvanceTo, setAutoAdvanceTo] = useState<number | null>(null);
    const [countdown, setCountdown] = useState<number>(0);
    const [pendingType, setPendingType] = useState<'idcard' | 'headshot' | null>(null);

    /**
     * Create a state to hold the capture type
     * webcam | upload
     */
    const [showCaptureType, setShowCaptureType] = useState<CaptureTypes | null>(
        null
    );

    /**
     * Check if the image has a default image
     */
    const hasDefault = (fileUrl) => {
        if (!fileUrl) {
            return true; // Consider null/undefined as having default
        }
        if (Array.isArray(fileUrl)) {
            return fileUrl.some((url) => url && url.includes(FILE_DEFAULT));
        }
        return fileUrl.includes(FILE_DEFAULT);
    };

    const getTodayKey = () => {
        try {
            return new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
        } catch {
            return 'monday';
        }
    };

    const getIdCardUrl = (): string | null => {
        const url = validations?.idcard;
        if (!url) return null;
        if (typeof url === 'string' && url.length > 0 && !url.includes(FILE_DEFAULT)) return url;
        return null;
    };

    const getHeadshotUrl = (): string | null => {
        const headshot = validations?.headshot;
        if (!headshot) return null;

        if (typeof headshot === 'string') {
            return headshot && !headshot.includes(FILE_DEFAULT) ? headshot : null;
        }
        if (Array.isArray(headshot)) {
            const found = headshot.find((v) => typeof v === 'string' && v && !v.includes(FILE_DEFAULT));
            return found || null;
        }
        if (typeof headshot === 'object') {
            const todayKey = getTodayKey();
            const todayUrl = headshot?.[todayKey];
            if (typeof todayUrl === 'string' && todayUrl && !todayUrl.includes(FILE_DEFAULT)) return todayUrl;
            const firstUrl = Object.values(headshot).find((v: any) => typeof v === 'string' && v && !v.includes(FILE_DEFAULT));
            return (firstUrl as string) || null;
        }

        return null;
    };

    const idCardUrl = getIdCardUrl();
    const headshotUrl = getHeadshotUrl();

    const beginCountdown = (nextStep: number, type: 'idcard' | 'headshot') => {
        setPendingType(type);
        setAutoAdvanceTo(nextStep);
        setCountdown(5);
    };

    // Start the 5s countdown when an upload completes.
    useEffect(() => {
        if (!autoAdvanceTo || countdown <= 0) return;

        const timer = setTimeout(() => {
            setCountdown((prev) => prev - 1);
        }, 1000);

        return () => clearTimeout(timer);
    }, [autoAdvanceTo, countdown]);

    // When countdown hits 0, advance.
    useEffect(() => {
        if (!autoAdvanceTo) return;
        if (countdown > 0) return;

        setCurrentStep(autoAdvanceTo);
        setAutoAdvanceTo(null);
        setPendingType(null);
        setValidationMessage(null);
        setShowCaptureType(null);
    }, [autoAdvanceTo, countdown]);

    // If we refresh and the poll already has the images, move forward automatically.
    // BUT: Don't auto-advance if we're in the middle of a countdown (let the countdown complete naturally)
    useEffect(() => {
        if (autoAdvanceTo) {
            // Don't auto-advance if countdown is active
            return;
        }

        if (currentStep === 2 && idCardUrl) {
            // ID is already done, go to headshot
            setCurrentStep(3);
        }
        if (currentStep === 3 && headshotUrl) {
            // Headshot already done for today, go to confirm
            setCurrentStep(4);
        }
    }, [currentStep, idCardUrl, headshotUrl, autoAdvanceTo]);

    const WaitingPanel: React.FC<{ type: 'idcard' | 'headshot' }> = ({ type }) => {
        const title = type === 'idcard' ? 'ID Card Uploaded' : 'Headshot Uploaded';
        const subtitle = type === 'idcard'
            ? 'Waiting validation… then moving to Headshot.'
            : 'Waiting validation… then moving to Review & Confirm.';

        const imageUrl = type === 'idcard' ? idCardUrl : headshotUrl;

        return (
            <div className="container-fluid">
                <div className="row justify-content-center">
                    <div className="col-lg-10">
                        <div
                            className="p-3"
                            style={{
                                background: "#34495e",
                                borderRadius: "0.5rem",
                                minHeight: "220px",
                                color: "#ecf0f1",
                            }}
                        >
                            <div className="d-flex align-items-center gap-2 mb-2" style={{ fontSize: '1rem' }}>
                                <span style={{ color: '#2ecc71', fontWeight: 700 }}>✅</span>
                                <span style={{ fontWeight: 600 }}>{title}</span>
                            </div>

                            <div style={{ color: '#95a5a6', marginBottom: '12px' }}>{subtitle}</div>

                            {imageUrl ? (
                                <div className="d-flex justify-content-center mb-3">
                                    <img
                                        src={imageUrl}
                                        alt={type === 'idcard' ? 'ID Card' : 'Headshot'}
                                        style={{
                                            maxWidth: '100%',
                                            maxHeight: '220px',
                                            borderRadius: '0.375rem',
                                            border: '2px solid #3498db',
                                        }}
                                    />
                                </div>
                            ) : (
                                <div
                                    className="d-flex align-items-center justify-content-center mb-3"
                                    style={{
                                        minHeight: '120px',
                                        background: 'rgba(255,255,255,0.05)',
                                        borderRadius: '0.375rem',
                                        border: '1px solid rgba(255,255,255,0.1)',
                                        color: '#95a5a6',
                                    }}
                                >
                                    Image will appear after poll refresh
                                </div>
                            )}

                            <div className="text-center" style={{ fontSize: '0.95rem' }}>
                                Auto-advancing in <span style={{ color: '#3498db', fontWeight: 700 }}>{countdown}</span>s
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    const renderStepContent = () => {
        window.scrollTo(0, 0);
        switch (currentStep) {
            case 1:
                return (
                    <InstructionView
                        validations={validations}
                        setCurrentStep={setCurrentStep}
                    />
                );
            case 2:
                if (pendingType === 'idcard' && autoAdvanceTo) {
                    return <WaitingPanel type="idcard" />;
                }
                return (
                    <UploadIDcardView
                        data={data}
                        student={student}
                        validations={validations}
                        showCaptureType={showCaptureType}
                        setShowCaptureType={setShowCaptureType}
                        setCurrentStep={setCurrentStep}
                        currentStep={currentStep}
                        onUploaded={() => {
                            setValidationMessage('ID uploaded. Waiting validation…');
                            beginCountdown(3, 'idcard');
                        }}
                        debug={debug}
                    />
                );
            case 3:
                if (pendingType === 'headshot' && autoAdvanceTo) {
                    return <WaitingPanel type="headshot" />;
                }
                return (
                    <UploadHeadshotView
                        data={data}
                        student={student}
                        validations={validations}
                        showCaptureType={showCaptureType}
                        setShowCaptureType={setShowCaptureType}
                        setCurrentStep={setCurrentStep}
                        currentStep={currentStep}
                        isImageSet={() => true}
                        onUploaded={() => {
                            setValidationMessage('Headshot uploaded. Waiting validation…');
                            beginCountdown(4, 'headshot');
                        }}
                        debug={debug}
                    />
                );
            case 4:
                return (
                    <ValidationConfirmationView
                        validations={validations}
                        student={student}
                        setCurrentStep={setCurrentStep}
                        onComplete={onComplete}
                    />
                );
            default:
                return (
                    <div className="d-flex justify-content-center align-items-center" style={{ minHeight: "400px" }}>
                        <Spinner animation="border" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </Spinner>
                    </div>
                );
        }
    };

    return (
        <StyledContainer>
            <ValidationErrorBoundary>
                {validationMessage && (
                    <div className="col-md-12">
                        <h3 className="text-white">{validationMessage}</h3>
                    </div>
                )}
                {renderStepContent()}
            </ValidationErrorBoundary>
        </StyledContainer>
    );
};

export default CaptureIDForValidation;
