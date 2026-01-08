import React from "react";

interface AttendanceRecord {
    id: number;
    date: string;
    day_name: string;
    formatted_date: string;
    time: string;
    status: string;
    course_date_id: number;
    created_at: string;
}

interface AttendanceHistoryProps {
    history: AttendanceRecord[];
}

const AttendanceHistory: React.FC<AttendanceHistoryProps> = ({ history }) => {
    if (!history || history.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No attendance days found for this course.
            </div>
        );
    }

    return (
        <div className="list-group">
            {history.map((record) => (
                <div
                    key={record.id}
                    className="list-group-item d-flex justify-content-between align-items-center"
                >
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
                        <span className="badge badge-success badge-pill">
                            <i className="fas fa-check mr-1"></i>
                            Present
                        </span>
                        <br />
                        <small className="text-muted">
                            Recorded: {new Date(record.created_at).toLocaleDateString()}
                        </small>
                    </div>
                </div>
            ))}
        </div>
    );
};

export default AttendanceHistory;
