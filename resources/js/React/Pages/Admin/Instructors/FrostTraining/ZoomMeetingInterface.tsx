import React, { useEffect, useState } from "react";
import {
    CourseMeetingShape,
    LaravelAdminShape,
} from "../../../../Config/types";
import { Alert, ListGroup, OverlayTrigger, Tooltip } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
    faLock,
    faArrowDown,
    faArrowUp,
} from "@fortawesome/free-solid-svg-icons";
import { updateMeetingData } from "../../../../Hooks/Admin/useInstructorHooks";

interface ZoomMeetingInterfaceProps {
    laravel: LaravelAdminShape;
    data: CourseMeetingShape;
    courseDateId: number;
}

const ZoomMeetingInterface: React.FC<ZoomMeetingInterfaceProps> = ({
    laravel,
    data,
    courseDateId,
}) => {
    const [activateScreenShare, setActivateScreenShare] =
        React.useState<boolean>(false);

    const [showListGroup, setShowListGroup] = useState<boolean>(false); // Add this state variable
    const [autoCloseTimer, setAutoCloseTimer] = useState(null);
    const [arrowIcon, setArrowIcon] = useState(faArrowDown);

    const toggleListGroup = () => {
        setShowListGroup(!showListGroup);
        setArrowIcon(showListGroup ? faArrowDown : faArrowUp);

        // Clear any existing timer
        if (autoCloseTimer) {
            clearTimeout(autoCloseTimer);
        }

        // Start a new timer to auto-close after 30 seconds
        const newAutoCloseTimer = setTimeout(() => {
            setShowListGroup(false);
            setArrowIcon(faArrowDown);
        }, 30000); // 30 seconds in milliseconds

        setAutoCloseTimer(newAutoCloseTimer);
    };

    const initialZoomStatus = localStorage.getItem("zoomStatus") || "disabled";

    const [meetingCredentials, setMeetingCredentials] = useState({
        zoomPassCode: "",
        zoomPassWord: "",
        zoomMeetingId: "",
        zoomStatus: initialZoomStatus,
        zoomEmail: "",
    });

    const { mutateAsync, isError, isSuccess } = updateMeetingData();

    const setCredentials = async (
        e: React.MouseEvent<HTMLButtonElement, MouseEvent>
    ) => {
        e.preventDefault();
        const payload = {
            course_date_id: courseDateId,
            zoomStatus: "enabled",
        };

        try {
            await mutateAsync(payload);

            if (isSuccess) {
                // Handle success
                setMeetingCredentials((prevCredentials) => ({
                    ...prevCredentials,
                    zoomStatus: "enabled",
                }));
                setActivateScreenShare(true);
                localStorage.setItem("zoomStatus", "enabled");
            }

            if (isError) {
                // Handle error
                console.error("An error occurred:", isError);
            }
        } catch (error) {
            // Handle other errors that might occur during the mutation
            console.error("An unexpected error occurred:", error);
        }
    };

    useEffect(() => {
        if (data && data.instructor && data.instructor.zoom_payload) {
            const {
                zoom_passcode,
                zoom_password,
                pmi,
                zoom_status,
                zoom_email,
            } = data.instructor.zoom_payload;

            setMeetingCredentials((prevCredentials) => ({
                ...prevCredentials,
                zoomPassCode: zoom_passcode,
                zoomPassWord: zoom_password,
                zoomMeetingId: pmi,
                zoomStatus: zoom_status,
                zoomEmail: zoom_email,
            }));
        }
    }, [data.instructor.zoom_payload]);

    useEffect(() => {
        // Check if meetingCredentials is defined and contains a valid zoomStatus
        if (meetingCredentials && meetingCredentials.zoomStatus === "enabled") {
            setActivateScreenShare(true);
        } else {
            // If not, check the local storage for the zoomStatus
            const zoomStatus = localStorage.getItem("zoomStatus");
            if (zoomStatus === "enabled") {
                setActivateScreenShare(true);
            } else {
                setActivateScreenShare(false);
            }
        }
    }, [meetingCredentials]);

    if (!meetingCredentials.zoomMeetingId || !meetingCredentials.zoomPassCode) {
        return (
            <Alert variant="danger">
                <i className="fa fa-exclimation-mark" />
                Zoom Credentails are Missing
            </Alert>
        );
    }

    const renderTooltip = (props) => (
        <Tooltip id="button-tooltip" {...props}>
            Activating the screen sharing feature will enable your screen view
            in the student's classroom. For optimal performance, it's
            recommended that you configure your Zoom client settings before
            engaging the screen sharing feature.
        </Tooltip>
    );

    if (meetingCredentials.zoomStatus === "disabled") {
        // Update the condition here
        return (
            <Alert variant="info" className="d-flex justify-content-between">
                <OverlayTrigger
                    placement="bottom"
                    delay={{ show: 250, hide: 400 }}
                    overlay={renderTooltip}
                >
                    <span>
                        <div
                            className="title"
                            style={{
                                fontSize: "1.2rem",
                            }}
                        >
                            Zoom Screen Sharing{" "}
                            <sup>
                                <i className="fas fa-question-circle"></i>
                            </sup>
                        </div>
                    </span>
                </OverlayTrigger>

                {data.instUnit.assistant_id !== laravel.user.id && (
                    <button
                        className="btn btn-primary"
                        onClick={(e) => setCredentials(e)}
                    >
                        <FontAwesomeIcon icon={faLock} /> Initiate Screen
                        Sharing
                    </button>
                )}
            </Alert>
        );
    }

    function getDay() {
        const getDay = () => {
            const today = new Date();
            return today.getDay(); // This returns 0 for Sunday, 1 for Monday, and so on.
        };

        return <span>Day {getDay() === 0 ? 7 : getDay()}</span>;
    }

    const renderLinkId = () => {
        if (laravel.user.role_id === 1) {
            return (
                <a
                    className="btn btn-sm btn-success"
                    href={`${laravel.site.base_url}/reset_course_date/${data.courseDate.id}`}
                >
                    {data.courseDate.id}
                </a>
            );
        }
        return null;
    };

    return (
        <div className="container">
            <div className="row bg-dark">
                <div className="col-11">
                    <div className="title bg-dark">
                        <h5>
                            {data.course.title_long}: {renderLinkId()}
                        </h5>
                        <p className="lead">
                            {data.course.title} - {getDay()}
                        </p>
                    </div>
                </div>
                <div className="col-1 bg-dark">
                    <button
                        className="btn btn-primary"
                        onClick={toggleListGroup}
                    >
                        <FontAwesomeIcon icon={arrowIcon} />
                    </button>
                </div>
            </div>
            {showListGroup && (
                 <div className="row" style={{
                    opacity: 1, // Set the initial opacity to 1 (fully visible)
                    transition: 'opacity 0.5s ease-in-out'
                }}>
                    <div className="col">
                        <ListGroup className="list-group-container">
                            <ListGroup.Item className="d-flex justify-content-between bg-gray">
                                <h5>Meeting ID:</h5>
                                <span>{meetingCredentials.zoomMeetingId}</span>
                            </ListGroup.Item>
                            <ListGroup.Item className="d-flex justify-content-between bg-gray">
                                <h5>Zoom Account Email:</h5>
                                <span>{meetingCredentials.zoomEmail}</span>
                            </ListGroup.Item>
                            <ListGroup.Item
                                className={`d-flex justify-content-between bg-gray`}
                            >
                                <h5>Passcode:</h5>
                                <span>{meetingCredentials.zoomPassCode}</span>
                            </ListGroup.Item>
                            <ListGroup.Item
                                className={`d-flex justify-content-between bg-gray`}
                            >
                                <h5>Password: (The Host Password)</h5>
                                <span>{meetingCredentials.zoomPassWord}</span>
                            </ListGroup.Item>
                            <ListGroup.Item
                                className={`d-flex justify-content-between bg-gray`}
                            >
                                <h5>Status:</h5>
                                <span>
                                    {meetingCredentials.zoomStatus.toUpperCase()}
                                </span>
                            </ListGroup.Item>
                        </ListGroup>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ZoomMeetingInterface;
