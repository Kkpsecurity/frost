import React, { useEffect, useState, FC } from "react";
import { createRoot } from 'react-dom/client';

import { Alert } from "react-bootstrap";
import apiClient from "../../../Config/axios";

import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ErrorBoundary } from "react-error-boundary";

// Internal Modules
import StudentPortalDataLayer from "./StudentPortalDataLayer";
import StudentPortalFallback from "../../../ErrorHandeling/StudentPortalFallback";

const queryClient = new QueryClient();

interface SiteProps {
    course_auth_id: number;
}

const StudentPortal: FC<SiteProps> = ({ course_auth_id }) => {
    const debug: boolean = process.env.APP_DEBUG === "true";

    const [currentCourseDateId, setCurrentCourseDateId] = useState<number | null>(null);
    
    const setBrowserAgent = async () => {
        try {
            const userAgent = window.navigator.userAgent;
            const response = await apiClient.post("classroom/portal/set_browser", { browser: userAgent });
            if (debug) console.log("Browser info sent successfully:", response.data);
        } catch (error) {
            console.error("Failed to send browser info:", error);
        }
    };

    const fetchCurrentCourseDateId = async () => {
        try {
            const response = await apiClient.get("classroom/portal/get_current_course_date_id/" + course_auth_id);
            const courseDateId = response.data;
            setCurrentCourseDateId(courseDateId === 0 ? null : courseDateId);
        } catch (error) {
            console.error("Error fetching courseDateId:", error);
        }
    };

    useEffect(() => {
        setBrowserAgent();
        fetchCurrentCourseDateId();
    }, []);

    if (course_auth_id === 0) {
        return <Alert variant="danger" className="missing-course-alert">Missing Course Auth ID</Alert>;
    }

    if(!currentCourseDateId) {
        return <Alert variant="danger" className="missing-course-alert">Missing Course Date ID</Alert>;
    }
    
   
    const AppWrapper = debug ? React.StrictMode : React.Fragment;

    return (
        <AppWrapper>
            <QueryClientProvider client={queryClient}>
                <ErrorBoundary FallbackComponent={StudentPortalFallback}>
                    <StudentPortalDataLayer
                        course_date_id={currentCourseDateId}
                        course_auth_id={course_auth_id}
                        debug={debug}
                    />
                </ErrorBoundary>

                {debug && <ReactQueryDevtools initialIsOpen={false} />}
            </QueryClientProvider>
        </AppWrapper>
    );
};

export default StudentPortal;

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}

function init() {
    const container = document.getElementById("StudentPortal");
    const propsContainer = document.getElementById("props");

    if (container && propsContainer) {
        const courseAuthId = parseInt(propsContainer.dataset.courseAuthId);

        // Create a root.
        const root = createRoot(container);

        // Initial render
        root.render(<StudentPortal course_auth_id={courseAuthId} />);
    }
}

