import React, { useState, useEffect } from 'react';
import { CourseDate } from '../Components/Offline/types';

interface AssistantViewProps {
    course: CourseDate;
    onExitClassroom: () => void;
}

interface StudentValidation {
    id: number;
    name: string;
    status: 'pending' | 'approved' | 'rejected';
    verification_type: 'id_card' | 'photo' | 'biometric';
    submitted_at: string;
    image_url?: string;
}

interface ChatMessage {
    id: number;
    user_name: string;
    message: string;
    timestamp: string;
    type: 'student' | 'instructor' | 'system';
}

const AssistantView: React.FC<AssistantViewProps> = ({ course, onExitClassroom }) => {
    const [activeTab, setActiveTab] = useState<'validation' | 'support' | 'monitor'>('validation');
    const [pendingValidations, setPendingValidations] = useState<StudentValidation[]>([]);
    const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
    const [supportTickets, setSupportTickets] = useState<any[]>([]);
    const [classroomStats, setClassroomStats] = useState({
        totalStudents: 0,
        activeStudents: 0,
        pendingValidations: 0,
        supportRequests: 0
    });

    // Mock data for development - replace with actual API calls
    useEffect(() => {
        // Mock pending validations
        setPendingValidations([
            {
                id: 1,
                name: "John Smith",
                status: 'pending',
                verification_type: 'id_card',
                submitted_at: '2025-10-08 09:30:00',
                image_url: '/images/sample-id.jpg'
            },
            {
                id: 2,
                name: "Maria Garcia",
                status: 'pending',
                verification_type: 'photo',
                submitted_at: '2025-10-08 09:32:00',
                image_url: '/images/sample-photo.jpg'
            }
        ]);

        // Mock chat messages
        setChatMessages([
            {
                id: 1,
                user_name: "Student123",
                message: "I'm having trouble accessing the lesson materials",
                timestamp: '09:45:00',
                type: 'student'
            },
            {
                id: 2,
                user_name: "Instructor",
                message: "I'll help you with that",
                timestamp: '09:46:00',
                type: 'instructor'
            }
        ]);

        setClassroomStats({
            totalStudents: 25,
            activeStudents: 23,
            pendingValidations: 2,
            supportRequests: 1
        });
    }, []);

    const handleValidationAction = (validationId: number, action: 'approve' | 'reject') => {
        setPendingValidations(prev =>
            prev.map(validation =>
                validation.id === validationId
                    ? { ...validation, status: action === 'approve' ? 'approved' : 'rejected' }
                    : validation
            )
        );
        console.log(`${action} validation for ID: ${validationId}`);
    };

    const handleSendChatMessage = (message: string) => {
        const newMessage: ChatMessage = {
            id: chatMessages.length + 1,
            user_name: "Assistant",
            message,
            timestamp: new Date().toLocaleTimeString('en-US', { hour12: false }),
            type: 'instructor'
        };
        setChatMessages(prev => [...prev, newMessage]);
    };

    return (
        <div className="assistant-view h-100 d-flex flex-column">
            {/* Header */}
            <div
                className="assistant-header d-flex justify-content-between align-items-center p-3"
                style={{
                    backgroundColor: "var(--frost-primary-color, #212a3e)",
                    color: "var(--frost-white-color, #ffffff)",
                }}
            >
                <div className="d-flex align-items-center">
                    <button
                        className="btn btn-link text-white p-0 me-3"
                        onClick={onExitClassroom}
                        style={{ textDecoration: "none" }}
                    >
                        <i className="fas fa-arrow-left fs-4"></i>
                    </button>
                    <div>
                        <h5 className="mb-0">
                            <i className="fas fa-user-shield me-2"></i>
                            Assistant Dashboard - {course.course_name}
                        </h5>
                        <small className="text-white-50">
                            Supporting: {course.instructor_name || "Main Instructor"} | Time: {course.time}
                        </small>
                    </div>
                </div>
                <div className="d-flex align-items-center gap-3">
                    {/* Quick Stats */}
                    <div className="d-flex gap-3 text-center">
                        <div>
                            <div className="fw-bold">{classroomStats.activeStudents}</div>
                            <small>Active</small>
                        </div>
                        <div>
                            <div className="fw-bold text-warning">{classroomStats.pendingValidations}</div>
                            <small>Pending</small>
                        </div>
                        <div>
                            <div className="fw-bold text-info">{classroomStats.supportRequests}</div>
                            <small>Support</small>
                        </div>
                    </div>
                </div>
            </div>

            {/* Navigation Tabs */}
            <div className="assistant-nav border-bottom">
                <ul className="nav nav-tabs nav-fill">
                    <li className="nav-item">
                        <button
                            className={`nav-link ${activeTab === 'validation' ? 'active' : ''}`}
                            onClick={() => setActiveTab('validation')}
                        >
                            <i className="fas fa-id-badge me-2"></i>
                            Student Validation
                            {pendingValidations.filter(v => v.status === 'pending').length > 0 && (
                                <span className="badge bg-warning ms-2">
                                    {pendingValidations.filter(v => v.status === 'pending').length}
                                </span>
                            )}
                        </button>
                    </li>
                    <li className="nav-item">
                        <button
                            className={`nav-link ${activeTab === 'support' ? 'active' : ''}`}
                            onClick={() => setActiveTab('support')}
                        >
                            <i className="fas fa-headset me-2"></i>
                            Support & Chat
                        </button>
                    </li>
                    <li className="nav-item">
                        <button
                            className={`nav-link ${activeTab === 'monitor' ? 'active' : ''}`}
                            onClick={() => setActiveTab('monitor')}
                        >
                            <i className="fas fa-desktop me-2"></i>
                            Classroom Monitor
                        </button>
                    </li>
                </ul>
            </div>

            {/* Tab Content */}
            <div className="assistant-content flex-grow-1 p-3">
                {activeTab === 'validation' && (
                    <div className="validation-panel">
                        <h6 className="mb-3">
                            <i className="fas fa-clipboard-check me-2"></i>
                            Pending Student Validations
                        </h6>
                        {pendingValidations.filter(v => v.status === 'pending').length === 0 ? (
                            <div className="text-center text-muted py-5">
                                <i className="fas fa-check-circle fs-1 mb-3"></i>
                                <p>All student validations are complete!</p>
                            </div>
                        ) : (
                            <div className="row g-3">
                                {pendingValidations
                                    .filter(validation => validation.status === 'pending')
                                    .map(validation => (
                                    <div key={validation.id} className="col-md-6 col-lg-4">
                                        <div className="card">
                                            <div className="card-body">
                                                <div className="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 className="card-title mb-0">{validation.name}</h6>
                                                    <span className="badge bg-warning">
                                                        {validation.verification_type.replace('_', ' ')}
                                                    </span>
                                                </div>
                                                <p className="card-text small text-muted">
                                                    Submitted: {validation.submitted_at}
                                                </p>
                                                {validation.image_url && (
                                                    <div className="mb-3">
                                                        <img
                                                            src={validation.image_url}
                                                            alt="Verification"
                                                            className="img-fluid rounded"
                                                            style={{ maxHeight: '150px', width: '100%', objectFit: 'cover' }}
                                                        />
                                                    </div>
                                                )}
                                                <div className="d-flex gap-2">
                                                    <button
                                                        className="btn btn-success btn-sm flex-fill"
                                                        onClick={() => handleValidationAction(validation.id, 'approve')}
                                                    >
                                                        <i className="fas fa-check me-1"></i>
                                                        Approve
                                                    </button>
                                                    <button
                                                        className="btn btn-danger btn-sm flex-fill"
                                                        onClick={() => handleValidationAction(validation.id, 'reject')}
                                                    >
                                                        <i className="fas fa-times me-1"></i>
                                                        Reject
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                )}

                {activeTab === 'support' && (
                    <div className="support-panel">
                        <div className="row h-100">
                            <div className="col-md-8">
                                <div className="card h-100">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-comments me-2"></i>
                                            Live Chat Support
                                        </h6>
                                    </div>
                                    <div className="card-body d-flex flex-column">
                                        <div className="chat-messages flex-grow-1 mb-3" style={{ maxHeight: '400px', overflowY: 'auto' }}>
                                            {chatMessages.map(message => (
                                                <div key={message.id} className="mb-2">
                                                    <div className={`d-flex ${message.type === 'instructor' ? 'justify-content-end' : 'justify-content-start'}`}>
                                                        <div className={`card ${message.type === 'instructor' ? 'bg-primary text-white' : 'bg-light'}`} style={{ maxWidth: '70%' }}>
                                                            <div className="card-body py-2 px-3">
                                                                <div className="small fw-bold">{message.user_name}</div>
                                                                <div>{message.message}</div>
                                                                <div className="small opacity-75">{message.timestamp}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="chat-input">
                                            <div className="input-group">
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    placeholder="Type your message to help students..."
                                                    onKeyDown={(e) => {
                                                        if (e.key === 'Enter' && e.currentTarget.value.trim()) {
                                                            handleSendChatMessage(e.currentTarget.value);
                                                            e.currentTarget.value = '';
                                                        }
                                                    }}
                                                />
                                                <button className="btn btn-primary">
                                                    <i className="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="card">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-exclamation-triangle me-2"></i>
                                            Support Requests
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="text-center text-muted">
                                            <i className="fas fa-clipboard-list fs-3 mb-2"></i>
                                            <p>No active support requests</p>
                                            <small>Students can request help during the class</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {activeTab === 'monitor' && (
                    <div className="monitor-panel">
                        <div className="row g-3">
                            <div className="col-md-8">
                                <div className="card">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-users me-2"></i>
                                            Student Activity Monitor
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="text-center text-muted py-5">
                                            <i className="fas fa-chart-line fs-1 mb-3"></i>
                                            <p>Real-time student activity monitoring</p>
                                            <small>Track lesson progress, engagement, and attendance</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="card">
                                    <div className="card-header">
                                        <h6 className="mb-0">
                                            <i className="fas fa-tachometer-alt me-2"></i>
                                            Class Statistics
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="mb-3">
                                            <div className="d-flex justify-content-between">
                                                <span>Total Students:</span>
                                                <strong>{classroomStats.totalStudents}</strong>
                                            </div>
                                        </div>
                                        <div className="mb-3">
                                            <div className="d-flex justify-content-between">
                                                <span>Currently Active:</span>
                                                <strong className="text-success">{classroomStats.activeStudents}</strong>
                                            </div>
                                        </div>
                                        <div className="mb-3">
                                            <div className="d-flex justify-content-between">
                                                <span>Pending Validation:</span>
                                                <strong className="text-warning">{classroomStats.pendingValidations}</strong>
                                            </div>
                                        </div>
                                        <div className="mb-3">
                                            <div className="d-flex justify-content-between">
                                                <span>Support Requests:</span>
                                                <strong className="text-info">{classroomStats.supportRequests}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default AssistantView;
