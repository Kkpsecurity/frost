import React, { useEffect, useState } from "react";
import { Alert } from "react-bootstrap";
import { StudentLessonType, StudentUnitType } from "../../../../Config/types";

const ShowListOfDays = ({ classData, getDayOfWeek, viewDayReport }) => {
    const { studentUnit, studentUnits, instUnit, courseDates, studentLessons } = classData;

    const [createdAtDate, setCreatedAtDate] = useState("");

    const alertStyle = {
        display: "flex",
        justifyContent: "start",
        alignItems: "center",
    };

    useEffect(() => {
        if (instUnit) {
            const formattedDate = new Date(Number(instUnit.created_at) * 1000).toLocaleString();
            setCreatedAtDate(formattedDate);
        }
    }, [instUnit]);

    const hasUnit = (unit: StudentUnitType) =>
        studentUnit && unit && studentUnit.id === unit.id;

    const allLessonsCompleted = (unit: StudentUnitType) =>
        studentLessons && studentLessons[unit.id]?.every(
            (lesson: StudentLessonType) => lesson.completed_at
        );

    const lessonStatus = (unit: StudentUnitType) => {
        if (unit.ejected_at) {
            return "Ejected";
        } else if (allLessonsCompleted(unit) && unit.completed_at) {
            return "Completed";
        } else {
            return "Incomplete";
        }
    };

    // Sort studentUnits by created_at date
    const sortedStudentUnits = [...studentUnits].sort((a, b) => Number(a.created_at) - Number(b.created_at));

    return (
        <>
            {!courseDates && (
                <Alert variant="danger" style={alertStyle}>
                    <p
                        style={{
                            fontSize: "1.3rem",
                            fontWeight: "bold",
                            margin: "0",
                            padding: "0",
                        }}
                    >
                        There are No Classes Scheduled today.
                    </p>
                </Alert>
            )}

            {createdAtDate && (
                <Alert variant="success" style={alertStyle}>
                    <span style={{ fontSize: "1.0rem", fontWeight: "bold" }}>
                        Live Class in Progress: {createdAtDate} :{" "}
                        {classData.instructor.fname}{" "}
                        {classData.instructor.lname}
                    </span>
                </Alert>
            )}

            <Alert variant="info" style={alertStyle}>
                <span style={{ fontSize: "1.0rem", fontWeight: "bold" }}>
                    The following list shows student attendance history by day,
                    click on the button to view the daily report.
                </span>
            </Alert>

            {sortedStudentUnits && (
                <div
                    className="list-group"
                    style={{
                        backgroundColor: "lightgray",
                        border: "1px solid #333",
                    }}
                >
                    {sortedStudentUnits.map((unit: StudentUnitType) => (
                        <div
                            key={unit.id}
                            className="list-group-item d-flex justify-content-between align-items-center"
                            style={{
                                backgroundColor: hasUnit(unit)
                                    ? "#f8d7da"
                                    : "#d4edda",
                                margin: "0",
                                padding: "10px 0",
                            }}
                        >
                            <div className="col">
                                <h5
                                    style={{
                                        fontSize: "1.0rem",
                                        textTransform: "uppercase",
                                    }}
                                >
                                    Day: <b>{getDayOfWeek(unit.created_at)} :{" "}
                                    {new Date(Number(unit.created_at) * 1000).toLocaleString()}</b>
                                </h5>
                            </div>
                            
                            <div className="col d-flex justify-content-end align-items-center">
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        viewDayReport(
                                            unit.id,
                                            new Date(Number(unit.created_at) * 1000).toLocaleString()
                                        );
                                    }}
                                    className="btn btn-outline-primary"
                                >
                                    {lessonStatus(unit)}
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </>
    );
};

export default ShowListOfDays;
