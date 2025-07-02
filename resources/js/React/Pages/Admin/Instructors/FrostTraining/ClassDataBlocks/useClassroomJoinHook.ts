import React, { useEffect, useReducer, useState } from "react";
import {
    useBeginANewDay,
    useAssitantTakeOver,
    useAssitantModerate,
    useActiveCourseData,
} from "../../../../../Hooks/Admin/useInstructorHooks";
import { CourseMeetingShape } from "../../../../../Config/types";

const viewReducer = (state, action) => {
    switch (action.type) {
        case "SET_CLASS_VIEW":
            return { ...state, classView: action.payload };
        case "SET_INSTRUCTOR_ASSIGNED":
            return { ...state, instructorAssigned: action.payload };
        case "SET_ASSISTANT_ASSIGNED":
            return { ...state, assignedAssistant: action.payload };
        default:
            return state;
    }
};

interface AxiosCustomResponse {
    data: {
        success: boolean;
        message: string;
        id: number;
    };
}

const classRoomJoinHook = ({
    laravel,
    setAssignedAssistantId,
    validatedInstructor,
    setAssignedInstructorId,
    setCourseDateId,
    courseDateId,
    debug = false,
}) => {
    // ClassRoomJoin
    const mutation = useBeginANewDay();

    /**
     * Assigns the courses data based off the instructor or assistant
     */
    const [assignedCourseMeetingData, setAssignedCourseMeetingData] =
        useState<CourseMeetingShape>();

    /**
     * Handles the view state
     */
    const [state, dispatch] = useReducer(viewReducer, {
        classView: "list",
        instructorAssigned: false,
        isInstructor: false,
        assistantAssigned: false,
    });

    /**
     * Sets the Auth User Id
     */
    const [authUserId, setAuthUserId] = useState<number | null>(null);
    useEffect(() => {
        if (laravel.user) setAuthUserId(laravel.user.id);
    }, [laravel]);

    /**
     * Get the Classroom Data
     */
    const { data: courseMeetingData, status, error } = useActiveCourseData();

    useEffect(() => {
        const fetchData = async () => {
            if (!courseMeetingData || !Array.isArray(courseMeetingData)) {
                console.error(
                    "No CourseMeetingData for InstUnit UseEffect, or data is not an array"
                );
                return;
            }

            console.log("courseMeetingDataSSS", courseMeetingData);

            // Assuming courseMeetingData is an object where each key is an index
            // Convert it to an array if necessary
            const courseMeetingArray = Object.values(courseMeetingData).filter(
                (item) => typeof item === "object" && item !== null
            );

            setCourseDateId(courseMeetingArray?.[0]?.courseDate?.id);

            const filteredData = courseMeetingArray.filter((courseMD) => {
                const { instUnit } = courseMD;

                if (!instUnit) {
                    if (debug) console.log("No InstUnit Found");
                    return false;
                }

                return (
                    instUnit.created_by === validatedInstructor.instructor.id ||
                    instUnit.assistant_id === validatedInstructor.instructor.id
                );
            });

            setAssignedCourseMeetingData(
                filteredData.length > 0 ? filteredData[0] : null
            );
        };

        fetchData();
    }, [courseMeetingData, validatedInstructor]);

    /**
     * Handles the Joining of the Classroom
     * @param InstUnit
     */
    const handleJoinClassroom = async (InstUnit) => {
        if (InstUnit.created_by === validatedInstructor.instructor.id) {
            setAssignedInstructorId(validatedInstructor.instructor.id);
            dispatch({
                type: "SET_INSTRUCTOR_ASSIGNED",
                payload: true,
            });

            dispatch({
                type: "SET_CLASS_VIEW",
                payload: "class",
            });
        }

        if (InstUnit.assistant_id === validatedInstructor.instructor.id) {
            setAssignedAssistantId(validatedInstructor.instructor.id);
            dispatch({
                type: "SET_ASSISTANT_ASSIGNED",
                payload: true,
            });

            dispatch({
                type: "SET_CLASS_VIEW",
                payload: "assistant",
            });
        }
    };

    useEffect(() => {
        const fetchData = async () => {
            if (!assignedCourseMeetingData) {
                console.log("No CourseMeetingData for InstUnit UseEffect");
                return;
            }
            console.log("courseMeetingArray", assignedCourseMeetingData);

            const InstUnit = assignedCourseMeetingData?.instUnit;
            if (!InstUnit) {
                console.log("No InstUnit Found");
                return;
            }

            await handleJoinClassroom(InstUnit);
        };

        fetchData();
    }, [validatedInstructor, assignedCourseMeetingData]);

    /**
     * Handles initializing the Day
     * @param e
     */
    const handleSetView = async (e) => {
        e.preventDefault();

        if (e.target.id === null) {
            alert("InstructorId is missing!");
            return;
        }

        try {
            mutation.mutate(e.target.id);
            setAssignedInstructorId(e.target.id);
        } catch (error) {
            console.error("POSTERROR: ", error);
        }
    };

    /**
     * Mutates the take over of the class
     */
    const assistantTakeOver = useAssitantTakeOver();

    /**
     * Handles the take over of the class
     * @param e
     */
    const handleTakeOver = async (e) => {
        e.preventDefault();

        const courseDateIdFromEvent = e.target.getAttribute("id");

        try {
            if (courseDateIdFromEvent) {
                if (
                    window.confirm(
                        "You are joining the class as an assistant. Do you want to continue?"
                    )
                ) {
                    const response: AxiosCustomResponse =
                        await assistantTakeOver.mutateAsync({
                            courseDateId: courseDateIdFromEvent,
                        });

                    if (response.data && response.data.id) {
                        setAssignedAssistantId(response.data.id);
                    } else {
                        console.error(
                            "ID not found in response data",
                            response.data
                        );
                    }
                }
            } else {
                console.error("courseDateId is missing");
            }
        } catch (error) {
            console.error("POSTERROR: ", error);
        }
    };

    const handelAssistModerate = useAssitantModerate();

    const handleAssignAssistant = async (e) => {
        e.preventDefault();

        const courseDateIdFromEvent = e.target.getAttribute("id");

        try {
            if (courseDateIdFromEvent) {
                if (
                    window.confirm(
                        "You are joining the class as an assistant. Do you want to continue?"
                    )
                ) {
                    const response: AxiosCustomResponse =
                        await handelAssistModerate.mutateAsync({
                            courseDateId: courseDateIdFromEvent,
                            type: "join",
                        });

                    if (response.data && response.data.id) {
                        setAssignedAssistantId(response.data.id);
                    } else {
                        console.error(
                            "ID not found in response data",
                            response.data
                        );
                    }
                }
            } else {
                console.error("courseDateId is missing");
            }
        } catch (error) {
            console.error("POSTERROR: ", error);
        }
    };

    console.log("assignedCourseMeetingData", assignedCourseMeetingData);
    return {
        CourseMeetingData:
            assignedCourseMeetingData && assignedCourseMeetingData,
        status,
        error,

        state,
        dispatch,

        assistantTakeOver,
        handleSetView,
        handleTakeOver,
        handleAssignAssistant,
    };
};

export default classRoomJoinHook;
