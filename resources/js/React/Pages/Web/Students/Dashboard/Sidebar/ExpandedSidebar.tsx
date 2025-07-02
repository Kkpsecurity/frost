import React, { useEffect } from "react";
import { ListGroup, Row, Col } from "react-bootstrap";
import { ClassDataShape, CourseLessonType } from "../../../../../Config/types";
import { colors } from "../../../../../Config/colors"; // Import the color palette

interface Props {
    data: ClassDataShape;
    getVariantClass: (
        lesson: CourseLessonType,
        isCompleted: boolean,
        inCompleted: boolean,
        studentFailed: boolean
    ) => string;
    activeLessonTitle: string;
    viewLesson: (lessonId: number) => void;
    setActiveLessonTitle: (title: string) => void;
    darkMode: boolean;
    toggleSidebarVisibility: () => void; // Add this line
    debug: boolean;
}

const ExpandedSidebar: React.FC<Props> = ({
    data,
    getVariantClass,
    activeLessonTitle,
    setActiveLessonTitle,
    viewLesson,
    darkMode,
    toggleSidebarVisibility, // Add this line
    debug = false,
}) => {
    /**
     * @description Set the active lesson title
     * @param lesson
     */
    const handleSetActiveLessonTitle = (lesson: CourseLessonType) => {
        if (
            data.instUnitLesson &&
            data.instUnitLesson.lesson_id === lesson.id
        ) {
            setActiveLessonTitle(lesson.title);
        }
    };

    useEffect(() => {
        const activeLesson = Object.values(data.courseLessons).find(
            (lesson: CourseLessonType) =>
                data.instUnitLesson?.lesson_id === lesson.id
        );

        if (activeLesson) {
            handleSetActiveLessonTitle(activeLesson);
        }
    }, [data.instUnitLesson]);

    const currentColors = darkMode ? colors.dark : colors.light;

    return (
        <div style={{ maxHeight: "100vh", overflowY: "auto" }}>
            <ListGroup variant="flush">
                {Object.values(data.courseLessons).map(
                    (lesson: CourseLessonType) => {
                        let isCompleted = false; // Indicates the student has completed the lesson
                        let inCompleted = false; // Indicates the student has not completed the lesson
                        let studentFailed = false; // Indicates the student has failed the lesson
                        let instIsCompleted = false; // Indicates the instructor has completed the lesson

                        const {
                            previousLessons, // Student's previous lessons
                            lessonInProgress,
                            is_live_class,
                        } = data;

                        /**
                         * Indicates the student has previous lessons
                         */
                        if (
                            Array.isArray(previousLessons) &&
                            previousLessons.includes(lesson.id)
                        ) {
                            isCompleted = true;
                        } else {
                            if (is_live_class) {
                                /**
                                 * Indicates that the instructor
                                 * has ended the lesson array
                                 */
                                if (
                                    data.completedInstLessons &&
                                    Array.isArray(data.completedInstLessons) &&
                                    data.completedInstLessons.some(
                                        (completedInstLessons) => {
                                            return (
                                                completedInstLessons.lesson_id ===
                                                lesson.id
                                            );
                                        }
                                    )
                                ) {
                                    instIsCompleted = true;
                                }

                                /**
                                 * Indicates that the lesson the student is completed
                                 */
                                if (debug)
                                    console.log(
                                        "THE-COMPLETED-LESSONS: ",
                                        data.completedStudentLessons
                                    );

                                if (instIsCompleted && isCompleted) {
                                    inCompleted = false;
                                }

                                /**
                                 * Indicates that the lesson the student is completed
                                 */
                                if (data.completedStudentLessons) {
                                    isCompleted =
                                        data.completedStudentLessons.some(
                                            (completedLesson) => {
                                                if (
                                                    completedLesson.lesson_id ===
                                                        lesson.id &&
                                                    completedLesson.dnc_at
                                                ) {
                                                    return true;
                                                }
                                                return false;
                                            }
                                        );
                                }

                                // we can consider it as incomplete
                                if (instIsCompleted && !isCompleted) {
                                    inCompleted = true;
                                }
                            }
                        }

                        return (
                            <ListGroup.Item
                                key={lesson["id"]}
                                className={`d-flex justify-content-between align-items-center mb-1 font-weight-bold text-capitalize border-bottom 
                                ${getVariantClass(
                                    lesson,
                                    isCompleted,
                                    inCompleted,
                                    studentFailed
                                )}`}
                            >
                                <Row>
                                    <Col lg={12}>
                                        <h5
                                            style={{
                                                color: "#111",
                                            }}
                                        >
                                            <b>{lesson["title"]}</b>
                                        </h5>
                                    </Col>
                                    <Col
                                        lg={8}
                                        style={{
                                            color: "#111",
                                        }}
                                    >
                                        Credit Minutes:{" "}
                                        <b>{lesson["credit_minutes"]}</b>
                                    </Col>
                                    <Col lg={4}>
                                        {(data.is_live_class && (
                                            <span>
                                                {isCompleted ? (
                                                    <span
                                                        style={{
                                                            color: "#111",
                                                        }}
                                                    >
                                                        Completed
                                                    </span>
                                                ) : data.instUnitLesson &&
                                                  data.instUnitLesson
                                                      .lesson_id ===
                                                      lesson.id ? (
                                                    <span
                                                        style={{
                                                            fontSize: "0.9rem",
                                                            color: "#111",
                                                        }}
                                                    >
                                                        In Progress
                                                    </span>
                                                ) : studentFailed ? (
                                                    <span
                                                        style={{
                                                            color: "#111",
                                                        }}
                                                    >
                                                        Timed-Out
                                                    </span>
                                                ) : inCompleted ? (
                                                    <span
                                                        style={{
                                                            color: "#111",
                                                        }}
                                                    >
                                                        Incomplete
                                                    </span>
                                                ) : (
                                                    <span
                                                        style={{
                                                            color: "#111",
                                                        }}
                                                    >
                                                        Pending
                                                    </span>
                                                )}
                                            </span>
                                        )) || (
                                            <span style={{ color: "#111" }}>
                                                <a
                                                    href="#"
                                                    onClick={() =>
                                                        viewLesson(lesson["id"])
                                                    }
                                                >
                                                    View
                                                </a>
                                            </span>
                                        )}
                                    </Col>
                                </Row>
                            </ListGroup.Item>
                        );
                    }
                )}
            </ListGroup>
        </div>
    );
};

export default ExpandedSidebar;
