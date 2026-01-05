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

interface InstructorDashboardProps {
    instructorData?: InstructorPollResponse;
    classroomData?: ClassroomPollResponse | null;
    chatData?: ChatPollResponse | null;
}

/**
 * InstructorDashboard - Main Dashboard Component
 *
 * Routes content based on classroom state:
 * - OFFLINE: No courseDate (default BulletinBoard)
 * - PENDING: courseDate exists but instructor hasn't started (BulletinBoard with "awaiting start" indicator)
 * - ONLINE: instUnit exists (Live classroom interface)
 */
const InstructorDashboard: React.FC<InstructorDashboardProps> = ({
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

    console.log("🎓 InstructorDashboard: Router state", {
        state,
        instructor,
        isClassroomActive,
        isClassroomPending,
        courseDatesCount: classroomData?.courseDates?.length,
        hasInstUnit: !!instructorData?.instUnit,
    });

    const activeCourseName =
        classroomData?.courseDates?.[0]?.course_name ||
        classroomData?.courseDates?.[0]?.course?.title ||
        instructorData?.instUnit?.course_unit_name ||
        "Live Class";

    const title =
        state === "online"
            ? activeCourseName
            : "Welcome: Florida Online Bulletin Board";

    return (
        <div className="m-0 p-0" style={{ margin: 0, padding: 0 }}>
            {/* Titlebar - Sits above offline and online content */}
            <InstructorTitlebar instructor={instructor} title={title} />

            {/* Content - Route based on state */}
            {(state === "offline" || state === "pending") && (
                <div>
                    {state === "pending" && (
                        <div className="alert alert-info m-3" role="alert">
                            <i className="fas fa-chalkboard-teacher mr-2"></i>
                            <strong>Ready to Teach</strong> - Select a course to
                            begin your session
                        </div>
                    )}

                    <BulletinBoard
                        classroomData={classroomData}
                        instructorData={instructorData}
                    />
                </div>
            )}

            {state === "online" && (
                <ClassroomInterface
                    instructorData={instructorData}
                    classroomData={classroomData}
                    chatData={chatData}
                />
            )}
        </div>
    );
};

export default InstructorDashboard;


