import React, { useState } from "react";
import axios from "axios";

export interface CourseItem {
    id: number;
    course_id: number;
    name: string;
    status: string;
    start_date: string | null;
    expire_date: string | null;
    is_passed: boolean;
    disabled_at: string | null;
    disabled_reason: string | null;
}

interface BanEnrollmentModalProps {
    course: CourseItem;
    onClose: () => void;
    onSuccess: () => void;
}

const BanEnrollmentModal: React.FC<BanEnrollmentModalProps> = ({ course, onClose, onSuccess }) => {
    const isBanned = !!course.disabled_at;
    const [reason, setReason] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async () => {
        if (!isBanned && !reason.trim()) {
            setError("A reason is required to ban a student.");
            return;
        }

        setLoading(true);
        setError(null);

        try {
            await axios.post(
                `/admin/support/student-tools/toggle-ban/${course.id}`,
                isBanned ? {} : { reason: reason.trim() }
            );
            onSuccess();
            onClose();
        } catch (err: any) {
            setError(
                err?.response?.data?.message ||
                err?.response?.data?.errors?.reason?.[0] ||
                "Request failed."
            );
        } finally {
            setLoading(false);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === "Enter") handleSubmit();
        if (e.key === "Escape") onClose();
    };

    return (
        <div
            className="modal show d-block"
            tabIndex={-1}
            role="dialog"
            style={{ backgroundColor: "rgba(0,0,0,0.6)", zIndex: 1050 }}
            onClick={(e) => { if (e.target === e.currentTarget) onClose(); }}
        >
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className={`modal-header ${isBanned ? "bg-success text-white" : "bg-danger text-white"}`}>
                        <h5 className="modal-title">
                            <i className={`fas fa-${isBanned ? "undo" : "ban"} mr-2`}></i>
                            {isBanned ? "Reinstate Enrollment" : "Ban Enrollment"}
                        </h5>
                        <button type="button" className="close text-white" onClick={onClose}>
                            <span>&times;</span>
                        </button>
                    </div>

                    <div className="modal-body">
                        <div className="mb-3">
                            <strong className="d-block">{course.name}</strong>
                            <small className="text-muted">Auth #{course.id}</small>
                        </div>

                        {isBanned && (
                            <div className="alert alert-warning py-2">
                                <i className="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Currently banned</strong>
                                {course.disabled_reason && (
                                    <span className="d-block mt-1">
                                        <em>{course.disabled_reason}</em>
                                    </span>
                                )}
                                {course.disabled_at && (
                                    <small className="text-muted d-block">
                                        Since {course.disabled_at}
                                    </small>
                                )}
                            </div>
                        )}

                        {!isBanned && (
                            <div className="form-group mb-0">
                                <label>
                                    Reason <span className="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={reason}
                                    onChange={(e) => setReason(e.target.value)}
                                    onKeyDown={handleKeyDown}
                                    placeholder="Enter reason for ban..."
                                    maxLength={500}
                                    autoFocus
                                    disabled={loading}
                                />
                            </div>
                        )}

                        {isBanned && (
                            <p className="mb-0 text-muted small">
                                This will remove the ban and restore the student's access to this enrollment.
                            </p>
                        )}

                        {error && (
                            <div className="alert alert-danger py-2 mt-3 mb-0">
                                <small>{error}</small>
                            </div>
                        )}
                    </div>

                    <div className="modal-footer">
                        <button className="btn btn-secondary" onClick={onClose} disabled={loading}>
                            Cancel
                        </button>
                        <button
                            className={`btn ${isBanned ? "btn-success" : "btn-danger"}`}
                            onClick={handleSubmit}
                            disabled={loading}
                        >
                            {loading
                                ? <><i className="fas fa-spinner fa-spin mr-2"></i>Processing...</>
                                : isBanned
                                    ? <><i className="fas fa-undo mr-2"></i>Reinstate Enrollment</>
                                    : <><i className="fas fa-ban mr-2"></i>Confirm Ban</>
                            }
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BanEnrollmentModal;
