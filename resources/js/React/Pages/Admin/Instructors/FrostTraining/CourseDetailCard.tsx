import React, { useState, useEffect } from "react";
import { Card } from "react-bootstrap";
import {
    StudentType,
    LaravelAdminShape,
    CourseMeetingShape,
    StudentUnitType,
    CourseAuthType,
} from "../../../../Config/types";

import LessonProgress from "../../../../Components/Plugins/Students/LessonProgress";
import StudentDetailCard from "./Students/StudentDetailCard";
import ZoomMeetingInterface from "./ZoomMeetingInterface";

import FrostInstructorVideoChat from "../../../../Components/Plugins/Frost/FrostVideoChat/FrostInstructorVideoChat";
import Loader from "../../../../Components/Widgets/Loader";

/**
 * ScreenSharing: Development only
 */
import FrostChatCard from "../../../../Components/Plugins/Frost/FrostAjaxChat/FrostChatCard";
import FrostInstructorScreenShare from "../../../../Components/Plugins/Frost/FrostScreenShare/FrostInstructorScreenShare";
import AssistingClassroom from "./Partials/AssistingClassroom";

/**
 * @interface CourseDetailCardProps
 */
interface CourseDetailCardProps {
    data: CourseMeetingShape;
    setSelectStudentId: React.Dispatch<React.SetStateAction<number | null>>;
    selectStudentId: number | null;
    selectedStudent: StudentType | null;
    courseAuths: CourseAuthType[] | [];
    loadingSelectedStudent: boolean;
    lessonStartTime: string;
    allStudents: StudentType[];
    activeLesson: number | null;
    courseDateId: number;
    headshot: string | string[] | null;
    idcard: string | null;
    laravel: LaravelAdminShape;
    studentUnit: StudentUnitType | null;
    isPaused: boolean;
}

/**
 * @param param0
 * @returns
 */
const CourseDetailCard: React.FC<CourseDetailCardProps> = ({
    data,
    setSelectStudentId,
    selectStudentId,
    loadingSelectedStudent,
    lessonStartTime,
    selectedStudent,
    courseAuths,    
    allStudents,
    activeLesson,
    studentUnit,
    courseDateId,
    headshot,
    idcard,
    laravel,
    isPaused,
}) => {
    const [error, setError] = useState<string | null>(null);
    
    /**
     * Trigger the Opening of the VideoChat
     */
    const [makeCall, setMakeCall] = React.useState<boolean>(false);  

    console.log("CourseDetailCard", selectedStudent);

    return (
        <>
            <Card className="card-flat" style={{ height: "auto" }}>
                {data?.instructor?.id === 2220000000 ? (
                    <FrostInstructorScreenShare
                        data={data}
                        laravel={laravel}
                        courseDateId={courseDateId}
                    />
                ) : (
                    <ZoomMeetingInterface
                        data={data}
                        laravel={laravel}
                        courseDateId={courseDateId}
                    />
                )}
            </Card>

            {data.instUnit.assistant_id !== null && (
                <>
                    <hr />
                    <Card className="card-flat" style={{ height: "auto" }}>
                        <AssistingClassroom
                            assistantId={data.instUnit.assistant_id}
                        />
                    </Card>
                </>
            )}

            <Card className="card-flat" style={{ height: "auto" }}>
                <LessonProgress
                    activeLesson={activeLesson}
                    lessons={data.lessons}
                    lessonStartTime={lessonStartTime}
                    courseUnitLessons={data?.courseUnitLessons}
                    isPaused={isPaused}
                />
            </Card>

            
            {selectStudentId && selectedStudent && (
                <>
                    {loadingSelectedStudent ? (
                        <Loader />
                    ) : (
                        <>
                            <hr />
                            <StudentDetailCard
                                studentUnit={studentUnit}
                                courseAuths={courseAuths}
                                selectedStudent={selectedStudent}
                                classData={data}
                                selectCourseId={selectedStudent.course_id}
                                setSelectStudentId={setSelectStudentId}
                                activeLesson={activeLesson}
                            />
                        </>
                    )}
                </>
            )}

            {(laravel.user.role_id === 1 || laravel.user.role_id === 2) && (
                <>
                    <hr />
                    <FrostInstructorVideoChat
                        makeCall={makeCall}
                        setMakeCall={setMakeCall}
                        allStudents={allStudents}
                        courseDateId={courseDateId}
                        laravelUserId={data?.instructor?.id}
                        laravel={laravel}
                    />
                </>
            )}

            <hr />
            <FrostChatCard
                course_date_id={courseDateId}
                isChatEnabled={data.isChatEnabled}
                chatUser={{
                    user_id: data?.instructor?.id,
                    user_type: "instructor",
                    user_name:
                        data?.instructor.fname + " " + data?.instructor.lname,
                    user_avatar: data?.instructor.avatar ?? "",
                }}
                darkMode={false}
                debug={false}
            />
        </>
    );
};

export default CourseDetailCard;
