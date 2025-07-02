import React, { useCallback, useEffect, useState } from "react";
import {
    CourseMeetingShape,
    LaravelAdminShape,
    StudentTabType,
    StudentType,
} from "../../../../Config/types";
import { useActiveStudentHook } from "../../../../Hooks/Admin/useInstructorHooks";
import PauseOverlay from "./Partials/PauseOverlay";
import { ToastContainer } from "react-toastify";
import { toast } from "react-toastify";
import ClassTopNav from "./ClassTopNav";
import { Alert, Card, Col, Row } from "react-bootstrap";
import Loading from "../../../../Components/Widgets/Loader";

import StudentsTabs from "./Students/StudentTabs";
import { useStudentData } from "./ClassDataBlocks/useStudentData";
import ValidationModals from "./ValidationModals";
import LessonProgress from "../../../../Components/Plugins/Students/LessonProgress";
import StudentDetailCard from "./Students/StudentDetailCard";
import { useCourseLessonsData } from "./ClassDataBlocks/useCourseLessonsData";
import ZoomMeetingInterface from "./ZoomMeetingInterface";
import usePauseClassRoomHook from "../../../../Hooks/Admin/usePauseClassRoomHook";
import { useTopNavData } from "./ClassDataBlocks/useTopNavData";

type AssistantVewProps = {
    laravel: LaravelAdminShape;
    CourseMeetingData: CourseMeetingShape;
    courseDateId: number | null;
    handleTakeOver: (event: React.MouseEvent<HTMLButtonElement>) => void;
    handleAssignAssistant: (event: React.MouseEvent<HTMLButtonElement>) => void;
    setAssignedAssistantId: Function;
    debug: boolean;
};

const AssistantView: React.FC<AssistantVewProps> = ({
    laravel,
    CourseMeetingData,
    courseDateId,
    handleTakeOver,
    handleAssignAssistant,
    setAssignedAssistantId,
    debug,
}) => {
    const [selectedUserType, setSelectedUserType] = useState<string | null>("");
    const [selectedUserHeadshot, setSelectedUserHeadshot] = useState<
        string | null
    >("");
    const [selectedUserIdcard, setSelectedUserIdcard] = useState<string | null>(
        ""
    );

    /**
     * Sets The Lesson start Time
     */
    const [lessonStartTime, setLessonStartTime] = useState<string>("");

    /**
     * Pagination and Search
     */
    const [currentPage, setCurrentPage] = useState<number>(1);
    const [search, setSearch] = useState<string>("");

    /**
     * State for the main array of students groups
     * unverifed, verifed, inactive
     */
    const [studentGroups, setStudentGroups] = useState<StudentTabType | null>(
        null
    );

    /**
     * Creates a single student array
     */
    const [flattenedStudentArray, setFlattenedStudentArray] = useState<
        StudentType[]
    >([]);

    /**
     * Course Lesson Data and Actions
     */
    const { ActivateLesson, setActiveLesson, activeLesson } =
        useCourseLessonsData(courseDateId);

    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [loadingSelectedStudent, setLoadingSelectedStudent] =
        useState<boolean>(false);

    /**
     * Handel Pause overlay
     */
    const { isPaused, togglePauseLesson, show, setShow } =
        usePauseClassRoomHook({
            laravel,
            courseDateId,
            activeLesson,
            instUnitLesson: CourseMeetingData.instUnitLesson,
        });

    /**
     * Managers The TopNav Actions
     */
    const { MarkLessonComplete, MarkCourseComplete } = useTopNavData(
        courseDateId,
        activeLesson,
        setActiveLesson
    );

    /**
     * Gets all students accessing the classroom
     * Polling monitors for new students entering or leaving the classroom
     * @polling: 15sec
     */
    const { data: activeStudentData, isLoading } = useActiveStudentHook(
        courseDateId,
        currentPage,
        search
    );

    const studentUnitLesson = activeStudentData?.studentUnitLesson || null;

    const flattenStudentArray = useCallback(
        (studentGroups: StudentTabType) => {
            if (!studentGroups) {
                return [];
            }

            const { verified, unverified, inactive } = studentGroups;

            // Extract student arrays from each category
            const verifiedStudents = verified?.students || [];
            const unverifiedStudents = unverified?.students || [];
            const inactiveStudents = inactive?.students || [];

            // Combine them into a single flat array
            const flatArray = [
                ...verifiedStudents,
                ...unverifiedStudents,
                ...inactiveStudents,
            ];

            return flatArray;
        },
        [activeStudentData]
    );

    useEffect(() => {
        if (activeStudentData) {
            const { verified, unverified, inactive } = activeStudentData;

            const updatedStudents: StudentTabType = {
                verified: {
                    students: verified?.data || [],
                    current_page: verified.current_page,
                    last_page: verified.last_page,
                    total: verified.total,
                },
                unverified: {
                    students: unverified?.data || [],
                    current_page: unverified.current_page,
                    last_page: unverified.last_page,
                    total: unverified.total,
                },
                inactive: {
                    students: inactive?.data || [],
                    current_page: inactive.current_page,
                    last_page: inactive.last_page,
                    total: inactive.total,
                },
                success: false,
                message: "",
            };

            setStudentGroups(updatedStudents);

            // Flatten the student arrays into a single array
            const flatArray = flattenStudentArray(updatedStudents);
            setFlattenedStudentArray(flatArray);
        }
    }, [activeStudentData, flattenStudentArray]);

    /**
     * Manages the Setting of the Active Lesson
     */
    useEffect(() => {
        if (CourseMeetingData !== undefined) {
            if (CourseMeetingData.success === false) {
                toast.error(
                    "Something went wrong! here's what we found: " +
                        CourseMeetingData.message
                );
            }

            if (CourseMeetingData?.instUnitLesson !== null) {
                setActiveLesson(CourseMeetingData.instUnitLesson.lesson_id);

                // Convert Unix timestamp to JavaScript Date
                if (
                    typeof CourseMeetingData.instUnitLesson.created_at ===
                    "number"
                ) {
                    const createdDate = new Date(
                        CourseMeetingData.instUnitLesson.created_at * 1000
                    );

                    const lessonStartTime = createdDate.toLocaleString();
                    setLessonStartTime(lessonStartTime);
                }
            } else {
                setActiveLesson(0);
                setLessonStartTime("");
            }
        } else {
            setActiveLesson(null);
        }
    }, [CourseMeetingData]);

    /**
     * Manages The Student State Data and Actions
     * For a single user
     */
    const {
        ValidateStudent,
        ViewStudentCard,
        setStudentAuthId,
        studentAuthId,
        selectedStudent,
        setSelectedStudent,
        selectStudentId,
        setSelectStudentId,
    } = useStudentData({
        courseDateId,
        setShow,
        setValidateType: setSelectedUserType,
        setHeadshot: setSelectedUserHeadshot,
        setIdcard: setSelectedUserIdcard,
    });

    const handleClose = ({} = {});

    if (!courseDateId || !activeStudentData) return <></>;
    const SelectStudent = (studentId: number) => {
        if (!studentId) alert("Student ID is Missing");

        // Call ViewStudentCard when a user is selected
        setLoadingSelectedStudent(true);

        setTimeout(() => {
            ViewStudentCard(studentId);
            setSelectedUserId(studentId);
            setLoadingSelectedStudent(false);
        }, 2000);
    };

    return (
        <>
            <ToastContainer />
            <Row className="d-flex">
                <Col xs={12} className="bg-dark text-white">
                    <ClassTopNav
                        laravel={laravel}
                        activeLesson={activeLesson}
                        data={CourseMeetingData}
                        markLessonComplete={() => {}}
                        markCourseComplete={() => {}}
                        pauseLesson={() => {}}
                        isPaused={isPaused}
                        setAssignedAssistantId={setAssignedAssistantId}
                    />
                </Col>
            </Row>
            <Row className="d-flex h-100">
                <Col
                    xs={12}
                    lg={4}
                    className="p-0 m-0"
                    style={{ backgroundColor: "#888" }}
                >
                    <StudentsTabs
                        studentGroups={studentGroups}
                        currentPage={currentPage}
                        setCurrentPage={setCurrentPage}
                        search={search}
                        setSearch={setSearch}
                        ValidateStudent={ValidateStudent}
                        selectedStudentId={selectedUserId}
                        student={selectedStudent}
                        SelectStudent={SelectStudent}
                        activeLesson={activeLesson}
                        instUnit={CourseMeetingData.instUnit}                        
                    />
                </Col>
                <Col
                    xs={12}
                    lg={8}
                    className="p-0 m-0"
                    style={{ backgroundColor: "#444" }}
                >
                    <ZoomMeetingInterface
                        laravel={laravel}
                        data={CourseMeetingData}
                        courseDateId={courseDateId}
                    />
                    
                    <hr />
                    <Card className="card-flat" style={{ height: "auto" }}>
                        <LessonProgress
                            activeLesson={activeLesson}
                            lessons={CourseMeetingData.lessons}
                            lessonStartTime={lessonStartTime}
                            courseUnitLessons={
                                CourseMeetingData.courseUnitLessons
                            }
                            isPaused={isPaused}
                        />
                    </Card>

                    {selectedStudent?.id && (
                        <>
                            {loadingSelectedStudent ? (
                                <Loading />
                            ) : (
                                <>
                                    <hr />
                                    <StudentDetailCard
                                       selectedStudent={selectedStudent}
                                       studentUnit={selectedStudent.studentUnit}
                                       courseAuths={selectedStudent.courseAuths}
                                       classData={CourseMeetingData}
                                       selectCourseId={selectedStudent.course_id}
                                       setSelectStudentId={setSelectStudentId}
                                       activeLesson={activeLesson}
                                    />
                                </>
                            )}
                        </>
                    )}
                </Col>
            </Row>

            {show && (
                <ValidationModals
                    ClassData={CourseMeetingData}
                    courseDateId={courseDateId}
                    studentAuthId={studentAuthId}
                    student={selectedStudent}
                    setHeadshot={setSelectedUserHeadshot}
                    headshot={selectedUserHeadshot}
                    setIdcard={setSelectedUserIdcard}
                    idcard={selectedUserIdcard}
                    show={show}
                />
            )}
        </>
    );
};

export default AssistantView;
