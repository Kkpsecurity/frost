import { ClassDataShape } from "../../../../../../Config/types";
import { PhotoTitle, StyledButton } from "../../../../../../Styles/CaptureStyled.styled";
import { StyledContainer } from "../../../../../../Styles/PhotoPreviewStyle";
import CaptureDevices from "../../Video/CaptureDevices";


interface UploadHeadshotViewProps {
    data: ClassDataShape;
    student: any;
    validations: any;
    showCaptureType: any;
    setShowCaptureType: any;
    setCurrentStep: any;
    currentStep: any;
    isImageSet: any;
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
    debug,
}: UploadHeadshotViewProps) => {
    return (
        <div className="container">
            <div className="row">
                {/* Left Side: Capture Component */}
                <div className="col-md-6">
                    <StyledContainer>
                        <PhotoTitle>Take a Photo of Your Headshot</PhotoTitle>
                        <CaptureDevices
                            data={data}
                            photoType="headshot"
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
                                onClick={() => setCurrentStep(1)}
                                className="m-2"
                            >
                                Back
                            </StyledButton>
                            <StyledButton
                                onClick={() => setCurrentStep(3)}
                                disabled={isImageSet()}
                                style={{
                                    marginRight: "20px",
                                    disabled: isImageSet() ? "disabled" : "",
                                }}
                            >
                                Next
                            </StyledButton>
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
                                Position yourself in front of the webcam
                                ensuring your face is clearly visible and
                                well-lit. Follow the on-screen instructions to
                                capture your headshot.
                            </p>
                        ) : showCaptureType === "upload" ? (
                            <p>
                                Please upload a clear, recent headshot from your
                                device. Make sure the photo is well-lit and your
                                face is unobstructed and visible.
                            </p>
                        ) : (
                            <p>
                                Choose 'Capture' to take a headshot with your
                                webcam, or 'Upload' to use a photo from your
                                device. This is necessary for your headshot and
                                State ID validation.
                            </p>
                        )}
                    </blockquote>
                </div>
            </div>
        </div>
    );
};

export default UploadHeadshotView;