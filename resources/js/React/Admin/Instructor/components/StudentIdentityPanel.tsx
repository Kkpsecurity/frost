import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import axios from 'axios';

interface StudentIdentityPanelProps {
    studentId: number;
    courseDateId: number;
    onClose: () => void;
}

interface IdentityData {
    student: {
        id: number;
        name: string;
        email: string;
        student_number?: string;
    };
    idcard: {
        validation_id: number | null;
        image_url: string | null;
        status: 'missing' | 'uploaded' | 'approved' | 'rejected';
        uploaded_at: string | null;
        reject_reason: string | null;
    };
    headshot: {
        validation_id: number | null;
        image_url: string | null;
        status: 'missing' | 'uploaded' | 'approved' | 'rejected';
        captured_at: string | null;
        reject_reason: string | null;
    };
    fully_verified: boolean;
}

const StudentIdentityPanel: React.FC<StudentIdentityPanelProps> = ({
    studentId,
    courseDateId,
    onClose,
}) => {
    const [notes, setNotes] = useState('');
    const [zoomLevel, setZoomLevel] = useState<'id' | 'headshot' | null>(null);
    const [rejectReason, setRejectReason] = useState({ idcard: '', headshot: '' });
    const [showRejectModal, setShowRejectModal] = useState({ idcard: false, headshot: false });
    const queryClient = useQueryClient();

    // Fetch student identity data
    const {
        data,
        isLoading,
        error,
    } = useQuery<IdentityData>({
        queryKey: ['student-identity', studentId, courseDateId],
        queryFn: async () => {
            const response = await axios.get(
                `/admin/instructors/student-identity/${studentId}/${courseDateId}`
            );
            return response.data;
        },
        retry: 2,
    });

    // Approve ID card mutation
    const approveIdCardMutation = useMutation({
        mutationFn: async (validationId: number) => {
            const response = await axios.post(
                `/admin/instructors/approve-validation/${validationId}`,
                { notes: notes }
            );
            return response.data;
        },
        onSuccess: (result) => {
            queryClient.invalidateQueries({ queryKey: ['student-identity', studentId, courseDateId] });
            queryClient.invalidateQueries({ queryKey: ['students'] });
            alert(result.message || 'ID card approved successfully!');
        },
        onError: (error: any) => {
            alert(error?.response?.data?.message || 'Failed to approve ID card');
        },
    });

    // Approve headshot mutation
    const approveHeadshotMutation = useMutation({
        mutationFn: async (validationId: number) => {
            const response = await axios.post(
                `/admin/instructors/approve-validation/${validationId}`,
                { notes: notes }
            );
            return response.data;
        },
        onSuccess: (result) => {
            queryClient.invalidateQueries({ queryKey: ['student-identity', studentId, courseDateId] });
            queryClient.invalidateQueries({ queryKey: ['students'] });
            alert(result.message || 'Headshot approved successfully!');
        },
        onError: (error: any) => {
            alert(error?.response?.data?.message || 'Failed to approve headshot');
        },
    });

    // Decline ID card mutation (reject + request new)
    const declineIdCardMutation = useMutation({
        mutationFn: async ({ validationId, reason }: { validationId: number; reason: string }) => {
            // First reject the validation
            await axios.post(
                `/admin/instructors/reject-validation/${validationId}`,
                { reason, notes: notes }
            );
            // Then request a new photo
            const response = await axios.post(
                `/admin/instructors/request-new-verification-photo/${studentId}/${courseDateId}`,
                { photo_type: 'id_card', notes: notes }
            );
            return response.data;
        },
        onSuccess: (result) => {
            queryClient.invalidateQueries({ queryKey: ['student-identity', studentId, courseDateId] });
            queryClient.invalidateQueries({ queryKey: ['students'] });
            alert('ID card declined and new photo requested from student');
            setRejectReason(prev => ({ ...prev, idcard: '' }));
            setShowRejectModal(prev => ({ ...prev, idcard: false }));
        },
        onError: (error: any) => {
            alert(error?.response?.data?.message || 'Failed to decline ID card');
        },
    });

    // Decline headshot mutation (reject + request new)
    const declineHeadshotMutation = useMutation({
        mutationFn: async ({ validationId, reason }: { validationId: number; reason: string }) => {
            // First reject the validation
            await axios.post(
                `/admin/instructors/reject-validation/${validationId}`,
                { reason, notes: notes }
            );
            // Then request a new photo
            const response = await axios.post(
                `/admin/instructors/request-new-verification-photo/${studentId}/${courseDateId}`,
                { photo_type: 'headshot', notes: notes }
            );
            return response.data;
        },
        onSuccess: (result) => {
            queryClient.invalidateQueries({ queryKey: ['student-identity', studentId, courseDateId] });
            queryClient.invalidateQueries({ queryKey: ['students'] });
            alert('Headshot declined and new photo requested from student');
            setRejectReason(prev => ({ ...prev, headshot: '' }));
            setShowRejectModal(prev => ({ ...prev, headshot: false }));
        },
        onError: (error: any) => {
            alert(error?.response?.data?.message || 'Failed to decline headshot');
        },
    });



    if (isLoading) {
        return (
            <div className="card bg-dark text-light border-secondary h-100">
                <div className="card-header d-flex justify-content-between align-items-center bg-dark border-secondary">
                    <h5 className="mb-0">
                        <i className="fas fa-id-card me-2"></i>
                        Student Identity Verification
                    </h5>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-light"
                        onClick={onClose}
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>
                <div className="card-body d-flex justify-content-center align-items-center" style={{ minHeight: '500px' }}>
                    <div className="text-center">
                        <div className="spinner-border text-primary mb-3" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                        <p className="text-muted">Loading student identity data...</p>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="card bg-dark text-light border-danger h-100">
                <div className="card-header d-flex justify-content-between align-items-center bg-danger text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-exclamation-triangle me-2"></i>
                        Error Loading Identity Data
                    </h5>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-light"
                        onClick={onClose}
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>
                <div className="card-body">
                    <div className="alert alert-danger">
                        <p className="mb-0">{(error as any)?.message || 'Failed to load student identity data'}</p>
                    </div>
                    <button className="btn btn-outline-light" onClick={onClose}>
                        <i className="fas fa-arrow-left me-2"></i>
                        Back to Teaching
                    </button>
                </div>
            </div>
        );
    }

    if (!data || (!data.idcard.validation_id && !data.headshot.validation_id)) {
        return (
            <div className="card bg-dark text-light border-warning h-100">
                <div className="card-header d-flex justify-content-between align-items-center bg-dark border-secondary">
                    <h5 className="mb-0">
                        <i className="fas fa-id-card me-2"></i>
                        Student Identity Verification
                    </h5>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-light"
                        onClick={onClose}
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>
                <div className="card-body">
                    <div className="alert alert-warning">
                        <h5>
                            <i className="fas fa-exclamation-circle me-2"></i>
                            No Verification Data Available
                        </h5>
                        <p className="mb-0">
                            {data?.student.name} has not yet uploaded their ID card or headshot for verification.
                        </p>
                    </div>
                    <div className="mt-3">
                        <p><strong>Student Information:</strong></p>
                        <ul className="list-unstyled">
                            <li><i className="fas fa-user me-2"></i> {data?.student.name}</li>
                            <li><i className="fas fa-envelope me-2"></i> {data?.student.email}</li>
                            {data?.student.student_number && (
                                <li><i className="fas fa-hashtag me-2"></i> {data?.student.student_number}</li>
                            )}
                        </ul>
                    </div>
                    <button className="btn btn-outline-light mt-3" onClick={onClose}>
                        <i className="fas fa-arrow-left me-2"></i>
                        Back to Teaching
                    </button>
                </div>
            </div>
        );
    }

    const { student, idcard, headshot } = data;

    return (
        <div className="card bg-dark text-light border-secondary h-100">
            {/* Header */}
            <div className="card-header bg-dark border-secondary">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 className="mb-1">
                            <i className="fas fa-id-card me-2 text-primary"></i>
                            Student Identity Verification
                        </h5>
                        <div className="text-muted small">
                            <i className="fas fa-user me-2"></i>
                            {student.name}
                            <span className="mx-2">|</span>
                            <i className="fas fa-envelope me-2"></i>
                            {student.email}
                            {student.student_number && (
                                <>
                                    <span className="mx-2">|</span>
                                    <i className="fas fa-hashtag me-2"></i>
                                    {student.student_number}
                                </>
                            )}
                        </div>
                    </div>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-light"
                        onClick={onClose}
                        disabled={
                            approveIdCardMutation.isPending ||
                            approveHeadshotMutation.isPending ||
                            declineIdCardMutation.isPending ||
                            declineHeadshotMutation.isPending
                        }
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {/* Main Content */}
            <div className="card-body" style={{ maxHeight: 'calc(100vh - 250px)', overflowY: 'auto' }}>
                {/* Status Badge */}
                <div className="mb-3">
                    <span
                        className={`badge ${
                            data.fully_verified
                                ? 'bg-success'
                                : (data.idcard.status === 'rejected' || data.headshot.status === 'rejected')
                                ? 'bg-danger'
                                : 'bg-warning text-dark'
                        }`}
                    >
                        {data.fully_verified && <i className="fas fa-check-circle me-1"></i>}
                        {!data.fully_verified && (data.idcard.status === 'rejected' || data.headshot.status === 'rejected') && <i className="fas fa-times-circle me-1"></i>}
                        {!data.fully_verified && data.idcard.status !== 'rejected' && data.headshot.status !== 'rejected' && <i className="fas fa-clock me-1"></i>}
                        {data.fully_verified ? 'FULLY VERIFIED' : (data.idcard.status === 'rejected' || data.headshot.status === 'rejected') ? 'REJECTED' : 'PENDING'}
                    </span>
                </div>

                {/* Image Comparison */}
                <div className="row g-3 mb-4">
                    {/* ID Card */}
                    <div className="col-12 col-lg-6">
                        <div className="card bg-secondary border-secondary">
                            <div className="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <h6 className="mb-0">
                                    <i className="fas fa-id-card me-2"></i>
                                    ID Card Photo
                                </h6>
                                <span className={`badge ${
                                    idcard.status === 'approved' ? 'bg-success' :
                                    idcard.status === 'rejected' ? 'bg-danger' :
                                    idcard.status === 'uploaded' ? 'bg-warning text-dark' :
                                    'bg-secondary'
                                }`}>
                                    {idcard.status.toUpperCase()}
                                </span>
                            </div>
                            <div className="card-body p-2">
                                {idcard.image_url ? (
                                    <div
                                        className="position-relative"
                                        style={{ cursor: zoomLevel === 'id' ? 'zoom-out' : 'zoom-in' }}
                                        onClick={() => setZoomLevel(zoomLevel === 'id' ? null : 'id')}
                                    >
                                        <img
                                            src={idcard.image_url}
                                            alt="ID Card"
                                            className="img-fluid w-100 rounded"
                                            style={{
                                                maxHeight: zoomLevel === 'id' ? 'none' : '300px',
                                                objectFit: 'contain',
                                            }}
                                        />
                                        <div className="position-absolute top-0 end-0 m-2">
                                            <span className="badge bg-dark">
                                                <i className={`fas fa-search-${zoomLevel === 'id' ? 'minus' : 'plus'} me-1`}></i>
                                                {zoomLevel === 'id' ? 'Zoom Out' : 'Zoom In'}
                                            </span>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="text-center text-muted py-5">
                                        <i className="fas fa-image fa-3x mb-3"></i>
                                        <p>No ID card uploaded</p>
                                    </div>
                                )}
                            </div>
                            {idcard.uploaded_at && (
                                <div className="card-footer bg-secondary text-muted small">
                                    Uploaded: {new Date(idcard.uploaded_at).toLocaleString()}
                                </div>
                            )}
                            {idcard.reject_reason && (
                                <div className="card-footer bg-danger text-white small">
                                    <strong>Rejected:</strong> {idcard.reject_reason}
                                </div>
                            )}
                            
                            {/* ID Card Action Buttons */}
                            {idcard.validation_id && idcard.status === 'uploaded' && (
                                <div className="card-footer bg-secondary">
                                    <div className="row g-2">
                                        <div className="col-6">
                                            <button
                                                className="btn btn-success btn-sm w-100"
                                                onClick={() => {
                                                    if (confirm('Approve this ID card?')) {
                                                        approveIdCardMutation.mutate(idcard.validation_id!);
                                                    }
                                                }}
                                                disabled={approveIdCardMutation.isPending || declineIdCardMutation.isPending}
                                            >
                                                {approveIdCardMutation.isPending ? (
                                                    <><i className="fas fa-spinner fa-spin me-1"></i> Approving...</>
                                                ) : (
                                                    <><i className="fas fa-check-circle me-1"></i> Approve</>
                                                )}
                                            </button>
                                        </div>
                                        <div className="col-6">
                                            <button
                                                className="btn btn-danger btn-sm w-100"
                                                onClick={() => setShowRejectModal(prev => ({ ...prev, idcard: true }))}
                                                disabled={declineIdCardMutation.isPending || approveIdCardMutation.isPending}
                                            >
                                                {declineIdCardMutation.isPending ? (
                                                    <><i className="fas fa-spinner fa-spin me-1"></i> Declining...</>
                                                ) : (
                                                    <><i className="fas fa-ban me-1"></i> Decline</>
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {idcard.status === 'approved' && (
                                <div className="card-footer bg-success text-white text-center">
                                    <i className="fas fa-check-circle me-1"></i> ID Card Approved
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Headshot */}
                    <div className="col-12 col-lg-6">
                        <div className="card bg-secondary border-secondary">
                            <div className="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <h6 className="mb-0">
                                    <i className="fas fa-camera me-2"></i>
                                    Today's Headshot
                                </h6>
                                <span className={`badge ${
                                    headshot.status === 'approved' ? 'bg-success' :
                                    headshot.status === 'rejected' ? 'bg-danger' :
                                    headshot.status === 'uploaded' ? 'bg-warning text-dark' :
                                    'bg-secondary'
                                }`}>
                                    {headshot.status.toUpperCase()}
                                </span>
                            </div>
                            <div className="card-body p-2">
                                {headshot.image_url ? (
                                    <div
                                        className="position-relative"
                                        style={{ cursor: zoomLevel === 'headshot' ? 'zoom-out' : 'zoom-in' }}
                                        onClick={() => setZoomLevel(zoomLevel === 'headshot' ? null : 'headshot')}
                                    >
                                        <img
                                            src={headshot.image_url}
                                            alt="Headshot"
                                            className="img-fluid w-100 rounded"
                                            style={{
                                                maxHeight: zoomLevel === 'headshot' ? 'none' : '300px',
                                                objectFit: 'contain',
                                            }}
                                        />
                                        <div className="position-absolute top-0 end-0 m-2">
                                            <span className="badge bg-dark">
                                                <i className={`fas fa-search-${zoomLevel === 'headshot' ? 'minus' : 'plus'} me-1`}></i>
                                                {zoomLevel === 'headshot' ? 'Zoom Out' : 'Zoom In'}
                                            </span>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="text-center text-muted py-5">
                                        <i className="fas fa-camera fa-3x mb-3"></i>
                                        <p>No headshot captured today</p>
                                    </div>
                                )}
                            </div>
                            {headshot.captured_at && (
                                <div className="card-footer bg-secondary text-muted small">
                                    Captured: {new Date(headshot.captured_at).toLocaleString()}
                                </div>
                            )}
                            {headshot.reject_reason && (
                                <div className="card-footer bg-danger text-white small">
                                    <strong>Rejected:</strong> {headshot.reject_reason}
                                </div>
                            )}
                            
                            {/* Headshot Action Buttons */}
                            {headshot.validation_id && headshot.status === 'uploaded' && (
                                <div className="card-footer bg-secondary">
                                    <div className="row g-2">
                                        <div className="col-6">
                                            <button
                                                className="btn btn-success btn-sm w-100"
                                                onClick={() => {
                                                    if (confirm('Approve this headshot?')) {
                                                        approveHeadshotMutation.mutate(headshot.validation_id!);
                                                    }
                                                }}
                                                disabled={approveHeadshotMutation.isPending || declineHeadshotMutation.isPending}
                                            >
                                                {approveHeadshotMutation.isPending ? (
                                                    <><i className="fas fa-spinner fa-spin me-1"></i> Approving...</>
                                                ) : (
                                                    <><i className="fas fa-check-circle me-1"></i> Approve</>
                                                )}
                                            </button>
                                        </div>
                                        <div className="col-6">
                                            <button
                                                className="btn btn-danger btn-sm w-100"
                                                onClick={() => setShowRejectModal(prev => ({ ...prev, headshot: true }))}
                                                disabled={declineHeadshotMutation.isPending || approveHeadshotMutation.isPending}
                                            >
                                                {declineHeadshotMutation.isPending ? (
                                                    <><i className="fas fa-spinner fa-spin me-1"></i> Declining...</>
                                                ) : (
                                                    <><i className="fas fa-ban me-1"></i> Decline</>
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {headshot.status === 'approved' && (
                                <div className="card-footer bg-success text-white text-center">
                                    <i className="fas fa-check-circle me-1"></i> Headshot Approved
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Overall Verification Status */}
                {data.fully_verified && (
                    <div className="alert alert-success mb-3">
                        <i className="fas fa-check-circle me-2"></i>
                        <strong>Student identity fully verified</strong> - Both ID card and headshot have been approved.
                    </div>
                )}

                {/* Match Confidence - Removed */}

                {/* Instructor Notes */}
                <div className="card bg-secondary border-secondary mb-3">
                    <div className="card-header bg-secondary">
                        <h6 className="mb-0">
                            <i className="fas fa-comment-dots me-2"></i>
                            Instructor Notes
                        </h6>
                    </div>
                    <div className="card-body">
                        <textarea
                            className="form-control bg-dark text-light border-secondary"
                            rows={3}
                            placeholder="Add notes about this verification (optional)..."
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            disabled={
                                approveIdCardMutation.isPending ||
                                approveHeadshotMutation.isPending ||
                                declineIdCardMutation.isPending ||
                                declineHeadshotMutation.isPending
                            }
                        ></textarea>
                    </div>
                </div>
            </div>

            {/* Footer - Only Back Button */}
            <div className="card-footer bg-dark border-secondary">
                <button
                    className="btn btn-outline-secondary"
                    onClick={onClose}
                    disabled={
                        approveIdCardMutation.isPending ||
                        approveHeadshotMutation.isPending ||
                        declineIdCardMutation.isPending ||
                        declineHeadshotMutation.isPending
                    }
                >
                    <i className="fas fa-arrow-left me-2"></i>
                    Back to Teaching
                </button>
            </div>

            {/* Decline ID Card Modal */}
            {showRejectModal.idcard && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content bg-dark text-light">
                            <div className="modal-header border-secondary">
                                <h5 className="modal-title">
                                    <i className="fas fa-ban text-danger me-2"></i>
                                    Decline ID Card
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close btn-close-white"
                                    onClick={() => setShowRejectModal(prev => ({ ...prev, idcard: false }))}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle me-2"></i>
                                    Declining will reject this photo and automatically request a new one from the student.
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">
                                        Reason for declining <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        className="form-control bg-secondary text-light border-secondary"
                                        rows={3}
                                        placeholder="Please explain what's wrong with the ID card..."
                                        value={rejectReason.idcard}
                                        onChange={(e) => setRejectReason(prev => ({ ...prev, idcard: e.target.value }))}
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div className="modal-footer border-secondary">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary"
                                    onClick={() => setShowRejectModal(prev => ({ ...prev, idcard: false }))}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={() => {
                                        if (!rejectReason.idcard.trim()) {
                                            alert('Please provide a reason for declining');
                                            return;
                                        }
                                        declineIdCardMutation.mutate({
                                            validationId: idcard.validation_id!,
                                            reason: rejectReason.idcard
                                        });
                                    }}
                                    disabled={declineIdCardMutation.isPending}
                                >
                                    {declineIdCardMutation.isPending ? (
                                        <><i className="fas fa-spinner fa-spin me-1"></i> Declining...</>
                                    ) : (
                                        <><i className="fas fa-ban me-1"></i> Decline & Request New</>  
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Decline Headshot Modal */}
            {showRejectModal.headshot && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content bg-dark text-light">
                            <div className="modal-header border-secondary">
                                <h5 className="modal-title">
                                    <i className="fas fa-ban text-danger me-2"></i>
                                    Decline Headshot
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close btn-close-white"
                                    onClick={() => setShowRejectModal(prev => ({ ...prev, headshot: false }))}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle me-2"></i>
                                    Declining will reject this photo and automatically request a new one from the student.
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">
                                        Reason for declining <span className="text-danger">*</span>
                                    </label>
                                    <textarea
                                        className="form-control bg-secondary text-light border-secondary"
                                        rows={3}
                                        placeholder="Please explain what's wrong with the headshot..."
                                        value={rejectReason.headshot}
                                        onChange={(e) => setRejectReason(prev => ({ ...prev, headshot: e.target.value }))}
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div className="modal-footer border-secondary">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary"
                                    onClick={() => setShowRejectModal(prev => ({ ...prev, headshot: false }))}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={() => {
                                        if (!rejectReason.headshot.trim()) {
                                            alert('Please provide a reason for declining');
                                            return;
                                        }
                                        declineHeadshotMutation.mutate({
                                            validationId: headshot.validation_id!,
                                            reason: rejectReason.headshot
                                        });
                                    }}
                                    disabled={declineHeadshotMutation.isPending}
                                >
                                    {declineHeadshotMutation.isPending ? (
                                        <><i className="fas fa-spinner fa-spin me-1"></i> Declining...</>
                                    ) : (
                                        <><i className="fas fa-ban me-1"></i> Decline & Request New</>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default StudentIdentityPanel;
