import React, { useCallback, useEffect, useState } from "react";
import { Alert } from "react-bootstrap";
import { ThemeProvider } from "styled-components";

import {
    ClassDataShape,
    LaravelDataShape,
    StudentType,
} from "../../../../../../Config/types";
import FrostStudentOfflineTheme from "../../../../../../Styles/FrostStudentOfflineTheme.styled";

import StudentSideBar from "../../StudentSideBar";
import OfflineTopNav from "./OfflineTopNav";
import ClassRoomActivities from "../ClassRoomActivities";
import sidebarCollapseHandler from "../../Sidebar/sidebarCollapseHandler";

interface Props {
    darkMode: boolean;
    data: ClassDataShape | null;
    student: StudentType;
    debug: boolean;
}

const OfflineClassRoom: React.FC<Props> = ({
    darkMode,
    data,
    student,
    debug = false,
}) => {
    const [sidebarVisible, setSidebarVisible] = useState(true);
    const [section, setSection] = useState<string>("home-nav-tab");

    const toggleSidebarVisibility = useCallback(() => {
        setSidebarVisible((prevSidebarVisible) => !prevSidebarVisible);
    }, []);

    const handleWindowResize = useCallback(() => {
        // Assuming the sidebar should be hidden on small screens
        setSidebarVisible(window.innerWidth > 768);
    }, []);


    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(null);

    const [offlineSessionStarted, setOfflineSessionStarted] = useState<boolean | null>(false);


    const createTrackLesson = (e) => {
        e.preventDefault();
        setOfflineSessionStarted(true);
        console.log("Create Track Lesson" + e.target.id);       
    };

    const viewLesson = useCallback((lesson_id: number) => {
       setSelectedLessonId(lesson_id);        
    }, []);

    useEffect(() => {
        window.addEventListener("resize", handleWindowResize);
        handleWindowResize(); // Call initially to set correct state based on window size

        return () => {
            window.removeEventListener("resize", handleWindowResize);
        };
    }, [handleWindowResize]);

    const theme = { darkMode };

    const frostClassName = sidebarVisible ? "expanded" : "";

    console.log("OfflineClasdsroom: ", sidebarVisible);

    return (
        <ThemeProvider theme={theme}>
            <FrostStudentOfflineTheme
                darkMode={darkMode}
                className="offline-classroom"
                sidebarVisible={sidebarVisible}
            >
                <div className={`frost-container ${frostClassName}`}>
                    <div className={`frost-sidebar ${frostClassName}`}>
                        <StudentSideBar
                            darkMode={darkMode}
                            sidebarVisible={sidebarVisible}
                            toggleSidebarVisibility={toggleSidebarVisibility}
                            viewLesson={viewLesson}
                            debug={debug}
                        />
                    </div>
                    <div className={`frost-content ${frostClassName}`}>
                        <div className="frost-content-header-nav" style={{                         
                            backgroundColor: "var(--color-primary)",
                            position: "inherit",
                            display: "block"
                        }}>
                            <OfflineTopNav
                                darkMode={darkMode}
                                data={data}
                                student={student}
                                section={section}
                                setSection={setSection}
                                debug={debug}
                            />
                        </div>
                        <ClassRoomActivities
                            data={data}
                            student={student}
                            section={section}
                            selectedLessonId={selectedLessonId}
                            offlineSessionStarted={offlineSessionStarted}
                            createTrackLesson={createTrackLesson}
                            debug={debug}
                        />
                    </div>
                </div>
            </FrostStudentOfflineTheme>
        </ThemeProvider>
    );
};

export default OfflineClassRoom;
