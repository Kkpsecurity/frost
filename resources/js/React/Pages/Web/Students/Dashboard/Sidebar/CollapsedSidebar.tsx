import React, { useState } from "react";
import {
    ClassDataShape,
    CourseLessonType,
    StudentType,
} from "../../../../../Config/types";
import styled from "styled-components";
import { colors } from "../../../../../Config/colors"; // Import the color palette

const SidebarContainer = styled.div<{ width: string; variantColor: string }>`
    position: relative;
    width: ${({ width }) => width};
    display: inline-block;
    margin-right: 1rem;
`;

const SidebarButton = styled.button<{ variantColor: string }>`
    width: 50px;
    height: 50px;
    border: none;
    outline: none;
    cursor: pointer;
    font-size: 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    color: ${({ variantColor }) => variantColor};
    z-index: 10; // Add this line
`;

const SidebarTitle = styled.div<{ showTitle: boolean }>`
    position: absolute;
    left: 60px;
    padding: 0.5rem;
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    box-shadow: 0 0 10px rgba(26, 26, 26, 0.5);
    display: ${({ showTitle }) => (showTitle ? "block" : "none")};
    min-width: 240px;
    transition: all 0.3s ease-in-out;
    width: auto;
    height: 60px;
    z-index: 1000; // Increase the z-index value
`;

interface Props {
    data: ClassDataShape;
    expandWidth: string;
    collapseWidth: string;
    getVariantClass: (
        lesson: CourseLessonType,
        isCompleted: boolean,
        inCompleted: boolean,
        studentFailed: boolean
    ) => string;
    darkMode: boolean;
    debug: boolean;
}

const CollapsedSidebar: React.FC<Props> = ({
    data,
    expandWidth,
    collapseWidth,
    getVariantClass,
    darkMode,
    debug = false,
}) => {
    const [hoveredLesson, setHoveredLesson] = useState<number>(0);

    const handleMouseOver = (lessonId: number) => {
        setHoveredLesson(lessonId);
    };

    const handleMouseLeave = () => {
        setHoveredLesson(0);
    };

    return (
        <>
            {Object.values(data.courseLessons).map(
                (lesson: CourseLessonType) => {
                    let isCompleted = false;
                    let inCompleted = false;
                    let studentFailed = false;
                    let instIsCompleted = false;

                    const {
                        previousLessons, // Student's previous lessons
                        is_live_class,
                    } = data;

                    if(!previousLessons) return null;

                    /**
                     * Indicates the student has previous lessons
                     */
                    if (Array.isArray(previousLessons) && previousLessons.includes(lesson.id)) {
                        isCompleted = true;
                    }  else {
                        if (is_live_class) {
                            
                            /**
                             * Indicates that the instructor
                             * has ended the lesson array
                             */
                            if (
                                data.completedInstLessons &&
                                Array.isArray(data.completedInstLessons) &&
                                data?.completedInstLessons.some(
                                    (completedInstLessons) => {
                                        return (
                                            completedInstLessons.lesson_id ===
                                            lesson.id
                                        );
                                    }
                                )
                            ) {
                                instIsCompleted = true;
                            }

                            /**
                             * Indicates that the lesson the student is completed
                             */
                            if (debug)
                                console.log(
                                    "THE-COMPLETED-LESSONS Collapsed: ",
                                    data?.completedStudentLessons
                                );

                            // we can consider it as incomplete
                            if (instIsCompleted && !isCompleted) {
                                inCompleted = true;
                            }
                        }

                        /**
                         * Indicates that the lesson the student is completed
                         */
                        if (data?.completedStudentLessons) {
                            studentFailed = data?.completedStudentLessons.some(
                                (completedLesson) => {
                                    if (
                                        completedLesson.lesson_id ===
                                            lesson.id &&
                                        completedLesson.dnc_at
                                    ) {
                                        return true;
                                    }
                                    return false;
                                }
                            );
                        }
                    }

                    return (
                        <div key={lesson.id}>
                            <SidebarContainer width={collapseWidth}>
                                <SidebarButton
                                    onMouseOver={() =>
                                        handleMouseOver(lesson.id)
                                    }
                                    onMouseLeave={handleMouseLeave}
                                    className={getVariantClass(
                                        lesson,
                                        isCompleted,
                                        inCompleted,
                                        studentFailed
                                    )}
                                >
                                    <b>
                                        {lesson["title"]
                                            .charAt(0)
                                            .toUpperCase()}
                                    </b>
                                </SidebarButton>
                            </SidebarContainer>
                            <SidebarTitle
                                showTitle={hoveredLesson === lesson.id}
                            >
                                {lesson["title"]}
                            </SidebarTitle>
                        </div>
                    );
                }
            )}
        </>
    );
};

export default CollapsedSidebar;
