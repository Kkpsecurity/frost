import React from 'react';

interface Lesson {
    id: number;
    lesson_id: number;
    title: string;
    status: 'passed' | 'failed' | 'pending';
    created_at: string | null;
    completed_at: string | null;
    dnc_at: string | null;
}

interface LessonsListProps {
    lessons: Lesson[];
    studentName: string;
}

const LessonsList: React.FC<LessonsListProps> = ({ lessons, studentName }) => {
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'passed':
                return <span className="badge badge-success badge-lg"><i className="fas fa-check mr-1"></i>Passed</span>;
            case 'failed':
                return <span className="badge badge-danger badge-lg"><i className="fas fa-times mr-1"></i>Failed</span>;
            case 'pending':
                return <span className="badge badge-warning badge-lg"><i className="fas fa-clock mr-1"></i>Pending</span>;
            default:
                return <span className="badge badge-secondary badge-lg">{status}</span>;
        }
    };

    return (
        <div className="lessons-list">
            <h6 className="mb-3">
                <i className="fas fa-book-open mr-2"></i>
                Course Lessons
            </h6>
            {!lessons || lessons.length === 0 ? (
                <div className="alert alert-info">
                    <i className="fas fa-info-circle mr-2"></i>
                    No lessons available for this course.
                </div>
            ) : (
                <div className="list-group">
                    <div className="list-group-item">
                        {lessons.map((lesson, index) => (
                            <div
                                key={lesson.id}
                                className={`d-flex justify-content-between align-items-center ${index < lessons.length - 1 ? 'mb-2 pb-2 border-bottom' : ''}`}
                            >
                                <div>
                                    <strong>{lesson.title}</strong>
                                    <br />
                                    {lesson.completed_at && (
                                        <small className="text-muted">
                                            Completed: {new Date(lesson.completed_at).toLocaleDateString('en-US', {
                                                month: 'short',
                                                day: 'numeric',
                                                year: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </small>
                                    )}
                                    {lesson.dnc_at && !lesson.completed_at && (
                                        <small className="text-muted">
                                            Failed: {new Date(lesson.dnc_at).toLocaleDateString('en-US', {
                                                month: 'short',
                                                day: 'numeric',
                                                year: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </small>
                                    )}
                                    {!lesson.completed_at && !lesson.dnc_at && (
                                        <small className="text-muted">
                                            Not started
                                        </small>
                                    )}
                                </div>
                                <div>
                                    {getStatusBadge(lesson.status)}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

export default LessonsList;
