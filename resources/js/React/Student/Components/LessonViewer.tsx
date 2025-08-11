import React, { useState } from 'react';

interface LessonViewerProps {
    lessonTitle?: string;
    lessonContent?: string;
    duration?: string;
}

const LessonViewer: React.FC<LessonViewerProps> = ({
    lessonTitle = 'Lesson Title',
    lessonContent = 'Lesson content will be displayed here...',
    duration = '0:00'
}) => {
    const [isCompleted, setIsCompleted] = useState(false);

    const markAsCompleted = () => {
        setIsCompleted(true);
    };

    return (
        <div className="lesson-viewer">
            <div className="card">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h6>{lessonTitle}</h6>
                    <span className="badge bg-info">{duration}</span>
                </div>
                <div className="card-body">
                    <div className="lesson-content mb-4">
                        <p>{lessonContent}</p>
                    </div>

                    <div className="lesson-actions">
                        <button
                            className={`btn btn-${isCompleted ? 'success' : 'primary'}`}
                            onClick={markAsCompleted}
                            disabled={isCompleted}
                        >
                            {isCompleted ? 'Completed ✓' : 'Mark as Complete'}
                        </button>

                        <button className="btn btn-outline-secondary ms-2">
                            <i className="fas fa-bookmark"></i> Bookmark
                        </button>

                        <button className="btn btn-outline-info ms-2">
                            <i className="fas fa-notes-medical"></i> Take Notes
                        </button>
                    </div>

                    {isCompleted && (
                        <div className="alert alert-success mt-3">
                            <i className="fas fa-check-circle"></i> Great job! Lesson completed.
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default LessonViewer;
