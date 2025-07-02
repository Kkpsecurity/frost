import React, { useEffect, useState } from "react";
import { Alert } from "react-bootstrap";

import { useLaravelData } from "../../../../Hooks/Web/useLaravelDataHook";
import { useClassRoomData } from "../../../../Hooks/Web/useClassRoomDataHooks";

import ZoomScreenSharePlayer from "./ZoomScreenSharePlayer";
import PageLoader from "../../../../Components/Widgets/PageLoader";
import apiClient from "../../../../Config/axios";
import { ErrorBoundary } from "react-error-boundary";
import ZoomComponentFallback from "../../../../ErrorHandeling/ZoomComponentFallback";

interface ScreenShareDataLayerProps {
    course_auth_id: number;
    debug: boolean;
}

const fetchCourseDateId = async (
    course_auth_id: number
): Promise<number | Error> => {
    try {
        const response = await apiClient.get(
            "classroom/portal/get_current_course_date_id/" + course_auth_id
        );
        return response.data;
    } catch (error) {
        return error;
    }
};

const ScreenShareDataLayer: React.FC<ScreenShareDataLayerProps> = ({
    course_auth_id,
    debug,
}) => {
    const [courseDateId, setCourseDateId] = useState<number | null>(null);

    useEffect(() => {
        (async () => {
            const id = await fetchCourseDateId(course_auth_id);

            if (id instanceof Error) {
                console.error(id);
                return;
            }

            setCourseDateId(id);
        })();
    }, [course_auth_id]);

    const laravelParams = {
        course_auth_id: String(course_auth_id),
    };

    const {
        data: laraData,
        isLoading: laraLoading,
        isError: laraError,
    } = useLaravelData(String(laravelParams.course_auth_id));

    const {
        data: classData,
        isLoading: classLoading,
        isError: classError,
    } = useClassRoomData(laravelParams.course_auth_id, true);

    if (courseDateId === null) {
        return (
            <div className="loading-container">
                <PageLoader base_url={`${window.location.origin}`} />
            </div>
        );
    }

    if (laraLoading || classLoading) {
        return (
            <div className="loading-container">
                <PageLoader base_url={`${window.location.origin}`} />
            </div>
        );
    }

    if (laraError || classError) {
        return (
            <Alert variant="danger">
                {laraError ? `LaraData Fetch Failed: ${laraData.error}` : ""}
                {classError ? `Class Data Fetch Failed: ${classError}` : ""}
            </Alert>
        );
    }

    return (
        <ErrorBoundary FallbackComponent={ZoomComponentFallback}>
        <ZoomScreenSharePlayer
            course_auth_id={course_auth_id}           
            debug={debug}
        />
        </ErrorBoundary>
    );
};

export default ScreenShareDataLayer;
