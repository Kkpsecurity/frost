import React from "react";
import { Container, Row, Col, Card } from "react-bootstrap";
import { useClassroom } from "../../hooks/useClassroom";
import { formatEasternDateTime } from "../../utils/timeUtils";
import { t } from "@/i18n";

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
                    <h1>📚 {course?.title || t('dashboard.courseFallback')}</h1>
                    <p className="text-muted">
                        {course?.description || t('dashboard.noDescription')}
                    </p>
                </Col>
            </Row>

            {/* Classroom Session Info */}
            <Row className="mb-4">
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">
                                📅 {t('dashboard.sessionDetails')}
                            </Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {courseDate ? (
                                <>
                                    <p>
                                        <strong>{t('dashboard.labelStart')}</strong>{" "}
                                        {formatEasternDateTime(
                                            courseDate.starts_at,
                                        )}
                                    </p>
                                    <p>
                                        <strong>{t('dashboard.labelEnd')}</strong>{" "}
                                        {formatEasternDateTime(
                                            courseDate.ends_at,
                                        )}
                                    </p>
                                    <p>
                                        <strong>{t('dashboard.labelMode')}</strong>{" "}
                                        <span className="badge bg-info">
                                            {courseDate.mode}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>{t('dashboard.labelStatus')}</strong>{" "}
                                        <span className="badge bg-success">
                                            {t('dashboard.statusActive')}
                                        </span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">
                                    {t('dashboard.noSessionData')}
                                </p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>

                {/* Instructor Info */}
                <Col md={6}>
                    <Card className="bg-light">
                        <Card.Header>
                            <Card.Title className="mb-0">
                                👨‍🏫 {t('dashboard.instructorTitle')}
                            </Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {instructor ? (
                                <>
                                    <p>
                                        <strong>{t('dashboard.labelName')}</strong> {instructor.name}
                                    </p>
                                    <p>
                                        <strong>{t('dashboard.labelEmail')}</strong>{" "}
                                        {instructor.email}
                                    </p>
                                    <p>
                                        <strong>{t('dashboard.labelStatus')}</strong>{" "}
                                        <span
                                            className={`badge ${instructor.online_status === "online" ? "bg-success" : "bg-secondary"}`}
                                        >
                                            {instructor.online_status ||
                                                t('dashboard.statusOffline')}
                                        </span>
                                    </p>
                                </>
                            ) : (
                                <p className="text-muted">
                                    {t('dashboard.noInstructorData')}
                                </p>
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
                            <Card.Title className="mb-0">
                                📖 {t('dashboard.lessonsTitle')} ({lessons.length})
                            </Card.Title>
                        </Card.Header>
                        <Card.Body>
                            {lessons.length > 0 ? (
                                <ul className="list-group">
                                    {lessons.map((lesson, idx) => (
                                        <li
                                            key={idx}
                                            className="list-group-item"
                                        >
                                            <strong>
                                                {lesson.lesson_data?.title ||
                                                    t('dashboard.lessonNumber', { number: String(idx + 1) })}
                                            </strong>
                                            <br />
                                            <small className="text-muted">
                                                {t('dashboard.labelDuration')}{" "}
                                                {lesson.lesson_data
                                                    ?.duration_minutes ||
                                                    0}{" "}
                                                {t('dashboard.minutesSuffix')}
                                            </small>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="text-muted">
                                    {t('dashboard.noLessons')}
                                </p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OnlineDashboard;
