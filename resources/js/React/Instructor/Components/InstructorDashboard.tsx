/**
 * Instructor Default Dashboard Component
 * Displays the main dashboard for instructors in admin/instructor route
 */

import React from 'react';
import InstructorDataLayer from './InstructorDataLayer';

interface InstructorDashboardProps {
    instructor?: any;
    className?: string;
    debug?: boolean;
}

const InstructorDashboard: React.FC<InstructorDashboardProps> = ({
    instructor,
    className = "",
    debug = false,
}) => {
    return (
        <div className={`instructor-dashboard ${className}`}>
            {/* Header */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 className="mb-1">
                                Instructor Dashboard
                                {instructor?.name && (
                                    <small className="text-muted ms-2">
                                        Welcome, {instructor.name}
                                    </small>
                                )}
                            </h2>
                            <p className="text-muted mb-0">
                                Manage your courses, students, and classroom activities
                            </p>
                        </div>
                        <div className="d-flex gap-2">
                            <button
                                onClick={() => console.log('Help clicked')}
                                className="btn btn-outline-secondary"
                                type="button"
                            >
                                <i className="fas fa-question-circle me-2"></i>
                                Help
                            </button>
                            <button
                                onClick={() => console.log('Settings clicked')}
                                className="btn btn-outline-primary"
                                type="button"
                            >
                                <i className="fas fa-cog me-2"></i>
                                Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Data Layer that handles all API calls and data management */}
            <InstructorDataLayer 
                instructorId={instructor?.id}
                debug={debug}
            />
        </div>
    );
};

export default InstructorDashboard;
