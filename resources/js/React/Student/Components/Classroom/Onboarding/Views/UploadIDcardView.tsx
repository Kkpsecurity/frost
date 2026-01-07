import React from 'react'
import { StyledContainer, PhotoTitle, StyledButton } from '../../../../../Styles.ts';
import CaptureDevices from '../Video/CaptureDevices';


const UploadIDcardView = ({
    data,
    student,
    validations,
    showCaptureType,
    setShowCaptureType,
    setCurrentStep,
    currentStep,
    onUploaded,
    debug,
}) => {
    return (
        <div className="container-fluid">
            <div className="row justify-content-center">
                <div className="col-lg-10">
                    {/* Compact Header */}
                    <div className="text-center mb-2">
                        <PhotoTitle style={{ fontSize: "1rem", margin: "0 0 0.25rem" }}>
                            Take a Photo of Your ID Card
                        </PhotoTitle>
                    </div>

                    {/* Compact Main Content */}
                    <div
                        className="d-flex flex-column flex-md-row gap-2 p-2"
                        style={{
                            background: "#34495e",
                            borderRadius: "0.5rem",
                            minHeight: "180px"
                        }}
                    >
                        {/* Left: Capture Controls */}
                        <div className="flex-fill">
                            <CaptureDevices
                                data={data}
                                photoType="idcard"
                                student={student}
                                validations={validations}
                                showCaptureType={showCaptureType}
                                setShowCaptureType={setShowCaptureType}
                                setCurrentStep={setCurrentStep}
                                currentStep={currentStep}
                                onUploaded={() => onUploaded?.()}
                                debug={debug}
                            />
                        </div>

                        {/* Right: Instructions */}
                        <div className="d-flex align-items-center" style={{ flex: "0 0 300px" }}>
                            <div
                                className="p-3"
                                style={{
                                    background: "rgba(52, 152, 219, 0.1)",
                                    borderRadius: "0.375rem",
                                    borderLeft: "3px solid #3498db",
                                    fontSize: "0.9rem",
                                    color: "#ecf0f1"
                                }}
                            >
                                {showCaptureType === "webcam" ? (
                                    "Position your ID card in front of the webcam. Ensure it's clearly visible and well-lit."
                                ) : showCaptureType === "upload" ? (
                                    "Upload a clear photo of your ID card. Ensure the entire card is visible and details are legible."
                                ) : (
                                    "Choose 'Take Photo' to use your webcam, or 'Upload Photo' to select a file from your device."
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Compact Navigation */}
                    <div className="d-flex justify-content-between mt-2">
                        <StyledButton
                            onClick={() => setCurrentStep(1)}
                            style={{ padding: "0.5rem 1rem" }}
                        >
                            ← Back
                        </StyledButton>
                        <div style={{ color: '#95a5a6', alignSelf: 'center', fontSize: '0.9rem' }}>
                            Uploading your ID will continue automatically
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};


export default UploadIDcardView
