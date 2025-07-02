import React from "react";
import { Alert } from "react-bootstrap";
import PageLoader from "../Components/Widgets/PageLoader";

interface Props {
    data: {
        success: boolean;
        message: string;
    };
    status: string;
    error: Error;
    debug: boolean;
}

const ReactQueryReturnHandeler = ({ data, status, error, debug }) => {
    if (debug === true) console.log("QueryData: ", data);

    const base_url: string = location.protocol + "//" + location.host;

    if (status === "loading") return <PageLoader base_url={base_url} />;
    if (status === "error") {
        return (
            <Alert variant="danger">
                Something went wrong! here's what we found:
                {error instanceof Error ? error.message : ""}
            </Alert>
        );
    }

    if (data.success === false) {
        return (
            <Alert variant="danger">
                Something went wrong! here's what we found: {data.message}
            </Alert>
        );
    }
};

export default ReactQueryReturnHandeler;
