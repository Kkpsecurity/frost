import React from 'react';
import Loader from '../../../components/Loader';
import CoursesGrid from '../../components/Offline/CoursesGrid';
import EmptyState from '../../components/Offline/EmptyState';
import UpcomingScheduleTable from '../../components/Offline/UpcomingScheduleTable';

interface BulletinBoardProps {
    classroomData?: any;
    instructorData?: any;
    isLoading?: boolean;
}

/**
 * BulletinBoard - Offline Mode Dashboard
 *
 * DISPLAY ONLY component - receives data via props from InstructorDataLayer context.
 * No API calls here - data is already being polled by the data layer.
 *
 * Features:
 * - Welcome header with instructor name
 * - Today's scheduled classes
 * - Upcoming scheduled classes
 */
const BulletinBoard: React.FC<BulletinBoardProps> = ({
    classroomData,
    instructorData,
    isLoading = false
}) => {
    // Expect classroomData structure: { courseDates: [], upcomingDates: [], courses: [], lessons: [] }
    const courseDates = classroomData?.courseDates || [];
    const upcomingDates = classroomData?.upcomingDates || [];
    const courses = classroomData?.courses || [];

    console.log("📋 BulletinBoard received classroomData:", classroomData);
    console.log("📋 BulletinBoard courseDates (today):", courseDates);
    console.log("📋 BulletinBoard upcomingDates (future):", upcomingDates);
    console.log("📋 BulletinBoard courses:", courses);

    // Loading state
    if (isLoading) {
        return (
            <div className="bulletin-board">
                <div style={{ minHeight: '200px' }} className="d-flex justify-content-center align-items-center">
                    <Loader size={32} label="Loading bulletin board..." />
                </div>
            </div>
        );
    }

    // No data state
    if (!courseDates || courseDates.length === 0) {
        return (
            <div className="bulletin-board">
                <div className="p-3">
                    <EmptyState
                        title="No Courses Scheduled"
                        message="There are no courses scheduled for today."
                        icon="fas fa-calendar-times"
                    />
                </div>
            </div>
        );
    }

    return (
        <div className="bulletin-board">
            {/* Content */}
            <div className="container-fluid" style={{ padding: '1rem' }}>
                {/* Today's Schedule - Display ALL course cards */}
                <div className="row mb-4">
                    <div className="col-12 mb-3">
                        <h4 className="mb-3">
                            <i className="fas fa-calendar-day mr-2"></i>
                            Today's Scheduled Classes ({courseDates.length})
                        </h4>
                    </div>
                    <div className="col-12">
                        <CoursesGrid courses={courseDates} />
                    </div>
                </div>

                {/* Upcoming Schedule Table - Shows tomorrow and beyond */}
                <UpcomingScheduleTable courseDates={upcomingDates} />
            </div>
        </div>
    );
};

export default BulletinBoard;
