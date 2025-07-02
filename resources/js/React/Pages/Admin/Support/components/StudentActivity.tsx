import React, { useEffect, useMemo, useState } from "react";
import { Card } from "react-bootstrap"; // Make sure to import Card from react-bootstrap
import {
    ClassDataShape,
    CourseType,
    SuppportClassDataType,
} from "../../../../Config/types";
import Bowser from "bowser";
import "./StudentSearch.css";

type CourseActivityType = {
    user_id: number;
    created_at: string;
    started_at: string;
    agreed_at: string;
    expires_at: string;
    disabled_at: string;
    completed_at: string;
    browser: string;
};

interface StudentActivityProps {
    classData: SuppportClassDataType;
    selectedCourseId: number | null;
    setSelectedCourseId: React.Dispatch<React.SetStateAction<number | null>>;
}

const StudentActivity = ({
    classData,
    selectedCourseId,
    setSelectedCourseId,
}: StudentActivityProps) => {
    const { courses } = classData;
    const [selectedCourse, setSelectedCourse] = useState<CourseType | null>(
        null
    );

    const TheSelectedCourse = useMemo(() => {        
        return selectedCourseId ? courses[selectedCourseId] as CourseType : null;
    }, [courses, selectedCourseId]);

    useEffect(() => {
        setSelectedCourse(TheSelectedCourse);
    }, [TheSelectedCourse]);

    // Correctly handle the select change to update selectedCourseId
    const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        // Convert the value to a number and update selectedCourseId
        const newCourseId = Number(e.target.value);
        setSelectedCourseId(newCourseId);
    };

    console.log("CCCLLLAAASSS", classData.studentActivity, selectedCourseId, selectedCourseId && classData.studentActivity[selectedCourseId])
    return (
        <Card>
            <Card.Header>
                <div className="d-flex justify-content-between align-items-center">
                    <h5>Select a Course</h5>
                    <select
                        className="form-select form-control"
                        onChange={handleSelectChange}
                        value={selectedCourseId || ""}
                    >
                        <option value="" disabled>
                            Select a Course
                        </option>

                        {Object.entries(courses).map(([key, value]) => {
                            return (
                                <option key={key} value={key}>
                                    {(value as CourseType).title_long}
                                </option>
                            );
                        })}
                    </select>
                </div>
            </Card.Header>
            <Card.Body>
            {selectedCourse ? (
    <>
        <h4>Selected Course: {selectedCourse?.title_long}</h4>
        
        <hr />
        {classData.studentActivity && selectedCourseId && classData.studentActivity[selectedCourseId] ? (
            Object.entries(classData.studentActivity[selectedCourseId]).map(([key, value], index) => {
                const isEven = index % 2 === 0;
                const itemClass = `list-group-item d-flex align-items-center justify-content-between ${isEven ? "even-class" : "odd-class"}`;

                const displayValue = key === "browser"
                    ? value ? `${Bowser.parse(String(value)).browser.name} - ${Bowser.parse(String(value)).browser.version}` : "Not Detected"
                    : value as React.ReactNode;

                return (
                    <div key={key} className={itemClass}>
                        <div>{key.replace(/[/_]/g, " ").toUpperCase()}</div>
                        <strong>{displayValue}</strong>
                    </div>
                );
            })
        ) : (
            <div className="alert alert-info">
                No activities to display for this course.
            </div>
        )}
    </>
) : (
    <div className="alert alert-danger">
        Select a course from the dropdown to view student activity.
    </div>
)}

            </Card.Body>
        </Card>
    );
};

export default StudentActivity;
