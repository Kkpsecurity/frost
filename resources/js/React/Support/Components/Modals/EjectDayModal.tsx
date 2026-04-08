import React, { useState } from "react";
import axios from "axios";

export interface AttendanceRecord {
    id: number;
    date: string;
    day_name: string;
    formatted_date: string;
    time: string;
    status: string;
    course_date_id: number;
    created_at: string;
    ejected_at: string | null;
    ejected_for: string | null;
}

interface EjectDayModalProps {
    record: AttendanceRecord;
    onClose: () => void;
    onSuccess: () => void;
}

const EjectDayModal: React.FC<EjectDayModalProps> = ({ record, onClose, onSuccess }) => {
    const isEjected = !!record.ejected_at;
    const [reason, setReason] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async () => {
        if (!isEjected && !reason.trim()) {
            setError("A reason is required to eject a student.");
            return;
        }

        setLoading(true);
        setError(null);

        try {
            await axios.post(
                `/admin/support/student-tools/toggle-day-ban/${record.id}`,
                isEjected ? {} : { reason: reason.trim() }
            );
            onSuccess();
            onClose();
        } catch (err: any) {
            setError(err?.response?.data?.message || "Request failed.");
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
                    <div className={`modal-header ${isEjected ? "bg-success text-white" : "bg-danger text-white"}`}>
                        <h5 className="modal-title">
                            <i className={`fas fa-${isEjected ? "undo" : "ban"} mr-2`}></i>
                            {isEjected ? "Reinstate Class Day" : "Eject from Class Day"}
                        </h5>
                        <button type="button" className="close text-white" onClick={onClose}>
                            <span>&times;</span>
                        </button>
                    </div>

                    <div className="modal-body">
                        <div className="mb-3">
                            <strong className="d-block">
                                <i className="far fa-calendar-alt mr-2"></i>
                                {record.day_name}, {record.formatted_date}
                            </strong>
                            <small className="text-muted">
                                <i className="far fa-clock mr-1"></i>
                                Attended at {record.time}
                            </small>
                        </div>

                        {isEjected && (
                            <div className="alert alert-warning py-2">
                                <i className="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Currently ejected</strong>
                                {record.ejected_for && (
                                    <span className="d-block mt-1">
                                        <em>{record.ejected_for}</em>
                                    </span>
                                )}
                                {record.ejected_at && (
                                    <small className="text-muted d-block">
                                        Since {new Date(record.ejected_at).toLocaleString()}
                                    </small>
                                )}
                            </div>
                        )}

                        {!isEjected && (
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
                                    placeholder="Enter reason for ejection..."
                                    autoFocus
                                    disabled={loading}
                                />
                            </div>
                        )}

                        {isEjected && (
                            <p className="mb-0 text-muted small">
                                This will reinstate the student's attendance for this class day.
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
                            className={`btn ${isEjected ? "btn-success" : "btn-danger"}`}
                            onClick={handleSubmit}
                            disabled={loading}
                        >
                            {loading
                                ? <><i className="fas fa-spinner fa-spin mr-2"></i>Processing...</>
                                : isEjected
                                    ? <><i className="fas fa-undo mr-2"></i>Reinstate Day</>
                                    : <><i className="fas fa-ban mr-2"></i>Confirm Eject</>
                            }
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default EjectDayModal;
