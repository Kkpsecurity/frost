import React from "react";
import UploadInstructions from "../UploadInstructions";
import { StyledButton } from "../../../../../Styles.ts";

interface InstructionViewProps {
    validations: any;
    setCurrentStep: React.Dispatch<React.SetStateAction<number>>;
}

const InstructionView: React.FC<InstructionViewProps> = ({
    validations,
    setCurrentStep,
}) => {
    return (
        <div className="container">
            <div className="row">
                <div className="col-12">
                    <UploadInstructions validations={validations} />
                    <div className="text-center mt-4">
                        <StyledButton
                            onClick={() => setCurrentStep(2)}
                            className="btn btn-primary btn-lg"
                        >
                            Begin Verification <i className="fas fa-arrow-right ms-2"></i>
                        </StyledButton>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default InstructionView;
