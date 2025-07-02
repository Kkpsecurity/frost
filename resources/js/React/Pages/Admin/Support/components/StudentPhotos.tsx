import React, { useEffect, useState } from "react";
import { Alert, Card } from "react-bootstrap";
import { useForm, FormProvider } from "react-hook-form";
import useValidationHook from "../../../../Hooks/Admin/useValidationHook";
import MessageConsole from "../../Instructors/FrostTraining/Partials/MessageConsole";
import { StudentUnitType } from "../../../../Config/types";
import ValidationForm from "../../Instructors/FrostTraining/ValidationForm";
import Loader from "../../../../Components/Widgets/Loader";

const StudentPhotos = ({ classData, student, selectedCourseId }) => {
    if (!student) return <Alert>Student not found</Alert>;
    if (!classData) return <Alert>Class data not found</Alert>;

    const { currentStudentUnit: studentUnit } = classData;
    const { validations } = student;

    if (!validations) return <Alert>Validations not found</Alert>;
    if (!studentUnit)
        return <Alert>No class found for this student today.</Alert>;

    // States for managing the headshot and ID card images
    const [headshot, setHeadshot] = useState<string>("");
    const [idcard, setIdcard] = useState<string>(validations.idcard);

    const courseDateId = studentUnit?.course_date_id;

    /**
     * Effect to set the headshot and ID card URLs
     */
    useEffect(() => {
        // Format today's date for comparison
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Check if studentUnit exists and then attempt to set the headshot URL
        const courseAuthId = studentUnit?.course_auth_id;
        if (courseAuthId) {
            const courseAuthIdHeadshots = validations?.[courseAuthId]?.headshot;
            if (
                courseAuthIdHeadshots &&
                typeof courseAuthIdHeadshots === "object"
            ) {
                let todaysHeadshotUrl = "";
                Object.entries(courseAuthIdHeadshots).forEach(
                    ([timestamp, url]) => {
                        const headshotDate = new Date(
                            parseInt(timestamp) * 1000
                        );

                        headshotDate.setHours(0, 0, 0, 0);

                        if (headshotDate.getTime() === today.getTime()) {
                            todaysHeadshotUrl = url as string;
                            setHeadshot(todaysHeadshotUrl);
                        }
                    }
                );

                // If no specific headshot for today, use a default or set to null
                setHeadshot(todaysHeadshotUrl);
            }
        }

        // Assuming idcard does not depend on the timestamp and is directly under the courseAuthId
        const idCardUrl = courseAuthId
            ? validations?.[courseAuthId]?.idcard
            : null;

        setIdcard(idCardUrl || null);
    }, [validations, studentUnit?.course_auth_id]);

    /**
     * Validation Hook
     */
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
    } = useValidationHook({
        classData,
        student,
        studentAuthId: studentUnit.course_auth_id,
    });

    const methods = useForm();

    if (loading) {
        return <Loader />;
    }

    return (
        <Card className="validation-card shadow">
            <MessageConsole
                status={validationMessage?.status ?? "default"}
                message={validationMessage?.message ?? "default"}
            />

            <FormProvider {...methods}>
                <ValidationForm
                    onSubmit={handlePhotoValidation}
                    student={student}
                    validations={{
                        ...validations,
                        headshot: headshot,
                        idcard: idcard,
                    }}
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
        </Card>
    );
};

export default StudentPhotos;
