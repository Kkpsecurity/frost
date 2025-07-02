import React, { useEffect, useState } from "react";
import ImageBox from "./Partials/ImageBox";
import Loader from "../../../../Components/Widgets/Loader";

import {
    StyledContainer,
    StyledRow,
    StyledCol,
    ValidationStatus,
} from "../../../../Styles/PhotoPreviewStyle";
import { StudentType } from "../../../../Config/types";
import { Alert } from "react-bootstrap";

const H2Styles: React.CSSProperties = {
    padding: "none",
    textAlign: "start",
    fontSize: "2rem",
    textTransform: "capitalize",
};

const dimensions: { width: string; height: string } = {
    width: "400px",
    height: "300px",
};

interface PhotoPreviewStyledProps {
    validations: StudentType["validations"];
    headShotStatus: boolean | null;
    idCardStatus: boolean | null;
    validationMode: "validate" | "decline" | "pending";
    handleDeletePhoto: () => void;
    validationStep: "begin" | "idcard" | "headshot" | "completed";
    setValidationStep: React.Dispatch<
        React.SetStateAction<"begin" | "idcard" | "headshot" | "completed">
    >;
}

const defaultImage: string = "no-image.jpg";

const PhotoPreviewStyled = ({
    validations,
    headShotStatus,
    idCardStatus,
    validationMode,
    handleDeletePhoto,
    validationStep,
    setValidationStep,
}: PhotoPreviewStyledProps) => {
    const [validationMessage, setValidationMessage] = useState("");
    const [isDefaultImage, setIsDefaultImage] = useState(false);
    const [currentImage, setCurrentImage] = useState("");
    const [validationReady, setValidationReady] = useState(false);

    const validate_idcard = (idcard: string) => {
        if (!idcard || idcard.endsWith(defaultImage)) {
            return false;
        }
        return true;
    };

    const validate_headshot = (headshots: Array<string>) => {
        if (!headshots || headshots.length === 0) {
            return false;
        }

        const latestDate = Object.keys(headshots).sort().pop(); // Get the latest date key
        const latestHeadshot = headshots[latestDate || ""];

        if (!latestHeadshot || latestHeadshot.endsWith(defaultImage)) {
            return false;
        }

        return true;
    };

    const beginValidation = () => {
        // are both photos uploaded
        if (
            validate_idcard(validations.idcard ?? "") &&
            validate_headshot(
                Array.isArray(validations.headshot) ? validations.headshot : []
            ) // Fix: Pass validations.headshot as an array
        ) {
            setValidationReady(true);
            setValidationStep("idcard");
        } else {
            setValidationMessage(
                "Both photos need to be uploaded before you can validate."
            );
        }
    };

    useEffect(() => {
        const idCardValidation = () => {
            if (idCardStatus) {
                setValidationMessage("ID Card validated successfully.");
                setValidationStep("headshot");
            } else {
                setValidationMessage("ID Card needs to be validated.");
            }
        };

        const headshotValidation = () => {
            if (headShotStatus) {
                setValidationMessage("Headshot validated successfully.");
                setValidationStep("completed");
            } else {
                setValidationMessage("Headshot needs to be validated.");
            }
        };

        const handleValidationStep = () => {
            switch (validationStep) {
                case "begin":
                    beginValidation();
                    break;
                case "idcard":
                    idCardValidation();
                    break;
                case "headshot":
                    headshotValidation();
                    break;
                case "completed":
                    setValidationMessage("Validation completed successfully.");
                    break;
                default:
                    break;
            }
        };

        handleValidationStep();
    }, [
        validationStep,
        validations,
        idCardStatus,
        headShotStatus,
        setValidationStep,
    ]);

    return (
        <StyledContainer>
            <StyledRow>
                <StyledCol md={12}>
                    <h2 style={H2Styles}>
                        Validate: <b>{validationStep}</b>
                    </h2>

                    {validationReady &&
                        (validationStep === "completed" ? (
                            <div className="text-center">
                                <h3>Validation Completed</h3>
                                <p>
                                    All validations are completed for this
                                    student.
                                </p>
                                <button
                                    className="btn btn-danger"
                                    onClick={() => setValidationStep("begin")}
                                >
                                    Reset Validations
                                </button>
                            </div>
                        ) : (
                            <center>
                                <ImageBox
                                    image={currentImage}
                                    hasImage={!isDefaultImage}
                                    imgType={validationStep}
                                    validationMode={validationMode}
                                />
                            </center>
                        )) || (
                            <Alert variant="warning">
                                <p>
                                    Both photos need to be uploaded before you
                                    can validate.
                                </p>
                            </Alert>
                        )}

                    <ValidationStatus
                        status={
                            validationStep === "idcard"
                                ? idCardStatus
                                : headShotStatus
                        }
                    >
                        <p>{validationMessage}</p>
                    </ValidationStatus>
                </StyledCol>
            </StyledRow>
        </StyledContainer>
    );
};

export default PhotoPreviewStyled;
