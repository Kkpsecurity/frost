import React from 'react';
import { useQuery } from '@tanstack/react-query';

interface StudentsPanelProps {
    courseDateId?: number;
    instUnitId?: number;
}

interface Student {
    id: number;
    student_id: number;
    student_name: string;
    student_email: string;
    status: 'online' | 'offline' | 'away';
    joined_at?: string;
    verified: boolean;
    progress_percent: number;
}

/**
 * StudentsPanel - Right sidebar panel showing active students with real-time status
 *
 * Features:
 * - Shows active students in current class
 * - Real-time status indicators (online, offline, away)
 * - Identity verification status
 * - Student progress tracking
 * - Instructor actions (message, eject, ban)
 * - Live count updates
 */
const StudentsPanel: React.FC<StudentsPanelProps> = ({ courseDateId, instUnitId }) => {
    // Fetch active students for current class
    const { data: studentsData, isLoading, error } = useQuery({
        queryKey: ['students', courseDateId],
        queryFn: async () => {
            if (!courseDateId) return null;

            const response = await fetch(`/admin/instructors/data/students/active?courseDateId=${courseDateId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch students: ${response.statusText}`);
            }

            return response.json();
        },
        staleTime: 5 * 1000, // 5 seconds - high refresh rate for live status
        gcTime: 1 * 60 * 1000, // 1 minute
        refetchInterval: 3000, // Poll every 3 seconds for live updates
        enabled: !!courseDateId,
        retry: 2,
    });

    const students: Student[] = studentsData?.students || [];
    const onlineCount = students.filter(s => s.status === 'online').length;

    if (isLoading) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-users me-2"></i>
                        Students
                    </h5>
                </div>
                <div className="card-body d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
                    <div className="text-center">
                        <div className="spinner-border spinner-border-sm text-primary" role="status">
                            <span className="visually-hidden">Loading students...</span>
                        </div>
                        <p className="mt-2 text-muted"><small>Loading students...</small></p>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-users me-2"></i>
                        Students
                    </h5>
                </div>
                <div className="card-body">
                    <div className="alert alert-danger alert-sm mb-0">
                        <small>
                            <i className="fas fa-exclamation-circle me-2"></i>
                            Failed to load students
                        </small>
                    </div>
                </div>
            </div>
        );
    }

    if (!students || students.length === 0) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-users me-2"></i>
                        Students (0)
                    </h5>
                </div>
                <div className="card-body">
                    <p className="text-muted small mb-0">No students in this class yet</p>
                </div>
            </div>
        );
    }

    return (
        <div className="card h-100">
            <div className="card-header bg-secondary text-white py-2 px-3">
                <h5 className="mb-0">
                    <i className="fas fa-users me-2"></i>
                    Students ({students.length})
                </h5>
                <small className="text-white-50">
                    {onlineCount} online / {students.length} total
                </small>
            </div>

            <div className="list-group list-group-flush m-0" style={{ maxHeight: '500px', overflow: 'auto' }}>
                {students.map((student, index) => (
                    <div
                        key={student.id}
                        className={`list-group-item list-group-item-action bg-transparent px-3 py-2 ${index === 0 ? 'border-top-0' : ''}`}
                    >
                        <div className="d-flex align-items-start gap-2">
                            <div
                                className="rounded-circle bg-secondary text-white-50 d-flex align-items-center justify-content-center flex-shrink-0"
                                style={{ width: 32, height: 32, fontSize: '0.8rem' }}
                                aria-hidden="true"
                            >
                                <i className="fas fa-user"></i>
                            </div>
                            <div className="flex-grow-1" style={{ minWidth: 0 }}>
                                <a
                                    href={`/admin/frost-support/student/${student.student_id}`}
                                    className="d-block fw-semibold link-light text-decoration-none text-truncate"
                                    title={student.student_name}
                                >
                                    {student.student_name}
                                </a>
                                <div className="small text-white-50 text-truncate" title={student.student_email}>
                                    {student.student_email}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default StudentsPanel;
