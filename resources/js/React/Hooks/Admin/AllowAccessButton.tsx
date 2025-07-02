import React from 'react';
import { Button, Tooltip, OverlayTrigger } from 'react-bootstrap';
import { StudentLessonType, StudentType } from '../../Config/types';

interface AllowAccessButtonProps {
    student: StudentType;
    studentLesson: StudentLessonType | null;
    activeLesson: number | null;
    ConfirmAllowAccess: React.MouseEventHandler<HTMLButtonElement>;
    isStudentLate: boolean;
}

const AllowAccessButton: React.FC<AllowAccessButtonProps> = ({
    student,
    studentLesson,
    activeLesson,
    ConfirmAllowAccess,
    isStudentLate,
}) => {
    const lateTooltip = (
        <Tooltip id="late-tooltip">
            The student {student.fname} {student.lname} was late to this lesson.
            Please add discretion before allowing the student back to class. {studentLesson?.lesson_id}
        </Tooltip>
    );

    
    if (activeLesson && activeLesson > 0 && isStudentLate) {
        return (
            <OverlayTrigger placement="top" overlay={lateTooltip}>
                <Button
                    className="m-1"
                    variant="primary"
                    size="sm"
                    onClick={ConfirmAllowAccess} // Corrected to properly pass the event handler
                >
                    <i className="fa fa-door-open" /> Allow Lesson Access
                </Button>
            </OverlayTrigger>
        );
    }

    return null; // Better to return null for no output
};

export default AllowAccessButton;
