import React, { useState } from 'react';
import ClassroomLayout from './ClassroomLayout';
import { CourseDate } from './Offline/types';

interface ClassroomManagerProps {
    initialCourse?: CourseDate;
    onExitClassroom?: () => void;
}

const ClassroomManager: React.FC<ClassroomManagerProps> = ({
    initialCourse,
    onExitClassroom
}) => {
    const [currentCourse, setCurrentCourse] = useState<CourseDate | null>(initialCourse || null);
    const [isInClassroom, setIsInClassroom] = useState(!!initialCourse);

    const handleEnterClassroom = (course: CourseDate) => {
        setCurrentCourse(course);
        setIsInClassroom(true);

        // TODO: API call to create InstUnit
        console.log('Creating InstUnit for course:', course.id);
    };

    const handleExitClassroom = () => {
        setIsInClassroom(false);
        setCurrentCourse(null);

        if (onExitClassroom) {
            onExitClassroom();
        }

        // TODO: API call to mark InstUnit as completed if needed
        console.log('Exiting classroom, saving session state');
    };

    // Show classroom interface if we're in a class
    if (isInClassroom && currentCourse) {
        return (
            <ClassroomLayout
                course={currentCourse}
                onBackToOverview={handleExitClassroom}
            />
        );
    }

    // If no course provided, show selection interface
    return (
        <div className="container-fluid p-4">
            <div className="text-center py-5">
                <i className="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                <h3 className="text-muted mb-3">Ready to Teach</h3>
                <p className="text-muted mb-4">
                    Select a course from your dashboard to start teaching.
                </p>
                <button
                    className="btn btn-primary"
                    onClick={() => onExitClassroom?.()}
                >
                    <i className="fas fa-arrow-left me-2"></i>
                    Back to Dashboard
                </button>
            </div>
        </div>
    );
};

export default ClassroomManager;
