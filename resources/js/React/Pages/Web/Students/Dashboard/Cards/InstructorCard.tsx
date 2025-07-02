// This project we are working on a Video Call system
// This is the Instructor Card that will be displayed on the Student Dashboard
// Purpose: To Track all Events to validatr a complete call for both the Student Side and Instructor side

import React from "react";
import { ClassDataShape, LaravelDataShape } from "../../../../../Config/types";
import { colors } from "../../../../../Config/colors";

import InstructorData from "./InstructorData";
import VideoCallApp from "./VideoCallApp";

import useStudentVideoCallBlock from "../../../../../Components/Plugins/Frost/FrostVideoChat/blocks/student/StudentVideoCallBlock";

interface Props {
    darkMode: boolean;
    laravel: LaravelDataShape;
    data: ClassDataShape;
    debug: boolean;
}

const InstructorCard: React.FC<Props> = ({
    darkMode,
    laravel,
    data,
    debug,
}) => {
    const colorSet = colors[darkMode ? "dark" : "light"];

    const {
        makeCall,
        callAccepted,
        inComingRequest,
        handleMakeCall,
        handleAcceptCall,
        handleEndCall,
    } = useStudentVideoCallBlock({
        laravel: laravel,
        data: data,
    });

    if (!data?.instructor) return <></>;

    return (
        <>
            <InstructorData
                data={data}
                laravel={laravel}
                colorSet={colorSet}
                handleMakeCall={handleMakeCall}
                makeCall={makeCall}
                inComingRequest={inComingRequest}
                callAccepted={callAccepted}
                darkMode={darkMode}
                debug={debug}
            />

            {laravel.user.id === 17 && (
                <div
                    style={{
                        position: "relative",
                        bottom: "0",
                        right: "0",
                    }}
                >
                    <VideoCallApp
                        laravel={laravel}
                        data={data}
                        inComingRequest={inComingRequest}
                        handleAcceptCall={handleAcceptCall}
                        handleEndCall={handleEndCall}
                        callAccepted={callAccepted}
                        makeCall={makeCall}
                        darkMode={darkMode}
                        debug={debug}
                    />
                </div>
            )}
        </>
    );
};

export default InstructorCard;
