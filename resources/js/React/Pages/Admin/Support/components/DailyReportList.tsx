import React from "react";
import { CourseUnitLessonType } from "../../../../Config/types";

export const DayHeader = ({ selectDate, todaysUnit, setShowDayReport }) => {
    if (!todaysUnit) return null;

    return (
        <div
            className="d-flex justify-content-between align-items-center"
            style={{ marginBottom: "10px" }}
        >
            <div>
                <h5 className="title">
                    Selected Day: <b>{selectDate}</b>
                </h5>
            </div>

            <button
                className="btn btn-primary"
                onClick={() => setShowDayReport(false)}
            >
                Back
            </button>
        </div>
    );
};

/**
 * Get the Lesson required for the day
 * @param param0 
 * @returns 
 */
export const CurrentDaysLessons = ({
    courseUnitLessons,
    getLessonTitle,
    getStudentLessonStatusColor,
    getStudentLessonTime,
    getStudentLessonStatus,
}) => {
    return (
        <>
            {courseUnitLessons && courseUnitLessons.length > 0 ? (
                Object.values<CourseUnitLessonType>(courseUnitLessons).map(
                    (lesson: CourseUnitLessonType) => (
                        <div
                            className="list-group"
                            style={{
                                backgroundColor: "lightgray",
                                border: "1px solid #333",
                                marginTop: "20px",
                            }}
                            key={`lesson-${lesson.lesson_id}`}
                        >
                            <div
                                className="list-group-item d-flex justify-content-between align-items-center"
                                style={{
                                    backgroundColor: "#f8d7da",
                                    margin: "0",
                                    padding: "10px 0",
                                }}
                            >
                                <div className="container">
                                    <div className="row">
                                        <div className="col">
                                            <h5>
                                                Lesson:{" "}
                                                {getLessonTitle(
                                                    lesson.lesson_id
                                                )}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                className="list-group-item d-flex justify-content-between align-items-center"
                                style={{
                                    backgroundColor:
                                        getStudentLessonStatusColor(
                                            lesson.lesson_id
                                        ),
                                    margin: "0",
                                    padding: "10px 0",
                                }}
                            >
                                <div className="container">
                                    <div className="row">
                                        <div className="col">
                                            <h5>Lesson Details</h5>
                                            <ul>
                                                <li>
                                                    Start Time:{" "}
                                                    {getStudentLessonTime(
                                                        lesson.lesson_id
                                                    )}
                                                </li>
                                                <li>
                                                    Status:{" "}
                                                    {getStudentLessonStatus(
                                                        lesson.lesson_id
                                                    )}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )
                )
            ) : (
                <p className="alert alert-danger text-center">
                    No lessons found for this date.
                </p>
            )}
        </>
    );
};
