import React from 'react';
import { Container, Row, Col, Card, ProgressBar } from 'react-bootstrap';

const DashboardTab: React.FC = () => {
    // Mock data - in real implementation this would come from props
    const courseDetails = {
        purchaseDate: 'March 15, 2024',
        startDate: 'March 20, 2024',
        expiryDate: 'March 20, 2025',
        completedDate: null
    };

    const studentInfo = {
        name: 'John Doe',
        email: 'john.doe@example.com',
        initials: 'JD',
        dateOfBirth: 'January 1, 1990',
        suffix: '',
        phone: '(555) 123-4567'
    };

    const progressData = {
        completed: 2,
        total: 14,
        percentage: (2 / 14) * 100
    };

    return (
        <Container fluid className="p-4">
            <Row className="g-4">
                {/* Left Column - Course Details */}
                <Col md={6}>
                    <Card 
                        className="h-100 shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Header 
                            className="py-3"
                            style={{
                                backgroundColor: '#f39c12', // Orange header like current design
                                color: 'white',
                                borderBottom: '1px solid #3a4454',
                                borderRadius: '12px 12px 0 0'
                            }}
                        >
                            <h5 className="mb-0 fw-bold">
                                <i className="fas fa-info-circle me-2"></i>
                                Course Details
                            </h5>
                        </Card.Header>
                        <Card.Body className="p-4">
                            <div className="course-detail-grid">
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Purchased Date:</span>
                                    <span className="text-white fw-bold">{courseDetails.purchaseDate}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Start Date:</span>
                                    <span className="text-white fw-bold">{courseDetails.startDate}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Expires Date:</span>
                                    <span className="text-white fw-bold">{courseDetails.expiryDate}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3">
                                    <span className="text-white-50 fw-medium">Completed Date:</span>
                                    <span className="text-warning fw-bold">
                                        {courseDetails.completedDate || 'In Progress'}
                                    </span>
                                </div>
                            </div>
                        </Card.Body>
                    </Card>
                </Col>

                {/* Right Column - Student Info */}
                <Col md={6}>
                    <Card 
                        className="h-100 shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Header 
                            className="py-3"
                            style={{
                                backgroundColor: '#f39c12', // Orange header like current design
                                color: 'white',
                                borderBottom: '1px solid #3a4454',
                                borderRadius: '12px 12px 0 0'
                            }}
                        >
                            <h5 className="mb-0 fw-bold">
                                <i className="fas fa-user me-2"></i>
                                Student Information
                            </h5>
                        </Card.Header>
                        <Card.Body className="p-4">
                            <div className="student-info-grid">
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Name:</span>
                                    <span className="text-white fw-bold">{studentInfo.name}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Email:</span>
                                    <span className="text-white fw-bold">{studentInfo.email}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Initials:</span>
                                    <span className="text-white fw-bold">{studentInfo.initials}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Date of Birth:</span>
                                    <span className="text-white fw-bold">{studentInfo.dateOfBirth}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3 border-bottom border-secondary">
                                    <span className="text-white-50 fw-medium">Suffix:</span>
                                    <span className="text-white fw-bold">{studentInfo.suffix || 'None'}</span>
                                </div>
                                <div className="detail-row d-flex justify-content-between align-items-center py-3">
                                    <span className="text-white-50 fw-medium">Phone:</span>
                                    <span className="text-white fw-bold">{studentInfo.phone}</span>
                                </div>
                            </div>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Bottom Section - Lesson Progress */}
            <Row className="mt-4">
                <Col>
                    <Card 
                        className="shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Header 
                            className="py-3"
                            style={{
                                backgroundColor: '#f39c12', // Orange header like current design
                                color: 'white',
                                borderBottom: '1px solid #3a4454',
                                borderRadius: '12px 12px 0 0'
                            }}
                        >
                            <h5 className="mb-0 fw-bold">
                                <i className="fas fa-chart-line me-2"></i>
                                Student Lessons Completed
                            </h5>
                        </Card.Header>
                        <Card.Body className="p-4">
                            <Row className="align-items-center">
                                <Col md={6}>
                                    <div className="progress-info">
                                        <h6 className="text-white mb-2">All lessons</h6>
                                        <div className="d-flex align-items-center">
                                            <span className="text-success fw-bold fs-5 me-3">
                                                {progressData.completed} out of {progressData.total}
                                            </span>
                                            <span className="text-white-50">
                                                ({Math.round(progressData.percentage)}% complete)
                                            </span>
                                        </div>
                                    </div>
                                </Col>
                                <Col md={6}>
                                    <div className="progress-bar-container">
                                        <ProgressBar 
                                            now={progressData.percentage}
                                            style={{
                                                height: '12px',
                                                borderRadius: '6px',
                                                backgroundColor: '#3a4454'
                                            }}
                                        >
                                            <ProgressBar 
                                                now={progressData.percentage}
                                                style={{
                                                    background: 'linear-gradient(45deg, #28a745, #20c997)',
                                                    borderRadius: '6px'
                                                }}
                                            />
                                        </ProgressBar>
                                        <div className="mt-2 text-center">
                                            <small className="text-success fw-bold">
                                                Keep up the great work! {progressData.total - progressData.completed} lessons remaining
                                            </small>
                                        </div>
                                    </div>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default DashboardTab;