import React, { useContext, useEffect, useState } from "react";
import { ValidatedInstructorContext } from "../../../../Context/Admin/ValidatedInstructorContext";
import classRoomJoinHook from "./ClassDataBlocks/useClassroomJoinHook";
import ActiveCourses from "./ActiveCourses";
import CourseOverView from "./CourseOverView";
import VirtualClassRoom from "./VirtualClassRoom";
import PageLoader from "../../../../Components/Widgets/PageLoader";
import AssistantView from "./AssistantView";
import { Alert } from "react-bootstrap";
import { AdminClassContext } from "../../../../Context/Admin/AdminClassContext";
import { CourseType } from "../../../../Config/types";

interface Props {
    laravel: any;
    debug: boolean;
}

enum ClassView {
    List = "list",
    Class = "class",
    Assistant = "assistant",
    Idle = "idle",
}

const initialState = {
    classView: ClassView.List,
    instructorAssigned: false,
    courseDateId: null,
    assignAssistant: false,
};

const InstructorClassRoom = ({ laravel, debug = false }: Props) => {
    if (debug) console.log("InstructorClassRoom: ", laravel);

    /**
     *  Given authenticated instructor's ID
     */
    const [authUserId, setAuthUserId] = useState<number | null>(
        laravel.user.id
    );

    /**
     *  The Assigned Instructor
     */
    const [assignedInstructorId, setAssignedInstructorId] = useState<
        number | null
    >(null);

    /**
     * This will return the auth instructor
     * we can use laravel.user.id to find the auth user
     */
    const validatedInstructor = useContext(ValidatedInstructorContext);

    /**
     * The Assigned Assistant
     */
    const [assignedAssistantId, setAssignedAssistantId] = useState<
        number | null
    >(null);

    /**
     * Tracks the Course Date Id
     */
    const [courseDateId, setCourseDateId] = React.useState<number | null>(null);

    /**
     * Return the courses the authenticated instructor is assigned to
     * if none are found then the instructor is not assigned to any courses
     */
    const [activeCourse, setActiveCourse] = useState<CourseType | null>(null);

    useEffect(() => {
        if (!validatedInstructor) return;

        console.log("Validated Instructor: ", validatedInstructor);

        const getCurrentCourseDateId = (validatedInstructor) => {
            if (!validatedInstructor) return null;
            const courses = validatedInstructor.courses;
            if (courses.length < 1) return null;
            const course = courses[0];
            if (!course) return null;
            return course.id;
        };

        const course_date_id = getCurrentCourseDateId(validatedInstructor);
        setCourseDateId(course_date_id);
    }, [validatedInstructor]);

    /**
     * Classroom Data Hook
     */
    const {
        CourseMeetingData,
        status,
        error,

        state,
        dispatch,

        handleSetView,
        handleTakeOver,
        assistantTakeOver,
        handleAssignAssistant,
    } = classRoomJoinHook({
        laravel,
        setAssignedAssistantId,
        validatedInstructor,
        setAssignedInstructorId,
        setCourseDateId,
        courseDateId,
    });

    /**
     * Sets the course date id
     */
    useEffect(() => {
        if (!CourseMeetingData) {
            console.log("No CourseMeetingData for CourseDate");
            return;
        }

        const fetchData = async () => {
            try {
                const cDateId = CourseMeetingData?.courseDate?.id;
                setCourseDateId(cDateId);
            } catch (error) {
                console.error("Error fetching course date:", error);
            }
        };

        fetchData();
    }, [CourseMeetingData]);

    if (status === "loading")
        return <PageLoader base_url={window.location.origin} />;

    if (status === "error") {
        // Set a timeout to reload the page after 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000); // 30000 milliseconds = 30 seconds

        return (
            <Alert variant="danger">
                There was an error loading the course data. 
                The page will reload in 30 seconds.
            </Alert>
        );
    }

    const renderActiveCoursesView = (): JSX.Element => {
        return (
            <ActiveCourses
                laravel={laravel}
                handleSetView={handleSetView}
                validatedInstructor={validatedInstructor}
                handleTakeOver={handleTakeOver}
                handleAssignAssistant={handleAssignAssistant}
                assistantId={assignedAssistantId}
                setAssignedAssistantId={setAssignedAssistantId}
                debug={debug}
            />
        );
    };

    const renderInstructorView = (): JSX.Element => {
        const InstUnit = CourseMeetingData?.instUnit;

        if (InstUnit?.completed_at) {
            return (
                <CourseOverView
                    data={CourseMeetingData}
                    instructor={validatedInstructor?.instructor}
                    courseDateId={state.courseDateId}
                />
            );
        } else if (CourseMeetingData) {
            if (!courseDateId) {
                return (
                    <Alert variant="danger">
                        The courseDateId must be set to load this section
                    </Alert>
                );
            } else {
                return (
                    <VirtualClassRoom
                        laravel={laravel}
                        CourseMeetingData={CourseMeetingData}
                        courseDateId={courseDateId}
                        debug={debug}
                    />
                );
            }
        } else {
            return (
                <Alert variant="danger">
                    There was an error loading the course data
                </Alert>
            );
        }
    };

    const renderAssistantView = (): JSX.Element => {
        if (!assignedAssistantId) {
            return (
                <ActiveCourses
                    laravel={laravel}
                    handleSetView={handleSetView}
                    validatedInstructor={validatedInstructor}
                    handleTakeOver={handleTakeOver}
                    handleAssignAssistant={handleAssignAssistant}
                    assistantId={assignedAssistantId}
                    setAssignedAssistantId={setAssignedAssistantId}
                    debug={debug}
                />
            );
        } else {
            return (
                <AssistantView
                    laravel={laravel}
                    courseDateId={courseDateId}
                    CourseMeetingData={CourseMeetingData}
                    handleTakeOver={handleTakeOver}
                    handleAssignAssistant={handleAssignAssistant}
                    setAssignedAssistantId={setAssignedAssistantId}
                    debug={false}
                />
            );
        }
    };

    console.log("InstructorClassRoomBuilder: ", state.classView, CourseMeetingData);

    const renderViewBasedOnState = () => {
        switch (state.classView) {
            case ClassView.Class:
                return renderInstructorView();
            case ClassView.Assistant:
                return renderAssistantView();
            case ClassView.List:
            case ClassView.Idle:
            default:
                return renderActiveCoursesView();
        }
    };

    return (
        <AdminClassContext.Provider value={CourseMeetingData}>
            {renderViewBasedOnState()}
        </AdminClassContext.Provider>
    );
};

export default InstructorClassRoom;
