import { yupResolver } from "@hookform/resolvers/yup";
import React, { useState } from "react";
import { Button, Form, Modal } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import * as yup from "yup";
import TextHidden from "../../Components/FormElements/TextHidden";
import Textarea from "../../Components/FormElements/Textarea";
import Loader from "../../Components/Widgets/Loader";

const EjectModal = ({
    student,
    showEjectModal,
    setShowEjectModal,
    setEjectReason,
    HandleEjectStudent,
    ejectLoading,
    isStudentEjected,
    studentUnit,
}) => {
    if (!studentUnit) return null;

    const schema = yup.object().shape({
        ejectReason: yup.string().required(),
    });

    const methods = useForm<FormData>({
        resolver: yupResolver(schema),
    });

    if (ejectLoading) return <Loader />;

    return (
        <Modal
            show={showEjectModal}
            onHide={() => setShowEjectModal(false)}
            backdrop="static"
            keyboard={false}
        >
            <FormProvider {...methods}>
                <form onSubmit={methods.handleSubmit(HandleEjectStudent)}>
                    <Modal.Header closeButton>
                        <Modal.Title>
                            {`Eject ${student.fname} ${student.lname}`}{" "}
                            <sup>{studentUnit.id}</sup>
                        </Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <div className="alert alert-danger">
                            <h3 className="text-warning">Administrative Action!</h3>
                            Ejecting {`${student.fname} ${student.lname}`} will
                            remove them from class for the remainder of the day.
                            They will be permitted to return the following day
                            but will be required to make up for the lost day's
                            work.
                        </div>

                        <TextHidden id="studentUnitId" value={studentUnit.id} />

                        <div className="form-group">
                            <Textarea
                                id="ejectReason"
                                title="Reason for Ejecting"
                                required={true}
                                value=""
                            />
                        </div>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button
                            variant="secondary"
                            onClick={() => setShowEjectModal(false)}
                        >
                            Close
                        </Button>
                        <Button type="submit" variant="primary">
                            {isStudentEjected ? "ReEnter" : "Eject"}{" "}
                            {`${student.fname} ${student.lname}`}
                        </Button>
                    </Modal.Footer>
                </form>
            </FormProvider>
        </Modal>
    );
};

export default EjectModal;
