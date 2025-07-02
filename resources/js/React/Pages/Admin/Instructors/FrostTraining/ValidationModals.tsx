import React, { Suspense, useEffect, useState } from "react";

// Types
import {
    CourseMeetingShape,
    StudentType,
    MessageConsoleType,
} from "../../../../Config/types";
import {
    useDeletePhoto,
    validateStudentHook,
} from "../../../../Hooks/Admin/useInstructorHooks";
import { FormProvider, useForm } from "react-hook-form";
import { Button, Modal } from "react-bootstrap";
import TextHidden from "../../../../Components/FormElements/TextHidden";
import BootstrapSwitchButton from "bootstrap-switch-button-react";
import Select from "../../../../Components/FormElements/Select";
import Textarea from "../../../../Components/FormElements/Textarea";
import Loader from "../../../../Components/Widgets/Loader";
import PhotoPreview from "./PhotoPreview";
import "./PreviewLayer.css";

import * as yup from "yup";
import MessageConsole from "./Partials/MessageConsole";
import ValidationForm from "./ValidationForm";
import useValidationHook from "../../../../Hooks/Admin/useValidationHook";

interface ValidationModalProps {
    ClassData: CourseMeetingShape;
    courseDateId: number;
    studentAuthId: number | null;
    student: StudentType | null;
    setHeadshot: (headshot: string) => void;
    headshot?: string | string[] | null;
    setIdcard: (idcard: string) => void;
    idcard?: string | null;
    show: boolean;
}

const defaultImage = "no-image.jpg";

const ValidationModals: React.FC<ValidationModalProps> = ({
    ClassData,
    courseDateId,
    studentAuthId,
    student,
    setHeadshot,
    headshot,
    setIdcard,
    idcard,
    show
}) => {

    const { validations } = student || {};
    
    const classData = ClassData;
    const {     
        loading,
        showPreview,
        validationMessage,
        validationStep,
        setValidationStep,
        validationMode,
        idCardStatus,
        headShotStatus,
        setValidationMode,
        setValidationMessage,
        handlePhotoValidation,
        handleDeletePhoto,
        getValidationTypes,
        getHeadshotTypes,
        getDeclineTypes,
        handleClose,
    } = useValidationHook({ classData, student, studentAuthId });

    /**
     * Prepare Form Hooks
     */
    const methods = useForm();

    return (
        <Modal
            show={show}
            onHide={() => handleClose()}
            size="lg"
            className="validation-modal"
            closeButton
        >
            <FormProvider {...methods}>
                <ValidationForm          
                    onSubmit={handlePhotoValidation}
                    student={student}
                    validations={validations}
                    validationMessage={validationMessage}
                    validationMode={validationMode}
                    setValidationMode={setValidationMode}
                    courseDateId={courseDateId}
                    handleDeletePhoto={handleDeletePhoto}
                    handleClose={handleClose}
                    validationStep={validationStep}
                    setValidationStep={setValidationStep}
                    headShotStatus={headShotStatus}
                    idCardStatus={idCardStatus}
                    getValidationTypes={getValidationTypes}
                    getDeclineTypes={getDeclineTypes}
                    getHeadshotTypes={getHeadshotTypes}
                />
            </FormProvider>
        </Modal>
    );
};

export default ValidationModals;
