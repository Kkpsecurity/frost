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
    const verifiedCount = students.filter(s => s.verified).length;

    // Get status style
    const getStatusStyle = (status: string) => {
        switch (status) {
            case 'online':
                return {
                    badge: 'badge-success',
                    icon: 'fas fa-circle text-success',
                    label: 'Online'
                };
            case 'away':
                return {
                    badge: 'badge-warning',
                    icon: 'fas fa-circle text-warning',
                    label: 'Away'
                };
            case 'offline':
            default:
                return {
                    badge: 'badge-secondary',
                    icon: 'fas fa-circle text-secondary',
                    label: 'Offline'
                };
        }
    };

    // Get verification status style
    const getVerificationStyle = (verified: boolean) => {
        return verified
            ? { icon: 'fas fa-shield-alt text-success', title: 'Verified' }
            : { icon: 'fas fa-exclamation-triangle text-warning', title: 'Unverified' };
    };

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
                    <div className="alert alert-info alert-sm mb-0">
                        <small>No students enrolled in this class</small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="card h-100">
            <div className="card-header bg-secondary text-white">
                <h5 className="mb-0">
                    <i className="fas fa-users me-2"></i>
                    👥 Students
                </h5>
                <small className="text-white-50">
                    {onlineCount} online / {students.length} total
                </small>
            </div>

            {/* Status Summary */}
            <div className="card-body pb-2 small">
                <div className="row text-center text-muted mb-2">
                    <div className="col-6">
                        <i className="fas fa-circle text-success me-1"></i>
                        <strong className="text-success">{onlineCount}</strong> Online
                    </div>
                    <div className="col-6">
                        <i className="fas fa-shield-alt text-success me-1"></i>
                        <strong className="text-success">{verifiedCount}</strong> Verified
                    </div>
                </div>
                <div className="progress" style={{ height: '4px' }}>
                    <div
                        className="progress-bar bg-success"
                        style={{ width: `${(onlineCount / students.length) * 100}%` }}
                    ></div>
                </div>
            </div>

            {/* Students List */}
            <div className="card-body" style={{ maxHeight: '500px', overflow: 'auto', paddingTop: '0.5rem' }}>
                <div className="students-list">
                    {students.map((student) => {
                        const statusStyle = getStatusStyle(student.status);
                        const verifyStyle = getVerificationStyle(student.verified);

                        return (
                            <div
                                key={student.id}
                                className="student-item p-2 mb-2 border rounded"
                                style={{ fontSize: '0.85rem' }}
                            >
                                {/* Student Header: Status + Name */}
                                <div className="d-flex justify-content-between align-items-start mb-1">
                                    <div className="d-flex align-items-center gap-2">
                                        <i className={statusStyle.icon}></i>
                                        <div>
                                            <div className="font-weight-bold">
                                                {student.student_name}
                                            </div>
                                            <small className="text-muted">{student.student_email}</small>
                                        </div>
                                    </div>
                                    <div className="d-flex gap-1">
                                        <i
                                            className={verifyStyle.icon}
                                            title={verifyStyle.title}
                                            style={{ fontSize: '0.9rem' }}
                                        ></i>
                                    </div>
                                </div>

                                {/* Status Badge */}
                                <div className="mb-2">
                                    <span className={`badge ${statusStyle.badge} badge-sm`}>
                                        <i className="fas fa-circle me-1" style={{ fontSize: '0.6rem' }}></i>
                                        {statusStyle.label}
                                    </span>
                                </div>

                                {/* Progress Bar */}
                                {student.progress_percent > 0 && (
                                    <div className="mb-2">
                                        <small className="text-muted d-block mb-1">
                                            Progress: <strong>{student.progress_percent}%</strong>
                                        </small>
                                        <div className="progress" style={{ height: '4px' }}>
                                            <div
                                                className="progress-bar bg-primary"
                                                style={{ width: `${student.progress_percent}%` }}
                                            ></div>
                                        </div>
                                    </div>
                                )}

                                {/* Join Time */}
                                {student.joined_at && (
                                    <small className="text-muted d-block mb-2">
                                        <i className="fas fa-sign-in-alt me-1"></i>
                                        Joined: {new Date(student.joined_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </small>
                                )}

                                {/* Action Buttons */}
                                <div className="d-flex gap-1 mt-2">
                                    <button
                                        className="btn btn-sm btn-outline-primary"
                                        title="Send message"
                                        style={{ padding: '0.25rem 0.5rem', fontSize: '0.75rem' }}
                                    >
                                        <i className="fas fa-comment me-1"></i>
                                        Message
                                    </button>
                                    <button
                                        className="btn btn-sm btn-outline-warning"
                                        title="Mute student"
                                        style={{ padding: '0.25rem 0.5rem', fontSize: '0.75rem' }}
                                    >
                                        <i className="fas fa-microphone-slash me-1"></i>
                                        Mute
                                    </button>
                                    <button
                                        className="btn btn-sm btn-outline-danger"
                                        title="Eject student"
                                        style={{ padding: '0.25rem 0.5rem', fontSize: '0.75rem' }}
                                    >
                                        <i className="fas fa-times me-1"></i>
                                        Eject
                                    </button>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Footer: Summary */}
            <div className="card-footer bg-light text-muted small">
                <div className="row text-center">
                    <div className="col-6">
                        <strong>{students.length}</strong> Enrolled
                    </div>
                    <div className="col-6">
                        <strong>{onlineCount}</strong> Active
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentsPanel;
