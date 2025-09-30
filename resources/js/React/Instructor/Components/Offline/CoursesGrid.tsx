import React from "react";
import { CourseDate } from "./types";
import CourseCard from "./CourseCard";
import EmptyState from "./EmptyState";

interface CoursesGridProps {
    courses: CourseDate[];
    onCourseSelect?: (course: CourseDate) => void;
    onStartClass?: (course: CourseDate) => void;
    onRefreshData?: () => void; // Add refresh callback
}

const CoursesGrid: React.FC<CoursesGridProps> = ({
    courses,
    onCourseSelect,
    onStartClass,
    onRefreshData,
}) => {
    if (courses.length === 0) {
        return (
            <EmptyState
                title="No Courses Scheduled"
                message="There are no courses scheduled for today."
                icon="fas fa-calendar-times"
            />
        );
    }

    return (
        <div className="row g-3">
            {courses.map((course) => (
                <div key={course.id} className="col-lg-6 col-xl-4">
                    <CourseCard
                        course={course}
                        onCourseSelect={onCourseSelect}
                        onStartClass={onStartClass}
                        onRefreshData={onRefreshData}
                    />
                </div>
            ))}
        </div>
    );
};

export default CoursesGrid;
