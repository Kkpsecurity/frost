import React from "react";

type StudentStatsBlockProps = {
    totalCourses: number;
    completedCourses: number;
    activeCourses: number;
    passedCourses: number;
};

const StudentStatsBlock = ({ totalCourses, completedCourses, activeCourses, passedCourses }: StudentStatsBlockProps) => {
    return (
        <div className="row mb-4">
            <div className="col-md-3 col-sm-6 mb-3">
                <div className="card frost-primary-bg text-white h-100">
                    <div className="card-body">
                        <div className="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 className="mb-0">{totalCourses}</h3>
                                <p className="mb-0">Total Courses</p>
                            </div>
                            <div className="fs-2">
                                <i className="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="col-md-3 col-sm-6 mb-3">
                <div className="card bg-success text-white h-100">
                    <div className="card-body">
                        <div className="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 className="mb-0">{completedCourses}</h3>
                                <p className="mb-0">Completed</p>
                            </div>
                            <div className="fs-2">
                                <i className="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="col-md-3 col-sm-6 mb-3">
                <div className="card bg-info text-white h-100">
                    <div className="card-body">
                        <div className="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 className="mb-0">{activeCourses}</h3>
                                <p className="mb-0">In Progress</p>
                            </div>
                            <div className="fs-2">
                                <i className="fas fa-play-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="col-md-3 col-sm-6 mb-3">
                <div className="card bg-warning text-white h-100">
                    <div className="card-body">
                        <div className="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 className="mb-0">{passedCourses}</h3>
                                <p className="mb-0">Passed</p>
                            </div>
                            <div className="fs-2">
                                <i className="fas fa-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentStatsBlock;
