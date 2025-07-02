import React, { useState, Suspense, useEffect } from "react";
import { Button, Card, Modal } from "react-bootstrap";
import MessageConsole from "./Partials/MessageConsole";
import TextHidden from "../../../../Components/FormElements/TextHidden";
import BootstrapSwitchButton from "bootstrap-switch-button-react";
import Textarea from "../../../../Components/FormElements/Textarea";
import Loader from "../../../../Components/Widgets/Loader";
import PhotoPreview from "./PhotoPreview";
import Select from "../../../../Components/FormElements/Select";
import { MessageConsoleType, StudentType } from "../../../../Config/types";
import { useForm } from "react-hook-form";
import { ValidateOptions } from "yup/lib/types";

interface ValidationFormProps {
    onSubmit: any;
    student: StudentType | null;
    validationMessage: MessageConsoleType | null;
    validationMode: "validate" | "decline" | "pending";
    setValidationMode: React.Dispatch<
        React.SetStateAction<"validate" | "decline" | "pending">
    >;
    courseDateId: number;
    validationStep: "begin" | "idcard" | "headshot" | "completed";
    setValidationStep: React.Dispatch<
        React.SetStateAction<"begin" | "idcard" | "headshot" | "completed">
    >;
    handleDeletePhoto: () => void;
    handleClose: () => void;
    validations: StudentType["validations"];
    headShotStatus: boolean | null;
    idCardStatus: boolean | null;
    getValidationTypes: () => { value: string; text: string }[];
    getDeclineTypes: () => { value: string; text: string }[];
    getHeadshotTypes: () => { value: string; text: string }[];
}

const ValidationForm = ({
    onSubmit,
    student,
    validations,
    validationMessage,
    validationMode,
    setValidationMode,
    courseDateId,
    handleDeletePhoto,
    handleClose,
    validationStep,
    setValidationStep,
    headShotStatus,
    idCardStatus,
    getValidationTypes,
    getDeclineTypes,
    getHeadshotTypes,
}: ValidationFormProps) => {
    interface OptionsType {
        text: string;
        value: string;
    }

    const handleSwitchChange = () => {
        const newMode = validationMode === "validate" ? "decline" : "validate";
        setValidationMode(newMode);
    };

    const [options, setOptions] = useState<OptionsType[]>([]);

    const validationTypes = getValidationTypes();
    const declineTypes = getDeclineTypes();
    const headshotTypes = getHeadshotTypes();

    useEffect(() => {
        let newOptions: OptionsType[] = [];

        if (validationMode === "validate") {
            if (validationStep === "headshot") {
                newOptions = [
                    { text: "Headshot Valid", value: "headshot_valid" },
                ];
            } else {
                newOptions = validationTypes.map((type) => ({
                    text: type.text,
                    value: type.value,
                }));
            }
        } else {
            if (validationStep === "headshot") {
                newOptions = headshotTypes.map((type) => ({
                    text: type.text,
                    value: type.value,
                }));
            } else {
                newOptions = declineTypes.map((type) => ({
                    text: type.text,
                    value: type.value,
                }));
            }
        }

        setOptions(newOptions);
    }, [validationMode, validationStep, validationTypes, headshotTypes, declineTypes]);

    return (
        <form onSubmit={onSubmit}>
            <Card>
                <Card.Header>
                    <Card.Title>
                        Validate Student:{" "}
                        <b>
                            {student?.fname} {student?.lname}
                        </b>
                    </Card.Title>
                </Card.Header>

                <Card.Body>
                    {validationMessage && (
                        <MessageConsole
                            status={validationMessage.status}
                            message={validationMessage.message}
                        />
                    )}

                    <TextHidden id="course_date_id" value={courseDateId} />
                    <TextHidden id="validationMode" value={validationMode} />

                    <div className="form-group">
                        <div className="row">
                            <div className="col-md-7">
                                <label htmlFor="validation_type">
                                    Validation Type: Select the Validation Type
                                </label>
                            </div>
                            <div className="col-md-5">
                                <BootstrapSwitchButton
                                    onChange={handleSwitchChange}
                                    offlabel="Decline"
                                    onlabel="Validate"
                                    checked={validationMode === "validate"}
                                    onstyle="success"
                                    offstyle="danger"
                                    style="w-100"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        className={`form-group validation-container ${validationMode}`}
                    >
                        <Select
                            id="validate_type"
                            options={options}
                            title={
                                validationMode === "validate"
                                    ? "Validate"
                                    : "Decline"
                            }
                            required={false}
                            value={""}
                        />
                    </div>

                    {validationMode === "decline" && (
                        <Textarea
                            id="message"
                            title="Message"
                            required={false}
                            value={""}
                        />
                    )}

                    <div className="form-group preview">
                        <Suspense fallback={<Loader />}>
                            <div className="text-center">
                                {headShotStatus === true && (
                                    <li>Headshot not Uploaded</li>
                                )}
                                
                                {idCardStatus === true && (
                                    <li>ID Card not Uploaded</li>
                                )}
                            </div>
                            <PhotoPreview
                                validations={validations}
                                idCardStatus={idCardStatus}
                                headShotStatus={headShotStatus}
                                validationMode={validationMode}
                                validationStep={validationStep}
                                setValidationStep={setValidationStep}
                                handleDeletePhoto={handleDeletePhoto}
                            />
                        </Suspense>
                    </div>
                </Card.Body>
            </Card>
        </form>
    );
};

export default ValidationForm;
