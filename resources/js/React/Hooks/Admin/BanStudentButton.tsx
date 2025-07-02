import React from "react";
import { Button, Tooltip, OverlayTrigger } from "react-bootstrap";

const BanStudentButton = ({
    student,
    setShowBanModal,
    isStudentBanned,
    banLoading,
}) => {
    // Tooltip for the Ban button
    const banTooltip = (
        <Tooltip id="ban-button-tooltip">
            Banning a student is a non-reversible event, and the student will
            fail the course and must repurchase. Make sure this is what you want
            to do.
        </Tooltip>
    );

    if (isStudentBanned) {
        return (
            <Button
                type="button"
                variant="danger"
                size="sm"
                onClick={() => {
                    alert("This feature is not yet implemented");
                }}
                disabled={banLoading}
                className="m-1"
            >
                <i className="fa fa-envelope-open-text" /> Request Student
                Unbanned
            </Button>
        );
    } else {
        return (
            <OverlayTrigger placement="top" overlay={banTooltip}>
                <Button
                    type="button"
                    variant="danger"
                    size="sm"
                    onClick={() => setShowBanModal(true)}
                    disabled={banLoading}
                    className="m-1"
                >
                    <i className="fa fa-ban" /> Ban Student
                </Button>
            </OverlayTrigger>
        );
    }
};

export default BanStudentButton;
