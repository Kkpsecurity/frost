import React, { useState } from "react";
import EjectDayModal, { AttendanceRecord } from "./Modals/EjectDayModal";
import DayLessonsModal from "./Modals/DayLessonsModal";

interface AttendanceHistoryProps {
    history: AttendanceRecord[];
    onRefresh: () => void;
    toolPermissions: Record<string, boolean>;
}

type ModalType = "eject" | "lessons" | null;

const AttendanceHistory: React.FC<AttendanceHistoryProps> = ({ history, onRefresh, toolPermissions }) => {
    const [modalRecord, setModalRecord] = useState<AttendanceRecord | null>(null);
    const [modalType, setModalType] = useState<ModalType>(null);

    const openModal = (record: AttendanceRecord, type: ModalType) => {
        setModalRecord(record);
        setModalType(type);
    };

    const closeModal = () => {
        setModalRecord(null);
        setModalType(null);
    };

    if (!history || history.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No attendance days found for this course.
            </div>
        );
    }

    return (
        <>
            <div className="list-group">
                {history.map((record) => {
                    const isEjected = !!record.ejected_at;

                    return (
                        <div
                            key={record.id}
                            className="list-group-item"
                            style={{ borderLeft: isEjected ? "4px solid #dc3545" : "4px solid #28a745" }}
                        >
                            {/* Day header */}
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 className="mb-1">
                                        <i className="far fa-calendar-check mr-2 text-primary"></i>
                                        {record.day_name}, {record.formatted_date}
                                    </h6>
                                    <small className="text-muted">
                                        <i className="far fa-clock mr-1"></i>
                                        {record.time}
                                    </small>
                                </div>
                                <div className="text-right">
                                    {isEjected ? (
                                        <span className="badge badge-danger badge-pill">
                                            <i className="fas fa-ban mr-1"></i>Ejected
                                        </span>
                                    ) : (
                                        <span className="badge badge-success badge-pill">
                                            <i className="fas fa-check mr-1"></i>Present
                                        </span>
                                    )}
                                    <br />
                                    <small className="text-muted">
                                        {new Date(record.created_at).toLocaleDateString()}
                                    </small>
                                </div>
                            </div>

                            {/* Ejection reason summary */}
                            {isEjected && record.ejected_for && (
                                <div className="alert alert-warning alert-sm mt-2 mb-2 py-1 px-2">
                                    <small>
                                        <strong>Ejection reason:</strong> {record.ejected_for}
                                    </small>
                                </div>
                            )}

                            {/* Action buttons */}
                            <div className="mt-2 d-flex align-items-center" style={{ gap: "6px" }}>
                                {toolPermissions["day-ban"] && (
                                    <button
                                        className={`btn btn-sm ${isEjected ? "btn-outline-success" : "btn-outline-danger"}`}
                                        onClick={() => openModal(record, "eject")}
                                    >
                                        <i className={`fas fa-${isEjected ? "undo" : "ban"} mr-1`}></i>
                                        {isEjected ? "Reinstate Day" : "Eject from Day"}
                                    </button>
                                )}
                                <button
                                    className="btn btn-sm btn-outline-primary"
                                    onClick={() => openModal(record, "lessons")}
                                >
                                    <i className="fas fa-book-open mr-1"></i>
                                    Lessons
                                </button>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Modals */}
            {modalType === "eject" && modalRecord && (
                <EjectDayModal
                    record={modalRecord}
                    onClose={closeModal}
                    onSuccess={() => { onRefresh(); closeModal(); }}
                />
            )}
            {modalType === "lessons" && modalRecord && (
                <DayLessonsModal
                    studentUnitId={modalRecord.id}
                    dayName={modalRecord.day_name}
                    formattedDate={modalRecord.formatted_date}
                    toolPermissions={toolPermissions}
                    onClose={closeModal}
                    onRefresh={onRefresh}
                />
            )}
        </>
    );
};

export default AttendanceHistory;
