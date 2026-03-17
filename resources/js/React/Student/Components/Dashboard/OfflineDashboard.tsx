import React from "react";
import { Container, Row, Col, Card, Alert } from "react-bootstrap";
import { useStudent } from "../../context/StudentContext";
import { t } from "@/i18n";

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
                        <Alert.Heading>⏳ {t('dashboard.waitingTitle')}</Alert.Heading>
                        <p>
                            {t('dashboard.waitingDesc')}
                        </p>
                    </Alert>
                </Col>
            </Row>

            {/* Course Information */}
            <Row className="mb-4">
                <Col>
                    <h1>📚 {(course as any)?.course_name || course?.title_long || course?.title || course?.name || t('dashboard.courseFallback')}</h1>
                    <p className="lead text-muted">
                        {course?.description || t('dashboard.noDescription')}
                    </p>
                </Col>
            </Row>

            {/* Course Details */}
            <Row className="mb-4">
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">ℹ️ {t('dashboard.courseInfoTitle')}</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {course ? (
                                <>
                                    <p>
                                        <strong>{t('dashboard.labelCourseName')}</strong> {(course as any)?.course_name || course.title_long || course.title || course.name}
                                    </p>
                                    <p className="mb-0">
                                        <strong>{t('dashboard.labelStatus')}</strong>{" "}
                                        <span className="badge bg-success">{t('dashboard.enrolled')}</span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">{t('dashboard.noCourseData')}</p>
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
                            <Card.Title className="mb-0">👤 {t('dashboard.studentInfoTitle')}</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {student?.student ? (
                                <>
                                    <p>
                                        <strong>{t('dashboard.labelName')}</strong> {student.student.name}
                                    </p>
                                    <p className="mb-0">
                                        <strong>{t('dashboard.labelEmail')}</strong> {student.student.email}
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">{t('dashboard.noStudentData')}</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OfflineDashboard;

