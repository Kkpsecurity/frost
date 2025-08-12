import React from 'react';

interface StudentDashboardProps {
    studentName?: string;
    currentCourse?: string;
    progress?: number;
    upcomingClasses?: number;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    studentName = 'Student',
    currentCourse = 'No Course',
    progress = 0,
    upcomingClasses = 0
}) => {
    return (
        <div className="student-dashboard">
            <div className="card">
                <div className="card-header">
                    <h5>Student Dashboard</h5>
                </div>
                <div className="card-body">
                    <p>Welcome back, {studentName}!</p>
                    <div className="row">
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Current Course</h6>
                                <p>{currentCourse}</p>
                            </div>
                        </div>
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Progress</h6>
                                <div className="progress">
                                    <div
                                        className="progress-bar"
                                        role="progressbar"
                                        style={{width: `${progress}%`}}
                                        aria-valuenow={progress}
                                        aria-valuemin={0}
                                        aria-valuemax={100}
                                    >
                                        {progress}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Upcoming Classes</h6>
                                <h3>{upcomingClasses}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
