import React from "react";
import { Alert } from "react-bootstrap";
import InstructorDashboard from "./InstructorDashboard";
import PageLoader from "../../Shared/Components/Widgets/PageLoader";
import {
    InstructorContextProvider,
    InstructorContextType,
} from "../Context/InstructorContext";
import {
    ClassroomActivityContextProvider,
    ClassroomActivityContextType,
} from "../Context/ClassroomActivityContext";
import {
    ChatSystemContextProvider,
    ChatSystemContextType,
} from "../Context/ChatSystemContext";
import {
    useInstructorDataPolling,
    useClassroomDataPolling,
    useChatMessagesPolling,
} from "../Hooks/useInstructorPolling";

/**
 * InstructorDataLayer - Uses polling hooks to fetch data
 *
 * Responsibilities:
 * - Call polling hooks
 * - Provide data via contexts
 * - Handle loading/error states
 */
const InstructorDataLayer: React.FC = () => {
    // Call the 3 polling hooks
    const {
        data: instructorData,
        isLoading: instructorLoading,
        error: instructorError,
    } = useInstructorDataPolling();
    const isClassroomActive = !!instructorData?.instUnit;

    // ALWAYS poll for classroom data (needed for BulletinBoard even when offline)
    const {
        data: classroomData,
        isLoading: classroomLoading,
        error: classroomError,
    } = useClassroomDataPolling(true);

    const courseDateId = classroomData?.courseDate?.id ?? null;

    // Only poll chat when classroom is active
    const {
        data: chatData,
        isLoading: chatLoading,
        error: chatError,
    } = useChatMessagesPolling(courseDateId, isClassroomActive);

    const isLoading = instructorLoading || classroomLoading;
    const error = instructorError || classroomError || chatError;

    // Show loading spinner
    if (isLoading && !instructorData) {
        return <PageLoader />;
    }

    // Show error if polling failed
    if (error) {
        return (
            <Alert variant="danger" className="m-4">
                <Alert.Heading>⚠️ Data Loading Error</Alert.Heading>
                <p>
                    {error instanceof Error
                        ? error.message
                        : "Unable to load instructor data"}
                </p>
                <p className="mb-0">
                    Please refresh the page or contact support.
                </p>
            </Alert>
        );
    }

    console.log("🎓 InstructorDataLayer: Component mounting...");
    console.log("🎓 InstructorDataLayer: Polling hook result:", {
        loading: isLoading,
        hasData: !!instructorData,
        error: error,
    });

    // Determine if there's an active classroom session
    const hasActiveClassroom = !!instructorData?.instUnit;

    console.log("🎓 InstructorDataLayer: Rendering with data:", {
        instructor: instructorData?.instructor,
        hasClassroom: hasActiveClassroom,
        classroomData,
        chatData,
    });

    // Always pass both instructor and classroom data separately
    // Don't merge them - they are independent data streams
    return (
        <InstructorDashboard
            instructorData={instructorData}
            classroomData={classroomData}
            chatData={hasActiveClassroom ? chatData : undefined}
        />
    );
};

export default InstructorDataLayer;
