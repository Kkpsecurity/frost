import React, { useEffect, useState } from "react";
import StudentStatusListGroupItem from "../../../../Hooks/Admin/StudentStatusListGroupItem";
import StudentLiveClassToolbar from "../../Instructors/FrostTraining/Students/StudentLiveClassToolbar";
import { CourseAuthType, StudentUnitType } from "../../../../Config/types";

const StudentStatusBar = ({
    classData,
    student,
    courseDateId,
    activeLesson,
    studentUnit,
    courseAuths,
    studentLesson,
    selectedCourseId,
}) => {
   
    const courseAuth = courseAuths && courseAuths.find(
        (auth: CourseAuthType) => auth.course_id === selectedCourseId
    );
   
    return selectedCourseId > 0 ? (
        <>
            <ul className="list-group">
                <StudentStatusListGroupItem
                    student={student}
                    courseDateId={courseDateId}
                    courseAuth={courseAuth}
                    activeLesson={activeLesson}
                    studentUnit={studentUnit}
                    studentLesson={studentLesson}
                    instUnit={classData.instUnit}
                />
            </ul>

            <StudentLiveClassToolbar
                student={student}
                classData={classData}
                activeLesson={activeLesson}
                studentLesson={studentLesson}
            />
        </>
    ) : null;
};

export default StudentStatusBar;
