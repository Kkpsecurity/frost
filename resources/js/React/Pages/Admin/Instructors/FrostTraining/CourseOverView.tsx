import React from "react";
import {
    CourseMeetingShape,
    CourseType,
    InstructorType,
} from "../../../../Config/types";

type Props = {
    data: CourseMeetingShape;
    instructor: InstructorType;
    courseDateId: number;
};

const CourseOverView: React.FC<Props> = ({ data, instructor }) => {
    return (
        <div className="container my-4">
            {" "}
            {/* Added margin on y-axis */}
            <div className="row mb-4">
                <div className="col-12">
                    <h1>Classroom Day Overview</h1>
                </div>
            </div>
            <div className="list-group mb-4">
                <div className="list-group-item">
                    <h2>{data.course.title}</h2>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Date:{" "}
                    <span>
                        {new Date(
                            data.instUnit.created_at
                        ).toLocaleDateString()}
                    </span>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Start Time:{" "}
                    <span>
                        {new Date(
                            data.instUnit.created_at
                        ).toLocaleTimeString()}
                    </span>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    End Time:{" "}
                    <span>
                        {new Date(
                            data.instUnit.completed_at
                        ).toLocaleTimeString()}
                    </span>
                </div>
            </div>
            <div className="list-group mb-4">
                <div className="list-group-item">
                    <h3>Instructor</h3>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Instructor:
                    <span>
                        {instructor?.fname} {instructor?.lname}
                    </span>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Email:
                    <span>{instructor?.email}</span>
                </div>
                <div className="list-group-item">
                    <h3>Assistant</h3>
                </div>
                {data.instUnit.assistant_id ? (
                    <>
                        <div className="list-group-item d-flex justify-content-between">
                            Name:
                            <span>
                                {data?.assistant?.fname} {data.assistant?.lname}
                            </span>
                        </div>
                        <div className="list-group-item d-flex justify-content-between">
                            Email:
                            <span>{data?.assistant?.email}</span>
                        </div>
                    </>
                ) : (
                    <div className="list-group-item">No Assistant in class</div>
                )}
            </div>
            <div className="list-group mb-4">
                <div className="list-group-item">
                    <h3>Students</h3>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Total students:
                    <span>{data.totalStudentsCount}</span>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Students Completed Class:
                    <span>{data.completedStudentsCount}</span>
                </div>
                <div className="list-group-item d-flex justify-content-between">
                    Students Missing Lessons:
                    <span>
                        {data.totalStudentsCount - data.completedStudentsCount}
                    </span>
                </div>
            </div>
        </div>
    );
};

export default CourseOverView;
