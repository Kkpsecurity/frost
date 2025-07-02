import React, { useEffect, useReducer, useState } from "react";
import { Alert } from "react-bootstrap";
import { ClassDataShape, LaravelDataShape } from "../../../Config/types";

import { StudentStyledDashboard } from "../../../Styles/StudentStyledDashboard.styled";

import { SpotlightTourProvider } from "../../../Context/SpotLightContext";
import StudentChallenge from "../../../Components/Plugins/Students/StudentChallenge";

/**
 * Tour
 */
import {
    LiveClassHelpData,
    OfflineClassHelpData,
} from "./Dashboard/FrostTour/data/data";

import SpotlightTourManager, {
    beginHelp,
} from "./Dashboard/FrostTour/FrostTourManager";

import { useLaravelData } from "../../../Hooks/Web/useLaravelDataHook";
import PageLoader from "../../../Components/Widgets/PageLoader";
import { useClassRoomData } from "../../../Hooks/Web/useClassRoomDataHooks";
import { ClassContext } from "../../../Context/ClassContext";
import StudentTitlebar from "./Partials/StudentTitlebar";
import StudentDashboard from "./Dashboard/StudentDashboard";
import { Button, Modal } from "react-bootstrap";

interface DataLayerProps {
    course_date_id: number;
    course_auth_id: number;
    debug: boolean;
}
const initialState: StateType = {
    isLoading: true,
    error: "",
    darkMode: false,
    navigationAwayCount: 0, // Initialize the count
};

interface StateType {
    isLoading: boolean;
    error: string;
    darkMode: boolean;
    navigationAwayCount: number; // Add this line
}

type ActionTypes =
    | { type: "setError"; payload: string }
    | { type: "toggleDarkMode" }
    | { type: "incrementNavigationAwayCount" }; // Add this line

const reducer = (state: StateType, action: ActionTypes): StateType => {
    switch (action.type) {
        case "setError":
            return { ...state, error: action.payload, isLoading: false };
        case "toggleDarkMode":
            const newDarkModeState = !state.darkMode;
            localStorage.setItem("darkMode", JSON.stringify(newDarkModeState));
            return { ...state, darkMode: newDarkModeState };
        case "incrementNavigationAwayCount": // Handle the new action
            return {
                ...state,
                navigationAwayCount: state.navigationAwayCount + 1,
            };
        default:
            console.warn("Unknown action: ", action);
            return state;
    }
};

const StudentPortalDataLayer: React.FC<DataLayerProps> = ({
    course_date_id,
    course_auth_id,
    debug = false,
}) => {
    if (debug) {
        console.log(
            "StudentPortalDataLayer Initialized",
            course_date_id,
            course_auth_id
        );
    }

    const [state, dispatch] = useReducer(reducer, initialState);
    const [isOnline, setIsOnline] = useState(false);
    const [helpData, setHelpData] = useState([]);

    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    const {
        data: laravelData,
        isLoading: isLaravelDataLoading,
        isError: isLaravelDataError,
        error: laravelDataError,
    } = useLaravelData(String(course_auth_id)) as {
        data: LaravelDataShape;
        isLoading: boolean;
        isError: boolean;
        error: string;
    };

    const {
        data: ClassRoomData,
        isLoading,
        error,
    } = useClassRoomData(String(course_auth_id), isOnline) as unknown as {
        data: ClassDataShape;
        isLoading: boolean;
        error: Error;
    };

    const AlertStudentModal = ({
        show,
        handleClose,
    }: {
        show: boolean;
        handleClose: () => void;
    }) => {
        return (
            <Modal
                show={show}
                onHide={handleClose}
                aria-labelledby="contained-modal-title-center"
                centered={true}
                style={{ zIndex: 9999, left: "20%" }}
            >
                <Modal.Header closeButton className="bg-danger text-white">
                    <Modal.Title>Attention Required!</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <h4>Navigating Away Count: {state.navigationAwayCount}</h4>
                    Your recent navigation away from the class page has been
                    detected. Such actions may hinder your ability to access the
                    instructional content, potentially impacting your learning
                    progress and outcomes. We strongly advise maintaining
                    presence on the class interface throughout the duration of
                    the lesson to guarantee your success and facilitate the
                    achievement of your educational objectives.
                </Modal.Body>

                <Modal.Footer>
                    <Button variant="warning" onClick={handleClose}>
                        Return to Class
                    </Button>
                </Modal.Footer>
            </Modal>
        );
    };

    // if a student leave the page crate a modal to indicate they are no longer active in the classroom
    useEffect(() => {
        const handleVisibilityChange = () => {
            const isLessonLive = ClassRoomData && ClassRoomData.studentLesson;

            if (document.hidden) {
                setIsOnline(false);

                // Start a timer if the lesson is live
                if (isLessonLive) {
                    // Wait 5 seconds before showing the message and incrementing the counter
                    setTimeout(() => {
                        setShow(true);
                        dispatch({ type: "incrementNavigationAwayCount" }); // Increment the count
                    }, 5000); // Delay in milliseconds
                }
            } else {
                setIsOnline(true);
            }
        };

        document.addEventListener("visibilitychange", handleVisibilityChange);

        return () => {
            document.removeEventListener(
                "visibilitychange",
                handleVisibilityChange
            );
        };
    }, [ClassRoomData, dispatch]); // Include dispatch in the dependency array

    useEffect(() => {
        const savedDarkMode = JSON.parse(
            localStorage.getItem("darkMode") || "false"
        );
        if (savedDarkMode !== state.darkMode) {
            dispatch({ type: "toggleDarkMode" });
        }
    }, []);

    useEffect(() => {
        if (ClassRoomData && ClassRoomData.course_date_id) {
            setHelpData(LiveClassHelpData);
        } else {
            setHelpData(OfflineClassHelpData);
        }
    }, [ClassRoomData]);

    if (isLaravelDataLoading || isLoading) {
        return <PageLoader base_url={window.location.origin} />;
    }

    if (isLaravelDataError || error) {
        return (
            <div>
                Error: {isLaravelDataError ? laravelDataError : error.message}
            </div>
        );
    }

    if(course_auth_id !== laravelData.user.course_auth_id) {
        return <Alert variant="danger" className="missing-course-alert">Invalid Course auth Id</Alert>;
    }
   
    return (
        <SpotlightTourProvider>
            {ClassRoomData && (
                <>
                    <AlertStudentModal show={show} handleClose={handleClose} />
                    <StudentChallenge
                        student={laravelData.user}
                        classData={ClassRoomData}
                    />
                </>
            )}

            <SpotlightTourManager classData={ClassRoomData} />

            <StudentStyledDashboard>
                <ClassContext.Provider value={ClassRoomData}>
                    {ClassRoomData && (
                        <>
                            {ClassRoomData.course && (
                                <div className="student-navbar-container">
                                    <StudentTitlebar
                                        laravel={laravelData}
                                        darkMode={state.darkMode}
                                        toggleDarkMode={() =>
                                            dispatch({ type: "toggleDarkMode" })
                                        }
                                        debug={debug}
                                    />
                                </div>
                            )}

                            <StudentDashboard
                                darkMode={state.darkMode}
                                laravel={laravelData}
                                debug={debug}
                            />
                        </>
                    )}
                </ClassContext.Provider>
            </StudentStyledDashboard>
        </SpotlightTourProvider>
    );
};

export default StudentPortalDataLayer;
