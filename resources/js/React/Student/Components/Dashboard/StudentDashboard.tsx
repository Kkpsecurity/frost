import React from "react";
import { Container, Row, Col, Card } from "react-bootstrap";
import { useStudent } from "../../context/StudentContext";
import { useClassroom } from "../../context/ClassroomContext";

interface StudentDashboardProps {
    courseAuthId?: number | null;
}

/**
 * StudentDashboard - Main dashboard component
 *
 * Receives data from contexts (StudentContext & ClassroomContext)
 * Handles all UI rendering and business logic
 * Does NOT poll data - that's StudentDataLayer's job
 *
 * Structure:
 * - Header with student info
 * - Course cards grid
 * - Classroom status
 * - Notifications
 */
const StudentDashboard: React.FC<StudentDashboardProps> = ({ courseAuthId }) => {
    const studentData = useStudent();
    const classroomData = useClassroom();

    return (
        <Container fluid className="p-4">
            <Row>
                <Col>
                    <h1>Student Dashboard</h1>
                    <p className="text-muted">
                        Course Auth ID: {courseAuthId}
                    </p>
                </Col>
            </Row>

            {/* Student Info Card */}
            <Row className="mb-4">
                <Col md={12}>
                    <Card>
                        <Card.Header>
                            <Card.Title className="mb-0">Student Information</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {studentData.student ? (
                                <div>
                                    <p>
                                        <strong>Name:</strong> {studentData.student.name}
                                    </p>
                                    <p>
                                        <strong>Email:</strong> {studentData.student.email}
                                    </p>
                                    <p>
                                        <strong>Role:</strong> {studentData.student.role}
                                    </p>
                                </div>
                            ) : (
                                <p className="text-muted">No student data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Progress Card */}
            <Row className="mb-4">
                <Col md={12}>
                    <Card>
                        <Card.Header>
                            <Card.Title className="mb-0">Progress</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {studentData.progress ? (
                                <div>
                                    <p>
                                        <strong>Total Courses:</strong>{" "}
                                        {studentData.progress.total_courses}
                                    </p>
                                    <p>
                                        <strong>Completed:</strong>{" "}
                                        {studentData.progress.completed}
                                    </p>
                                    <p>
                                        <strong>In Progress:</strong>{" "}
                                        {studentData.progress.in_progress}
                                    </p>
                                </div>
                            ) : (
                                <p className="text-muted">No progress data available</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Classroom Status */}
            <Row className="mb-4">
                <Col md={12}>
                    <Card>
                        <Card.Header>
                            <Card.Title className="mb-0">Classroom Status</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <p>
                                <strong>Active:</strong>{" "}
                                {classroomData.classroom_status.active ? "Yes" : "No"}
                            </p>
                            <p>
                                <strong>Instructor Online:</strong>{" "}
                                {classroomData.classroom_status.instructor_online
                                    ? "Yes"
                                    : "No"}
                            </p>
                            <p>
                                <strong>Student Count:</strong>{" "}
                                {classroomData.classroom_status.student_count}
                            </p>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Debug: Raw Data */}
            <Row>
                <Col md={12}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">Debug: Polling Data</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <pre className="mb-0">
                                {JSON.stringify(
                                    {
                                        student: studentData.student,
                                        classroom_status: classroomData.classroom_status,
                                        courses_count: studentData.courses.length,
                                        students_count: classroomData.students.length,
                                    },
                                    null,
                                    2
                                )}
                            </pre>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default StudentDashboard;
