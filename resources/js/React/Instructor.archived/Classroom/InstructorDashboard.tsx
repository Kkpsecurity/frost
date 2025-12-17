import React from 'react';

interface InstructorDashboardProps {
    instructorName?: string;
    activeClasses?: number;
    totalStudents?: number;
}

const InstructorDashboard: React.FC<InstructorDashboardProps> = ({
    instructorName = 'Instructor',
    activeClasses = 0,
    totalStudents = 0
}) => {
    return (
        <div className="instructor-dashboard">
            <div className="card">
                <div className="card-header">
                    <h5>Instructor Dashboard</h5>
                </div>
                <div className="card-body">
                    <p>Welcome, {instructorName}!</p>
                    <div className="row">
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Active Classes</h6>
                                <h3>{activeClasses}</h3>
                            </div>
                        </div>
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Total Students</h6>
                                <h3>{totalStudents}</h3>
                            </div>
                        </div>
                        <div className="col-md-4">
                            <div className="stat-card">
                                <h6>Pending Reviews</h6>
                                <h3>12</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default InstructorDashboard;
