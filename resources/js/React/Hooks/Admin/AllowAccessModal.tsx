import React, { useState } from "react";
import { Button, Form, Modal } from "react-bootstrap";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { FormProvider, useForm, SubmitHandler } from "react-hook-form";
import TextHidden from "../../Components/FormElements/TextHidden";
import { StudentType } from "../../Config/types";
import AllowAccessSupportText from "./AllowAccessSupportText";

interface AllowAccessModalProps {
    student: StudentType;
    showAllowAccessModal: boolean;
    setShowAllowAccessModal: React.Dispatch<React.SetStateAction<boolean>>;
    setAllowAccessReason: React.Dispatch<React.SetStateAction<string>>;
    HandleAllowAccess: SubmitHandler<FormData>; // Update the type here
    allowAccessLoading: boolean;
    studentUnitId: number | null;
}

interface FormData {
    allowAccessReason: string;
    studentUnitId: number | null;
}

const AllowAccessModal: React.FC<AllowAccessModalProps> = ({
    student,
    showAllowAccessModal,
    setShowAllowAccessModal,
    setAllowAccessReason,
    HandleAllowAccess, // This should now accept form data
    allowAccessLoading,
    studentUnitId,
}) => {
    if (!student) return null;

    const [showModal, setShowModal] = useState(false);

    const handleShow = () => setShowModal(true);
    const handleClose = () => setShowModal(false);

    const schema = yup.object().shape({
        allowAccessReason: yup.string().required(),
    });

    const methods = useForm<FormData>({
        resolver: yupResolver(schema),
        defaultValues: {
            allowAccessReason: `Instructor Allow Access for Student ${new Date().toLocaleString()}`,
            studentUnitId: studentUnitId,
        },
    });

    return (
        <Modal
            show={showAllowAccessModal}
            onHide={() => setShowAllowAccessModal(false)}
            backdrop="static"
            keyboard={false}
        >
            <FormProvider {...methods}>
                <form onSubmit={methods.handleSubmit(HandleAllowAccess)}>
                    <Modal.Header closeButton>
                        <Modal.Title>{`Allow Access ${student.fname} ${student.lname}`}</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <div className="alert alert-success text-dark">
                            <h3 className="text-dark">Allow Access</h3>
                            Allowing access for{" "}
                            {`${student.fname} ${student.lname}`} enables their
                            participation in the class. Please remind students
                            of the importance of being present on the class page
                            at the start of each lesson to ensure their
                            attendance is accurately recorded. Absence at lesson
                            start, especially for more than 5 minutes, may lead
                            to missing the lesson, impacting their course
                            progress.
                            <button
                                className="btn btn-link p-0 ml-1"
                                type="button"
                                onClick={handleShow}
                            >
                                Read More
                            </button>
                        </div>

                        <TextHidden id="studentUnitId" value={studentUnitId} />
                        <TextHidden
                            id="allowAccessReason"
                            value={
                                "Instructor Allow Access for Student " +
                                new Date().toLocaleString()
                            }
                        />
                    </Modal.Body>
                    <Modal.Footer>
                        <Button
                            variant="secondary"
                            onClick={() => setShowAllowAccessModal(false)}
                        >
                            Close
                        </Button>
                        <Button type="submit" variant="primary">
                            Allow Access for{" "}
                            {`${student.fname} ${student.lname}`}
                        </Button>
                    </Modal.Footer>
                    <Modal show={showModal} onHide={handleClose}>
                        <Modal.Header closeButton>
                            <Modal.Title>
                                Understanding Attendance and Participation
                            </Modal.Title>
                        </Modal.Header>
                        <Modal.Body>
                            <AllowAccessSupportText />
                        </Modal.Body>
                        <Modal.Footer>
                            <Button variant="secondary" onClick={handleClose}>
                                Close
                            </Button>
                        </Modal.Footer>
                    </Modal>
                </form>
            </FormProvider>
        </Modal>
    );
};

export default AllowAccessModal;
