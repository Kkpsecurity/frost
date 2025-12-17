import React from "react";
import { CourseDate } from "../../models";

interface UpcomingScheduleTableProps {
    courseDates: CourseDate[];
}

const UpcomingScheduleTable: React.FC<UpcomingScheduleTableProps> = ({ courseDates }) => {
    console.log("📅 UpcomingScheduleTable received courseDates:", courseDates);
    console.log("📅 UpcomingScheduleTable count:", courseDates?.length || 0);

    // Backend already filters for upcoming dates, no need to filter again
    const upcomingDates = courseDates || [];

    // Don't show table if no upcoming dates
    if (upcomingDates.length === 0) {
        return (
            <div className="row">
                <div className="col-12">
                    <div className="alert alert-info">
                        <i className="fas fa-info-circle mr-2"></i>
                        No upcoming classes scheduled for the next 7 days.
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="row">
            <div className="col-12">
                <div className="card">
                    <div className="card-header">
                        <h5 className="mb-0">
                            <i className="fas fa-calendar-week mr-2"></i>
                            Upcoming Week Schedule ({upcomingDates.length})
                        </h5>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Course</th>
                                        <th>Unit</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {upcomingDates.map((courseDate: any) => (
                                        <tr key={courseDate.id}>
                                            <td>
                                                <strong>
                                                    {new Date(courseDate.starts_at).toLocaleDateString('en-US', {
                                                        weekday: 'short',
                                                        month: 'short',
                                                        day: 'numeric'
                                                    })}
                                                </strong>
                                            </td>
                                            <td>{courseDate.course_unit?.course?.title || 'N/A'}</td>
                                            <td>{courseDate.course_unit?.title || 'N/A'}</td>
                                            <td>{new Date(courseDate.starts_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td>
                                            <td>{new Date(courseDate.ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</td>
                                            <td>
                                                <span className="badge bg-secondary">Upcoming</span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UpcomingScheduleTable;
