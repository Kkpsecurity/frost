import React from "react";
import DayLessonsPanel from "../DayLessonsPanel";

interface DayLessonsModalProps {
    studentUnitId: number;
    dayName: string;
    formattedDate: string;
    toolPermissions: Record<string, boolean>;
    onClose: () => void;
    onRefresh: () => void;
}

const DayLessonsModal: React.FC<DayLessonsModalProps> = ({
    studentUnitId,
    dayName,
    formattedDate,
    toolPermissions,
    onClose,
    onRefresh,
}) => {
    return (
        <div
            className="modal show d-block"
            tabIndex={-1}
            role="dialog"
            style={{ backgroundColor: "rgba(0,0,0,0.6)", zIndex: 1050 }}
            onClick={(e) => { if (e.target === e.currentTarget) onClose(); }}
        >
            <div className="modal-dialog modal-lg" role="document">
                <div className="modal-content">
                    <div className="modal-header bg-primary text-white">
                        <h5 className="modal-title">
                            <i className="fas fa-book-open mr-2"></i>
                            Lessons — {dayName}, {formattedDate}
                        </h5>
                        <button type="button" className="close text-white" onClick={onClose}>
                            <span>&times;</span>
                        </button>
                    </div>

                    <div className="modal-body">
                        <DayLessonsPanel
                            studentUnitId={studentUnitId}
                            onRefresh={onRefresh}
                            toolPermissions={toolPermissions}
                        />
                    </div>

                    <div className="modal-footer">
                        <button className="btn btn-secondary" onClick={onClose}>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DayLessonsModal;
