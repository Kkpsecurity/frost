import React from "react";
import { Button, Tooltip, OverlayTrigger } from "react-bootstrap";
import { StudentLessonType, StudentType } from "../../Config/types";

interface RevokeDNCButtonProps {
    student: StudentType;
    activeLesson: number | null;
    studentLesson: StudentLessonType;
    ConfirmRevokeDNC: React.MouseEventHandler<HTMLButtonElement>;
}

const RevokeDNCButton: React.FC<RevokeDNCButtonProps> = ({
    student,
    activeLesson,
    studentLesson,
    ConfirmRevokeDNC,
}) => {
    console.log("RevokeDNCButton", activeLesson, studentLesson);
    if (!student || !activeLesson || !studentLesson) return <></>;

    // Tooltip content
    const dncTooltip = (
        <Tooltip id="dnc-tooltip">
            The student has been DNC due to missing 2 or more challenges. You
            can reset the last challenge. if you feel the student should be re-entered
        </Tooltip>
    );

    const studentLessonAttendance =
        studentLesson && studentLesson.dnc_at ? true : false;

    if (studentLessonAttendance) {
        return (
            <OverlayTrigger placement="top" overlay={dncTooltip}>
                <Button
                    size="sm"
                    className="m-1"
                    variant="warning"
                    onClick={ConfirmRevokeDNC}
                >
                    <i className="fa fa-times-circle mr-1"></i> Revoke DNC
                </Button>
            </OverlayTrigger>
        );
    }

    // Return null if conditions are not met or dnc_at is not present
    return null;
};

export default RevokeDNCButton;
