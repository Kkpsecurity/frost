import React from "react";
import { createRoot } from "react-dom/client";

import { ErrorBoundary } from "react-error-boundary";
import ZoomComponentFallback from "../../../../ErrorHandeling/ZoomComponentFallback";
import { QueryClientProvider, QueryClient } from "@tanstack/react-query";
import ScreenShareDataLayer from "./ScreenShareDataLayer";

const queryClient = new QueryClient();

interface ScreenSharePlayerProps {
    course_auth_id: string;
}

let debug: boolean = false;

declare global {
    interface Window {
        APP_ENV: string;
    }
}

const ScreenSharePlayer = ({ course_auth_id }: ScreenSharePlayerProps) => {
    const [courseAuthId, setCourseAuthId] = React.useState<number>(
        parseInt(course_auth_id, 10)
    );

    const AppWrapper =
        window.APP_ENV === "staging" ? React.StrictMode : React.Fragment;

    return (
        <AppWrapper>
            <QueryClientProvider client={queryClient} contextSharing={true}>
                <ErrorBoundary FallbackComponent={ZoomComponentFallback}>
                    <ScreenShareDataLayer
                        course_auth_id={courseAuthId}
                        debug={debug}
                    />
                </ErrorBoundary>
            </QueryClientProvider>
        </AppWrapper>
    );
};

export default ScreenSharePlayer;

// Using createRoot instead of ReactDOM.render
const container = document.getElementById("StudentZoomPlayer");

if (container) {
    const root = createRoot(container);
    const props = Object.assign({}, container.dataset);
    root.render(
        <ScreenSharePlayer course_auth_id={props.course_auth_id as string} />
    );
}
