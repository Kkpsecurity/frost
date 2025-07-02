import React, { useState, useEffect, memo, useMemo } from "react";
import { Card, ListGroup, ProgressBar } from "react-bootstrap";
import {
    ChallengeType,
    ClassDataShape,
    CourseLessonType,
} from "../../../../../Config/types";
import { colors } from "../../../../../Config/colors";
import LessonProgress from "../../../../../Components/Plugins/Students/LessonProgress";
import ChallengeHistory from "./ChallengeHistory";

type ActiveLessonCardProps = {
    darkMode: boolean;
    data: ClassDataShape;
    debug: boolean;
};

const ActiveLessonCard = ({ darkMode, data, debug }: ActiveLessonCardProps) => {
    if (debug) console.log("ActiveLessonCard: ", data, darkMode);

    /**
     * The color set for the active lesson card
     */
    const colorSet = darkMode ? colors.dark : colors.light;

    /**
     * The start time of the lesson
     */
    const [lessonStartTime, setLessonStartTime] = useState<string>("");

    useEffect(() => {
        if (data.instUnitLesson && data.instUnitLesson.created_at) {
            const dateStarted: number = parseInt(
                data.instUnitLesson.created_at
            );
            const createdDate = new Date(dateStarted * 1000);
            setLessonStartTime(createdDate.toLocaleString());
        }
    }, [data.instUnitLesson]);

    /**
     * The ClassDataShape
     */
    const { courseLessons, instUnitLesson } = data;

    const courseLessonsArray = Object.values(courseLessons);
    const activeLesson = courseLessonsArray.find(
        (lesson) => lesson.id === instUnitLesson?.lesson_id
    );

    const [challages, setChallages] = useState<ChallengeType[]>([]);

    if (!instUnitLesson) {
        return (
            <Card className={`mb-2 p-0`}>
                <Card.Header
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                        color: colorSet.navbarTextColor,
                    }}
                >
                    <i className="fa fa-dot"></i> Pending Lesson Start
                </Card.Header>
                <Card.Body
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                        color: colorSet.navbarTextColor,
                    }}
                >
                    <p
                        className="text-dark"
                        style={{
                            fontSize: "1.2rem",
                            fontWeight: "bold",
                        }}
                    >
                        Currently, there are no active lessons. Please be aware
                        that maintaining your presence within the class
                        environment during this waiting period is crucial. As
                        soon as the instructor initiates the lesson, your
                        immediate participation is required. Failure to be
                        present at the commencement of the lesson may result in
                        you being marked as inactive for the session. This
                        adherence ensures you are fully recognized for your
                        participation and can engage with the lesson content
                        without interruption.
                    </p>
                </Card.Body>
            </Card>
        );
    }

    const allLessonsCompleted =
        courseLessonsArray?.length > 0 &&
        data.completedStudentLessons?.length === courseLessonsArray?.length;

   
    if (allLessonsCompleted) {
        return (
            <Card bg="success" className={`mb-2 mt-3 p-0`}>
                <Card.Header
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                        color: colorSet.navbarTextColor,
                    }}
                >
                    Active Lesson
                </Card.Header>
                <Card.Body>
                    <Card.Title
                        style={{
                            fontSize: "1.2rem",
                            color: colorSet.navbarTextColor,
                        }}
                    >
                        All lessons for today have been completed. Please return
                        tomorrow for the next day's lessons.
                    </Card.Title>
                </Card.Body>
            </Card>
        );
    }

    return (
        <Card bg="info" className={`mb-2 p-0`}>
            <Card.Header
                style={{
                    backgroundColor: colorSet.navbarBgColor,
                    color: colorSet.navbarTextColor,
                }}
            >
                Active Lesson
            </Card.Header>
            <Card.Body>
                <ListGroup variant="flush">
                    <ListGroup.Item>
                        <LessonProgress
                            activeLesson={activeLesson?.id ?? null}
                            lessons={data.courseLessons}
                            lessonStartTime={lessonStartTime}
                            courseUnitLessons={data.courseUnitLessons}
                            isPaused={data?.isPaused}
                        />
                    </ListGroup.Item>
                    <ListGroup.Item>
                        <ChallengeHistory data={data} colorSet={colorSet} />
                    </ListGroup.Item>
                </ListGroup>
            </Card.Body>
        </Card>
    );
};

export default memo(ActiveLessonCard);
