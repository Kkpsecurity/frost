import React, { useEffect, useState } from "react";
import { Container, ListGroup, ListGroupItem } from "react-bootstrap";
import Loader from "../../../../Components/Widgets/Loader";
import {
    CourseType,
    CourseLessonType,
    StudentLessonType,
    CourseUnitLessonType,
    StudentUnitType,
} from "../../../../Config/types";

const StudentCourseLessons = ({ classData, selectedCourseId }) => {
    if (!classData && !selectedCourseId) return <Loader />;

    /**
     * courseLessons - The lessons for the student's course.
     */
    const [courseLessons, setCourseLessons] = useState<CourseUnitLessonType>();

    /**
     * The Status of the list item.
     */
    const [listItemStatus, setListItemStatus] = useState<string>("");

    /**
     * lessons - The lessons for the class.
     * studentLessons - The lessons the student has taken for the live day.
     * studentCompletedLessons - The lessons that have been completed.
     * activeLesson - The lesson id that is currently active.
     */
    const {
        lessons,
        currentStudentUnit,
        selectedCourseAuthId,
        studentCompletedLessons,
        activeLesson,
    } = classData;

    const findLesson = (
        activeLessonId: number,
        lessons: { [key: string]: CourseLessonType[] }
    ): CourseLessonType | null => {
        for (const lessonArray of Object.values(lessons)) {
            if (Array.isArray(lessonArray)) {
                const foundLesson = lessonArray.find(
                    (lesson) => lesson.id === activeLessonId
                );
                if (foundLesson) {
                    return foundLesson;
                }
            }
        }

        return null;
    };

    const getLessonStatus = (lessonId: number): string => {
        if (activeLesson === lessonId) {
            return "Active";
        }

        const completedLessons =
            studentCompletedLessons &&
            studentCompletedLessons[selectedCourseAuthId];

        // Check if completedLessons is not null or undefined
        if (completedLessons) {
            // Note: returns a list of lessonIDs that have been completed
            for (const studentLessonId of Object.values(completedLessons)) {
                if (studentLessonId === lessonId) {
                    return "Completed";
                }
            }
        }

        const foundLesson = findLesson(lessonId, lessons) as unknown as
            | StudentLessonType
            | undefined;
        if (foundLesson) {
            if (foundLesson.dnc_at) {
                return "Incompleted";
            }
        }

        // If the student was active in the class but missed the lesson
        if (currentStudentUnit) {
            return "Failed";
        }

        return "Pending";
    };


    
    const getLessonStatusClass = (lessonId: number): string => {
        const status = getLessonStatus(lessonId);

        switch (status) {
            case "Active":
                return "list-group-item-primary"; // Active
            case "Completed":
                return "list-group-item-success"; // Completed
            case "Incompleted":
                return "list-group-item-warning"; // Incompleted due to DNC
            case "Failed":
                return "list-group-item-danger"; // Failed due to being active but missing
            default:
                return "bg-gray"; // Pending
        }
    };

    // Example usage within a component:
    const LessonList = ({ lessons }) => {
        return (
            <ListGroup>
                {lessons.map((lesson: CourseLessonType) => (
                    <ListGroupItem
                        key={lesson.id}
                        className={getLessonStatusClass(lesson.id)}
                    >
                        <div>Lesson {lesson.title}</div>
                        <div>{getLessonStatus(lesson.id)}</div>
                    </ListGroupItem>
                ))}
            </ListGroup>
        );
    };

    const getStatusDescription = (statusClass) => {
        switch (statusClass) {
            case "list-group-item-primary":
                return "Active: Currently ongoing lesson.";
            case "list-group-item-success":
                return "Completed: You have finished this lesson.";
            case "list-group-item-danger":
                return "Incompleted: Did not complete due to challenges.";
            case "list-group-item-warning":
                return "Failed: You were present but missed this lesson.";
            case "list-group-item-secondary":
                return "Pending: Yet to be started.";
            default:
                return "Unknown status.";
        }
    };

    useEffect(() => {
        if (lessons && selectedCourseId) {
            const cLessons = lessons[selectedCourseId];
            setCourseLessons(cLessons);
        }
    }, [lessons, selectedCourseId]);

    return (
        <Container>
            <h4 className="title h4">Student's Lessons Progress</h4>
            <ListGroup>
            {courseLessons &&
                    Object.entries(courseLessons).map(([lessonId, lesson]) =>
                        // Ensure lesson is not null before rendering
                        lesson ? (
                            <ListGroupItem
                                key={lessonId}
                                className={`d-flex justify-content-between align-items-center ${getLessonStatusClass(
                                    parseInt(lessonId)
                                )}`}
                            >
                                <div>
                                    <i className="fa fa-arrow-right mr-1" />{" "}
                                    {lesson.title}
                                </div>
                                <div>{getLessonStatus(parseInt(lessonId))}</div>
                            </ListGroupItem>
                        ) : (
                            <ListGroupItem
                                key={lessonId}
                                className="list-group-item-secondary"
                            >
                                <div>Lesson not found</div>
                            </ListGroupItem>
                        )
                    )}
            </ListGroup>
        </Container>
    );
};

export default StudentCourseLessons;
