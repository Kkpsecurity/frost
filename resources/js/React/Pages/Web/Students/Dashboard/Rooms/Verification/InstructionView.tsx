import React from "react";
import UploadInstructions from "./UploadInstructions";
import { StyledAlert } from "../../../../../../Styles/OfflineDashboardStyles.styled";
import { StyledButton } from "../../../../../../Styles/CaptureStyled.styled";

const arrowPoint = "https://img.icons8.com/ios/50/000000/long-arrow-right.png";

const InstructionView = ({ validations, setCurrentStep }) => {
    return (
        <div>
            <StyledAlert>
                <UploadInstructions validations={validations} />
            </StyledAlert>
            CONTINUE{" "}
            <img
                src={arrowPoint}
                style={{
                    marginRight: "20px",
                }}
            />
            <StyledButton onClick={() => setCurrentStep(2)}>Next</StyledButton>
        </div>
    );
};

export default InstructionView;
