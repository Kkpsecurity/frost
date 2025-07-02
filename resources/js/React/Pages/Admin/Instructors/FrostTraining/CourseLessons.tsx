import React, { useEffect, useMemo, useState } from "react";
import { ListGroup } from "react-bootstrap";
import { StyledLessonGroup } from "../../../../Styles/StyledLessonGroup.styled";
import {
    CourseLessonType,
    InstUnitLessonType,
    LaravelAdminShape,
    ZoomConfigType,
} from "../../../../Config/types";
import _ from "lodash";
import { start } from "repl";

interface Props {
    laravel: LaravelAdminShape;
    lessons: CourseLessonType[];
    completedLessons: {
        lesson_id: number;
        completed_at: string;
    }[];
    courseDateId: number;
    ActivateLesson: React.MouseEventHandler<HTMLButtonElement>;
    activeLesson: number | null;
    instUnitLesson: InstUnitLessonType;
    zoomStatus: string | null;
}

const LessonList: React.FC<Props> = ({
    laravel,
    lessons,
    completedLessons,
    courseDateId,
    ActivateLesson,
    activeLesson,
    instUnitLesson,
    zoomStatus,
}) => {
    // Create the state for disableTimeout and set it to null
    const [disableTimeout, setDisableTimeout] = useState(() => {
        const savedDisableTimeout = localStorage.getItem("disableTimeout");
        return savedDisableTimeout ? Number(savedDisableTimeout) : null;
    });

    const lessonStartTimer =  120; // laravel.site.instructor_pre_start_minutes;

    /**
     * The time remaining in the 3-minute timeout window.
     */
    const [timeRemaining, setTimeRemaining] = useState<number | null>(null);

    // Get the Variant Class Name for the Lesson buttons
    const getVariantClass = (
        lesson: CourseLessonType,
        isCompleted: boolean
    ): string => {
        if (instUnitLesson && instUnitLesson.lesson_id === lesson.id) {
            return "primary"; // Active Lesson
        }

        if (isCompleted) {
            return "success"; // Completed Lesson
        }

        return "dark"; // Inactive Lesson
    };

    const isActive = useMemo(() => {
        return zoomStatus === "enabled";
    }, [zoomStatus]);

    // Using lodash's debounce
    const saveToLocalStorage = _.debounce((value) => {
        if (value) {
            localStorage.setItem("disableTimeout", String(value));
        } else {
            localStorage.removeItem("disableTimeout");
        }
    }, 300); // 300ms delay

    useEffect(() => {
        saveToLocalStorage(disableTimeout);
    }, [disableTimeout]);

    // Set the initial disableTimeout when activeLesson becomes null
    useEffect(() => {
        console.log("Running the disableTimeout effect because of change in:", {
            activeLesson,
            disableTimeout,
        });

        if (!activeLesson && !disableTimeout) {
            const now = new Date().getTime();            
            const threeMinutes = lessonStartTimer * 1000;
            setDisableTimeout(now + threeMinutes);
        }
    }, [activeLesson]);

    // Use disableTimeout (whether initial or from localStorage) to control button disabling & countdown
    useEffect(() => {
        if (disableTimeout) {
            const timeRemainingAtStart = disableTimeout - new Date().getTime();

            setTimeRemaining(timeRemainingAtStart);

            const timerInterval = setInterval(() => {
                const timeLeft = disableTimeout - new Date().getTime();
                if (timeLeft > 1000) {
                    setTimeRemaining(timeLeft);
                } else {
                    setTimeRemaining(null);
                    clearInterval(timerInterval);
                }
            }, 1000);

            const timerTimeout = setTimeout(() => {
                setDisableTimeout(null);
                clearInterval(timerInterval);
            }, timeRemainingAtStart);

            // Clear the timeout and interval if the component unmounts
            return () => {
                clearTimeout(timerTimeout);
                clearInterval(timerInterval);
            };
        }
    }, [disableTimeout]);

    /**
     * To determin the buttons are disabled or not
     * rule1: if there is no zoomPassCode set for the course date, disable all buttons
     * rule2: if there is no active lesson, enable all buttons
     * rule3: if there is an active lesson all other buttons are disabled     *
     *
     * @param isActive {boolean} - Indicates whether or not a lesson is currently active.
     * @param activeLesson {number | null} - The ID of the currently active lesson, if there is one.
     * @param lesson {CourseLessonType} - The lesson object that the button is associated with.
     *
     * @returns {boolean} - True if the button should be disabled, false otherwise.
     */
    const handleDisableButton = (
        isActive: boolean,
        activeLesson: number | null,
        lesson: CourseLessonType,
        selectLesson: boolean
    ): boolean => {
        const now = new Date().getTime();

        // If within the 3-minute timeout window, disable the button
        if (disableTimeout && now < disableTimeout) {
            return true;
        }

        if (isActive === false) {
            return true; // Disable if no active meeting
        } else {
            if (!activeLesson || typeof activeLesson === "undefined") {
                return false; // If no active lesson, enable all buttons
            } else {
                if (activeLesson !== lesson.id) {
                    return true; // If there's an active lesson and it's not the current lesson, disable the button
                }
            }
        }

        return false; // Default to enabling the button
    };

    return (
        <StyledLessonGroup>
            {zoomStatus === "enabled" && timeRemaining && (
                <div className="alert alert-warning mb-0">
                    Please wait {Math.ceil(timeRemaining / 1000)} seconds before
                    starting another lesson.
                </div>
            )}
            <ListGroup className="lesson-group">
                {Object.values(lessons).map(
                    (lesson: CourseLessonType, index) => {
                        const completedLesson = completedLessons.find(
                            (completedLesson) =>
                                completedLesson.lesson_id === lesson.id
                        );

                        // Set isCompleted to true if completedLesson object is found, otherwise false
                        const isCompleted = completedLesson !== undefined;
                        const disableBut = instUnitLesson
                            ? instUnitLesson.lesson_id === lesson.id
                            : false;

                        return (
                            <ListGroup.Item
                                key={lesson.id}
                                variant={getVariantClass(lesson, isCompleted)}
                            >
                                <h6 className="uppercase">
                                    <b>{lesson.title}</b>
                                </h6>
                                Credit Minutes: <b>{lesson.credit_minutes}</b>
                                <span className="lead float-right">
                                    {isCompleted ? (
                                        <span className="text-dark">
                                            Completed
                                        </span>
                                    ) : instUnitLesson &&
                                      instUnitLesson.lesson_id === lesson.id ? (
                                        <span className="text-dark">
                                            In Progress
                                        </span>
                                    ) : (
                                        <button
                                            className="btn btn-sm btn-success float-right"
                                            onClick={(e) => ActivateLesson(e)}
                                            data-id={lesson.id}
                                            disabled={handleDisableButton(
                                                isActive,
                                                activeLesson,
                                                lesson,
                                                disableBut
                                            )}
                                        >
                                            Begin Lesson
                                        </button>
                                    )}
                                </span>
                            </ListGroup.Item>
                        );
                    }
                )}
            </ListGroup>
        </StyledLessonGroup>
    );
};

export default LessonList;
