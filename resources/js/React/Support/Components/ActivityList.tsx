import React, { useState, useMemo } from "react";

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

const ActivityList: React.FC<ActivityListProps> = ({
    activities,
    studentName,
}) => {
    const [selectedWeek, setSelectedWeek] = useState<string>("current");
    const [currentPage, setCurrentPage] = useState<number>(1);
    const itemsPerPage = 10; // Number of activities per page

    // Calculate week options from activities
    const weekOptions = useMemo(() => {
        if (!activities || activities.length === 0) return [];

        const weeks = new Map<
            string,
            { start: Date; end: Date; label: string }
        >();
        const now = new Date();
        const currentMonday = new Date(now);
        currentMonday.setDate(now.getDate() - now.getDay() + 1); // Monday of current week
        currentMonday.setHours(0, 0, 0, 0);

        // Add current week
        const currentSunday = new Date(currentMonday);
        currentSunday.setDate(currentMonday.getDate() + 6);
        weeks.set("current", {
            start: currentMonday,
            end: currentSunday,
            label: "This Week",
        });

        // Group activities by week
        activities.forEach((activity) => {
            const activityDate = new Date(activity.date);
            const activityMonday = new Date(activityDate);
            activityMonday.setDate(
                activityDate.getDate() - activityDate.getDay() + 1,
            );
            activityMonday.setHours(0, 0, 0, 0);

            // Skip if it's current week
            if (activityMonday.getTime() === currentMonday.getTime()) return;

            const weekKey = activityMonday.toISOString().split("T")[0];
            if (!weeks.has(weekKey)) {
                const sunday = new Date(activityMonday);
                sunday.setDate(activityMonday.getDate() + 6);

                weeks.set(weekKey, {
                    start: activityMonday,
                    end: sunday,
                    label: `${activityMonday.toLocaleDateString("en-US", { month: "short", day: "numeric" })} - ${sunday.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}`,
                });
            }
        });

        return Array.from(weeks.entries()).sort(
            (a, b) => b[1].start.getTime() - a[1].start.getTime(),
        );
    }, [activities]);

    // Filter activities by selected week
    const filteredActivities = useMemo(() => {
        if (!activities || activities.length === 0) return [];

        const selectedWeekData = weekOptions.find(
            ([key]) => key === selectedWeek,
        )?.[1];
        if (!selectedWeekData) return [];

        return activities.filter((activity) => {
            const activityDate = new Date(activity.date);
            return (
                activityDate >= selectedWeekData.start &&
                activityDate <= selectedWeekData.end
            );
        });
    }, [activities, selectedWeek, weekOptions]);

    // Reset to page 1 when week changes
    React.useEffect(() => {
        setCurrentPage(1);
    }, [selectedWeek]);

    // Pagination calculations
    const totalPages = Math.ceil(filteredActivities.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedActivities = filteredActivities.slice(startIndex, endIndex);

    const goToPage = (page: number) => {
        if (page >= 1 && page <= totalPages) {
            setCurrentPage(page);
        }
    };

    const renderPagination = () => {
        if (totalPages <= 1) return null;

        const pageNumbers = [];
        const maxVisiblePages = 5;
        let startPage = Math.max(
            1,
            currentPage - Math.floor(maxVisiblePages / 2),
        );
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        // Adjust start if we're near the end
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            pageNumbers.push(i);
        }

        return (
            <nav aria-label="Activity pagination" className="mt-4">
                <ul className="pagination justify-content-center mb-0">
                    <li
                        className={`page-item ${currentPage === 1 ? "disabled" : ""}`}
                    >
                        <button
                            className="page-link"
                            onClick={() => goToPage(currentPage - 1)}
                            disabled={currentPage === 1}
                            aria-label="Previous"
                        >
                            <span aria-hidden="true">&laquo;</span>
                        </button>
                    </li>
                    {startPage > 1 && (
                        <>
                            <li className="page-item">
                                <button
                                    className="page-link"
                                    onClick={() => goToPage(1)}
                                >
                                    1
                                </button>
                            </li>
                            {startPage > 2 && (
                                <li className="page-item disabled">
                                    <span className="page-link">...</span>
                                </li>
                            )}
                        </>
                    )}
                    {pageNumbers.map((page) => (
                        <li
                            key={page}
                            className={`page-item ${page === currentPage ? "active" : ""}`}
                        >
                            <button
                                className="page-link"
                                onClick={() => goToPage(page)}
                            >
                                {page}
                            </button>
                        </li>
                    ))}
                    {endPage < totalPages && (
                        <>
                            {endPage < totalPages - 1 && (
                                <li className="page-item disabled">
                                    <span className="page-link">...</span>
                                </li>
                            )}
                            <li className="page-item">
                                <button
                                    className="page-link"
                                    onClick={() => goToPage(totalPages)}
                                >
                                    {totalPages}
                                </button>
                            </li>
                        </>
                    )}
                    <li
                        className={`page-item ${currentPage === totalPages ? "disabled" : ""}`}
                    >
                        <button
                            className="page-link"
                            onClick={() => goToPage(currentPage + 1)}
                            disabled={currentPage === totalPages}
                            aria-label="Next"
                        >
                            <span aria-hidden="true">&raquo;</span>
                        </button>
                    </li>
                </ul>
            </nav>
        );
    };

    if (!activities || activities.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No activity recorded for {studentName} in this course yet.
            </div>
        );
    }

    // Group paginated activities by date
    const groupedActivities = paginatedActivities.reduce(
        (groups: { [key: string]: ActivityItem[] }, activity) => {
            const date = activity.date;
            if (!groups[date]) {
                groups[date] = [];
            }
            groups[date].push(activity);
            return groups;
        },
        {},
    );

    // Get icon based on activity type
    const getActivityIcon = (type: string) => {
        switch (type.toLowerCase()) {
            case "login":
                return "fa-sign-in-alt text-success";
            case "logout":
                return "fa-sign-out-alt text-muted";
            case "waiting_room_entry":
                return "fa-clock text-info";
            case "lesson_started":
                return "fa-play-circle text-primary";
            case "lesson_completed":
                return "fa-check-circle text-success";
            case "exam_started":
                return "fa-file-alt text-warning";
            case "exam_completed":
                return "fa-trophy text-success";
            case "video_watched":
                return "fa-video text-info";
            case "document_viewed":
                return "fa-file-pdf text-danger";
            default:
                return "fa-circle text-secondary";
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
                    {/* Activity count and pagination info */}
                    <div className="d-flex justify-content-between align-items-center mb-3">
                        <small className="text-muted">
                            Showing {startIndex + 1}-
                            {Math.min(endIndex, filteredActivities.length)} of{" "}
                            {filteredActivities.length} activities
                        </small>
                        {totalPages > 1 && (
                            <small className="text-muted">
                                Page {currentPage} of {totalPages}
                            </small>
                        )}
                    </div>

                    {Object.keys(groupedActivities)
                        .sort()
                        .reverse()
                        .map((date) => (
                            <div key={date} className="mb-4">
                                <h5 className="text-muted mb-3">
                                    <i className="fas fa-calendar-day mr-2"></i>
                                    {new Date(date).toLocaleDateString(
                                        "en-US",
                                        {
                                            weekday: "long",
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        },
                                    )}
                                </h5>
                                <ul className="list-group">
                                    {groupedActivities[date].map((activity) => (
                                        <li
                                            key={activity.id}
                                            className="list-group-item"
                                        >
                                            <div className="d-flex align-items-start">
                                                <div className="mr-3 flex-shrink-0">
                                                    <i
                                                        className={`fas ${getActivityIcon(activity.type)} fa-lg`}
                                                    ></i>
                                                </div>
                                                <div className="flex-grow-1 overflow-hidden">
                                                    <div className="d-flex justify-content-between align-items-start">
                                                        <div
                                                            className="flex-grow-1 mr-3"
                                                            style={{
                                                                minWidth: 0,
                                                            }}
                                                        >
                                                            <h6 className="mb-1 text-break">
                                                                {
                                                                    activity.description
                                                                }
                                                            </h6>
                                                            {activity.details && (
                                                                <p
                                                                    className="mb-0 text-muted small text-break"
                                                                    style={{
                                                                        wordWrap:
                                                                            "break-word",
                                                                    }}
                                                                >
                                                                    {
                                                                        activity.details
                                                                    }
                                                                </p>
                                                            )}
                                                        </div>
                                                        <small className="text-muted text-nowrap flex-shrink-0">
                                                            {new Date(
                                                                activity.timestamp,
                                                            ).toLocaleTimeString(
                                                                "en-US",
                                                                {
                                                                    hour: "2-digit",
                                                                    minute: "2-digit",
                                                                },
                                                            )}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}

                    {/* Pagination controls */}
                    {renderPagination()}
                </>
            )}
        </div>
    );
};

export default ActivityList;
