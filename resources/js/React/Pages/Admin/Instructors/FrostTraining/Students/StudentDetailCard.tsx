import React, { useEffect, useState } from "react";
import {
    CourseAuthType,
    CourseMeetingShape,
    InstUnitType,
    LaravelAdminShape,
    StudentLessonType,
    StudentType,
    StudentUnitType,
} from "../../../../../Config/types";
import { Card, ListGroup, Modal } from "react-bootstrap";
import StudentStatusListGroupItem from "../../../../../Hooks/Admin/StudentStatusListGroupItem";
import StudentProfileListItems from "./StudentProfileListItems";
import StudentStatusBar from "../../../Support/components/StudentStatusBar";

const StudentHeaderTitle = ({ selectedStudent, setSelectStudentId }) => {
    return (
        <div className="d-flex justify-content-between align-items-center">
            <h4
                style={{
                    color: "white",
                    fontSize: "1.1rem",
                    textTransform: "uppercase",
                    margin: "0",
                    padding: "0",
                }}
            >
                Student Details
            </h4>
            <button
                className="btn btn-flat btn-sm"
                onClick={() => setSelectStudentId(null)}
            >
                <span className="text-white">
                    <i className="fa fa-times"></i>
                </span>
            </button>
        </div>
    );
};

interface StudentDetailCardProps {
    classData: CourseMeetingShape;
    studentUnit: StudentUnitType | null;
    courseAuths: CourseAuthType[] | [];
    selectedStudent: StudentType | null;
    setSelectStudentId: (id: number | null) => void;
    selectCourseId: number | null;
    activeLesson: number | null;
}

const StudentDetailCard: React.FC<StudentDetailCardProps> = ({
    classData,
    selectedStudent,
    studentUnit,
    courseAuths,
    setSelectStudentId,
    selectCourseId,
    activeLesson
}) => {
    if (!selectedStudent) return null;
    if (!selectCourseId) return null;

    console.log("StudentDetailCard", selectedStudent, selectCourseId);

    const [studentLesson, setStudentLesson] =
        useState<StudentLessonType | null>(null);

    const [showModal, setShowModal] = useState<boolean>(false);
    const [currentImage, setCurrentImage] = useState("");

    const handleImageClick = (imageUrl: string): void => {
        setCurrentImage(imageUrl);
        setShowModal(true);
    };

    useEffect(() => {
        if (!selectedStudent) return;
        if (!selectedStudent.studentLessons) return;

        const lesson = selectedStudent.studentLessons.find(
            (lesson) => lesson.lesson_id === activeLesson
        );

        setStudentLesson(lesson);
    }, [selectedStudent, activeLesson]);

    return (
        <>
            <Card className="card-flat" style={{ height: "auto" }}>
                <Card.Header className="bg-gray">
                    <StudentHeaderTitle
                        selectedStudent={selectedStudent}
                        setSelectStudentId={setSelectStudentId}
                    />
                </Card.Header>

                <ListGroup
                    variant="default"
                    style={{
                        borderRadius: 0,
                    }}
                >
                    <StudentStatusBar
                        classData={classData}
                        student={selectedStudent}
                        studentUnit={studentUnit}
                        courseAuths={courseAuths}
                        courseDateId={selectedStudent.course_date_id}
                        activeLesson={activeLesson}
                        studentLesson={studentLesson}
                        selectedCourseId={classData?.course.id}
                    />
                </ListGroup>
                <ListGroup variant="default">
                    <StudentProfileListItems
                        selectedStudent={selectedStudent}
                        handleImageClick={handleImageClick}
                    />
                </ListGroup>
            </Card>
            <Modal
                show={showModal}
                onHide={() => setShowModal(!showModal)}
                centered
            >
                <Modal.Header closeButton>
                    <Modal.Title>Image Preview</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <img
                        src={currentImage}
                        alt="Student"
                        style={{ width: "100%" }}
                    />
                </Modal.Body>
            </Modal>
        </>
    );
};

export default StudentDetailCard;
