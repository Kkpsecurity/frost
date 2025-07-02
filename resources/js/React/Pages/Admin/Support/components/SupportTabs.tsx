import React, { useEffect, useState } from "react";
import TabsLink from "./TabsLink";
import TabPanels from "./TabPanles";
import StudentStatusBar from "./StudentStatusBar";

import { StudentLessonType, StudentUnitType } from "../../../../Config/types";

const SupportTabs = ({
    isActive,
    classData,
    student,
    setSelectedCourseId,
    selectedCourseId,
}) => {
    const [selectedTab, setSelectedTab] = useState("activity");
    const [courseDateId, setCourseDateId] = useState<number | null>(
        classData.courseDateId
    );
    const [studentLesson, setStudentLesson] =
        useState<StudentLessonType | null>(null);

    const { todayStudentLessons, activeLesson, currentStudentUnit } = classData;
    const studentUnit: StudentUnitType | null = currentStudentUnit;

    useEffect(() => {
        if (activeLesson && todayStudentLessons) {
            let foundStudentLesson: StudentLessonType | null = null;

            foundStudentLesson = todayStudentLessons.find(
                (lesson: StudentLessonType) => lesson.lesson_id === activeLesson
            );

            setStudentLesson(foundStudentLesson);
        }
    }, [activeLesson, todayStudentLessons]);
   
    return (
        <div className="card">
            <div className="card-header p-2">
                <TabsLink
                    classData={classData}
                    selectedCourseId={selectedCourseId}
                    selectedTab={selectedTab}
                    setSelectedTab={setSelectedTab}
                />
            </div>

            <div className="card-body tabs">
                <StudentStatusBar
                    classData={classData}
                    student={student}
                    studentUnit={studentUnit}
                    courseAuths={student.courseAuths}
                    courseDateId={courseDateId}
                    activeLesson={activeLesson}
                    studentLesson={studentLesson}
                    selectedCourseId={selectedCourseId}
                />

                {selectedTab && (
                    <TabPanels
                        classData={classData}
                        student={student}
                        selectedCourseId={selectedCourseId}
                        setSelectedCourseId={setSelectedCourseId}
                        activeLesson={activeLesson}
                        selectedTab={selectedTab}
                        setSelectedTab={setSelectedTab}
                    />
                )}
            </div>
        </div>
    );
};

export default SupportTabs;
