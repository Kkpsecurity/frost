import React from "react";
import InstructorTitlebar from "./Common/InstructorTitlebar";
import BulletinBoard from "../Classroom/offline/BulletinBoard";
import ClassroomInterface from "../Interfaces/ClassroomInterface";
import { useClassRouter } from "../Hooks/useClassRouter";
import {
    InstructorPollResponse,
    ClassroomPollResponse,
    ChatPollResponse,
} from "../types";

interface AssistantDashboardProps {
    instructorData?: InstructorPollResponse;
    classroomData?: ClassroomPollResponse | null;
    chatData?: ChatPollResponse | null;
}

/**
 * AssistantDashboard - Assistant View of Classroom
 *
 * Similar to InstructorDashboard but with read-only controls
 * Routes content based on classroom state:
 * - OFFLINE: No active classroom (BulletinBoard)
 * - ONLINE: instUnit exists (Live classroom interface - READ ONLY)
 */
const AssistantDashboard: React.FC<AssistantDashboardProps> = ({
    instructorData,
    classroomData,
    chatData,
}) => {
    // Use hook to determine classroom state
    const { state, isClassroomActive, isClassroomPending } = useClassRouter(
        classroomData?.courseDates || [],
        instructorData?.instUnit
    );

    const { instructor } = instructorData || {};

    console.log("🎨 AssistantDashboard: Router state", {
        state,
        assistant: instructor,
        isClassroomActive,
        isClassroomPending,
        courseDatesCount: classroomData?.courseDates?.length,
        hasInstUnit: !!instructorData?.instUnit,
    });

    const activeCourseName =
        classroomData?.courseDates?.[0]?.course_name ||
        classroomData?.courseDates?.[0]?.course?.title ||
        "Unknown Course";

    // ONLINE STATE: Active classroom with assistant permissions
    if (isClassroomActive) {
        console.log("🎨 AssistantDashboard: Rendering ONLINE classroom (read-only mode)");
        return (
            <>
                <InstructorTitlebar
                    courseName={activeCourseName}
                    instructor={instructor}
                    hasActiveClass={true}
                />
                <ClassroomInterface
                    instructorData={instructorData}
                    classroomData={classroomData}
                    chatData={chatData}
                    isAssistantMode={true} // Pass flag to disable instructor controls
                />
            </>
        );
    }

    // OFFLINE STATE: No active classroom - show bulletin board
    console.log("🎨 AssistantDashboard: Rendering OFFLINE bulletin board");
    return (
        <>
            <InstructorTitlebar
                courseName="Assistant Dashboard"
                instructor={instructor}
                hasActiveClass={false}
            />
            <BulletinBoard
                classroomData={classroomData}
                instructorData={instructorData}
                isLoading={false}
            />
        </>
    );
};

export default AssistantDashboard;
