import React, { useState, useEffect, useMemo } from "react";
import { ProgressBar } from "react-bootstrap";
import { parse } from "date-fns";


type Lesson = {
    id: number;
    title: string;
    credit_minutes: number;
};

type CourseUnitLesson = {
    id: number;
    course_unit_id: number;
    lesson_id: number;
    instr_seconds: number;
};

interface Props {
    lessonStartTime: string;
    activeLesson: number | null;
    lessons: { [key: number]: Lesson };
    courseUnitLessons: { [key: number]: CourseUnitLesson };
    isPaused: boolean;
}

const LessonProgress: React.FC<Props> = ({
    lessonStartTime,
    activeLesson,
    lessons,
    courseUnitLessons,
    isPaused,
}) => {
    console.log("LessonProgressData", {
        start_time: lessonStartTime,
        activeLesson: activeLesson,
        lessons: lessons,
        courseUnitLessons: courseUnitLessons,
        isPaused: isPaused,
    });

    LessonProgress.defaultProps = {
        lessonStartTime: "",
        activeLesson: null,
        lessons: [],
        courseUnitLessons: [],
        isPaused: false,
    };

    /**
     * The progress of the active lesson
     */
    const [progress, setProgress] = useState<number>(0);

    /**
     * The start time of the active lesson
     */
    const [startTime, setStartTime] = useState<Date | null>(null);

    /**
     * The index of the active lesson
     */
    const [activeLessonIndex, setActiveLessonIndex] = useState<number | null>(
        null
    );

    /**
     * Set the active lesson start time
     */
    useEffect(() => {
        console.log("activeLesson value:", activeLesson); // Debug

        if (activeLesson !== null && activeLesson > 0) {
            setStartTime(
                parse(lessonStartTime, "M/d/yyyy, h:mm:ss a", new Date())
            );
            setActiveLessonIndex(activeLesson);
        } else {
            setStartTime(null);
            setActiveLessonIndex(null);
        }
    }, [activeLesson, lessonStartTime]);

    /**
     * The start date time
     */
    const startDateTime = useMemo(() => startTime ?? new Date(), [startTime]);

    useEffect(() => {
        let interval: number;

        if (startTime && activeLesson && courseUnitLessons[activeLesson]) {
            const recommendedTime = courseUnitLessons[activeLesson].instr_seconds;
            const startTimestamp = startTime.getTime();

            const updateProgress = () => {
                const currentTimestamp = new Date().getTime();
                const timeDifference = currentTimestamp - startTimestamp;
                const progressPercentage = Math.round((timeDifference / (recommendedTime * 1000)) * 100);

                if (progressPercentage >= 100) {
                    setProgress(100);
                    clearInterval(interval);
                } else {
                    setProgress(progressPercentage);
                }
            };

            interval = window.setInterval(updateProgress, 1000);
        } else {
            setProgress(0);
        }

        return () => {
            clearInterval(interval);
        };
    }, [isPaused, activeLesson, courseUnitLessons, setProgress, startTime]);


    if (activeLesson) {
        return (
            <>
                {courseUnitLessons[activeLesson] ? (
                    <>
                        <div className="d-flex bg-black justify-content-between p-2 m-0">
                            <b className="text-white">
                                {lessons[activeLesson]?.title ?? ""} Started:{" "}
                                <span className="text-yellow">{`${startDateTime.toLocaleDateString()} ${startDateTime.toLocaleTimeString()}`}</span>
                            </b>
                            <time>
                                {Math.floor(courseUnitLessons[activeLesson].instr_seconds / 60)}:
                                {courseUnitLessons[activeLesson].instr_seconds % 60}
                            </time>
                        </div>
                        <ProgressBar now={progress} label={`${progress}`} />
                    </>
                ) : (
                    <div className="alert alert-info m-0">
                        Pending lesson to start
                    </div>
                )}
            </>
        );
    } else {
        return <></>;
    }
};

export default LessonProgress;