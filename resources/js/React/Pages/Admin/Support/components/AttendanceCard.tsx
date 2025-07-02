import React from "react";
import { Alert, ListGroup, OverlayTrigger, Tooltip } from "react-bootstrap";
import { CourseUnitType, StudentUnitType } from "../../../../Config/types";

const ToolTipText =
    "This Attendance Record is for the current course week. Being present indicates that the student has been checked in for the day in this current week, but does not necessarily mean that they have passed the day's requirements.";

const AttendanceCard = ({ classData, selectedCourseId }) => {
    if (!classData || !selectedCourseId)
        return (
            <Alert variant="warning">Select a course to view attendance.</Alert>
        );

    const { studentUnits, currentStudentUnit: studentUnit } = classData;

    const calculateWeekRange = React.useMemo(() => {
        const currentDate = new Date();
        const weekStart =
            currentDate.getDate() -
            currentDate.getDay() +
            (currentDate.getDay() === 0 ? -6 : 1);
        const weekEnd = weekStart + 4;
        const startOfWeek = new Date(currentDate.setDate(weekStart)).setHours(
            0,
            0,
            0,
            0
        );
        const endOfWeek = new Date(currentDate.setDate(weekEnd)).setHours(
            23,
            59,
            59,
            999
        );
        return { startOfWeek, endOfWeek };
    }, []);
    

    const wasStudentPresentOnDay = (
        studentUnits: StudentUnitType[],
        day: number
    ) => {
        const { startOfWeek } = calculateWeekRange;
        const dayDate = new Date(startOfWeek);
        dayDate.setDate(dayDate.getDate() + day - 1);
        dayDate.setHours(0, 0, 0, 0); // Ensure start of day for comparison
    
        return studentUnits.some((unit) => {
            const unitDate = new Date(unit.created_at * 1000); // Convert UNIX timestamp to Date
            unitDate.setHours(0, 0, 0, 0);
            return unitDate.getTime() === dayDate.getTime();
        });
    };
    

    const renderTooltip = (props) => (
        <Tooltip id="button-tooltip" {...props}>
            {ToolTipText}
        </Tooltip>
    );

    const displayWeekAttendance = () => {
        const weekDays = [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
        ];

        return (
            <ListGroup>
                {weekDays.map((dayName, index) => (
                    <ListGroup.Item
                        key={dayName}
                        className={`d-flex justify-content-between align-items-center ${
                            wasStudentPresentOnDay(
                                studentUnits,
                                index + 1
                            )
                                ? "bg-success text-white"
                                : "bg-danger text-white"
                        }`}
                    >
                        <span>{dayName} - </span>
                        {wasStudentPresentOnDay(
                            studentUnits,
                            index + 1
                        )
                            ? "Present"
                            : "Absent"}
                    </ListGroup.Item>
                ))}
            </ListGroup>
        );
    };

    return (
        <div>
            <h4 className="d-flex justify-content-between align-items-center">
                <span>Attendance Record</span>
                <OverlayTrigger placement="right" overlay={renderTooltip}>
                    <button className="btn btn-sm btn-info" aria-label="Help">
                        <i className="fas fa-question-circle"></i>
                    </button>
                </OverlayTrigger>
            </h4>
            {displayWeekAttendance()}
        </div>
    );
};

export default AttendanceCard;
