import React, { useState, useEffect } from 'react';
import axios from 'axios';

interface StudentData {
    id: number;
    name: string;
    email: string;
    course_auth_id: number;
    student_unit_id?: number;
}

interface ValidationData {
    idcard?: string | null;
    idcard_status?: string;
    headshot?: Record<string, string> | string | null;
    headshot_status?: string;
}

interface StudentDetailPanelProps {
    student: StudentData | null;
    onClose: () => void;
    onApprove?: (validationType: 'idcard' | 'headshot') => void;
    onReject?: (validationType: 'idcard' | 'headshot', reason: string) => void;
}

const StudentDetailPanel: React.FC<StudentDetailPanelProps> = ({
    student,
    onClose,
    onApprove,
    onReject,
}) => {
    const [loading, setLoading] = useState(false);
    const [validations, setValidations] = useState<ValidationData | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [rejectReason, setRejectReason] = useState('');
    const [showRejectModal, setShowRejectModal] = useState<'idcard' | 'headshot' | null>(null);

    useEffect(() => {
        if (student) {
            fetchValidations();
        }
    }, [student]);

    const fetchValidations = async () => {
        if (!student) return;

        setLoading(true);
        setError(null);

        try {
            // Fetch validation data for this student
            const response = await axios.get(`/admin/instructors/student-validations/${student.course_auth_id}`);
            setValidations(response.data.validations);
        } catch (err: any) {
            setError(err?.response?.data?.message || 'Failed to load validations');
            console.error('Failed to fetch validations:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleApprove = async (type: 'idcard' | 'headshot') => {
        if (!student) return;

        setLoading(true);
        try {
            await axios.post(`/admin/instructors/approve-validation`, {
                course_auth_id: student.course_auth_id,
                student_unit_id: student.student_unit_id,
                type,
            });

            // Refresh validations
            await fetchValidations();
            
            if (onApprove) {
                onApprove(type);
            }
        } catch (err: any) {
            setError(err?.response?.data?.message || `Failed to approve ${type}`);
        } finally {
            setLoading(false);
        }
    };

    const handleReject = async (type: 'idcard' | 'headshot') => {
        if (!student || !rejectReason.trim()) {
            setError('Rejection reason is required');
            return;
        }

        setLoading(true);
        try {
            await axios.post(`/admin/instructors/reject-validation`, {
                course_auth_id: student.course_auth_id,
                student_unit_id: student.student_unit_id,
                type,
                reason: rejectReason,
            });

            // Refresh validations
            await fetchValidations();
            
            if (onReject) {
                onReject(type, rejectReason);
            }

            setRejectReason('');
            setShowRejectModal(null);
        } catch (err: any) {
            setError(err?.response?.data?.message || `Failed to reject ${type}`);
        } finally {
            setLoading(false);
        }
    };

    const getHeadshotUrl = (): string | null => {
        if (!validations?.headshot) return null;

        if (typeof validations.headshot === 'string') {
            return validations.headshot;
        }

        // Get today's key
        const today = new Date().toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        
        if (typeof validations.headshot === 'object') {
            return validations.headshot[today] || null;
        }

        return null;
    };

    const getStatusBadge = (status?: string) => {
        if (!status) return <span className="badge bg-secondary">Unknown</span>;

        switch (status) {
            case 'approved':
                return <span className="badge bg-success">Approved</span>;
            case 'rejected':
                return <span className="badge bg-danger">Rejected</span>;
            case 'uploaded':
                return <span className="badge bg-info">Pending Review</span>;
            case 'missing':
                return <span className="badge bg-warning">Not Uploaded</span>;
            default:
                return <span className="badge bg-secondary">{status}</span>;
        }
    };

    if (!student) {
        return null;
    }

    const idCardUrl = validations?.idcard;
    const headshotUrl = getHeadshotUrl();

    return (
        <>
            <div className="w-100 p-0 m-0 mb-3">
                <div className="card m-0 bg-dark text-light border-secondary rounded shadow-sm">
                    {/* Header */}
                    <div className="card-header d-flex justify-content-between align-items-center bg-dark text-light py-3 px-3 border-bottom border-secondary">
                        <div>
                            <h5 className="mb-1">
                                <i className="fas fa-user-check me-2"></i>
                                Student Identity Verification
                            </h5>
                            <div className="text-muted small">
                                {student.name} • {student.email}
                            </div>
                        </div>
                        <button
                            type="button"
                            className="btn btn-sm btn-outline-secondary"
                            onClick={onClose}
                        >
                            <i className="fas fa-times me-1"></i>
                            Close
                        </button>
                    </div>

                    {/* Body */}
                    <div className="card-body py-3 px-3">
                        {loading && (
                            <div className="text-center py-4">
                                <div className="spinner-border text-primary" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        )}

                        {error && (
                            <div className="alert alert-danger">
                                <i className="fas fa-exclamation-triangle me-2"></i>
                                {error}
                            </div>
                        )}

                        {!loading && !error && (
                            <div className="row g-3">
                                {/* ID Card Section */}
                                <div className="col-12 col-lg-6">
                                    <div className="card bg-darker border-secondary">
                                        <div className="card-header bg-darker border-bottom border-secondary d-flex justify-content-between align-items-center">
                                            <h6 className="mb-0">
                                                <i className="fas fa-id-card me-2"></i>
                                                ID Card
                                            </h6>
                                            {getStatusBadge(validations?.idcard_status)}
                                        </div>
                                        <div className="card-body text-center">
                                            {idCardUrl ? (
                                                <>
                                                    <img
                                                        src={idCardUrl}
                                                        alt="ID Card"
                                                        className="img-fluid rounded mb-3"
                                                        style={{ maxHeight: '300px', objectFit: 'contain' }}
                                                    />
                                                    {validations?.idcard_status === 'uploaded' && (
                                                        <div className="d-flex gap-2 justify-content-center">
                                                            <button
                                                                className="btn btn-success btn-sm"
                                                                onClick={() => handleApprove('idcard')}
                                                                disabled={loading}
                                                            >
                                                                <i className="fas fa-check me-1"></i>
                                                                Approve
                                                            </button>
                                                            <button
                                                                className="btn btn-danger btn-sm"
                                                                onClick={() => setShowRejectModal('idcard')}
                                                                disabled={loading}
                                                            >
                                                                <i className="fas fa-times me-1"></i>
                                                                Reject
                                                            </button>
                                                        </div>
                                                    )}
                                                </>
                                            ) : (
                                                <div className="text-muted py-5">
                                                    <i className="fas fa-id-card fa-3x mb-3 opacity-25"></i>
                                                    <p>No ID card uploaded</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Headshot Section */}
                                <div className="col-12 col-lg-6">
                                    <div className="card bg-darker border-secondary">
                                        <div className="card-header bg-darker border-bottom border-secondary d-flex justify-content-between align-items-center">
                                            <h6 className="mb-0">
                                                <i className="fas fa-camera me-2"></i>
                                                Today's Headshot
                                            </h6>
                                            {getStatusBadge(validations?.headshot_status)}
                                        </div>
                                        <div className="card-body text-center">
                                            {headshotUrl ? (
                                                <>
                                                    <img
                                                        src={headshotUrl}
                                                        alt="Headshot"
                                                        className="img-fluid rounded mb-3"
                                                        style={{ maxHeight: '300px', objectFit: 'contain' }}
                                                    />
                                                    {validations?.headshot_status === 'uploaded' && (
                                                        <div className="d-flex gap-2 justify-content-center">
                                                            <button
                                                                className="btn btn-success btn-sm"
                                                                onClick={() => handleApprove('headshot')}
                                                                disabled={loading}
                                                            >
                                                                <i className="fas fa-check me-1"></i>
                                                                Approve
                                                            </button>
                                                            <button
                                                                className="btn btn-danger btn-sm"
                                                                onClick={() => setShowRejectModal('headshot')}
                                                                disabled={loading}
                                                            >
                                                                <i className="fas fa-times me-1"></i>
                                                                Reject
                                                            </button>
                                                        </div>
                                                    )}
                                                </>
                                            ) : (
                                                <div className="text-muted py-5">
                                                    <i className="fas fa-camera fa-3x mb-3 opacity-25"></i>
                                                    <p>No headshot uploaded for today</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Reject Modal */}
            {showRejectModal && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content bg-dark text-light">
                            <div className="modal-header border-secondary">
                                <h5 className="modal-title">
                                    Reject {showRejectModal === 'idcard' ? 'ID Card' : 'Headshot'}
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close btn-close-white"
                                    onClick={() => {
                                        setShowRejectModal(null);
                                        setRejectReason('');
                                    }}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="mb-3">
                                    <label className="form-label">Rejection Reason *</label>
                                    <textarea
                                        className="form-control bg-darker text-light border-secondary"
                                        rows={3}
                                        value={rejectReason}
                                        onChange={(e) => setRejectReason(e.target.value)}
                                        placeholder="Enter reason for rejection..."
                                    ></textarea>
                                </div>
                            </div>
                            <div className="modal-footer border-secondary">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={() => {
                                        setShowRejectModal(null);
                                        setRejectReason('');
                                    }}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={() => handleReject(showRejectModal)}
                                    disabled={!rejectReason.trim() || loading}
                                >
                                    <i className="fas fa-times me-1"></i>
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default StudentDetailPanel;
