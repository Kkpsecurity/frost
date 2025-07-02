import React, { useState } from "react";
import { Modal, Button } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faQuestion, faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import HelpText from "./HelpText";

const TitleBar = ({ student, searchResult, newSearch }) => {
    const [showHelpModal, setShowHelpModal] = useState(false);

    const handleClose = () => setShowHelpModal(false);
    const handleShow = () => setShowHelpModal(true);

    return (
        <div className="row">
            <div className="col-7">
                <h3 className="page-title">Search for a Student</h3>
                <p className="lead bold">
                    Searches can be done on both Live and Offline Students
                </p>
            </div>
            <div className="col-5">
                {(student || searchResult) && (
                    <Button
                        variant="primary"
                        className="float-right"
                        onClick={() => newSearch()}
                    >
                        <FontAwesomeIcon icon={faArrowLeft} /> New Search
                    </Button>
                )}

                <Button
                    variant="warning"
                    className="float-right mr-2"
                    onClick={handleShow}
                >
                    <FontAwesomeIcon icon={faQuestion} /> Help
                </Button>

                <Modal show={showHelpModal} onHide={handleClose} size="lg">
                    <Modal.Header closeButton>
                        <Modal.Title>Help Guide</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <HelpText />
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleClose}>
                            Close
                        </Button>
                    </Modal.Footer>
                </Modal>
            </div>
        </div>
    );
};

export default TitleBar;
