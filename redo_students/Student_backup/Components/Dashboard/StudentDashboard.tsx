import React from "react";
import { Container, Row, Col } from "react-bootstrap";

interface StudentDashboardProps {
    data: any;
    courseAuthId?: number | null;
}

/**
 * StudentDashboard - Displays student dashboard UI
 * Receives polled data from StudentDataLayer
 * Handles all business logic (face verification, validation, etc.)
 */
const StudentDashboard: React.FC<StudentDashboardProps> = ({ data, courseAuthId }) => {
    // TODO: All dashboard logic goes here
    // - Face verification checks
    // - Validation checks
    // - UI rendering

    return (
        <Container fluid className="py-5">
            <Row>
                <Col>
                    <h1>Student Dashboard</h1>
                    <pre>{JSON.stringify(data, null, 2)}</pre>
                </Col>
            </Row>
        </Container>
    );
};

export default StudentDashboard;
