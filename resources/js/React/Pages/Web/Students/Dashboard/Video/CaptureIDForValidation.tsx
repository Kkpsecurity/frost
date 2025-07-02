import React, { useEffect, useState } from "react";
import { Alert } from "react-bootstrap";
import { ClassDataShape, StudentType } from "../../../../../Config/types";
import CaptureDevices from "./CaptureDevices";
import { Icon } from "../../../../../Styles/StyleCaptureID.styled";
import UploadInstructions from "../Rooms/Verification/UploadInstructions";
import ValidationErrorBoundary from "../Rooms/Verification/ValidationErrorBoundry";
import {
    StyledAlert,
    StyledButton,
    StyledContainer,
    PhotoTitle,
} from "../../../../../Styles/CaptureStyled.styled";
import { toast } from "react-toastify";
import Loader from "../../../../../Components/Widgets/Loader";
import { set } from "lodash";
import InstructionView from "../Rooms/Verification/InstructionView";
import UploadHeadshotView from "../Rooms/Verification/UploadHeadshotView";
import UploadIDcardView from "../Rooms/Verification/UploadIDcardView";

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
        if (Array.isArray(fileUrl)) {
            return fileUrl.some((url) => url.includes(FILE_DEFAULT));
        }
        return fileUrl.includes(FILE_DEFAULT);
    };

    // useEffect to check the ID card image
    useEffect(() => {
        // Only proceed to next steps if images are valid (not "no-image")
        if (validations) {
            const isHeadshotValid =
                validations.headshot && !hasDefault(validations.headshot);
            const isIdCardValid =
                validations.idcard && !hasDefault(validations.idcard);

            if (currentStep === 1) {
                return;
            } else {
                if (!isIdCardValid) {
                    setCurrentStep(3); // Go to ID card capture if invalid
                } else if (!isHeadshotValid) {
                    setCurrentStep(2); // Go to headshot capture if invalid
                }
            }
        }
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
            case 3:
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
            default:
                return <Loader />;
        }
    };

    return (
        <StyledContainer>
            <ValidationErrorBoundary>
                {validationMessage && (
                    <div className="col-md-12">
                        <h3>{validationMessage}</h3>
                    </div>
                )}
                {renderStepContent()}
            </ValidationErrorBoundary>
        </StyledContainer>
    );
};

export default CaptureIDForValidation;
