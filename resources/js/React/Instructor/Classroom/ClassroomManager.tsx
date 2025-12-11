import React, { useState } from 'react';

interface ClassroomManagerProps {
    classrooms?: any[];
}

const ClassroomManager: React.FC<ClassroomManagerProps> = ({ classrooms = [] }) => {
    const [activeClassrooms, setActiveClassrooms] = useState(classrooms);

    return (
        <div className="classroom-manager">
            <div className="card">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h6>Classroom Manager</h6>
                    <button className="btn btn-sm btn-primary">
                        Create Classroom
                    </button>
                </div>
                <div className="card-body">
                    <div className="row">
                        {activeClassrooms.length === 0 ? (
                            <div className="col-12">
                                <p className="text-center text-muted">No classrooms available</p>
                            </div>
                        ) : (
                            activeClassrooms.map((classroom, index) => (
                                <div key={index} className="col-md-6 mb-3">
                                    <div className="card">
                                        <div className="card-body">
                                            <h6 className="card-title">{classroom.name}</h6>
                                            <p className="card-text">
                                                Students: {classroom.studentCount}
                                            </p>
                                            <button className="btn btn-sm btn-success">
                                                Join Class
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ClassroomManager;
