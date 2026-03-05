import React from "react";
import { Container, Row, Col, Card, Alert } from "react-bootstrap";
import { useStudent } from "../../context/StudentContext";

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
    const student = useStudent();

    // Find the selected course enrollment, fall back to first course if no ID passed
    const courseAuth = courseAuthId
        ? student?.courses?.find((c: any) => c.id === courseAuthId)
        : student?.courses?.[0];
    const course = (courseAuth as any)?.course ?? courseAuth;

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
                    <h1>📚 {(course as any)?.course_name || course?.title_long || course?.title || course?.name || "Course"}</h1>
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
                                        <strong>Course Name:</strong> {(course as any)?.course_name || course.title_long || course.title || course.name}
                                    </p>
                                    <p className="mb-0">
                                        <strong>Status:</strong>{" "}
                                        <span className="badge bg-success">Enrolled</span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No course data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Student Info */}
            <Row>
                <Col>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">👤 Student Info</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {student?.student ? (
                                <>
                                    <p>
                                        <strong>Name:</strong> {student.student.name}
                                    </p>
                                    <p className="mb-0">
                                        <strong>Email:</strong> {student.student.email}
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No student data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OfflineDashboard;

