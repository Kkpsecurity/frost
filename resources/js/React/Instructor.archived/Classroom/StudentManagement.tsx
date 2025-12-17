import React, { useState } from 'react';

interface StudentManagementProps {
    students?: any[];
}

const StudentManagement: React.FC<StudentManagementProps> = ({ students = [] }) => {
    const [studentList, setStudentList] = useState(students);

    return (
        <div className="student-management">
            <div className="card">
                <div className="card-header">
                    <h6>Student Management</h6>
                </div>
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {studentList.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="text-center">
                                            No students enrolled
                                        </td>
                                    </tr>
                                ) : (
                                    studentList.map((student, index) => (
                                        <tr key={index}>
                                            <td>{student.name}</td>
                                            <td>{student.email}</td>
                                            <td>
                                                <span className={`badge bg-${student.status === 'active' ? 'success' : 'warning'}`}>
                                                    {student.status}
                                                </span>
                                            </td>
                                            <td>{student.progress}%</td>
                                            <td>
                                                <button className="btn btn-sm btn-outline-primary me-1">
                                                    View
                                                </button>
                                                <button className="btn btn-sm btn-outline-secondary">
                                                    Message
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentManagement;
