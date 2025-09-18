import React from 'react';
import { Card, Button, OverlayTrigger, Tooltip } from 'react-bootstrap';

interface Lesson {
    id: number;
    title: string;
    duration: number; // minutes
    completed: boolean;
    inProgress?: boolean;
}

interface StudentClassroomSidebarProps {
    collapsed: boolean;
    onToggle: () => void;
    lessons?: Lesson[];
}

const StudentClassroomSidebar: React.FC<StudentClassroomSidebarProps> = ({
    collapsed,
    onToggle,
    lessons = [
        { id: 1, title: "Security Officer And Private Investigator Licensure", duration: 60, completed: true },
        { id: 2, title: "Legal Powers and Limitations", duration: 180, completed: true },
        { id: 3, title: "Emergency Procedures", duration: 120, completed: false, inProgress: true },
        { id: 4, title: "Report Writing", duration: 90, completed: false },
        { id: 5, title: "Ethics and Professional Conduct", duration: 60, completed: false },
        { id: 6, title: "Communication Skills", duration: 120, completed: false },
        { id: 7, title: "Observation and Documentation", duration: 90, completed: false },
    ]
}) => {
    const getStatusColor = (lesson: Lesson) => {
        if (lesson.completed) return '#28a745'; // Green for completed
        if (lesson.inProgress) return '#ffc107'; // Yellow for in progress
        return '#6c757d'; // Gray for not started
    };

    const getStatusIcon = (lesson: Lesson) => {
        if (lesson.completed) return '✓';
        if (lesson.inProgress) return '◐';
        return '○';
    };

    const renderLessonCard = (lesson: Lesson) => {
        if (collapsed) {
            // Collapsed view - show just initials with tooltip
            const initials = lesson.title.split(' ').map(word => word[0]).join('').substring(0, 3);
            
            return (
                <OverlayTrigger
                    key={lesson.id}
                    placement="right"
                    overlay={
                        <Tooltip id={`lesson-${lesson.id}`}>
                            <strong>{lesson.title}</strong><br/>
                            {lesson.duration} minutes<br/>
                            Status: {lesson.completed ? 'Completed' : lesson.inProgress ? 'In Progress' : 'Not Started'}
                        </Tooltip>
                    }
                >
                    <div
                        className="d-flex align-items-center justify-content-center mb-2 rounded"
                        style={{
                            width: '50px',
                            height: '50px',
                            backgroundColor: getStatusColor(lesson),
                            color: 'white',
                            fontSize: '12px',
                            fontWeight: 'bold',
                            cursor: 'pointer',
                            transition: 'all 0.2s ease'
                        }}
                        onMouseEnter={(e) => {
                            e.currentTarget.style.transform = 'scale(1.05)';
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.transform = 'scale(1)';
                        }}
                    >
                        {initials}
                    </div>
                </OverlayTrigger>
            );
        }

        // Expanded view - full lesson cards
        return (
            <Card 
                key={lesson.id}
                className="mb-3 shadow-sm"
                style={{
                    backgroundColor: getStatusColor(lesson),
                    border: 'none',
                    borderRadius: '8px',
                    transition: 'all 0.2s ease'
                }}
                onMouseEnter={(e) => {
                    e.currentTarget.style.transform = 'translateY(-2px)';
                    e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                }}
                onMouseLeave={(e) => {
                    e.currentTarget.style.transform = 'translateY(0)';
                    e.currentTarget.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                }}
            >
                <Card.Body className="p-3">
                    <div className="d-flex justify-content-between align-items-start mb-2">
                        <span className="badge bg-light text-dark fs-6">
                            {getStatusIcon(lesson)}
                        </span>
                        <small className="text-white-50">
                            {lesson.duration} min
                        </small>
                    </div>
                    
                    <Card.Title 
                        className="text-white mb-2" 
                        style={{ 
                            fontSize: '0.9rem', 
                            lineHeight: '1.3',
                            fontWeight: '600'
                        }}
                    >
                        {lesson.title}
                    </Card.Title>
                    
                    <Button 
                        variant="light" 
                        size="sm" 
                        className="w-100 fw-bold"
                        style={{
                            borderRadius: '6px',
                            fontSize: '0.8rem'
                        }}
                        onClick={() => console.log(`Opening lesson: ${lesson.title}`)}
                    >
                        <i className="fas fa-play me-1"></i>
                        View
                    </Button>
                </Card.Body>
            </Card>
        );
    };

    return (
        <div 
            className="classroom-sidebar h-100 d-flex flex-column"
            style={{
                backgroundColor: '#2c3448', // Slightly lighter than main background
                borderRight: '1px solid #3a4454',
                position: 'relative'
            }}
        >
            {/* Sidebar Header */}
            <div className="p-3 border-bottom border-secondary">
                {!collapsed && (
                    <h6 className="text-white mb-0 fw-bold">
                        <i className="fas fa-book-open me-2 text-success"></i>
                        Course Lessons
                    </h6>
                )}
            </div>

            {/* Lessons List */}
            <div 
                className="flex-grow-1 p-3" 
                style={{ 
                    overflowY: 'auto',
                    maxHeight: 'calc(100vh - 120px)'
                }}
            >
                {lessons.map(renderLessonCard)}
            </div>

            {/* Toggle Button */}
            <div className="p-2 border-top border-secondary">
                <Button
                    variant="outline-light"
                    size="sm"
                    className="w-100"
                    onClick={onToggle}
                    style={{
                        borderRadius: '6px',
                        fontSize: '0.8rem'
                    }}
                >
                    <i className={`fas fa-chevron-${collapsed ? 'right' : 'left'}`}></i>
                    {!collapsed && <span className="ms-2">Collapse</span>}
                </Button>
            </div>
        </div>
    );
};

export default StudentClassroomSidebar;