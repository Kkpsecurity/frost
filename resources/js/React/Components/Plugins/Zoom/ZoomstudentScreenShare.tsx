import React, { useState, useEffect } from "react";
import { Alert } from "react-bootstrap";
import apiClient from "../../../Config/axios";

import { LaravelDataShape, ClassDataShape } from "../../../Config/types";
import PageLoader from "../../Widgets/PageLoader";

interface Props {
    laravel: LaravelDataShape;
    data: ClassDataShape;
    debug: boolean;
}

const ZoomstudentScreenShare: React.FC<Props> = ({
    laravel,
    data,
    debug = false,
}) => {
    const [zoomStatus, setZoomStatus] = useState<string>("");
    const IFrameUrl =
        laravel.site.base_url +
        "/classroom/portal/zoom/screen_share/" +
        laravel.user.course_auth_id +
        "/" +
        data.courseDate.id;

    console.log("ZoomstudentScreenShare", laravel, data, debug);

    useEffect(() => {
        setZoomStatus(data?.instructor?.zoom_payload?.zoom_status || "");
    }, [data.instructor]);

    if (zoomStatus === "disabled") {
        return (
            <Alert variant="info">
                Screen Sharing Not Ready! Please be patient Starting Soon!.
            </Alert>
        );
    }

    return (
        <div style={{
            width: "100%",
            height: "520px",
            maxHeight: "520px",
            position: "relative",
            background: "black",
        }}> 
            <iframe
                src={IFrameUrl}
                style={{
                    position: "absolute",
                    top: "0",
                    left: "0",
                    width: "100%",
                    height: "520px",
                }}
                allow="fullscreen *"
            ></iframe>
        </div>
    );
};

export default ZoomstudentScreenShare;
