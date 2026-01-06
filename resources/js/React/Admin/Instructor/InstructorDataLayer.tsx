import React, { useEffect, useState } from "react";
import {
    useInstructorDataPolling,
    useClassroomDataPolling,
    useChatMessagesPolling,
} from "./Hooks/useInstructorPolling";
import InstructorDashboard from "./components/InstructorDashboard";
import PageLoader from "../../Shared/Components/Widgets/PageLoader";

const InstructorDataLayer: React.FC = () => {
    console.log("🎓 InstructorDataLayer: Component mounting...");
    const [hasActiveClassroom, setHasActiveClassroom] = useState(false);

    // Poll instructor data every 30 seconds
    const {
        data: instructorData,
        isLoading: loadingInstructor,
        error: instructorError,
    } = useInstructorDataPolling();

    console.log("🎓 InstructorDataLayer: Polling hook result:", {
        loading: loadingInstructor,
        hasData: !!instructorData,
        error: instructorError,
    });

    // Poll classroom data every 15 seconds (only if classroom is active)
    const {
        data: classroomData,
        isLoading: loadingClassroom,
        error: classroomError,
    } = useClassroomDataPolling(true);

    // Poll chat messages every 3 seconds (only if classroom is active)
    const {
        data: chatData,
        isLoading: loadingChat,
        error: chatError,
    } = useChatMessagesPolling(hasActiveClassroom);

    // Check if classroom is active based on instructor data
    useEffect(() => {
        if (instructorData?.instUnit) {
            console.log(
                "✅ Active classroom detected:",
                instructorData.instUnit
            );
            setHasActiveClassroom(true);
        } else {
            console.log("⚠️ No active classroom");
            setHasActiveClassroom(false);
        }
    }, [instructorData]);

    const loading = loadingInstructor;

    if (loading) {
        return <PageLoader />;
    }

    if (instructorError) {
        return (
            <div className="alert alert-danger m-4">
                <strong>Failed to load instructor data</strong>
                <div className="mt-2">
                    <pre style={{ whiteSpace: "pre-wrap" }}>
                        {String(instructorError)}
                    </pre>
                </div>
            </div>
        );
    }

    // Render instructor dashboard with polling data
    console.log("🎓 InstructorDataLayer: Rendering with data:", {
        instructor: instructorData?.instructor,
        hasClassroom: hasActiveClassroom,
        instUnit: instructorData?.instUnit,
        instUnitId: instructorData?.instUnit?.id,
        instUnitIsNull: instructorData?.instUnit === null,
        classroomData,
        chatData,
    });

    return (
        <InstructorDashboard
            instructorData={instructorData}
            classroomData={classroomData}
            chatData={hasActiveClassroom ? chatData : null}
        />
    );
};

export default InstructorDataLayer;
