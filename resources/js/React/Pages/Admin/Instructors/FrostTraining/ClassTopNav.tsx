import React, { useState } from "react";
import { Nav } from "react-bootstrap";
import {
    CourseMeetingShape,
    LaravelAdminShape,
} from "../../../../Config/types";
import apiClient from "../../../../Config/axios";
import { useAssitantModerate } from "../../../../Hooks/Admin/useInstructorHooks";
import ClassRoomTools from "./Partials/ClassRoomTools";
import { UseMutationResult } from "@tanstack/react-query";

const COMPLETION_COOLDOWN_KEY = "completionCooldownExpiration";

interface Props {
    laravel: LaravelAdminShape;
    activeLesson: number | null;
    data: CourseMeetingShape | null;
    markLessonComplete: Function;
    markCourseComplete: Function;
    setAssignedAssistantId: Function;
    pauseLesson: Function;
    isPaused: boolean;
    debug?: boolean;
}

const ClassTopNav: React.FC<Props> = ({
    laravel,
    activeLesson,
    data,
    markLessonComplete,
    markCourseComplete,
    setAssignedAssistantId,
    pauseLesson,
    isPaused,
    debug = false,
}) => {
    if (debug) console.log("ClassTopNav: ", data, activeLesson);

    /**
     * The cooldown timer for the complete button
     */
    const completionCooldown = React.useRef<number>(0);

    const [courseMarkedCompleted, setCourseMarkedCompleted] =
        React.useState(false);

    /**
     * Button is Disabled after last lesson is cloesed
     */
    const [completeBtnDisableTimer, setCompleteBtnDisableTimer] =
        React.useState(true);

    const MarkDayCompletedButton = () => {
        if (
            data?.completedLessons?.length === Object.keys(data.lessons).length
        ) {
            return (
                <a
                    onClick={(e) => {
                        if (
                            !courseMarkedCompleted &&
                            completionCooldown.current < 1
                        ) {
                            if (
                                window.confirm(
                                    "Are you sure you want to mark this day as completed?"
                                )
                            ) {
                                markCourseComplete(e);
                                setCourseMarkedCompleted(true);
                            }
                        }
                    }}
                    className={`btn btn-danger nav-link ${
                        courseMarkedCompleted || completionCooldown.current > 0
                            ? "disabled text-white"
                            : ""
                    }`}
                    href="#"
                >
                    {completionCooldown.current > 0
                        ? `Please wait ${Math.floor(
                              completionCooldown.current / 60
                          )}m ${completionCooldown.current % 60}s`
                        : "Mark Day Completed"}
                </a>
            );
        } else {
            return (
                <span>
                    {data?.completedLessons?.length || 0} of{" "}
                    {Object.keys(data.lessons).length} lessons completed
                </span>
            );
        }
    };

    /**
     * Force update the component
     */
    const [, forceUpdate] = useState({});

    interface AxiosCustomResponse {
        data: {
            success: boolean;
            message: string;
            id: number;
        };
    }

    // Define the type for the mutation function's argument
    type MutateArg = {
        courseDateId: string;
        type: string;
    };

    const mutate: UseMutationResult<
        AxiosCustomResponse,
        unknown,
        MutateArg,
        unknown
    > = useAssitantModerate() as unknown as UseMutationResult<
        AxiosCustomResponse,
        unknown,
        MutateArg,
        unknown
    >;

    const leaveClass = React.useCallback(
        async (e: React.MouseEvent<HTMLButtonElement>) => {
            e.preventDefault();

            const courseDateIdFromEvent = e.currentTarget.getAttribute("id");

            try {
                if (courseDateIdFromEvent) {
                    const response: AxiosCustomResponse =
                        await mutate.mutateAsync({
                            courseDateId: courseDateIdFromEvent,
                            type: "leave",
                        });

                    setAssignedAssistantId(null);
                    window.location.reload();
                } else {
                    console.error("courseDateId is missing");
                }
            } catch (error: any) {
                console.error("POSTERROR: ", error);
            }
        },
        [mutate, setAssignedAssistantId]
    );

    /**
     * Manages the cooldown timer for the complete button
     * @returns
     */
    const startCooldownTimer = () => {
        const cooldownEndTimeStr = localStorage.getItem(
            COMPLETION_COOLDOWN_KEY
        );

        if (cooldownEndTimeStr) {
            const cooldownEndTime = parseInt(cooldownEndTimeStr);
            const remainingTime = cooldownEndTime - Date.now();

            if (remainingTime > 0) {
                completionCooldown.current = Math.ceil(remainingTime / 1000);

                const interval = setInterval(() => {
                    completionCooldown.current -= 1;
                    forceUpdate({});

                    if (completionCooldown.current <= 0) {
                        clearInterval(interval);
                        localStorage.removeItem(COMPLETION_COOLDOWN_KEY);
                    }
                }, 1000);

                return () => clearInterval(interval); // cleanup function
            } else {
                localStorage.removeItem(COMPLETION_COOLDOWN_KEY);
            }
        }
    };

    const handleMarkCourseComplete = React.useCallback(
        (e) => {
            if (!courseMarkedCompleted && completionCooldown.current < 1) {
                if (
                    window.confirm(
                        "Are you sure you want to mark this day as completed?"
                    )
                ) {
                    markCourseComplete(e);
                    setCourseMarkedCompleted(true);
                }
            }
        },
        [courseMarkedCompleted]
    );

    /**
     * Initialize the cooldown timer
     */
    React.useEffect(() => {
        startCooldownTimer();
    }, []);

    /**
     * Set the cooldown timer when the last lesson is completed
     */
    React.useEffect(() => {
        if (
            data?.completedLessons?.length ===
            Object.keys(data.lessons).length - 1
        ) {
            const cooldownEndTime = Date.now() + 180 * 1000;
            localStorage.setItem(
                COMPLETION_COOLDOWN_KEY,
                String(cooldownEndTime)
            );
            completionCooldown.current = 180;

            // Start the countdown after setting cooldown
            startCooldownTimer();
        }
    }, [data]);

    /**
     * Manages The complete lesson button to allow the instructor to close the lesson
     */
    React.useEffect(() => {
        setCompleteBtnDisableTimer(data?.instructorCanClose ? false : true);
    }, [data.instructorCanClose]);

    if (!data) {
        return (
            <>
                <Nav className="navbar bg-dark navbar-expand-lg navbar-dark">
                    <div>Error Load Navbar no data</div>
                </Nav>
            </>
        );
    }

    return (
        <Nav className="navbar bg-dark navbar-expand-lg navbar-dark">
            <a className="navbar-brand bold uppercase" href="#">
                {data.course.title_long} | {data.course.title}
            </a>
            <span
                className="border ml-4 p-2"
                style={{
                    borderRadius: "25px",
                    backgroundColor: isPaused
                        ? "red"
                        : activeLesson
                        ? "green"
                        : "yellow",
                }}
            >
                {isPaused ? (
                    <span className="text-white">Lesson is Paused</span>
                ) : activeLesson ? (
                    <span className="text-white">Lesson is Live</span>
                ) : null}
            </span>

            <ClassRoomTools
                data={data}
                laravel={laravel}
                leaveClass={leaveClass}
                activeLesson={activeLesson}
                isPaused={isPaused}
                pauseLesson={pauseLesson}
                completeBtnDisableTimer={completeBtnDisableTimer}
                markLessonComplete={markLessonComplete}
                MarkDayCompletedButton={MarkDayCompletedButton}
            />

            <button
                className="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle"
            >
                <span className="navbar-toggler-icon" />
            </button>
        </Nav>
    );
};

export default React.memo(ClassTopNav);
