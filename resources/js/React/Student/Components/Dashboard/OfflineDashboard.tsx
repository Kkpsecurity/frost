import React from "react";
import { Container, Row, Col, Card, Alert } from "react-bootstrap";
import { useClassroom } from "../../hooks/useClassroom";

interface OfflineDashboardProps {
    courseAuthId?: number | null;
}

/**
 * OfflineDashboard - Student dashboard view
 *
 * Shown when:
 * - No courseDate exists (no scheduled session)
 * - Waiting for a class to be scheduled
 * - Browsing course materials
 *
 * Responsibilities:
 * - Display course information
 * - Show course materials
 * - Display student progress
 * - Show upcoming classes notice
 *
 * Does NOT handle:
 * - Data fetching
 * - Polling logic
 * - Route switching (MainDashboard handles that)
 */
const OfflineDashboard: React.FC<OfflineDashboardProps> = ({ courseAuthId }) => {
    const classroom = useClassroom();

    const course = classroom?.course;
    const config = classroom?.config;

    return (
        <Container fluid className="py-4">
            {/* Waiting Notice */}
            <Row className="mb-4">
                <Col>
                    <Alert variant="info">
                        <Alert.Heading>⏳ Waiting for Class to Start</Alert.Heading>
                        <p>
                            No scheduled classroom session at this moment. Check back soon or explore the course materials below.
                        </p>
                    </Alert>
                </Col>
            </Row>

            {/* Course Information */}
            <Row className="mb-4">
                <Col>
                    <h1>📚 {course?.title || "Course"}</h1>
                    <p className="lead text-muted">
                        {course?.description || "No description available"}
                    </p>
                </Col>
            </Row>

            {/* Course Details */}
            <Row className="mb-4">
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">ℹ️ Course Info</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {course ? (
                                <>
                                    <p>
                                        <strong>Total Duration:</strong> {course.total_minutes} minutes
                                    </p>
                                    <p>
                                        <strong>Category:</strong> {course.category === 'D' ? 'Desktop' : 'Web'}
                                    </p>
                                    <p>
                                        <strong>Expiration Days:</strong> {course.policy_expire_days} days
                                    </p>
                                    <p>
                                        <strong>Status:</strong>{" "}
                                        <span className={`badge ${course.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                            {course.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No course data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>

                {/* Classroom Settings */}
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">⚙️ Classroom Settings</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {config?.features ? (
                                <>
                                    <p>
                                        <strong>Face Verification:</strong>{" "}
                                        <span className={`badge ${config.features.faceVerificationEnabled ? 'bg-warning' : 'bg-secondary'}`}>
                                            {config.features.faceVerificationEnabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Attendance Tracking:</strong>{" "}
                                        <span className={`badge ${config.features.attendanceTrackingEnabled ? 'bg-success' : 'bg-secondary'}`}>
                                            {config.features.attendanceTrackingEnabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Chat:</strong>{" "}
                                        <span className={`badge ${config.features.chatEnabled ? 'bg-info' : 'bg-secondary'}`}>
                                            {config.features.chatEnabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Video Calls:</strong>{" "}
                                        <span className={`badge ${config.features.videoCallEnabled ? 'bg-primary' : 'bg-secondary'}`}>
                                            {config.features.videoCallEnabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No configuration data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Debug Info */}
            <Row>
                <Col>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">🔍 Debug Info</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <p>
                                <strong>courseAuthId:</strong> {courseAuthId || 'Not provided'}
                            </p>
                            <p>
                                <strong>Has courseDate:</strong> {classroom?.courseDate ? 'Yes' : 'No'}
                            </p>
                            <p>
                                <strong>Has instUnit:</strong> {classroom?.instUnit ? 'Yes' : 'No'}
                            </p>
                            <p>
                                <strong>Classroom Loading:</strong> {classroom?.loading ? 'Loading...' : 'Done'}
                            </p>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OfflineDashboard;
