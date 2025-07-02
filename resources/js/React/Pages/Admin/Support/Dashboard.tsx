import React from "react";
import { createRoot } from "react-dom/client";
import { ErrorBoundary } from "react-error-boundary";

import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
const queryClient = new QueryClient();

import SupportDataLayer from "./SupportDataLayer";
import SupportDashboardFallback from "../../../ErrorHandeling/SupportDashboardFallback";

const Dashboard: React.FC = () => {
    const debug: boolean = true;
    
    return (
        <React.StrictMode>
            <QueryClientProvider client={queryClient}>
                <ErrorBoundary FallbackComponent={SupportDashboardFallback}>
                    <SupportDataLayer debug={debug} />
                </ErrorBoundary>

                {debug === true ? (
                    <ReactQueryDevtools
                        initialIsOpen={true}
                        position="bottom-right"
                    />
                ) : null}
            </QueryClientProvider>
        </React.StrictMode>
    );
};

export default Dashboard;

if (document.getElementById("FrostSupportCenter")) {
    const container = document.getElementById("FrostSupportCenter");
    const root = createRoot(container);
    root.render(<Dashboard />);
}
