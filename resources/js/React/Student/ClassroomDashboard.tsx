import React from 'react';
import { Navbar, Container, Row, Col, Card } from "react-bootstrap";
import type { DashboardData } from './types/dashboard';

interface ClassroomDashboardProps {
    data: DashboardData;
}

const ClassroomDashboard: React.FC<ClassroomDashboardProps> = ({ data }) => {
    const { user, incompleteAuths, completedAuths, stats } = data;
    const userFullName = `${user.fname} ${user.lname}`;

    return (
        <div style={{ minHeight: "100vh", display: "flex", flexDirection: "column" }}>
            {/* Top Bar */}
            <Navbar bg="dark" variant="dark" className="border-bottom" style={{ height: 60 }}>
                <Container fluid>
                    <Navbar.Brand>Welcome, {userFullName}</Navbar.Brand>
                </Container>
            </Navbar>

            {/* Main Layout */}
            <div style={{ flex: 1, display: "flex", overflow: "hidden" }}>
                {/* Left Sidebar - Stats */}
                <div
                    style={{
                        width: 250,
                        backgroundColor: "#f8f9fa",
                        borderRight: "1px solid #dee2e6",
                        padding: "1rem",
                    }}
                >
                    <h5 className="mb-3">Your Progress</h5>
                    <div className="d-grid gap-2">
                        <Card className="mb-2">
                            <Card.Body>
                                <Card.Title>Active Courses</Card.Title>
                                <h3>{stats.active_courses}</h3>
                            </Card.Body>
                        </Card>
                        <Card className="mb-2">
                            <Card.Body>
                                <Card.Title>Completed</Card.Title>
                                <h3>{stats.completed_courses}</h3>
                            </Card.Body>
                        </Card>
                        <Card>
                            <Card.Body>
                                <Card.Title>Overall Progress</Card.Title>
                                <div className="progress">
                                    <div
                                        className="progress-bar"
                                        role="progressbar"
                                        style={{ width: `${stats.overall_progress}%` }}
                                        aria-valuenow={stats.overall_progress}
                                        aria-valuemin={0}
                                        aria-valuemax={100}
                                    >
                                        {stats.overall_progress}%
                                    </div>
                                </div>
                            </Card.Body>
                        </Card>
                    </div>
                </div>

                {/* Content Area */}
                <div style={{ flex: 1, padding: "1rem", overflowY: "auto" }}>
                    {/* Active Courses */}
                    <section className="mb-4">
                        <h4>Active Courses</h4>
                        <Row xs={1} md={2} lg={3} className="g-4">
                            {incompleteAuths.map((auth) => (
                                <Col key={auth.id}>
                                    <Card>
                                        <Card.Body>
                                            <Card.Title>{auth.course?.title || 'Untitled Course'}</Card.Title>
                                            <Card.Text>
                                                {auth.course?.description || 'No description available'}
                                            </Card.Text>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            ))}
                            {incompleteAuths.length === 0 && (
                                <Col>
                                    <Card className="text-center">
                                        <Card.Body>
                                            <Card.Text>No active courses</Card.Text>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            )}
                        </Row>
                    </section>

                    {/* Completed Courses */}
                    <section>
                        <h4>Completed Courses</h4>
                        <Row xs={1} md={2} lg={3} className="g-4">
                            {completedAuths.map((auth) => (
                                <Col key={auth.id}>
                                    <Card className="bg-light">
                                        <Card.Body>
                                            <Card.Title>{auth.course?.title || 'Untitled Course'}</Card.Title>
                                            <Card.Text>
                                                {auth.course?.description || 'No description available'}
                                            </Card.Text>
                                            <small className="text-muted">
                                                Completed: {auth.completed_at ? new Date(auth.completed_at).toLocaleDateString() : 'Unknown'}
                                            </small>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            ))}
                            {completedAuths.length === 0 && (
                                <Col>
                                    <Card className="text-center">
                                        <Card.Body>
                                            <Card.Text>No completed courses yet</Card.Text>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            )}
                        </Row>
                    </section>
                </div>
            </div>
        </div>
    );
};

export default ClassroomDashboard;
