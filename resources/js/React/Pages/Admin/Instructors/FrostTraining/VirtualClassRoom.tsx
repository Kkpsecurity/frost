import React, { useState, useEffect, useCallback } from "react";
import { Row, Col, Alert, ToastContainer } from "react-bootstrap";

import {
    StudentTabType,
    LaravelAdminShape,
    CourseMeetingShape,
    StudentType,
    StudentLessonType,
} from "../../../../Config/types";

import { useActiveStudentHook } from "../../../../Hooks/Admin/useInstructorHooks";

import { toast } from "react-toastify";

import ClassTopNav from "./ClassTopNav";
import ValidationModals from "./ValidationModals";
import CourseLessons from "./CourseLessons";
import CourseDetailCard from "./CourseDetailCard";
import StudentsTabs from "./Students/StudentTabs";

import { studentValidation } from "./Validation/VerifyStudentRules";

import { useTopNavData } from "./ClassDataBlocks/useTopNavData";
import { useCourseLessonsData } from "./ClassDataBlocks/useCourseLessonsData";
import { useStudentData } from "./ClassDataBlocks/useStudentData";

import PauseOverlay from "./Partials/PauseOverlay";
import usePauseClassRoomHook from "../../../../Hooks/Admin/usePauseClassRoomHook";
import PageLoader from "../../../../Components/Widgets/PageLoader";

type VirtualClassroomProps = {
    laravel: LaravelAdminShape;
    CourseMeetingData: CourseMeetingShape;
    courseDateId: number;
    debug: boolean;
};

const VirtualClassroom: React.FC<VirtualClassroomProps> = ({
    laravel,
    CourseMeetingData,
    courseDateId,
    debug,
}) => {
    if (debug) console.log("VirtualClassroom", CourseMeetingData);


    const [validateType, setValidateType] = useState<string | null>("");
    const [headshot, setHeadshot] = useState<string | string[] | null>("");
    const [idcard, setIdcard] = useState<string | null>("");

    const [currentPage, setCurrentPage] = useState<number>(1);
    const [search, setSearch] = useState<string>("");

    /**
     * Sets The Lesson start Time
     */
    const [lessonStartTime, setLessonStartTime] = useState<string>("");

    /**
     * State for all students
     */
    const [allStudents, setAllStudents] = useState<StudentTabType | null>(null);
    const [studentFlatArray, setStudentFlatArray] = useState<StudentType[]>([]);
    const [assignedAssistantId, setAssignedAssistantId] = useState<
        number | null
    >(null);

    /**
     * Selected Student State
     */
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [loadingSelectedStudent, setLoadingSelectedStudent] =
        useState<boolean>(false);
    const [studentLesson, setStudentLesson] =
        useState<StudentLessonType | null>(null);

    /**
     * Course Lesson Data and Actions
     */
    const { ActivateLesson, setActiveLesson, activeLesson } =
        useCourseLessonsData(courseDateId);

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
     * Managers The TopNav State Data and Actions
     */
    const { MarkLessonComplete, MarkCourseComplete } = useTopNavData(
        courseDateId,
        activeLesson,
        setActiveLesson
    );

    /**
     * Gets All Students that accesses the classroom
     * Polling monitors for new students entering or leaving the classroom
     * @polling: 15sec
     */
    const { data: splitKeyStudents, isLoading } = useActiveStudentHook(
        courseDateId,
        currentPage,
        search
    );

    const flattenStudentArray = useCallback(
        (studentGroups: StudentTabType) => {
            if (!studentGroups) {
                return [];
            }

            // Extract just the student arrays from each category
            const verifiedStudents = studentGroups.verified.students || [];
            const unverifiedStudents = studentGroups.unverified.students || [];
            const inactiveStudents = studentGroups.inactive.students || [];

            // Combine them into a single flat array
            const flatArray = [
                ...verifiedStudents,
                ...unverifiedStudents,
                ...inactiveStudents,
            ];

            return flatArray;
        },
        [splitKeyStudents] // Update dependency array if necessary
    );

    useEffect(() => {
        if (splitKeyStudents) {
            const updatedStudents: StudentTabType = {
                verified: {
                    students: splitKeyStudents.verified?.data || [],
                    current_page: splitKeyStudents.verified.current_page,
                    last_page: splitKeyStudents.verified.last_page,
                    total: splitKeyStudents.verified.total,
                },
                unverified: {
                    students: splitKeyStudents.unverified?.data || [],
                    current_page: splitKeyStudents.unverified.current_page,
                    last_page: splitKeyStudents.unverified.last_page,
                    total: splitKeyStudents.unverified.total,
                },
                inactive: {
                    students: splitKeyStudents.inactive?.data || [],
                    current_page: splitKeyStudents.inactive.current_page,
                    last_page: splitKeyStudents.inactive.last_page,
                    total: splitKeyStudents.inactive.total,
                },
                success: false,
                message: "",
            };

            setAllStudents(updatedStudents);

            // Flatten the student arrays into a single array
            const flatArray = flattenStudentArray(updatedStudents);
            setStudentFlatArray(flatArray);
        }
    }, [splitKeyStudents, flattenStudentArray]);

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

                    const lessonStartTime = createdDate.toLocaleString(); // Use toLocaleString() or specify the desired format

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
        courseAuths,
        selectedStudent,
        studentUnit,
        setSelectedStudent,
        selectStudentId,
        setSelectStudentId,
    } = useStudentData({
        courseDateId,
        setShow,
        setValidateType,
        setHeadshot,
        setIdcard,
    });
 
    const handleClose = () => {};

    useEffect(() => {
        selectedStudent?.studentLessons?.map((lesson) => {
            if (lesson.lesson_id === activeLesson) {
                setStudentLesson(lesson);
            }
        });
    }, [activeLesson]);

    if (!courseDateId) {
        return (
            <Alert variant="danger">
                CourseDate ID is not being passed to this point
            </Alert>
        );
    }

    if (!splitKeyStudents) return <></>;

    /**
     * Get the student Id and set the selected student
     * @param studentId
     */
    const SelectStudent = (studentId: number) => {
        setLoadingSelectedStudent(true);

        // Check if it's the same student being re-selected to possibly skip reloading
        if (studentId !== selectStudentId) {
            setSelectedUserId(studentId);

            // Simulate fetching student details or other async operation
            setTimeout(() => {
                ViewStudentCard(studentId);
                setLoadingSelectedStudent(false); // Set loading to false after operation completes
            }, 2000);
        } else {
            setLoadingSelectedStudent(false);
        }
    };

   return (
        <>
            <PauseOverlay handleClose={togglePauseLesson} isPaused={isPaused} />
            <ToastContainer />
            <Row className="d-flex">
                <Col xs={12} className="bg-dark text-white">
                    <ClassTopNav
                        laravel={laravel}
                        data={CourseMeetingData}
                        activeLesson={activeLesson}
                        markLessonComplete={MarkLessonComplete}
                        markCourseComplete={MarkCourseComplete}
                        pauseLesson={togglePauseLesson}
                        setAssignedAssistantId={setAssignedAssistantId}
                        isPaused={isPaused}
                    />
                </Col>
            </Row>
            <Row>
                <Col lg={3} md={3} xs={6} className="bg-dark m-0 p-0">
                    <div style={{ maxHeight: "100%", overflowY: "auto" }}>
                        <CourseLessons
                            laravel={laravel}
                            courseDateId={courseDateId}
                            lessons={CourseMeetingData.lessons}
                            completedLessons={
                                CourseMeetingData.completedLessons
                            }
                            ActivateLesson={ActivateLesson}
                            activeLesson={activeLesson}
                            instUnitLesson={CourseMeetingData.instUnitLesson}
                            zoomStatus={
                                CourseMeetingData?.instructor?.zoom_payload
                                    ?.zoom_status ?? null
                            }
                        />
                    </div>
                </Col>

                <Col xs={6} lg={5} className="bg-dark vh-100">
                    <div style={{ maxHeight: "100%", overflowY: "auto" }}>
                        <CourseDetailCard
                            data={CourseMeetingData}
                            activeLesson={activeLesson}
                            courseDateId={courseDateId}
                            lessonStartTime={lessonStartTime}
                            loadingSelectedStudent={loadingSelectedStudent}
                            selectStudentId={selectStudentId}
                            selectedStudent={selectedStudent}
                            studentUnit={studentUnit}
                            courseAuths={courseAuths}
                            setSelectStudentId={setSelectStudentId}
                            allStudents={flattenStudentArray(allStudents)}
                            headshot={headshot}
                            idcard={idcard}
                            laravel={laravel}
                            isPaused={isPaused}
                        />
                    </div>
                </Col>

                <Col
                    xs={12}
                    lg={4}
                    className="p-0 m-0"
                    style={{ backgroundColor: "#888" }}
                >
                    <StudentsTabs
                        studentGroups={allStudents ?? null}
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
            </Row>
            console.log("VALVALVALV", headshot, idcard);
            {show && (
                <ValidationModals
                    ClassData={CourseMeetingData}
                    courseDateId={courseDateId}
                    studentAuthId={studentAuthId}
                    student={selectedStudent}
                    setHeadshot={setHeadshot}
                    headshot={headshot}
                    setIdcard={setIdcard}
                    idcard={idcard}
                    show={show}
                />
            )}
        </>
    );
};

export default VirtualClassroom;
