import React, { useState } from "react";
import { Button, Modal } from "react-bootstrap";
import apiClient from "../../Config/axios";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import Loader from "../../Components/Widgets/Loader";

const DNCModal = ({
    student,
    showDNCModal,
    dncLoading,
    studentLesson,
    setShowDNCModal,
    HandleRevokeDNC,
    activeLesson,
}) => {
    const revokeDNCMessage =
        "The student is presently designated as DNC (Did Not Complete) for this lesson, possibly due to missing challenges. If necessary, you have the option to revoke this status; however, it's important to note that they will receive an additional challenge that must be completed.";
   
    const isStudentDNC =  studentLesson?.dnc_at ? true : false;

    return (
        <>
            <ToastContainer />
            <Modal
                show={showDNCModal}
                onHide={() => setShowDNCModal(false)}
                backdrop="static"
                keyboard={false}
            >
                <Modal.Header closeButton>
                    <Modal.Title>DNC Student</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {dncLoading && (
                        <div className="modal-loader">
                            <Loader />
                        </div>
                    )}
                    {!dncLoading && activeLesson ? (
                        isStudentDNC ? (
                            <div className="alert alert-danger">
                                <h4>
                                    <i className="fa fa-exclamation-circle"></i>{" "}
                                    {student.fname}: is DNC'ed for this lesson.
                                </h4>
                                {revokeDNCMessage}
                            </div>
                        ) : (
                            <></>
                        )
                    ) : (
                        <></>
                    )}

                    {!isStudentDNC && (
                        <div className="alert alert-info">
                            If the student is currently marked as DNC (Did Not
                            Complete) for this lesson, likely due to missing
                            challenges, please review the following information:
                            <ul>
                                <li>
                                    Student Name: {student?.fname}{" "}
                                    {student?.lname}
                                </li>
                                <li>Email: {student?.email}</li>
                                <li>Reason for DNC: Missing challenges</li>
                            </ul>
                            <p>
                                Keep in mind that revoking the DNC status will
                                require the student to redo the last challenge.
                                If they fail to complete it, the student will be
                                marked as DNC once again.
                            </p>
                        </div>
                    )}
                </Modal.Body>
                <Modal.Footer>
                    <Button
                        variant="secondary"
                        onClick={() => setShowDNCModal(false)}
                    >
                        Close
                    </Button>
                    {activeLesson && isStudentDNC && (
                        <Button
                            variant="primary"
                            onClick={(event) => {
                                const studentUnitId = student.student_unit_id;
                                HandleRevokeDNC({
                                    studentUnitId,
                                    activeLesson,
                                });
                            }}
                        >
                            Revoke DNC
                        </Button>
                    )}
                </Modal.Footer>
            </Modal>
        </>
    );
};

export default DNCModal;
