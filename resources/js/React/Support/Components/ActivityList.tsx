import React, { useState, useMemo } from 'react';

interface ActivityItem {
    id: number;
    date: string;
    type: string;
    description: string;
    details?: string;
    timestamp: string;
}

interface ActivityListProps {
    activities: ActivityItem[];
    studentName: string;
}

const ActivityList: React.FC<ActivityListProps> = ({ activities, studentName }) => {
    const [selectedWeek, setSelectedWeek] = useState<string>('current');

    // Calculate week options from activities
    const weekOptions = useMemo(() => {
        if (!activities || activities.length === 0) return [];

        const weeks = new Map<string, { start: Date; end: Date; label: string }>();
        const now = new Date();
        const currentMonday = new Date(now);
        currentMonday.setDate(now.getDate() - now.getDay() + 1); // Monday of current week
        currentMonday.setHours(0, 0, 0, 0);

        // Add current week
        const currentSunday = new Date(currentMonday);
        currentSunday.setDate(currentMonday.getDate() + 6);
        weeks.set('current', {
            start: currentMonday,
            end: currentSunday,
            label: 'This Week'
        });

        // Group activities by week
        activities.forEach(activity => {
            const activityDate = new Date(activity.date);
            const activityMonday = new Date(activityDate);
            activityMonday.setDate(activityDate.getDate() - activityDate.getDay() + 1);
            activityMonday.setHours(0, 0, 0, 0);

            // Skip if it's current week
            if (activityMonday.getTime() === currentMonday.getTime()) return;

            const weekKey = activityMonday.toISOString().split('T')[0];
            if (!weeks.has(weekKey)) {
                const sunday = new Date(activityMonday);
                sunday.setDate(activityMonday.getDate() + 6);

                weeks.set(weekKey, {
                    start: activityMonday,
                    end: sunday,
                    label: `${activityMonday.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${sunday.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`
                });
            }
        });

        return Array.from(weeks.entries()).sort((a, b) =>
            b[1].start.getTime() - a[1].start.getTime()
        );
    }, [activities]);

    // Filter activities by selected week
    const filteredActivities = useMemo(() => {
        if (!activities || activities.length === 0) return [];

        const selectedWeekData = weekOptions.find(([key]) => key === selectedWeek)?.[1];
        if (!selectedWeekData) return [];

        return activities.filter(activity => {
            const activityDate = new Date(activity.date);
            return activityDate >= selectedWeekData.start && activityDate <= selectedWeekData.end;
        });
    }, [activities, selectedWeek, weekOptions]);

    if (!activities || activities.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No activity recorded for {studentName} in this course yet.
            </div>
        );
    }

    // Group filtered activities by date
    const groupedActivities = filteredActivities.reduce((groups: { [key: string]: ActivityItem[] }, activity) => {
        const date = activity.date;
        if (!groups[date]) {
            groups[date] = [];
        }
        groups[date].push(activity);
        return groups;
    }, {});

    // Get icon based on activity type
    const getActivityIcon = (type: string) => {
        switch (type.toLowerCase()) {
            case 'login':
                return 'fa-sign-in-alt text-success';
            case 'logout':
                return 'fa-sign-out-alt text-muted';
            case 'lesson_started':
                return 'fa-play-circle text-primary';
            case 'lesson_completed':
                return 'fa-check-circle text-success';
            case 'exam_started':
                return 'fa-file-alt text-warning';
            case 'exam_completed':
                return 'fa-trophy text-success';
            case 'video_watched':
                return 'fa-video text-info';
            case 'document_viewed':
                return 'fa-file-pdf text-danger';
            default:
                return 'fa-circle text-secondary';
        }
    };

    return (
        <div className="activity-list">
            {/* Week selector dropdown */}
            {weekOptions.length > 0 && (
                <div className="form-group mb-4">
                    <label htmlFor="weekSelect" className="font-weight-bold">
                        <i className="fas fa-calendar-week mr-2"></i>
                        Select Week
                    </label>
                    <select
                        id="weekSelect"
                        className="form-control"
                        value={selectedWeek}
                        onChange={(e) => setSelectedWeek(e.target.value)}
                    >
                        {weekOptions.map(([key, week]) => (
                            <option key={key} value={key}>
                                {week.label}
                            </option>
                        ))}
                    </select>
                </div>
            )}

            {/* Activities grouped by date */}
            {filteredActivities.length === 0 ? (
                <div className="alert alert-warning">
                    <i className="fas fa-exclamation-triangle mr-2"></i>
                    No activities found for the selected week.
                </div>
            ) : (
                <>
                    {Object.keys(groupedActivities).sort().reverse().map((date) => (
                        <div key={date} className="mb-4">
                            <h5 className="text-muted mb-3">
                                <i className="fas fa-calendar-day mr-2"></i>
                                {new Date(date).toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}
                            </h5>
                            <ul className="list-group">
                                {groupedActivities[date].map((activity) => (
                                    <li key={activity.id} className="list-group-item">
                                        <div className="d-flex align-items-start">
                                            <div className="mr-3">
                                                <i className={`fas ${getActivityIcon(activity.type)} fa-lg`}></i>
                                            </div>
                                            <div className="flex-grow-1">
                                                <div className="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 className="mb-1">{activity.description}</h6>
                                                        {activity.details && (
                                                            <p className="mb-0 text-muted small">{activity.details}</p>
                                                        )}
                                                    </div>
                                                    <small className="text-muted">
                                                        {new Date(activity.timestamp).toLocaleTimeString('en-US', {
                                                            hour: '2-digit',
                                                            minute: '2-digit'
                                                        })}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </>
            )}
        </div>
    );
};

export default ActivityList;
