import React from "react";
import InstructorTitlebar from "./Common/InstructorTitlebar";
import BulletinBoard from "../Classroom/offline/BulletinBoard";
import { useClassRouter } from "../Hooks/useClassRouter";
import { InstructorPollResponse, ClassroomPollResponse, ChatPollResponse } from "../types";

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
    chatData
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

    return (
        <div className="m-0 p-0" style={{ margin: 0, padding: 0 }}>
            {/* Titlebar - Always visible */}
            <InstructorTitlebar instructor={instructor} title="Welcome: Florida Online Bulletin Board" />
            
    
            {/* Content - Route based on state */}
            {(state === 'offline' || state === 'pending') && (
                <div>
                    {state === 'pending' && (
                        <div className="alert alert-info m-3" role="alert">
                            <i className="fas fa-chalkboard-teacher mr-2"></i>
                            <strong>Ready to Teach</strong> - Select a course to begin your session
                        </div>
                    )}

                    <BulletinBoard classroomData={classroomData} instructorData={instructorData} />
                </div>
            )}

            {state === 'online' && (
                <div className="p-5">
                    <h2>🎓 Online Classroom (Coming Soon)</h2>
                    <p>Live classroom interface will display here</p>
                </div>
            )}
        </div>
    );
};

export default InstructorDashboard;


