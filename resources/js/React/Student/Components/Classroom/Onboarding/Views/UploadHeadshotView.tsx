import { ClassDataShape } from "../../../../../../Config/types";
import { PhotoTitle, StyledButton, StyledContainer } from "../../../../../Styles.ts";
import CaptureDevices from "../Video/CaptureDevices";


interface UploadHeadshotViewProps {
    data: ClassDataShape;
    student: any;
    validations: any;
    showCaptureType: any;
    setShowCaptureType: any;
    setCurrentStep: any;
    currentStep: any;
    isImageSet: any;
    onUploaded?: () => void;
    debug?: boolean;
}

const UploadHeadshotView = ({
    data,
    student,
    validations,
    showCaptureType,
    setShowCaptureType,
    setCurrentStep,
    currentStep,
    isImageSet,
    onUploaded,
    debug,
}: UploadHeadshotViewProps) => {
    return (
        <div className="container-fluid">
            <div className="row justify-content-center">
                <div className="col-lg-10">
                    {/* Compact Header */}
                    <div className="text-center mb-3">
                        <PhotoTitle style={{ fontSize: "1.1rem", margin: "0 0 0.5rem" }}>
                            Take a Photo of Your Headshot
                        </PhotoTitle>
                    </div>

                    {/* Compact Main Content */}
                    <div
                        className="d-flex flex-column flex-md-row gap-3 p-3"
                        style={{
                            background: "#34495e",
                            borderRadius: "0.5rem",
                            minHeight: "250px"
                        }}
                    >
                        {/* Left: Capture Controls */}
                        <div className="flex-fill">
                            <CaptureDevices
                                data={data}
                                photoType="headshot"
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
                                    "Position yourself in front of the webcam ensuring your face is clearly visible and well-lit."
                                ) : showCaptureType === "upload" ? (
                                    "Upload a clear, recent headshot from your device. Make sure the photo is well-lit."
                                ) : (
                                    "Choose 'Take Photo' to use your webcam, or 'Upload Photo' to select a file from your device."
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Compact Navigation */}
                    <div className="d-flex justify-content-between mt-2">
                        <StyledButton
                            onClick={() => setCurrentStep(2)}
                            style={{ padding: "0.5rem 1rem" }}
                        >
                            ← Back to ID Card
                        </StyledButton>
                        <div style={{ color: '#95a5a6', alignSelf: 'center', fontSize: '0.9rem' }}>
                            Uploading your headshot will continue automatically
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UploadHeadshotView;
