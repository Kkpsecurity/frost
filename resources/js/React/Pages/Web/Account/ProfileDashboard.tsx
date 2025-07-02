import React from "react";
import { createRoot } from "react-dom/client";
import { StudentType } from "../../../Config/types";

// Create a the QueryClient
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
const queryClient = new QueryClient();

import { ErrorBoundary } from "react-error-boundary";
import AccountDashboardFallback from "../../../ErrorHandeling/AccountDashboardFallback";
import AccountDashboardDataLayer from "./AccountDashboardDataLayer";

let debug: boolean = true;

const ProfileDashboard: React.FC = () => {
    if (debug) console.log("PROFILE: ");

    return (
        <React.StrictMode>
            <QueryClientProvider client={queryClient}>
                <ErrorBoundary FallbackComponent={AccountDashboardFallback}>
                    <AccountDashboardDataLayer debug={debug} />
                </ErrorBoundary>

                {debug ? (
                    <ReactQueryDevtools
                        initialIsOpen={false}
                        position="bottom-right"
                    />
                ) : null}
            </QueryClientProvider>
        </React.StrictMode>
    );
};

export default ProfileDashboard;

const container = document.getElementById("ProfileDashboard");
if (container) {
    const root = createRoot(container);
    root.render(<ProfileDashboard />);
}
