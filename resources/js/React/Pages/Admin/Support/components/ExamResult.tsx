import React from "react";
import { Alert } from "react-bootstrap";

const ExamResult = ({ student, classData }) => {
    const { courseAuths: CourseAuths } = student;

    if (!CourseAuths) {
        return <Alert variant="danger">Error Loading CourseAuth</Alert>;
    }

    const CourseAuth = CourseAuths[classData.selectedCourseId];

    return (
        <div className="container">
            <section className="dashboard">
                <div className="card shadow-lg">
                    <div className="card-body p-5">
                        <h3 className="card-title text-dark">Exam Results</h3>
                        <p className="alert">
                            Thank you for taking the exam! Here are your
                            results:
                        </p>

                        {CourseAuth.is_passed ? (
                            <p className="alert alert-success">
                                Congratulations! You have successfully completed
                                the exam. Please visit the classroom to obtain
                                your certificate.
                            </p>
                        ) : (
                            <p className="alert alert-danger">
                                Unfortunately, you did not pass the exam. Please
                                contact your instructor or visit the classroom
                                to obtain more information.
                            </p>
                        )}

                        <ul className="list-group list-group-flush">
                            <li className="list-group-item d-flex justify-content-between">
                                <span>Completed At:</span>{" "}
                                <span>{CourseAuth.completed_at || "N/A"}</span>
                            </li>
                            <li className="list-group-item d-flex justify-content-between">
                                <span>Score:</span>{" "}
                                <span>{CourseAuth.score || "N/A"}</span>
                            </li>
                            <li className="list-group-item d-flex justify-content-between">
                                <span>Is Passed:</span>{" "}
                                <span>
                                    {CourseAuth.is_passed ? "Yes" : "No"}
                                </span>
                            </li>

                            {!CourseAuth.is_passed &&
                                CourseAuth.next_attempt_at && (
                                    <li className="list-group-item d-flex justify-content-between">
                                        <span>Next Attempt At:</span>
                                        <span>
                                            {CourseAuth.next_attempt_at ||
                                                "N/A"}
                                        </span>
                                    </li>
                                )}
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default ExamResult;
