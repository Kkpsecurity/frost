import React from 'react'
import { useLaravelAdminHook } from "../Hooks/useAdminLaravelHook";

/**
 * Retrieve the Laravel Config and setting
 * Get InstructorSession
 * Get ClassRoomSession
 */

interface InstructorDataLayerProps {
    instructorId?: any;
    debug?: boolean;
}

const InstructorDataLayer: React.FC<InstructorDataLayerProps> = ({
    instructorId,
    debug = false,
}) => {
    if (debug) {
        console.log("🔧 InstructorDataLayer props:", { instructorId, debug });
    }

    const { data: laravelData, isLoading, error } = useLaravelAdminHook();

    return (
        <div>
            <h2>Instructor Data Layer</h2>
            {isLoading && <p>Loading Laravel config...</p>}
            {error && <p>Error: {error.message}</p>}
            {laravelData && <pre>{JSON.stringify(laravelData, null, 2)}</pre>}
        </div>
    );
};

export default InstructorDataLayer;
