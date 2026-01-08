import React from 'react';

interface AttendanceDay {
    date: string;
    dayName: string;
    isPresent: boolean;
    courseDateId?: number;
}

interface WeeklyAttendanceProps {
    attendance: AttendanceDay[];
}

const WeeklyAttendance: React.FC<WeeklyAttendanceProps> = ({ attendance }) => {
    return (
        <div className="weekly-attendance">
            <h6 className="mb-3">
                <i className="fas fa-calendar-week mr-2"></i>
                Weekly Attendance (Mon - Fri)
            </h6>
            <div className="list-group">
                <div className="list-group-item">
                    {attendance.map((day, index) => (
                        <div key={index} className={`d-flex justify-content-between align-items-center ${index < attendance.length - 1 ? 'mb-2 pb-2 border-bottom' : ''}`}>
                            <div>
                                <strong>Day {index + 1}: {day.dayName}</strong>
                                <br />
                                <small className="text-muted">
                                    {new Date(day.date).toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric',
                                        year: 'numeric'
                                    })}
                                </small>
                            </div>
                            <div>
                                {day.isPresent ? (
                                    <span className="badge badge-success badge-lg">
                                        <i className="fas fa-check mr-1"></i>
                                        Present
                                    </span>
                                ) : (
                                    <span className="badge badge-danger badge-lg">
                                        <i className="fas fa-times mr-1"></i>
                                        Absent
                                    </span>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default WeeklyAttendance;
