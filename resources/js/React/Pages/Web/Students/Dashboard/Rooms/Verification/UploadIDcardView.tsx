import React from 'react'
import { StyledContainer } from '../../../../../../Styles/PhotoPreviewStyle';
import { PhotoTitle, StyledButton } from '../../../../../../Styles/CaptureStyled.styled';
import CaptureDevices from '../../Video/CaptureDevices';


const UploadIDcardView = ({
    data,
    student,
    validations,
    showCaptureType,
    setShowCaptureType,
    setCurrentStep,
    currentStep,
    debug,
}) => {
    return (
        <div className="container">
            <div className="row">
                {/* Left Side: Capture Component */}
                <div className="col-md-6">
                    <StyledContainer>
                        <PhotoTitle>Take a Photo of Your ID Card</PhotoTitle>
                        <CaptureDevices
                            data={data}
                            photoType="idcard"
                            student={student}
                            validations={validations}
                            showCaptureType={showCaptureType}
                            setShowCaptureType={setShowCaptureType}
                            setCurrentStep={setCurrentStep}
                            currentStep={currentStep}
                            debug={debug}
                        />
                        <div>
                            <StyledButton
                                onClick={() => setCurrentStep(2)}
                                className="m-2"
                            >
                                Back
                            </StyledButton>
                            {/* Add the Next button if necessary */}
                        </div>
                    </StyledContainer>
                </div>

                {/* Right Side: Instructional Message Based on Capture Type */}
                <div className="col-md-6 d-flex align-items-center justify-content-center">
                    <blockquote
                        className="alert"
                        style={{
                            borderLeft: "0.25rem solid #f689d3",
                        }}
                    >
                        {showCaptureType === "webcam" ? (
                            <p>
                                Position the ID card in front of the webcam.
                                Ensure it's clearly visible and well-lit. Follow
                                the on-screen instructions to capture the image.
                            </p>
                        ) : showCaptureType === "upload" ? (
                            <p>
                                Please upload a clear, recent photo of your ID
                                card. Ensure the entire card is visible and the
                                details are legible.
                            </p>
                        ) : (
                            <p>
                                Choose 'Capture' to take a photo of your ID card
                                with your webcam, or 'Upload' to use a photo
                                from your device. This is necessary for ID card
                                validation.
                            </p>
                        )}
                    </blockquote>
                </div>
            </div>
        </div>
    );
};


export default UploadIDcardView