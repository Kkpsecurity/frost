import React, { useEffect } from "react";
import { createRoot } from "react-dom/client";
import { ErrorBoundary } from "react-error-boundary";

import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
const queryClient = new QueryClient();


import InstructorPortalDataLayer from "./InstructorPortalDataLayer";
import InstructorPortalFallback from "../../../ErrorHandeling/InstructorPortalFallback";

const InstructorPortal: React.FC = () => {
    const debug: boolean = true;

    const AppWrapper = process.env.NODE_ENV === "development" ? React.StrictMode : React.Fragment;

    return (
        <AppWrapper>
            <QueryClientProvider client={queryClient} contextSharing={true}>
                <ErrorBoundary FallbackComponent={InstructorPortalFallback}>
                    <InstructorPortalDataLayer debug={debug} />
                </ErrorBoundary>

                {debug === true ? (
                    <ReactQueryDevtools
                        initialIsOpen={true}
                        position="bottom-right"
                    />
                ) : null}
            </QueryClientProvider>
        </AppWrapper>
    );
};

export default InstructorPortal;

if (document.getElementById("InstructorPortal")) {
    const container = document.getElementById("InstructorPortal");
    const root = createRoot(container);
    root.render(<InstructorPortal />);
}
