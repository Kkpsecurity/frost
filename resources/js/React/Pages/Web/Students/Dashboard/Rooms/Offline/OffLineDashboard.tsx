import React from "react";
import { ClassDataShape, StudentType } from "../../../../../../Config/types";
import { Row } from "react-bootstrap";

import {
    LgContainer,
    ResponsiveCol,
    StyledCard,
    StyledCardHeader,
    StyledListGroup,
    StyledListGroupItem,
} from "../../../../../../Styles/OfflineDashboardStyles.styled";

interface StudentDashboardProps {
    data: ClassDataShape;
    student: StudentType;
    section: string;
    darkMode: boolean;
    selectedLessonId: number | null;
    debug: boolean;
}

import { colors } from "../../../../../../Config/colors";

const colorPalette = (darkMode: boolean) =>
    darkMode ? colors.dark : colors.light;

const OffLineDashboard = ({
    data,
    student,
    section,
    darkMode,
    selectedLessonId,
    debug,
}: StudentDashboardProps) => {
    const { course, created_at, starts_at, ends_at, completed_at } = data;

    const formatTimestampToUS = (timestamp: number) => {
        const date = new Date(timestamp * 1000);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        });
    };

    // Key mapping object
    const keyMap = {
        fname: "First Name",
        lname: "Last Name",
        suffix: "Suffix",
    };

    return (
        <LgContainer fluid>
            <Row>
                <ResponsiveCol lg={6} md={6} sm={12} className="mb-2">
                    <StyledCard className="course-detail-card">
                        <StyledCardHeader>Course Details</StyledCardHeader>
                        <StyledListGroup>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Title:</strong> {course.title_long}
                            </StyledListGroupItem>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Purchased Date:</strong> {created_at}
                            </StyledListGroupItem>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Start Date:</strong> {starts_at}
                            </StyledListGroupItem>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Expires Date:</strong> {ends_at}
                            </StyledListGroupItem>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Completed Date:</strong> {completed_at}
                            </StyledListGroupItem>
                        </StyledListGroup>
                    </StyledCard>
                </ResponsiveCol>

                <ResponsiveCol lg={6} md={6} sm={12} className="mb-2">
                    <StyledCard className="student-info-card">
                        <StyledCardHeader>Student Info</StyledCardHeader>
                        <StyledListGroup>
                            {/* Existing List Group Items */}
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Name:</strong> {student.fname}{" "}
                                {student.lname}
                            </StyledListGroupItem>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Email:</strong> {student.email}
                            </StyledListGroupItem>

                            {student.student_info &&
                                Object.entries(student.student_info).map(
                                    ([key, value], index) => (
                                        <StyledListGroupItem
                                            key={index}
                                            className="d-flex justify-content-between align-items-center mb-3"
                                        >
                                            <strong>
                                                {keyMap[key] || key}:
                                            </strong>{" "}
                                            {value}
                                        </StyledListGroupItem>
                                    )
                            )}
                        </StyledListGroup>
                    </StyledCard>
                </ResponsiveCol>
            </Row>
            <Row>
                <ResponsiveCol lg={12} md={12} sm={12} className="mb-2">
                    <StyledCard
                        className="lesson-completed-card"
                        style={{
                            display: "block",
                        }}
                    >
                        <StyledCardHeader>
                            Student Lessons Completed
                        </StyledCardHeader>
                        <StyledListGroup>
                            <StyledListGroupItem className="d-flex justify-content-between align-items-center mb-3">
                                <strong>All lessons</strong>
                                <span>
                                    {data.allCompletedStudentLessonsTotal || 0}{" "}
                                    out of {data.allLessonsTotal || 0}
                                    <br />
                                </span>
                            </StyledListGroupItem>
                        </StyledListGroup>
                    </StyledCard>
                </ResponsiveCol>
            </Row>
        </LgContainer>
    );
};

export default OffLineDashboard;
