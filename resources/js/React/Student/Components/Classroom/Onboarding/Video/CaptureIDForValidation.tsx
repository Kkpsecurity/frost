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
        headshot: string | string[] | null;
        idcard: string | null;
    } | null;
    debug?: boolean;
}

const FILE_DEFAULT = "no-image";

// ValidationConfirmationView component for the final step
const ValidationConfirmationView: React.FC<{
    validations: any;
    student: StudentType;
    setCurrentStep: React.Dispatch<React.SetStateAction<number>>;
}> = ({ validations, student, setCurrentStep }) => {
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleConfirmValidation = async () => {
        setIsSubmitting(true);
        try {
            // Here you would submit the validation confirmation
            // For now, we'll just show success
            toast.success("Validation completed successfully!");
            setTimeout(() => {
                // This could trigger the onComplete callback
                console.log("Validation process completed");
            }, 2000);
        } catch (error) {
            toast.error("Failed to complete validation");
        } finally {
            setIsSubmitting(false);
        }
    };

    const getImageUrl = (imageData: string | string[] | null) => {
        if (!imageData) return null;
        if (Array.isArray(imageData)) {
            return imageData.find(url => url && !url.includes(FILE_DEFAULT));
        }
        return imageData && !imageData.includes(FILE_DEFAULT) ? imageData : null;
    };

    const headshotUrl = getImageUrl(validations?.headshot);
    const idCardUrl = getImageUrl(validations?.idcard);

    return (
        <div className="container-fluid">
            <div className="row justify-content-center">
                <div className="col-lg-10">
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
                                        className="d-flex align-items-center justify-content-center"
                                        style={{
                                            color: "#e74c3c",
                                            minHeight: "120px",
                                            background: "rgba(231, 76, 60, 0.1)",
                                            borderRadius: "0.375rem",
                                            width: "100%"
                                        }}
                                    >
                                        No ID card uploaded
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
                                        className="d-flex align-items-center justify-content-center"
                                        style={{
                                            color: "#e74c3c",
                                            minHeight: "120px",
                                            background: "rgba(231, 76, 60, 0.1)",
                                            borderRadius: "0.375rem",
                                            width: "100%"
                                        }}
                                    >
                                        No headshot uploaded
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Compact Navigation */}
                    <div className="d-flex justify-content-between">
                        <StyledButton
                            onClick={() => setCurrentStep(2)}
                            style={{ padding: "0.5rem 1rem" }}
                        >
                            ← Back to ID Card
                        </StyledButton>
                        <StyledButton
                            onClick={handleConfirmValidation}
                            disabled={isSubmitting || !headshotUrl || !idCardUrl}
                            style={{
                                background: !headshotUrl || !idCardUrl ? "#6c757d" : "#2ecc71",
                                color: "#ffffff",
                                padding: "0.5rem 1rem"
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
}) => {
    const [currentStep, setCurrentStep] = useState(1);
    const [validationMessage, setValidationMessage] = useState<string | null>(
        null
    );

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

    // useEffect to handle step flow - always go through all steps sequentially
    useEffect(() => {
        // Start with instructions, then go through ID card, headshot, and confirmation
        if (currentStep === 1) {
            // Stay on instructions until user clicks next
            return;
        }
        // Let users progress through each step manually - no auto-skipping
    }, [validations]);

    /**
     * If we are in step two check if set if so ether move to ext step or go back to step one
     */
    useEffect(() => {
        // Check for headshot validation and manage the workflow accordingly
        if (currentStep === 2) {
            if (!hasDefault(validations.headshot)) {
                setValidationMessage("Validating Headshot Please wait.");
                setCurrentStep(null); // triggers loader
                setTimeout(() => {
                    setCurrentStep(3);
                    setValidationMessage(null);
                }, 2000);
            }
        } else {
            setValidationMessage(
                "Please upload a Headshot that matches your ID card."
            );
        }

        // Check for ID card validation and manage the workflow accordingly
        if (currentStep === 3) {
            if (!hasDefault(validations.idcard)) {
                setValidationMessage("Validating ID Card Please wait.");
                setCurrentStep(null); // triggers loader
                setTimeout(() => {
                    setCurrentStep(1);
                    setValidationMessage(null);
                }, 2000);
            }
        } else {
            setValidationMessage("Please upload a copy of your ID.");
        }
    }, [currentStep, validations]); // Include validations in the dependency array

    const isImageSet = () => {
        switch (currentStep) {
            case 1:
                return true;
            case 2:
                return validations?.headshot === null;
            case 3:
                return validations?.idcard === null;
            default:
                return false;
        }
    };

    const renderStepContent = () => {
        window.scrollTo(0, 0);
        const getToNextStep = () => {
            setCurrentStep(currentStep + 1);
        };

        switch (currentStep) {
            case 1:
                return (
                    <InstructionView
                        validations={validations}
                        setCurrentStep={setCurrentStep}
                    />
                );
            case 2:
                return (
                    <UploadIDcardView
                        data={data}
                        student={student}
                        validations={validations}
                        showCaptureType={showCaptureType}
                        setShowCaptureType={setShowCaptureType}
                        setCurrentStep={setCurrentStep}
                        currentStep={currentStep}
                        debug={debug}
                    />
                );
            case 3:
                return (
                    <UploadHeadshotView
                        data={data}
                        student={student}
                        validations={validations}
                        showCaptureType={showCaptureType}
                        setShowCaptureType={setShowCaptureType}
                        setCurrentStep={setCurrentStep}
                        currentStep={currentStep}
                        isImageSet={isImageSet}
                        debug={debug}
                    />
                );
            case 4:
                return (
                    <ValidationConfirmationView
                        validations={validations}
                        student={student}
                        setCurrentStep={setCurrentStep}
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
