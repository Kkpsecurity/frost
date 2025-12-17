import React from "react";
import { Container, Row, Col, Card } from "react-bootstrap";
import { useClassroom } from "../../hooks/useClassroom";

interface OnlineDashboardProps {
    courseAuthId?: number | null;
}

/**
 * OnlineDashboard - Live classroom interface
 *
 * Shown when:
 * - CourseDate exists (scheduled classroom session)
 * - Student is viewing/in a classroom
 *
 * Responsibilities:
 * - Display course and classroom information
 * - Show lesson details
 * - Display instructor info
 * - Show session timing
 *
 * Does NOT handle:
 * - Data fetching
 * - Polling logic
 * - Route switching (MainDashboard handles that)
 */
const OnlineDashboard: React.FC<OnlineDashboardProps> = ({ courseAuthId }) => {
    const classroom = useClassroom();

    const course = classroom?.course;
    const courseDate = classroom?.courseDate;
    const instructor = classroom?.instructor;
    const lessons = classroom?.lessons || [];

    return (
        <Container fluid className="py-4">
            <Row className="mb-4">
                <Col>
                    <h1>📚 {course?.title || "Course"}</h1>
                    <p className="text-muted">
                        {course?.description || "No description available"}
                    </p>
                </Col>
            </Row>

            {/* Classroom Session Info */}
            <Row className="mb-4">
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">📅 Session Details</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {courseDate ? (
                                <>
                                    <p>
                                        <strong>Start:</strong> {courseDate.starts_at}
                                    </p>
                                    <p>
                                        <strong>End:</strong> {courseDate.ends_at}
                                    </p>
                                    <p>
                                        <strong>Mode:</strong> <span className="badge bg-info">{courseDate.mode}</span>
                                    </p>
                                    <p>
                                        <strong>Status:</strong> <span className="badge bg-success">Active</span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No session data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>

                {/* Instructor Info */}
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">👨‍🏫 Instructor</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {instructor ? (
                                <>
                                    <p>
                                        <strong>Name:</strong> {instructor.name}
                                    </p>
                                    <p>
                                        <strong>Email:</strong> {instructor.email}
                                    </p>
                                    <p>
                                        <strong>Status:</strong>{" "}
                                        <span className={`badge ${instructor.online_status === 'online' ? 'bg-success' : 'bg-secondary'}`}>
                                            {instructor.online_status || 'offline'}
                                        </span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">No instructor data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Lessons */}
            <Row>
                <Col>
                    <Card>
                        <Card.Header>
                            <Card.Title className="mb-0">📖 Lessons ({lessons.length})</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {lessons.length > 0 ? (
                                <ul className="list-group">
                                    {lessons.map((lesson, idx) => (
                                        <li key={idx} className="list-group-item">
                                            <strong>{lesson.lesson_data?.title || `Lesson ${idx + 1}`}</strong>
                                            <br />
                                            <small className="text-muted">
                                                Duration: {lesson.lesson_data?.duration_minutes || 0} minutes
                                            </small>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="text-muted">No lessons available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OnlineDashboard;
