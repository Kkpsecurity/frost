import React, { useContext, useState, useEffect, memo } from "react";
import { ListGroup } from "react-bootstrap";
import styled from "styled-components";

import { ClassDataShape, CourseLessonType } from "../../../../Config/types";
import { ClassContext } from "../../../../Context/ClassContext";
import { colors } from "../../../../Config/colors";

import CollapsedSidebar from "./Sidebar/CollapsedSidebar";
import ExpandedSidebar from "./Sidebar/ExpandedSidebar";

interface Props {
    darkMode: boolean;
    sidebarVisible: boolean;
    toggleSidebarVisibility: () => void;
    viewLesson: (lesson_id: number) => void;
    debug: boolean;
}

import {
    Sidebar,
    EXPAND_WIDTH,
    COLLAPSE_WIDTH,
} from "../../../../Styles/StyledSidebar.styled";

const StudentSideBar: React.FC<Props> = ({
    darkMode,
    sidebarVisible,
    toggleSidebarVisibility,
    viewLesson,
    debug = false,
}) => {
    /**
     * @description Get the class data from the context
     */
    const data = useContext(ClassContext) as ClassDataShape;

    const [activeLessonTitle, setActiveLessonTitle] = useState<string | null>(
        null
    );

    const getVariantClass = (
        lesson: CourseLessonType,
        isCompleted: boolean,
        inCompleted: boolean,
        studentFailed: boolean
    ): string => {
        if(debug) console.log("StudentSideBar: getVariantClass: ", lesson);

        if (
            data?.instUnitLesson &&
            data?.instUnitLesson.lesson_id === lesson.id
        ) {
            return "bg-info text-dark";
        } else if (studentFailed) {
            return "bg-danger";
        } else if (inCompleted) {
            return "bg-warning";
        } else if (isCompleted) {
            return "bg-success";
        }

        return "bg-dark-disabled";
    };

    useEffect(() => {
        if (data?.previousLessons.length > 0) {
            setActiveLessonTitle("Second Attempt");
        } else {
            setActiveLessonTitle("");
        }
    }, [data?.previousLessons]);

    return (
        <Sidebar className="sidebar" darkMode={darkMode}>
            <div
                className="sidebar-header d-flex justify-content-between align-items-center"
                style={{
                    width: sidebarVisible ? EXPAND_WIDTH : COLLAPSE_WIDTH,
                }}
            >
                <button
                    className="btn btn-lg mb-1 toggleSidebarButton"
                    onClick={toggleSidebarVisibility}
                >
                    <i
                        className={`fas fa-${
                            !sidebarVisible
                                ? "angle-double-right"
                                : "angle-double-left"
                        }`}
                    ></i>
                </button>
            </div>
            <div
                className="sidebar-content"
                style={{
                    width: sidebarVisible ? EXPAND_WIDTH : COLLAPSE_WIDTH,
                    transition: "width 0.3s ease",
                    height: "100vh",
                }}
            >
                {sidebarVisible ? (
                    <ExpandedSidebar
                        data={data}
                        getVariantClass={getVariantClass}
                        activeLessonTitle={activeLessonTitle}
                        setActiveLessonTitle={setActiveLessonTitle}
                        viewLesson={viewLesson}
                        toggleSidebarVisibility={toggleSidebarVisibility}
                        darkMode={darkMode}
                        debug={debug}
                    />
                ) : (
                    <CollapsedSidebar
                        data={data}
                        expandWidth={EXPAND_WIDTH}
                        collapseWidth={COLLAPSE_WIDTH}
                        getVariantClass={getVariantClass}
                        darkMode={darkMode}
                        debug={debug}
                    />
                )}
            </div>
        </Sidebar>
    );
};

export default memo(StudentSideBar);
