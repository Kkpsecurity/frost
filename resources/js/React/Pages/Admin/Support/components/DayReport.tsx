import React, { Dispatch, useEffect, useState } from "react";
import styled from "styled-components";

import {
    CourseLessonType,
    CourseUnitLessonType,
    CourseUnitType,
    InstUnitType,
    StudentLessonType,
    StudentUnitType,
} from "../../../../Config/types";

import { CurrentDaysLessons, DayHeader } from "./DailyReportList";

const DayReportWrapper = styled.div`
    display: block;
    width: 100%;
`;

interface LiveClassSupportProps {
    classData: any;
    selectUnitId: number | null;
    selectDay: number | null;
    selectDate: string | null;
    activeLesson: number | null;
    selectedCourseId: number | null;
    setShowDayReport: Dispatch<React.SetStateAction<boolean>>;
    showDayReport: boolean;
}

const DayReport = ({
    classData,
    selectUnitId,
    selectDay,
    selectDate,
    activeLesson,
    selectedCourseId,
    setShowDayReport,
    showDayReport,
}: LiveClassSupportProps) => {
    const {
        studentUnits,
        currentStudentLessons: studentLessons,
        courseUnits,
        courseUnitLessons,
        instUnit,
        lessons,
    } = classData;

    const [todaysUnit, setTodaysUnit] = useState<InstUnitType | null>(null);

    const [todaysLessons, setTodaysLessons] =
        useState<CourseUnitLessonType>(courseUnitLessons);

    /**
     * This tells what time the Instructor started this Class
     */
    useEffect(() => {
        if (instUnit && selectUnitId) {
            setTodaysUnit(instUnit || null);
        }
    }, [instUnit, selectUnitId]);

    /**
     * Get the Lesson required for the day
     */
    useEffect(() => {
        if (courseUnitLessons) {
            setTodaysLessons(courseUnitLessons);
        }
    }, [courseUnitLessons]);

    const getLessonTitle = (lessonId: number): string => {
        const lesson = lessons.find(
            (lesson: CourseLessonType) => lesson.id === lessonId
        );
        return lesson ? lesson.title : "";
    };

    const getStudentLessonStatusColor = (lessonId: number) => {
        const studentLesson = studentLessons.find(
            (lesson: StudentLessonType) => lesson.lesson_id === lessonId
        );

        if (!studentLesson) return "lightgray";
        if (studentLesson.dnc_at) return "#f8d7da";

        return studentLesson.completed_at ? "#d4edda" : "#fff3cd";
    };

    const getStudentLessonTime = (lessonId: number) => {};

    const getStudentLessonStatus = (lessonId: number) => {};

    return (
        <DayReportWrapper>
            <DayHeader
                selectDate={selectDate}
                todaysUnit={todaysUnit}
                setShowDayReport={setShowDayReport}
            />
            <CurrentDaysLessons
                courseUnitLessons={todaysLessons}
                getLessonTitle={getLessonTitle}
                getStudentLessonTime={getStudentLessonTime}
                getStudentLessonStatus={getStudentLessonStatus}
                getStudentLessonStatusColor={getStudentLessonStatusColor}
            />
        </DayReportWrapper>
    );
};

export default DayReport;
