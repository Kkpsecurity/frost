import React, { useState, useEffect } from "react";
import { Button } from "react-bootstrap";

import ImageIDCapture from "./ImageIDCapture";
import ImageIDUpload from "./ImageIDUpload";
import CapturedPreview from "./CapturedPreview";

import extractFileName from "../../../../../Helpers/extractFileName";

import { ClassDataShape, StudentType } from "../../../../../Config/types";

import {
    StyledCaputureDevices,
    StyledCardHeader,
    StyledRow,
    StyledCol,
    StyledDeviceTitle,
    StyledButtonGroup,
} from "../../../../../Styles/StyledCapturedDevice.styled";
import PageLoader from "../../../../../Components/Widgets/PageLoader";

interface CaptureDevicesProps {
    data: ClassDataShape | null;
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
    currentStep: number,
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
     *
     * WebCam Capture Button
     * @returns
     */
    const TakePhoto = ({ photoType, headshot, idcard, showCaptureType }) => {
        const isActive = showCaptureType === "webcam";
        let isDisabled = false;
    
        // Check for the headshot case
        if (photoType === "headshot") {
            // The button should be disabled if a valid headshot exists and it's not a placeholder.
            isDisabled = headshot && !headshot.includes("no-image");
        }
    
        // Check for the ID card case
        if (photoType === "idcard") {
            // The button should be disabled if a valid ID card exists and it's not a placeholder.
            isDisabled = idcard && !idcard.includes("no-image");
        }
    
        return (
            <Button
                className={`btn btn-md m-1 ${isActive ? "active" : ""}`}
                variant={isActive ? "success" : "outline-primary"}
                onClick={!isActive ? () => setShowCaptureType("webcam") : undefined}
                disabled={isDisabled}
            >
                Take Photo
            </Button>
        );
    };
    

    /**
     *  Upload Photo Button
     * @param param0
     * @returns
     */
    const UploadPhoto = ({ photoType, headshot, idcard, showCaptureType }) => {
        const isActive = showCaptureType === "upload";
        let isDisabled = false;
    
        // Check for the headshot case
        if (photoType === "headshot") {
            // The button should be disabled if a valid headshot exists and it's not a placeholder.
            isDisabled = headshot && !headshot.includes("no-image");
        }
    
        // Check for the ID card case
        if (photoType === "idcard") {
            // The button should be disabled if a valid ID card exists and it's not a placeholder.
            isDisabled = idcard && !idcard.includes("no-image");
        }
    
        return (
            <Button
                className={`btn btn-md m-1 ${isActive ? "active" : ""}`}
                variant={isActive ? "success" : "secondary"}
                onClick={!isActive ? () => setShowCaptureType("upload") : undefined}
                disabled={isDisabled}
            >
                Upload Photo
            </Button>
        );
    };
    

    useEffect(() => {
        const today = new Date();
        const todayISO = today.toISOString().slice(0, 10); // 'YYYY-MM-DD'
        const dayOfWeek = today.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
    
        if (!validations || !student.currentStudentUnit) {
            console.log("Validations or student unit not available.");
            return;
        }
    
        // Handle headshots based on the day of the week
        const headshots = validations.headshot;
        let headshotToSet = null;
    
        if (headshots && typeof headshots === 'object' && !(headshots instanceof Array)) {
            if (headshots[dayOfWeek]) {
                headshotToSet = headshots[dayOfWeek];
                if (typeof headshotToSet === 'string' && headshotToSet.includes("no-image")) {
                    console.log("No valid headshot for today, need to upload.");
                    headshotToSet = null;
                }
            }
        } else if (typeof headshots === 'string') {
            headshotToSet = headshots; // Assume it's valid unless checked elsewhere
        }
    
        if (headshotToSet) {
            setHeadshot(headshotToSet);
        }
    
        // After headshot handling, process ID card if headshot is set
        setIdcard(validations.idcard);        
    
    }, [validations, student, setHeadshot, setIdcard]);
    
    console.log("Validations22222", idcard);

    return (
        <StyledCaputureDevices>
            <StyledCardHeader>
                {photoType === "headshot"
                    ? "Student Head Shot"
                    : "Student ID Card"}
            </StyledCardHeader>

            <StyledRow>
                <StyledCol className="text-center" flexBasis="15%">
                    <StyledDeviceTitle>
                        {photoType === "headshot" ? (
                            <img
                                src={headShotIcon}
                                alt="Head Shot"
                                width="180px"
                            />
                        ) : (
                            <img src={idCardIcon} alt="ID Card" width="180px" />
                        )}
                    </StyledDeviceTitle>
                </StyledCol>

                <StyledCol flexBasis="85%">
                    <StyledButtonGroup>
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
                    </StyledButtonGroup>
                </StyledCol>
            </StyledRow>

            <StyledRow id="viewport">
                <StyledCol>
                    {showCaptureType === "webcam" ? (
                        <ImageIDCapture
                            data={data}
                            student={student}
                            photoType={photoType}
                            headshot={headshot}
                            idcard={idcard}
                            debug={debug}
                        />
                    ) : showCaptureType === "upload" ? (
                        <ImageIDUpload
                            data={data}
                            student={student}
                            photoType={photoType}
                            headshot={headshot}
                            idcard={idcard}
                            debug={debug}
                        />
                    ) : (
                        <CapturedPreview
                            photoType={photoType}
                            headshot={headshot}
                            idcard={idcard}
                        />
                    )}
                </StyledCol>
            </StyledRow>
        </StyledCaputureDevices>
    );
};

export default CaptureDevices;
