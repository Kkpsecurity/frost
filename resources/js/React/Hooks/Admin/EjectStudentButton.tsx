import React from "react";
import { Button, Tooltip, OverlayTrigger } from "react-bootstrap";

const EjectStudentButton = ({
    classData,
    isStudentEjected,
    ejectLoading,
    ConfirmEjectStudent,
}) => {
    const { currentStudentUnit: studentUnit } = classData;

    // Tooltip text
    const tooltip = (
        <Tooltip id="button-tooltip">
            Ejecting a student is just for the current day. If you must eject a
            student, DO NOT EJECT them from the Zoom interface. Use this tool
            instead to eject a student for the current day.
        </Tooltip>
    );

    return !isStudentEjected ? (
        <OverlayTrigger placement="top" overlay={tooltip}>
            <Button
                variant="danger"
                size="sm"
                type="button"
                onClick={() => ConfirmEjectStudent(studentUnit)}
                disabled={ejectLoading}
                className="m-1"
            >
                <i className="fa fa-sign-out-alt"></i> Eject Student
            </Button>
        </OverlayTrigger>
    ) : (
        <OverlayTrigger placement="top" overlay={tooltip}>
            <Button
                variant="success"
                size="sm"
                type="button"
                onClick={() => ConfirmEjectStudent(studentUnit)}
                disabled={ejectLoading}
                className="m-1"
            >
                <i className="fa fa-sign-in-alt"></i> ReEnter Student
            </Button>
        </OverlayTrigger>
    );
};

export default EjectStudentButton;
