import React, { useState, useEffect, useContext } from "react";
import StudentSideBar from "../StudentSideBar";
import MeetingArea from "../MeetingArea";
import { ClassDataShape, LaravelDataShape } from "../../../../../Config/types";
import sidebarCollapseHandler from "../Sidebar/sidebarCollapseHandler";
import FrostStudentLiveTheme from "../../../../../Styles/FrostStudentLiveTheme.styled";
import PauseOverlay from "../../../../Admin/Instructors/FrostTraining/Partials/PauseOverlay";
import { ClassContext } from "../../../../../Context/ClassContext";
import PageLoader from "../../../../../Components/Widgets/PageLoader";
import { ThemeProvider } from "react-bootstrap";

interface VCRProps {
    darkMode: boolean;
    laravel: LaravelDataShape;
    debug?: boolean;
}

interface SidebarCollapseHandlerProps {
    setSidebarVisible: React.Dispatch<React.SetStateAction<boolean>>;
    sidebarVisible: boolean;
    setSidebarHidden: React.Dispatch<React.SetStateAction<boolean>>;
    sidebarHidden: boolean;
}

const VirtualClassRoom: React.FC<VCRProps> = ({
    darkMode,
    laravel,
    debug = false,
}) => {
    if (debug) console.log("VirtualClassRoom: ", laravel);

    /**
     * Sidebar Collapse Handler // optional prop
     */
    const [sidebarVisible, setSidebarVisible] = useState(true);
    const [sidebarHidden, setSidebarHidden] = useState(false);

    /**
     * Sidebar Collapse Handler
     */
    const { toggleSidebarVisibility, handleWindowResize } =
        sidebarCollapseHandler({
            setSidebarVisible,
            sidebarVisible,
            setSidebarHidden,
            sidebarHidden,
        });

    const frostContainerClassName = `${sidebarVisible ? "expanded" : ""}`;
    const frostSidebarClassName = `${sidebarVisible ? "expanded" : ""}`;
    const frostContentClassName = `${sidebarVisible ? "expanded" : ""}`;

    /**
     * Pauses Lesson
     */
    const [isPaused, setIsPaused] = useState(false);
    const [pauseLesson, setPauseLesson] = useState(false);

    /**
     * ClassRoom Context 
     */
    const data = useContext(ClassContext) as unknown as ClassDataShape;

    useEffect(() => {
        const resizeCallback = handleWindowResize();
        window.addEventListener("resize", resizeCallback);

        return () => {
            window.removeEventListener("resize", resizeCallback);
        };
    }, []);

    useEffect(() => {
        setIsPaused(data?.lessonPaused);
    }, [data?.lessonPaused]);

    const theme = { darkMode };
    
    return (
        <ThemeProvider theme={theme}>
            <FrostStudentLiveTheme
                darkMode={darkMode}
                sidebarVisible={sidebarVisible}
                className="online-classroom"
            >
                <PauseOverlay handleClose={false} isPaused={isPaused} />
                <div className={"frost-container " + frostContainerClassName}>
                    <div className={"frost-sidebar " + frostSidebarClassName}>
                        {!sidebarHidden && (
                            <div className={frostSidebarClassName}>
                                <StudentSideBar
                                    darkMode={darkMode}
                                    sidebarVisible={sidebarVisible}
                                    toggleSidebarVisibility={
                                        toggleSidebarVisibility
                                    }
                                    viewLesson={() => alert("View Lesson only available for Offline Class")}
                                    debug={debug}
                                />
                            </div>
                        )}
                    </div>
                    
                    <div className={"frost-content " + frostContentClassName}>
                        <MeetingArea
                            darkMode={darkMode}
                            laravel={laravel}
                            debug={debug}
                        />
                    </div>
                </div>
            </FrostStudentLiveTheme>
        </ThemeProvider>
    );
};

export default VirtualClassRoom;
