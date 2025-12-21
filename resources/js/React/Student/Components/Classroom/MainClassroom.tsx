import React from "react";
import { useClassroom } from "../../context/ClassroomContext";
import MainOffline from "./MainOffline";
import MainOnline from "./MainOnline";

interface MainClassroomProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
}

/**
 * MainClassroom - Orchestrator for classroom experience
 * 
 * Determines whether student is in:
 * - MainOffline: Self-study mode (no live class scheduled/available)
 * - MainOnline: Live classroom with instructor
 * 
 * Decision based on classroom poll data:
 * - CourseDate exists + InstUnit exists = Online (live class)
 * - No CourseDate OR No InstUnit = Offline (self-study)
 */
const MainClassroom: React.FC<MainClassroomProps> = ({ courseAuthId, student, onBackToDashboard }) => {
    const classroomContext = useClassroom();

    // Loading classroom data
    if (!classroomContext) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: "400px" }}>
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading classroom...</span>
                    </div>
                    <p className="mt-3">Loading classroom...</p>
                </div>
            </div>
        );
    }

    const { courseDate, instUnit } = classroomContext;

    // Determine online/offline status
    const isOnline = courseDate && instUnit;

    // Show appropriate classroom view
    if (isOnline) {
        return <MainOnline classroom={classroomContext} student={student} onBackToDashboard={onBackToDashboard} />;
    } else {
        return <MainOffline courseAuthId={courseAuthId} student={student} onBackToDashboard={onBackToDashboard} />;
    }
};

export default MainClassroom;
